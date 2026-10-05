<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();
$errors = [];

$stmt = $pdo->prepare('SELECT p.*,cs.nama_lengkap FROM pendaftaran p LEFT JOIN calon_siswa cs ON cs.pendaftaran_id=p.id WHERE p.user_id=? AND p.status="diterima" ORDER BY p.created_at DESC LIMIT 1');
$stmt->execute([$user['id']]);
$pendaftaran = $stmt->fetch();
if (!$pendaftaran) { flash('success','Fitur ini hanya tersedia untuk pendaftar yang diterima.'); redirect('dashboard.php'); }
$pid = $pendaftaran['id'];

$bayar = $pdo->prepare('SELECT * FROM pembayaran WHERE pendaftaran_id=? ORDER BY id DESC LIMIT 1');
$bayar->execute([$pid]);
$pembayaran = $bayar->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!$pembayaran || $pembayaran['status'] === 'ditolak')) {
    $nominal = (float)($_POST['nominal'] ?? 0);
    $namaPengirim = trim($_POST['nama_pengirim'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');

    if ($nominal <= 0) $errors[] = 'Nominal pembayaran wajib diisi.';
    if ($namaPengirim === '') $errors[] = 'Nama pengirim wajib diisi.';
    if (empty($_FILES['bukti']['name'])) $errors[] = 'Bukti transfer wajib diunggah.';

    if (!$errors) {
        $file = $_FILES['bukti'];
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];
        $mime = '';
        if ($file['error'] === UPLOAD_ERR_OK) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = (string)$finfo->file($file['tmp_name']);
        }

        if ($file['error'] !== UPLOAD_ERR_OK) $errors[] = 'Upload gagal, coba lagi.';
        elseif (!isset($allowed[$mime])) $errors[] = 'Format file harus JPG/PNG/PDF.';
        elseif ($file['size'] > 2*1024*1024) $errors[] = 'Ukuran maksimal 2MB.';

        if (!$errors) {
            $ext = $allowed[$mime];
            $namaFile = 'bayar_' . $pid . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $namaFile)) {
                if ($pembayaran && $pembayaran['status'] === 'ditolak') {
                    // Update pembayaran yang ditolak
                    $pdo->prepare('UPDATE pembayaran SET nominal=?,nama_pengirim=?,bukti_file=?,catatan=?,status="menunggu",catatan_admin=NULL,updated_at=NOW() WHERE pendaftaran_id=?')
                        ->execute([$nominal,$namaPengirim,'uploads/'.$namaFile,$catatan?:null,$pid]);
                    flash('success','Bukti pembayaran berhasil diupload ulang. Menunggu konfirmasi admin.');
                } else {
                    // Insert pembayaran baru
                    $pdo->prepare('INSERT INTO pembayaran (pendaftaran_id,jenis,nominal,nama_pengirim,bukti_file,catatan) VALUES (?,?,?,?,?,?)')
                        ->execute([$pid,'daftar_ulang',$nominal,$namaPengirim,'uploads/'.$namaFile,$catatan?:null]);
                    flash('success','Bukti pembayaran berhasil diunggah. Menunggu konfirmasi admin.');
                }
                redirect('dashboard.php');
            } else { $errors[] = 'Gagal menyimpan file.'; }
        }
    }
}

// Tahapan (sama seperti dashboard)
$stepLabels = ['Formulir', 'Verifikasi', 'Hasil Seleksi', 'Daftar Ulang', 'Terdaftar'];
$stepActive = 3;
$stepDoneUpTo = 2;

$sudahKirim = $pembayaran && $pembayaran['status'] !== 'ditolak';
$dikonfirmasi = $pembayaran && $pembayaran['status'] === 'dikonfirmasi';

if (!function_exists('ppdb_icon')) {
    function ppdb_icon(string $name): string {
        $icons = [
            'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>',
            'check' => '<path d="M20 6 9 17l-5-5"/>',
            'alert' => '<path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.9 2.6 17.3A1.5 1.5 0 0 0 4 19.5h16a1.5 1.5 0 0 0 1.3-2.2L13.7 3.9a1.5 1.5 0 0 0-2.6 0Z"/>',
            'star' => '<path d="M12 3.5 14.7 9l6 .9-4.3 4.2 1 6-5.4-2.8-5.4 2.8 1-6L3.3 9.9l6-.9Z"/>',
            'x' => '<path d="M18 6 6 18"/><path d="M6 6l12 12"/>',
            'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 20c1.6-4 5-6 8-6s6.4 2 8 6"/>',
            'coin' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10"/><path d="M9.5 9.3c0-1.3 1.2-2 2.5-2s2.5.7 2.5 1.8-1 1.7-2.5 2-2.5.8-2.5 2 1.2 1.9 2.5 1.9 2.3-.6 2.5-1.6"/>',
            'bank' => '<path d="M3 10 12 4l9 6"/><path d="M5 10v8"/><path d="M9.5 10v8"/><path d="M14.5 10v8"/><path d="M19 10v8"/><path d="M3 20h18"/>',
            'upload' => '<path d="M12 16V4"/><path d="m7 9 5-5 5 5"/><path d="M4 20h16"/>',
        ];
        return $icons[$name] ?? '';
    }
}

$pageTitle = 'Upload Bukti Pembayaran - TK Harapan Bunda'; $activePage = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
<style>
.ppdb {
    --paper: #FBF7EF;
    --paper-line: #E7DFCE;
    --chalk: #2F4538;
    --chalk-soft: #3E5A48;
    --ink: #262A20;
    --muted: #6B7060;
    --coral: #E1552E;
    --leaf: #3F7E52;
    --leaf-soft: #E7F1E4;
    --amber: #C6820E;
    --amber-soft: #FBF0DC;
    --berry: #B23C3C;
    --berry-soft: #F8E7E4;
    --blue: #35618A;
    --blue-soft: #E5EEF5;
    font-family: 'Inter', system-ui, sans-serif;
    color: var(--ink);
}
.ppdb h1, .ppdb h2, .ppdb h3 { font-family: 'Fraunces', Georgia, serif; font-weight: 600; letter-spacing: -0.01em; }
.ppdb .icon { width: 1em; height: 1em; stroke: currentColor; stroke-width: 1.75; fill: none; stroke-linecap: round; stroke-linejoin: round; vertical-align: -0.15em; }

/* Intro */
.ppdb-dashboard-intro { padding: 30px 0 4px; background: var(--paper); }
.ppdb-dashboard-intro__inner { display: flex; align-items: center; justify-content: space-between; gap: 20px; }
.ppdb-dashboard-intro__text { min-width: 0; }
.ppdb-dashboard-intro__eyebrow {
    display: inline-block; margin-bottom: 7px; color: var(--leaf);
    font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
}
.ppdb-dashboard-intro h1 { margin: 0; color: var(--chalk); font-size: clamp(28px, 4vw, 38px); line-height: 1.15; }
.ppdb-dashboard-intro p { margin: 7px 0 0; color: var(--muted); font-size: 14px; line-height: 1.6; }
.ppdb-dashboard-intro__icon {
    width: 48px; height: 48px; flex: 0 0 48px; display: flex; align-items: center; justify-content: center;
    border: 1px solid var(--paper-line); border-radius: 13px; background: #fff; color: var(--chalk);
}
.ppdb-dashboard-intro__icon .icon { width: 22px; height: 22px; }

/* Hero: kartu pelajar */
.ppdb-hero {
    display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;
    background: var(--chalk); color: #F3F0E6; border-radius: 18px;
    padding: 30px 32px; flex-wrap: wrap;
}
.ppdb-hero__field-label { font-size: 13px; color: #C9D3C9; margin: 0 0 6px; }
.ppdb-hero__id { font-size: 32px; margin: 0; line-height: 1.1; }
.ppdb-hero__student { margin: 10px 0 0; font-size: 15px; color: #DCE3D8; }
.ppdb-hero__student strong { color: #fff; font-weight: 600; }

.ppdb-stamp {
    display: inline-flex; align-items: center; gap: 8px;
    background: #F3F0E6; color: var(--chalk); border-radius: 999px;
    padding: 10px 18px; font-weight: 600; font-size: 14.5px;
    transform: rotate(-3deg); white-space: nowrap;
}
.ppdb-stamp .icon { width: 18px; height: 18px; }
.ppdb-stamp--amber { background: var(--amber-soft); color: #8a5c0a; }
.ppdb-stamp--green { background: var(--leaf-soft); color: #2c5a3a; }
.ppdb-stamp--berry { background: var(--berry-soft); color: #8a2d2d; }

/* Stepper */
.ppdb-steps {
    display: flex; margin: 26px 0 24px; padding: 0; list-style: none;
    border: 1px solid var(--paper-line); background: var(--paper); border-radius: 14px; overflow: hidden;
}
.ppdb-step { flex: 1; padding: 16px 14px; position: relative; text-align: center; border-right: 1px solid var(--paper-line); }
.ppdb-step:last-child { border-right: none; }
.ppdb-step__dot {
    width: 26px; height: 26px; border-radius: 50%; margin: 0 auto 8px;
    display: flex; align-items: center; justify-content: center;
    background: #fff; border: 2px solid #D8D0BC; color: #9a9583; font-size: 12px; font-weight: 700;
}
.ppdb-step__label { font-size: 13px; color: var(--muted); font-weight: 500; }
.ppdb-step--done .ppdb-step__dot { background: var(--leaf); border-color: var(--leaf); color: #fff; }
.ppdb-step--done .ppdb-step__label { color: var(--ink); }
.ppdb-step--active .ppdb-step__dot { background: var(--chalk); border-color: var(--chalk); color: #fff; }
.ppdb-step--active .ppdb-step__label { color: var(--ink); font-weight: 700; }
.ppdb-step__dot .icon { width: 14px; height: 14px; }

/* Catatan */
.ppdb-note {
    display: flex; gap: 14px; border-radius: 12px; padding: 18px 20px; margin-bottom: 16px;
    border: 1px solid var(--paper-line); background: var(--paper);
}
.ppdb-note--error { background: var(--berry-soft); border-color: #ebc9c4; }
.ppdb-note .icon { width: 22px; height: 22px; flex-shrink: 0; margin-top: 2px; }
.ppdb-note--error .icon { color: var(--berry); }
.ppdb-note h4 { margin: 0 0 6px; font-size: 15.5px; }
.ppdb-note p { margin: 0; font-size: 14.5px; line-height: 1.6; color: var(--ink); }
.ppdb-note ul { margin: 0; padding-left: 18px; font-size: 14.5px; line-height: 1.7; }

/* Kwitansi / panel */
.ppdb-receipt { background: #fff; border: 1px solid var(--paper-line); border-radius: 14px; padding: 24px; margin-bottom: 16px; }
.ppdb-receipt__head { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 8px; }
.ppdb-receipt__head h3 { font-size: 18px; margin: 0; }
.ppdb-receipt__sub { color: var(--muted); font-size: 14px; margin: 0 0 4px; }
.ppdb-rows { margin: 14px 0 0; padding: 0; list-style: none; }
.ppdb-rows li {
    display: flex; justify-content: space-between; gap: 16px; padding: 12px 0;
    border-top: 1px dashed var(--paper-line); font-size: 14.5px;
}
.ppdb-rows li span { color: var(--muted); }
.ppdb-rows li strong { color: var(--ink); text-align: right; word-break: break-word; }
.ppdb-rows--big strong { font-family: 'Fraunces', Georgia, serif; font-size: 17px; }
.ppdb-account { font-family: 'Fraunces', Georgia, serif; font-size: 24px; letter-spacing: .04em; color: var(--chalk); }

/* Form */
.ppdb-form { background: #fff; border: 1.5px solid var(--paper-line); border-radius: 14px; padding: 28px; margin-bottom: 16px; }
.ppdb-form h3 { font-size: 19px; margin: 0 0 4px; }
.ppdb-form > p { color: var(--muted); font-size: 14.5px; margin: 0 0 22px; line-height: 1.6; }
.ppdb-field { margin-bottom: 18px; }
.ppdb-field label { display: block; font-size: 14px; font-weight: 600; color: var(--ink); margin-bottom: 8px; }
.ppdb-field label .req { color: var(--berry); }
.ppdb-field label .opt { color: var(--muted); font-weight: 400; }
.ppdb-field small { display: block; margin-top: 6px; color: var(--muted); font-size: 13px; line-height: 1.5; }
.ppdb-field input[type=text],
.ppdb-field input[type=number],
.ppdb-field input[type=file],
.ppdb-field textarea {
    width: 100%; box-sizing: border-box; padding: 12px 14px;
    border: 1.5px solid var(--paper-line); border-radius: 10px; background: var(--paper);
    color: var(--ink); font-family: inherit; font-size: 14.5px; line-height: 1.5; outline: none;
}
.ppdb-field textarea { resize: vertical; }
.ppdb-field input:focus, .ppdb-field textarea:focus { border-color: var(--chalk-soft); box-shadow: 0 0 0 3px rgba(62,90,72,.1); }
.ppdb-field input[type=file] { background: #fff; padding: 10px 12px; }
.ppdb-form__actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 8px; }

.ppdb-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    background: var(--coral); color: #fff; border: none; border-radius: 10px;
    padding: 12px 28px; font-family: inherit; font-size: 14.5px; font-weight: 600; text-decoration: none;
    box-sizing: border-box; cursor: pointer;
}
.ppdb-btn--outline { background: transparent; color: var(--chalk); border: 1.5px solid var(--paper-line); }

/* CTA */
.ppdb-cta {
    border: 1.5px solid var(--paper-line); border-radius: 14px; padding: 26px; text-align: center;
    margin-bottom: 16px; background: #fff;
}
.ppdb-cta .icon { width: 30px; height: 30px; color: var(--chalk); }
.ppdb-cta h3 { font-size: 19px; margin: 12px 0 8px; }
.ppdb-cta p { color: var(--muted); font-size: 14.5px; margin: 0 0 18px; line-height: 1.6; }
.ppdb-cta__actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
.ppdb-waiting {
    display: inline-flex; align-items: center; gap: 8px; background: var(--amber-soft); color: #8a5c0a;
    padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px;
}

@media (max-width: 640px) {
    .ppdb-dashboard-intro { padding-top: 23px; }
    .ppdb-dashboard-intro__inner { align-items: flex-start; }
    .ppdb-dashboard-intro__icon { width: 42px; height: 42px; flex-basis: 42px; }
    .ppdb-dashboard-intro h1 { font-size: 27px; }
    .ppdb-dashboard-intro p { font-size: 13px; }
    .ppdb-hero { flex-direction: column; }
    .ppdb-steps { flex-wrap: wrap; }
    .ppdb-step { flex: 1 1 33%; border-bottom: 1px solid var(--paper-line); }
    .ppdb-form__actions .ppdb-btn, .ppdb-cta__actions .ppdb-btn { width: 100%; }
    .ppdb-account { font-size: 20px; }
}
</style>

<div class="ppdb">

<section class="ppdb-dashboard-intro">
    <div class="container ppdb-dashboard-intro__inner">
        <div class="ppdb-dashboard-intro__text">
            <span class="ppdb-dashboard-intro__eyebrow">Pembayaran</span>
            <h1>Upload bukti pembayaran</h1>
            <p>Unggah bukti transfer daftar ulang untuk diverifikasi admin.</p>
        </div>
        <div class="ppdb-dashboard-intro__icon">
            <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('coin') ?></svg>
        </div>
    </div>
</section>

<div class="container section">

    <!-- Kartu pelajar -->
    <div class="ppdb-hero">
        <div>
            <p class="ppdb-hero__field-label">Nomor pendaftaran</p>
            <h2 class="ppdb-hero__id"><?= e($pendaftaran['nomor_pendaftaran']) ?></h2>
            <?php if ($pendaftaran['nama_lengkap']): ?>
                <p class="ppdb-hero__student">Calon siswa: <strong><?= e($pendaftaran['nama_lengkap']) ?></strong></p>
            <?php endif; ?>
        </div>
        <span class="ppdb-stamp ppdb-stamp--green">
            <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('star') ?></svg>
            Diterima
        </span>
    </div>

    <!-- Tahapan -->
    <ul class="ppdb-steps">
        <?php foreach ($stepLabels as $i => $lbl):
            $cls = '';
            if ($i <= $stepDoneUpTo) $cls = 'ppdb-step--done';
            elseif ($i === $stepActive) $cls = 'ppdb-step--active';
        ?>
            <li class="ppdb-step <?= $cls ?>">
                <span class="ppdb-step__dot">
                    <?php if ($cls === 'ppdb-step--done'): ?>
                        <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('check') ?></svg>
                    <?php else: ?>
                        <?= $i + 1 ?>
                    <?php endif; ?>
                </span>
                <span class="ppdb-step__label"><?= e($lbl) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($errors): ?>
        <div class="ppdb-note ppdb-note--error">
            <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('alert') ?></svg>
            <div>
                <h4>Periksa kembali isian Anda</h4>
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($sudahKirim): ?>

        <!-- Status pembayaran -->
        <div class="ppdb-receipt">
            <div class="ppdb-receipt__head">
                <h3>Status Pembayaran</h3>
                <span class="ppdb-stamp ppdb-stamp--<?= $dikonfirmasi ? 'green' : 'amber' ?>">
                    <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon($dikonfirmasi ? 'check' : 'clock') ?></svg>
                    <?= e(pembayaran_label($pembayaran['status'])) ?>
                </span>
            </div>
            <p class="ppdb-receipt__sub">
                <?= $dikonfirmasi
                    ? 'Pembayaran Anda telah dikonfirmasi oleh admin.'
                    : 'Bukti pembayaran Anda sedang diverifikasi oleh admin.' ?>
            </p>
            <ul class="ppdb-rows ppdb-rows--big">
                <li><span>Nominal</span><strong>Rp <?= number_format((float)$pembayaran['nominal'], 0, ',', '.') ?></strong></li>
                <li><span>Nama pengirim</span><strong><?= e($pembayaran['nama_pengirim']) ?></strong></li>
            </ul>
        </div>

        <div class="ppdb-cta">
            <?php if ($dikonfirmasi): ?>
                <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('star') ?></svg>
                <h3>Pembayaran selesai</h3>
                <p>Lanjutkan ke daftar ulang untuk menyelesaikan pendaftaran.</p>
                <div class="ppdb-cta__actions">
                    <a class="ppdb-btn" href="daftar-ulang.php">Lakukan Daftar Ulang</a>
                    <a class="ppdb-btn ppdb-btn--outline" href="dashboard.php">Kembali ke Dashboard</a>
                </div>
            <?php else: ?>
                <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('clock') ?></svg>
                <h3>Menunggu konfirmasi admin</h3>
                <p>Anda akan bisa melakukan daftar ulang setelah pembayaran dikonfirmasi.</p>
                <div class="ppdb-cta__actions">
                    <a class="ppdb-btn ppdb-btn--outline" href="dashboard.php">Kembali ke Dashboard</a>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>

        <?php if ($pembayaran && $pembayaran['status'] === 'ditolak'): ?>
            <div class="ppdb-note ppdb-note--error">
                <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('x') ?></svg>
                <div>
                    <h4>Pembayaran ditolak</h4>
                    <p><strong>Alasan:</strong> <?= e($pembayaran['catatan_admin'] ?? 'Tidak ada keterangan') ?></p>
                    <p style="margin-top:6px">Silakan upload ulang bukti transfer yang benar di bawah ini.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Informasi rekening -->
        <div class="ppdb-receipt">
            <div class="ppdb-receipt__head">
                <h3>Informasi Pembayaran</h3>
                <span class="ppdb-stamp ppdb-stamp--amber">
                    <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('bank') ?></svg>
                    Transfer Bank
                </span>
            </div>
            <p class="ppdb-receipt__sub">Silakan transfer ke rekening berikut.</p>
            <ul class="ppdb-rows">
                <li><span>Bank</span><strong>Bank BRI</strong></li>
                <li><span>Nomor rekening</span><strong class="ppdb-account">1234-5678-9012</strong></li>
                <li><span>Atas nama</span><strong>TK Harapan Bunda</strong></li>
                <li><span>Nominal daftar ulang</span><strong style="font-family:'Fraunces',Georgia,serif;font-size:20px;color:var(--leaf)">Rp 500.000</strong></li>
            </ul>
        </div>

        <!-- Form upload -->
        <form method="post" enctype="multipart/form-data" class="ppdb-form">
            <h3>Upload Bukti Transfer</h3>
            <p>Isi data transfer dan unggah buktinya agar admin bisa memverifikasi.</p>

            <div class="ppdb-field">
                <label for="nominal">Nominal yang ditransfer <span class="req">*</span></label>
                <input type="number" id="nominal" name="nominal" required placeholder="Contoh: 500000">
                <small>Masukkan nominal sesuai yang Anda transfer.</small>
            </div>

            <div class="ppdb-field">
                <label for="nama_pengirim">Nama pengirim <span class="req">*</span></label>
                <input type="text" id="nama_pengirim" name="nama_pengirim" required placeholder="Nama sesuai rekening pengirim">
                <small>Nama harus sesuai dengan rekening yang digunakan untuk transfer.</small>
            </div>

            <div class="ppdb-field">
                <label for="bukti">Bukti transfer <span class="req">*</span></label>
                <input type="file" id="bukti" name="bukti" required accept="image/jpeg,image/png,application/pdf">
                <small>Format JPG, PNG, atau PDF. Maksimal 2MB.</small>
            </div>

            <div class="ppdb-field">
                <label for="catatan">Catatan <span class="opt">(opsional)</span></label>
                <textarea id="catatan" name="catatan" rows="3" placeholder="Catatan tambahan jika ada..."></textarea>
            </div>

            <div class="ppdb-form__actions">
                <button type="submit" class="ppdb-btn">
                    <svg class="icon" viewBox="0 0 24 24" style="width:18px;height:18px"><?= ppdb_icon('upload') ?></svg>
                    Upload Bukti Transfer
                </button>
                <a class="ppdb-btn ppdb-btn--outline" href="dashboard.php">Kembali ke Dashboard</a>
            </div>
        </form>

    <?php endif; ?>

</div>

</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
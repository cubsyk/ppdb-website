<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

$stmt = $pdo->prepare('SELECT p.*,cs.nama_lengkap FROM pendaftaran p LEFT JOIN calon_siswa cs ON cs.pendaftaran_id=p.id WHERE p.user_id=? AND p.status="diterima" ORDER BY p.created_at DESC LIMIT 1');
$stmt->execute([$user['id']]);
$pendaftaran = $stmt->fetch();
if (!$pendaftaran) { flash('success','Fitur ini hanya tersedia untuk pendaftar yang diterima.'); redirect('dashboard.php'); }
$pid = $pendaftaran['id'];

// Cek pembayaran (ambil yang terbaru)
$stmtP = $pdo->prepare('SELECT * FROM pembayaran WHERE pendaftaran_id=? ORDER BY id DESC LIMIT 1');
$stmtP->execute([$pid]);
$pembayaran = $stmtP->fetch();
if (!$pembayaran || $pembayaran['status'] !== 'dikonfirmasi') {
    flash('success','Silakan upload bukti pembayaran terlebih dahulu.'); redirect('pembayaran.php');
}

$duRow = $pdo->prepare('SELECT * FROM daftar_ulang WHERE pendaftaran_id=?');
$duRow->execute([$pid]); $du = $duRow->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$du) {
    $catatan = trim($_POST['catatan'] ?? '');
    $pdo->prepare('INSERT INTO daftar_ulang (pendaftaran_id,konfirmasi,catatan) VALUES (?,1,?)')->execute([$pid,$catatan?:null]);
    $pdo->prepare('UPDATE pendaftaran SET status="siswa_terdaftar" WHERE id=?')->execute([$pid]);
    flash('success','Daftar ulang berhasil! Selamat datang di TK Harapan Bunda.');
    redirect('dashboard.php');
}

// Tahapan (sama seperti dashboard)
$stepLabels = ['Formulir', 'Verifikasi', 'Hasil Seleksi', 'Daftar Ulang', 'Terdaftar'];
$stepActive = $du ? 4 : 3;
$stepDoneUpTo = $du ? 4 : 2;

if (!function_exists('ppdb_icon')) {
    function ppdb_icon(string $name): string {
        $icons = [
            'check' => '<path d="M20 6 9 17l-5-5"/>',
            'star' => '<path d="M12 3.5 14.7 9l6 .9-4.3 4.2 1 6-5.4-2.8-5.4 2.8 1-6L3.3 9.9l6-.9Z"/>',
            'badge' => '<circle cx="12" cy="8" r="5"/><path d="M8.5 12.5 6 21l6-3 6 3-2.5-8.5"/>',
            'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 20c1.6-4 5-6 8-6s6.4 2 8 6"/>',
            'coin' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10"/><path d="M9.5 9.3c0-1.3 1.2-2 2.5-2s2.5.7 2.5 1.8-1 1.7-2.5 2-2.5.8-2.5 2 1.2 1.9 2.5 1.9 2.3-.6 2.5-1.6"/>',
        ];
        return $icons[$name] ?? '';
    }
}

$pageTitle = 'Daftar Ulang - TK Harapan Bunda';
$activePage = 'dashboard';
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

/* Catatan & kwitansi */
.ppdb-note {
    display: flex; gap: 14px; border-radius: 12px; padding: 18px 20px; margin-bottom: 16px;
    border: 1px solid var(--paper-line); background: var(--paper);
}
.ppdb-note--success { background: var(--leaf-soft); border-color: #cfe4cb; }
.ppdb-note .icon { width: 22px; height: 22px; flex-shrink: 0; margin-top: 2px; }
.ppdb-note--success .icon { color: var(--leaf); }
.ppdb-note h4 { margin: 0 0 6px; font-size: 15.5px; }
.ppdb-note p { margin: 0; font-size: 14.5px; line-height: 1.6; color: var(--ink); }

.ppdb-receipt { background: #fff; border: 1px solid var(--paper-line); border-radius: 14px; padding: 24px; margin-bottom: 16px; }
.ppdb-receipt__head { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 4px; }
.ppdb-receipt__head h3 { font-size: 18px; margin: 0; }
.ppdb-receipt__amount { color: var(--muted); font-size: 14.5px; margin: 6px 0 0; }
.ppdb-receipt__amount strong { color: var(--ink); font-size: 16px; }

/* Form */
.ppdb-form { background: #fff; border: 1.5px solid var(--paper-line); border-radius: 14px; padding: 28px; margin-bottom: 16px; }
.ppdb-form .icon.ppdb-form__icon { width: 30px; height: 30px; color: var(--chalk); }
.ppdb-form h3 { font-size: 19px; margin: 12px 0 8px; }
.ppdb-form > p { color: var(--muted); font-size: 14.5px; margin: 0 0 20px; line-height: 1.6; }
.ppdb-form label { display: block; font-size: 14px; font-weight: 600; color: var(--ink); margin-bottom: 8px; }
.ppdb-form label span { color: var(--muted); font-weight: 400; }
.ppdb-form textarea {
    width: 100%; box-sizing: border-box; padding: 12px 14px; margin-bottom: 20px;
    border: 1.5px solid var(--paper-line); border-radius: 10px; background: var(--paper);
    color: var(--ink); font-family: inherit; font-size: 14.5px; line-height: 1.5; resize: vertical; outline: none;
}
.ppdb-form textarea:focus { border-color: var(--chalk-soft); box-shadow: 0 0 0 3px rgba(62,90,72,.1); }
.ppdb-form__actions { display: flex; gap: 10px; flex-wrap: wrap; }

.ppdb-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    background: var(--coral); color: #fff; border: none; border-radius: 10px;
    padding: 12px 28px; font-family: inherit; font-size: 14.5px; font-weight: 600; text-decoration: none;
    box-sizing: border-box; cursor: pointer;
}
.ppdb-btn--outline { background: transparent; color: var(--chalk); border: 1.5px solid var(--paper-line); }

/* Selesai */
.ppdb-done {
    text-align: center; padding: 56px 32px; background: var(--paper);
    border: 1px solid var(--paper-line); border-radius: 18px; margin-bottom: 16px;
}
.ppdb-done .icon { width: 40px; height: 40px; color: var(--leaf); }
.ppdb-done h2 { font-size: 24px; margin: 18px 0 8px; color: var(--chalk); }
.ppdb-done p { color: var(--muted); margin: 0 0 22px; font-size: 15px; }

@media (max-width: 640px) {
    .ppdb-dashboard-intro { padding-top: 23px; }
    .ppdb-dashboard-intro__inner { align-items: flex-start; }
    .ppdb-dashboard-intro__icon { width: 42px; height: 42px; flex-basis: 42px; }
    .ppdb-dashboard-intro h1 { font-size: 27px; }
    .ppdb-dashboard-intro p { font-size: 13px; }
    .ppdb-hero { flex-direction: column; }
    .ppdb-steps { flex-wrap: wrap; }
    .ppdb-step { flex: 1 1 33%; border-bottom: 1px solid var(--paper-line); }
    .ppdb-form__actions .ppdb-btn { width: 100%; }
}
</style>

<div class="ppdb">

<section class="ppdb-dashboard-intro">
    <div class="container ppdb-dashboard-intro__inner">
        <div class="ppdb-dashboard-intro__text">
            <span class="ppdb-dashboard-intro__eyebrow">Daftar Ulang</span>
            <h1>Konfirmasi daftar ulang</h1>
            <p>Konfirmasi kehadiran calon siswa yang telah diterima.</p>
        </div>
        <div class="ppdb-dashboard-intro__icon">
            <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('user') ?></svg>
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
        <?php if ($du): ?>
            <span class="ppdb-stamp ppdb-stamp--green">
                <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('badge') ?></svg>
                Siswa Terdaftar
            </span>
        <?php else: ?>
            <span class="ppdb-stamp ppdb-stamp--green">
                <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('star') ?></svg>
                Diterima
            </span>
        <?php endif; ?>
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

    <?php if ($du): ?>

        <div class="ppdb-done">
            <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('badge') ?></svg>
            <h2>Daftar ulang selesai</h2>
            <p>Status Anda telah diubah menjadi <strong>Siswa Terdaftar</strong>.</p>
            <a class="ppdb-btn" href="dashboard.php">Kembali ke Dashboard</a>
        </div>

    <?php else: ?>

        <?php if ($pendaftaran['hasil_seleksi']): ?>
            <div class="ppdb-note ppdb-note--success">
                <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('star') ?></svg>
                <div>
                    <h4>Hasil seleksi</h4>
                    <p><?= nl2br(e($pendaftaran['hasil_seleksi'])) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="ppdb-receipt">
            <div class="ppdb-receipt__head">
                <h3>Status Pembayaran</h3>
                <span class="ppdb-stamp ppdb-stamp--green">
                    <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('check') ?></svg>
                    Dikonfirmasi
                </span>
            </div>
            <p class="ppdb-receipt__amount">Nominal: <strong>Rp <?= number_format((float)$pembayaran['nominal'], 0, ',', '.') ?></strong></p>
        </div>

        <form method="post" class="ppdb-form">
            <svg class="icon ppdb-form__icon" viewBox="0 0 24 24"><?= ppdb_icon('star') ?></svg>
            <h3>Selamat, Anda diterima</h3>
            <p>Tekan tombol konfirmasi di bawah untuk menyelesaikan daftar ulang dan menjadi siswa terdaftar.</p>

            <label for="catatan">Catatan <span>(opsional)</span></label>
            <textarea id="catatan" name="catatan" rows="3" placeholder="Tulis catatan untuk panitia jika ada..."></textarea>

            <div class="ppdb-form__actions">
                <button class="ppdb-btn" type="submit">Konfirmasi Daftar Ulang</button>
                <a class="ppdb-btn ppdb-btn--outline" href="dashboard.php">Kembali</a>
            </div>
        </form>

    <?php endif; ?>

</div>

</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

$stmt = $pdo->prepare('SELECT p.*, cs.nama_lengkap FROM pendaftaran p LEFT JOIN calon_siswa cs ON cs.pendaftaran_id=p.id WHERE p.user_id=? ORDER BY p.created_at DESC LIMIT 1');
$stmt->execute([$user['id']]);
$pendaftaran = $stmt->fetch();

// Cek pembayaran jika diterima
$pembayaran = null;
if ($pendaftaran && $pendaftaran['status'] === 'diterima') {
    $stmtP = $pdo->prepare('SELECT * FROM pembayaran WHERE pendaftaran_id=?');
    $stmtP->execute([$pendaftaran['id']]);
    $pembayaran = $stmtP->fetch();
}

$dokCount = 0; $dokValid = 0;
if ($pendaftaran) {
    $ds = $pdo->prepare('SELECT COUNT(*) c, SUM(status="valid") v FROM dokumen WHERE pendaftaran_id=?');
    $ds->execute([$pendaftaran['id']]);
    $dr = $ds->fetch();
    $dokCount = (int)$dr['c']; $dokValid = (int)$dr['v'];
}

$pengumuman = $pdo->query('SELECT * FROM pengumuman WHERE status="publish" ORDER BY created_at DESC LIMIT 5')->fetchAll();

// --- Tahapan pendaftaran (stepper) ---
// Menggantikan progress bar persentase dengan tahapan bernama, lebih mudah dibaca orang tua.
$stepLabels = ['Formulir', 'Verifikasi', 'Hasil Seleksi', 'Daftar Ulang', 'Terdaftar'];
$stepActive = 0;      // index tahap yang sedang berjalan
$stepDoneUpTo = -1;   // index terakhir yang sudah selesai (inclusive)
$isRejected = false;
$needsRevision = false;

if ($pendaftaran) {
    $status = $pendaftaran['status'];
    switch ($status) {
        case 'draft':
            $stepActive = 0; $stepDoneUpTo = -1; break;
        case 'menunggu_verifikasi':
            $stepActive = 1; $stepDoneUpTo = 0; break;
        case 'perlu_perbaikan':
            $stepActive = 1; $stepDoneUpTo = 0; $needsRevision = true; break;
        case 'terverifikasi':
            $stepActive = 2; $stepDoneUpTo = 1; break;
        case 'tidak_diterima':
            $stepActive = 2; $stepDoneUpTo = 1; $isRejected = true; break;
        case 'diterima':
            $stepActive = 3; $stepDoneUpTo = 2; break;
        case 'siswa_terdaftar':
            $stepActive = 4; $stepDoneUpTo = 4; break;
        default:
            $stepActive = 0; $stepDoneUpTo = -1;
    }
}

// Ikon status untuk kartu/tiket (stroke SVG, bukan emoji)
$stampIcons = [
    'draft' => 'edit',
    'menunggu_verifikasi' => 'clock',
    'perlu_perbaikan' => 'alert',
    'terverifikasi' => 'check',
    'diterima' => 'star',
    'tidak_diterima' => 'x',
    'siswa_terdaftar' => 'badge',
];
$stampTone = [
    'draft' => 'slate',
    'menunggu_verifikasi' => 'amber',
    'perlu_perbaikan' => 'amber',
    'terverifikasi' => 'blue',
    'diterima' => 'green',
    'tidak_diterima' => 'berry',
    'siswa_terdaftar' => 'green',
];

function ppdb_icon(string $name): string {
    $icons = [
        'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'alert' => '<path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.9 2.6 17.3A1.5 1.5 0 0 0 4 19.5h16a1.5 1.5 0 0 0 1.3-2.2L13.7 3.9a1.5 1.5 0 0 0-2.6 0Z"/>',
        'star' => '<path d="M12 3.5 14.7 9l6 .9-4.3 4.2 1 6-5.4-2.8-5.4 2.8 1-6L3.3 9.9l6-.9Z"/>',
        'x' => '<path d="M18 6 6 18"/><path d="M6 6l12 12"/>',
        'badge' => '<circle cx="12" cy="8" r="5"/><path d="M8.5 12.5 6 21l6-3 6 3-2.5-8.5"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 20c1.6-4 5-6 8-6s6.4 2 8 6"/>',
        'doc' => '<path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/><path d="M9.5 13h5"/><path d="M9.5 16.5h5"/>',
        'coin' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10"/><path d="M9.5 9.3c0-1.3 1.2-2 2.5-2s2.5.7 2.5 1.8-1 1.7-2.5 2-2.5.8-2.5 2 1.2 1.9 2.5 1.9 2.3-.6 2.5-1.6"/>',
        'megaphone' => '<path d="M3 11v2a2 2 0 0 0 2 2h1l3 5V4l-3 5H5a2 2 0 0 0-2 2Z"/><path d="M14 8a4 4 0 0 1 0 8"/><path d="M17.5 5.5a8 8 0 0 1 0 13"/>',
    ];
    return $icons[$name] ?? '';
}

$pageTitle = 'Dashboard - TK Harapan Bunda';
$activePage = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
<style>

/* =========================================================
   DASHBOARD INTRO
   ========================================================= */

.ppdb-dashboard-intro {
    padding: 30px 0 4px;
    background: var(--paper);
}

.ppdb-dashboard-intro__inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.ppdb-dashboard-intro__text {
    min-width: 0;
}

.ppdb-dashboard-intro__eyebrow {
    display: inline-block;
    margin-bottom: 7px;
    color: var(--leaf);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.ppdb-dashboard-intro h1 {
    margin: 0;
    color: var(--chalk);
    font-family: 'Fraunces', Georgia, serif;
    font-size: clamp(28px, 4vw, 38px);
    line-height: 1.15;
}

.ppdb-dashboard-intro p {
    margin: 7px 0 0;
    color: var(--muted);
    font-size: 14px;
    line-height: 1.6;
}

.ppdb-dashboard-intro__icon {
    width: 48px;
    height: 48px;
    flex: 0 0 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--paper-line);
    border-radius: 13px;
    background: #fff;
    color: var(--chalk);
}

.ppdb-dashboard-intro__icon .icon {
    width: 22px;
    height: 22px;
}

@media (max-width: 640px) {

    .ppdb-dashboard-intro {
        padding-top: 23px;
    }

    .ppdb-dashboard-intro__inner {
        align-items: flex-start;
    }

    .ppdb-dashboard-intro__icon {
        width: 42px;
        height: 42px;
        flex-basis: 42px;
    }

    .ppdb-dashboard-intro h1 {
        font-size: 27px;
    }

    .ppdb-dashboard-intro p {
        font-size: 13px;
    }
}

/* ---- PPDB dashboard: tema "Rapor & Kartu Pelajar" (scoped ke .ppdb) ---- */
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap');

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

.ppdb-flash {
    display: flex; align-items: center; gap: 12px;
    background: var(--leaf-soft); border: 1px solid #cfe4cb; color: #2c5a3a;
    border-radius: 10px; padding: 14px 18px; margin-bottom: 22px; font-size: 15px;
}
.ppdb-flash .icon { width: 20px; height: 20px; flex-shrink: 0; color: var(--leaf); }

/* Empty state */
.ppdb-empty {
    text-align: center; padding: 64px 32px; background: var(--paper);
    border: 1px solid var(--paper-line); border-radius: 18px;
}
.ppdb-empty .icon { width: 40px; height: 40px; color: var(--chalk); }
.ppdb-empty h2 { font-size: 24px; margin: 18px 0 8px; }
.ppdb-empty p { color: var(--muted); margin-bottom: 22px; font-size: 15px; }

/* Hero: kartu pelajar / tiket pendaftaran */
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
.ppdb-stamp--blue { background: var(--blue-soft); color: #244a68; }
.ppdb-stamp--slate { background: rgba(255,255,255,0.14); color: #F3F0E6; }

/* Stepper tahapan */
.ppdb-steps {
    display: flex; margin: 26px 0 24px; padding: 0; list-style: none;
    border: 1px solid var(--paper-line); background: var(--paper); border-radius: 14px;
    overflow: hidden;
}
.ppdb-step {
    flex: 1; padding: 16px 14px; position: relative; text-align: center;
    border-right: 1px solid var(--paper-line);
}
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
.ppdb-step--revision .ppdb-step__dot { background: var(--amber); border-color: var(--amber); color: #fff; }
.ppdb-step--rejected .ppdb-step__dot { background: var(--berry); border-color: var(--berry); color: #fff; }
.ppdb-step__dot .icon { width: 14px; height: 14px; }

/* Grid panel */
.ppdb-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 16px; }
.ppdb-panel {
    background: #fff; border: 1px solid var(--paper-line); border-radius: 14px; padding: 24px;
}
.ppdb-panel__icon {
    width: 42px; height: 42px; border-radius: 10px; background: var(--paper);
    display: flex; align-items: center; justify-content: center; color: var(--chalk);
    margin-bottom: 14px;
}
.ppdb-panel__icon .icon { width: 22px; height: 22px; }
.ppdb-panel h3 { font-size: 17px; margin: 0 0 6px; }
.ppdb-panel p { color: var(--muted); font-size: 14.5px; margin: 0 0 16px; line-height: 1.5; }
.ppdb-panel p strong { color: var(--ink); }

.ppdb-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    background: var(--coral); color: #fff; border: none; border-radius: 10px;
    padding: 11px 20px; font-size: 14.5px; font-weight: 600; text-decoration: none;
    width: 100%; box-sizing: border-box;
}
.ppdb-btn--outline { background: transparent; color: var(--chalk); border: 1.5px solid var(--paper-line); }

/* Catatan (admin / hasil seleksi) */
.ppdb-note {
    display: flex; gap: 14px; border-radius: 12px; padding: 18px 20px; margin-bottom: 16px;
    border: 1px solid var(--paper-line); background: var(--paper);
}
.ppdb-note--warn { background: var(--amber-soft); border-color: #e7d3a3; }
.ppdb-note--success { background: var(--leaf-soft); border-color: #cfe4cb; }
.ppdb-note .icon { width: 22px; height: 22px; flex-shrink: 0; margin-top: 2px; }
.ppdb-note--warn .icon { color: var(--amber); }
.ppdb-note--success .icon { color: var(--leaf); }
.ppdb-note h4 { margin: 0 0 6px; font-size: 15.5px; }
.ppdb-note p { margin: 0; font-size: 14.5px; line-height: 1.6; color: var(--ink); }

/* Kwitansi pembayaran */
.ppdb-receipt { background: #fff; border: 1px solid var(--paper-line); border-radius: 14px; padding: 24px; margin-bottom: 16px; }
.ppdb-receipt__head { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 4px; }
.ppdb-receipt__head h3 { font-size: 18px; margin: 0; }
.ppdb-receipt__amount { color: var(--muted); font-size: 14.5px; margin-top: 6px; }
.ppdb-receipt__amount strong { color: var(--ink); font-size: 16px; }
.ppdb-receipt__proof {
    margin-top: 14px; padding-top: 14px; border-top: 1px dashed var(--paper-line);
    font-size: 14px;
}
.ppdb-receipt__proof a { color: var(--blue); text-decoration: underline; }
.ppdb-reason { margin-top: 12px; background: var(--berry-soft); border-radius: 10px; padding: 14px 16px; font-size: 14px; color: #8a2d2d; }

/* CTA bawah */
.ppdb-cta {
    border: 1.5px solid var(--paper-line); border-radius: 14px; padding: 26px; text-align: center;
    margin-bottom: 16px; background: #fff;
}
.ppdb-cta .icon { width: 30px; height: 30px; color: var(--chalk); }
.ppdb-cta h3 { font-size: 19px; margin: 12px 0 8px; }
.ppdb-cta p { color: var(--muted); font-size: 14.5px; margin-bottom: 18px; line-height: 1.6; }
.ppdb-cta .ppdb-btn { width: auto; padding: 12px 28px; }
.ppdb-waiting {
    display: inline-flex; align-items: center; gap: 8px; background: var(--amber-soft); color: #8a5c0a;
    padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px;
}

/* Papan pengumuman */
.ppdb-board { margin-top: 36px; }
.ppdb-board h2 { font-size: 21px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
.ppdb-board h2 .icon { width: 20px; height: 20px; color: var(--chalk); }
.ppdb-board__item {
    background: var(--paper); border: 1px solid var(--paper-line); border-radius: 12px;
    padding: 18px 20px; margin-bottom: 12px;
}
.ppdb-board__item h4 { margin: 0 0 6px; font-size: 16px; }
.ppdb-board__item p { margin: 0; font-size: 14.5px; line-height: 1.6; color: var(--ink); }
.ppdb-board__date { color: var(--muted); font-size: 12.5px; margin-top: 8px; display: block; }

@media (max-width: 640px) {
    .ppdb-hero { flex-direction: column; }
    .ppdb-steps { flex-wrap: wrap; }
    .ppdb-step { flex: 1 1 33%; border-bottom: 1px solid var(--paper-line); }
}
</style>

<section class="ppdb-dashboard-intro">
    <div class="container ppdb-dashboard-intro__inner">

        <div class="ppdb-dashboard-intro__text">
            <span class="ppdb-dashboard-intro__eyebrow">
                Dashboard Orang Tua
            </span>

            <h1>
                Selamat datang, <?= e($user['nama']) ?>
            </h1>

            <p>
                Pantau proses pendaftaran dan informasi PPDB Anda di sini.
            </p>
        </div>

        <div class="ppdb-dashboard-intro__icon">
            <svg class="icon" viewBox="0 0 24 24">
                <?= ppdb_icon('user') ?>
            </svg>
        </div>

    </div>
</section>

<div class="container section ppdb">
    <?php if ($m = flash('success')): ?>
        <div class="ppdb-flash">
            <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('check') ?></svg>
            <span><?= e($m) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!$pendaftaran): ?>
        <div class="ppdb-empty">
            <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('doc') ?></svg>
            <h2>Belum ada pendaftaran</h2>
            <p>Mulai proses pendaftaran dengan mengisi data calon siswa.</p>
            <a class="ppdb-btn" href="pendaftaran-form.php" style="width:auto;padding:13px 30px">Mulai Pendaftaran</a>
        </div>
    <?php else:
        $label = status_label($status);
        $tone = $stampTone[$status] ?? 'slate';
        $iconName = $stampIcons[$status] ?? 'clock';
    ?>

    <!-- Kartu pelajar / tiket pendaftaran -->
    <div class="ppdb-hero">
        <div>
            <p class="ppdb-hero__field-label">Nomor pendaftaran</p>
            <h2 class="ppdb-hero__id"><?= e($pendaftaran['nomor_pendaftaran']) ?></h2>
            <?php if ($pendaftaran['nama_lengkap']): ?>
                <p class="ppdb-hero__student">Calon siswa: <strong><?= e($pendaftaran['nama_lengkap']) ?></strong></p>
            <?php endif; ?>
        </div>
        <span class="ppdb-stamp ppdb-stamp--<?= $tone ?>">
            <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon($iconName) ?></svg>
            <?= e($label) ?>
        </span>
    </div>

    <!-- Tahapan pendaftaran -->
    <ul class="ppdb-steps">
        <?php foreach ($stepLabels as $i => $lbl):
            $cls = '';
            if ($isRejected && $i === $stepActive) $cls = 'ppdb-step--rejected';
            elseif ($needsRevision && $i === $stepActive) $cls = 'ppdb-step--revision';
            elseif ($i <= $stepDoneUpTo) $cls = 'ppdb-step--done';
            elseif ($i === $stepActive) $cls = 'ppdb-step--active';
        ?>
            <li class="ppdb-step <?= $cls ?>">
                <span class="ppdb-step__dot">
                    <?php if ($cls === 'ppdb-step--done'): ?>
                        <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('check') ?></svg>
                    <?php elseif ($cls === 'ppdb-step--rejected'): ?>
                        <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('x') ?></svg>
                    <?php elseif ($cls === 'ppdb-step--revision'): ?>
                        <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('alert') ?></svg>
                    <?php else: ?>
                        <?= $i + 1 ?>
                    <?php endif; ?>
                </span>
                <span class="ppdb-step__label"><?= e($lbl) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Data Siswa & Dokumen -->
    <div class="ppdb-grid">
        <div class="ppdb-panel">
            <div class="ppdb-panel__icon"><svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('user') ?></svg></div>
            <h3>Data Siswa & Orang Tua</h3>
            <p><?= $status === 'draft' ? 'Data belum lengkap, silakan lengkapi formulir.' : 'Data sudah diisi dan tersimpan.' ?></p>
            <a class="ppdb-btn <?= in_array($status, ['draft', 'perlu_perbaikan']) ? '' : 'ppdb-btn--outline' ?>" href="pendaftaran-form.php">
                <?= in_array($status, ['draft', 'perlu_perbaikan']) ? 'Edit Data' : 'Lihat Data' ?>
            </a>
        </div>
        <div class="ppdb-panel">
            <div class="ppdb-panel__icon"><svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('doc') ?></svg></div>
            <h3>Dokumen</h3>
            <p><strong><?= $dokCount ?></strong> dokumen diunggah, <strong><?= $dokValid ?> valid</strong></p>
            <a class="ppdb-btn ppdb-btn--outline" href="upload-dokumen.php">Kelola Dokumen</a>
        </div>
    </div>

    <!-- Pesan & hasil seleksi -->
    <?php if ($pendaftaran['catatan_admin']): ?>
        <div class="ppdb-note ppdb-note--warn">
            <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('alert') ?></svg>
            <div>
                <h4>Pesan dari admin</h4>
                <p><?= nl2br(e($pendaftaran['catatan_admin'])) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($pendaftaran['hasil_seleksi']): ?>
        <div class="ppdb-note ppdb-note--success">
            <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('star') ?></svg>
            <div>
                <h4>Hasil seleksi</h4>
                <p><?= nl2br(e($pendaftaran['hasil_seleksi'])) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Status pembayaran -->
    <?php if ($pembayaran): ?>
        <div class="ppdb-receipt">
            <div class="ppdb-receipt__head">
                <h3>Status Pembayaran</h3>
                <span class="ppdb-stamp ppdb-stamp--<?= $pembayaran['status'] === 'dikonfirmasi' ? 'green' : ($pembayaran['status'] === 'ditolak' ? 'berry' : 'amber') ?>">
                    <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon($pembayaran['status'] === 'dikonfirmasi' ? 'check' : ($pembayaran['status'] === 'ditolak' ? 'x' : 'clock')) ?></svg>
                    <?= e(pembayaran_label($pembayaran['status'])) ?>
                </span>
            </div>
            <p class="ppdb-receipt__amount">Nominal: <strong>Rp <?= number_format((float)$pembayaran['nominal'], 0, ',', '.') ?></strong></p>

            <?php if ($pembayaran['status'] === 'ditolak' && $pembayaran['catatan_admin']): ?>
                <div class="ppdb-reason"><strong>Alasan penolakan:</strong> <?= e($pembayaran['catatan_admin']) ?></div>
            <?php endif; ?>

            <?php if ($pembayaran['status'] === 'menunggu'): ?>
                <div class="ppdb-receipt__proof">
                    <strong>Bukti transfer:</strong><br>
                    <a href="file.php?f=<?= urlencode($pembayaran['bukti_file']) ?>" target="_blank"><?= e($pembayaran['bukti_file']) ?></a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Aksi kontekstual -->
    <?php if ($status === 'diterima' && (!$pembayaran || $pembayaran['status'] === 'ditolak')): ?>
        <div class="ppdb-cta">
            <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('coin') ?></svg>
            <h3>Upload Bukti Pembayaran</h3>
            <p>Silakan upload bukti transfer daftar ulang agar admin bisa memverifikasi pembayaran Anda.</p>
            <a class="ppdb-btn" href="pembayaran.php">Upload Sekarang</a>
        </div>
    <?php endif; ?>

    <?php if ($status === 'diterima' && (!$pembayaran || $pembayaran['status'] !== 'dikonfirmasi')): ?>
        <div class="ppdb-cta">
            <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('star') ?></svg>
            <h3>Selamat, Anda diterima</h3>
            <p>Segera lakukan daftar ulang setelah pembayaran dikonfirmasi admin.</p>
            <?php if ($pembayaran && $pembayaran['status'] === 'dikonfirmasi'): ?>
                <a class="ppdb-btn" href="daftar-ulang.php">Lakukan Daftar Ulang</a>
            <?php else: ?>
                <span class="ppdb-waiting">
                    <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('clock') ?></svg>
                    Menunggu konfirmasi pembayaran
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (in_array($status, ['draft', 'perlu_perbaikan'])): ?>
        <div class="ppdb-cta">
            <svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('doc') ?></svg>
            <h3>Kirim Pendaftaran</h3>
            <p>Pastikan semua data dan dokumen sudah lengkap sebelum mengirim ke panitia.</p>
            <a class="ppdb-btn" href="kirim-pendaftaran.php">Kirim ke Panitia</a>
        </div>
    <?php endif; ?>

    <?php endif; ?>

    <!-- Pengumuman -->
    <?php if ($pengumuman): ?>
        <div class="ppdb-board">
            <h2><svg class="icon" viewBox="0 0 24 24"><?= ppdb_icon('megaphone') ?></svg> Pengumuman Terbaru</h2>
            <?php foreach ($pengumuman as $pg): ?>
                <div class="ppdb-board__item">
                    <h4><?= e($pg['judul']) ?></h4>
                    <p><?= nl2br(e($pg['isi'])) ?></p>
                    <small class="ppdb-board__date"><?= date('d/m/Y', strtotime($pg['created_at'])) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
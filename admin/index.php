<?php
declare(strict_types=1);

$cssBase = '../';

require __DIR__ . '/../includes/auth.php';
require_admin();

$user = current_user();

$stats = $pdo->query("
    SELECT
        COUNT(*) total,
        SUM(status='menunggu_verifikasi') menunggu,
        SUM(status='perlu_perbaikan') perbaikan,
        SUM(status='terverifikasi') terverifikasi,
        SUM(status='diterima') diterima,
        SUM(status='tidak_diterima') tidak_diterima,
        SUM(status='daftar_tunggu') tunggu,
        SUM(status='siswa_terdaftar') terdaftar
    FROM pendaftaran
")->fetch();

$pembayaranStats = $pdo->query("
    SELECT
        COUNT(*) total,
        SUM(status='menunggu') menunggu,
        SUM(status='dikonfirmasi') konfirmasi,
        SUM(status='ditolak') ditolak
    FROM pembayaran
")->fetch();

$pageTitle = 'Dashboard Admin - TK Harapan Bunda';
$activePage = 'admin';

require __DIR__ . '/../includes/header.php';
?>

<style>
/* =========================================================
   ADMIN DASHBOARD
   Tema Rapor & Kartu Pelajar
   ========================================================= */

.ppdb-admin-page {
    padding: 38px 0 80px;
    background:
        linear-gradient(rgba(231, 223, 206, .45) 1px, transparent 1px),
        #FBF7EF;
    background-size: 100% 34px;
}

/* ---------- Hero ---------- */

.ppdb-admin-hero {
    position: relative;
    overflow: hidden;
    margin-bottom: 28px;
    padding: 30px 34px;
    border: 1px solid #DCD3C0;
    border-radius: 22px;
    background: #2F4538;
    box-shadow: 0 12px 30px rgba(47, 69, 56, .12);
}

.ppdb-admin-hero::after {
    content: "";
    position: absolute;
    width: 180px;
    height: 180px;
    right: -55px;
    top: -75px;
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 50%;
    box-shadow:
        0 0 0 24px rgba(255,255,255,.035),
        0 0 0 48px rgba(255,255,255,.025);
}

.ppdb-admin-hero-inner {
    position: relative;
    z-index: 1;
}

.ppdb-admin-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 9px;
    color: #DCE9DE;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
}

.ppdb-admin-eyebrow svg {
    width: 16px;
    height: 16px;
}

.ppdb-admin-hero h1 {
    margin: 0;
    color: #FFFDF8;
    font-family: "Fraunces", Georgia, serif;
    font-size: clamp(30px, 5vw, 43px);
    line-height: 1.1;
}

.ppdb-admin-hero p {
    margin: 9px 0 0;
    color: #D8E2DA;
    font-size: 14px;
    line-height: 1.6;
}

/* ---------- Flash ---------- */

.ppdb-admin-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 22px;
    padding: 13px 16px;
    border: 1px solid #CFE1D2;
    border-radius: 13px;
    background: #E7F1E4;
    color: #315F3E;
    font-size: 13px;
    font-weight: 600;
}

.ppdb-admin-alert svg {
    width: 18px;
    height: 18px;
    flex: 0 0 18px;
}

/* ---------- Section Heading ---------- */

.ppdb-admin-section-heading {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 15px;
    margin: 0 0 14px;
}

.ppdb-admin-section-heading h2 {
    margin: 0;
    color: #2F4538;
    font-family: "Fraunces", Georgia, serif;
    font-size: 23px;
}

.ppdb-admin-section-heading p {
    margin: 4px 0 0;
    color: #7A7E70;
    font-size: 12px;
}

/* ---------- Statistics ---------- */

.ppdb-admin-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 30px;
}

.ppdb-admin-stat {
    position: relative;
    overflow: hidden;
    min-height: 135px;
    padding: 19px;
    border: 1px solid #E2DAC8;
    border-radius: 17px;
    background: #FFFDF8;
    box-shadow: 0 5px 18px rgba(47, 69, 56, .045);
}

.ppdb-admin-stat::after {
    content: "";
    position: absolute;
    right: -18px;
    bottom: -24px;
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: var(--soft);
}

.ppdb-admin-stat-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.ppdb-admin-stat-icon {
    width: 37px;
    height: 37px;
    display: grid;
    place-items: center;
    border-radius: 11px;
    background: var(--soft);
    color: var(--accent);
}

.ppdb-admin-stat-icon svg {
    width: 19px;
    height: 19px;
}

.ppdb-admin-stat-number {
    margin-top: 17px;
    color: var(--accent);
    font-family: "Fraunces", Georgia, serif;
    font-size: 29px;
    font-weight: 700;
    line-height: 1;
}

.ppdb-admin-stat-label {
    margin-top: 7px;
    color: #656A5D;
    font-size: 12px;
    font-weight: 700;
}

/* ---------- Quick Actions ---------- */

.ppdb-admin-actions {
    display: grid;
    grid-template-columns: 1.4fr 1fr 1fr .7fr;
    gap: 12px;
    margin-bottom: 34px;
}

.ppdb-admin-action {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 62px;
    padding: 12px 15px;
    border: 1px solid #DDD5C4;
    border-radius: 15px;
    background: #FFFDF8;
    color: #2F4538;
    text-decoration: none;
    transition:
        transform .18s ease,
        border-color .18s ease,
        background .18s ease;
}

.ppdb-admin-action:hover {
    transform: translateY(-2px);
    border-color: #BFCDBE;
    background: #F8F5EC;
}

.ppdb-admin-action.primary {
    border-color: #2F4538;
    background: #2F4538;
    color: #fff;
}

.ppdb-admin-action.primary:hover {
    background: #3E5A48;
}

.ppdb-admin-action-icon {
    width: 35px;
    height: 35px;
    flex: 0 0 35px;
    display: grid;
    place-items: center;
    border-radius: 10px;
    background: #E7F1E4;
    color: #3F7E52;
}

.ppdb-admin-action.primary .ppdb-admin-action-icon {
    background: rgba(255,255,255,.12);
    color: #fff;
}

.ppdb-admin-action-icon svg {
    width: 18px;
    height: 18px;
}

.ppdb-admin-action-text {
    min-width: 0;
}

.ppdb-admin-action-title {
    display: block;
    font-size: 13px;
    font-weight: 800;
}

.ppdb-admin-action-sub {
    display: block;
    margin-top: 2px;
    color: #85897C;
    font-size: 10px;
}

.ppdb-admin-action.primary .ppdb-admin-action-sub {
    color: #D5E0D8;
}

/* ---------- Table Card ---------- */

.ppdb-admin-table-card {
    overflow: hidden;
    border: 1px solid #E2DAC8;
    border-radius: 20px;
    background: #FFFDF8;
    box-shadow: 0 8px 25px rgba(47, 69, 56, .055);
}

.ppdb-admin-table-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 20px 22px;
    border-bottom: 1px solid #E7DFCE;
}

.ppdb-admin-table-title {
    display: flex;
    align-items: center;
    gap: 11px;
}

.ppdb-admin-table-title-icon {
    width: 38px;
    height: 38px;
    display: grid;
    place-items: center;
    border-radius: 11px;
    background: #E7F1E4;
    color: #3F7E52;
}

.ppdb-admin-table-title-icon svg {
    width: 19px;
    height: 19px;
}

.ppdb-admin-table-title h2 {
    margin: 0;
    color: #2F4538;
    font-family: "Fraunces", Georgia, serif;
    font-size: 20px;
}

.ppdb-admin-table-title p {
    margin: 2px 0 0;
    color: #8A8D80;
    font-size: 11px;
}

.ppdb-admin-count {
    padding: 6px 10px;
    border: 1px solid #DDE5DB;
    border-radius: 999px;
    background: #F1F6EF;
    color: #3F7E52;
    font-size: 11px;
    font-weight: 800;
}

/* ---------- Table ---------- */

.ppdb-admin-table-wrap {
    overflow-x: auto;
}

.ppdb-admin-table {
    width: 100%;
    min-width: 760px;
    border-collapse: collapse;
}

.ppdb-admin-table th {
    padding: 12px 18px;
    border-bottom: 1px solid #E7DFCE;
    background: #FBF7EF;
    color: #777B6E;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .07em;
    text-align: left;
    text-transform: uppercase;
}

.ppdb-admin-table td {
    padding: 15px 18px;
    border-bottom: 1px solid #EEE8DA;
    color: #4C5147;
    font-size: 12px;
    vertical-align: middle;
}

.ppdb-admin-table tbody tr:last-child td {
    border-bottom: 0;
}

.ppdb-admin-table tbody tr {
    transition: background .15s ease;
}

.ppdb-admin-table tbody tr:hover {
    background: #FCFAF4;
}

.ppdb-admin-number {
    color: #2F4538;
    font-family: "Fraunces", Georgia, serif;
    font-size: 13px;
    font-weight: 700;
}

.ppdb-admin-student {
    color: #2D322B;
    font-weight: 750;
}

.ppdb-admin-parent {
    color: #656A5D;
}

.ppdb-admin-date {
    color: #85897C;
    white-space: nowrap;
}

/* ---------- Status Badge ---------- */

.ppdb-admin-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 9px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 800;
    white-space: nowrap;
}

.ppdb-admin-status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

/* ---------- Detail Button ---------- */

.ppdb-admin-detail {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 11px;
    border: 1px solid #D7D0BF;
    border-radius: 9px;
    background: #FFFDF8;
    color: #3E5A48;
    font-size: 11px;
    font-weight: 800;
    text-decoration: none;
}

.ppdb-admin-detail:hover {
    border-color: #AEBEAF;
    background: #F1F5EE;
}

.ppdb-admin-detail svg {
    width: 13px;
    height: 13px;
}

/* ---------- Empty ---------- */

.ppdb-admin-empty {
    padding: 45px 20px;
    text-align: center;
    color: #85897C;
    font-size: 13px;
}

/* ---------- Responsive ---------- */

@media (max-width: 1000px) {

    .ppdb-admin-stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .ppdb-admin-actions {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {

    .ppdb-admin-page {
        padding: 25px 0 60px;
    }

    .ppdb-admin-hero {
        padding: 25px 21px;
        border-radius: 18px;
    }

    .ppdb-admin-stats {
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .ppdb-admin-stat {
        min-height: 120px;
        padding: 15px;
    }

    .ppdb-admin-stat-number {
        font-size: 25px;
    }

    .ppdb-admin-actions {
        grid-template-columns: 1fr;
    }

    .ppdb-admin-table-head {
        padding: 17px;
    }
}
</style>

<section class="ppdb-admin-page">
    <div class="container">

        <!-- HERO -->
        <div class="ppdb-admin-hero">
            <div class="ppdb-admin-hero-inner">

                <div class="ppdb-admin-eyebrow">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M4 4h16v16H4z"/>
                        <path d="M8 8h8"/>
                        <path d="M8 12h5"/>
                        <path d="M8 16h8"/>
                    </svg>
                    Panel Administrasi PPDB
                </div>

                <h1>Dashboard Admin</h1>

                <p>
                    Selamat datang, <?= e($user['nama']) ?>.
                    Pantau pendaftaran dan kelola proses PPDB dari sini.
                </p>

            </div>
        </div>

        <?php if ($m = flash('success')): ?>

            <div class="ppdb-admin-alert">
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="m5 12 4 4L19 6"/>
                </svg>

                <?= e($m) ?>
            </div>

        <?php endif; ?>


        <!-- STATISTIK -->
        <div class="ppdb-admin-section-heading">
            <div>
                <h2>Ringkasan Pendaftaran</h2>
                <p>Gambaran singkat kondisi PPDB saat ini.</p>
            </div>
        </div>

        <div class="ppdb-admin-stats">

            <?php
            $cards = [
                [
                    'label' => 'Total Pendaftar',
                    'value' => $stats['total'],
                    'accent' => '#2F4538',
                    'soft' => '#E7F1E4',
                    'icon' => 'users'
                ],
                [
                    'label' => 'Menunggu Verifikasi',
                    'value' => $stats['menunggu'],
                    'accent' => '#C6820E',
                    'soft' => '#FBF0DC',
                    'icon' => 'clock'
                ],
                [
                    'label' => 'Perlu Perbaikan',
                    'value' => $stats['perbaikan'],
                    'accent' => '#B23C3C',
                    'soft' => '#F8E7E4',
                    'icon' => 'alert'
                ],
                [
                    'label' => 'Terverifikasi',
                    'value' => $stats['terverifikasi'],
                    'accent' => '#3F7E52',
                    'soft' => '#E7F1E4',
                    'icon' => 'check'
                ],
                [
                    'label' => 'Diterima',
                    'value' => $stats['diterima'],
                    'accent' => '#3F7E52',
                    'soft' => '#E7F1E4',
                    'icon' => 'award'
                ],
                [
                    'label' => 'Tidak Diterima',
                    'value' => $stats['tidak_diterima'],
                    'accent' => '#B23C3C',
                    'soft' => '#F8E7E4',
                    'icon' => 'x'
                ],
                [
                    'label' => 'Daftar Tunggu',
                    'value' => $stats['tunggu'],
                    'accent' => '#C6820E',
                    'soft' => '#FBF0DC',
                    'icon' => 'list'
                ],
                [
                    'label' => 'Siswa Terdaftar',
                    'value' => $stats['terdaftar'],
                    'accent' => '#3E5A48',
                    'soft' => '#E7F1E4',
                    'icon' => 'school'
                ]
            ];
            ?>

            <?php foreach ($cards as $card): ?>

                <div
                    class="ppdb-admin-stat"
                    style="
                        --accent: <?= $card['accent'] ?>;
                        --soft: <?= $card['soft'] ?>;
                    "
                >

                    <div class="ppdb-admin-stat-top">

                        <div class="ppdb-admin-stat-icon">

                            <?php if ($card['icon'] === 'users'): ?>

                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>

                            <?php elseif ($card['icon'] === 'clock'): ?>

                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="M12 7v5l3 2"/>
                                </svg>

                            <?php elseif ($card['icon'] === 'alert'): ?>

                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M10.3 3.7 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"/>
                                    <path d="M12 9v4"/>
                                    <path d="M12 17h.01"/>
                                </svg>

                            <?php elseif ($card['icon'] === 'check'): ?>

                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="m8 12 2.5 2.5L16 9"/>
                                </svg>

                            <?php elseif ($card['icon'] === 'award'): ?>

                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="5"/>
                                    <path d="m8.5 12.5-1 8 4.5-2 4.5 2-1-8"/>
                                </svg>

                            <?php elseif ($card['icon'] === 'x'): ?>

                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="m9 9 6 6"/>
                                    <path d="m15 9-6 6"/>
                                </svg>

                            <?php elseif ($card['icon'] === 'list'): ?>

                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M8 6h12"/>
                                    <path d="M8 12h12"/>
                                    <path d="M8 18h12"/>
                                    <path d="M4 6h.01"/>
                                    <path d="M4 12h.01"/>
                                    <path d="M4 18h.01"/>
                                </svg>

                            <?php else: ?>

                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M4 20V8l8-5 8 5v12"/>
                                    <path d="M8 20v-5h8v5"/>
                                    <path d="M9 10h6"/>
                                </svg>

                            <?php endif; ?>

                        </div>

                    </div>

                    <div class="ppdb-admin-stat-number">
                        <?= (int) $card['value'] ?>
                    </div>

                    <div class="ppdb-admin-stat-label">
                        <?= e($card['label']) ?>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>


        <!-- AKSI CEPAT -->
        <div class="ppdb-admin-section-heading">
            <div>
                <h2>Akses Cepat</h2>
                <p>Kelola bagian penting PPDB dengan cepat.</p>
            </div>
        </div>

        <div class="ppdb-admin-actions">

            <a class="ppdb-admin-action primary" href="pendaftar.php">

                <div class="ppdb-admin-action-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M19 8v6"/>
                        <path d="M22 11h-6"/>
                    </svg>
                </div>

                <div class="ppdb-admin-action-text">
                    <span class="ppdb-admin-action-title">
                        Lihat Semua Pendaftar
                    </span>
                    <span class="ppdb-admin-action-sub">
                        Periksa dan verifikasi data
                    </span>
                </div>

            </a>


            <a class="ppdb-admin-action" href="pembayaran.php">

                <div class="ppdb-admin-action-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="M3 10h18"/>
                        <path d="M7 15h3"/>
                    </svg>
                </div>

                <div class="ppdb-admin-action-text">
                    <span class="ppdb-admin-action-title">
                        Kelola Pembayaran
                    </span>
                    <span class="ppdb-admin-action-sub">
                        <?= (int) $pembayaranStats['total'] ?> data pembayaran
                    </span>
                </div>

            </a>


            <a class="ppdb-admin-action" href="pengumuman.php">

                <div class="ppdb-admin-action-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M4 14h3l8 4V6l-8 4H4z"/>
                        <path d="M7 14v5"/>
                        <path d="M19 9a4 4 0 0 1 0 6"/>
                    </svg>
                </div>

                <div class="ppdb-admin-action-text">
                    <span class="ppdb-admin-action-title">
                        Pengumuman
                    </span>
                    <span class="ppdb-admin-action-sub">
                        Kelola informasi PPDB
                    </span>
                </div>

            </a>


            <a class="ppdb-admin-action" href="../logout.php">

                <div class="ppdb-admin-action-icon">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M10 17l5-5-5-5"/>
                        <path d="M15 12H3"/>
                        <path d="M21 19V5a2 2 0 0 0-2-2h-6"/>
                    </svg>
                </div>

                <div class="ppdb-admin-action-text">
                    <span class="ppdb-admin-action-title">
                        Keluar
                    </span>
                    <span class="ppdb-admin-action-sub">
                        Logout akun
                    </span>
                </div>

            </a>

        </div>


        <!-- PENDAFTAR TERBARU -->
        <?php
        $rows = $pdo->query("
            SELECT
                p.*,
                cs.nama_lengkap,
                u.nama nama_user
            FROM pendaftaran p
            LEFT JOIN calon_siswa cs
                ON cs.pendaftaran_id = p.id
            LEFT JOIN users u
                ON u.id = p.user_id
            ORDER BY p.updated_at DESC
            LIMIT 10
        ")->fetchAll();
        ?>

        <div class="ppdb-admin-table-card">

            <div class="ppdb-admin-table-head">

                <div class="ppdb-admin-table-title">

                    <div class="ppdb-admin-table-title-icon">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M4 4h16v16H4z"/>
                            <path d="M8 8h8"/>
                            <path d="M8 12h8"/>
                            <path d="M8 16h5"/>
                        </svg>
                    </div>

                    <div>
                        <h2>Pendaftar Terbaru</h2>
                        <p>10 pendaftaran yang terakhir diperbarui</p>
                    </div>

                </div>

                <div class="ppdb-admin-count">
                    <?= count($rows) ?> Data
                </div>

            </div>


            <div class="ppdb-admin-table-wrap">

                <table class="ppdb-admin-table">

                    <thead>
                        <tr>
                            <th>No. Pendaftaran</th>
                            <th>Calon Siswa</th>
                            <th>Orang Tua</th>
                            <th>Tgl Daftar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!$rows): ?>

                        <tr>
                            <td colspan="6">
                                <div class="ppdb-admin-empty">
                                    Belum ada data pendaftar.
                                </div>
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php
                        $statusColors = [
                            'draft' => [
                                'text' => '#6B7060',
                                'bg' => '#F0EEE7'
                            ],
                            'menunggu_verifikasi' => [
                                'text' => '#95650B',
                                'bg' => '#FBF0DC'
                            ],
                            'diterima' => [
                                'text' => '#356B45',
                                'bg' => '#E7F1E4'
                            ],
                            'tidak_diterima' => [
                                'text' => '#A83B3B',
                                'bg' => '#F8E7E4'
                            ],
                            'perlu_perbaikan' => [
                                'text' => '#A83B3B',
                                'bg' => '#F8E7E4'
                            ],
                            'siswa_terdaftar' => [
                                'text' => '#315F3E',
                                'bg' => '#E1EDE0'
                            ],
                            'terverifikasi' => [
                                'text' => '#356B45',
                                'bg' => '#E7F1E4'
                            ],
                            'daftar_tunggu' => [
                                'text' => '#95650B',
                                'bg' => '#FBF0DC'
                            ]
                        ];
                        ?>

                        <?php foreach ($rows as $r): ?>

                            <?php
                            $statusStyle =
                                $statusColors[$r['status']]
                                ?? [
                                    'text' => '#6B7060',
                                    'bg' => '#F0EEE7'
                                ];
                            ?>

                            <tr>

                                <td>
                                    <div class="ppdb-admin-number">
                                        <?= e($r['nomor_pendaftaran']) ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="ppdb-admin-student">
                                        <?= e($r['nama_lengkap'] ?? '-') ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="ppdb-admin-parent">
                                        <?= e($r['nama_user'] ?? '-') ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="ppdb-admin-date">
                                        <?= $r['tanggal_daftar']
                                            ? date(
                                                'd/m/Y',
                                                strtotime($r['tanggal_daftar'])
                                            )
                                            : '-' ?>
                                    </div>
                                </td>

                                <td>

                                    <span
                                        class="ppdb-admin-status"
                                        style="
                                            color: <?= $statusStyle['text'] ?>;
                                            background: <?= $statusStyle['bg'] ?>;
                                        "
                                    >
                                        <span class="ppdb-admin-status-dot"></span>
                                        <?= e(status_label($r['status'])) ?>
                                    </span>

                                </td>

                                <td>

                                    <a
                                        class="ppdb-admin-detail"
                                        href="detail.php?id=<?= (int) $r['id'] ?>"
                                    >
                                        Detail

                                        <svg viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.8"
                                             stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <path d="M5 12h14"/>
                                            <path d="m13 6 6 6-6 6"/>
                                        </svg>
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
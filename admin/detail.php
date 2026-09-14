<?php
declare(strict_types=1);

$cssBase = '../';

require __DIR__ . '/../includes/auth.php';
require_admin();

$adminUser = current_user();

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    redirect('pendaftar.php');
}

/*
|--------------------------------------------------------------------------
| Ambil data pendaftaran
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT
        p.*,

        /* Data calon siswa */
        cs.nama_lengkap,
        cs.nama_panggilan,
        cs.tempat_lahir,
        cs.tanggal_lahir,
        cs.nik,
        cs.nomor_kk,
        cs.agama,
        cs.anak_ke,
        cs.jumlah_saudara,
        cs.berat_badan,
        cs.tinggi_badan,
        cs.lingkar_kepala,
        cs.penghasilan_orang_tua,
        cs.jarak_sekolah,
        cs.alamat AS cs_alamat,
        cs.jenis_kelamin,

        /* Data ayah */
        ot.nama_ayah,
        ot.tempat_lahir_ayah,
        ot.tanggal_lahir_ayah,
        ot.nik_ayah,
        ot.agama_ayah,
        ot.pendidikan_ayah,
        ot.pekerjaan_ayah,
        ot.no_hp_ayah,

        /* Data ibu */
        ot.nama_ibu,
        ot.tempat_lahir_ibu,
        ot.tanggal_lahir_ibu,
        ot.nik_ibu,
        ot.agama_ibu,
        ot.pendidikan_ibu,
        ot.pekerjaan_ibu,
        ot.no_hp_ibu,

        /* Akun orang tua */
        u.nama AS nama_user,
        u.email AS email_user

    FROM pendaftaran p
    LEFT JOIN calon_siswa cs
        ON cs.pendaftaran_id = p.id
    LEFT JOIN orang_tua ot
        ON ot.pendaftaran_id = p.id
    LEFT JOIN users u
        ON u.id = p.user_id
    WHERE p.id = ?
");

$stmt->execute([$id]);
$r = $stmt->fetch();

if (!$r) {
    flash('success', 'Data tidak ditemukan.');
    redirect('pendaftar.php');
}

/*
|--------------------------------------------------------------------------
| Dokumen
|--------------------------------------------------------------------------
*/
$doks = $pdo->prepare("
    SELECT *
    FROM dokumen
    WHERE pendaftaran_id = ?
    ORDER BY uploaded_at DESC
");

$doks->execute([$id]);
$dokumen = $doks->fetchAll();

/*
|--------------------------------------------------------------------------
| Riwayat verifikasi
|--------------------------------------------------------------------------
*/
$logs = $pdo->prepare("
    SELECT
        v.*,
        u.nama AS admin_nama
    FROM verifikasi v
    JOIN users u
        ON u.id = v.admin_id
    WHERE v.pendaftaran_id = ?
    ORDER BY v.created_at DESC
");

$logs->execute([$id]);
$riwayat = $logs->fetchAll();

/*
|--------------------------------------------------------------------------
| Proses aksi admin
|--------------------------------------------------------------------------
*/
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $catatan = trim($_POST['catatan'] ?? '');
    $hasil = trim($_POST['hasil_seleksi'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | Update status dokumen
    |--------------------------------------------------------------------------
    */
    if ($aksi === 'dok_status') {
        $dokId = (int)($_POST['dok_id'] ?? 0);
        $dokStat = $_POST['dok_status'] ?? '';
        $alasan = trim($_POST['alasan'] ?? '');

        if (!in_array($dokStat, ['menunggu', 'valid', 'tidak_valid'], true)) {
            $errors[] = 'Status dokumen tidak valid.';
        } else {
            $pdo->prepare("
                UPDATE dokumen
                SET status = ?, alasan = ?
                WHERE id = ?
                  AND pendaftaran_id = ?
            ")->execute([
                $dokStat,
                $alasan !== '' ? $alasan : null,
                $dokId,
                $id
            ]);

            flash('success', 'Status dokumen diperbarui.');
            redirect("detail.php?id=$id");
        }

    /*
    |--------------------------------------------------------------------------
    | Verifikasi / Setujui
    |--------------------------------------------------------------------------
    */
    } elseif ($aksi === 'setujui') {

        $pdo->prepare("
            UPDATE pendaftaran
            SET status = 'terverifikasi',
                catatan_admin = ?
            WHERE id = ?
        ")->execute([
            $catatan !== '' ? $catatan : null,
            $id
        ]);

        $pdo->prepare("
            INSERT INTO verifikasi
                (pendaftaran_id, admin_id, aksi, catatan)
            VALUES (?, ?, ?, ?)
        ")->execute([
            $id,
            $adminUser['id'],
            'setujui',
            $catatan !== '' ? $catatan : null
        ]);

        flash('success', 'Pendaftaran berhasil diverifikasi.');
        redirect("detail.php?id=$id");

    /*
    |--------------------------------------------------------------------------
    | Minta perbaikan
    |--------------------------------------------------------------------------
    */
    } elseif ($aksi === 'perbaikan') {

        if ($catatan === '') {
            $errors[] = 'Alasan perbaikan wajib diisi.';
        } else {
            $pdo->prepare("
                UPDATE pendaftaran
                SET status = 'perlu_perbaikan',
                    catatan_admin = ?
                WHERE id = ?
            ")->execute([
                $catatan,
                $id
            ]);

            $pdo->prepare("
                INSERT INTO verifikasi
                    (pendaftaran_id, admin_id, aksi, catatan)
                VALUES (?, ?, ?, ?)
            ")->execute([
                $id,
                $adminUser['id'],
                'perbaikan',
                $catatan
            ]);

            flash('success', 'Permintaan perbaikan berhasil dikirim.');
            redirect("detail.php?id=$id");
        }

    /*
    |--------------------------------------------------------------------------
    | Diterima
    |--------------------------------------------------------------------------
    */
    } elseif ($aksi === 'diterima') {

        if ($hasil === '') {
            $errors[] = 'Keterangan hasil seleksi wajib diisi.';
        } else {
            $pdo->prepare("
                UPDATE pendaftaran
                SET status = 'diterima',
                    catatan_admin = ?,
                    hasil_seleksi = ?
                WHERE id = ?
            ")->execute([
                $catatan !== '' ? $catatan : null,
                $hasil,
                $id
            ]);

            $pdo->prepare("
                INSERT INTO verifikasi
                    (pendaftaran_id, admin_id, aksi, catatan)
                VALUES (?, ?, ?, ?)
            ")->execute([
                $id,
                $adminUser['id'],
                'hasil',
                $catatan !== '' ? $catatan : null
            ]);

            flash('success', 'Pendaftar berhasil diterima.');
            redirect("detail.php?id=$id");
        }

    /*
    |--------------------------------------------------------------------------
    | Tidak diterima
    |--------------------------------------------------------------------------
    */
    } elseif ($aksi === 'tidak_diterima') {

        $pdo->prepare("
            UPDATE pendaftaran
            SET status = 'tidak_diterima',
                catatan_admin = ?
            WHERE id = ?
        ")->execute([
            $catatan !== '' ? $catatan : null,
            $id
        ]);

        $pdo->prepare("
            INSERT INTO verifikasi
                (pendaftaran_id, admin_id, aksi, catatan)
            VALUES (?, ?, ?, ?)
        ")->execute([
            $id,
            $adminUser['id'],
            'tolak',
            $catatan !== '' ? $catatan : null
        ]);

        flash('success', 'Pendaftar ditandai tidak diterima.');
        redirect("detail.php?id=$id");

    /*
    |--------------------------------------------------------------------------
    | Daftar tunggu
    |--------------------------------------------------------------------------
    */
    } elseif ($aksi === 'tunggu') {

        $pdo->prepare("
            UPDATE pendaftaran
            SET status = 'daftar_tunggu'
            WHERE id = ?
        ")->execute([$id]);

        flash('success', 'Status pendaftar menjadi Daftar Tunggu.');
        redirect("detail.php?id=$id");
    }
}

/*
|--------------------------------------------------------------------------
| Page
|--------------------------------------------------------------------------
*/
$pageTitle = 'Detail Pendaftaran - Admin';
$activePage = 'admin';

$statusColors = [
    'draft'              => '#6B7060',
    'menunggu_verifikasi' => '#C6820E',
    'terverifikasi'      => '#3F7E52',
    'diterima'           => '#3F7E52',
    'tidak_diterima'     => '#B23C3C',
    'perlu_perbaikan'    => '#B23C3C',
    'daftar_tunggu'      => '#35618A',
    'siswa_terdaftar'    => '#2F4538',
];

$bc = $statusColors[$r['status'] ?? 'draft'] ?? '#6B7060';

require __DIR__ . '/../includes/header.php';
?>

<style>
    .ppdb-detail-page {
        --paper: #FBF7EF;
        --paper-line: #E7DFCE;
        --chalk: #2F4538;
        --leaf: #3E5A48;
        --ink: #262A20;
        --muted: #6B7060;
        --coral: #E1552E;
        --green: #3F7E52;
        --soft-green: #E7F1E4;
        --amber: #C6820E;
        --soft-amber: #FBF0DC;
        --berry: #B23C3C;
        --soft-berry: #F8E7E4;
        --blue: #35618A;
        --soft-blue: #E5EEF5;
    }

    .ppdb-detail-page {
        background: var(--paper);
        min-height: calc(100vh - 100px);
        padding: 28px 0 60px;
    }

    .ppdb-detail-page .container {
        max-width: 1180px;
    }

    /* HERO */

    .detail-hero {
        background: var(--chalk);
        border-radius: 22px;
        padding: 28px 30px;
        color: #fff;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .detail-hero::after {
        content: "";
        position: absolute;
        width: 170px;
        height: 170px;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 50%;
        right: -55px;
        top: -65px;
    }

    .detail-hero-inner {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .detail-eyebrow {
        display: block;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #D9E5D7;
        margin-bottom: 7px;
    }

    .detail-hero h1 {
        margin: 0;
        font-family: 'Fraunces', Georgia, serif;
        font-size: clamp(28px, 4vw, 38px);
        line-height: 1.1;
        color: #fff;
    }

    .detail-number {
        margin: 8px 0 0;
        color: #C9D5CB;
        font-size: 13px;
    }

    .detail-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        border-radius: 999px;
        background: #fff;
        color: var(--chalk);
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    /* ALERT */

    .detail-alert {
        border-radius: 14px;
        padding: 13px 16px;
        margin-bottom: 16px;
        font-size: 13px;
    }

    .detail-alert-error {
        background: var(--soft-berry);
        color: var(--berry);
        border: 1px solid #EBCAC5;
    }

    .detail-alert-success {
        background: var(--soft-green);
        color: var(--green);
        border: 1px solid #CFE1CA;
    }

    .detail-alert ul {
        margin: 0;
        padding-left: 18px;
    }

    /* BACK */

    .detail-back {
        margin-bottom: 18px;
    }

    .detail-back a {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: var(--chalk);
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        padding: 9px 13px;
        background: #fff;
        border: 1px solid var(--paper-line);
        border-radius: 11px;
        transition: .2s ease;
    }

    .detail-back a:hover {
        border-color: var(--chalk);
        transform: translateY(-1px);
    }

    /* GRID */

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .detail-card {
        background: #fff;
        border: 1px solid var(--paper-line);
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 5px 18px rgba(47,69,56,.04);
    }

    .detail-card.full {
        grid-column: 1 / -1;
    }

    .detail-card-head {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        margin-bottom: 17px;
        padding-bottom: 13px;
        border-bottom: 1px solid var(--paper-line);
    }

    .detail-card-icon {
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: var(--soft-green);
        color: var(--leaf);
    }

    .detail-card-icon svg {
        width: 19px;
        height: 19px;
    }

    .detail-card h2 {
        margin: 0;
        color: var(--chalk);
        font-family: 'Fraunces', Georgia, serif;
        font-size: 21px;
        line-height: 1.2;
    }

    .detail-card-subtitle {
        margin: 3px 0 0;
        color: var(--muted);
        font-size: 12px;
    }

    /* TABLE */

    .detail-table {
        width: 100%;
        border-collapse: collapse;
    }

    .detail-table tr + tr {
        border-top: 1px solid #F0EBDD;
    }

    .detail-table td {
        padding: 10px 4px;
        vertical-align: top;
        font-size: 13px;
        line-height: 1.5;
    }

    .detail-table td:first-child {
        width: 42%;
        color: var(--muted);
        font-weight: 700;
        padding-right: 12px;
    }

    .detail-table td:last-child {
        color: var(--ink);
        font-weight: 600;
        word-break: break-word;
    }

    .detail-table .section-row td {
        padding-top: 16px;
        padding-bottom: 8px;
        color: var(--leaf);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
        border-top: 0;
    }

    /* PARENT BOX */

    .parent-box {
        background: var(--paper);
        border: 1px solid var(--paper-line);
        border-radius: 14px;
        padding: 15px;
    }

    .parent-box + .parent-box {
        margin-top: 12px;
    }

    .parent-title {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--chalk);
        font-size: 14px;
        font-weight: 900;
        margin-bottom: 7px;
    }

    .parent-title span {
        width: 27px;
        height: 27px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: var(--chalk);
        color: #fff;
        font-size: 11px;
    }

    /* DOCUMENT */

    .document-item {
        border: 1px solid var(--paper-line);
        border-radius: 14px;
        padding: 14px;
        background: #fff;
    }

    .document-item + .document-item {
        margin-top: 10px;
    }

    .document-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .document-name {
        min-width: 0;
        color: var(--ink);
        font-size: 13px;
        font-weight: 800;
    }

    .document-name a {
        color: var(--leaf);
        text-decoration: none;
    }

    .document-name a:hover {
        text-decoration: underline;
    }

    .document-status {
        padding: 5px 10px;
        border-radius: 999px;
        color: #fff;
        font-size: 11px;
        font-weight: 900;
        white-space: nowrap;
    }

    .document-reason {
        margin: 7px 0 0;
        color: var(--berry);
        font-size: 12px;
    }

    .document-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 11px;
    }

    /* FORMS */

    .detail-form {
        display: grid;
        gap: 13px;
        max-width: 620px;
    }

    .detail-form label {
        display: grid;
        gap: 6px;
        color: var(--ink);
        font-size: 13px;
        font-weight: 800;
    }

    .detail-form select,
    .detail-form input,
    .detail-form textarea,
    .document-form select,
    .document-form input {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid var(--paper-line);
        border-radius: 11px;
        background: #fff;
        color: var(--ink);
        font-family: inherit;
        font-size: 13px;
        outline: none;
        transition: .2s ease;
    }

    .detail-form select:focus,
    .detail-form input:focus,
    .detail-form textarea:focus,
    .document-form select:focus,
    .document-form input:focus {
        border-color: var(--leaf);
        box-shadow: 0 0 0 3px rgba(62,90,72,.08);
    }

    .detail-form textarea {
        resize: vertical;
    }

    .document-form select {
        width: auto;
        min-width: 130px;
    }

    .document-form input {
        flex: 1;
        min-width: 180px;
    }

    .detail-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 16px;
        border: 0;
        border-radius: 11px;
        background: var(--chalk);
        color: #fff;
        font-family: inherit;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: .2s ease;
    }

    .detail-btn:hover {
        background: var(--leaf);
        transform: translateY(-1px);
    }

    /* EMPTY */

    .detail-empty {
        padding: 25px 10px;
        text-align: center;
        color: var(--muted);
        font-size: 13px;
    }

    /* HISTORY */

    .history-item {
        border-left: 3px solid var(--green);
        padding: 11px 14px;
        background: var(--paper);
        border-radius: 0 11px 11px 0;
    }

    .history-item + .history-item {
        margin-top: 8px;
    }

    .history-title {
        color: var(--ink);
        font-size: 13px;
        font-weight: 800;
    }

    .history-title em {
        color: var(--leaf);
        font-style: normal;
    }

    .history-note {
        margin: 5px 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .history-date {
        color: var(--muted);
        font-size: 11px;
    }

    /* RESPONSIVE */

    @media (max-width: 800px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .detail-card.full {
            grid-column: auto;
        }

        .detail-hero-inner {
            align-items: flex-start;
            flex-direction: column;
        }

        .detail-status {
            align-self: flex-start;
        }
    }

    @media (max-width: 600px) {
        .ppdb-detail-page {
            padding-top: 20px;
        }

        .detail-hero {
            padding: 22px;
            border-radius: 17px;
        }

        .detail-card {
            padding: 17px;
            border-radius: 15px;
        }

        .detail-table td {
            display: block;
            width: auto !important;
            padding: 7px 3px;
        }

        .detail-table td:first-child {
            padding-bottom: 1px;
        }

        .detail-table td:last-child {
            padding-top: 1px;
        }

        .document-form {
            align-items: stretch;
        }

        .document-form select,
        .document-form input,
        .document-form .detail-btn {
            width: 100%;
        }
    }
</style>

<div class="ppdb-detail-page">

    <div class="container">

        <!-- HERO -->
        <section class="detail-hero">
            <div class="detail-hero-inner">

                <div>
                    <span class="detail-eyebrow">Dashboard Admin</span>

                    <h1>Detail Pendaftaran</h1>

                    <p class="detail-number">
                        Nomor Pendaftaran:
                        <strong><?= e($r['nomor_pendaftaran'] ?? '-') ?></strong>
                    </p>
                </div>

                <span class="detail-status">
                    <?= e(status_label($r['status'] ?? 'draft')) ?>
                </span>

            </div>
        </section>

        <!-- ALERT -->
        <?php if ($errors): ?>
            <div class="detail-alert detail-alert-error">
                <ul>
                    <?php foreach ($errors as $er): ?>
                        <li><?= e($er) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($m = flash('success')): ?>
            <div class="detail-alert detail-alert-success">
                <?= e($m) ?>
            </div>
        <?php endif; ?>

        <!-- BACK -->
        <div class="detail-back">
            <a href="pendaftar.php">
                <span>&larr;</span>
                Kembali ke Data Pendaftar
            </a>
        </div>

        <div class="detail-grid">

            <!-- DATA CALON SISWA -->
            <section class="detail-card">

                <div class="detail-card-head">
                    <div class="detail-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="8" r="3.5"/>
                            <path d="M5 20c.8-3.4 3.1-5 7-5s6.2 1.6 7 5"/>
                        </svg>
                    </div>

                    <div>
                        <h2>Data Calon Siswa</h2>
                        <p class="detail-card-subtitle">
                            Informasi lengkap peserta didik
                        </p>
                    </div>
                </div>

                <table class="detail-table">

                    <tr>
                        <td>Nama Lengkap</td>
                        <td><?= e($r['nama_lengkap'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <td>Nama Panggilan</td>
                        <td><?= e($r['nama_panggilan'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <td>Tempat, Tanggal Lahir</td>
                        <td>
                            <?= e($r['tempat_lahir'] ?? '-') ?>,
                            <?= !empty($r['tanggal_lahir'])
                                ? date('d/m/Y', strtotime($r['tanggal_lahir']))
                                : '-' ?>
                        </td>
                    </tr>

                    <tr>
                        <td>NIK</td>
                        <td><?= e($r['nik'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <td>Nomor KK</td>
                        <td><?= e($r['nomor_kk'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <td>Agama</td>
                        <td><?= e($r['agama'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <td>Jenis Kelamin</td>
                        <td><?= e($r['jenis_kelamin'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <td>Anak Ke-</td>
                        <td><?= e($r['anak_ke'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <td>Jumlah Saudara Kandung</td>
                        <td><?= e($r['jumlah_saudara'] ?? '-') ?> orang</td>
                    </tr>

                    <tr>
                        <td>Berat Badan</td>
                        <td><?= e($r['berat_badan'] ?? '-') ?> kg</td>
                    </tr>

                    <tr>
                        <td>Tinggi Badan</td>
                        <td><?= e($r['tinggi_badan'] ?? '-') ?> cm</td>
                    </tr>

                    <tr>
                        <td>Lingkar Kepala</td>
                        <td><?= e($r['lingkar_kepala'] ?? '-') ?> cm</td>
                    </tr>

                    <tr>
                        <td>Penghasilan Orang Tua</td>
                        <td><?= e($r['penghasilan_orang_tua'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <td>Jarak Tempuh Sekolah</td>
                        <td><?= e($r['jarak_sekolah'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <td>Program</td>
                        <td><?= e($r['program'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <td>Alamat Rumah</td>
                        <td><?= nl2br(e($r['cs_alamat'] ?? '-')) ?></td>
                    </tr>

                </table>

            </section>


            <!-- DATA ORANG TUA -->
            <section class="detail-card">

                <div class="detail-card-head">
                    <div class="detail-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="9" cy="8" r="3"/>
                            <circle cx="17" cy="9" r="2.5"/>
                            <path d="M3.5 20c.6-3.3 2.5-5 5.5-5s4.9 1.7 5.5 5"/>
                            <path d="M14 15.5c2.7-.2 4.7 1.2 5.5 4.5"/>
                        </svg>
                    </div>

                    <div>
                        <h2>Data Orang Tua</h2>
                        <p class="detail-card-subtitle">
                            Informasi ayah dan ibu peserta didik
                        </p>
                    </div>
                </div>


                <!-- AYAH -->
                <div class="parent-box">

                    <div class="parent-title">
                        <span>AY</span>
                        Data Ayah
                    </div>

                    <table class="detail-table">

                        <tr>
                            <td>Nama</td>
                            <td><?= e($r['nama_ayah'] ?? '-') ?></td>
                        </tr>

                        <tr>
                            <td>Tempat, Tanggal Lahir</td>
                            <td>
                                <?= e($r['tempat_lahir_ayah'] ?? '-') ?>,
                                <?= !empty($r['tanggal_lahir_ayah'])
                                    ? date('d/m/Y', strtotime($r['tanggal_lahir_ayah']))
                                    : '-' ?>
                            </td>
                        </tr>

                        <tr>
                            <td>NIK</td>
                            <td><?= e($r['nik_ayah'] ?? '-') ?></td>
                        </tr>

                        <tr>
                            <td>Agama</td>
                            <td><?= e($r['agama_ayah'] ?? '-') ?></td>
                        </tr>

                        <tr>
                            <td>Pendidikan</td>
                            <td><?= e($r['pendidikan_ayah'] ?? '-') ?></td>
                        </tr>

                        <tr>
                            <td>Pekerjaan</td>
                            <td><?= e($r['pekerjaan_ayah'] ?? '-') ?></td>
                        </tr>

                        <tr>
                            <td>No. Telepon</td>
                            <td><?= e($r['no_hp_ayah'] ?? '-') ?></td>
                        </tr>

                    </table>

                </div>


                <!-- IBU -->
                <div class="parent-box">

                    <div class="parent-title">
                        <span>IB</span>
                        Data Ibu
                    </div>

                    <table class="detail-table">

                        <tr>
                            <td>Nama</td>
                            <td><?= e($r['nama_ibu'] ?? '-') ?></td>
                        </tr>

                        <tr>
                            <td>Tempat, Tanggal Lahir</td>
                            <td>
                                <?= e($r['tempat_lahir_ibu'] ?? '-') ?>,
                                <?= !empty($r['tanggal_lahir_ibu'])
                                    ? date('d/m/Y', strtotime($r['tanggal_lahir_ibu']))
                                    : '-' ?>
                            </td>
                        </tr>

                        <tr>
                            <td>NIK</td>
                            <td><?= e($r['nik_ibu'] ?? '-') ?></td>
                        </tr>

                        <tr>
                            <td>Agama</td>
                            <td><?= e($r['agama_ibu'] ?? '-') ?></td>
                        </tr>

                        <tr>
                            <td>Pendidikan</td>
                            <td><?= e($r['pendidikan_ibu'] ?? '-') ?></td>
                        </tr>

                        <tr>
                            <td>Pekerjaan</td>
                            <td><?= e($r['pekerjaan_ibu'] ?? '-') ?></td>
                        </tr>

                        <tr>
                            <td>No. Telepon</td>
                            <td><?= e($r['no_hp_ibu'] ?? '-') ?></td>
                        </tr>

                    </table>

                </div>


                <!-- AKUN -->
                <div class="parent-box">

                    <div class="parent-title">
                        <span>AK</span>
                        Akun Pendaftar
                    </div>

                    <table class="detail-table">

                        <tr>
                            <td>Nama Akun</td>
                            <td><?= e($r['nama_user'] ?? '-') ?></td>
                        </tr>

                        <tr>
                            <td>Email</td>
                            <td><?= e($r['email_user'] ?? '-') ?></td>
                        </tr>

                    </table>

                </div>

            </section>


            <!-- DOKUMEN -->
            <section class="detail-card full">

                <div class="detail-card-head">
                    <div class="detail-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M6 3h8l4 4v14H6z"/>
                            <path d="M14 3v5h5"/>
                            <path d="M9 13h6"/>
                            <path d="M9 17h6"/>
                        </svg>
                    </div>

                    <div>
                        <h2>Dokumen Pendaftaran</h2>
                        <p class="detail-card-subtitle">
                            Periksa dan validasi dokumen yang diunggah
                        </p>
                    </div>
                </div>

                <?php if (!$dokumen): ?>

                    <div class="detail-empty">
                        Belum ada dokumen yang diunggah.
                    </div>

                <?php else: ?>

                    <?php foreach ($dokumen as $dok): ?>

                        <?php
                        $dokStatusColors = [
                            'menunggu' => '#6B7060',
                            'valid' => '#3F7E52',
                            'tidak_valid' => '#B23C3C',
                        ];

                        $dokLabels = [
                            'menunggu' => 'Menunggu',
                            'valid' => 'Valid',
                            'tidak_valid' => 'Tidak Valid',
                        ];

                        $dbc = $dokStatusColors[$dok['status']] ?? '#6B7060';
                        $dlbl = $dokLabels[$dok['status']] ?? 'Menunggu';
                        ?>

                        <div class="document-item">

                            <div class="document-top">

                                <div class="document-name">

                                    <?= e($dok['jenis']) ?>

                                    ·

                                    <a
                                        href="../file.php?f=<?= urlencode($dok['path_file']) ?>"
                                        target="_blank"
                                        rel="noopener"
                                    >
                                        <?= e($dok['nama_file']) ?>
                                    </a>

                                </div>

                                <span
                                    class="document-status"
                                    style="background:<?= $dbc ?>"
                                >
                                    <?= e($dlbl) ?>
                                </span>

                            </div>

                            <?php if (!empty($dok['alasan'])): ?>

                                <p class="document-reason">
                                    Alasan: <?= e($dok['alasan']) ?>
                                </p>

                            <?php endif; ?>


                            <form method="post" class="document-form">

                                <input
                                    type="hidden"
                                    name="aksi"
                                    value="dok_status"
                                >

                                <input
                                    type="hidden"
                                    name="dok_id"
                                    value="<?= (int)$dok['id'] ?>"
                                >

                                <select name="dok_status">

                                    <option
                                        value="menunggu"
                                        <?= $dok['status'] === 'menunggu' ? 'selected' : '' ?>
                                    >
                                        Menunggu
                                    </option>

                                    <option
                                        value="valid"
                                        <?= $dok['status'] === 'valid' ? 'selected' : '' ?>
                                    >
                                        Valid
                                    </option>

                                    <option
                                        value="tidak_valid"
                                        <?= $dok['status'] === 'tidak_valid' ? 'selected' : '' ?>
                                    >
                                        Tidak Valid
                                    </option>

                                </select>

                                <input
                                    name="alasan"
                                    placeholder="Alasan jika tidak valid"
                                    value="<?= e($dok['alasan'] ?? '') ?>"
                                >

                                <button
                                    class="detail-btn"
                                    type="submit"
                                >
                                    Simpan
                                </button>

                            </form>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </section>


            <!-- AKSI VERIFIKASI -->
            <section class="detail-card">

                <div class="detail-card-head">

                    <div class="detail-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 3l7 3v5c0 4.5-2.8 7.7-7 10-4.2-2.3-7-5.5-7-10V6z"/>
                            <path d="M8.5 12l2.2 2.2 4.8-5"/>
                        </svg>
                    </div>

                    <div>
                        <h2>Aksi Verifikasi</h2>
                        <p class="detail-card-subtitle">
                            Tentukan status pendaftaran peserta
                        </p>
                    </div>

                </div>


                <form method="post" class="detail-form">

                    <label>
                        Pilih Aksi

                        <select
                            name="aksi"
                            id="aksiVerifikasi"
                        >
                            <option value="">
                                -- Pilih --
                            </option>

                            <option value="setujui">
                                Verifikasi / Setujui
                            </option>

                            <option value="perbaikan">
                                Minta Perbaikan
                            </option>

                            <option value="diterima">
                                Diterima
                            </option>

                            <option value="tidak_diterima">
                                Tidak Diterima
                            </option>

                            <option value="tunggu">
                                Daftar Tunggu
                            </option>
                        </select>

                    </label>


                    <label>
                        Catatan / Alasan

                        <textarea
                            name="catatan"
                            rows="4"
                            placeholder="Tulis catatan jika diperlukan..."
                        ></textarea>

                    </label>


                    <div id="hasilBox" style="display:none">

                        <label>
                            Keterangan Hasil Seleksi
                            <span style="font-weight:600;color:var(--muted)">
                                (akan tampil ke orang tua)
                            </span>

                            <textarea
                                name="hasil_seleksi"
                                rows="3"
                                placeholder="Contoh: Selamat, peserta dinyatakan diterima sebagai siswa baru."
                            ></textarea>

                        </label>

                    </div>


                    <button
                        class="detail-btn"
                        type="submit"
                    >
                        Terapkan Aksi
                    </button>

                </form>

            </section>


            <!-- INFORMASI STATUS -->
            <section class="detail-card">

                <div class="detail-card-head">

                    <div class="detail-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 10v6"/>
                            <circle cx="12" cy="7" r=".8" fill="currentColor" stroke="none"/>
                        </svg>
                    </div>

                    <div>
                        <h2>Informasi Pendaftaran</h2>
                        <p class="detail-card-subtitle">
                            Ringkasan status pendaftaran
                        </p>
                    </div>

                </div>

                <table class="detail-table">

                    <tr>
                        <td>Nomor Pendaftaran</td>
                        <td><?= e($r['nomor_pendaftaran'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <td>Program</td>
                        <td><?= e($r['program'] ?? '-') ?></td>
                    </tr>

                    <tr>
                        <td>Status</td>
                        <td>
                            <strong>
                                <?= e(status_label($r['status'] ?? 'draft')) ?>
                            </strong>
                        </td>
                    </tr>

                    <?php if (!empty($r['created_at'])): ?>

                        <tr>
                            <td>Tanggal Dibuat</td>
                            <td>
                                <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?>
                            </td>
                        </tr>

                    <?php endif; ?>

                    <?php if (!empty($r['submitted_at'])): ?>

                        <tr>
                            <td>Tanggal Dikirim</td>
                            <td>
                                <?= date('d/m/Y H:i', strtotime($r['submitted_at'])) ?>
                            </td>
                        </tr>

                    <?php endif; ?>

                    <?php if (!empty($r['catatan_admin'])): ?>

                        <tr>
                            <td>Catatan Admin</td>
                            <td>
                                <?= nl2br(e($r['catatan_admin'])) ?>
                            </td>
                        </tr>

                    <?php endif; ?>

                    <?php if (!empty($r['hasil_seleksi'])): ?>

                        <tr>
                            <td>Hasil Seleksi</td>
                            <td>
                                <?= nl2br(e($r['hasil_seleksi'])) ?>
                            </td>
                        </tr>

                    <?php endif; ?>

                </table>

            </section>


            <!-- RIWAYAT -->
            <?php if ($riwayat): ?>

                <section class="detail-card full">

                    <div class="detail-card-head">

                        <div class="detail-card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2"/>
                            </svg>
                        </div>

                        <div>
                            <h2>Riwayat Verifikasi</h2>
                            <p class="detail-card-subtitle">
                                Aktivitas admin pada pendaftaran ini
                            </p>
                        </div>

                    </div>


                    <?php foreach ($riwayat as $log): ?>

                        <div class="history-item">

                            <div class="history-title">

                                <?= e($log['admin_nama']) ?>

                                ·

                                <em><?= e($log['aksi']) ?></em>

                            </div>

                            <?php if (!empty($log['catatan'])): ?>

                                <p class="history-note">
                                    <?= nl2br(e($log['catatan'])) ?>
                                </p>

                            <?php endif; ?>

                            <div class="history-date">

                                <?= !empty($log['created_at'])
                                    ? date('d/m/Y H:i', strtotime($log['created_at']))
                                    : '-' ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </section>

            <?php endif; ?>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const aksi = document.getElementById('aksiVerifikasi');
    const hasilBox = document.getElementById('hasilBox');

    if (!aksi || !hasilBox) {
        return;
    }

    function updateHasilBox() {
        hasilBox.style.display = aksi.value === 'diterima'
            ? 'block'
            : 'none';
    }

    aksi.addEventListener('change', updateHasilBox);

    updateHasilBox();
});
</script>


<?php require __DIR__ . '/../includes/footer.php'; ?>
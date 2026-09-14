<?php
declare(strict_types=1);

$cssBase = '../';

require __DIR__ . '/../includes/auth.php';
require_admin();

$search = trim($_GET['q'] ?? '');
$filterStatus = trim($_GET['status'] ?? '');
$filterTgl = trim($_GET['tgl'] ?? '');

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;


/*
|--------------------------------------------------------------------------
| Filter
|--------------------------------------------------------------------------
*/
$where = '1=1';
$params = [];

if ($search !== '') {
    $where .= '
        AND (
            cs.nama_lengkap LIKE ?
            OR cs.nama_panggilan LIKE ?
            OR p.nomor_pendaftaran LIKE ?
            OR u.nama LIKE ?
            OR u.email LIKE ?
        )
    ';

    $keyword = "%{$search}%";

    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
}

if ($filterStatus !== '') {
    $where .= ' AND p.status = ?';
    $params[] = $filterStatus;
}

if ($filterTgl !== '') {
    $where .= ' AND DATE(p.tanggal_daftar) = ?';
    $params[] = $filterTgl;
}


/*
|--------------------------------------------------------------------------
| Hitung total
|--------------------------------------------------------------------------
*/
$countStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM pendaftaran p
    LEFT JOIN calon_siswa cs
        ON cs.pendaftaran_id = p.id
    LEFT JOIN users u
        ON u.id = p.user_id
    WHERE {$where}
");

$countStmt->execute($params);

$total = (int)$countStmt->fetchColumn();

$totalPages = max(
    1,
    (int)ceil($total / $perPage)
);


/*
|--------------------------------------------------------------------------
| Data pendaftar
|--------------------------------------------------------------------------
*/
$rows = $pdo->prepare("
    SELECT
        p.*,
        cs.nama_lengkap,
        cs.nama_panggilan,
        u.nama AS nama_user,
        u.email AS email_user
    FROM pendaftaran p
    LEFT JOIN calon_siswa cs
        ON cs.pendaftaran_id = p.id
    LEFT JOIN users u
        ON u.id = p.user_id
    WHERE {$where}
    ORDER BY p.updated_at DESC
    LIMIT {$perPage}
    OFFSET {$offset}
");

$rows->execute($params);

$pendaftar = $rows->fetchAll();


/*
|--------------------------------------------------------------------------
| Status yang tersedia
|--------------------------------------------------------------------------
*/
$statuses = [
    'draft',
    'menunggu_verifikasi',
    'perlu_perbaikan',
    'terverifikasi',
    'diterima',
    'tidak_diterima',
    'daftar_tunggu',
    'daftar_ulang',
    'siswa_terdaftar'
];


/*
|--------------------------------------------------------------------------
| Warna status
|--------------------------------------------------------------------------
*/
$statusClasses = [
    'draft' => 'draft',
    'menunggu_verifikasi' => 'menunggu',
    'perlu_perbaikan' => 'perbaikan',
    'terverifikasi' => 'terverifikasi',
    'diterima' => 'diterima',
    'tidak_diterima' => 'ditolak',
    'daftar_tunggu' => 'tunggu',
    'daftar_ulang' => 'daftar-ulang',
    'siswa_terdaftar' => 'terdaftar'
];


$pageTitle = 'Data Pendaftar - Admin';
$activePage = 'admin';

require __DIR__ . '/../includes/header.php';
?>

<style>
    .ppdb-admin-list {
        --paper: #FBF7EF;
        --paper-line: #E7DFCE;
        --chalk: #2F4538;
        --leaf: #3E5A48;
        --ink: #262A20;
        --muted: #6B7060;

        --green: #3F7E52;
        --soft-green: #E7F1E4;

        --amber: #C6820E;
        --soft-amber: #FBF0DC;

        --berry: #B23C3C;
        --soft-berry: #F8E7E4;

        --blue: #35618A;
        --soft-blue: #E5EEF5;
    }

    .ppdb-admin-list {
        min-height: calc(100vh - 100px);
        background: var(--paper);
        padding: 28px 0 60px;
    }

    .ppdb-admin-list .container {
        max-width: 1200px;
    }


    /* =========================================================
       HERO
       ========================================================= */

    .admin-list-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .admin-list-eyebrow {
        display: block;
        margin-bottom: 6px;
        color: var(--green);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .admin-list-hero h1 {
        margin: 0;
        color: var(--chalk);
        font-family: 'Fraunces', Georgia, serif;
        font-size: clamp(30px, 4vw, 40px);
        line-height: 1.1;
    }

    .admin-list-hero p {
        margin: 8px 0 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.5;
    }

    .admin-list-total {
        padding: 12px 16px;
        border: 1px solid var(--paper-line);
        border-radius: 13px;
        background: #fff;
        color: var(--chalk);
        font-size: 13px;
        font-weight: 800;
        white-space: nowrap;
    }


    /* =========================================================
       BACK
       ========================================================= */

    .admin-list-back {
        margin-bottom: 17px;
    }

    .admin-list-back a {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 13px;
        border: 1px solid var(--paper-line);
        border-radius: 11px;
        background: #fff;
        color: var(--chalk);
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        transition: .2s ease;
    }

    .admin-list-back a:hover {
        border-color: var(--chalk);
        transform: translateY(-1px);
    }


    /* =========================================================
       FILTER
       ========================================================= */

    .admin-filter {
        display: grid;
        grid-template-columns: minmax(220px, 1.4fr) minmax(180px, .8fr) 170px auto auto;
        gap: 9px;
        align-items: center;
        padding: 15px;
        margin-bottom: 18px;

        border: 1px solid var(--paper-line);
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(47,69,56,.04);
    }

    .admin-filter input,
    .admin-filter select {
        width: 100%;
        box-sizing: border-box;
        min-height: 42px;
        padding: 9px 12px;

        border: 1px solid var(--paper-line);
        border-radius: 11px;
        background: var(--paper);

        color: var(--ink);
        font-family: inherit;
        font-size: 13px;
        outline: none;
    }

    .admin-filter input:focus,
    .admin-filter select:focus {
        border-color: var(--leaf);
        box-shadow: 0 0 0 3px rgba(62,90,72,.08);
    }

    .admin-filter .filter-btn {
        min-height: 42px;
        padding: 9px 17px;
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

    .admin-filter .filter-btn:hover {
        background: var(--leaf);
    }

    .admin-filter .reset-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 42px;
        padding: 9px 15px;

        border: 1px solid var(--paper-line);
        border-radius: 11px;

        background: #fff;
        color: var(--chalk);

        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }

    .admin-filter .reset-btn:hover {
        border-color: var(--chalk);
    }


    /* =========================================================
       TABLE CARD
       ========================================================= */

    .admin-table-card {
        overflow: hidden;

        border: 1px solid var(--paper-line);
        border-radius: 18px;
        background: #fff;

        box-shadow: 0 5px 18px rgba(47,69,56,.04);
    }

    .admin-table-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;

        padding: 18px 20px;
        border-bottom: 1px solid var(--paper-line);
    }

    .admin-table-head h2 {
        margin: 0;
        color: var(--chalk);
        font-family: 'Fraunces', Georgia, serif;
        font-size: 20px;
    }

    .admin-table-head span {
        color: var(--muted);
        font-size: 12px;
    }


    /* =========================================================
       TABLE
       ========================================================= */

    .admin-table-wrap {
        overflow-x: auto;
    }

    .admin-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .admin-table th {
        padding: 12px 14px;

        background: var(--paper);

        border-bottom: 1px solid var(--paper-line);

        color: var(--muted);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .07em;
        text-transform: uppercase;
        text-align: left;
        white-space: nowrap;
    }

    .admin-table td {
        padding: 14px;

        border-bottom: 1px solid #F0EBDD;

        color: var(--ink);
        font-size: 13px;
        vertical-align: middle;
    }

    .admin-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .admin-table tbody tr {
        transition: .15s ease;
    }

    .admin-table tbody tr:hover {
        background: #FCFAF5;
    }

    .row-number {
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
    }

    .registration-number {
        color: var(--chalk);
        font-weight: 900;
        white-space: nowrap;
    }

    .student-name {
        color: var(--ink);
        font-weight: 800;
    }

    .student-callname {
        display: block;
        margin-top: 3px;
        color: var(--muted);
        font-size: 11px;
    }

    .parent-name {
        color: var(--ink);
        font-weight: 700;
    }

    .program-name {
        color: var(--leaf);
        font-weight: 800;
    }

    .date-text {
        color: var(--muted);
        white-space: nowrap;
        font-size: 12px;
    }


    /* =========================================================
       STATUS BADGE
       ========================================================= */

    .admin-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        padding: 6px 10px;

        border-radius: 999px;

        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .admin-status::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-draft {
        color: #6B7060;
        background: #F1F0EA;
    }

    .status-menunggu {
        color: var(--amber);
        background: var(--soft-amber);
    }

    .status-perbaikan {
        color: var(--berry);
        background: var(--soft-berry);
    }

    .status-terverifikasi {
        color: var(--blue);
        background: var(--soft-blue);
    }

    .status-diterima {
        color: var(--green);
        background: var(--soft-green);
    }

    .status-ditolak {
        color: var(--berry);
        background: var(--soft-berry);
    }

    .status-tunggu {
        color: var(--blue);
        background: var(--soft-blue);
    }

    .status-daftar-ulang {
        color: var(--amber);
        background: var(--soft-amber);
    }

    .status-terdaftar {
        color: var(--green);
        background: var(--soft-green);
    }


    /* =========================================================
       DETAIL BUTTON
       ========================================================= */

    .detail-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 8px 13px;

        border: 1px solid var(--paper-line);
        border-radius: 10px;

        background: #fff;
        color: var(--chalk);

        font-size: 11px;
        font-weight: 900;
        text-decoration: none;

        transition: .2s ease;
    }

    .detail-link:hover {
        background: var(--chalk);
        border-color: var(--chalk);
        color: #fff;
        transform: translateY(-1px);
    }


    /* =========================================================
       EMPTY
       ========================================================= */

    .admin-empty {
        padding: 55px 20px;
        text-align: center;
    }

    .admin-empty-icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 12px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 14px;
        background: var(--soft-green);
        color: var(--leaf);
    }

    .admin-empty-icon svg {
        width: 23px;
        height: 23px;
    }

    .admin-empty strong {
        display: block;
        color: var(--chalk);
        font-family: 'Fraunces', Georgia, serif;
        font-size: 18px;
    }

    .admin-empty p {
        margin: 5px 0 0;
        color: var(--muted);
        font-size: 12px;
    }


    /* =========================================================
       PAGINATION
       ========================================================= */

    .admin-pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex-wrap: wrap;

        margin-top: 18px;
    }

    .admin-pagination a {
        min-width: 36px;
        height: 36px;
        box-sizing: border-box;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 0 10px;

        border: 1px solid var(--paper-line);
        border-radius: 10px;

        background: #fff;
        color: var(--chalk);

        font-size: 12px;
        font-weight: 900;
        text-decoration: none;

        transition: .2s ease;
    }

    .admin-pagination a:hover {
        border-color: var(--chalk);
    }

    .admin-pagination a.active {
        border-color: var(--chalk);
        background: var(--chalk);
        color: #fff;
    }


    /* =========================================================
       RESPONSIVE
       ========================================================= */

    @media (max-width: 900px) {
        .admin-filter {
            grid-template-columns: 1fr 1fr;
        }

        .admin-filter input[name="q"] {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 600px) {

        .ppdb-admin-list {
            padding-top: 20px;
        }

        .admin-list-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .admin-list-total {
            width: 100%;
            box-sizing: border-box;
        }

        .admin-filter {
            grid-template-columns: 1fr;
            padding: 12px;
        }

        .admin-filter input[name="q"] {
            grid-column: auto;
        }

        .admin-filter .filter-btn,
        .admin-filter .reset-btn {
            width: 100%;
        }

        .admin-table-head {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>


<div class="ppdb-admin-list">

    <div class="container">

        <!-- HERO -->
        <section class="admin-list-hero">

            <div>
                <span class="admin-list-eyebrow">
                    Dashboard Admin
                </span>

                <h1>Data Pendaftar</h1>

                <p>
                    Kelola dan periksa seluruh data pendaftaran PPDB.
                </p>
            </div>

            <div class="admin-list-total">
                <?= $total ?> pendaftar
            </div>

        </section>


        <!-- BACK -->
        <div class="admin-list-back">
            <a href="index.php">
                <span>&larr;</span>
                Kembali ke Dashboard
            </a>
        </div>


        <!-- FILTER -->
        <form method="get" class="admin-filter">

            <input
                type="search"
                name="q"
                value="<?= e($search) ?>"
                placeholder="Cari nama, nomor pendaftaran, atau email..."
            >

            <select name="status">

                <option value="">
                    Semua Status
                </option>

                <?php foreach ($statuses as $s): ?>

                    <option
                        value="<?= e($s) ?>"
                        <?= $filterStatus === $s ? 'selected' : '' ?>
                    >
                        <?= e(status_label($s)) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <input
                type="date"
                name="tgl"
                value="<?= e($filterTgl) ?>"
            >

            <button
                class="filter-btn"
                type="submit"
            >
                Terapkan
            </button>

            <a
                class="reset-btn"
                href="pendaftar.php"
            >
                Reset
            </a>

        </form>


        <!-- TABLE -->
        <section class="admin-table-card">

            <div class="admin-table-head">

                <div>
                    <h2>Daftar Pendaftar</h2>
                </div>

                <span>
                    Menampilkan <?= count($pendaftar) ?> dari <?= $total ?> data
                </span>

            </div>


            <div class="admin-table-wrap">

                <table class="admin-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>No. Pendaftaran</th>
                            <th>Calon Siswa</th>
                            <th>Orang Tua</th>
                            <th>Program</th>
                            <th>Tanggal Daftar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($pendaftar as $i => $r): ?>

                        <?php
                        $badgeClass = $statusClasses[$r['status'] ?? 'draft']
                            ?? 'draft';
                        ?>

                        <tr>

                            <td>
                                <span class="row-number">
                                    <?= $offset + $i + 1 ?>
                                </span>
                            </td>

                            <td>
                                <span class="registration-number">
                                    <?= e($r['nomor_pendaftaran'] ?? '-') ?>
                                </span>
                            </td>

                            <td>

                                <span class="student-name">
                                    <?= e($r['nama_lengkap'] ?? '-') ?>
                                </span>

                                <?php if (!empty($r['nama_panggilan'])): ?>

                                    <span class="student-callname">
                                        Panggilan:
                                        <?= e($r['nama_panggilan']) ?>
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>
                                <span class="parent-name">
                                    <?= e($r['nama_user'] ?? '-') ?>
                                </span>
                            </td>

                            <td>
                                <span class="program-name">
                                    <?= e($r['program'] ?? '-') ?>
                                </span>
                            </td>

                            <td>
                                <span class="date-text">
                                    <?= !empty($r['tanggal_daftar'])
                                        ? date(
                                            'd/m/Y',
                                            strtotime($r['tanggal_daftar'])
                                        )
                                        : '-' ?>
                                </span>
                            </td>

                            <td>

                                <span
                                    class="admin-status status-<?= e($badgeClass) ?>"
                                >
                                    <?= e(status_label($r['status'] ?? 'draft')) ?>
                                </span>

                            </td>

                            <td>

                                <a
                                    class="detail-link"
                                    href="detail.php?id=<?= (int)$r['id'] ?>"
                                >
                                    Detail
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>


                    <?php if (!$pendaftar): ?>

                        <tr>

                            <td colspan="8">

                                <div class="admin-empty">

                                    <div class="admin-empty-icon">

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <circle
                                                cx="11"
                                                cy="11"
                                                r="7"
                                            />

                                            <path d="m20 20-4-4"/>
                                        </svg>

                                    </div>

                                    <strong>
                                        Data tidak ditemukan
                                    </strong>

                                    <p>
                                        Coba ubah kata pencarian atau filter
                                        yang digunakan.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>


        <!-- PAGINATION -->
        <?php if ($totalPages > 1): ?>

            <div class="admin-pagination">

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                    <?php
                    $query = http_build_query(
                        array_merge(
                            $_GET,
                            ['page' => $i]
                        )
                    );
                    ?>

                    <a
                        href="pendaftar.php?<?= e($query) ?>"
                        class="<?= $i === $page ? 'active' : '' ?>"
                    >
                        <?= $i ?>
                    </a>

                <?php endfor; ?>

            </div>

        <?php endif; ?>

    </div>

</div>


<?php require __DIR__ . '/../includes/footer.php'; ?>
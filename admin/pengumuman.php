<?php
declare(strict_types=1);

$cssBase = '../';

require __DIR__ . '/../includes/auth.php';
require_admin();

$adminUser = current_user();
$errors = [];


/*
|--------------------------------------------------------------------------
| Proses pengumuman
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $act = $_POST['act'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | Tambah
    |--------------------------------------------------------------------------
    */
    if ($act === 'tambah') {

        $judul = trim($_POST['judul'] ?? '');
        $isi = trim($_POST['isi'] ?? '');
        $statusInput = $_POST['status'] ?? 'publish';

        $status = in_array(
            $statusInput,
            ['draft', 'publish'],
            true
        )
            ? $statusInput
            : 'publish';


        if ($judul === '') {
            $errors[] = 'Judul wajib diisi.';
        }

        if ($isi === '') {
            $errors[] = 'Isi pengumuman wajib diisi.';
        }


        if (!$errors) {

            $stmt = $pdo->prepare("
                INSERT INTO pengumuman
                    (admin_id, judul, isi, status)
                VALUES
                    (?, ?, ?, ?)
            ");

            $stmt->execute([
                $adminUser['id'],
                $judul,
                $isi,
                $status
            ]);

            flash(
                'success',
                'Pengumuman berhasil ditambahkan.'
            );

            redirect('pengumuman.php');
        }


    /*
    |--------------------------------------------------------------------------
    | Hapus
    |--------------------------------------------------------------------------
    */
    } elseif ($act === 'hapus') {

        $hapusId = (int)($_POST['hapus_id'] ?? 0);

        if ($hapusId > 0) {

            $stmt = $pdo->prepare("
                DELETE FROM pengumuman
                WHERE id = ?
            ");

            $stmt->execute([$hapusId]);
        }

        flash(
            'success',
            'Pengumuman berhasil dihapus.'
        );

        redirect('pengumuman.php');


    /*
    |--------------------------------------------------------------------------
    | Toggle Publish / Draft
    |--------------------------------------------------------------------------
    */
    } elseif ($act === 'toggle') {

        $togId = (int)($_POST['tog_id'] ?? 0);
        $togStat = $_POST['tog_status'] ?? '';

        $newStatus = ($togStat === 'publish')
            ? 'draft'
            : 'publish';


        if ($togId > 0) {

            $stmt = $pdo->prepare("
                UPDATE pengumuman
                SET status = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $newStatus,
                $togId
            ]);
        }

        flash(
            'success',
            'Status pengumuman diperbarui.'
        );

        redirect('pengumuman.php');
    }
}


/*
|--------------------------------------------------------------------------
| Ambil pengumuman
|--------------------------------------------------------------------------
*/
$list = $pdo->query("
    SELECT
        pg.*,
        u.nama AS admin_nama
    FROM pengumuman pg
    JOIN users u
        ON u.id = pg.admin_id
    ORDER BY pg.created_at DESC
")->fetchAll();


$pageTitle = 'Pengumuman - Admin';
$activePage = 'admin';

require __DIR__ . '/../includes/header.php';
?>

<style>
    .ppdb-announcement-page {
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
    }


    .ppdb-announcement-page {
        min-height: calc(100vh - 100px);
        background: var(--paper);
        padding: 28px 0 60px;
    }

    .ppdb-announcement-page .container {
        max-width: 1180px;
    }


    /* =========================================================
       HERO
       ========================================================= */

    .announcement-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;

        margin-bottom: 20px;
    }

    .announcement-eyebrow {
        display: block;

        margin-bottom: 6px;

        color: var(--green);

        font-size: 11px;
        font-weight: 900;

        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .announcement-hero h1 {
        margin: 0;

        color: var(--chalk);

        font-family: 'Fraunces', Georgia, serif;
        font-size: clamp(30px, 4vw, 40px);
        line-height: 1.1;
    }

    .announcement-hero p {
        margin: 8px 0 0;

        color: var(--muted);

        font-size: 14px;
        line-height: 1.6;
    }


    .announcement-count {
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
       ALERT
       ========================================================= */

    .announcement-alert {
        padding: 13px 16px;

        margin-bottom: 17px;

        border-radius: 13px;

        font-size: 13px;
    }

    .announcement-alert-success {
        background: var(--soft-green);
        border: 1px solid #CFE1CA;
        color: var(--green);
    }

    .announcement-alert-error {
        background: var(--soft-berry);
        border: 1px solid #EBCAC5;
        color: var(--berry);
    }

    .announcement-alert ul {
        margin: 0;
        padding-left: 18px;
    }


    /* =========================================================
       BACK
       ========================================================= */

    .announcement-back {
        margin-bottom: 18px;
    }

    .announcement-back a {
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

    .announcement-back a:hover {
        border-color: var(--chalk);
        transform: translateY(-1px);
    }


    /* =========================================================
       MAIN GRID
       ========================================================= */

    .announcement-grid {
        display: grid;

        grid-template-columns: 380px minmax(0, 1fr);

        gap: 18px;

        align-items: start;
    }


    /* =========================================================
       CARD
       ========================================================= */

    .announcement-card {
        border: 1px solid var(--paper-line);
        border-radius: 18px;

        background: #fff;

        box-shadow: 0 5px 18px rgba(47,69,56,.04);
    }

    .announcement-card-head {
        display: flex;
        align-items: center;
        gap: 11px;

        padding: 18px 20px;

        border-bottom: 1px solid var(--paper-line);
    }

    .announcement-card-icon {
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

    .announcement-card-icon svg {
        width: 19px;
        height: 19px;
    }

    .announcement-card-head h2 {
        margin: 0;

        color: var(--chalk);

        font-family: 'Fraunces', Georgia, serif;
        font-size: 20px;
    }

    .announcement-card-head p {
        margin: 3px 0 0;

        color: var(--muted);

        font-size: 11px;
    }


    /* =========================================================
       FORM
       ========================================================= */

    .announcement-form {
        padding: 20px;
    }

    .announcement-form-group {
        display: grid;
        gap: 6px;

        margin-bottom: 15px;
    }

    .announcement-form-group label {
        color: var(--ink);

        font-size: 12px;
        font-weight: 900;
    }

    .announcement-form-group input,
    .announcement-form-group textarea,
    .announcement-form-group select {
        width: 100%;
        box-sizing: border-box;

        padding: 10px 12px;

        border: 1px solid var(--paper-line);
        border-radius: 11px;

        background: var(--paper);

        color: var(--ink);

        font-family: inherit;
        font-size: 13px;

        outline: none;

        transition: .2s ease;
    }

    .announcement-form-group textarea {
        min-height: 145px;

        resize: vertical;

        line-height: 1.6;
    }

    .announcement-form-group input:focus,
    .announcement-form-group textarea:focus,
    .announcement-form-group select:focus {
        border-color: var(--leaf);

        box-shadow:
            0 0 0 3px rgba(62,90,72,.08);
    }


    .announcement-submit {
        width: 100%;

        min-height: 42px;

        padding: 10px 16px;

        border: 0;
        border-radius: 11px;

        background: var(--chalk);
        color: #fff;

        font-family: inherit;
        font-size: 12px;
        font-weight: 900;

        cursor: pointer;

        transition: .2s ease;
    }

    .announcement-submit:hover {
        background: var(--leaf);
        transform: translateY(-1px);
    }


    /* =========================================================
       LIST
       ========================================================= */

    .announcement-list {
        display: grid;
        gap: 11px;

        padding: 18px;
    }

    .announcement-item {
        padding: 17px;

        border: 1px solid var(--paper-line);
        border-radius: 15px;

        background: #fff;

        transition: .2s ease;
    }

    .announcement-item:hover {
        border-color: #D8CEB9;
        box-shadow: 0 5px 16px rgba(47,69,56,.04);
    }

    .announcement-item-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;

        gap: 15px;
    }

    .announcement-item-title {
        min-width: 0;
    }

    .announcement-item-title h3 {
        margin: 0;

        color: var(--chalk);

        font-family: 'Fraunces', Georgia, serif;
        font-size: 18px;
        line-height: 1.3;
    }

    .announcement-preview {
        margin: 6px 0 0;

        color: var(--muted);

        font-size: 12px;
        line-height: 1.6;
    }


    /* =========================================================
       STATUS
       ========================================================= */

    .announcement-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        padding: 6px 10px;

        border-radius: 999px;

        font-size: 10px;
        font-weight: 900;

        white-space: nowrap;
    }

    .announcement-status::before {
        content: "";

        width: 6px;
        height: 6px;

        border-radius: 50%;

        background: currentColor;
    }

    .announcement-status-publish {
        background: var(--soft-green);
        color: var(--green);
    }

    .announcement-status-draft {
        background: #F1F0EA;
        color: var(--muted);
    }


    /* =========================================================
       META
       ========================================================= */

    .announcement-meta {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;

        margin-top: 11px;

        color: var(--muted);

        font-size: 11px;
    }

    .announcement-meta-dot {
        width: 3px;
        height: 3px;

        border-radius: 50%;

        background: var(--paper-line);
    }


    /* =========================================================
       ACTIONS
       ========================================================= */

    .announcement-actions {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;

        margin-top: 13px;
        padding-top: 12px;

        border-top: 1px solid #F0EBDD;
    }

    .announcement-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 34px;

        padding: 7px 11px;

        border: 1px solid var(--paper-line);
        border-radius: 9px;

        background: #fff;
        color: var(--chalk);

        font-family: inherit;
        font-size: 11px;
        font-weight: 800;

        cursor: pointer;

        transition: .2s ease;
    }

    .announcement-action:hover {
        border-color: var(--chalk);
        background: var(--chalk);
        color: #fff;
    }

    .announcement-action-delete {
        color: var(--berry);
        border-color: #EBCAC5;
    }

    .announcement-action-delete:hover {
        background: var(--berry);
        border-color: var(--berry);
        color: #fff;
    }


    /* =========================================================
       EMPTY
       ========================================================= */

    .announcement-empty {
        padding: 55px 20px;

        text-align: center;
    }

    .announcement-empty-icon {
        width: 50px;
        height: 50px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 0 auto 13px;

        border-radius: 14px;

        background: var(--soft-green);
        color: var(--leaf);
    }

    .announcement-empty-icon svg {
        width: 24px;
        height: 24px;
    }

    .announcement-empty strong {
        display: block;

        color: var(--chalk);

        font-family: 'Fraunces', Georgia, serif;
        font-size: 19px;
    }

    .announcement-empty p {
        margin: 5px 0 0;

        color: var(--muted);
        font-size: 12px;
    }


    /* =========================================================
       RESPONSIVE
       ========================================================= */

    @media (max-width: 900px) {

        .announcement-grid {
            grid-template-columns: 1fr;
        }
    }


    @media (max-width: 600px) {

        .ppdb-announcement-page {
            padding-top: 20px;
        }

        .announcement-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .announcement-count {
            width: 100%;
            box-sizing: border-box;
        }

        .announcement-card-head {
            padding: 16px;
        }

        .announcement-form {
            padding: 16px;
        }

        .announcement-list {
            padding: 12px;
        }

        .announcement-item {
            padding: 14px;
        }

        .announcement-item-top {
            align-items: flex-start;
            flex-direction: column;
        }

        .announcement-status {
            align-self: flex-start;
        }
    }
</style>


<div class="ppdb-announcement-page">

    <div class="container">

        <!-- HERO -->
        <section class="announcement-hero">

            <div>

                <span class="announcement-eyebrow">
                    Dashboard Admin
                </span>

                <h1>
                    Kelola Pengumuman
                </h1>

                <p>
                    Buat dan kelola informasi yang akan ditampilkan kepada pendaftar.
                </p>

            </div>

            <div class="announcement-count">
                <?= count($list) ?> pengumuman
            </div>

        </section>


        <!-- ALERT SUCCESS -->
        <?php if ($m = flash('success')): ?>

            <div class="announcement-alert announcement-alert-success">
                <?= e($m) ?>
            </div>

        <?php endif; ?>


        <!-- ALERT ERROR -->
        <?php if ($errors): ?>

            <div class="announcement-alert announcement-alert-error">

                <ul>

                    <?php foreach ($errors as $er): ?>

                        <li><?= e($er) ?></li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>


        <!-- BACK -->
        <div class="announcement-back">

            <a href="index.php">
                <span>&larr;</span>
                Kembali ke Dashboard
            </a>

        </div>


        <!-- MAIN -->
        <div class="announcement-grid">


            <!-- =================================================
                 FORM TAMBAH
                 ================================================= -->

            <section class="announcement-card">

                <div class="announcement-card-head">

                    <div class="announcement-card-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M12 5v14"/>
                            <path d="M5 12h14"/>
                        </svg>

                    </div>

                    <div>

                        <h2>
                            Tambah Pengumuman
                        </h2>

                        <p>
                            Buat informasi baru untuk pendaftar.
                        </p>

                    </div>

                </div>


                <form
                    class="announcement-form"
                    method="post"
                >

                    <input
                        type="hidden"
                        name="act"
                        value="tambah"
                    >


                    <div class="announcement-form-group">

                        <label for="judul">
                            Judul Pengumuman
                        </label>

                        <input
                            id="judul"
                            type="text"
                            name="judul"
                            required
                            maxlength="200"
                            value="<?= e($_POST['judul'] ?? '') ?>"
                            placeholder="Contoh: Jadwal Daftar Ulang"
                        >

                    </div>


                    <div class="announcement-form-group">

                        <label for="isi">
                            Isi Pengumuman
                        </label>

                        <textarea
                            id="isi"
                            name="isi"
                            rows="7"
                            required
                            placeholder="Tulis isi pengumuman di sini..."
                        ><?= e($_POST['isi'] ?? '') ?></textarea>

                    </div>


                    <div class="announcement-form-group">

                        <label for="status">
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                        >

                            <option value="publish">
                                Publish
                            </option>

                            <option
                                value="draft"
                                <?= ($_POST['status'] ?? '') === 'draft'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Draft
                            </option>

                        </select>

                    </div>


                    <button
                        class="announcement-submit"
                        type="submit"
                    >
                        Simpan Pengumuman
                    </button>

                </form>

            </section>


            <!-- =================================================
                 DAFTAR
                 ================================================= -->

            <section class="announcement-card">

                <div class="announcement-card-head">

                    <div class="announcement-card-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M6 4h12"/>
                            <path d="M6 8h12"/>
                            <path d="M6 12h8"/>
                            <path d="M6 16h10"/>
                            <path d="M6 20h7"/>
                        </svg>

                    </div>

                    <div>

                        <h2>
                            Daftar Pengumuman
                        </h2>

                        <p>
                            Pengumuman terbaru ditampilkan paling atas.
                        </p>

                    </div>

                </div>


                <?php if (!$list): ?>

                    <div class="announcement-empty">

                        <div class="announcement-empty-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M6 4h12"/>
                                <path d="M6 8h12"/>
                                <path d="M6 12h8"/>
                                <path d="M6 16h10"/>
                                <path d="M6 20h7"/>
                            </svg>

                        </div>

                        <strong>
                            Belum ada pengumuman
                        </strong>

                        <p>
                            Buat pengumuman pertama untuk ditampilkan kepada pendaftar.
                        </p>

                    </div>

                <?php else: ?>

                    <div class="announcement-list">

                        <?php foreach ($list as $pg): ?>

                            <?php
                            $isPublish = $pg['status'] === 'publish';

                            $preview = mb_substr(
                                $pg['isi'],
                                0,
                                120
                            );

                            if (mb_strlen($pg['isi']) > 120) {
                                $preview .= '...';
                            }
                            ?>

                            <article class="announcement-item">

                                <div class="announcement-item-top">

                                    <div class="announcement-item-title">

                                        <h3>
                                            <?= e($pg['judul']) ?>
                                        </h3>

                                        <p class="announcement-preview">
                                            <?= e($preview) ?>
                                        </p>

                                    </div>


                                    <span
                                        class="announcement-status <?= $isPublish
                                            ? 'announcement-status-publish'
                                            : 'announcement-status-draft' ?>"
                                    >
                                        <?= $isPublish
                                            ? 'Publish'
                                            : 'Draft' ?>
                                    </span>

                                </div>


                                <div class="announcement-meta">

                                    <span>
                                        <?= !empty($pg['created_at'])
                                            ? date(
                                                'd/m/Y',
                                                strtotime($pg['created_at'])
                                            )
                                            : '-' ?>
                                    </span>

                                    <span class="announcement-meta-dot"></span>

                                    <span>
                                        <?= e($pg['admin_nama']) ?>
                                    </span>

                                </div>


                                <div class="announcement-actions">

                                    <!-- TOGGLE -->
                                    <form method="post">

                                        <input
                                            type="hidden"
                                            name="act"
                                            value="toggle"
                                        >

                                        <input
                                            type="hidden"
                                            name="tog_id"
                                            value="<?= (int)$pg['id'] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="tog_status"
                                            value="<?= e($pg['status']) ?>"
                                        >

                                        <button
                                            class="announcement-action"
                                            type="submit"
                                        >
                                            <?= $isPublish
                                                ? 'Jadikan Draft'
                                                : 'Publish' ?>
                                        </button>

                                    </form>


                                    <!-- DELETE -->
                                    <form
                                        method="post"
                                        onsubmit="return confirm('Hapus pengumuman ini?')"
                                    >

                                        <input
                                            type="hidden"
                                            name="act"
                                            value="hapus"
                                        >

                                        <input
                                            type="hidden"
                                            name="hapus_id"
                                            value="<?= (int)$pg['id'] ?>"
                                        >

                                        <button
                                            class="announcement-action announcement-action-delete"
                                            type="submit"
                                        >
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </section>

        </div>

    </div>

</div>


<?php require __DIR__ . '/../includes/footer.php'; ?>
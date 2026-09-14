<?php
declare(strict_types=1);

$cssBase = '../';

require __DIR__ . '/../includes/auth.php';
require_admin();

$user = current_user();
$errors = [];


/*
|--------------------------------------------------------------------------
| Proses pembayaran
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $aksi = $_POST['aksi'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | Konfirmasi
    |--------------------------------------------------------------------------
    */
    if ($aksi === 'konfirmasi') {

        $id = (int)($_POST['id'] ?? 0);
        $catatan = trim($_POST['catatan'] ?? '');

        if ($id <= 0) {
            $errors[] = 'Data pembayaran tidak valid.';
        } else {

            $stmt = $pdo->prepare("
                UPDATE pembayaran
                SET status = 'dikonfirmasi',
                    catatan_admin = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $catatan !== '' ? $catatan : null,
                $id
            ]);

            flash('success', 'Pembayaran berhasil dikonfirmasi.');
            redirect('pembayaran.php');
        }

    /*
    |--------------------------------------------------------------------------
    | Tolak
    |--------------------------------------------------------------------------
    */
    } elseif ($aksi === 'tolak') {

        $id = (int)($_POST['id'] ?? 0);
        $catatan = trim($_POST['catatan'] ?? '');

        if ($id <= 0) {
            $errors[] = 'Data pembayaran tidak valid.';
        } elseif ($catatan === '') {
            $errors[] = 'Alasan penolakan wajib diisi.';
        } else {

            $stmt = $pdo->prepare("
                UPDATE pembayaran
                SET status = 'ditolak',
                    catatan_admin = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $catatan,
                $id
            ]);

            flash('success', 'Pembayaran ditolak.');
            redirect('pembayaran.php');
        }
    }
}


/*
|--------------------------------------------------------------------------
| Ambil data pembayaran
|--------------------------------------------------------------------------
*/
$bayars = $pdo->query("
    SELECT
        pb.*,
        p.nomor_pendaftaran,
        p.program,
        cs.nama_lengkap,
        cs.nama_panggilan,
        u.nama AS nama_user,
        u.email AS email_user
    FROM pembayaran pb

    JOIN pendaftaran p
        ON p.id = pb.pendaftaran_id

    LEFT JOIN calon_siswa cs
        ON cs.pendaftaran_id = p.id

    JOIN users u
        ON u.id = p.user_id

    ORDER BY pb.created_at DESC
")->fetchAll();


$pageTitle = 'Kelola Pembayaran - Admin';
$activePage = 'admin';

require __DIR__ . '/../includes/header.php';
?>

<style>
    .ppdb-payment-page {
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

    .ppdb-payment-page {
        min-height: calc(100vh - 100px);
        background: var(--paper);
        padding: 28px 0 60px;
    }

    .ppdb-payment-page .container {
        max-width: 1200px;
    }


    /* =========================================================
       HERO
       ========================================================= */

    .payment-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .payment-eyebrow {
        display: block;
        margin-bottom: 6px;

        color: var(--green);

        font-size: 11px;
        font-weight: 900;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .payment-hero h1 {
        margin: 0;

        color: var(--chalk);

        font-family: 'Fraunces', Georgia, serif;
        font-size: clamp(30px, 4vw, 40px);
        line-height: 1.1;
    }

    .payment-hero p {
        margin: 8px 0 0;

        color: var(--muted);

        font-size: 14px;
        line-height: 1.6;
    }

    .payment-total {
        display: flex;
        align-items: center;
        gap: 9px;

        padding: 12px 16px;

        border: 1px solid var(--paper-line);
        border-radius: 13px;

        background: #fff;

        color: var(--chalk);

        font-size: 13px;
        font-weight: 800;

        white-space: nowrap;
    }

    .payment-total-dot {
        width: 8px;
        height: 8px;

        border-radius: 50%;

        background: var(--green);
    }


    /* =========================================================
       ALERT
       ========================================================= */

    .payment-alert {
        padding: 13px 16px;
        margin-bottom: 17px;

        border-radius: 13px;

        font-size: 13px;
    }

    .payment-alert-success {
        background: var(--soft-green);
        border: 1px solid #CFE1CA;
        color: var(--green);
    }

    .payment-alert-error {
        background: var(--soft-berry);
        border: 1px solid #EBCAC5;
        color: var(--berry);
    }

    .payment-alert ul {
        margin: 0;
        padding-left: 18px;
    }


    /* =========================================================
       BACK
       ========================================================= */

    .payment-back {
        margin-bottom: 17px;
    }

    .payment-back a {
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

    .payment-back a:hover {
        border-color: var(--chalk);
        transform: translateY(-1px);
    }


    /* =========================================================
       MAIN CARD
       ========================================================= */

    .payment-card {
        overflow: hidden;

        border: 1px solid var(--paper-line);
        border-radius: 18px;

        background: #fff;

        box-shadow: 0 5px 18px rgba(47,69,56,.04);
    }

    .payment-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;

        padding: 19px 21px;

        border-bottom: 1px solid var(--paper-line);
    }

    .payment-card-title {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .payment-card-icon {
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

    .payment-card-icon svg {
        width: 19px;
        height: 19px;
    }

    .payment-card-head h2 {
        margin: 0;

        color: var(--chalk);

        font-family: 'Fraunces', Georgia, serif;
        font-size: 20px;
    }

    .payment-card-head p {
        margin: 3px 0 0;

        color: var(--muted);

        font-size: 11px;
    }


    /* =========================================================
       TABLE
       ========================================================= */

    .payment-table-wrap {
        overflow-x: auto;
    }

    .payment-table {
        width: 100%;
        min-width: 1050px;

        border-collapse: collapse;
    }

    .payment-table th {
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

    .payment-table td {
        padding: 14px;

        border-bottom: 1px solid #F0EBDD;

        color: var(--ink);

        font-size: 13px;
        vertical-align: middle;
    }

    .payment-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .payment-table tbody tr {
        transition: .15s ease;
    }

    .payment-table tbody tr:hover {
        background: #FCFAF5;
    }


    /* =========================================================
       DATA
       ========================================================= */

    .payment-number {
        color: var(--chalk);
        font-weight: 900;
        white-space: nowrap;
    }

    .payment-date {
        display: block;
        margin-top: 4px;

        color: var(--muted);
        font-size: 11px;
    }

    .payment-student {
        color: var(--ink);
        font-weight: 800;
    }

    .payment-callname {
        display: block;
        margin-top: 3px;

        color: var(--muted);
        font-size: 11px;
    }

    .payment-parent {
        color: var(--ink);
        font-weight: 700;
    }

    .payment-sender {
        display: block;
        margin-top: 3px;

        color: var(--muted);
        font-size: 11px;
    }

    .payment-program {
        color: var(--leaf);
        font-weight: 800;
    }

    .payment-amount {
        color: var(--chalk);

        font-family: 'Fraunces', Georgia, serif;
        font-size: 15px;
        font-weight: 800;

        white-space: nowrap;
    }


    /* =========================================================
       STATUS
       ========================================================= */

    .payment-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        padding: 6px 10px;

        border-radius: 999px;

        font-size: 10px;
        font-weight: 900;

        white-space: nowrap;
    }

    .payment-status::before {
        content: "";

        width: 6px;
        height: 6px;

        border-radius: 50%;

        background: currentColor;
    }

    .payment-status-waiting {
        background: var(--soft-amber);
        color: var(--amber);
    }

    .payment-status-confirmed {
        background: var(--soft-green);
        color: var(--green);
    }

    .payment-status-rejected {
        background: var(--soft-berry);
        color: var(--berry);
    }

    .payment-note {
        margin: 6px 0 0;

        color: var(--muted);

        font-size: 11px;
        line-height: 1.45;
    }

    .payment-admin-note {
        margin: 4px 0 0;

        color: var(--berry);

        font-size: 11px;
        line-height: 1.45;
    }


    /* =========================================================
       BUTTON
       ========================================================= */

    .payment-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        min-height: 34px;
        padding: 7px 11px;

        border: 1px solid var(--chalk);
        border-radius: 9px;

        background: var(--chalk);
        color: #fff;

        font-family: inherit;
        font-size: 11px;
        font-weight: 800;

        text-decoration: none;
        cursor: pointer;

        transition: .2s ease;
    }

    .payment-btn:hover {
        background: var(--leaf);
        border-color: var(--leaf);
        transform: translateY(-1px);
    }

    .payment-btn-outline {
        background: #fff;
        color: var(--chalk);
        border-color: var(--paper-line);
    }

    .payment-btn-outline:hover {
        background: var(--chalk);
        border-color: var(--chalk);
        color: #fff;
    }

    .payment-btn-danger {
        background: #fff;
        color: var(--berry);
        border-color: #EBCAC5;
    }

    .payment-btn-danger:hover {
        background: var(--berry);
        border-color: var(--berry);
        color: #fff;
    }

    .payment-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        flex-wrap: wrap;
    }

    .payment-no-action {
        color: var(--muted);
        font-size: 12px;
    }


    /* =========================================================
       EMPTY
       ========================================================= */

    .payment-empty {
        padding: 55px 20px;
        text-align: center;
    }

    .payment-empty-icon {
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

    .payment-empty-icon svg {
        width: 24px;
        height: 24px;
    }

    .payment-empty strong {
        display: block;

        color: var(--chalk);

        font-family: 'Fraunces', Georgia, serif;
        font-size: 19px;
    }

    .payment-empty p {
        margin: 5px 0 0;

        color: var(--muted);
        font-size: 12px;
    }


    /* =========================================================
       MODAL
       ========================================================= */

    .payment-modal {
        position: fixed;
        inset: 0;
        z-index: 1000;

        display: none;
        align-items: center;
        justify-content: center;

        padding: 20px;

        background: rgba(38,42,32,.48);
    }

    .payment-modal.active {
        display: flex;
    }

    .payment-modal-card {
        width: 100%;
        max-width: 480px;

        padding: 24px;

        border: 1px solid var(--paper-line);
        border-radius: 18px;

        background: #fff;

        box-shadow: 0 20px 60px rgba(38,42,32,.18);
    }

    .payment-modal-icon {
        width: 42px;
        height: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin-bottom: 13px;

        border-radius: 12px;

        background: var(--soft-green);
        color: var(--green);
    }

    .payment-modal-danger .payment-modal-icon {
        background: var(--soft-berry);
        color: var(--berry);
    }

    .payment-modal-icon svg {
        width: 20px;
        height: 20px;
    }

    .payment-modal-card h3 {
        margin: 0;

        color: var(--chalk);

        font-family: 'Fraunces', Georgia, serif;
        font-size: 22px;
    }

    .payment-modal-card > p {
        margin: 7px 0 18px;

        color: var(--muted);

        font-size: 13px;
    }

    .payment-modal-card > p strong {
        color: var(--ink);
    }

    .payment-modal-field {
        margin-bottom: 17px;
    }

    .payment-modal-field label {
        display: block;

        margin-bottom: 6px;

        color: var(--ink);

        font-size: 12px;
        font-weight: 900;
    }

    .payment-modal-field textarea {
        width: 100%;
        box-sizing: border-box;

        padding: 10px 12px;

        border: 1px solid var(--paper-line);
        border-radius: 11px;

        background: var(--paper);

        color: var(--ink);

        font-family: inherit;
        font-size: 13px;

        resize: vertical;
        outline: none;
    }

    .payment-modal-field textarea:focus {
        border-color: var(--leaf);
        box-shadow: 0 0 0 3px rgba(62,90,72,.08);
    }

    .payment-modal-buttons {
        display: flex;
        gap: 8px;
    }

    .payment-modal-buttons button {
        flex: 1;
    }


    /* =========================================================
       RESPONSIVE
       ========================================================= */

    @media (max-width: 700px) {

        .ppdb-payment-page {
            padding-top: 20px;
        }

        .payment-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .payment-total {
            width: 100%;
            box-sizing: border-box;
        }

        .payment-card-head {
            align-items: flex-start;
            flex-direction: column;
        }

        .payment-modal-card {
            padding: 20px;
        }

        .payment-modal-buttons {
            flex-direction: column;
        }
    }
</style>


<div class="ppdb-payment-page">

    <div class="container">

        <!-- HERO -->
        <section class="payment-hero">

            <div>

                <span class="payment-eyebrow">
                    Dashboard Admin
                </span>

                <h1>
                    Kelola Pembayaran
                </h1>

                <p>
                    Periksa bukti transfer dan konfirmasi pembayaran daftar ulang.
                </p>

            </div>

            <div class="payment-total">

                <span class="payment-total-dot"></span>

                <?= count($bayars) ?> pembayaran

            </div>

        </section>


        <!-- ALERT -->
        <?php if ($m = flash('success')): ?>

            <div class="payment-alert payment-alert-success">
                <?= e($m) ?>
            </div>

        <?php endif; ?>


        <?php if ($errors): ?>

            <div class="payment-alert payment-alert-error">

                <ul>

                    <?php foreach ($errors as $err): ?>

                        <li><?= e($err) ?></li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>


        <!-- BACK -->
        <div class="payment-back">

            <a href="index.php">
                <span>&larr;</span>
                Kembali ke Dashboard
            </a>

        </div>


        <!-- PAYMENT CARD -->
        <section class="payment-card">

            <div class="payment-card-head">

                <div class="payment-card-title">

                    <div class="payment-card-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <rect
                                x="3"
                                y="5"
                                width="18"
                                height="14"
                                rx="2"
                            />

                            <path d="M3 10h18"/>

                            <path d="M7 15h3"/>
                        </svg>

                    </div>

                    <div>

                        <h2>
                            Daftar Pembayaran
                        </h2>

                        <p>
                            Pembayaran yang masuk akan muncul di sini.
                        </p>

                    </div>

                </div>

            </div>


            <?php if (!$bayars): ?>

                <div class="payment-empty">

                    <div class="payment-empty-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <rect
                                x="3"
                                y="5"
                                width="18"
                                height="14"
                                rx="2"
                            />

                            <path d="M3 10h18"/>

                            <path d="M8 15h2"/>
                        </svg>

                    </div>

                    <strong>
                        Belum ada pembayaran
                    </strong>

                    <p>
                        Data pembayaran dari pendaftar akan muncul setelah mereka mengunggah bukti transfer.
                    </p>

                </div>

            <?php else: ?>

                <div class="payment-table-wrap">

                    <table class="payment-table">

                        <thead>

                            <tr>
                                <th>No. Pendaftaran</th>
                                <th>Calon Siswa</th>
                                <th>Orang Tua</th>
                                <th>Program</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Bukti Transfer</th>
                                <th>Aksi</th>
                            </tr>

                        </thead>


                        <tbody>

                        <?php foreach ($bayars as $b): ?>

                            <?php

                            $status = $b['status'] ?? 'menunggu';

                            $statusClass = match ($status) {
                                'dikonfirmasi' => 'payment-status-confirmed',
                                'ditolak' => 'payment-status-rejected',
                                default => 'payment-status-waiting'
                            };

                            $statusLabel = pembayaran_label($status);

                            ?>

                            <tr>

                                <!-- NOMOR -->
                                <td>

                                    <span class="payment-number">
                                        <?= e($b['nomor_pendaftaran'] ?? '-') ?>
                                    </span>

                                    <?php if (!empty($b['created_at'])): ?>

                                        <span class="payment-date">
                                            <?= date(
                                                'd/m/Y H:i',
                                                strtotime($b['created_at'])
                                            ) ?>
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- SISWA -->
                                <td>

                                    <span class="payment-student">
                                        <?= e($b['nama_lengkap'] ?? '-') ?>
                                    </span>

                                    <?php if (!empty($b['nama_panggilan'])): ?>

                                        <span class="payment-callname">
                                            Panggilan:
                                            <?= e($b['nama_panggilan']) ?>
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- ORANG TUA -->
                                <td>

                                    <span class="payment-parent">
                                        <?= e($b['nama_user'] ?? '-') ?>
                                    </span>

                                    <?php if (!empty($b['nama_pengirim'])): ?>

                                        <span class="payment-sender">
                                            Pengirim:
                                            <?= e($b['nama_pengirim']) ?>
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- PROGRAM -->
                                <td>

                                    <span class="payment-program">
                                        <?= e($b['program'] ?? '-') ?>
                                    </span>

                                </td>


                                <!-- NOMINAL -->
                                <td>

                                    <span class="payment-amount">
                                        Rp <?= number_format(
                                            (float)$b['nominal'],
                                            0,
                                            ',',
                                            '.'
                                        ) ?>
                                    </span>

                                </td>


                                <!-- STATUS -->
                                <td>

                                    <span class="payment-status <?= $statusClass ?>">
                                        <?= e($statusLabel) ?>
                                    </span>

                                    <?php if (!empty($b['catatan'])): ?>

                                        <p class="payment-note">
                                            Catatan:
                                            <?= e($b['catatan']) ?>
                                        </p>

                                    <?php endif; ?>

                                    <?php if (!empty($b['catatan_admin'])): ?>

                                        <p class="payment-admin-note">
                                            Admin:
                                            <?= e($b['catatan_admin']) ?>
                                        </p>

                                    <?php endif; ?>

                                </td>


                                <!-- BUKTI -->
                                <td>

                                    <?php if (!empty($b['bukti_file'])): ?>

                                        <a
                                            href="../file.php?f=<?= urlencode($b['bukti_file']) ?>"
                                            target="_blank"
                                            rel="noopener"
                                            class="payment-btn payment-btn-outline"
                                        >
                                            Lihat Bukti
                                        </a>

                                    <?php else: ?>

                                        <span class="payment-no-action">
                                            Tidak ada
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- AKSI -->
                                <td>

                                    <div class="payment-actions">

                                        <?php if ($status === 'menunggu'): ?>

                                            <button
                                                type="button"
                                                class="payment-btn"
                                                onclick="showPaymentModal(
                                                    'konfirmasi',
                                                    <?= (int)$b['id'] ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $b['nomor_pendaftaran'] ?? '-',
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                )"
                                            >
                                                Konfirmasi
                                            </button>

                                            <button
                                                type="button"
                                                class="payment-btn payment-btn-danger"
                                                onclick="showPaymentModal(
                                                    'tolak',
                                                    <?= (int)$b['id'] ?>,
                                                    <?= htmlspecialchars(
                                                        json_encode(
                                                            $b['nomor_pendaftaran'] ?? '-',
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_AMP |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_QUOT
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                )"
                                            >
                                                Tolak
                                            </button>

                                        <?php else: ?>

                                            <span class="payment-no-action">
                                                Selesai
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </section>

    </div>

</div>


<!-- =========================================================
     MODAL KONFIRMASI
     ========================================================= -->

<div
    id="modalKonfirmasi"
    class="payment-modal"
>

    <div class="payment-modal-card">

        <div class="payment-modal-icon">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="m5 12 4 4L19 6"/>
            </svg>

        </div>

        <h3>
            Konfirmasi Pembayaran
        </h3>

        <p>
            Pastikan bukti pembayaran sudah sesuai sebelum mengonfirmasi.
            <br>
            No. Pendaftaran:
            <strong id="konfirmasiNomor"></strong>
        </p>


        <form method="post">

            <input
                type="hidden"
                name="aksi"
                value="konfirmasi"
            >

            <input
                type="hidden"
                name="id"
                id="konfirmasiId"
            >


            <div class="payment-modal-field">

                <label>
                    Catatan Admin
                    <span style="color:var(--muted);font-weight:600">
                        (opsional)
                    </span>
                </label>

                <textarea
                    name="catatan"
                    rows="3"
                    placeholder="Catatan untuk pendaftar..."
                ></textarea>

            </div>


            <div class="payment-modal-buttons">

                <button
                    type="submit"
                    class="payment-btn"
                >
                    Konfirmasi Pembayaran
                </button>

                <button
                    type="button"
                    class="payment-btn payment-btn-outline"
                    onclick="hidePaymentModal('konfirmasi')"
                >
                    Batal
                </button>

            </div>

        </form>

    </div>

</div>


<!-- =========================================================
     MODAL TOLAK
     ========================================================= -->

<div
    id="modalTolak"
    class="payment-modal payment-modal-danger"
>

    <div class="payment-modal-card">

        <div class="payment-modal-icon">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M6 6l12 12"/>
                <path d="M18 6 6 18"/>
            </svg>

        </div>

        <h3>
            Tolak Pembayaran
        </h3>

        <p>
            Pembayaran akan ditandai sebagai tidak valid.
            <br>
            No. Pendaftaran:
            <strong id="tolakNomor"></strong>
        </p>


        <form method="post">

            <input
                type="hidden"
                name="aksi"
                value="tolak"
            >

            <input
                type="hidden"
                name="id"
                id="tolakId"
            >


            <div class="payment-modal-field">

                <label>
                    Alasan Penolakan
                    <span style="color:var(--berry)">
                        *
                    </span>
                </label>

                <textarea
                    name="catatan"
                    rows="3"
                    required
                    placeholder="Jelaskan alasan penolakan kepada pendaftar..."
                ></textarea>

            </div>


            <div class="payment-modal-buttons">

                <button
                    type="submit"
                    class="payment-btn payment-btn-danger"
                >
                    Tolak Pembayaran
                </button>

                <button
                    type="button"
                    class="payment-btn payment-btn-outline"
                    onclick="hidePaymentModal('tolak')"
                >
                    Batal
                </button>

            </div>

        </form>

    </div>

</div>


<script>
function showPaymentModal(type, id, nomor) {

    if (type === 'konfirmasi') {

        document.getElementById('konfirmasiId').value = id;
        document.getElementById('konfirmasiNomor').textContent = nomor;

        document
            .getElementById('modalKonfirmasi')
            .classList.add('active');

    }

    if (type === 'tolak') {

        document.getElementById('tolakId').value = id;
        document.getElementById('tolakNomor').textContent = nomor;

        document
            .getElementById('modalTolak')
            .classList.add('active');

    }
}


function hidePaymentModal(type) {

    if (type === 'konfirmasi') {

        document
            .getElementById('modalKonfirmasi')
            .classList.remove('active');

    }

    if (type === 'tolak') {

        document
            .getElementById('modalTolak')
            .classList.remove('active');

    }
}


/*
|--------------------------------------------------------------------------
| Klik area luar modal
|--------------------------------------------------------------------------
*/
document
    .getElementById('modalKonfirmasi')
    ?.addEventListener('click', function (e) {

        if (e.target === this) {
            hidePaymentModal('konfirmasi');
        }

    });


document
    .getElementById('modalTolak')
    ?.addEventListener('click', function (e) {

        if (e.target === this) {
            hidePaymentModal('tolak');
        }

    });


/*
|--------------------------------------------------------------------------
| ESC untuk menutup modal
|--------------------------------------------------------------------------
*/
document.addEventListener('keydown', function (e) {

    if (e.key === 'Escape') {

        hidePaymentModal('konfirmasi');
        hidePaymentModal('tolak');

    }

});
</script>


<?php require __DIR__ . '/../includes/footer.php'; ?>
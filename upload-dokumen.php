<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require_login();

date_default_timezone_set('Asia/Jakarta');

$user = current_user();

$stmt = $pdo->prepare('SELECT * FROM pendaftaran WHERE user_id=? ORDER BY created_at DESC LIMIT 1');
$stmt->execute([$user['id']]);
$pendaftaran = $stmt->fetch();
if (!$pendaftaran) {
    flash('success', 'Mulai pendaftaran terlebih dahulu.');
    redirect('pendaftaran-form.php');
}

$pid = $pendaftaran['id'];

// Tipe file asli (dicek dari isi file) => ekstensi yang dipakai
$allowedTypes = [
    'image/jpeg'      => 'jpg',
    'image/png'       => 'png',
    'application/pdf' => 'pdf',
];
$maxSize = 2 * 1024 * 1024; // 2MB
$jenisDokumen = ['Kartu Keluarga', 'Akta Kelahiran', 'KTP Orang Tua', 'Pas Foto', 'Dokumen Pendukung'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis = trim($_POST['jenis'] ?? '');
    if (!in_array($jenis, $jenisDokumen, true)) {
        $errors[] = 'Jenis dokumen tidak valid.';
    }
    if (empty($_FILES['file']['name'])) {
        $errors[] = 'File wajib dipilih.';
    }

    if (!$errors) {
        $file = $_FILES['file'];
        $mime = '';

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload gagal, coba lagi.';
        } elseif ($file['size'] > $maxSize) {
            $errors[] = 'Ukuran file maksimal 2MB.';
        } else {
            // Cek tipe asli file, bukan tipe kiriman browser
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = (string) $finfo->file($file['tmp_name']);

            if (!isset($allowedTypes[$mime])) {
                $errors[] = 'Format file harus JPG, PNG, atau PDF.';
            }
        }

        if (!$errors) {
            // Ekstensi ditentukan server dari whitelist, bukan dari nama file user
            $ext = $allowedTypes[$mime];
            $namaFile = 'dok_' . $pid . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $namaFile)) {
                $pdo->prepare('INSERT INTO dokumen (pendaftaran_id,jenis,nama_file,path_file,status) VALUES (?,?,?,?,?)')
                    ->execute([$pid, $jenis, basename($file['name']), 'uploads/' . $namaFile, 'menunggu']);
                flash('success', 'Dokumen berhasil diunggah.');
                redirect('upload-dokumen.php');
            } else {
                $errors[] = 'Gagal menyimpan file.';
            }
        }
    }
}

$dokumen = $pdo->prepare('SELECT * FROM dokumen WHERE pendaftaran_id=? ORDER BY uploaded_at DESC');
$dokumen->execute([$pid]);
$doks = $dokumen->fetchAll();

$uploadedTypes = array_column($doks, 'jenis');
$statusLabel = ['menunggu' => 'Menunggu', 'valid' => 'Valid', 'tidak_valid' => 'Tidak Valid'];

$pageTitle = 'Upload Dokumen - TK Harapan Bunda';
$activePage = 'dashboard';
require __DIR__ . '/includes/header.php';
?>

<style>

/* =========================================================
   PPDB UPLOAD DOKUMEN
   TEMA RAPOR & KARTU PELAJAR
   ========================================================= */

.ppdb-upload-page {
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

    font-family: 'Inter', system-ui, sans-serif;
    color: var(--ink);

    background: linear-gradient(rgba(231,223,206,.25) 1px, transparent 1px);
    background-size: 100% 36px;
    padding-bottom: 70px;
}


/* ---------- Hero ---------- */

.ppdb-upload-page__hero {
    position: relative;
    overflow: hidden;
    padding: 48px 0;
    background: var(--paper);
    border-bottom: 1px solid var(--paper-line);
}

.ppdb-upload-page__hero::after {
    content: 'DOKUMEN';
    position: absolute;
    right: -10px;
    bottom: -30px;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 125px;
    font-weight: 700;
    color: rgba(47,69,56,.045);
    pointer-events: none;
}

.ppdb-upload-page__eyebrow {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 9px;
    margin: 0 0 10px;
    color: var(--leaf);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.ppdb-upload-page__eyebrow::before {
    content: '';
    width: 27px;
    height: 2px;
    background: var(--coral);
}

.ppdb-upload-page h1 {
    position: relative;
    z-index: 1;
    margin: 0 0 8px;
    color: var(--chalk);
    font-family: 'Fraunces', Georgia, serif;
    font-size: clamp(34px, 5vw, 48px);
    line-height: 1.05;
}

.ppdb-upload-page__hero-text {
    position: relative;
    z-index: 1;
    max-width: 680px;
    margin: 0;
    color: var(--muted);
    font-size: 14px;
    line-height: 1.7;
}


/* ---------- Content ---------- */

.ppdb-upload-page__content {
    padding-top: 38px;
}

.ppdb-upload-page__alert {
    margin-bottom: 20px;
    padding: 15px 18px;
    border: 1px solid;
    border-radius: 13px;
    font-size: 13.5px;
    line-height: 1.6;
}

.ppdb-upload-page__alert--success {
    background: var(--leaf-soft);
    border-color: #BBD6BF;
    color: var(--chalk);
}

.ppdb-upload-page__alert--error {
    background: var(--berry-soft);
    border-color: #E9C8C4;
    color: #8A2D2D;
}

.ppdb-upload-page__alert-title {
    margin-bottom: 6px;
    font-size: 14px;
    font-weight: 700;
}

.ppdb-upload-page__alert ul {
    margin: 0;
    padding-left: 20px;
    font-size: 13px;
    line-height: 1.7;
}


/* ---------- Layout ---------- */

.ppdb-upload-page__layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 270px;
    gap: 18px;
    align-items: start;
}

.ppdb-upload-page__main {
    display: grid;
    gap: 18px;
}

.ppdb-upload-page__card {
    padding: 30px;
    background: #fff;
    border: 1px solid var(--paper-line);
    border-radius: 16px;
}


/* ---------- Section heading ---------- */

.ppdb-upload-page__section-heading {
    display: flex;
    align-items: center;
    gap: 13px;
    margin-bottom: 23px;
    padding-bottom: 18px;
    border-bottom: 1px solid var(--paper-line);
}

.ppdb-upload-page__section-number {
    width: 39px;
    height: 39px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: var(--leaf-soft);
    color: var(--leaf);
    font-family: 'Fraunces', Georgia, serif;
    font-size: 14px;
    font-weight: 700;
}

.ppdb-upload-page__section-heading--amber .ppdb-upload-page__section-number {
    background: var(--amber-soft);
    color: var(--amber);
}

.ppdb-upload-page__section-heading h2 {
    margin: 0 0 3px;
    color: var(--chalk);
    font-family: 'Fraunces', Georgia, serif;
    font-size: 23px;
}

.ppdb-upload-page__section-heading p {
    margin: 0;
    color: var(--muted);
    font-size: 12.5px;
}


/* ---------- Form ---------- */

.ppdb-upload-page__fields {
    display: grid;
    gap: 17px;
}

.ppdb-upload-page__field {
    display: block;
}

.ppdb-upload-page__field > span {
    display: block;
    margin-bottom: 7px;
    color: var(--ink);
    font-size: 13px;
    font-weight: 600;
}

.ppdb-upload-page__required {
    color: var(--coral);
}

.ppdb-upload-page__field select,
.ppdb-upload-page__field input[type="file"] {
    width: 100%;
    box-sizing: border-box;
    padding: 11px 12px;
    border: 1px solid #DCD5C6;
    border-radius: 9px;
    background: #FCFAF5;
    color: var(--ink);
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 14px;
    outline: none;
    transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
}

.ppdb-upload-page__field select {
    height: 43px;
}

.ppdb-upload-page__field select:focus,
.ppdb-upload-page__field input[type="file"]:focus {
    border-color: var(--leaf);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(63,126,82,.09);
}

.ppdb-upload-page__hint {
    margin: 7px 0 0;
    color: var(--muted);
    font-size: 12px;
}

.ppdb-upload-page__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 26px;
    padding-top: 23px;
    border-top: 1px solid var(--paper-line);
}

.ppdb-upload-page__btn {
    min-height: 45px;
    padding: 0 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 9px;
    background: var(--coral);
    color: #fff;
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    transition: transform .18s ease, box-shadow .18s ease;
}

.ppdb-upload-page__btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 7px 18px rgba(225,85,46,.18);
}

.ppdb-upload-page__btn--outline {
    background: transparent;
    color: var(--chalk);
    border: 1px solid var(--paper-line);
}

.ppdb-upload-page__btn--outline:hover {
    background: #fff;
    box-shadow: none;
}


/* ---------- Daftar dokumen ---------- */

.ppdb-upload-page__empty {
    margin: 0;
    padding: 22px;
    background: var(--paper);
    border: 1px dashed var(--paper-line);
    border-radius: 12px;
    color: var(--muted);
    font-size: 13.5px;
    text-align: center;
}

.ppdb-upload-page__list {
    display: grid;
    gap: 12px;
}

.ppdb-upload-page__doc {
    padding: 16px 18px;
    background: #FCFAF5;
    border: 1px solid var(--paper-line);
    border-radius: 13px;
}

.ppdb-upload-page__doc-top {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.ppdb-upload-page__doc-name {
    display: block;
    color: var(--chalk);
    font-family: 'Fraunces', Georgia, serif;
    font-size: 17px;
    font-weight: 600;
}

.ppdb-upload-page__doc-file {
    display: block;
    margin-top: 2px;
    color: var(--muted);
    font-size: 12.5px;
    word-break: break-all;
}

.ppdb-upload-page__badge {
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}

.ppdb-upload-page__badge--menunggu {
    background: var(--amber-soft);
    color: var(--amber);
}

.ppdb-upload-page__badge--valid {
    background: var(--leaf-soft);
    color: var(--leaf);
}

.ppdb-upload-page__badge--tidak_valid {
    background: var(--berry-soft);
    color: var(--berry);
}

.ppdb-upload-page__doc-reason {
    margin: 10px 0 0;
    color: var(--berry);
    font-size: 13px;
    line-height: 1.55;
}

.ppdb-upload-page__doc-date {
    display: block;
    margin-top: 9px;
    color: var(--muted);
    font-size: 12px;
}


/* ---------- Sidebar ---------- */

.ppdb-upload-page__side {
    display: grid;
    gap: 16px;
    position: sticky;
    top: 20px;
}

.ppdb-upload-page__dark-card {
    position: relative;
    overflow: hidden;
    padding: 24px;
    background: var(--chalk);
    color: #F3F0E6;
    border-radius: 15px;
}

.ppdb-upload-page__dark-card::after {
    content: 'PPDB';
    position: absolute;
    right: -10px;
    bottom: -26px;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 72px;
    font-weight: 700;
    color: rgba(255,255,255,.045);
}

.ppdb-upload-page__dark-label {
    margin: 0 0 6px;
    color: #BFCBBF;
    font-size: 11px;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.ppdb-upload-page__dark-card h3 {
    margin: 0 0 16px;
    color: #F3F0E6;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 20px;
}

.ppdb-upload-page__number {
    position: relative;
    z-index: 1;
    padding: 13px 0;
    border-top: 1px solid rgba(255,255,255,.12);
    border-bottom: 1px solid rgba(255,255,255,.12);
}

.ppdb-upload-page__number small {
    display: block;
    margin-bottom: 3px;
    color: #BFCBBF;
    font-size: 11px;
}

.ppdb-upload-page__number strong {
    color: #fff;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 18px;
    letter-spacing: .02em;
}

.ppdb-upload-page__info {
    padding: 21px;
    background: var(--paper);
    border: 1px solid var(--paper-line);
    border-radius: 15px;
}

.ppdb-upload-page__info h3 {
    margin: 0 0 13px;
    color: var(--chalk);
    font-family: 'Fraunces', Georgia, serif;
    font-size: 18px;
}

.ppdb-upload-page__info ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.ppdb-upload-page__info li {
    display: flex;
    gap: 9px;
    padding: 9px 0;
    border-top: 1px solid var(--paper-line);
    color: var(--muted);
    font-size: 12.5px;
    line-height: 1.5;
}

.ppdb-upload-page__info li:first-child {
    padding-top: 0;
    border-top: none;
}

.ppdb-upload-page__info li.is-done {
    color: var(--chalk);
    font-weight: 600;
}

.ppdb-upload-page__check {
    width: 19px;
    height: 19px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--paper-line);
    border-radius: 50%;
    background: #fff;
    color: transparent;
    font-size: 9px;
    font-weight: 700;
}

.ppdb-upload-page__info li.is-done .ppdb-upload-page__check {
    border-color: transparent;
    background: var(--leaf-soft);
    color: var(--leaf);
}


/* ---------- Responsive ---------- */

@media (max-width: 900px) {
    .ppdb-upload-page__layout {
        grid-template-columns: 1fr;
    }

    .ppdb-upload-page__side {
        position: static;
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 650px) {
    .ppdb-upload-page__hero {
        padding: 42px 0;
    }

    .ppdb-upload-page__content {
        padding-top: 28px;
    }

    .ppdb-upload-page__card {
        padding: 20px;
    }

    .ppdb-upload-page__side {
        grid-template-columns: 1fr;
    }

    .ppdb-upload-page__actions {
        flex-direction: column;
    }

    .ppdb-upload-page__btn {
        width: 100%;
    }
}

</style>


<!-- Pakai <div>, bukan <main>, karena header.php sudah membuka <main> -->
<div class="ppdb-upload-page">

    <!-- HERO -->
    <section class="ppdb-upload-page__hero">
        <div class="container">

            <p class="ppdb-upload-page__eyebrow">
                Dokumen Persyaratan
            </p>

            <h1>Upload Dokumen</h1>

            <p class="ppdb-upload-page__hero-text">
                Unggah dokumen persyaratan PPDB dalam format JPG, PNG,
                atau PDF dengan ukuran maksimal 2MB.
            </p>

        </div>
    </section>


    <!-- CONTENT -->
    <section class="container ppdb-upload-page__content">

        <?php if ($m = flash('success')): ?>
            <div class="ppdb-upload-page__alert ppdb-upload-page__alert--success">
                <?= e($m) ?>
            </div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="ppdb-upload-page__alert ppdb-upload-page__alert--error">
                <div class="ppdb-upload-page__alert-title">
                    Periksa kembali data berikut:
                </div>
                <ul>
                    <?php foreach ($errors as $er): ?>
                        <li><?= e($er) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>


        <div class="ppdb-upload-page__layout">

            <div class="ppdb-upload-page__main">

                <!-- FORM UPLOAD -->
                <form
                    method="post"
                    enctype="multipart/form-data"
                    class="ppdb-upload-page__card"
                >

                    <div class="ppdb-upload-page__section-heading">
                        <span class="ppdb-upload-page__section-number">01</span>

                        <div>
                            <h2>Unggah Dokumen</h2>
                            <p>Pilih jenis dokumen lalu pilih file yang akan diunggah.</p>
                        </div>
                    </div>

                    <div class="ppdb-upload-page__fields">

                        <label class="ppdb-upload-page__field">
                            <span>
                                Jenis dokumen
                                <b class="ppdb-upload-page__required">*</b>
                            </span>

                            <select name="jenis" required>
                                <option value="">Pilih jenis</option>
                                <?php foreach ($jenisDokumen as $j): ?>
                                    <option value="<?= e($j) ?>"><?= e($j) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label class="ppdb-upload-page__field">
                            <span>
                                File
                                <b class="ppdb-upload-page__required">*</b>
                            </span>

                            <input
                                type="file"
                                name="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                required
                            >

                            <p class="ppdb-upload-page__hint">
                                Format JPG, PNG, atau PDF. Maksimal 2MB.
                            </p>
                        </label>

                    </div>

                    <div class="ppdb-upload-page__actions">
                        <button type="submit" class="ppdb-upload-page__btn">
                            Upload
                        </button>

                        <a
                            href="dashboard.php"
                            class="ppdb-upload-page__btn ppdb-upload-page__btn--outline"
                        >
                            Kembali ke Dashboard
                        </a>
                    </div>

                </form>


                <!-- DAFTAR DOKUMEN -->
                <div class="ppdb-upload-page__card">

                    <div class="ppdb-upload-page__section-heading ppdb-upload-page__section-heading--amber">
                        <span class="ppdb-upload-page__section-number">02</span>

                        <div>
                            <h2>Dokumen Terunggah</h2>
                            <p>Status pemeriksaan dokumen oleh sekolah.</p>
                        </div>
                    </div>

                    <?php if (!$doks): ?>

                        <p class="ppdb-upload-page__empty">
                            Belum ada dokumen yang diunggah.
                        </p>

                    <?php else: ?>

                        <div class="ppdb-upload-page__list">

                            <?php foreach ($doks as $dok):
                                $st  = $dok['status'] ?? 'menunggu';
                                if (!isset($statusLabel[$st])) {
                                    $st = 'menunggu';
                                }
                            ?>

                                <div class="ppdb-upload-page__doc">

                                    <div class="ppdb-upload-page__doc-top">

                                        <div>
                                            <span class="ppdb-upload-page__doc-name">
                                                <?= e($dok['jenis']) ?>
                                            </span>

                                            <small class="ppdb-upload-page__doc-file">
                                                <?= e($dok['nama_file']) ?>
                                            </small>
                                        </div>

                                        <span class="ppdb-upload-page__badge ppdb-upload-page__badge--<?= $st ?>">
                                            <?= $statusLabel[$st] ?>
                                        </span>

                                    </div>

                                    <?php if (!empty($dok['alasan'])): ?>
                                        <p class="ppdb-upload-page__doc-reason">
                                            Alasan: <?= e($dok['alasan']) ?>
                                        </p>
                                    <?php endif; ?>

                                    <small class="ppdb-upload-page__doc-date">
                                        <?= date('d/m/Y H:i', strtotime($dok['uploaded_at'])) ?>
                                    </small>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </div>

            </div>


            <!-- SIDEBAR -->
            <aside class="ppdb-upload-page__side">

                <div class="ppdb-upload-page__dark-card">

                    <p class="ppdb-upload-page__dark-label">
                        Nomor pendaftaran
                    </p>

                    <h3>Pendaftaran Anda</h3>

                    <div class="ppdb-upload-page__number">
                        <small>ID Pendaftaran</small>
                        <strong><?= e($pendaftaran['nomor_pendaftaran']) ?></strong>
                    </div>

                </div>


                <div class="ppdb-upload-page__info">

                    <h3>Dokumen yang diperlukan</h3>

                    <ul>
                        <?php foreach ($jenisDokumen as $j): ?>
                            <li class="<?= in_array($j, $uploadedTypes, true) ? 'is-done' : '' ?>">
                                <span class="ppdb-upload-page__check">✓</span>
                                <?= e($j) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div>

            </aside>

        </div>

    </section>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
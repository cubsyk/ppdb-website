<?php
declare(strict_types=1);

require __DIR__ . '/includes/auth.php';
require_login();

$user = current_user();

$stmt = $pdo->prepare('
    SELECT p.*, cs.nama_lengkap
    FROM pendaftaran p
    LEFT JOIN calon_siswa cs ON cs.pendaftaran_id = p.id
    WHERE p.user_id = ?
    ORDER BY p.created_at DESC
    LIMIT 1
');
$stmt->execute([$user['id']]);
$pendaftaran = $stmt->fetch();

if (!$pendaftaran || !in_array($pendaftaran['status'], ['draft', 'perlu_perbaikan'])) {
    flash('success', 'Pendaftaran tidak dapat dikirim pada status ini.');
    redirect('dashboard.php');
}

$pid = $pendaftaran['id'];

$cs = $pdo->prepare('SELECT id FROM calon_siswa WHERE pendaftaran_id = ?');
$cs->execute([$pid]);
$hasSiswa = $cs->fetch();

$ot = $pdo->prepare('SELECT id FROM orang_tua WHERE pendaftaran_id = ?');
$ot->execute([$pid]);
$hasOt = $ot->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!$hasSiswa || !$hasOt) {
        flash('success', 'Isi data calon siswa dan orang tua terlebih dahulu.');
        redirect('pendaftaran-form.php');
    }

    $pdo->prepare('
        UPDATE pendaftaran
        SET status = ?, tanggal_daftar = ?
        WHERE id = ?
    ')->execute([
        'menunggu_verifikasi',
        date('Y-m-d H:i:s'),
        $pid
    ]);

    flash(
        'success',
        'Pendaftaran berhasil dikirim! Panitia akan memeriksa data Anda.'
    );

    redirect('dashboard.php');
}

$pageTitle = 'Kirim Pendaftaran - TK Harapan Bunda';
$activePage = 'dashboard';

require __DIR__ . '/includes/header.php';
?>

<style>
/* =========================================================
   KIRIM PENDAFTARAN
   Tema Rapor & Kartu Pelajar
   ========================================================= */

.ppdb-submit-page {
    padding: 42px 0 80px;
    background:
        linear-gradient(rgba(231, 223, 206, .45) 1px, transparent 1px),
        #FBF7EF;
    background-size: 100% 34px;
}

.ppdb-submit-wrap {
    max-width: 760px;
    margin: 0 auto;
}

/* ---------- Header ---------- */

.ppdb-submit-heading {
    text-align: center;
    margin-bottom: 28px;
}

.ppdb-submit-heading .eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    padding: 7px 13px;
    border: 1px solid #DCD3C0;
    border-radius: 999px;
    background: #FFFDF8;
    color: #6B7060;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.ppdb-submit-heading .eyebrow svg {
    width: 15px;
    height: 15px;
}

.ppdb-submit-heading h1 {
    margin: 0;
    color: #2F4538;
    font-family: "Fraunces", Georgia, serif;
    font-size: clamp(30px, 5vw, 44px);
    line-height: 1.1;
}

.ppdb-submit-heading p {
    max-width: 560px;
    margin: 12px auto 0;
    color: #6B7060;
    font-size: 15px;
    line-height: 1.7;
}

/* ---------- Main Card ---------- */

.ppdb-submit-card {
    position: relative;
    overflow: hidden;
    border: 1px solid #E3DCCB;
    border-radius: 22px;
    background: #FFFDF8;
    box-shadow: 0 12px 35px rgba(47, 69, 56, .08);
}

/* garis dekorasi atas */

.ppdb-submit-card::before {
    content: "";
    display: block;
    height: 7px;
    background: #2F4538;
}

/* ---------- Card Inner ---------- */

.ppdb-submit-inner {
    padding: 38px;
}

.ppdb-submit-status {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 30px;
    padding-bottom: 24px;
    border-bottom: 1px dashed #D9D0BC;
}

.ppdb-submit-status-icon {
    width: 48px;
    height: 48px;
    flex: 0 0 48px;
    display: grid;
    place-items: center;
    border-radius: 14px;
    background: #E7F1E4;
    color: #3F7E52;
}

.ppdb-submit-status-icon svg {
    width: 25px;
    height: 25px;
}

.ppdb-submit-status-text strong {
    display: block;
    margin-bottom: 3px;
    color: #2F4538;
    font-size: 15px;
}

.ppdb-submit-status-text span {
    color: #6B7060;
    font-size: 13px;
}

/* ---------- Info ---------- */

.ppdb-submit-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 28px;
}

.ppdb-submit-info-item {
    padding: 18px;
    border: 1px solid #E7DFCE;
    border-radius: 15px;
    background: #FBF7EF;
}

.ppdb-submit-info-label {
    margin-bottom: 7px;
    color: #8A8D80;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
}

.ppdb-submit-info-value {
    color: #262A20;
    font-size: 15px;
    font-weight: 700;
    word-break: break-word;
}

.ppdb-submit-number {
    color: #2F4538;
    font-family: "Fraunces", Georgia, serif;
    font-size: 18px;
}

/* ---------- Confirmation ---------- */

.ppdb-submit-confirm {
    padding: 24px;
    margin-bottom: 28px;
    border: 1px solid #E8D7B9;
    border-radius: 17px;
    background: #FBF0DC;
}

.ppdb-submit-confirm-title {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 9px;
    color: #80580B;
    font-size: 14px;
    font-weight: 800;
}

.ppdb-submit-confirm-title svg {
    width: 18px;
    height: 18px;
    flex: 0 0 18px;
}

.ppdb-submit-confirm p {
    margin: 0;
    color: #6F6048;
    font-size: 13px;
    line-height: 1.7;
}

/* ---------- Buttons ---------- */

.ppdb-submit-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.ppdb-submit-actions .btn {
    min-height: 46px;
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 700;
}

.ppdb-submit-main-btn {
    border: 0;
    background: #2F4538;
    color: #fff;
    box-shadow: 0 6px 16px rgba(47, 69, 56, .18);
    cursor: pointer;
    transition: transform .18s ease, box-shadow .18s ease;
}

.ppdb-submit-main-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 9px 20px rgba(47, 69, 56, .22);
}

.ppdb-submit-main-btn svg {
    width: 17px;
    height: 17px;
    margin-right: 7px;
    vertical-align: -3px;
}

.ppdb-submit-actions .btn-outline {
    border: 1px solid #D6CEBC;
    background: #FFFDF8;
    color: #3E5A48;
}

.ppdb-submit-actions .btn-outline:hover {
    background: #F4EFE4;
}

/* ---------- Incomplete ---------- */

.ppdb-submit-incomplete {
    text-align: center;
    padding: 12px 0 4px;
}

.ppdb-submit-warning-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 18px;
    display: grid;
    place-items: center;
    border-radius: 18px;
    background: #FBF0DC;
    color: #C6820E;
}

.ppdb-submit-warning-icon svg {
    width: 31px;
    height: 31px;
}

.ppdb-submit-incomplete h2 {
    margin: 0 0 9px;
    color: #2F4538;
    font-family: "Fraunces", Georgia, serif;
    font-size: 27px;
}

.ppdb-submit-incomplete p {
    max-width: 480px;
    margin: 0 auto 22px;
    color: #6B7060;
    font-size: 14px;
    line-height: 1.7;
}

.ppdb-submit-incomplete .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 45px;
    padding: 11px 19px;
    border-radius: 11px;
    background: #2F4538;
    color: #fff;
    font-weight: 700;
}

/* ---------- Bottom Note ---------- */

.ppdb-submit-note {
    margin-top: 18px;
    text-align: center;
    color: #8A8D80;
    font-size: 12px;
    line-height: 1.6;
}

/* ---------- Responsive ---------- */

@media (max-width: 640px) {

    .ppdb-submit-page {
        padding: 28px 0 60px;
    }

    .ppdb-submit-inner {
        padding: 25px 20px;
    }

    .ppdb-submit-info {
        grid-template-columns: 1fr;
    }

    .ppdb-submit-actions {
        flex-direction: column;
    }

    .ppdb-submit-actions .btn {
        width: 100%;
        margin: 0 !important;
    }

    .ppdb-submit-status {
        align-items: flex-start;
    }
}
</style>

<section class="ppdb-submit-page">
    <div class="container ppdb-submit-wrap">

        <div class="ppdb-submit-heading">
            <div class="eyebrow">
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16v16H4z"/>
                    <path d="M8 8h8"/>
                    <path d="M8 12h8"/>
                    <path d="M8 16h5"/>
                </svg>
                Tahap Akhir
            </div>

            <h1>Kirim Pendaftaran</h1>

            <p>
                Cek sekali lagi data yang sudah diisi.
                Kalau semuanya sudah sesuai, pendaftaran bisa langsung dikirim
                untuk diperiksa oleh panitia.
            </p>
        </div>

        <div class="ppdb-submit-card">
            <div class="ppdb-submit-inner">

                <?php if (!$hasSiswa || !$hasOt): ?>

                    <div class="ppdb-submit-incomplete">

                        <div class="ppdb-submit-warning-icon">
                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M12 9v4"/>
                                <path d="M12 17h.01"/>
                                <path d="M10.3 3.7 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3l-7.5-13.3a2 2 0 0 0-3.4 0Z"/>
                            </svg>
                        </div>

                        <h2>Data Belum Lengkap</h2>

                        <p>
                            Data calon siswa dan orang tua belum ditemukan.
                            Lengkapi formulir terlebih dahulu sebelum
                            mengirim pendaftaran.
                        </p>

                        <a class="btn" href="pendaftaran-form.php">
                            Isi Data Dahulu
                        </a>

                    </div>

                <?php else: ?>

                    <div class="ppdb-submit-status">

                        <div class="ppdb-submit-status-icon">
                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M9 11l3 3L22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>

                        <div class="ppdb-submit-status-text">
                            <strong>Data Pendaftaran Siap Dikirim</strong>
                            <span>
                                Pastikan informasi di bawah sudah benar.
                            </span>
                        </div>

                    </div>

                    <div class="ppdb-submit-info">

                        <div class="ppdb-submit-info-item">
                            <div class="ppdb-submit-info-label">
                                Nomor Pendaftaran
                            </div>

                            <div class="ppdb-submit-info-value ppdb-submit-number">
                                <?= e($pendaftaran['nomor_pendaftaran']) ?>
                            </div>
                        </div>

                        <?php if ($pendaftaran['nama_lengkap']): ?>

                            <div class="ppdb-submit-info-item">
                                <div class="ppdb-submit-info-label">
                                    Calon Peserta Didik
                                </div>

                                <div class="ppdb-submit-info-value">
                                    <?= e($pendaftaran['nama_lengkap']) ?>
                                </div>
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="ppdb-submit-confirm">

                        <div class="ppdb-submit-confirm-title">
                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 8v4"/>
                                <path d="M12 16h.01"/>
                            </svg>

                            Sebelum dikirim
                        </div>

                        <p>
                            Setelah pendaftaran dikirim, data tidak dapat
                            diubah sampai ada konfirmasi atau permintaan
                            perbaikan dari admin/panitia.
                        </p>

                    </div>

                    <form method="post">
                        <div class="ppdb-submit-actions">

                            <button
                                class="btn ppdb-submit-main-btn"
                                type="submit"
                            >
                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M22 2 11 13"/>
                                    <path d="m22 2-7 20-4-9-9-4Z"/>
                                </svg>
                                Kirim Pendaftaran Sekarang
                            </button>

                            <a
                                class="btn btn-outline"
                                href="dashboard.php"
                            >
                                Batal
                            </a>

                        </div>
                    </form>

                    <div class="ppdb-submit-note">
                        Pastikan nomor pendaftaran disimpan untuk keperluan
                        pengecekan status selanjutnya.
                    </div>

                <?php endif; ?>

            </div>
        </div>

    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
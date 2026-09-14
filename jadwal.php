<?php
$pageTitle = 'Jadwal PPDB - TK Harapan Bunda';
$activePage = 'jadwal';
require __DIR__ . '/includes/header.php';
?>

<style>
/* =========================================================
   PPDB SCHEDULE
   Tema: Rapor & Kartu Pelajar
   ========================================================= */

.ppdb-schedule {
    --paper: #FBF7EF;
    --paper-line: #E7DFCE;
    --chalk: #2F4538;
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
}

.ppdb-schedule h1,
.ppdb-schedule h2,
.ppdb-schedule h3 {
    font-family: 'Fraunces', Georgia, serif;
    font-weight: 600;
    letter-spacing: -0.015em;
}

.ppdb-schedule p {
    line-height: 1.7;
}

/* ---------- Header ---------- */

.ppdb-schedule__header {
    background: var(--paper);
    border-bottom: 1px solid var(--paper-line);
    padding: 58px 0;
    position: relative;
    overflow: hidden;
}

.ppdb-schedule__header::after {
    content: '2026';
    position: absolute;
    right: 15px;
    bottom: -30px;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 125px;
    font-weight: 700;
    color: rgba(47,69,56,.045);
    pointer-events: none;
}

.ppdb-schedule__eyebrow {
    display: flex;
    align-items: center;
    gap: 9px;
    color: var(--leaf);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin: 0 0 13px;
    position: relative;
    z-index: 1;
}

.ppdb-schedule__eyebrow::before {
    content: '';
    width: 27px;
    height: 2px;
    background: var(--coral);
}

.ppdb-schedule__header h1 {
    color: var(--chalk);
    font-size: clamp(36px, 5vw, 52px);
    margin: 0 0 9px;
    position: relative;
    z-index: 1;
}

.ppdb-schedule__header p:last-child {
    color: var(--muted);
    margin: 0;
    font-size: 15px;
    position: relative;
    z-index: 1;
}

/* ---------- Main ---------- */

.ppdb-schedule__section {
    padding: 60px 0;
}

.ppdb-schedule__intro {
    max-width: 690px;
    margin-bottom: 35px;
}

.ppdb-schedule__intro-label {
    color: var(--coral);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin: 0 0 7px;
}

.ppdb-schedule__intro h2 {
    color: var(--chalk);
    font-size: 31px;
    margin: 0 0 9px;
}

.ppdb-schedule__intro p:last-child {
    color: var(--muted);
    font-size: 14.5px;
    margin: 0;
}

/* ---------- Timeline ---------- */

.ppdb-schedule__timeline {
    position: relative;
    max-width: 900px;
    margin: 0 auto;
    padding: 5px 0;
}

.ppdb-schedule__timeline::before {
    content: '';
    position: absolute;
    left: 126px;
    top: 28px;
    bottom: 28px;
    width: 2px;
    background: var(--paper-line);
}

.ppdb-schedule__item {
    display: grid;
    grid-template-columns: 105px 42px 1fr;
    gap: 20px;
    align-items: center;
    margin-bottom: 18px;
    position: relative;
}

.ppdb-schedule__item:last-child {
    margin-bottom: 0;
}

.ppdb-schedule__date {
    color: var(--muted);
    font-size: 12.5px;
    font-weight: 700;
    line-height: 1.45;
    text-align: right;
}

.ppdb-schedule__dot {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid var(--paper-line);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 2;
    color: var(--muted);
    font-family: 'Fraunces', Georgia, serif;
    font-size: 14px;
    font-weight: 700;
}

.ppdb-schedule__item:nth-child(1) .ppdb-schedule__dot {
    background: var(--leaf);
    border-color: var(--leaf);
    color: #fff;
}

.ppdb-schedule__item:nth-child(2) .ppdb-schedule__dot {
    background: var(--amber);
    border-color: var(--amber);
    color: #fff;
}

.ppdb-schedule__item:nth-child(3) .ppdb-schedule__dot {
    background: var(--coral);
    border-color: var(--coral);
    color: #fff;
}

.ppdb-schedule__item:nth-child(4) .ppdb-schedule__dot {
    background: var(--chalk);
    border-color: var(--chalk);
    color: #fff;
}

.ppdb-schedule__card {
    background: #fff;
    border: 1px solid var(--paper-line);
    border-radius: 14px;
    padding: 20px 23px;
    transition: transform .18s ease, box-shadow .18s ease;
}

.ppdb-schedule__card:hover {
    transform: translateX(3px);
    box-shadow: 0 10px 25px rgba(47,69,56,.07);
}

.ppdb-schedule__card h3 {
    color: var(--chalk);
    font-size: 18px;
    margin: 0 0 5px;
}

.ppdb-schedule__card p {
    color: var(--muted);
    font-size: 13.5px;
    margin: 0;
}

/* ---------- Summary ---------- */

.ppdb-schedule__summary {
    margin-top: 35px;
    background: var(--paper);
    border: 1px solid var(--paper-line);
    border-radius: 15px;
    padding: 25px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 25px;
}

.ppdb-schedule__summary h3 {
    color: var(--chalk);
    font-size: 21px;
    margin: 0 0 5px;
}

.ppdb-schedule__summary p {
    color: var(--muted);
    font-size: 13.5px;
    margin: 0;
}

.ppdb-schedule__badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--leaf-soft);
    color: var(--leaf);
    padding: 10px 16px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
}

/* ---------- Important Note ---------- */

.ppdb-schedule__note {
    margin-top: 16px;
    padding: 18px 20px;
    background: var(--amber-soft);
    border: 1px solid #E7D3A3;
    border-radius: 12px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.ppdb-schedule__note-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #fff;
    color: var(--amber);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-weight: 700;
    font-size: 13px;
}

.ppdb-schedule__note strong {
    display: block;
    color: #8A5C0A;
    font-size: 14px;
    margin-bottom: 3px;
}

.ppdb-schedule__note p {
    color: #705A28;
    font-size: 13px;
    margin: 0;
}

/* ---------- Responsive ---------- */

@media (max-width: 700px) {
    .ppdb-schedule__timeline::before {
        left: 20px;
        top: 24px;
        bottom: 24px;
    }

    .ppdb-schedule__item {
        grid-template-columns: 42px 1fr;
        gap: 15px;
        align-items: start;
    }

    .ppdb-schedule__date {
        grid-column: 2;
        grid-row: 1;
        text-align: left;
        order: 2;
        margin-bottom: -5px;
    }

    .ppdb-schedule__dot {
        grid-column: 1;
        grid-row: 1 / span 2;
    }

    .ppdb-schedule__card {
        grid-column: 2;
        grid-row: 2;
    }

    .ppdb-schedule__summary {
        align-items: flex-start;
        flex-direction: column;
    }

    .ppdb-schedule__badge {
        white-space: normal;
    }
}

@media (max-width: 520px) {
    .ppdb-schedule__header {
        padding: 48px 0;
    }

    .ppdb-schedule__section {
        padding: 45px 0;
    }

    .ppdb-schedule__card {
        padding: 18px;
    }

    .ppdb-schedule__summary {
        padding: 22px;
    }
}
</style>

<main class="ppdb-schedule">

    <!-- PAGE HEADER -->
    <section class="ppdb-schedule__header">
        <div class="container">

            <p class="ppdb-schedule__eyebrow">
                Penerimaan Peserta Didik Baru
            </p>

            <h1>Jadwal PPDB</h1>

            <p>
                Ikuti setiap tahap pendaftaran agar proses berjalan
                dengan lancar.
            </p>

        </div>
    </section>


    <!-- TIMELINE -->
    <section class="container ppdb-schedule__section">

        <div class="ppdb-schedule__intro">

            <p class="ppdb-schedule__intro-label">
                Alur pendaftaran
            </p>

            <h2>
                Dari formulir hingga daftar ulang.
            </h2>

            <p>
                Berikut tahapan penting PPDB TK Harapan Bunda
                tahun ajaran 2026/2027.
            </p>

        </div>


        <div class="ppdb-schedule__timeline">

            <!-- 01 -->
            <div class="ppdb-schedule__item">

                <div class="ppdb-schedule__date">
                    1 Jan -<br>
                    30 Apr 2026
                </div>

                <div class="ppdb-schedule__dot">
                    01
                </div>

                <div class="ppdb-schedule__card">

                    <h3>Pendaftaran Online</h3>

                    <p>
                        Pendaftaran online gelombang 1 dibuka.
                        Isi formulir calon siswa dan lengkapi data
                        yang diperlukan.
                    </p>

                </div>

            </div>


            <!-- 02 -->
            <div class="ppdb-schedule__item">

                <div class="ppdb-schedule__date">
                    2 - 10<br>
                    Mei 2026
                </div>

                <div class="ppdb-schedule__dot">
                    02
                </div>

                <div class="ppdb-schedule__card">

                    <h3>Observasi & Wawancara</h3>

                    <p>
                        Observasi anak dan wawancara bersama orang tua
                        sebagai bagian dari proses penerimaan.
                    </p>

                </div>

            </div>


            <!-- 03 -->
            <div class="ppdb-schedule__item">

                <div class="ppdb-schedule__date">
                    15 Mei<br>
                    2026
                </div>

                <div class="ppdb-schedule__dot">
                    03
                </div>

                <div class="ppdb-schedule__card">

                    <h3>Pengumuman Hasil</h3>

                    <p>
                        Hasil penerimaan peserta didik baru
                        diumumkan pada tanggal yang telah ditentukan.
                    </p>

                </div>

            </div>


            <!-- 04 -->
            <div class="ppdb-schedule__item">

                <div class="ppdb-schedule__date">
                    16 - 31<br>
                    Mei 2026
                </div>

                <div class="ppdb-schedule__dot">
                    04
                </div>

                <div class="ppdb-schedule__card">

                    <h3>Daftar Ulang</h3>

                    <p>
                        Peserta yang diterima melakukan daftar ulang
                        dan pengukuran seragam.
                    </p>

                </div>

            </div>

        </div>


        <!-- RINGKASAN -->
        <div class="ppdb-schedule__summary">

            <div>
                <h3>Catat tanggal pentingnya.</h3>

                <p>
                    Pastikan setiap tahap diikuti sesuai jadwal
                    agar proses pendaftaran tidak terlewat.
                </p>
            </div>

            <span class="ppdb-schedule__badge">
                Tahun Ajaran 2026/2027
            </span>

        </div>


        <!-- CATATAN -->
        <div class="ppdb-schedule__note">

            <span class="ppdb-schedule__note-icon">!</span>

            <div>
                <strong>Perhatian</strong>

                <p>
                    Jadwal di atas merupakan jadwal PPDB yang tercantum
                    pada informasi sekolah. Simpan halaman ini sebagai
                    pengingat setiap tahapan pendaftaran.
                </p>
            </div>

        </div>

    </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
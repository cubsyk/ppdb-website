<?php
$pageTitle = 'Program - TK Harapan Bunda';
$activePage = 'program';
require __DIR__ . '/includes/header.php';
?>

<style>
/* =========================================================
   PPDB PROGRAM
   Tema: Rapor & Kartu Pelajar
   ========================================================= */

.ppdb-program {
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
}

.ppdb-program h1,
.ppdb-program h2,
.ppdb-program h3 {
    font-family: 'Fraunces', Georgia, serif;
    font-weight: 600;
    letter-spacing: -0.015em;
}

.ppdb-program p {
    line-height: 1.7;
}

/* ---------- Header ---------- */

.ppdb-program__header {
    background: var(--paper);
    border-bottom: 1px solid var(--paper-line);
    padding: 58px 0;
    position: relative;
    overflow: hidden;
}

.ppdb-program__header::after {
    content: 'PROGRAM';
    position: absolute;
    right: -15px;
    bottom: -22px;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 105px;
    font-weight: 700;
    color: rgba(47,69,56,.045);
    pointer-events: none;
}

.ppdb-program__eyebrow {
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

.ppdb-program__eyebrow::before {
    content: '';
    width: 27px;
    height: 2px;
    background: var(--coral);
}

.ppdb-program__header h1 {
    color: var(--chalk);
    font-size: clamp(36px, 5vw, 52px);
    margin: 0 0 9px;
    position: relative;
    z-index: 1;
}

.ppdb-program__header p:last-child {
    color: var(--muted);
    margin: 0;
    font-size: 15px;
    position: relative;
    z-index: 1;
}

/* ---------- Main Section ---------- */

.ppdb-program__section {
    padding: 58px 0;
}

.ppdb-program__intro {
    max-width: 680px;
    margin-bottom: 27px;
}

.ppdb-program__intro-label {
    color: var(--coral);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin: 0 0 7px;
}

.ppdb-program__intro h2 {
    color: var(--chalk);
    font-size: 31px;
    margin: 0 0 9px;
}

.ppdb-program__intro p:last-child {
    color: var(--muted);
    font-size: 14.5px;
    margin: 0;
}

/* ---------- Program Cards ---------- */

.ppdb-program__cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.ppdb-program__card {
    background: #fff;
    border: 1px solid var(--paper-line);
    border-radius: 16px;
    padding: 27px;
    position: relative;
    overflow: hidden;
}

.ppdb-program__card::after {
    content: '';
    position: absolute;
    right: -32px;
    bottom: -40px;
    width: 120px;
    height: 120px;
    border: 1px solid var(--paper-line);
    border-radius: 50%;
}

.ppdb-program__card--a {
    border-top: 4px solid var(--leaf);
}

.ppdb-program__card--b {
    border-top: 4px solid var(--amber);
}

.ppdb-program__card--daycare {
    border-top: 4px solid var(--coral);
}

.ppdb-program__card-number {
    font-family: 'Fraunces', Georgia, serif;
    color: #DCD4C5;
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 17px;
}

.ppdb-program__card h3 {
    color: var(--chalk);
    font-size: 25px;
    margin: 0 0 5px;
    position: relative;
    z-index: 1;
}

.ppdb-program__age {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 6px;
    background: var(--leaf-soft);
    color: var(--leaf);
    font-size: 11.5px;
    font-weight: 700;
    margin-bottom: 17px;
}

.ppdb-program__card--b .ppdb-program__age {
    background: var(--amber-soft);
    color: #8A5C0A;
}

.ppdb-program__card--daycare .ppdb-program__age {
    background: var(--berry-soft);
    color: var(--berry);
}

.ppdb-program__card p:last-child {
    color: var(--muted);
    font-size: 14px;
    margin: 0;
    position: relative;
    z-index: 1;
}

/* ---------- Schedule ---------- */

.ppdb-program__schedule {
    background: var(--paper);
    border-top: 1px solid var(--paper-line);
    border-bottom: 1px solid var(--paper-line);
    padding: 58px 0;
}

.ppdb-program__schedule-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.ppdb-program__schedule-card {
    background: #fff;
    border: 1px solid var(--paper-line);
    border-radius: 15px;
    padding: 28px;
}

.ppdb-program__schedule-heading {
    display: flex;
    align-items: center;
    gap: 13px;
    margin-bottom: 21px;
}

.ppdb-program__schedule-icon {
    width: 43px;
    height: 43px;
    border-radius: 10px;
    background: var(--leaf-soft);
    color: var(--leaf);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.ppdb-program__schedule-icon--amber {
    background: var(--amber-soft);
    color: var(--amber);
}

.ppdb-program__schedule-icon svg {
    width: 21px;
    height: 21px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.ppdb-program__schedule-heading h2 {
    color: var(--chalk);
    font-size: 21px;
    margin: 0;
}

.ppdb-program__activities {
    list-style: none;
    margin: 0;
    padding: 0;
}

.ppdb-program__activities li {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 11px 0;
    border-top: 1px solid var(--paper-line);
    color: var(--muted);
    font-size: 14px;
}

.ppdb-program__activities li:first-child {
    border-top: none;
    padding-top: 0;
}

.ppdb-program__activities li:last-child {
    padding-bottom: 0;
}

.ppdb-program__check {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: var(--leaf-soft);
    color: var(--leaf);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 11px;
    font-weight: 700;
}

/* ---------- Time Table ---------- */

.ppdb-program__times {
    width: 100%;
    border-collapse: collapse;
}

.ppdb-program__times tr {
    border-top: 1px solid var(--paper-line);
}

.ppdb-program__times tr:first-child {
    border-top: none;
}

.ppdb-program__times td {
    padding: 13px 0;
    font-size: 14px;
}

.ppdb-program__times td:first-child {
    color: var(--muted);
}

.ppdb-program__times td:last-child {
    color: var(--chalk);
    font-weight: 700;
    text-align: right;
}

/* ---------- Closing ---------- */

.ppdb-program__closing {
    margin-top: 16px;
    background: var(--chalk);
    border-radius: 16px;
    padding: 29px 31px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 25px;
}

.ppdb-program__closing h2 {
    color: #F3F0E6;
    font-size: 24px;
    margin: 0 0 5px;
}

.ppdb-program__closing p {
    color: #C9D3C9;
    font-size: 14px;
    margin: 0;
}

.ppdb-program__closing-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--amber-soft);
    color: #8A5C0A;
    padding: 10px 15px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    transform: rotate(-2deg);
    white-space: nowrap;
}

/* ---------- Responsive ---------- */

@media (max-width: 800px) {
    .ppdb-program__cards,
    .ppdb-program__schedule-grid {
        grid-template-columns: 1fr;
    }

    .ppdb-program__header {
        padding: 48px 0;
    }
}

@media (max-width: 520px) {
    .ppdb-program__section,
    .ppdb-program__schedule {
        padding: 45px 0;
    }

    .ppdb-program__card,
    .ppdb-program__schedule-card {
        padding: 23px;
    }

    .ppdb-program__closing {
        padding: 24px;
        align-items: flex-start;
        flex-direction: column;
    }

    .ppdb-program__closing-badge {
        white-space: normal;
    }
}
</style>

<main class="ppdb-program">

    <!-- PAGE HEADER -->
    <section class="ppdb-program__header">
        <div class="container">

            <p class="ppdb-program__eyebrow">
                Program Pembelajaran
            </p>

            <h1>Program Belajar</h1>

            <p>
                Pilihan program yang disusun sesuai usia dan kebutuhan
                perkembangan anak.
            </p>

        </div>
    </section>


    <!-- PROGRAM UTAMA -->
    <section class="container ppdb-program__section">

        <div class="ppdb-program__intro">

            <p class="ppdb-program__intro-label">
                Pilihan program
            </p>

            <h2>
                Setiap usia punya cara belajar yang berbeda.
            </h2>

            <p>
                Kegiatan pembelajaran disesuaikan dengan tahap perkembangan
                anak agar proses belajar terasa nyaman dan menyenangkan.
            </p>

        </div>


        <div class="ppdb-program__cards">

            <!-- KELOMPOK A -->
            <article class="ppdb-program__card ppdb-program__card--a">

                <div class="ppdb-program__card-number">01</div>

                <h3>Kelompok A</h3>

                <span class="ppdb-program__age">
                    Usia 4-5 tahun
                </span>

                <p>
                    Fokus pada adaptasi sekolah, kemandirian, motorik,
                    bahasa, dan kemampuan bersosialisasi.
                </p>

            </article>


            <!-- KELOMPOK B -->
            <article class="ppdb-program__card ppdb-program__card--b">

                <div class="ppdb-program__card-number">02</div>

                <h3>Kelompok B</h3>

                <span class="ppdb-program__age">
                    Usia 5-6 tahun
                </span>

                <p>
                    Fokus pada kesiapan masuk SD, literasi awal, numerasi
                    awal, keberanian tampil, dan tanggung jawab.
                </p>

            </article>


            <!-- DAYCARE -->
            <article class="ppdb-program__card ppdb-program__card--daycare">

                <div class="ppdb-program__card-number">03</div>

                <h3>Daycare Ceria</h3>

                <span class="ppdb-program__age">
                    Program tambahan
                </span>

                <p>
                    Pendampingan setelah jam belajar dengan kegiatan ringan,
                    istirahat, makan, dan bermain terarah.
                </p>

            </article>

        </div>

    </section>


    <!-- KEGIATAN & JAM BELAJAR -->
    <section class="ppdb-program__schedule">

        <div class="container">

            <div class="ppdb-program__schedule-grid">

                <!-- KEGIATAN HARIAN -->
                <article class="ppdb-program__schedule-card">

                    <div class="ppdb-program__schedule-heading">

                        <div class="ppdb-program__schedule-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M4 5h16v15H4z"/>
                                <path d="M8 3v4"/>
                                <path d="M16 3v4"/>
                                <path d="M4 10h16"/>
                                <path d="m8 14 2 2 4-4"/>
                            </svg>
                        </div>

                        <h2>Kegiatan Harian</h2>

                    </div>

                    <ul class="ppdb-program__activities">

                        <li>
                            <span class="ppdb-program__check">01</span>
                            Doa dan pembiasaan pagi
                        </li>

                        <li>
                            <span class="ppdb-program__check">02</span>
                            Kegiatan motorik kasar dan halus
                        </li>

                        <li>
                            <span class="ppdb-program__check">03</span>
                            Belajar tema mingguan
                        </li>

                        <li>
                            <span class="ppdb-program__check">04</span>
                            Makan bersama
                        </li>

                        <li>
                            <span class="ppdb-program__check">05</span>
                            Seni, musik, dan bercerita
                        </li>

                    </ul>

                </article>


                <!-- JAM BELAJAR -->
                <article class="ppdb-program__schedule-card">

                    <div class="ppdb-program__schedule-heading">

                        <div class="ppdb-program__schedule-icon ppdb-program__schedule-icon--amber">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2"/>
                            </svg>
                        </div>

                        <h2>Jam Belajar</h2>

                    </div>

                    <table class="ppdb-program__times">

                        <tr>
                            <td>Senin - Kamis</td>
                            <td>07.30 - 10.30</td>
                        </tr>

                        <tr>
                            <td>Jumat</td>
                            <td>07.30 - 10.00</td>
                        </tr>

                        <tr>
                            <td>Daycare</td>
                            <td>10.30 - 15.00</td>
                        </tr>

                    </table>

                </article>

            </div>


            <!-- CLOSING -->
            <div class="ppdb-program__closing">

                <div>
                    <h2>Belajar sambil bermain, tumbuh setiap hari.</h2>

                    <p>
                        Kegiatan dirancang agar anak dapat belajar,
                        bereksplorasi, dan membangun kebiasaan positif.
                    </p>
                </div>

                <span class="ppdb-program__closing-badge">
                    Program 2026/2027
                </span>

            </div>

        </div>

    </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php
$pageTitle = 'kelompok - TK Harapan Bunda';
$activePage = 'kelompok';
require __DIR__ . '/includes/header.php';
?>

<style>
/* =========================================================
   PPDB kelompok
   Tema: Rapor & Kartu Pelajar
   ========================================================= */

.ppdb-kelompok {
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

.ppdb-kelompok h1,
.ppdb-kelompok h2,
.ppdb-kelompok h3 {
    font-family: 'Fraunces', Georgia, serif;
    font-weight: 600;
    letter-spacing: -0.015em;
}

.ppdb-kelompok p {
    line-height: 1.7;
}

/* ---------- Header ---------- */

.ppdb-kelompok__header {
    background: var(--paper);
    border-bottom: 1px solid var(--paper-line);
    padding: 58px 0;
    position: relative;
    overflow: hidden;
}

.ppdb-kelompok__header::after {
    content: 'kelompok';
    position: absolute;
    right: -15px;
    bottom: -22px;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 105px;
    font-weight: 700;
    color: rgba(47,69,56,.045);
    pointer-events: none;
}

.ppdb-kelompok__eyebrow {
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

.ppdb-kelompok__eyebrow::before {
    content: '';
    width: 27px;
    height: 2px;
    background: var(--coral);
}

.ppdb-kelompok__header h1 {
    color: var(--chalk);
    font-size: clamp(36px, 5vw, 52px);
    margin: 0 0 9px;
    position: relative;
    z-index: 1;
}

.ppdb-kelompok__header p:last-child {
    color: var(--muted);
    margin: 0;
    font-size: 15px;
    position: relative;
    z-index: 1;
}

/* ---------- Main Section ---------- */

.ppdb-kelompok__section {
    padding: 58px 0;
}

.ppdb-kelompok__intro {
    max-width: 680px;
    margin-bottom: 27px;
}

.ppdb-kelompok__intro-label {
    color: var(--coral);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin: 0 0 7px;
}

.ppdb-kelompok__intro h2 {
    color: var(--chalk);
    font-size: 31px;
    margin: 0 0 9px;
}

.ppdb-kelompok__intro p:last-child {
    color: var(--muted);
    font-size: 14.5px;
    margin: 0;
}

/* ---------- kelompok Cards ---------- */

.ppdb-kelompok__cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.ppdb-kelompok__card {
    background: #fff;
    border: 1px solid var(--paper-line);
    border-radius: 16px;
    padding: 27px;
    position: relative;
    overflow: hidden;
}

.ppdb-kelompok__card::after {
    content: '';
    position: absolute;
    right: -32px;
    bottom: -40px;
    width: 120px;
    height: 120px;
    border: 1px solid var(--paper-line);
    border-radius: 50%;
}

.ppdb-kelompok__card--a {
    border-top: 4px solid var(--leaf);
}

.ppdb-kelompok__card--b {
    border-top: 4px solid var(--amber);
}

.ppdb-kelompok__card-number {
    font-family: 'Fraunces', Georgia, serif;
    color: #DCD4C5;
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 17px;
}

.ppdb-kelompok__card h3 {
    color: var(--chalk);
    font-size: 25px;
    margin: 0 0 5px;
    position: relative;
    z-index: 1;
}

.ppdb-kelompok__age {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 6px;
    background: var(--leaf-soft);
    color: var(--leaf);
    font-size: 11.5px;
    font-weight: 700;
    margin-bottom: 17px;
}

.ppdb-kelompok__card--b .ppdb-kelompok__age {
    background: var(--amber-soft);
    color: #8A5C0A;
}

.ppdb-kelompok__card p:last-child {
    color: var(--muted);
    font-size: 14px;
    margin: 0;
    position: relative;
    z-index: 1;
}

/* ---------- Schedule ---------- */

.ppdb-kelompok__schedule {
    background: var(--paper);
    border-top: 1px solid var(--paper-line);
    border-bottom: 1px solid var(--paper-line);
    padding: 58px 0;
}

.ppdb-kelompok__schedule-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.ppdb-kelompok__schedule-card {
    background: #fff;
    border: 1px solid var(--paper-line);
    border-radius: 15px;
    padding: 28px;
}

.ppdb-kelompok__schedule-heading {
    display: flex;
    align-items: center;
    gap: 13px;
    margin-bottom: 21px;
}

.ppdb-kelompok__schedule-icon {
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

.ppdb-kelompok__schedule-icon--amber {
    background: var(--amber-soft);
    color: var(--amber);
}

.ppdb-kelompok__schedule-icon svg {
    width: 21px;
    height: 21px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.ppdb-kelompok__schedule-heading h2 {
    color: var(--chalk);
    font-size: 21px;
    margin: 0;
}

.ppdb-kelompok__activities {
    list-style: none;
    margin: 0;
    padding: 0;
}

.ppdb-kelompok__activities li {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 11px 0;
    border-top: 1px solid var(--paper-line);
    color: var(--muted);
    font-size: 14px;
}

.ppdb-kelompok__activities li:first-child {
    border-top: none;
    padding-top: 0;
}

.ppdb-kelompok__activities li:last-child {
    padding-bottom: 0;
}

.ppdb-kelompok__check {
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

.ppdb-kelompok__times {
    width: 100%;
    border-collapse: collapse;
}

.ppdb-kelompok__times tr {
    border-top: 1px solid var(--paper-line);
}

.ppdb-kelompok__times tr:first-child {
    border-top: none;
}

.ppdb-kelompok__times td {
    padding: 13px 0;
    font-size: 14px;
}

.ppdb-kelompok__times td:first-child {
    color: var(--muted);
}

.ppdb-kelompok__times td:last-child {
    color: var(--chalk);
    font-weight: 700;
    text-align: right;
}

/* ---------- Closing ---------- */

.ppdb-kelompok__closing {
    margin-top: 16px;
    background: var(--chalk);
    border-radius: 16px;
    padding: 29px 31px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 25px;
}

.ppdb-kelompok__closing h2 {
    color: #F3F0E6;
    font-size: 24px;
    margin: 0 0 5px;
}

.ppdb-kelompok__closing p {
    color: #C9D3C9;
    font-size: 14px;
    margin: 0;
}

.ppdb-kelompok__closing-badge {
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
    .ppdb-kelompok__cards,
    .ppdb-kelompok__schedule-grid {
        grid-template-columns: 1fr;
    }

    .ppdb-kelompok__header {
        padding: 48px 0;
    }
}

@media (max-width: 520px) {
    .ppdb-kelompok__section,
    .ppdb-kelompok__schedule {
        padding: 45px 0;
    }

    .ppdb-kelompok__card,
    .ppdb-kelompok__schedule-card {
        padding: 23px;
    }

    .ppdb-kelompok__closing {
        padding: 24px;
        align-items: flex-start;
        flex-direction: column;
    }

    .ppdb-kelompok__closing-badge {
        white-space: normal;
    }
}
</style>

<main class="ppdb-kelompok">

    <!-- PAGE HEADER -->
    <section class="ppdb-kelompok__header">
        <div class="container">

            <p class="ppdb-kelompok__eyebrow">
                kelompok Pembelajaran
            </p>

            <h1>kelompok Belajar</h1>

            <p>
                Pilihan kelompok yang disusun sesuai usia dan kebutuhan
                perkembangan anak.
            </p>

        </div>
    </section>


    <!-- kelompok UTAMA -->
    <section class="container ppdb-kelompok__section">

        <div class="ppdb-kelompok__intro">

            <p class="ppdb-kelompok__intro-label">
                Pilihan kelompok
            </p>

            <h2>
                Setiap usia punya cara belajar yang berbeda.
            </h2>

            <p>
                Kegiatan pembelajaran disesuaikan dengan tahap perkembangan
                anak agar proses belajar terasa nyaman dan menyenangkan.
            </p>

        </div>


        <div class="ppdb-kelompok__cards">

            <!-- KELOMPOK A -->
            <article class="ppdb-kelompok__card ppdb-kelompok__card--a">

                <div class="ppdb-kelompok__card-number">01</div>

                <h3>Kelompok A</h3>

                <span class="ppdb-kelompok__age">
                    Usia 4-5 tahun
                </span>

                <p>
                    Fokus pada adaptasi sekolah, kemandirian, motorik,
                    bahasa, dan kemampuan bersosialisasi.
                </p>

            </article>


            <!-- KELOMPOK B -->
            <article class="ppdb-kelompok__card ppdb-kelompok__card--b">

                <div class="ppdb-kelompok__card-number">02</div>

                <h3>Kelompok B</h3>

                <span class="ppdb-kelompok__age">
                    Usia 5-6 tahun
                </span>

                <p>
                    Fokus pada kesiapan masuk SD, literasi awal, numerasi
                    awal, keberanian tampil, dan tanggung jawab.
                </p>

            </article>

        </div>

    </section>


    <!-- KEGIATAN & JAM BELAJAR -->
    <section class="ppdb-kelompok__schedule">

        <div class="container">

            <div class="ppdb-kelompok__schedule-grid">

                <!-- KEGIATAN HARIAN -->
                <article class="ppdb-kelompok__schedule-card">

                    <div class="ppdb-kelompok__schedule-heading">

                        <div class="ppdb-kelompok__schedule-icon">
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

                    <ul class="ppdb-kelompok__activities">

                        <li>
                            <span class="ppdb-kelompok__check">01</span>
                            Doa dan pembiasaan pagi
                        </li>

                        <li>
                            <span class="ppdb-kelompok__check">02</span>
                            Kegiatan motorik kasar dan halus
                        </li>

                        <li>
                            <span class="ppdb-kelompok__check">03</span>
                            Belajar tema mingguan
                        </li>

                        <li>
                            <span class="ppdb-kelompok__check">04</span>
                            Makan bersama
                        </li>

                        <li>
                            <span class="ppdb-kelompok__check">05</span>
                            Seni, musik, dan bercerita
                        </li>

                    </ul>

                </article>


                <!-- JAM BELAJAR -->
                <article class="ppdb-kelompok__schedule-card">

                    <div class="ppdb-kelompok__schedule-heading">

                        <div class="ppdb-kelompok__schedule-icon ppdb-kelompok__schedule-icon--amber">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2"/>
                            </svg>
                        </div>

                        <h2>Jam Belajar</h2>

                    </div>

                    <table class="ppdb-kelompok__times">

                        <tr>
                            <td>Senin - Kamis</td>
                            <td>07.30 - 10.30</td>
                        </tr>

                        <tr>
                            <td>Jumat</td>
                            <td>07.30 - 10.00</td>
                        </tr>

                    </table>

                </article>

            </div>


            <!-- CLOSING -->
            <div class="ppdb-kelompok__closing">

                <div>
                    <h2>Belajar sambil bermain, tumbuh setiap hari.</h2>

                    <p>
                        Kegiatan dirancang agar anak dapat belajar,
                        bereksplorasi, dan membangun kebiasaan positif.
                    </p>
                </div>

                <span class="ppdb-kelompok__closing-badge">
                    kelompok 2026/2027
                </span>

            </div>

        </div>

    </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php
$pageTitle = 'Biaya PPDB - TK Harapan Bunda';
$activePage = 'biaya';
require __DIR__ . '/includes/header.php';
?>

<style>
/* =========================================================
   PPDB FEE
   Tema: Rapor & Kartu Pelajar
   ========================================================= */

.ppdb-fee {
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

.ppdb-fee h1,
.ppdb-fee h2,
.ppdb-fee h3 {
    font-family: 'Fraunces', Georgia, serif;
    font-weight: 600;
    letter-spacing: -0.015em;
}

.ppdb-fee p {
    line-height: 1.7;
}

/* ---------- Header ---------- */

.ppdb-fee__header {
    background: var(--paper);
    border-bottom: 1px solid var(--paper-line);
    padding: 58px 0;
    position: relative;
    overflow: hidden;
}

.ppdb-fee__header::after {
    content: 'BIAYA';
    position: absolute;
    right: -5px;
    bottom: -24px;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 105px;
    font-weight: 700;
    color: rgba(47,69,56,.045);
    pointer-events: none;
}

.ppdb-fee__eyebrow {
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

.ppdb-fee__eyebrow::before {
    content: '';
    width: 27px;
    height: 2px;
    background: var(--coral);
}

.ppdb-fee__header h1 {
    color: var(--chalk);
    font-size: clamp(36px, 5vw, 52px);
    margin: 0 0 9px;
    position: relative;
    z-index: 1;
}

.ppdb-fee__header p:last-child {
    color: var(--muted);
    margin: 0;
    font-size: 15px;
    position: relative;
    z-index: 1;
}

/* ---------- Main ---------- */

.ppdb-fee__section {
    padding: 58px 0;
}

.ppdb-fee__intro {
    max-width: 680px;
    margin-bottom: 27px;
}

.ppdb-fee__intro-label {
    color: var(--coral);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin: 0 0 7px;
}

.ppdb-fee__intro h2 {
    color: var(--chalk);
    font-size: 31px;
    margin: 0 0 9px;
}

.ppdb-fee__intro p:last-child {
    color: var(--muted);
    font-size: 14.5px;
    margin: 0;
}

/* ---------- Fee Layout ---------- */

.ppdb-fee__layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 280px;
    gap: 16px;
    align-items: start;
}

/* ---------- Table ---------- */

.ppdb-fee__table-wrap {
    background: #fff;
    border: 1px solid var(--paper-line);
    border-radius: 15px;
    overflow: hidden;
}

.ppdb-fee__table {
    width: 100%;
    border-collapse: collapse;
}

.ppdb-fee__table thead {
    background: var(--chalk);
    color: #F3F0E6;
}

.ppdb-fee__table th {
    padding: 16px 19px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
}

.ppdb-fee__table td {
    padding: 17px 19px;
    border-top: 1px solid var(--paper-line);
    font-size: 14px;
    vertical-align: middle;
}

.ppdb-fee__table td:first-child {
    color: var(--chalk);
    font-weight: 700;
}

.ppdb-fee__table td:nth-child(2) {
    color: var(--coral);
    font-family: 'Fraunces', Georgia, serif;
    font-size: 17px;
    font-weight: 700;
    white-space: nowrap;
}

.ppdb-fee__table td:last-child {
    color: var(--muted);
}

.ppdb-fee__table tbody tr:hover {
    background: #FCFAF5;
}

.ppdb-fee__free {
    display: inline-flex;
    align-items: center;
    background: var(--leaf-soft);
    color: var(--leaf);
    padding: 5px 9px;
    border-radius: 6px;
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 12px;
    font-weight: 700;
}

/* ---------- Summary Card ---------- */

.ppdb-fee__summary {
    background: var(--chalk);
    color: #F3F0E6;
    border-radius: 15px;
    padding: 24px;
    position: relative;
    overflow: hidden;
}

.ppdb-fee__summary::after {
    content: 'Rp';
    position: absolute;
    right: -7px;
    bottom: -35px;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 105px;
    font-weight: 700;
    color: rgba(255,255,255,.045);
}

.ppdb-fee__summary-label {
    color: #C9D3C9;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin: 0 0 7px;
}

.ppdb-fee__summary h3 {
    font-size: 22px;
    margin: 0 0 20px;
}

.ppdb-fee__amount {
    position: relative;
    z-index: 1;
    padding-bottom: 18px;
    border-bottom: 1px solid rgba(255,255,255,.12);
}

.ppdb-fee__amount strong {
    display: block;
    color: #fff;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 31px;
    line-height: 1.1;
}

.ppdb-fee__amount span {
    color: #BFCBBF;
    font-size: 11.5px;
}

.ppdb-fee__summary-list {
    position: relative;
    z-index: 1;
    list-style: none;
    padding: 0;
    margin: 17px 0 0;
}

.ppdb-fee__summary-list li {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 9px 0;
    font-size: 12.5px;
}

.ppdb-fee__summary-list span {
    color: #BFCBBF;
}

.ppdb-fee__summary-list strong {
    color: #fff;
}

/* ---------- Note ---------- */

.ppdb-fee__note {
    margin-top: 16px;
    background: var(--amber-soft);
    border: 1px solid #E7D3A3;
    border-radius: 12px;
    padding: 17px 19px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.ppdb-fee__note-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #fff;
    color: var(--amber);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 13px;
    font-weight: 700;
}

.ppdb-fee__note strong {
    display: block;
    color: #8A5C0A;
    font-size: 14px;
    margin-bottom: 3px;
}

.ppdb-fee__note p {
    color: #705A28;
    font-size: 13px;
    margin: 0;
}

/* ---------- Bottom CTA ---------- */

.ppdb-fee__cta {
    margin-top: 16px;
    background: var(--paper);
    border: 1px solid var(--paper-line);
    border-radius: 15px;
    padding: 25px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.ppdb-fee__cta h3 {
    color: var(--chalk);
    font-size: 21px;
    margin: 0 0 5px;
}

.ppdb-fee__cta p {
    color: var(--muted);
    font-size: 13.5px;
    margin: 0;
}

.ppdb-fee__cta-badge {
    background: var(--leaf-soft);
    color: var(--leaf);
    border-radius: 999px;
    padding: 10px 15px;
    font-size: 12.5px;
    font-weight: 700;
    white-space: nowrap;
}

/* ---------- Responsive ---------- */

@media (max-width: 800px) {
    .ppdb-fee__layout {
        grid-template-columns: 1fr;
    }

    .ppdb-fee__summary {
        order: -1;
    }
}

@media (max-width: 620px) {
    .ppdb-fee__header {
        padding: 48px 0;
    }

    .ppdb-fee__section {
        padding: 45px 0;
    }

    .ppdb-fee__table-wrap {
        overflow-x: auto;
    }

    .ppdb-fee__table {
        min-width: 650px;
    }

    .ppdb-fee__cta {
        align-items: flex-start;
        flex-direction: column;
    }

    .ppdb-fee__cta-badge {
        white-space: normal;
    }
}
</style>

<main class="ppdb-fee">

    <!-- PAGE HEADER -->
    <section class="ppdb-fee__header">
        <div class="container">

            <p class="ppdb-fee__eyebrow">
                Informasi PPDB
            </p>

            <h1>Rincian Biaya</h1>

            <p>
                Informasi biaya masuk, perlengkapan, dan pembayaran bulanan.
            </p>

        </div>
    </section>


    <!-- RINCIAN BIAYA -->
    <section class="container ppdb-fee__section">

        <div class="ppdb-fee__intro">

            <p class="ppdb-fee__intro-label">
                Transparansi biaya
            </p>

            <h2>
                Semua biaya, dijelaskan dengan sederhana.
            </h2>

            <p>
                Berikut rincian biaya PPDB TK Harapan Bunda
                untuk membantu orang tua mempersiapkan kebutuhan
                pendaftaran.
            </p>

        </div>


        <div class="ppdb-fee__layout">

            <!-- TABLE -->
            <div class="ppdb-fee__table-wrap">

                <table class="ppdb-fee__table">

                    <thead>
                        <tr>
                            <th>Komponen</th>
                            <th>Nominal</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>Formulir</td>
                            <td>
                                <span class="ppdb-fee__free">Gratis</span>
                            </td>
                            <td>Pendaftaran melalui website</td>
                        </tr>

                        <tr>
                            <td>Uang pangkal</td>
                            <td>Rp1.500.000</td>
                            <td>Dibayar saat daftar ulang</td>
                        </tr>

                        <tr>
                            <td>SPP bulanan</td>
                            <td>Rp250.000</td>
                            <td>Dibayar setiap bulan</td>
                        </tr>

                        <tr>
                            <td>Seragam dan perlengkapan</td>
                            <td>Rp750.000</td>
                            <td>Paket awal peserta didik</td>
                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- SUMMARY -->
            <aside class="ppdb-fee__summary">

                <p class="ppdb-fee__summary-label">
                    Ringkasan
                </p>

                <h3>Biaya awal</h3>

                <div class="ppdb-fee__amount">
                    <strong>Rp2.250.000</strong>
                    <span>Uang pangkal + seragam & perlengkapan</span>
                </div>

                <ul class="ppdb-fee__summary-list">

                    <li>
                        <span>Formulir</span>
                        <strong>Gratis</strong>
                    </li>

                    <li>
                        <span>Uang pangkal</span>
                        <strong>Rp1.500.000</strong>
                    </li>

                    <li>
                        <span>Seragam</span>
                        <strong>Rp750.000</strong>
                    </li>

                    <li>
                        <span>SPP / bulan</span>
                        <strong>Rp250.000</strong>
                    </li>

                </ul>

            </aside>

        </div>


        <!-- CATATAN -->
        <div class="ppdb-fee__note">

            <span class="ppdb-fee__note-icon">!</span>

            <div>

                <strong>Perhatian</strong>

                <p>
                    Biaya dapat berubah sesuai kebijakan sekolah.
                    Hubungi panitia PPDB untuk konfirmasi terbaru.
                </p>

            </div>

        </div>


        <!-- CLOSING -->
        <div class="ppdb-fee__cta">

            <div>
                <h3>Sudah siap dengan proses pendaftaran?</h3>

                <p>
                    Formulir pendaftaran dapat diisi secara online
                    melalui website PPDB TK Harapan Bunda.
                </p>
            </div>

            <span class="ppdb-fee__cta-badge">
                PPDB 2026/2027
            </span>

        </div>

    </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
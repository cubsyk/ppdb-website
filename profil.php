<?php
$pageTitle = 'Profil - TK Harapan Bunda';
$activePage = 'profil';
require __DIR__ . '/includes/header.php';
?>

<style>
/* =========================================================
   PPDB PROFILE
   Tema: Rapor & Kartu Pelajar
   ========================================================= */

.ppdb-profile {
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
    --berry-soft: #F8E7E4;

    font-family: 'Inter', system-ui, sans-serif;
    color: var(--ink);
}

.ppdb-profile h1,
.ppdb-profile h2,
.ppdb-profile h3 {
    font-family: 'Fraunces', Georgia, serif;
    font-weight: 600;
    letter-spacing: -0.015em;
}

.ppdb-profile p {
    line-height: 1.7;
}

/* ---------- Page Header ---------- */

.ppdb-profile__header {
    background: var(--paper);
    border-bottom: 1px solid var(--paper-line);
    padding: 58px 0;
    position: relative;
    overflow: hidden;
}

.ppdb-profile__header::after {
    content: 'PROFILE';
    position: absolute;
    right: -10px;
    bottom: -20px;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 110px;
    font-weight: 700;
    color: rgba(47,69,56,.045);
    pointer-events: none;
}

.ppdb-profile__eyebrow {
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

.ppdb-profile__eyebrow::before {
    content: '';
    width: 27px;
    height: 2px;
    background: var(--coral);
}

.ppdb-profile__header h1 {
    color: var(--chalk);
    font-size: clamp(36px, 5vw, 52px);
    margin: 0 0 9px;
    position: relative;
    z-index: 1;
}

.ppdb-profile__header p:last-child {
    color: var(--muted);
    margin: 0;
    font-size: 15px;
    position: relative;
    z-index: 1;
}

/* ---------- Main ---------- */

.ppdb-profile__section {
    padding: 58px 0;
}

.ppdb-profile__intro {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(300px, .8fr);
    gap: 18px;
    align-items: stretch;
}

/* ---------- About ---------- */

.ppdb-profile__about {
    background: #fff;
    border: 1px solid var(--paper-line);
    border-radius: 16px;
    padding: 30px;
}

.ppdb-profile__about-label {
    color: var(--coral);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin: 0 0 8px;
}

.ppdb-profile__about h2 {
    color: var(--chalk);
    font-size: 29px;
    margin: 0 0 17px;
}

.ppdb-profile__about p {
    color: var(--muted);
    font-size: 14.5px;
    margin: 0 0 14px;
}

.ppdb-profile__about p:last-child {
    margin-bottom: 0;
}

/* ---------- Identity Card ---------- */

.ppdb-profile__identity {
    background: var(--chalk);
    color: #F3F0E6;
    border-radius: 16px;
    padding: 28px;
    position: relative;
    overflow: hidden;
}

.ppdb-profile__identity::after {
    content: 'TK';
    position: absolute;
    right: -10px;
    bottom: -28px;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 130px;
    font-weight: 700;
    color: rgba(255,255,255,.045);
}

.ppdb-profile__identity-label {
    color: #C9D3C9;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin: 0 0 5px;
}

.ppdb-profile__identity h3 {
    font-size: 25px;
    margin: 0 0 22px;
}

.ppdb-profile__details {
    position: relative;
    z-index: 1;
}

.ppdb-profile__detail {
    display: grid;
    grid-template-columns: 75px 1fr;
    gap: 13px;
    padding: 11px 0;
    border-top: 1px solid rgba(255,255,255,.12);
}

.ppdb-profile__detail:last-child {
    border-bottom: 1px solid rgba(255,255,255,.12);
}

.ppdb-profile__detail span {
    color: #BFCBBF;
    font-size: 12.5px;
}

.ppdb-profile__detail strong {
    color: #fff;
    font-size: 13px;
    font-weight: 500;
    overflow-wrap: anywhere;
}

/* ---------- Vision Mission ---------- */

.ppdb-profile__values {
    background: var(--paper);
    border-top: 1px solid var(--paper-line);
    border-bottom: 1px solid var(--paper-line);
    padding: 58px 0;
}

.ppdb-profile__section-title {
    margin-bottom: 25px;
}

.ppdb-profile__section-title p {
    color: var(--coral);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin: 0 0 7px;
}

.ppdb-profile__section-title h2 {
    color: var(--chalk);
    font-size: 32px;
    margin: 0;
}

.ppdb-profile__values-grid {
    display: grid;
    grid-template-columns: .75fr 1.25fr;
    gap: 16px;
}

.ppdb-profile__value-card {
    background: #fff;
    border: 1px solid var(--paper-line);
    border-radius: 15px;
    padding: 28px;
}

.ppdb-profile__value-card--vision {
    background: var(--chalk);
    color: #F3F0E6;
    border-color: var(--chalk);
}

.ppdb-profile__value-card--vision h3 {
    color: #F3F0E6;
}

.ppdb-profile__value-card--vision p {
    color: #D4DED4;
}

.ppdb-profile__value-card--mission h3 {
    color: var(--chalk);
}

.ppdb-profile__value-card h3 {
    font-size: 23px;
    margin: 0 0 12px;
}

.ppdb-profile__value-card p {
    font-size: 14.5px;
    margin: 0;
}

.ppdb-profile__mission-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.ppdb-profile__mission-list li {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 10px 0;
    border-top: 1px solid var(--paper-line);
    color: var(--muted);
    font-size: 14px;
    line-height: 1.6;
}

.ppdb-profile__mission-list li:first-child {
    padding-top: 0;
    border-top: none;
}

.ppdb-profile__mission-list li:last-child {
    padding-bottom: 0;
}

.ppdb-profile__mission-dot {
    width: 23px;
    height: 23px;
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

/* ---------- Closing Card ---------- */

.ppdb-profile__closing {
    margin-top: 16px;
    border: 1px solid var(--paper-line);
    background: #fff;
    border-radius: 15px;
    padding: 27px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 25px;
}

.ppdb-profile__closing h3 {
    color: var(--chalk);
    font-size: 22px;
    margin: 0 0 5px;
}

.ppdb-profile__closing p {
    color: var(--muted);
    font-size: 14px;
    margin: 0;
}

.ppdb-profile__closing-mark {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: var(--amber-soft);
    color: var(--amber);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.ppdb-profile__closing-mark svg {
    width: 22px;
    height: 22px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* ---------- Responsive ---------- */

@media (max-width: 800px) {
    .ppdb-profile__intro,
    .ppdb-profile__values-grid {
        grid-template-columns: 1fr;
    }

    .ppdb-profile__header {
        padding: 48px 0;
    }
}

@media (max-width: 520px) {
    .ppdb-profile__section,
    .ppdb-profile__values {
        padding: 45px 0;
    }

    .ppdb-profile__about,
    .ppdb-profile__identity,
    .ppdb-profile__value-card {
        padding: 23px;
    }

    .ppdb-profile__detail {
        grid-template-columns: 65px 1fr;
    }

    .ppdb-profile__closing {
        padding: 23px;
        align-items: flex-start;
    }
}
</style>

<main class="ppdb-profile">

    <!-- PAGE HEADER -->
    <section class="ppdb-profile__header">
        <div class="container">
            <p class="ppdb-profile__eyebrow">
                Tentang Sekolah
            </p>

            <h1>Profil TK Harapan Bunda</h1>

            <p>
                Mengenal lebih dekat lingkungan dan nilai yang kami bangun
                bersama anak-anak.
            </p>
        </div>
    </section>


    <!-- TENTANG & IDENTITAS -->
    <section class="container ppdb-profile__section">

        <div class="ppdb-profile__intro">

            <article class="ppdb-profile__about">
                <p class="ppdb-profile__about-label">Tentang kami</p>

                <h2>Tempat anak belajar dengan rasa aman.</h2>

                <p>
                    TK Harapan Bunda adalah lembaga pendidikan anak usia dini
                    yang mengutamakan suasana belajar aman, tertib, dan
                    menyenangkan.
                </p>

                <p>
                    Kegiatan sekolah disusun agar anak terbiasa mandiri,
                    percaya diri, peduli teman, dan siap masuk jenjang
                    berikutnya.
                </p>

                <p>
                    Pembelajaran dilakukan melalui bermain, bercerita,
                    praktik sederhana, kegiatan seni, olahraga ringan,
                    dan pembiasaan harian.
                </p>
            </article>


            <!-- IDENTITAS SEKOLAH -->
            <article class="ppdb-profile__identity">

                <p class="ppdb-profile__identity-label">
                    Kartu identitas
                </p>

                <h3>Identitas Sekolah</h3>

                <div class="ppdb-profile__details">

                    <div class="ppdb-profile__detail">
                        <span>Nama</span>
                        <strong>TK Harapan Bunda</strong>
                    </div>

                    <div class="ppdb-profile__detail">
                        <span>Alamat</span>
                        <strong>Jl. Melati No. 10, Indonesia</strong>
                    </div>

                    <div class="ppdb-profile__detail">
                        <span>Kontak</span>
                        <strong>0812-3456-7890</strong>
                    </div>

                    <div class="ppdb-profile__detail">
                        <span>Email</span>
                        <strong>ppdb@tkharapanbunda.sch.id</strong>
                    </div>

                </div>

            </article>

        </div>

    </section>


    <!-- VISI & MISI -->
    <section class="ppdb-profile__values">

        <div class="container">

            <div class="ppdb-profile__section-title">
                <p>Arah pendidikan</p>
                <h2>Visi & Misi</h2>
            </div>


            <div class="ppdb-profile__values-grid">

                <!-- VISI -->
                <article class="ppdb-profile__value-card ppdb-profile__value-card--vision">

                    <h3>Visi</h3>

                    <p>
                        Menjadi taman kanak-kanak yang membantu anak tumbuh
                        menjadi pribadi mandiri, santun, kreatif, dan siap
                        belajar.
                    </p>

                </article>


                <!-- MISI -->
                <article class="ppdb-profile__value-card ppdb-profile__value-card--mission">

                    <h3>Misi</h3>

                    <ul class="ppdb-profile__mission-list">

                        <li>
                            <span class="ppdb-profile__mission-dot">01</span>
                            <span>
                                Menyediakan lingkungan belajar yang aman dan
                                ramah anak.
                            </span>
                        </li>

                        <li>
                            <span class="ppdb-profile__mission-dot">02</span>
                            <span>
                                Membiasakan perilaku baik dalam kegiatan
                                sehari-hari.
                            </span>
                        </li>

                        <li>
                            <span class="ppdb-profile__mission-dot">03</span>
                            <span>
                                Mengembangkan kemampuan sosial, bahasa,
                                motorik, seni, dan kognitif anak.
                            </span>
                        </li>

                    </ul>

                </article>

            </div>


            <!-- PENUTUP -->
            <div class="ppdb-profile__closing">

                <div>
                    <h3>Belajar dimulai dari lingkungan yang nyaman.</h3>

                    <p>
                        TK Harapan Bunda mendampingi setiap anak untuk
                        berkembang sesuai tahap dan potensinya.
                    </p>
                </div>

                <div class="ppdb-profile__closing-mark">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 3.5 14.7 9l6 .9-4.3 4.2 1 6-5.4-2.8-5.4 2.8 1-6L3.3 9.9l6-.9Z"/>
                    </svg>
                </div>

            </div>

        </div>

    </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
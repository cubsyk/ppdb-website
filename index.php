<?php
$pageTitle = 'Beranda - PPDB TK Harapan Bunda';
$activePage = 'home';
require __DIR__ . '/config/database.php';

$statsCount = 0;
try {
    $statsStatement = $pdo->query('SELECT COUNT(*) FROM pendaftaran');
    $statsCount = (int) $statsStatement->fetchColumn();
} catch (PDOException $exception) {
    $statsCount = 0;
}

require __DIR__ . '/includes/header.php';
?>

<style>
/* =========================================================
   PPDB HOME
   Tema: Rapor & Kartu Pelajar
   Catatan: warna ditulis langsung (tanpa var(--...)) supaya
   tidak bergantung pada variabel CSS.
   Palet:
   paper #FBF7EF | paper-line #E7DFCE | chalk #2F4538
   ink #262A20 | muted #6B7060 | coral #E1552E
   leaf #3F7E52 | leaf-soft #E7F1E4
   amber #C6820E | amber-soft #FBF0DC
   ========================================================= */

.ppdb-home {
    font-family: 'Inter', system-ui, sans-serif;
    color: #262A20;
    overflow: hidden;
}

.ppdb-home h1,
.ppdb-home h2,
.ppdb-home h3 {
    font-family: 'Fraunces', Georgia, serif;
    font-weight: 600;
    letter-spacing: -0.015em;
}

.ppdb-home p {
    line-height: 1.65;
}

/* ---------- Hero ---------- */

.ppdb-home__hero {
    position: relative;
    background: #FBF7EF;
    border-bottom: 1px solid #E7DFCE;
    min-height: calc(100vh - 107px);
    padding: 60px 0;
    box-sizing: border-box;
    display: flex;
    align-items: center;
}

.ppdb-home__hero::before {
    content: '';
    position: absolute;
    width: 260px;
    height: 260px;
    border: 1px solid #DED5C2;
    border-radius: 50%;
    right: -100px;
    top: -100px;
    opacity: .7;
    pointer-events: none;
}

.ppdb-home__hero::after {
    content: '';
    position: absolute;
    width: 150px;
    height: 150px;
    border: 1px solid #DED5C2;
    border-radius: 50%;
    right: -45px;
    top: -45px;
    opacity: .7;
    pointer-events: none;
}

.ppdb-home__hero-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(340px, .8fr);
    gap: 56px;
    align-items: center;
    position: relative;
    z-index: 1;
    width: min(1120px, calc(100% - 48px));
    margin: 0 auto;
}

.ppdb-home__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    color: #3F7E52;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin: 0 0 16px;
}

.ppdb-home__eyebrow::before {
    content: '';
    width: 28px;
    height: 2px;
    background: #E1552E;
    display: inline-block;
}

.ppdb-home__hero h1 {
    color: #2F4538;
    font-size: clamp(38px, 5vw, 64px);
    line-height: 1.06;
    max-width: 760px;
    margin: 0 0 20px;
}

.ppdb-home__hero h1 span {
    color: #E1552E;
}

.ppdb-home__lead {
    max-width: 650px;
    color: #6B7060;
    font-size: 16px;
    margin: 0 0 28px;
}

/* ---------- Buttons ---------- */

.ppdb-home__actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.ppdb-home__btn {
    position: relative;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    padding: 0 22px;
    border-radius: 10px;
    background: #E1552E;
    color: #fff;
    text-decoration: none;
    font-size: 14.5px;
    font-weight: 700;
    transition: transform .18s ease, box-shadow .18s ease;
}

.ppdb-home__btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(225, 85, 46, .18);
}

.ppdb-home__btn::after {
    content: '';
    position: absolute;
    top: 0;
    left: -120%;
    width: 70%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255,255,255,.25),
        transparent
    );
    transform: skewX(-18deg);
    transition: left .55s ease;
}

.ppdb-home__btn:hover::after {
    left: 140%;
}

.ppdb-home__btn--outline {
    background: transparent;
    color: #2F4538;
    border: 1.5px solid #E7DFCE;
}

.ppdb-home__btn--outline:hover {
    box-shadow: none;
    background: #fff;
}

/* ---------- Info Card ---------- */

.ppdb-home__info {
    background: #2F4538;
    color: #F3F0E6;
    border-radius: 18px;
    padding: 28px;
    position: relative;
    box-shadow: 0 18px 45px rgba(47, 69, 56, .12);
}

.ppdb-home__info::after {
    content: 'PPDB';
    position: absolute;
    right: 18px;
    bottom: 8px;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 55px;
    font-weight: 700;
    color: rgba(255,255,255,.045);
    pointer-events: none;
}

.ppdb-home__info-label {
    color: #C9D3C9;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin: 0 0 5px;
}

.ppdb-home__info h2 {
    color: #F3F0E6;
    font-size: 24px;
    margin: 0 0 22px;
}

.ppdb-home__schedule {
    list-style: none;
    margin: 0;
    padding: 0;
}

.ppdb-home__schedule li {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    padding: 13px 0;
    border-top: 1px solid rgba(255,255,255,.12);
    font-size: 13.5px;
    transition: padding-left .25s ease;
}

.ppdb-home__schedule li:last-child {
    border-bottom: 1px solid rgba(255,255,255,.12);
}

.ppdb-home__schedule li:hover {
    padding-left: 6px;
}

.ppdb-home__schedule span {
    color: #BFCBBF;
}

.ppdb-home__schedule strong {
    color: #fff;
    text-align: right;
}

.ppdb-home__count {
    margin-top: 20px;
    padding: 14px 16px;
    background: rgba(255,255,255,.08);
    border-radius: 10px;
    transition: transform .25s ease, background .25s ease;
}

.ppdb-home__count:hover {
    transform: translateY(-2px);
    background: rgba(255,255,255,.12);
}

.ppdb-home__count small {
    display: block;
    color: #BFCBBF;
    font-size: 12px;
    margin-bottom: 3px;
}

.ppdb-home__count strong {
    color: #fff;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 27px;
}

/* ---------- Section ---------- */

.ppdb-home__section {
    padding: 68px 0;
}

.ppdb-home__section-head {
    max-width: 680px;
    margin-bottom: 30px;
}

.ppdb-home__section-head h2 {
    color: #2F4538;
    font-size: clamp(28px, 4vw, 39px);
    margin: 0 0 10px;
}

.ppdb-home__section-head p {
    color: #6B7060;
    font-size: 15px;
    margin: 0;
}

/* ---------- Feature Cards ---------- */

.ppdb-home__features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.ppdb-home__card {
    background: #fff;
    border: 1px solid #E7DFCE;
    border-radius: 14px;
    padding: 25px;
    position: relative;
    transition:
        transform .28s cubic-bezier(.2, .8, .2, 1),
        box-shadow .28s ease;
}

.ppdb-home__card:hover {
    transform: translateY(-7px) rotate(-.35deg);
    box-shadow: 0 16px 34px rgba(47, 69, 56, .10);
}

.ppdb-home__card:nth-child(2):hover {
    transform: translateY(-7px) rotate(.35deg);
}

.ppdb-home__number {
    position: absolute;
    right: 20px;
    top: 17px;
    font-family: 'Fraunces', Georgia, serif;
    font-size: 28px;
    color: #E9E1D2;
    font-weight: 700;
    transition: transform .3s ease, color .3s ease;
}

.ppdb-home__card:hover .ppdb-home__number {
    transform: translateY(-3px);
    color: #D9CFBD;
}

.ppdb-home__card-icon {
    width: 44px;
    height: 44px;
    border-radius: 11px;
    background: #E7F1E4;
    color: #3F7E52;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
    transition: transform .3s ease, border-radius .3s ease;
}

.ppdb-home__card:hover .ppdb-home__card-icon {
    transform: rotate(-4deg) scale(1.06);
    border-radius: 14px;
}

.ppdb-home__card-icon svg {
    width: 22px;
    height: 22px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.7;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.ppdb-home__card:nth-child(2) .ppdb-home__card-icon {
    background: #FBF0DC;
    color: #C6820E;
}

.ppdb-home__card:nth-child(3) .ppdb-home__card-icon {
    background: #F8E7E4;
    color: #E1552E;
}

.ppdb-home__card h3 {
    color: #2F4538;
    font-size: 19px;
    margin: 0 0 8px;
}

.ppdb-home__card p {
    color: #6B7060;
    font-size: 14px;
    margin: 0;
}

/* ---------- Bottom CTA ---------- */

.ppdb-home__cta {
    position: relative;
    overflow: hidden;
    margin-top: 16px;
    background: #2F4538;
    border-radius: 16px;
    padding: 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 25px;
}

.ppdb-home__cta::before {
    content: '';
    position: absolute;
    width: 180px;
    height: 180px;
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 50%;
    right: -65px;
    top: -90px;
    pointer-events: none;
}

.ppdb-home__cta::after {
    content: '';
    position: absolute;
    width: 110px;
    height: 110px;
    border: 1px solid rgba(255,255,255,.05);
    border-radius: 50%;
    right: 35px;
    top: -55px;
    pointer-events: none;
}

.ppdb-home__cta h2 {
    color: #F3F0E6;
    font-size: 27px;
    margin: 0 0 6px;
}

.ppdb-home__cta p {
    color: #C9D3C9;
    font-size: 14px;
    margin: 0;
}

.ppdb-home__cta .ppdb-home__btn {
    position: relative;
    z-index: 1;
    flex-shrink: 0;
}

/* ---------- Responsive ---------- */

@media (max-width: 800px) {
    .ppdb-home__hero {
        padding: 50px 0;
    }

    .ppdb-home__hero-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .ppdb-home__features {
        grid-template-columns: 1fr;
    }

    .ppdb-home__cta {
        align-items: flex-start;
        flex-direction: column;
    }
}

@media (max-width: 520px) {
    .ppdb-home__hero h1 {
        font-size: 36px;
    }

    .ppdb-home__section {
        padding: 48px 0;
    }

    .ppdb-home__info {
        padding: 23px;
    }

    .ppdb-home__schedule li {
        flex-direction: column;
        gap: 3px;
    }

    .ppdb-home__schedule strong {
        text-align: left;
    }

    .ppdb-home__cta {
        padding: 25px;
    }

    .ppdb-home__actions {
        flex-direction: column;
    }

    .ppdb-home__btn {
        width: 100%;
    }
}

/* =========================================================
   ANIMATIONS
   ========================================================= */

.ppdb-home [data-reveal] {
    opacity: 0;
    transform: translateY(24px);
    transition:
        opacity .7s ease,
        transform .7s cubic-bezier(.2, .8, .2, 1);
}

.ppdb-home [data-reveal].is-visible {
    opacity: 1;
    transform: translateY(0);
}

.ppdb-home [data-reveal="left"] {
    transform: translateX(-28px);
}

.ppdb-home [data-reveal="left"].is-visible {
    transform: translateX(0);
}

.ppdb-home [data-reveal="right"] {
    transform: translateX(28px);
}

.ppdb-home [data-reveal="right"].is-visible {
    transform: translateX(0);
}

/* Kartu fitur: pakai transisi sendiri agar hover tetap halus setelah reveal */
.ppdb-home .ppdb-home__card[data-reveal] {
    transition:
        opacity .7s ease,
        transform .7s cubic-bezier(.2, .8, .2, 1),
        box-shadow .28s ease;
}

.ppdb-home .ppdb-home__card[data-reveal].is-visible:hover {
    transform: translateY(-7px) rotate(-.35deg);
}

.ppdb-home .ppdb-home__card:nth-child(2)[data-reveal].is-visible:hover {
    transform: translateY(-7px) rotate(.35deg);
}

/* Hero decoration */
.ppdb-home__hero::before {
    animation: ppdbCircleFloat 8s ease-in-out infinite;
}

.ppdb-home__hero::after {
    animation: ppdbCircleFloat 6s ease-in-out infinite reverse;
}

@keyframes ppdbCircleFloat {
    0%, 100% { transform: translate(0, 0); }
    50%      { transform: translate(-12px, 14px); }
}

/* Hero text */
.ppdb-home__hero-content {
    animation: ppdbHeroText .8s cubic-bezier(.2, .8, .2, 1) both;
}

@keyframes ppdbHeroText {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Stagger */
.ppdb-home [data-delay="1"] { transition-delay: .08s; }
.ppdb-home [data-delay="2"] { transition-delay: .16s; }
.ppdb-home [data-delay="3"] { transition-delay: .24s; }
.ppdb-home [data-delay="4"] { transition-delay: .32s; }

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    .ppdb-home *,
    .ppdb-home *::before,
    .ppdb-home *::after {
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
    }

    .ppdb-home [data-reveal] {
        opacity: 1;
        transform: none;
    }
}
</style>

<!-- Pakai <div>, bukan <main>, karena header.php sudah membuka <main> -->
<div class="ppdb-home">

    <!-- HERO -->
    <section class="ppdb-home__hero">
        <div class="container ppdb-home__hero-grid">

            <div class="ppdb-home__hero-content">
                <p class="ppdb-home__eyebrow">
                    Penerimaan Peserta Didik Baru
                </p>

                <h1>
                    Awal perjalanan kecil,
                    <span>masa depan besar.</span>
                </h1>

                <p class="ppdb-home__lead">
                    Selamat datang di PPDB TK Harapan Bunda.
                    Pendaftaran siswa baru untuk Kelompok A, Kelompok B,
                    dan Daycare Ceria tahun ajaran 2026/2027 sudah dibuka.
                </p>

                <div class="ppdb-home__actions">
                    <a class="ppdb-home__btn" href="daftar.php">
                        Daftar Sekarang
                    </a>

                    <a class="ppdb-home__btn ppdb-home__btn--outline" href="profil.php">
                        Lihat Profil Sekolah
                    </a>
                </div>
            </div>

            <!-- INFO PPDB -->
            <div class="ppdb-home__info" data-reveal="right">

                <p class="ppdb-home__info-label">
                    Informasi pendaftaran
                </p>

                <h2>PPDB 2026/2027</h2>

                <ul class="ppdb-home__schedule">
                    <li>
                        <span>Gelombang 1</span>
                        <strong>1 Jan - 30 Apr 2026</strong>
                    </li>

                    <li>
                        <span>Observasi</span>
                        <strong>2 - 10 Mei 2026</strong>
                    </li>

                    <li>
                        <span>Pengumuman</span>
                        <strong>15 Mei 2026</strong>
                    </li>
                </ul>

                <div class="ppdb-home__count">
                    <small>Total pendaftaran online</small>
                    <strong><?= $statsCount; ?> pendaftar</strong>
                </div>

            </div>

        </div>
    </section>


    <!-- KEUNGGULAN -->
    <section class="container ppdb-home__section">

        <div class="ppdb-home__section-head" data-reveal>
            <h2>Tempat anak tumbuh, bermain, dan belajar.</h2>

            <p>
                Kami menghadirkan lingkungan belajar yang nyaman dan kegiatan
                yang membantu anak berkembang sesuai tahap usianya.
            </p>
        </div>

        <div class="ppdb-home__features">

            <article class="ppdb-home__card" data-reveal data-delay="1">
                <span class="ppdb-home__number">01</span>

                <div class="ppdb-home__card-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 21h18"/>
                        <path d="M5 21V9l7-5 7 5v12"/>
                        <path d="M9 21v-6h6v6"/>
                    </svg>
                </div>

                <h3>Kelas Nyaman</h3>

                <p>
                    Ruang belajar bersih, aman, dan disiapkan sesuai
                    kebutuhan anak usia dini.
                </p>
            </article>


            <article class="ppdb-home__card" data-reveal data-delay="2">
                <span class="ppdb-home__number">02</span>

                <div class="ppdb-home__card-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="7" r="4"/>
                        <path d="M4 21c1.6-4 4.3-6 8-6s6.4 2 8 6"/>
                        <path d="M19 5v4"/>
                        <path d="M17 7h4"/>
                    </svg>
                </div>

                <h3>Guru Pendamping</h3>

                <p>
                    Guru mendampingi anak dalam bermain, belajar,
                    dan membangun kebiasaan positif.
                </p>
            </article>


            <article class="ppdb-home__card" data-reveal data-delay="3">
                <span class="ppdb-home__number">03</span>

                <div class="ppdb-home__card-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
                        <path d="M8 6h8"/>
                        <path d="M8 10h7"/>
                    </svg>
                </div>

                <h3>Kegiatan Variatif</h3>

                <p>
                    Belajar bahasa, motorik, seni, numerasi awal,
                    serta kegiatan sosial melalui aktivitas yang menyenangkan.
                </p>
            </article>

        </div>


        <!-- CTA -->
        <div class="ppdb-home__cta" data-reveal>

            <div>
                <h2>Siap memulai pendaftaran?</h2>

                <p>
                    Lengkapi data calon siswa dan ikuti proses PPDB
                    TK Harapan Bunda secara online.
                </p>
            </div>

            <a class="ppdb-home__btn" href="daftar.php">
                Mulai Pendaftaran
            </a>

        </div>

    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const revealItems = document.querySelectorAll('.ppdb-home [data-reveal]');

    if (!('IntersectionObserver' in window)) {
        revealItems.forEach(item => item.classList.add('is-visible'));
        return;
    }

    const revealObserver = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;

                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        },
        {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        }
    );

    revealItems.forEach(item => revealObserver.observe(item));
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
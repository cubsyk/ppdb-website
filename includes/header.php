<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$pageTitle = $pageTitle ?? 'PPDB TK Harapan Bunda';
$activePage = $activePage ?? '';
$currentUser = $_SESSION['user'] ?? null;

$base = $cssBase ?? '';
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>
    </title>

    <meta
        name="description"
        content="PPDB Online TK Harapan Bunda"
    >

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="<?= $base ?>assets/css/style.css"
    >

</head>

<body>

<header class="site-header ppdb-site-header">

    <!-- TOP BAR -->
    <div class="ppdb-topbar">

        <div class="ppdb-header-container">

            <div class="ppdb-topbar-left">

                <span class="ppdb-topbar-dot"></span>

                <span>
                    PPDB Tahun Ajaran 2026/2027
                </span>

            </div>

            <div class="ppdb-topbar-right">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2
                             19.79 19.79 0 0 1-8.63-3.07
                             19.5 19.5 0 0 1-6-6
                             19.79 19.79 0 0 1-3.07-8.67
                             A2 2 0 0 1 4.11 2h3
                             a2 2 0 0 1 2 1.72
                             12.84 12.84 0 0 0 .7 2.81
                             2 2 0 0 1-.45 2.11L8.09 9.91
                             a16 16 0 0 0 6 6l1.27-1.27
                             a2 2 0 0 1 2.11-.45
                             12.84 12.84 0 0 1 2.81.7
                             A2 2 0 0 1 22 16.92z"/>
                </svg>

                <span>
                    Telp: (021) 786 9909
                </span>

            </div>

        </div>

    </div>


    <!-- NAVBAR -->
    <nav class="ppdb-navbar">

        <div class="ppdb-header-container ppdb-navbar-inner">

            <!-- BRAND -->
            <a
                href="<?= $base ?>index.php"
                class="ppdb-brand"
            >

                <span class="ppdb-brand-logo">

                    <img
                        src="<?= $base ?>images/logo.png"
                        alt="Logo TK Harapan Bunda"
                        width="46"
                        height="46"
                    >

                </span>

                <span class="ppdb-brand-text">

                    <strong>
                        TK Harapan Bunda
                    </strong>

                    <small>
                        Belajar, bermain, berkarakter
                    </small>

                </span>

            </a>


            <!-- MOBILE TOGGLE -->
            <button
                class="ppdb-nav-toggle"
                type="button"
                data-nav-toggle
                aria-label="Buka menu"
                aria-expanded="false"
            >

                <span></span>
                <span></span>
                <span></span>

            </button>


            <!-- MENU -->
            <div
                class="ppdb-nav-menu"
                data-nav-menu
            >

                <a
                    class="<?= $activePage === 'home' ? 'active' : ''; ?>"
                    href="<?= $base ?>index.php"
                >
                    Beranda
                </a>

                <a
                    class="<?= $activePage === 'profil' ? 'active' : ''; ?>"
                    href="<?= $base ?>profil.php"
                >
                    Profil
                </a>

                <a
                    class="<?= $activePage === 'program' ? 'active' : ''; ?>"
                    href="<?= $base ?>program.php"
                >
                    Program
                </a>

                <a
                    class="<?= $activePage === 'jadwal' ? 'active' : ''; ?>"
                    href="<?= $base ?>jadwal.php"
                >
                    Jadwal
                </a>

                <a
                    class="<?= $activePage === 'biaya' ? 'active' : ''; ?>"
                    href="<?= $base ?>biaya.php"
                >
                    Biaya
                </a>


                <?php if ($currentUser): ?>

                    <?php if ($currentUser['role'] === 'admin'): ?>

                        <a
                            class="ppdb-nav-action <?= $activePage === 'admin' ? 'active' : ''; ?>"
                            href="<?= $base ?>admin/index.php"
                        >

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect
                                    x="3"
                                    y="3"
                                    width="7"
                                    height="7"
                                    rx="1"
                                />
                                <rect
                                    x="14"
                                    y="3"
                                    width="7"
                                    height="7"
                                    rx="1"
                                />
                                <rect
                                    x="3"
                                    y="14"
                                    width="7"
                                    height="7"
                                    rx="1"
                                />
                                <rect
                                    x="14"
                                    y="14"
                                    width="7"
                                    height="7"
                                    rx="1"
                                />
                            </svg>

                            Dashboard Admin

                        </a>

                    <?php else: ?>

                        <a
                            class="<?= $activePage === 'dashboard' ? 'active' : ''; ?>"
                            href="<?= $base ?>dashboard.php"
                        >
                            Dashboard Saya
                        </a>

                        <a
                            class="ppdb-nav-outline"
                            href="<?= $base ?>logout.php"
                        >

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M10 17l5-5-5-5"/>
                                <path d="M15 12H3"/>
                                <path d="M21 19V5a2 2 0 0 0-2-2h-6"/>
                            </svg>

                            Keluar

                        </a>

                    <?php endif; ?>

                <?php else: ?>

                <a
                    class="ppdb-nav-register <?= $activePage === 'login' ? 'active' : ''; ?>"
                    href="<?= $base ?>login.php"
                >
                    Login
                </a>

                    <a
                        class="ppdb-nav-register <?= $activePage === 'daftar' ? 'active' : ''; ?>"
                        href="<?= $base ?>register.php"
                    >

                        Daftar

                    </a>

                <?php endif; ?>

            </div>

        </div>

    </nav>

</header>


<main>


<style>

/* =========================================================
   GLOBAL HEADER
   Tema Rapor & Kartu Pelajar
   ========================================================= */

.ppdb-site-header {
    position: relative;
    z-index: 100;
    font-family: "Inter", Arial, sans-serif;
}

/* =========================================================
   TOP BAR
   ========================================================= */

.ppdb-topbar {
    min-height: 31px;
    background: #2F4538;
    color: #DCE6DE;
    font-size: 10px;
    font-weight: 600;
}

.ppdb-header-container {
    width: min(1180px, calc(100% - 40px));
    margin: 0 auto;
}

.ppdb-topbar .ppdb-header-container {
    min-height: 31px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.ppdb-topbar-left,
.ppdb-topbar-right {
    display: flex;
    align-items: center;
    gap: 7px;
}

.ppdb-topbar-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #AFC8B2;
}

.ppdb-topbar-right svg {
    width: 13px;
    height: 13px;
}

/* =========================================================
   NAVBAR
   ========================================================= */

.ppdb-navbar {
    position: relative;
    z-index: 1000;
    border-bottom: 1px solid #E4DDCE;
    background: rgba(255,253,248,.97);
    box-shadow: 0 4px 18px rgba(47,69,56,.035);
}

.ppdb-navbar-inner {
    min-height: 76px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 25px;
}

/* =========================================================
   BRAND
   ========================================================= */

.ppdb-brand {
    display: inline-flex;
    align-items: center;
    gap: 11px;
    flex-shrink: 0;
    color: #2F4538;
    text-decoration: none;
}

.ppdb-brand-logo {
    position: relative;
    width: 46px;
    height: 46px;
    display: grid;
    place-items: center;
    overflow: hidden;
    border: 1px solid #DCD5C4;
    border-radius: 13px;
    background: #FBF7EF;
}

.ppdb-brand-logo::after {
    content: "";
    position: absolute;
    inset: 4px;
    border: 1px dashed #CFC6B2;
    border-radius: 9px;
    pointer-events: none;
}

.ppdb-brand-logo img {
    position: relative;
    z-index: 1;
    width: 42px;
    height: 42px;
    object-fit: contain;
}

.ppdb-brand-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.ppdb-brand-text strong {
    color: #2F4538;
    font-family: "Fraunces", Georgia, serif;
    font-size: 17px;
    font-weight: 700;
    line-height: 1.1;
}

.ppdb-brand-text small {
    color: #7A7E70;
    font-size: 9px;
    font-weight: 600;
    letter-spacing: .03em;
}

/* =========================================================
   NAV MENU
   ========================================================= */

.ppdb-nav-menu {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 3px;
}

.ppdb-nav-menu > a {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 38px;
    padding: 7px 11px;
    border-radius: 9px;
    color: #62685C;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
    transition:
        color .18s ease,
        background .18s ease;
}

.ppdb-nav-menu > a:hover {
    color: #2F4538;
    background: #F4F0E7;
}

.ppdb-nav-menu > a.active:not(.ppdb-nav-action):not(.ppdb-nav-register) {
    color: #2F4538;
    background: #F0F4ED;
}

.ppdb-nav-menu > a.active:not(.ppdb-nav-action):not(.ppdb-nav-register)::after {
    content: "";
    position: absolute;
    left: 50%;
    bottom: 3px;
    width: 15px;
    height: 2px;
    border-radius: 99px;
    background: #3F7E52;
    transform: translateX(-50%);
}

/* =========================================================
   NAV ACTIONS
   ========================================================= */

.ppdb-nav-action {
    margin-left: 5px;
    padding-left: 13px !important;
    padding-right: 13px !important;
    gap: 6px;
    border: 1px solid #2F4538;
    background: #2F4538;
    color: #fff !important;
}

.ppdb-nav-action:hover {
    background: #3E5A48 !important;
    color: #fff !important;
}

.ppdb-nav-action.active {
    background: #3E5A48 !important;
}

.ppdb-nav-action svg {
    width: 14px;
    height: 14px;
}

.ppdb-nav-outline {
    margin-left: 4px;
    gap: 5px;
    border: 1px solid #D7D0C0;
    background: #FFFDF8;
    color: #3E5A48 !important;
}

.ppdb-nav-outline:hover {
    border-color: #B9C7BA;
    background: #F1F5EE !important;
}

.ppdb-nav-outline svg {
    width: 14px;
    height: 14px;
}

.ppdb-nav-register {
    margin-left: 5px;
    padding-left: 15px !important;
    padding-right: 15px !important;
    border: 1px solid #2F4538;
    background: #2F4538;
    color: #fff !important;
}

.ppdb-nav-register:hover {
    background: #3E5A48 !important;
    color: #fff !important;
}

.ppdb-nav-register.active {
    background: #3E5A48 !important;
}

/* =========================================================
   MOBILE TOGGLE
   ========================================================= */

.ppdb-nav-toggle {
    display: none;
    width: 42px;
    height: 42px;
    padding: 9px;
    border: 1px solid #DCD5C4;
    border-radius: 11px;
    background: #FFFDF8;
    cursor: pointer;
}

.ppdb-nav-toggle span {
    display: block;
    width: 20px;
    height: 2px;
    margin: 4px auto;
    border-radius: 99px;
    background: #2F4538;
    transition:
        transform .2s ease,
        opacity .2s ease;
}

.ppdb-nav-toggle.is-open span:nth-child(1) {
    transform: translateY(6px) rotate(45deg);
}

.ppdb-nav-toggle.is-open span:nth-child(2) {
    opacity: 0;
}

.ppdb-nav-toggle.is-open span:nth-child(3) {
    transform: translateY(-6px) rotate(-45deg);
}

/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 900px) {

    .ppdb-header-container {
        width: min(100% - 30px, 680px);
    }

    .ppdb-nav-toggle {
        display: block;
        flex-shrink: 0;
        position: relative;
        z-index: 1002;
    }

    .ppdb-navbar-inner {
        min-height: 68px;
        position: relative;
    }

    .ppdb-nav-menu {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        z-index: 1001;

        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 4px;

        padding: 10px;
        border: 1px solid #DDD5C4;
        border-radius: 15px;
        background: #FFFDF8;
        box-shadow: 0 15px 35px rgba(47,69,56,.12);

        /* Animasi */
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-10px) scale(.98);
        transform-origin: top center;

        transition:
            opacity .22s ease,
            transform .22s ease,
            visibility 0s linear .22s;
    }

    .ppdb-nav-menu.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0) scale(1);

        transition:
            opacity .22s ease,
            transform .22s ease,
            visibility 0s linear 0s;
    }

    .ppdb-nav-menu > a {
        justify-content: flex-start;
        min-height: 42px;
        padding: 9px 12px;
    }

    .ppdb-nav-menu > a.active:not(.ppdb-nav-action):not(.ppdb-nav-register)::after {
        display: none;
    }

    .ppdb-nav-action,
    .ppdb-nav-register,
    .ppdb-nav-outline {
        margin-left: 0;
    }

    .ppdb-nav-action,
    .ppdb-nav-register {
        justify-content: center !important;
    }
}

@media (max-width: 600px) {

    .ppdb-topbar .ppdb-header-container {
        justify-content: center;
    }

    .ppdb-topbar-left {
        display: none;
    }

    .ppdb-topbar-right {
        font-size: 9px;
    }

    .ppdb-header-container {
        width: calc(100% - 24px);
    }

    .ppdb-brand-logo {
        width: 42px;
        height: 42px;
    }

    .ppdb-brand-logo img {
        width: 38px;
        height: 38px;
    }

    .ppdb-brand-text strong {
        font-size: 15px;
    }

    .ppdb-brand-text small {
        font-size: 8px;
    }
}

</style>


<script>
(function () {
    function initMobileNav() {
        const toggle = document.querySelector('[data-nav-toggle]');
        const menu = document.querySelector('[data-nav-menu]');

        if (!toggle || !menu) return;

        toggle.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            const isOpen = menu.classList.contains('is-open');

            menu.classList.toggle('is-open', !isOpen);
            toggle.classList.toggle('is-open', !isOpen);

            toggle.setAttribute(
                'aria-expanded',
                !isOpen ? 'true' : 'false'
            );
        });

        menu.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        document.addEventListener('click', function () {
            menu.classList.remove('is-open');
            toggle.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMobileNav);
    } else {
        initMobileNav();
    }
})();
</script>
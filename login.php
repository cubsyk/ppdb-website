<?php
declare(strict_types=1);

require __DIR__ . '/includes/auth.php';

$pageTitle = 'Login - TK Harapan Bunda';
$activePage = 'daftar';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = post('email');
    $pass = post('password');

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email=? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password_hash'])) {
        login_user($user);

        redirect(
            $user['role'] === 'admin'
                ? 'admin/index.php'
                : 'dashboard.php'
        );
    }

    $errors[] = 'Email atau password salah.';
}

require __DIR__ . '/includes/header.php';
?>

<style>
/* =========================================================
   LOGIN
   Tema Rapor & Kartu Pelajar
   ========================================================= */

.ppdb-login-page {
    padding: 55px 0 85px;
    background:
        linear-gradient(rgba(231, 223, 206, .45) 1px, transparent 1px),
        #FBF7EF;
    background-size: 100% 34px;
}

.ppdb-login-wrap {
    max-width: 900px;
    margin: 0 auto;
}

.ppdb-login-grid {
    display: grid;
    grid-template-columns: .85fr 1.15fr;
    overflow: hidden;
    border: 1px solid #DDD5C4;
    border-radius: 24px;
    background: #FFFDF8;
    box-shadow: 0 15px 40px rgba(47, 69, 56, .09);
}

/* =========================================================
   INFO PANEL
   ========================================================= */

.ppdb-login-info {
    position: relative;
    overflow: hidden;
    padding: 42px 34px;
    background: #2F4538;
    color: #fff;
}

.ppdb-login-info::before {
    content: "";
    position: absolute;
    width: 230px;
    height: 230px;
    right: -105px;
    bottom: -105px;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 50%;
    box-shadow:
        0 0 0 28px rgba(255,255,255,.035),
        0 0 0 56px rgba(255,255,255,.025);
}

.ppdb-login-info-inner {
    position: relative;
    z-index: 1;
}

.ppdb-login-info-label {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 20px;
    color: #DCE8DE;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
}

.ppdb-login-info-label svg {
    width: 16px;
    height: 16px;
}

.ppdb-login-info h2 {
    margin: 0;
    color: #FFFDF8;
    font-family: "Fraunces", Georgia, serif;
    font-size: 31px;
    line-height: 1.15;
}

.ppdb-login-info-description {
    margin: 13px 0 30px;
    color: #D6E0D8;
    font-size: 13px;
    line-height: 1.75;
}

.ppdb-login-info-list {
    display: grid;
    gap: 13px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.ppdb-login-info-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    color: #E4EBE5;
    font-size: 12px;
    line-height: 1.55;
}

.ppdb-login-check {
    width: 22px;
    height: 22px;
    flex: 0 0 22px;
    display: grid;
    place-items: center;
    border-radius: 7px;
    background: rgba(255,255,255,.1);
    color: #DDECDD;
}

.ppdb-login-check svg {
    width: 13px;
    height: 13px;
}

/* =========================================================
   FORM PANEL
   ========================================================= */

.ppdb-login-form-panel {
    padding: 42px 43px;
}

.ppdb-login-form-heading {
    margin-bottom: 24px;
}

.ppdb-login-form-heading h1 {
    margin: 0;
    color: #2F4538;
    font-family: "Fraunces", Georgia, serif;
    font-size: 29px;
    line-height: 1.15;
}

.ppdb-login-form-heading p {
    margin: 7px 0 0;
    color: #777B6E;
    font-size: 13px;
    line-height: 1.6;
}

/* =========================================================
   ALERT
   ========================================================= */

.ppdb-login-alert {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 17px;
    padding: 11px 13px;
    border-radius: 11px;
    font-size: 12px;
    line-height: 1.5;
}

.ppdb-login-alert svg {
    width: 17px;
    height: 17px;
    flex: 0 0 17px;
}

.ppdb-login-alert.success {
    border: 1px solid #CFE1D2;
    background: #E7F1E4;
    color: #315F3E;
}

.ppdb-login-alert.error {
    border: 1px solid #E8C9C5;
    background: #F8E7E4;
    color: #A83B3B;
}

/* =========================================================
   FORM
   ========================================================= */

.ppdb-login-form {
    display: grid;
    gap: 17px;
}

.ppdb-login-field {
    display: block;
}

.ppdb-login-field > span {
    display: block;
    margin-bottom: 7px;
    color: #3E453B;
    font-size: 12px;
    font-weight: 800;
}

.ppdb-login-input-wrap {
    position: relative;
}

.ppdb-login-input-wrap > svg {
    position: absolute;
    left: 13px;
    top: 50%;
    width: 17px;
    height: 17px;
    transform: translateY(-50%);
    color: #8B9083;
    pointer-events: none;
}

.ppdb-login-field input {
    width: 100%;
    min-height: 46px;
    box-sizing: border-box;
    padding: 11px 13px 11px 40px;
    border: 1px solid #DCD5C6;
    border-radius: 11px;
    outline: none;
    background: #FFFDF8;
    color: #262A20;
    font-family: inherit;
    font-size: 13px;
    transition:
        border-color .18s ease,
        box-shadow .18s ease,
        background .18s ease;
}

.ppdb-login-field input::placeholder {
    color: #A4A69D;
}

.ppdb-login-field input:focus {
    border-color: #718B78;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(63,126,82,.08);
}

/* =========================================================
   SUBMIT
   ========================================================= */

.ppdb-login-submit {
    width: 100%;
    min-height: 47px;
    margin-top: 2px;
    border: 0;
    border-radius: 11px;
    background: #2F4538;
    color: #fff;
    font-family: inherit;
    font-size: 13px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 7px 16px rgba(47,69,56,.16);
    transition:
        transform .18s ease,
        background .18s ease,
        box-shadow .18s ease;
}

.ppdb-login-submit:hover {
    transform: translateY(-1px);
    background: #3E5A48;
    box-shadow: 0 9px 20px rgba(47,69,56,.2);
}

.ppdb-login-submit svg {
    width: 17px;
    height: 17px;
    margin-right: 6px;
    vertical-align: -3px;
}

/* =========================================================
   REGISTER
   ========================================================= */

.ppdb-login-register {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    margin-top: 23px;
    padding-top: 21px;
    border-top: 1px dashed #DCD4C3;
    color: #777B6E;
    font-size: 12px;
}

.ppdb-login-register a {
    color: #3F7E52;
    font-weight: 800;
    text-decoration: none;
}

.ppdb-login-register a:hover {
    text-decoration: underline;
}

/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 760px) {

    .ppdb-login-page {
        padding: 30px 0 60px;
    }

    .ppdb-login-grid {
        grid-template-columns: 1fr;
        border-radius: 19px;
    }

    .ppdb-login-info {
        padding: 27px 23px;
    }

    .ppdb-login-info h2 {
        font-size: 27px;
    }

    .ppdb-login-info-description {
        margin-bottom: 20px;
    }

    .ppdb-login-info-list {
        grid-template-columns: 1fr 1fr;
    }

    .ppdb-login-form-panel {
        padding: 30px 23px;
    }
}

@media (max-width: 480px) {

    .ppdb-login-info-list {
        grid-template-columns: 1fr;
    }

    .ppdb-login-form-heading h1 {
        font-size: 26px;
    }
}
</style>

<section class="ppdb-login-page">
    <div class="container ppdb-login-wrap">

        <div class="ppdb-login-grid">

            <!-- PANEL INFORMASI -->
            <aside class="ppdb-login-info">

                <div class="ppdb-login-info-inner">

                    <div class="ppdb-login-info-label">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M4 4h16v16H4z"/>
                            <path d="M8 8h8"/>
                            <path d="M8 12h8"/>
                            <path d="M8 16h5"/>
                        </svg>

                        Portal PPDB

                    </div>

                    <h2>Selamat Datang Kembali</h2>

                    <p class="ppdb-login-info-description">
                        Masuk ke akun Anda untuk melanjutkan proses
                        pendaftaran peserta didik baru di TK Harapan Bunda.
                    </p>

                    <ul class="ppdb-login-info-list">

                        <li>
                            <span class="ppdb-login-check">
                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor"
                                     stroke-width="2"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="m5 12 4 4L19 6"/>
                                </svg>
                            </span>
                            Lanjutkan pengisian formulir pendaftaran.
                        </li>

                        <li>
                            <span class="ppdb-login-check">
                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor"
                                     stroke-width="2"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="m5 12 4 4L19 6"/>
                                </svg>
                            </span>
                            Pantau status pendaftaran Anda.
                        </li>

                        <li>
                            <span class="ppdb-login-check">
                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor"
                                     stroke-width="2"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="m5 12 4 4L19 6"/>
                                </svg>
                            </span>
                            Lihat informasi dan pengumuman PPDB.
                        </li>

                    </ul>

                </div>

            </aside>


            <!-- FORM LOGIN -->
            <div class="ppdb-login-form-panel">

                <div class="ppdb-login-form-heading">
                    <h1>Masuk ke Akun</h1>
                    <p>
                        Gunakan email dan password yang sudah didaftarkan.
                    </p>
                </div>


                <?php if ($m = flash('success')): ?>

                    <div class="ppdb-login-alert success">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="m5 12 4 4L19 6"/>
                        </svg>

                        <?= e($m) ?>

                    </div>

                <?php endif; ?>


                <?php if ($errors): ?>

                    <div class="ppdb-login-alert error">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M10.3 3.7 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"/>
                            <path d="M12 9v4"/>
                            <path d="M12 17h.01"/>
                        </svg>

                        <?= e($errors[0]) ?>

                    </div>

                <?php endif; ?>


                <form class="ppdb-login-form" method="post">

                    <label class="ppdb-login-field">

                        <span>Email</span>

                        <div class="ppdb-login-input-wrap">

                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path d="m3 7 9 6 9-6"/>
                            </svg>

                            <input
                                type="email"
                                name="email"
                                placeholder="nama@email.com"
                                autocomplete="email"
                                required
                            >

                        </div>

                    </label>


                    <label class="ppdb-login-field">

                        <span>Password</span>

                        <div class="ppdb-login-input-wrap">

                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="5" y="10" width="14" height="10" rx="2"/>
                                <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                            </svg>

                            <input
                                type="password"
                                name="password"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                            >

                        </div>

                    </label>


                    <button class="ppdb-login-submit" type="submit">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M10 17l5-5-5-5"/>
                            <path d="M15 12H3"/>
                            <path d="M21 19V5a2 2 0 0 0-2-2h-6"/>
                        </svg>

                        Login

                    </button>

                </form>


                <div class="ppdb-login-register">
                    Belum punya akun?
                    <a href="register.php">Buat Akun Orang Tua</a>
                </div>

            </div>

        </div>

    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
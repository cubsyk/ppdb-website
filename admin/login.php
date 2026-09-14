<?php
declare(strict_types=1);

$cssBase = '../';

require __DIR__ . '/../includes/auth.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        'SELECT * FROM users WHERE email=? AND role="admin" LIMIT 1'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password_hash'])) {
        login_user($user);
        redirect('../admin/index.php');
    }

    $errors[] = 'Email atau password admin salah.';
}

$pageTitle = 'Login Admin - TK Harapan Bunda';
$activePage = 'admin';

require __DIR__ . '/../includes/header.php';
?>

<style>
    .ppdb-admin-login {
        min-height: calc(100vh - 180px);
        padding: 60px 0 80px;
        background: #FBF7EF;
        position: relative;
        overflow: hidden;
    }

    .ppdb-admin-login::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background-image:
            linear-gradient(#E7DFCE 1px, transparent 1px),
            linear-gradient(90deg, #E7DFCE 1px, transparent 1px);
        background-size: 28px 28px;
        opacity: .45;
    }

    .ppdb-admin-login .container {
        position: relative;
        z-index: 1;
    }

    .ppdb-admin-login__wrap {
        max-width: 1000px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: .9fr 1.1fr;
        gap: 28px;
        align-items: stretch;
    }

    /* =========================
       LEFT PANEL
    ========================= */

    .ppdb-admin-login__intro {
        background: #2F4538;
        color: #fff;
        border-radius: 24px;
        padding: 40px;
        min-height: 460px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        box-shadow: 0 16px 38px rgba(47, 69, 56, .14);
    }

    .ppdb-admin-login__intro::before {
        content: "";
        position: absolute;
        width: 230px;
        height: 230px;
        border: 1px solid rgba(255,255,255,.10);
        border-radius: 50%;
        top: -70px;
        right: -70px;
    }

    .ppdb-admin-login__intro::after {
        content: "";
        position: absolute;
        width: 170px;
        height: 170px;
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 50%;
        bottom: -80px;
        left: -70px;
    }

    .ppdb-admin-login__intro-content {
        position: relative;
        z-index: 1;
    }

    .ppdb-admin-login__eyebrow {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 22px;
        color: #E9EFEA;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .ppdb-admin-login__eyebrow::before {
        content: "";
        width: 32px;
        height: 2px;
        background: #E1552E;
        border-radius: 20px;
    }

    .ppdb-admin-login__intro h1 {
        margin: 0 0 18px;
        color: #fff;
        font-family: "Fraunces", Georgia, serif;
        font-size: clamp(36px, 4vw, 52px);
        line-height: 1.02;
        letter-spacing: -.025em;
    }

    .ppdb-admin-login__intro p {
        margin: 0;
        max-width: 390px;
        color: #D9E3DC;
        font-size: 15px;
        line-height: 1.75;
    }

    .ppdb-admin-login__secure {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 15px 16px;
        border: 1px solid rgba(255,255,255,.12);
        background: rgba(255,255,255,.07);
        border-radius: 16px;
    }

    .ppdb-admin-login__secure-icon {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: rgba(255,255,255,.10);
        font-size: 20px;
    }

    .ppdb-admin-login__secure strong {
        display: block;
        margin-bottom: 3px;
        color: #fff;
        font-size: 13px;
    }

    .ppdb-admin-login__secure span {
        color: #C9D6CD;
        font-size: 12px;
    }

    /* =========================
       RIGHT CARD
    ========================= */

    .ppdb-admin-login__card {
        background: #fff;
        border: 1px solid #E7DFCE;
        border-radius: 24px;
        padding: 40px;
        box-sizing: border-box;
        box-shadow: 0 16px 38px rgba(38, 42, 32, .07);
    }

    .ppdb-admin-login__heading {
        margin-bottom: 28px;
    }

    .ppdb-admin-login__heading h2 {
        margin: 0 0 8px;
        color: #262A20;
        font-family: "Fraunces", Georgia, serif;
        font-size: 31px;
        line-height: 1.15;
    }

    .ppdb-admin-login__heading p {
        margin: 0;
        color: #6B7060;
        font-size: 14px;
        line-height: 1.6;
    }

    /* Error */
    .ppdb-admin-login__error {
        margin-bottom: 20px;
        padding: 12px 14px;
        border: 1px solid #EBCAC5;
        border-radius: 12px;
        background: #F8E7E4;
        color: #9A3434;
        font-size: 13px;
        line-height: 1.5;
    }

    /* Form */
    .ppdb-admin-login__field {
        display: block;
        margin-bottom: 18px;
    }

    .ppdb-admin-login__field span {
        display: block;
        margin-bottom: 7px;
        color: #262A20;
        font-size: 13px;
        font-weight: 750;
    }

    .ppdb-admin-login__input {
        width: 100%;
        min-height: 48px;
        box-sizing: border-box;
        padding: 12px 14px;
        border: 1px solid #E7DFCE;
        border-radius: 12px;
        background: #FFFDF9;
        color: #262A20;
        font-family: inherit;
        font-size: 14px;
        outline: none;
        transition: .2s ease;
    }

    .ppdb-admin-login__input:focus {
        border-color: #3F7E52;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(63,126,82,.10);
    }

    .ppdb-admin-login__submit {
        width: 100%;
        min-height: 50px;
        margin-top: 4px;
        border: 0;
        border-radius: 12px;
        background: #3F7E52;
        color: #fff;
        font-family: inherit;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        transition: .2s ease;
    }

    .ppdb-admin-login__submit:hover {
        background: #2F4538;
        transform: translateY(-1px);
    }

    /* =========================
       DEFAULT ACCOUNT
    ========================= */

    .ppdb-admin-login__account {
        margin-top: 24px;
        padding: 16px;
        border: 1px dashed #D8CCB8;
        border-radius: 14px;
        background: #FBF7EF;
    }

    .ppdb-admin-login__account-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 11px;
        color: #262A20;
        font-size: 13px;
        font-weight: 800;
    }

    .ppdb-admin-login__account-title-icon {
        width: 22px;
        height: 22px;
        display: grid;
        place-items: center;
        border-radius: 7px;
        background: #E7F1E4;
        color: #3F7E52;
        font-size: 12px;
        font-weight: 900;
    }

    .ppdb-admin-login__account p {
        margin: 5px 0;
        color: #6B7060;
        font-size: 12px;
    }

    .ppdb-admin-login__account strong {
        color: #262A20;
    }

    .ppdb-admin-login__warning {
        margin-top: 10px !important;
        color: #C6820E !important;
        font-size: 11px !important;
    }

    .ppdb-admin-login__back {
        margin-top: 20px;
        text-align: center;
    }

    .ppdb-admin-login__back a {
        color: #3F7E52;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }

    .ppdb-admin-login__back a:hover {
        text-decoration: underline;
    }

    /* =========================
       RESPONSIVE
    ========================= */

    @media (max-width: 800px) {
        .ppdb-admin-login {
            padding: 40px 0 60px;
        }

        .ppdb-admin-login__wrap {
            grid-template-columns: 1fr;
            max-width: 560px;
        }

        .ppdb-admin-login__intro {
            min-height: auto;
            padding: 30px;
            gap: 30px;
        }

        .ppdb-admin-login__intro h1 {
            font-size: 40px;
        }

        .ppdb-admin-login__card {
            padding: 30px;
        }
    }

    @media (max-width: 480px) {
        .ppdb-admin-login {
            padding: 28px 0 45px;
        }

        .ppdb-admin-login__intro,
        .ppdb-admin-login__card {
            padding: 24px;
            border-radius: 18px;
        }

        .ppdb-admin-login__intro h1 {
            font-size: 33px;
        }

        .ppdb-admin-login__heading h2 {
            font-size: 27px;
        }
    }
</style>

<section class="ppdb-admin-login">
    <div class="container">

        <div class="ppdb-admin-login__wrap">

            <!-- INTRO -->
            <div class="ppdb-admin-login__intro">

                <div class="ppdb-admin-login__intro-content">

                    <div class="ppdb-admin-login__eyebrow">
                        Area Panitia PPDB
                    </div>

                    <h1>
                        Selamat datang, Admin.
                    </h1>

                    <p>
                        Kelola proses pendaftaran, verifikasi berkas,
                        pembayaran, dan informasi PPDB TK Harapan Bunda
                        dari satu tempat.
                    </p>

                </div>

                <div class="ppdb-admin-login__secure">

                    <div class="ppdb-admin-login__secure-icon">
                        🔐
                    </div>

                    <div>
                        <strong>Akses khusus admin</strong>
                        <span>
                            Gunakan akun panitia yang terdaftar.
                        </span>
                    </div>

                </div>

            </div>


            <!-- LOGIN CARD -->
            <div class="ppdb-admin-login__card">

                <div class="ppdb-admin-login__heading">
                    <h2>Login Admin</h2>
                    <p>
                        Masuk untuk mengakses halaman pengelolaan PPDB.
                    </p>
                </div>


                <?php if ($errors): ?>
                    <div class="ppdb-admin-login__error">
                        <?= e($errors[0]) ?>
                    </div>
                <?php endif; ?>


                <form method="post">

                    <label class="ppdb-admin-login__field">
                        <span>Email Admin</span>

                        <input
                            class="ppdb-admin-login__input"
                            type="email"
                            name="email"
                            autocomplete="username"
                            required
                        >
                    </label>


                    <label class="ppdb-admin-login__field">
                        <span>Password</span>

                        <input
                            class="ppdb-admin-login__input"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            required
                        >
                    </label>


                    <button
                        class="ppdb-admin-login__submit"
                        type="submit"
                    >
                        Masuk ke Dashboard
                    </button>

                </form>


                <!-- DEFAULT ACCOUNT -->
                <div class="ppdb-admin-login__account">

                    <div class="ppdb-admin-login__account-title">
                        <span class="ppdb-admin-login__account-title-icon">
                            i
                        </span>
                        Akun Default
                    </div>

                    <p>
                        Email:
                        <strong>
                            admin@tkharapanbunda.sch.id
                        </strong>
                    </p>

                    <p>
                        Password:
                        <strong>
                            admin123
                        </strong>
                    </p>

                    <p class="ppdb-admin-login__warning">
                        Disarankan mengganti password setelah login pertama.
                    </p>

                </div>


                <div class="ppdb-admin-login__back">
                    <a href="../login.php">
                        ← Login sebagai Orang Tua
                    </a>
                </div>

            </div>

        </div>

    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
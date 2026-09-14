<?php
declare(strict_types=1);

require __DIR__ . '/includes/auth.php';

$pageTitle = 'Buat Akun - TK Harapan Bunda';
$activePage = 'daftar';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = post('nama');
    $email = post('email');
    $pass = post('password');
    $confirm = post('confirm_password');

    if ($nama === '') {
        $errors[] = 'Nama wajib diisi.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }

    if (strlen($pass) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }

    if ($pass !== $confirm) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }

    if (!$errors) {
        try {

            $stmt = $pdo->prepare(
                'INSERT INTO users (nama,email,password_hash,role)
                 VALUES (?,?,?,?)'
            );

            $stmt->execute([
                $nama,
                $email,
                password_hash($pass, PASSWORD_DEFAULT),
                'orang_tua'
            ]);

            flash(
                'success',
                'Akun berhasil dibuat. Silakan login.'
            );

            redirect('login.php');

        } catch (PDOException $e) {

            $errors[] = 'Email sudah digunakan.';
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<style>
/* =========================================================
   REGISTER
   Tema Rapor & Kartu Pelajar
   ========================================================= */

.ppdb-register-page {
    padding: 55px 0 85px;
    background:
        linear-gradient(rgba(231, 223, 206, .45) 1px, transparent 1px),
        #FBF7EF;
    background-size: 100% 34px;
}

.ppdb-register-wrap {
    max-width: 900px;
    margin: 0 auto;
}

.ppdb-register-grid {
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

.ppdb-register-info {
    position: relative;
    overflow: hidden;
    padding: 42px 34px;
    background: #2F4538;
    color: #fff;
}

.ppdb-register-info::before {
    content: "";
    position: absolute;
    width: 240px;
    height: 240px;
    right: -115px;
    bottom: -115px;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 50%;
    box-shadow:
        0 0 0 28px rgba(255,255,255,.035),
        0 0 0 56px rgba(255,255,255,.025);
}

.ppdb-register-info-inner {
    position: relative;
    z-index: 1;
}

.ppdb-register-label {
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

.ppdb-register-label svg {
    width: 16px;
    height: 16px;
}

.ppdb-register-info h2 {
    margin: 0;
    color: #FFFDF8;
    font-family: "Fraunces", Georgia, serif;
    font-size: 31px;
    line-height: 1.15;
}

.ppdb-register-description {
    margin: 13px 0 29px;
    color: #D6E0D8;
    font-size: 13px;
    line-height: 1.75;
}

/* ---------- Alur ---------- */

.ppdb-register-flow {
    display: grid;
    gap: 14px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.ppdb-register-flow li {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 11px;
    color: #E4EBE5;
    font-size: 12px;
    line-height: 1.5;
}

.ppdb-register-step {
    width: 25px;
    height: 25px;
    flex: 0 0 25px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(255,255,255,.16);
    border-radius: 8px;
    background: rgba(255,255,255,.09);
    color: #E2EEE3;
    font-size: 10px;
    font-weight: 800;
}

.ppdb-register-flow strong {
    display: block;
    margin-bottom: 2px;
    color: #FFFDF8;
    font-size: 12px;
}

/* =========================================================
   FORM PANEL
   ========================================================= */

.ppdb-register-form-panel {
    padding: 42px 43px;
}

.ppdb-register-heading {
    margin-bottom: 24px;
}

.ppdb-register-heading h1 {
    margin: 0;
    color: #2F4538;
    font-family: "Fraunces", Georgia, serif;
    font-size: 29px;
    line-height: 1.15;
}

.ppdb-register-heading p {
    margin: 7px 0 0;
    color: #777B6E;
    font-size: 13px;
    line-height: 1.6;
}

/* =========================================================
   ALERT
   ========================================================= */

.ppdb-register-alert {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    margin-bottom: 18px;
    padding: 12px 14px;
    border: 1px solid #E8C9C5;
    border-radius: 11px;
    background: #F8E7E4;
    color: #A83B3B;
    font-size: 12px;
    line-height: 1.6;
}

.ppdb-register-alert svg {
    width: 17px;
    height: 17px;
    flex: 0 0 17px;
    margin-top: 1px;
}

.ppdb-register-alert ul {
    margin: 0;
    padding-left: 17px;
}

.ppdb-register-alert li + li {
    margin-top: 3px;
}

/* =========================================================
   FORM
   ========================================================= */

.ppdb-register-form {
    display: grid;
    gap: 17px;
}

.ppdb-register-field {
    display: block;
}

.ppdb-register-field > span {
    display: block;
    margin-bottom: 7px;
    color: #3E453B;
    font-size: 12px;
    font-weight: 800;
}

.ppdb-register-input-wrap {
    position: relative;
}

.ppdb-register-input-wrap > svg {
    position: absolute;
    left: 13px;
    top: 50%;
    width: 17px;
    height: 17px;
    transform: translateY(-50%);
    color: #8B9083;
    pointer-events: none;
}

.ppdb-register-field input {
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

.ppdb-register-field input::placeholder {
    color: #A4A69D;
}

.ppdb-register-field input:focus {
    border-color: #718B78;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(63,126,82,.08);
}

/* ---------- Password Row ---------- */

.ppdb-register-password-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 13px;
}

/* =========================================================
   SUBMIT
   ========================================================= */

.ppdb-register-submit {
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

.ppdb-register-submit:hover {
    transform: translateY(-1px);
    background: #3E5A48;
    box-shadow: 0 9px 20px rgba(47,69,56,.2);
}

.ppdb-register-submit svg {
    width: 17px;
    height: 17px;
    margin-right: 6px;
    vertical-align: -3px;
}

/* =========================================================
   LOGIN LINK
   ========================================================= */

.ppdb-register-login {
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

.ppdb-register-login a {
    color: #3F7E52;
    font-weight: 800;
    text-decoration: none;
}

.ppdb-register-login a:hover {
    text-decoration: underline;
}

/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 760px) {

    .ppdb-register-page {
        padding: 30px 0 60px;
    }

    .ppdb-register-grid {
        grid-template-columns: 1fr;
        border-radius: 19px;
    }

    .ppdb-register-info {
        padding: 27px 23px;
    }

    .ppdb-register-info h2 {
        font-size: 27px;
    }

    .ppdb-register-description {
        margin-bottom: 20px;
    }

    .ppdb-register-flow {
        grid-template-columns: 1fr 1fr;
    }

    .ppdb-register-form-panel {
        padding: 30px 23px;
    }
}

@media (max-width: 480px) {

    .ppdb-register-flow {
        grid-template-columns: 1fr;
    }

    .ppdb-register-password-row {
        grid-template-columns: 1fr;
        gap: 17px;
    }

    .ppdb-register-heading h1 {
        font-size: 26px;
    }
}
</style>

<section class="ppdb-register-page">
    <div class="container ppdb-register-wrap">

        <div class="ppdb-register-grid">

            <!-- PANEL INFORMASI -->
            <aside class="ppdb-register-info">

                <div class="ppdb-register-info-inner">

                    <div class="ppdb-register-label">

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

                        Pendaftaran PPDB

                    </div>

                    <h2>Mulai dari Sini</h2>

                    <p class="ppdb-register-description">
                        Buat akun orang tua terlebih dahulu.
                        Setelah itu, Anda dapat mengisi data calon peserta
                        didik dan memantau proses pendaftaran.
                    </p>

                    <ul class="ppdb-register-flow">

                        <li>
                            <span class="ppdb-register-step">01</span>
                            <div>
                                <strong>Buat Akun</strong>
                                Daftarkan akun orang tua.
                            </div>
                        </li>

                        <li>
                            <span class="ppdb-register-step">02</span>
                            <div>
                                <strong>Isi Data</strong>
                                Lengkapi data calon peserta didik.
                            </div>
                        </li>

                        <li>
                            <span class="ppdb-register-step">03</span>
                            <div>
                                <strong>Kirim Pendaftaran</strong>
                                Periksa data lalu kirim.
                            </div>
                        </li>

                        <li>
                            <span class="ppdb-register-step">04</span>
                            <div>
                                <strong>Pantau Status</strong>
                                Cek perkembangan pendaftaran.
                            </div>
                        </li>

                    </ul>

                </div>

            </aside>


            <!-- FORM -->
            <div class="ppdb-register-form-panel">

                <div class="ppdb-register-heading">
                    <h1>Buat Akun Orang Tua</h1>
                    <p>
                        Isi data berikut untuk membuat akun PPDB.
                    </p>
                </div>


                <?php if ($errors): ?>

                    <div class="ppdb-register-alert">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M10.3 3.7 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z"/>
                            <path d="M12 9v4"/>
                            <path d="M12 17h.01"/>
                        </svg>

                        <ul>
                            <?php foreach ($errors as $er): ?>
                                <li><?= e($er) ?></li>
                            <?php endforeach; ?>
                        </ul>

                    </div>

                <?php endif; ?>


                <form class="ppdb-register-form" method="post">

                    <!-- NAMA -->

                    <label class="ppdb-register-field">

                        <span>Nama Lengkap Orang Tua</span>

                        <div class="ppdb-register-input-wrap">

                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <circle cx="12" cy="8" r="4"/>
                                <path d="M4 21a8 8 0 0 1 16 0"/>
                            </svg>

                            <input
                                name="nama"
                                type="text"
                                placeholder="Nama lengkap orang tua"
                                value="<?= e($_POST['nama'] ?? '') ?>"
                                autocomplete="name"
                                required
                            >

                        </div>

                    </label>


                    <!-- EMAIL -->

                    <label class="ppdb-register-field">

                        <span>Email</span>

                        <div class="ppdb-register-input-wrap">

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
                                value="<?= e($_POST['email'] ?? '') ?>"
                                autocomplete="email"
                                required
                            >

                        </div>

                    </label>


                    <!-- PASSWORD -->

                    <div class="ppdb-register-password-row">

                        <label class="ppdb-register-field">

                            <span>Password</span>

                            <div class="ppdb-register-input-wrap">

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
                                    placeholder="Minimal 6 karakter"
                                    autocomplete="new-password"
                                    required
                                >

                            </div>

                        </label>


                        <label class="ppdb-register-field">

                            <span>Konfirmasi Password</span>

                            <div class="ppdb-register-input-wrap">

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
                                    name="confirm_password"
                                    placeholder="Ulangi password"
                                    autocomplete="new-password"
                                    required
                                >

                            </div>

                        </label>

                    </div>


                    <!-- SUBMIT -->

                    <button class="ppdb-register-submit" type="submit">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M15 12H3"/>
                            <path d="m10 7-5 5 5 5"/>
                            <path d="M21 19V5a2 2 0 0 0-2-2h-6"/>
                        </svg>

                        Buat Akun

                    </button>

                </form>


                <div class="ppdb-register-login">
                    Sudah punya akun?
                    <a href="login.php">Login di sini</a>
                </div>

            </div>

        </div>

    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
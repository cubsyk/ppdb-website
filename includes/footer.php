</main>

<style>
/* =========================================================
   FOOTER
   Tema Rapor & Kartu Pelajar
   ========================================================= */

.ppdb-site-footer {
    position: relative;
    overflow: hidden;
    border-top: 1px solid #D9D1BF;
    background: #2F4538;
    color: #DCE5DE;
}

.ppdb-site-footer::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    height: 4px;
    background: #3F7E52;
}

.ppdb-footer-container {
    width: min(1180px, calc(100% - 40px));
    margin: 0 auto;
}

/* ---------- Main Footer ---------- */

.ppdb-footer-main {
    display: grid;
    grid-template-columns: 1.3fr .7fr;
    gap: 50px;
    padding: 42px 0 34px;
}

/* ---------- Brand ---------- */

.ppdb-footer-brand {
    display: flex;
    align-items: flex-start;
    gap: 13px;
}

.ppdb-footer-logo {
    width: 47px;
    height: 47px;
    flex: 0 0 47px;
    display: grid;
    place-items: center;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 13px;
    background: rgba(255,255,255,.08);
}

.ppdb-footer-logo img {
    width: 42px;
    height: 42px;
    object-fit: contain;
}

.ppdb-footer-brand strong {
    display: block;
    margin-top: 1px;
    color: #FFFDF8;
    font-family: "Fraunces", Georgia, serif;
    font-size: 19px;
    line-height: 1.2;
}

.ppdb-footer-brand p {
    margin: 6px 0 0;
    color: #C5D2C8;
    font-size: 11px;
    line-height: 1.7;
}

/* ---------- Footer Contact ---------- */

.ppdb-footer-contact {
    padding-left: 25px;
    border-left: 1px solid rgba(255,255,255,.12);
}

.ppdb-footer-heading {
    margin: 0 0 13px;
    color: #FFFDF8;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.ppdb-footer-contact-item {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    margin-bottom: 10px;
    color: #C5D2C8;
    font-size: 11px;
    line-height: 1.6;
}

.ppdb-footer-contact-item svg {
    width: 16px;
    height: 16px;
    flex: 0 0 16px;
    margin-top: 1px;
    color: #AFC5B2;
}

/* ---------- Bottom ---------- */

.ppdb-footer-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 16px 0 18px;
    border-top: 1px solid rgba(255,255,255,.1);
}

.ppdb-footer-bottom p {
    margin: 0;
    color: #AEBDB1;
    font-size: 10px;
    line-height: 1.5;
}

.ppdb-footer-note {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.ppdb-footer-note svg {
    width: 13px;
    height: 13px;
    color: #AFC5B2;
}

/* ---------- Responsive ---------- */

@media (max-width: 650px) {

    .ppdb-footer-container {
        width: calc(100% - 30px);
    }

    .ppdb-footer-main {
        grid-template-columns: 1fr;
        gap: 27px;
        padding: 34px 0 27px;
    }

    .ppdb-footer-contact {
        padding: 22px 0 0;
        border-top: 1px solid rgba(255,255,255,.12);
        border-left: 0;
    }

    .ppdb-footer-bottom {
        align-items: flex-start;
        flex-direction: column;
        gap: 7px;
    }
}
</style>

<footer class="site-footer ppdb-site-footer">

    <div class="ppdb-footer-container">

        <div class="ppdb-footer-main">

            <!-- IDENTITAS SEKOLAH -->
            <div class="ppdb-footer-brand">

                <div class="ppdb-footer-logo">

                    <img
                        src="<?= $cssBase ?? '' ?>images/logo.png"
                        alt="Logo TK Harapan Bunda"
                        width="42"
                        height="42"
                    >

                </div>

                <div>

                    <strong>
                        TK Harapan Bunda
                    </strong>

                    <p>
                        Belajar, bermain, berkarakter
                    </p>

                    <p>
                        Jl. H. Misan I No.7 RT.02/04 Kukusan, Beji Depok 164225
                    </p>

                </div>

            </div>


            <!-- KONTAK -->
            <div class="ppdb-footer-contact">

                <div class="ppdb-footer-heading">
                    Informasi PPDB
                </div>

                <div class="ppdb-footer-contact-item">

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
                                 12.84 12.84 0 0 1 .7 2.81
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


                <div class="ppdb-footer-contact-item">

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
                            y="5"
                            width="18"
                            height="14"
                            rx="2"
                        />

                        <path d="m3 7 9 6 9-6"/>

                    </svg>

                    <span>
                        ppdb@tkharapanbunda.sch.id
                    </span>

                </div>

            </div>

        </div>


        <!-- COPYRIGHT -->
        <div class="ppdb-footer-bottom">

            <p>
                © <?= date('Y') ?> TK Harapan Bunda. Semua hak dilindungi.
            </p>

            <p class="ppdb-footer-note">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M12 3 4 7v5c0 5 3.5 8 8 9
                             4.5-1 8-4 8-9V7l-8-4Z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>

                Portal PPDB TK Harapan Bunda

            </p>

        </div>

    </div>

</footer>

<script src="<?= $cssBase ?? '' ?>assets/js/script.js"></script>

</body>
</html>
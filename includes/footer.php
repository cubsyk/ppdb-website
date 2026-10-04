</main>

<style>
/* =========================================================
   FOOTER
   Full Width Warm PPDB Theme
   ========================================================= */

.ppdb-site-footer {
    position: relative;
    width: 100%;
    overflow: hidden;
    margin-top: 0;

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


/* =========================================================
   CONTAINER
   ========================================================= */

.ppdb-footer-container {
    width: 100%;
    max-width: none;

    margin: 0;

    padding-left: 30px;
    padding-right: 30px;

    box-sizing: border-box;
}


/* =========================================================
   MAIN FOOTER
   ========================================================= */

.ppdb-footer-main {
    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        minmax(0, 1fr);

    gap: 40px;

    padding: 42px 0 36px;
}


/* =========================================================
   BRAND / IDENTITAS SEKOLAH
   ========================================================= */

.ppdb-footer-brand {
    display: flex;
    align-items: flex-start;

    gap: 14px;

    min-width: 0;
}

.ppdb-footer-logo {
    width: 48px;
    height: 48px;

    flex: 0 0 48px;

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

.ppdb-footer-brand-info {
    min-width: 0;
}

.ppdb-footer-brand strong {
    display: block;

    margin: 1px 0 6px;

    color: #FFFDF8;

    font-family: "Fraunces", Georgia, serif;

    font-size: 20px;
    line-height: 1.2;
}

.ppdb-footer-brand p {
    margin: 0;

    color: #C5D2C8;

    font-size: 11px;
    line-height: 1.65;
}

.ppdb-footer-brand p + p {
    margin-top: 4px;
}


/* =========================================================
   INFORMASI PPDB
   ========================================================= */

.ppdb-footer-contact {
    width: min(100%, 390px);

    justify-self: end;

    min-width: 0;

    padding-left: 28px;

    border-left: 1px solid rgba(255,255,255,.13);

    box-sizing: border-box;
}

.ppdb-footer-heading {
    margin: 1px 0 15px;

    color: #FFFDF8;

    font-size: 11px;
    font-weight: 800;

    letter-spacing: .08em;

    text-transform: uppercase;
}

.ppdb-footer-contact-list {
    display: grid;

    gap: 11px;
}

.ppdb-footer-contact-item {
    display: flex;

    align-items: flex-start;

    gap: 10px;

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


/* =========================================================
   FOOTER BOTTOM
   ========================================================= */

.ppdb-footer-bottom {
    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        minmax(0, 1fr);

    gap: 40px;

    align-items: center;

    padding: 16px 0 18px;

    border-top: 1px solid rgba(255,255,255,.10);
}

.ppdb-footer-bottom p {
    margin: 0;

    color: #AEBDB1;

    font-size: 10px;

    line-height: 1.5;
}


/* PORTAL DI KANAN */

.ppdb-footer-note {
    display: inline-flex;

    align-items: center;

    justify-self: end;

    gap: 6px;

    white-space: nowrap;
}

.ppdb-footer-note svg {
    width: 13px;
    height: 13px;

    color: #AFC5B2;
}


/* =========================================================
   DESKTOP LEBIH LEBAR
   ========================================================= */

@media (min-width: 1200px) {

    .ppdb-footer-container {
        padding-left: 42px;
        padding-right: 42px;
    }

    .ppdb-footer-main {
        grid-template-columns:
            minmax(0, 1fr)
            minmax(0, 1fr);

        gap: 80px;
    }

    .ppdb-footer-bottom {
        gap: 80px;
    }
}


/* =========================================================
   TABLET
   ========================================================= */

@media (max-width: 760px) {

    .ppdb-footer-container {
        width: 100%;

        padding-left: 20px;
        padding-right: 20px;
    }

    .ppdb-footer-main {
        grid-template-columns: 1fr;

        gap: 26px;

        padding: 34px 0 28px;
    }

    .ppdb-footer-contact {
        width: 100%;

        justify-self: stretch;

        padding: 24px 0 0;

        border-top: 1px solid rgba(255,255,255,.12);

        border-left: 0;
    }

    .ppdb-footer-bottom {
        grid-template-columns: 1fr;

        gap: 8px;

        align-items: start;
    }

    .ppdb-footer-note {
        justify-self: start;
    }
}


/* =========================================================
   HP KECIL
   ========================================================= */

@media (max-width: 480px) {

    .ppdb-footer-container {
        padding-left: 16px;
        padding-right: 16px;
    }

    .ppdb-footer-brand {
        gap: 11px;
    }

    .ppdb-footer-logo {
        width: 44px;
        height: 44px;

        flex-basis: 44px;
    }

    .ppdb-footer-logo img {
        width: 38px;
        height: 38px;
    }

    .ppdb-footer-brand strong {
        font-size: 18px;
    }

    .ppdb-footer-brand p,
    .ppdb-footer-contact-item {
        font-size: 10.5px;
    }
}
</style>


<footer class="site-footer ppdb-site-footer">

    <div class="ppdb-footer-container">

        <!-- =================================================
             MAIN FOOTER
             ================================================= -->

        <div class="ppdb-footer-main">


            <!-- =================================================
                 IDENTITAS SEKOLAH
                 ================================================= -->

            <div class="ppdb-footer-brand">

                <div class="ppdb-footer-logo">

                    <img
                        src="<?= $cssBase ?? '' ?>images/logo.png"
                        alt="Logo TK Harapan Bunda"
                        width="42"
                        height="42"
                    >

                </div>


                <div class="ppdb-footer-brand-info">

                    <strong>
                        TK Harapan Bunda
                    </strong>

                    <p>
                        Belajar, bermain, berkarakter
                    </p>

                    <p>
                        Jl. H. Misan I No.7 RT.02/04 Kukusan,
                        Beji Depok 164225
                    </p>

                </div>

            </div>


            <!-- =================================================
                 INFORMASI PPDB
                 ================================================= -->

            <div class="ppdb-footer-contact">

                <div class="ppdb-footer-heading">
                    Informasi PPDB
                </div>


                <div class="ppdb-footer-contact-list">


                    <!-- TELEPON -->

                    <div class="ppdb-footer-contact-item">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2
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
                                A2 2 0 0 1 22 16.92z"
                            />

                        </svg>


                        <span>
                            Telp: (021) 786 9909
                        </span>

                    </div>


                    <!-- EMAIL -->

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

                            <path
                                d="m3 7 9 6 9-6"
                            />

                        </svg>


                        <span>
                            ppdb@tkharapanbunda.sch.id
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- =================================================
             FOOTER BOTTOM
             ================================================= -->

        <div class="ppdb-footer-bottom">


            <!-- KIRI -->

            <p>
                © <?= date('Y') ?> TK Harapan Bunda.
                Semua hak dilindungi.
            </p>


            <!-- KANAN -->

            <p class="ppdb-footer-note">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >

                    <path
                        d="M12 3 4 7v5c0 5 3.5 8 8 9
                        4.5-1 8-4 8-9V7l-8-4Z"
                    />

                    <path
                        d="m9 12 2 2 4-4"
                    />

                </svg>


                Portal PPDB TK Harapan Bunda

            </p>

        </div>

    </div>

</footer>


<script src="<?= $cssBase ?? '' ?>assets/js/script.js"></script>

</body>
</html>
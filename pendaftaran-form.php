<?php
declare(strict_types=1);

require __DIR__ . '/includes/auth.php';
require_login();

date_default_timezone_set('Asia/Jakarta');

$user = current_user();
$errors = [];

/* =========================================================
   AMBIL PENDAFTARAN TERAKHIR
   ========================================================= */

$stmt = $pdo->prepare(
    'SELECT * FROM pendaftaran
     WHERE user_id=?
     ORDER BY created_at DESC
     LIMIT 1'
);

$stmt->execute([$user['id']]);
$pendaftaran = $stmt->fetch();

if (!$pendaftaran) {

    $noPend = generate_registration_number();

    $pdo->prepare(
        'INSERT INTO pendaftaran
         (user_id, nomor_pendaftaran, status)
         VALUES (?,?,?)'
    )->execute([
        $user['id'],
        $noPend,
        'draft'
    ]);

    $stmt->execute([$user['id']]);
    $pendaftaran = $stmt->fetch();
}

$pid = $pendaftaran['id'];


/* =========================================================
   DATA CALON SISWA
   ========================================================= */

$csRow = $pdo->prepare(
    'SELECT * FROM calon_siswa
     WHERE pendaftaran_id=?'
);

$csRow->execute([$pid]);
$cs = $csRow->fetch() ?: [];


/* =========================================================
   DATA ORANG TUA
   ========================================================= */

$otRow = $pdo->prepare(
    'SELECT * FROM orang_tua
     WHERE pendaftaran_id=?'
);

$otRow->execute([$pid]);
$ot = $otRow->fetch() ?: [];


/* =========================================================
   PROSES FORM
   ========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* -----------------------------------------------------
       DATA CALON SISWA
       ----------------------------------------------------- */

    $csData = [
        'nama_lengkap'       => post('nama_lengkap'),
        'nama_panggilan'     => post('nama_panggilan'),
        'tempat_lahir'       => post('tempat_lahir'),
        'tanggal_lahir'      => post('tanggal_lahir'),
        'nik'                => post('nik'),
        'nomor_kk'           => post('nomor_kk'),
        'agama'              => post('agama'),
        'anak_ke'            => post('anak_ke'),
        'jumlah_saudara'     => post('jumlah_saudara'),
        'berat_badan'        => post('berat_badan'),
        'tinggi_badan'       => post('tinggi_badan'),
        'lingkar_kepala'     => post('lingkar_kepala'),
        'penghasilan_orang_tua' => post('penghasilan_orang_tua'),
        'jarak_sekolah'      => post('jarak_sekolah'),
        'alamat'             => post('alamat'),
        'jenis_kelamin'      => post('jenis_kelamin')
    ];


    /* -----------------------------------------------------
       DATA AYAH & IBU
       ----------------------------------------------------- */

    $otData = [

        // AYAH
        'nama_ayah'          => post('nama_ayah'),
        'tempat_lahir_ayah'  => post('tempat_lahir_ayah'),
        'tanggal_lahir_ayah' => post('tanggal_lahir_ayah'),
        'nik_ayah'           => post('nik_ayah'),
        'agama_ayah'         => post('agama_ayah'),
        'pendidikan_ayah'    => post('pendidikan_ayah'),
        'pekerjaan_ayah'     => post('pekerjaan_ayah'),
        'no_hp_ayah'         => post('no_hp_ayah'),

        // IBU
        'nama_ibu'           => post('nama_ibu'),
        'tempat_lahir_ibu'   => post('tempat_lahir_ibu'),
        'tanggal_lahir_ibu'  => post('tanggal_lahir_ibu'),
        'nik_ibu'            => post('nik_ibu'),
        'agama_ibu'          => post('agama_ibu'),
        'pendidikan_ibu'     => post('pendidikan_ibu'),
        'pekerjaan_ibu'      => post('pekerjaan_ibu'),
        'no_hp_ibu'          => post('no_hp_ibu')
    ];


    $kelompok = post('kelompok');


    /* =====================================================
       VALIDASI CALON SISWA
       ===================================================== */

    $req = [

        'nama_lengkap'          => 'Nama lengkap',
        'nama_panggilan'        => 'Nama panggilan',
        'tempat_lahir'          => 'Tempat lahir',
        'tanggal_lahir'         => 'Tanggal lahir',
        'nik'                   => 'NIK anak',
        'nomor_kk'              => 'Nomor kartu keluarga',
        'agama'                 => 'Agama',
        'anak_ke'               => 'Anak ke',
        'jumlah_saudara'        => 'Jumlah saudara kandung',
        'berat_badan'           => 'Berat badan',
        'tinggi_badan'          => 'Tinggi badan',
        'lingkar_kepala'        => 'Lingkar kepala',
        'penghasilan_orang_tua'  => 'Penghasilan orang tua',
        'jarak_sekolah'         => 'Jarak tempuh sekolah',
        'alamat'                => 'Alamat rumah',
        'jenis_kelamin'         => 'Jenis kelamin'
    ];

    foreach ($req as $key => $label) {

        if (($csData[$key] ?? '') === '') {
            $errors[] = $label . ' wajib diisi.';
        }
    }


    /* =====================================================
       VALIDASI AYAH & IBU
       ===================================================== */

    $reqOt = [

        'nama_ayah'          => 'Nama ayah',
        'tempat_lahir_ayah'  => 'Tempat lahir ayah',
        'tanggal_lahir_ayah' => 'Tanggal lahir ayah',
        'nik_ayah'           => 'NIK ayah',
        'agama_ayah'         => 'Agama ayah',
        'pendidikan_ayah'    => 'Pendidikan ayah',
        'pekerjaan_ayah'     => 'Pekerjaan ayah',
        'no_hp_ayah'         => 'Nomor telepon ayah',

        'nama_ibu'           => 'Nama ibu',
        'tempat_lahir_ibu'   => 'Tempat lahir ibu',
        'tanggal_lahir_ibu'  => 'Tanggal lahir ibu',
        'nik_ibu'            => 'NIK ibu',
        'agama_ibu'          => 'Agama ibu',
        'pendidikan_ibu'     => 'Pendidikan ibu',
        'pekerjaan_ibu'      => 'Pekerjaan ibu',
        'no_hp_ibu'          => 'Nomor telepon ibu'
    ];

    foreach ($reqOt as $key => $label) {

        if (($otData[$key] ?? '') === '') {
            $errors[] = $label . ' wajib diisi.';
        }
    }


    /* =====================================================
       VALIDASI kelompok
       ===================================================== */

    $kelompoks = [
        'Kelompok A',
        'Kelompok B',
        'Daycare Ceria'
    ];

    if (!in_array($kelompok, $kelompoks, true)) {
        $errors[] = 'kelompok pendaftaran wajib dipilih.';
    }


    /* =====================================================
       SIMPAN DATA
       ===================================================== */

    if (!$errors) {

        /* -------------------------------------------------
           CALON SISWA
           ------------------------------------------------- */

        if ($cs) {

            $stmt = $pdo->prepare(
                'UPDATE calon_siswa SET
                    nama_lengkap=?,
                    nama_panggilan=?,
                    tempat_lahir=?,
                    tanggal_lahir=?,
                    nik=?,
                    nomor_kk=?,
                    agama=?,
                    anak_ke=?,
                    jumlah_saudara=?,
                    berat_badan=?,
                    tinggi_badan=?,
                    lingkar_kepala=?,
                    penghasilan_orang_tua=?,
                    jarak_sekolah=?,
                    alamat=?,
                    jenis_kelamin=?
                 WHERE pendaftaran_id=?'
            );

            $stmt->execute([

                $csData['nama_lengkap'],
                $csData['nama_panggilan'],
                $csData['tempat_lahir'],
                $csData['tanggal_lahir'],
                $csData['nik'],
                $csData['nomor_kk'],
                $csData['agama'],
                $csData['anak_ke'],
                $csData['jumlah_saudara'],
                $csData['berat_badan'],
                $csData['tinggi_badan'],
                $csData['lingkar_kepala'],
                $csData['penghasilan_orang_tua'],
                $csData['jarak_sekolah'],
                $csData['alamat'],
                $csData['jenis_kelamin'],
                $pid
            ]);

        } else {

            $stmt = $pdo->prepare(
                'INSERT INTO calon_siswa
                (
                    pendaftaran_id,
                    nama_lengkap,
                    nama_panggilan,
                    tempat_lahir,
                    tanggal_lahir,
                    nik,
                    nomor_kk,
                    agama,
                    anak_ke,
                    jumlah_saudara,
                    berat_badan,
                    tinggi_badan,
                    lingkar_kepala,
                    penghasilan_orang_tua,
                    jarak_sekolah,
                    alamat,
                    jenis_kelamin
                )
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
            );

            $stmt->execute([

                $pid,
                $csData['nama_lengkap'],
                $csData['nama_panggilan'],
                $csData['tempat_lahir'],
                $csData['tanggal_lahir'],
                $csData['nik'],
                $csData['nomor_kk'],
                $csData['agama'],
                $csData['anak_ke'],
                $csData['jumlah_saudara'],
                $csData['berat_badan'],
                $csData['tinggi_badan'],
                $csData['lingkar_kepala'],
                $csData['penghasilan_orang_tua'],
                $csData['jarak_sekolah'],
                $csData['alamat'],
                $csData['jenis_kelamin']
            ]);
        }


        /* -------------------------------------------------
           ORANG TUA
           ------------------------------------------------- */

        if ($ot) {

            $stmt = $pdo->prepare(
                'UPDATE orang_tua SET

                    nama_ayah=?,
                    tempat_lahir_ayah=?,
                    tanggal_lahir_ayah=?,
                    nik_ayah=?,
                    agama_ayah=?,
                    pendidikan_ayah=?,
                    pekerjaan_ayah=?,
                    no_hp_ayah=?,

                    nama_ibu=?,
                    tempat_lahir_ibu=?,
                    tanggal_lahir_ibu=?,
                    nik_ibu=?,
                    agama_ibu=?,
                    pendidikan_ibu=?,
                    pekerjaan_ibu=?,
                    no_hp_ibu=?

                 WHERE pendaftaran_id=?'
            );

            $stmt->execute([

                $otData['nama_ayah'],
                $otData['tempat_lahir_ayah'],
                $otData['tanggal_lahir_ayah'],
                $otData['nik_ayah'],
                $otData['agama_ayah'],
                $otData['pendidikan_ayah'],
                $otData['pekerjaan_ayah'],
                $otData['no_hp_ayah'],

                $otData['nama_ibu'],
                $otData['tempat_lahir_ibu'],
                $otData['tanggal_lahir_ibu'],
                $otData['nik_ibu'],
                $otData['agama_ibu'],
                $otData['pendidikan_ibu'],
                $otData['pekerjaan_ibu'],
                $otData['no_hp_ibu'],

                $pid
            ]);

        } else {

            $stmt = $pdo->prepare(
                'INSERT INTO orang_tua
                (
                    pendaftaran_id,

                    nama_ayah,
                    tempat_lahir_ayah,
                    tanggal_lahir_ayah,
                    nik_ayah,
                    agama_ayah,
                    pendidikan_ayah,
                    pekerjaan_ayah,
                    no_hp_ayah,

                    nama_ibu,
                    tempat_lahir_ibu,
                    tanggal_lahir_ibu,
                    nik_ibu,
                    agama_ibu,
                    pendidikan_ibu,
                    pekerjaan_ibu,
                    no_hp_ibu
                )
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
            );

            $stmt->execute([

                $pid,

                $otData['nama_ayah'],
                $otData['tempat_lahir_ayah'],
                $otData['tanggal_lahir_ayah'],
                $otData['nik_ayah'],
                $otData['agama_ayah'],
                $otData['pendidikan_ayah'],
                $otData['pekerjaan_ayah'],
                $otData['no_hp_ayah'],

                $otData['nama_ibu'],
                $otData['tempat_lahir_ibu'],
                $otData['tanggal_lahir_ibu'],
                $otData['nik_ibu'],
                $otData['agama_ibu'],
                $otData['pendidikan_ibu'],
                $otData['pekerjaan_ibu'],
                $otData['no_hp_ibu']
            ]);
        }


        /* -------------------------------------------------
           kelompok PENDAFTARAN
           ------------------------------------------------- */

        $pdo->prepare(
            'UPDATE pendaftaran
             SET kelompok=?
             WHERE id=?'
        )->execute([
            $kelompok,
            $pid
        ]);


        flash(
            'success',
            'Data pendaftaran berhasil disimpan.'
        );

        redirect('dashboard.php');
    }
}


/* =========================================================
   HELPER VALUE
   ========================================================= */

$p = function ($key, $source = []) {

    return e(
        $_POST[$key]
        ?? ($source[$key] ?? '')
    );
};


$pageTitle = 'Form Pendaftaran - TK Harapan Bunda';
$activePage = 'dashboard';

require __DIR__ . '/includes/header.php';

?>

<style>

/* =========================================================
   PPDB FORM
   TEMA RAPOR & KARTU PELAJAR
   ========================================================= */

.ppdb-form-page {

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

    background:
        linear-gradient(
            rgba(231,223,206,.25) 1px,
            transparent 1px
        );

    background-size: 100% 36px;
    padding-bottom: 70px;
}


/* =========================================================
   HEADER
   ========================================================= */

.ppdb-form-page__hero {

    background: var(--paper);
    border-bottom: 1px solid var(--paper-line);

    padding: 48px 0;

    position: relative;
    overflow: hidden;
}

.ppdb-form-page__hero::after {

    content: 'FORM';

    position: absolute;

    right: -10px;
    bottom: -30px;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 125px;
    font-weight: 700;

    color: rgba(47,69,56,.045);

    pointer-events: none;
}

.ppdb-form-page__eyebrow {

    display: flex;
    align-items: center;
    gap: 9px;

    color: var(--leaf);

    font-size: 12px;
    font-weight: 700;

    letter-spacing: .08em;
    text-transform: uppercase;

    margin: 0 0 10px;

    position: relative;
    z-index: 1;
}

.ppdb-form-page__eyebrow::before {

    content: '';

    width: 27px;
    height: 2px;

    background: var(--coral);
}

.ppdb-form-page h1 {

    margin: 0 0 8px;

    color: var(--chalk);

    font-family: 'Fraunces', Georgia, serif;

    font-size: clamp(34px, 5vw, 48px);

    line-height: 1.05;

    position: relative;
    z-index: 1;
}

.ppdb-form-page__hero-text {

    margin: 0;

    color: var(--muted);

    font-size: 14px;

    max-width: 680px;

    line-height: 1.7;

    position: relative;
    z-index: 1;
}


/* =========================================================
   CONTAINER
   ========================================================= */

.ppdb-form-page__content {

    padding-top: 38px;
}


/* =========================================================
   ERROR
   ========================================================= */

.ppdb-form-page__error {

    margin-bottom: 20px;

    padding: 17px 19px;

    background: var(--berry-soft);

    border: 1px solid #E9C8C4;

    border-radius: 13px;

    color: #8A2D2D;
}

.ppdb-form-page__error-title {

    font-weight: 700;

    margin-bottom: 7px;

    font-size: 14px;
}

.ppdb-form-page__error ul {

    margin: 0;
    padding-left: 20px;

    font-size: 13px;

    line-height: 1.7;
}


/* =========================================================
   LAYOUT
   ========================================================= */

.ppdb-form-page__layout {

    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        270px;

    gap: 18px;

    align-items: start;
}


/* =========================================================
   MAIN CARD
   ========================================================= */

.ppdb-form-page__card {

    background: #fff;

    border: 1px solid var(--paper-line);

    border-radius: 16px;

    padding: 30px;
}


/* =========================================================
   SECTION
   ========================================================= */

.ppdb-form-page__section {

    margin-bottom: 40px;
}

.ppdb-form-page__section:last-of-type {

    margin-bottom: 0;
}

.ppdb-form-page__section-heading {

    display: flex;
    align-items: center;

    gap: 13px;

    padding-bottom: 18px;

    margin-bottom: 23px;

    border-bottom: 1px solid var(--paper-line);
}

.ppdb-form-page__section-number {

    width: 39px;
    height: 39px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: var(--leaf-soft);

    color: var(--leaf);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 14px;
    font-weight: 700;
}

.ppdb-form-page__section-heading--parent
.ppdb-form-page__section-number {

    background: var(--amber-soft);
    color: var(--amber);
}

.ppdb-form-page__section-heading h2 {

    margin: 0 0 3px;

    color: var(--chalk);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 23px;
}

.ppdb-form-page__section-heading p {

    margin: 0;

    color: var(--muted);

    font-size: 12.5px;
}


/* =========================================================
   SUB SECTION AYAH / IBU
   ========================================================= */

.ppdb-form-page__parent-box {

    padding: 21px;

    margin-bottom: 20px;

    background: #FCFAF5;

    border: 1px solid var(--paper-line);

    border-radius: 13px;
}

.ppdb-form-page__parent-box:last-child {

    margin-bottom: 0;
}

.ppdb-form-page__parent-title {

    display: flex;
    align-items: center;

    gap: 10px;

    margin: 0 0 19px;

    color: var(--chalk);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 20px;
}

.ppdb-form-page__parent-badge {

    width: 30px;
    height: 30px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: var(--chalk);

    color: #fff;

    font-family: 'Inter', sans-serif;

    font-size: 12px;
    font-weight: 700;
}


/* =========================================================
   GRID
   ========================================================= */

.ppdb-form-page__fields {

    display: grid;

    gap: 17px;
}

.ppdb-form-page__row {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 15px;
}


/* =========================================================
   FIELD
   ========================================================= */

.ppdb-form-page__field {

    display: block;
}

.ppdb-form-page__field > span {

    display: block;

    margin-bottom: 7px;

    color: var(--ink);

    font-size: 13px;

    font-weight: 600;
}

.ppdb-form-page__required {

    color: var(--coral);
}

.ppdb-form-page__field input,
.ppdb-form-page__field select,
.ppdb-form-page__field textarea {

    width: 100%;

    box-sizing: border-box;

    border: 1px solid #DCD5C6;

    background: #FCFAF5;

    color: var(--ink);

    border-radius: 9px;

    padding: 11px 12px;

    font-family: 'Inter', system-ui, sans-serif;

    font-size: 14px;

    outline: none;

    transition:
        border-color .15s ease,
        box-shadow .15s ease,
        background .15s ease;
}

.ppdb-form-page__field input,
.ppdb-form-page__field select {

    height: 43px;
}

.ppdb-form-page__field textarea {

    min-height: 90px;

    resize: vertical;

    line-height: 1.55;
}

.ppdb-form-page__field input:focus,
.ppdb-form-page__field select:focus,
.ppdb-form-page__field textarea:focus {

    border-color: var(--leaf);

    background: #fff;

    box-shadow:
        0 0 0 3px rgba(63,126,82,.09);
}

.ppdb-form-page__field input::placeholder,
.ppdb-form-page__field textarea::placeholder {

    color: #A7A292;
}


/* =========================================================
   UNIT INPUT
   ========================================================= */

.ppdb-form-page__input-unit {

    position: relative;
}

.ppdb-form-page__input-unit input {

    padding-right: 55px;
}

.ppdb-form-page__input-unit span {

    position: absolute;

    right: 12px;
    top: 50%;

    transform: translateY(-50%);

    color: var(--muted);

    font-size: 12px;

    pointer-events: none;
}


/* =========================================================
   kelompok
   ========================================================= */

.ppdb-form-page__kelompok {

    margin-top: 4px;

    padding: 20px;

    background: var(--paper);

    border: 1px solid var(--paper-line);

    border-radius: 12px;
}

.ppdb-form-page__kelompok-label {

    margin: 0 0 9px;

    color: var(--chalk);

    font-size: 13px;

    font-weight: 700;
}

.ppdb-form-page__kelompok-label small {

    color: var(--muted);

    font-weight: 400;
}

.ppdb-form-page__kelompok select {

    width: 100%;

    height: 44px;

    box-sizing: border-box;

    border: 1px solid #DCD5C6;

    background: #fff;

    border-radius: 9px;

    padding: 0 12px;

    color: var(--ink);

    font-family: 'Inter', sans-serif;

    font-size: 14px;
}


/* =========================================================
   SIDEBAR
   ========================================================= */

.ppdb-form-page__side {

    display: grid;

    gap: 16px;

    position: sticky;

    top: 20px;
}

.ppdb-form-page__dark-card {

    background: var(--chalk);

    color: #F3F0E6;

    border-radius: 15px;

    padding: 24px;

    position: relative;

    overflow: hidden;
}

.ppdb-form-page__dark-card::after {

    content: 'PPDB';

    position: absolute;

    right: -10px;
    bottom: -26px;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 72px;

    font-weight: 700;

    color: rgba(255,255,255,.045);
}

.ppdb-form-page__dark-label {

    margin: 0 0 6px;

    color: #BFCBBF;

    font-size: 11px;

    letter-spacing: .08em;

    text-transform: uppercase;
}

.ppdb-form-page__dark-card h3 {

    margin: 0 0 16px;

    color: #F3F0E6;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 20px;
}

.ppdb-form-page__number {

    position: relative;

    z-index: 1;

    padding: 13px 0;

    border-top: 1px solid rgba(255,255,255,.12);

    border-bottom: 1px solid rgba(255,255,255,.12);
}

.ppdb-form-page__number small {

    display: block;

    margin-bottom: 3px;

    color: #BFCBBF;

    font-size: 11px;
}

.ppdb-form-page__number strong {

    color: #fff;

    font-family: 'Fraunces', Georgia, serif;

    font-size: 18px;

    letter-spacing: .02em;
}


/* =========================================================
   INFO CARD
   ========================================================= */

.ppdb-form-page__info {

    background: var(--paper);

    border: 1px solid var(--paper-line);

    border-radius: 15px;

    padding: 21px;
}

.ppdb-form-page__info h3 {

    margin: 0 0 13px;

    color: var(--chalk);

    font-family: 'Fraunces', Georgia, serif;

    font-size: 18px;
}

.ppdb-form-page__info ul {

    list-style: none;

    padding: 0;
    margin: 0;
}

.ppdb-form-page__info li {

    display: flex;

    gap: 9px;

    padding: 9px 0;

    border-top: 1px solid var(--paper-line);

    color: var(--muted);

    font-size: 12.5px;

    line-height: 1.5;
}

.ppdb-form-page__info li:first-child {

    border-top: none;

    padding-top: 0;
}

.ppdb-form-page__check {

    width: 19px;
    height: 19px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: var(--leaf-soft);

    color: var(--leaf);

    font-size: 9px;

    font-weight: 700;
}


/* =========================================================
   ACTION
   ========================================================= */

.ppdb-form-page__actions {

    display: flex;

    gap: 10px;

    flex-wrap: wrap;

    margin-top: 30px;

    padding-top: 23px;

    border-top: 1px solid var(--paper-line);
}

.ppdb-form-page__btn {

    min-height: 45px;

    padding: 0 24px;

    border: none;

    border-radius: 9px;

    background: var(--coral);

    color: #fff;

    font-family: 'Inter', sans-serif;

    font-size: 14px;

    font-weight: 700;

    cursor: pointer;

    text-decoration: none;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    transition:
        transform .18s ease,
        box-shadow .18s ease;
}

.ppdb-form-page__btn:hover {

    transform: translateY(-1px);

    box-shadow:
        0 7px 18px rgba(225,85,46,.18);
}

.ppdb-form-page__btn--outline {

    background: transparent;

    color: var(--chalk);

    border: 1px solid var(--paper-line);
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 900px) {

    .ppdb-form-page__layout {

        grid-template-columns: 1fr;
    }

    .ppdb-form-page__side {

        position: static;

        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 650px) {

    .ppdb-form-page__hero {

        padding: 42px 0;
    }

    .ppdb-form-page__content {

        padding-top: 28px;
    }

    .ppdb-form-page__card {

        padding: 20px;
    }

    .ppdb-form-page__row,
    .ppdb-form-page__side {

        grid-template-columns: 1fr;
    }

    .ppdb-form-page__parent-box {

        padding: 17px;
    }

    .ppdb-form-page__actions {

        flex-direction: column;
    }

    .ppdb-form-page__btn {

        width: 100%;
    }
}

</style>


<main class="ppdb-form-page">

    <!-- =====================================================
         HEADER
         ===================================================== -->

    <section class="ppdb-form-page__hero">

        <div class="container">

            <p class="ppdb-form-page__eyebrow">
                Pendaftaran Peserta Didik Baru
            </p>

            <h1>Formulir Pendaftaran</h1>

            <p class="ppdb-form-page__hero-text">
                Lengkapi data peserta didik dan orang tua sesuai
                dengan dokumen yang dimiliki.
            </p>

        </div>

    </section>


    <!-- =====================================================
         CONTENT
         ===================================================== -->

    <section class="container ppdb-form-page__content">


        <?php if ($errors): ?>

            <div class="ppdb-form-page__error">

                <div class="ppdb-form-page__error-title">
                    Periksa kembali data berikut:
                </div>

                <ul>

                    <?php foreach ($errors as $error): ?>

                        <li><?= e($error) ?></li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>


        <div class="ppdb-form-page__layout">


            <!-- =================================================
                 FORM UTAMA
                 ================================================= -->

            <form method="post" class="ppdb-form-page__card">


                <!-- =================================================
                     01. PESERTA DIDIK
                     ================================================= -->

                <section class="ppdb-form-page__section">

                    <div class="ppdb-form-page__section-heading">

                        <span class="ppdb-form-page__section-number">
                            01
                        </span>

                        <div>

                            <h2>Peserta Didik</h2>

                            <p>
                                Identitas lengkap calon peserta didik.
                            </p>

                        </div>

                    </div>


                    <div class="ppdb-form-page__fields">


                        <!-- Nama -->

                        <div class="ppdb-form-page__row">

                            <label class="ppdb-form-page__field">

                                <span>
                                    Nama lengkap
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <input
                                    type="text"
                                    name="nama_lengkap"
                                    required
                                    value="<?= $p('nama_lengkap', $cs) ?>"
                                    placeholder="Nama lengkap sesuai dokumen"
                                >

                            </label>


                            <label class="ppdb-form-page__field">

                                <span>
                                    Nama panggilan
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <input
                                    type="text"
                                    name="nama_panggilan"
                                    required
                                    value="<?= $p('nama_panggilan', $cs) ?>"
                                    placeholder="Nama panggilan sehari-hari"
                                >

                            </label>

                        </div>


                        <!-- TTL -->

                        <div class="ppdb-form-page__row">

                            <label class="ppdb-form-page__field">

                                <span>
                                    Tempat lahir
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <input
                                    type="text"
                                    name="tempat_lahir"
                                    required
                                    value="<?= $p('tempat_lahir', $cs) ?>"
                                    placeholder="Kota tempat lahir"
                                >

                            </label>


                            <label class="ppdb-form-page__field">

                                <span>
                                    Tanggal lahir
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <input
                                    type="date"
                                    name="tanggal_lahir"
                                    required
                                    value="<?= $p('tanggal_lahir', $cs) ?>"
                                >

                            </label>

                        </div>


                        <!-- NIK & KK -->

                        <div class="ppdb-form-page__row">

                            <label class="ppdb-form-page__field">

                                <span>
                                    NIK anak
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <input
                                    type="text"
                                    name="nik"
                                    required
                                    maxlength="16"
                                    value="<?= $p('nik', $cs) ?>"
                                    placeholder="16 digit NIK"
                                >

                            </label>


                            <label class="ppdb-form-page__field">

                                <span>
                                    Nomor kartu keluarga
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <input
                                    type="text"
                                    name="nomor_kk"
                                    required
                                    maxlength="16"
                                    value="<?= $p('nomor_kk', $cs) ?>"
                                    placeholder="16 digit nomor KK"
                                >

                            </label>

                        </div>


                        <!-- Agama & Jenis Kelamin -->

                        <div class="ppdb-form-page__row">

                            <label class="ppdb-form-page__field">

                                <span>
                                    Agama
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <select name="agama" required>

                                    <option value="">
                                        Pilih agama
                                    </option>

                                    <?php
                                    $agamaList = [
                                        'Islam',
                                        'Kristen Protestan',
                                        'Katolik',
                                        'Hindu',
                                        'Buddha',
                                        'Konghucu'
                                    ];
                                    ?>

                                    <?php foreach ($agamaList as $agama): ?>

                                        <option
                                            value="<?= e($agama) ?>"
                                            <?= $p('agama', $cs) === $agama ? 'selected' : '' ?>
                                        >
                                            <?= e($agama) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </label>


                            <label class="ppdb-form-page__field">

                                <span>
                                    Jenis kelamin
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <select name="jenis_kelamin" required>

                                    <option value="">
                                        Pilih jenis kelamin
                                    </option>

                                    <option
                                        value="Laki-laki"
                                        <?= $p('jenis_kelamin', $cs) === 'Laki-laki' ? 'selected' : '' ?>
                                    >
                                        Laki-laki
                                    </option>

                                    <option
                                        value="Perempuan"
                                        <?= $p('jenis_kelamin', $cs) === 'Perempuan' ? 'selected' : '' ?>
                                    >
                                        Perempuan
                                    </option>

                                </select>

                            </label>

                        </div>


                        <!-- Anak ke & saudara -->

                        <div class="ppdb-form-page__row">

                            <label class="ppdb-form-page__field">

                                <span>
                                    Anak ke
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <input
                                    type="number"
                                    name="anak_ke"
                                    required
                                    min="1"
                                    value="<?= $p('anak_ke', $cs) ?>"
                                    placeholder="Contoh: 1"
                                >

                            </label>


                            <label class="ppdb-form-page__field">

                                <span>
                                    Jumlah saudara kandung
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <input
                                    type="number"
                                    name="jumlah_saudara"
                                    required
                                    min="0"
                                    value="<?= $p('jumlah_saudara', $cs) ?>"
                                    placeholder="Contoh: 2"
                                >

                            </label>

                        </div>


                        <!-- Berat, tinggi, lingkar kepala -->

                        <div class="ppdb-form-page__row">

                            <label class="ppdb-form-page__field">

                                <span>
                                    Berat badan
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <div class="ppdb-form-page__input-unit">

                                    <input
                                        type="number"
                                        name="berat_badan"
                                        required
                                        min="0"
                                        step="0.1"
                                        value="<?= $p('berat_badan', $cs) ?>"
                                        placeholder="Contoh: 15"
                                    >

                                    <span>kg</span>

                                </div>

                            </label>


                            <label class="ppdb-form-page__field">

                                <span>
                                    Tinggi badan
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <div class="ppdb-form-page__input-unit">

                                    <input
                                        type="number"
                                        name="tinggi_badan"
                                        required
                                        min="0"
                                        step="0.1"
                                        value="<?= $p('tinggi_badan', $cs) ?>"
                                        placeholder="Contoh: 100"
                                    >

                                    <span>cm</span>

                                </div>

                            </label>

                        </div>


                        <label class="ppdb-form-page__field">

                            <span>
                                Lingkar kepala
                                <b class="ppdb-form-page__required">*</b>
                            </span>

                            <div class="ppdb-form-page__input-unit">

                                <input
                                    type="number"
                                    name="lingkar_kepala"
                                    required
                                    min="0"
                                    step="0.1"
                                    value="<?= $p('lingkar_kepala', $cs) ?>"
                                    placeholder="Contoh: 50"
                                >

                                <span>cm</span>

                            </div>

                        </label>


                        <!-- Penghasilan -->

                        <label class="ppdb-form-page__field">

                            <span>
                                Penghasilan orang tua
                                <b class="ppdb-form-page__required">*</b>
                            </span>

                            <select
                                name="penghasilan_orang_tua"
                                required
                            >

                                <option value="">
                                    Pilih penghasilan
                                </option>

                                <?php
                                $penghasilanList = [
                                    'Di bawah Rp1.000.000',
                                    'Rp1.000.000 - Rp2.500.000',
                                    'Rp2.500.000 - Rp5.000.000',
                                    'Rp5.000.000 - Rp10.000.000',
                                    'Di atas Rp10.000.000'
                                ];
                                ?>

                                <?php foreach ($penghasilanList as $penghasilan): ?>

                                    <option
                                        value="<?= e($penghasilan) ?>"
                                        <?= $p('penghasilan_orang_tua', $cs) === $penghasilan ? 'selected' : '' ?>
                                    >
                                        <?= e($penghasilan) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </label>


                        <!-- Jarak -->

                        <label class="ppdb-form-page__field">

                            <span>
                                Jarak tempuh sekolah
                                <b class="ppdb-form-page__required">*</b>
                            </span>

                            <input
                                type="text"
                                name="jarak_sekolah"
                                required
                                value="<?= $p('jarak_sekolah', $cs) ?>"
                                placeholder="Contoh: 2 km / 15 menit"
                            >

                        </label>


                        <!-- Alamat -->

                        <label class="ppdb-form-page__field">

                            <span>
                                Alamat rumah
                                <b class="ppdb-form-page__required">*</b>
                            </span>

                            <textarea
                                name="alamat"
                                required
                                placeholder="Alamat lengkap tempat tinggal"
                            ><?= $p('alamat', $cs) ?></textarea>

                        </label>


                    </div>

                </section>


                <!-- =================================================
                     02. ORANG TUA
                     ================================================= -->

                <section class="ppdb-form-page__section">

                    <div class="ppdb-form-page__section-heading ppdb-form-page__section-heading--parent">

                        <span class="ppdb-form-page__section-number">
                            02
                        </span>

                        <div>

                            <h2>Nama Orang Tua</h2>

                            <p>
                                Data ayah dan ibu peserta didik.
                            </p>

                        </div>

                    </div>


                    <!-- =================================================
                         AYAH
                         ================================================= -->

                    <div class="ppdb-form-page__parent-box">

                        <h3 class="ppdb-form-page__parent-title">

                            <span class="ppdb-form-page__parent-badge">
                                A
                            </span>

                            Data Ayah

                        </h3>


                        <div class="ppdb-form-page__fields">


                            <label class="ppdb-form-page__field">

                                <span>
                                    Nama ayah
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <input
                                    type="text"
                                    name="nama_ayah"
                                    required
                                    value="<?= $p('nama_ayah', $ot) ?>"
                                    placeholder="Nama lengkap ayah"
                                >

                            </label>


                            <div class="ppdb-form-page__row">

                                <label class="ppdb-form-page__field">

                                    <span>
                                        Tempat lahir
                                        <b class="ppdb-form-page__required">*</b>
                                    </span>

                                    <input
                                        type="text"
                                        name="tempat_lahir_ayah"
                                        required
                                        value="<?= $p('tempat_lahir_ayah', $ot) ?>"
                                        placeholder="Kota tempat lahir"
                                    >

                                </label>


                                <label class="ppdb-form-page__field">

                                    <span>
                                        Tanggal lahir
                                        <b class="ppdb-form-page__required">*</b>
                                    </span>

                                    <input
                                        type="date"
                                        name="tanggal_lahir_ayah"
                                        required
                                        value="<?= $p('tanggal_lahir_ayah', $ot) ?>"
                                    >

                                </label>

                            </div>


                            <div class="ppdb-form-page__row">

                                <label class="ppdb-form-page__field">

                                    <span>
                                        NIK
                                        <b class="ppdb-form-page__required">*</b>
                                    </span>

                                    <input
                                        type="text"
                                        name="nik_ayah"
                                        required
                                        maxlength="16"
                                        value="<?= $p('nik_ayah', $ot) ?>"
                                        placeholder="16 digit NIK"
                                    >

                                </label>


                                <label class="ppdb-form-page__field">

                                    <span>
                                        Agama
                                        <b class="ppdb-form-page__required">*</b>
                                    </span>

                                    <select name="agama_ayah" required>

                                        <option value="">
                                            Pilih agama
                                        </option>

                                        <?php foreach ($agamaList as $agama): ?>

                                            <option
                                                value="<?= e($agama) ?>"
                                                <?= $p('agama_ayah', $ot) === $agama ? 'selected' : '' ?>
                                            >
                                                <?= e($agama) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </label>

                            </div>


                            <div class="ppdb-form-page__row">

                                <label class="ppdb-form-page__field">

                                    <span>
                                        Pendidikan
                                        <b class="ppdb-form-page__required">*</b>
                                    </span>

                                    <select name="pendidikan_ayah" required>

                                        <option value="">
                                            Pilih pendidikan
                                        </option>

                                        <?php
                                        $pendidikanList = [
                                            'Tidak sekolah',
                                            'SD',
                                            'SMP',
                                            'SMA/SMK',
                                            'D1',
                                            'D2',
                                            'D3',
                                            'D4',
                                            'S1',
                                            'S2',
                                            'S3'
                                        ];
                                        ?>

                                        <?php foreach ($pendidikanList as $pendidikan): ?>

                                            <option
                                                value="<?= e($pendidikan) ?>"
                                                <?= $p('pendidikan_ayah', $ot) === $pendidikan ? 'selected' : '' ?>
                                            >
                                                <?= e($pendidikan) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </label>


                                <label class="ppdb-form-page__field">

                                    <span>
                                        Pekerjaan
                                        <b class="ppdb-form-page__required">*</b>
                                    </span>

                                    <input
                                        type="text"
                                        name="pekerjaan_ayah"
                                        required
                                        value="<?= $p('pekerjaan_ayah', $ot) ?>"
                                        placeholder="Pekerjaan ayah"
                                    >

                                </label>

                            </div>


                            <label class="ppdb-form-page__field">

                                <span>
                                    Nomor telepon
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <input
                                    type="tel"
                                    name="no_hp_ayah"
                                    required
                                    value="<?= $p('no_hp_ayah', $ot) ?>"
                                    placeholder="08xxxxxxxxxx"
                                >

                            </label>


                        </div>

                    </div>


                    <!-- =================================================
                         IBU
                         ================================================= -->

                    <div class="ppdb-form-page__parent-box">

                        <h3 class="ppdb-form-page__parent-title">

                            <span class="ppdb-form-page__parent-badge">
                                B
                            </span>

                            Data Ibu

                        </h3>


                        <div class="ppdb-form-page__fields">


                            <label class="ppdb-form-page__field">

                                <span>
                                    Nama ibu
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <input
                                    type="text"
                                    name="nama_ibu"
                                    required
                                    value="<?= $p('nama_ibu', $ot) ?>"
                                    placeholder="Nama lengkap ibu"
                                >

                            </label>


                            <div class="ppdb-form-page__row">

                                <label class="ppdb-form-page__field">

                                    <span>
                                        Tempat lahir
                                        <b class="ppdb-form-page__required">*</b>
                                    </span>

                                    <input
                                        type="text"
                                        name="tempat_lahir_ibu"
                                        required
                                        value="<?= $p('tempat_lahir_ibu', $ot) ?>"
                                        placeholder="Kota tempat lahir"
                                    >

                                </label>


                                <label class="ppdb-form-page__field">

                                    <span>
                                        Tanggal lahir
                                        <b class="ppdb-form-page__required">*</b>
                                    </span>

                                    <input
                                        type="date"
                                        name="tanggal_lahir_ibu"
                                        required
                                        value="<?= $p('tanggal_lahir_ibu', $ot) ?>"
                                    >

                                </label>

                            </div>


                            <div class="ppdb-form-page__row">

                                <label class="ppdb-form-page__field">

                                    <span>
                                        NIK
                                        <b class="ppdb-form-page__required">*</b>
                                    </span>

                                    <input
                                        type="text"
                                        name="nik_ibu"
                                        required
                                        maxlength="16"
                                        value="<?= $p('nik_ibu', $ot) ?>"
                                        placeholder="16 digit NIK"
                                    >

                                </label>


                                <label class="ppdb-form-page__field">

                                    <span>
                                        Agama
                                        <b class="ppdb-form-page__required">*</b>
                                    </span>

                                    <select name="agama_ibu" required>

                                        <option value="">
                                            Pilih agama
                                        </option>

                                        <?php foreach ($agamaList as $agama): ?>

                                            <option
                                                value="<?= e($agama) ?>"
                                                <?= $p('agama_ibu', $ot) === $agama ? 'selected' : '' ?>
                                            >
                                                <?= e($agama) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </label>

                            </div>


                            <div class="ppdb-form-page__row">

                                <label class="ppdb-form-page__field">

                                    <span>
                                        Pendidikan
                                        <b class="ppdb-form-page__required">*</b>
                                    </span>

                                    <select name="pendidikan_ibu" required>

                                        <option value="">
                                            Pilih pendidikan
                                        </option>

                                        <?php foreach ($pendidikanList as $pendidikan): ?>

                                            <option
                                                value="<?= e($pendidikan) ?>"
                                                <?= $p('pendidikan_ibu', $ot) === $pendidikan ? 'selected' : '' ?>
                                            >
                                                <?= e($pendidikan) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </label>


                                <label class="ppdb-form-page__field">

                                    <span>
                                        Pekerjaan
                                        <b class="ppdb-form-page__required">*</b>
                                    </span>

                                    <input
                                        type="text"
                                        name="pekerjaan_ibu"
                                        required
                                        value="<?= $p('pekerjaan_ibu', $ot) ?>"
                                        placeholder="Pekerjaan ibu"
                                    >

                                </label>

                            </div>


                            <label class="ppdb-form-page__field">

                                <span>
                                    Nomor telepon
                                    <b class="ppdb-form-page__required">*</b>
                                </span>

                                <input
                                    type="tel"
                                    name="no_hp_ibu"
                                    required
                                    value="<?= $p('no_hp_ibu', $ot) ?>"
                                    placeholder="08xxxxxxxxxx"
                                >

                            </label>


                        </div>

                    </div>

                </section>


                <!-- =================================================
                     03. KELOMPOK
                     ================================================= -->

                <section class="ppdb-form-page__section">

                    <div class="ppdb-form-page__section-heading">

                        <span class="ppdb-form-page__section-number">
                            03
                        </span>

                        <div>

                            <h2>Kelompok Pendaftaran</h2>

                            <p>
                                Pilih kelompok yang akan diikuti peserta didik.
                            </p>

                        </div>

                    </div>


                    <div class="ppdb-form-page__kelompok">

                        <p class="ppdb-form-page__kelompok-label">

                            Kelompok pilihan

                            <small>
                                * wajib dipilih
                            </small>

                        </p>


                        <select name="kelompok" required>

                            <option value="">
                                Pilih kelompok
                            </option>

                            <?php
                            $kelompokList = [
                                'Kelompok A',
                                'Kelompok B',
                            ];
                            ?>

                            <?php foreach ($kelompokList as $pr): ?>

                                <option
                                    value="<?= e($pr) ?>"
                                    <?= $p('kelompok', $pendaftaran) === $pr ? 'selected' : '' ?>
                                >
                                    <?= e($pr) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </section>


                <!-- =================================================
                     ACTION
                     ================================================= -->

                <div class="ppdb-form-page__actions">

                    <button
                        type="submit"
                        class="ppdb-form-page__btn"
                    >
                        Simpan Data
                    </button>


                    <a
                        href="dashboard.php"
                        class="ppdb-form-page__btn ppdb-form-page__btn--outline"
                    >
                        Kembali ke Dashboard
                    </a>

                </div>


            </form>


            <!-- =================================================
                 SIDEBAR
                 ================================================= -->

            <aside class="ppdb-form-page__side">


                <div class="ppdb-form-page__dark-card">

                    <p class="ppdb-form-page__dark-label">
                        Nomor pendaftaran
                    </p>

                    <h3>
                        Pendaftaran Anda
                    </h3>

                    <div class="ppdb-form-page__number">

                        <small>
                            ID Pendaftaran
                        </small>

                        <strong>
                            <?= e($pendaftaran['nomor_pendaftaran']) ?>
                        </strong>

                    </div>

                </div>


                <div class="ppdb-form-page__info">

                    <h3>
                        Sebelum menyimpan
                    </h3>

                    <ul>

                        <li>

                            <span class="ppdb-form-page__check">
                                ✓
                            </span>

                            Pastikan nama dan NIK sesuai dokumen.

                        </li>


                        <li>

                            <span class="ppdb-form-page__check">
                                ✓
                            </span>

                            Periksa kembali data tanggal lahir.

                        </li>


                        <li>

                            <span class="ppdb-form-page__check">
                                ✓
                            </span>

                            Pastikan nomor telepon ayah dan ibu aktif.

                        </li>


                        <li>

                            <span class="ppdb-form-page__check">
                                ✓
                            </span>

                            Periksa kembali data fisik peserta didik.

                        </li>


                        <li>

                            <span class="ppdb-form-page__check">
                                ✓
                            </span>

                            Pilih kelompok pendaftaran yang sesuai.

                        </li>

                    </ul>

                </div>


            </aside>


        </div>

    </section>

</main>


<?php require __DIR__ . '/includes/footer.php'; ?>
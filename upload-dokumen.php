<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

$stmt = $pdo->prepare('SELECT * FROM pendaftaran WHERE user_id=? ORDER BY created_at DESC LIMIT 1');
$stmt->execute([$user['id']]);
$pendaftaran = $stmt->fetch();
if (!$pendaftaran) { flash('success','Mulai pendaftaran terlebih dahulu.'); redirect('pendaftaran-form.php'); }

$pid = $pendaftaran['id'];
$allowedTypes = ['image/jpeg','image/png','application/pdf'];
$maxSize = 2 * 1024 * 1024; // 2MB
$jenisDokumen = ['Kartu Keluarga','Akta Kelahiran','KTP Orang Tua','Pas Foto','Dokumen Pendukung'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis = trim($_POST['jenis'] ?? '');
    if (!in_array($jenis, $jenisDokumen)) { $errors[] = 'Jenis dokumen tidak valid.'; }
    if (empty($_FILES['file']['name'])) { $errors[] = 'File wajib dipilih.'; }
    if (!$errors) {
        $file = $_FILES['file'];
        if ($file['error'] !== UPLOAD_ERR_OK) { $errors[] = 'Upload gagal, coba lagi.'; }
        elseif (!in_array($file['type'], $allowedTypes)) { $errors[] = 'Format file harus JPG, PNG, atau PDF.'; }
        elseif ($file['size'] > $maxSize) { $errors[] = 'Ukuran file maksimal 2MB.'; }
        if (!$errors) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $namaFile = 'dok_' . $pid . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $namaFile)) {
                $pdo->prepare('INSERT INTO dokumen (pendaftaran_id,jenis,nama_file,path_file,status) VALUES (?,?,?,?,?)')->execute([$pid,$jenis,$file['name'],'uploads/'.$namaFile,'menunggu']);
                flash('success','Dokumen berhasil diunggah.');
                redirect('upload-dokumen.php');
            } else { $errors[] = 'Gagal menyimpan file.'; }
        }
    }
}

$dokumen = $pdo->prepare('SELECT * FROM dokumen WHERE pendaftaran_id=? ORDER BY uploaded_at DESC');
$dokumen->execute([$pid]); $doks = $dokumen->fetchAll();

$pageTitle = 'Upload Dokumen - TK Harapan Bunda'; $activePage = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
<section class="page-title"><div class="container"><h1>Upload Dokumen</h1><p>Unggah dokumen persyaratan PPDB.</p></div></section>
<div class="container section">
<?php if($m=flash('success')):?><div class="alert alert-success" style="margin-bottom:18px"><?=e($m)?></div><?php endif;?>
<?php if($errors):?><div class="alert alert-error" style="margin-bottom:18px"><ul><?php foreach($errors as $er):?><li><?=e($er)?></li><?php endforeach;?></ul></div><?php endif;?>
<div style="display:grid;grid-template-columns:1fr 1.6fr;gap:24px;align-items:start">
<form class="registration-form" method="post" enctype="multipart/form-data">
    <h2>Unggah Dokumen</h2>
    <label>Jenis dokumen<select name="jenis" required><option value="">Pilih jenis</option><?php foreach($jenisDokumen as $j):?><option value="<?=e($j)?>"><?=e($j)?></option><?php endforeach;?></select></label>
    <label>File (JPG/PNG/PDF maks 2MB)<input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" required></label>
    <button class="btn" type="submit">Upload</button>
    <a class="btn btn-outline" href="dashboard.php" style="margin-left:8px">Kembali</a>
</form>
<div>
    <h2>Dokumen Terunggah</h2>
    <?php if(!$doks):?><p style="color:var(--muted)">Belum ada dokumen.</p><?php else:?>
    <div style="display:grid;gap:12px">
    <?php foreach($doks as $dok):
        $bc=['menunggu'=>'#667085','valid'=>'#4f9d69','tidak_valid'=>'#e53e3e'][$dok['status']];
        $lbl=['menunggu'=>'Menunggu','valid'=>'Valid','tidak_valid'=>'Tidak Valid'][$dok['status']];
    ?>
    <div class="plain-box" style="padding:14px">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
            <div><strong><?=e($dok['jenis'])?></strong><br><small style="color:var(--muted)"><?=e($dok['nama_file'])?></small></div>
            <span style="background:<?=$bc?>;color:#fff;padding:5px 12px;border-radius:999px;font-size:13px;font-weight:900"><?=$lbl?></span>
        </div>
        <?php if($dok['alasan']):?><p style="margin:8px 0 0;color:#e53e3e;font-size:13px">Alasan: <?=e($dok['alasan'])?></p><?php endif;?>
        <small style="color:var(--muted)"><?=date('d/m/Y H:i',strtotime($dok['uploaded_at']))?></small>
    </div>
    <?php endforeach;?>
    </div>
    <?php endif;?>
</div>
</div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

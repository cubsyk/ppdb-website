<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

$stmt = $pdo->prepare('SELECT p.*,cs.nama_lengkap FROM pendaftaran p LEFT JOIN calon_siswa cs ON cs.pendaftaran_id=p.id WHERE p.user_id=? AND p.status="diterima" ORDER BY p.created_at DESC LIMIT 1');
$stmt->execute([$user['id']]);
$pendaftaran = $stmt->fetch();
if (!$pendaftaran) { flash('success','Fitur ini hanya tersedia untuk pendaftar yang diterima.'); redirect('dashboard.php'); }
$pid = $pendaftaran['id'];

// Cek pembayaran
$stmtP = $pdo->prepare('SELECT * FROM pembayaran WHERE pendaftaran_id=?');
$stmtP->execute([$pid]);
$pembayaran = $stmtP->fetch();
if (!$pembayaran || $pembayaran['status'] !== 'dikonfirmasi') {
    flash('success','Silakan upload bukti pembayaran terlebih dahulu.'); redirect('pembayaran.php');
}

$duRow = $pdo->prepare('SELECT * FROM daftar_ulang WHERE pendaftaran_id=?');
$duRow->execute([$pid]); $du = $duRow->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$du) {
    $catatan = trim($_POST['catatan'] ?? '');
    $pdo->prepare('INSERT INTO daftar_ulang (pendaftaran_id,konfirmasi,catatan) VALUES (?,1,?)')->execute([$pid,$catatan?:null]);
    $pdo->prepare('UPDATE pendaftaran SET status="siswa_terdaftar" WHERE id=?')->execute([$pid]);
    flash('success','Daftar ulang berhasil! Selamat datang di TK Harapan Bunda.');
    redirect('dashboard.php');
}
$pageTitle='Daftar Ulang - TK Harapan Bunda'; $activePage='dashboard';
require __DIR__ . '/includes/header.php';
?>
<section class="page-title"><div class="container"><h1>Daftar Ulang</h1><p>Konfirmasi kehadiran calon siswa yang telah diterima.</p></div></section>
<div class="container section">
<?php if($du):?>
<div class="card" style="max-width:580px;margin:auto;text-align:center;padding:48px">
    <h2 style="color:#2f6f4e">&#127881; Daftar Ulang Selesai</h2>
    <p>Status Anda telah diubah menjadi <strong>Siswa Terdaftar</strong>.</p>
    <a class="btn" href="dashboard.php">Kembali ke Dashboard</a>
</div>
<?php else:?>
<div class="card" style="max-width:580px;margin:auto;padding:36px">
    <h2>&#127881; Selamat, Anda Diterima!</h2>
    <p>Nomor: <strong><?=e($pendaftaran['nomor_pendaftaran'])?></strong></p>
    <?php if($pendaftaran['hasil_seleksi']):?><div class="plain-box" style="margin-bottom:16px"><p><?=nl2br(e($pendaftaran['hasil_seleksi']))?></p></div><?php endif;?>
    <form method="post">
        <label style="display:grid;gap:6px;font-weight:900;margin-bottom:16px">Catatan / Konfirmasi (opsional)<textarea name="catatan" rows="3"></textarea></label>
        <button class="btn" type="submit">Konfirmasi Daftar Ulang</button>
        <a class="btn btn-outline" href="dashboard.php" style="margin-left:10px">Kembali</a>
    </form>
</div>
<?php endif;?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

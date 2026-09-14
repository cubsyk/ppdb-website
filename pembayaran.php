<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();
$errors = [];

$stmt = $pdo->prepare('SELECT * FROM pendaftaran WHERE user_id=? AND status="diterima" ORDER BY created_at DESC LIMIT 1');
$stmt->execute([$user['id']]);
$pendaftaran = $stmt->fetch();
if (!$pendaftaran) { flash('success','Fitur ini hanya tersedia untuk pendaftar yang diterima.'); redirect('dashboard.php'); }
$pid = $pendaftaran['id'];

$bayar = $pdo->prepare('SELECT * FROM pembayaran WHERE pendaftaran_id=?');
$bayar->execute([$pid]);
$pembayaran = $bayar->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!$pembayaran || $pembayaran['status'] === 'ditolak')) {
    $nominal = (float)($_POST['nominal'] ?? 0);
    $namaPengirim = trim($_POST['nama_pengirim'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');
    
    if ($nominal <= 0) $errors[] = 'Nominal pembayaran wajib diisi.';
    if ($namaPengirim === '') $errors[] = 'Nama pengirim wajib diisi.';
    if (empty($_FILES['bukti']['name'])) $errors[] = 'Bukti transfer wajib diunggah.';
    
    if (!$errors) {
        $file = $_FILES['bukti'];
        if ($file['error'] !== UPLOAD_ERR_OK) $errors[] = 'Upload gagal, coba lagi.';
        elseif (!in_array($file['type'], ['image/jpeg','image/png','application/pdf'])) $errors[] = 'Format file harus JPG/PNG/PDF.';
        elseif ($file['size'] > 2*1024*1024) $errors[] = 'Ukuran maksimal 2MB.';
        
        if (!$errors) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $namaFile = 'bayar_' . $pid . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $namaFile)) {
                if ($pembayaran && $pembayaran['status'] === 'ditolak') {
                    // Update pembayaran yang ditolak
                    $pdo->prepare('UPDATE pembayaran SET nominal=?,nama_pengirim=?,bukti_file=?,catatan=?,status="menunggu",catatan_admin=NULL,updated_at=NOW() WHERE pendaftaran_id=?')
                        ->execute([$nominal,$namaPengirim,'uploads/'.$namaFile,$catatan?:null,$pid]);
                    flash('success','Bukti pembayaran berhasil diupload ulang. Menunggu konfirmasi admin.');
                } else {
                    // Insert pembayaran baru
                    $pdo->prepare('INSERT INTO pembayaran (pendaftaran_id,jenis,nominal,nama_pengirim,bukti_file,catatan) VALUES (?,?,?,?,?,?)')
                        ->execute([$pid,'daftar_ulang',$nominal,$namaPengirim,'uploads/'.$namaFile,$catatan?:null]);
                    flash('success','Bukti pembayaran berhasil diunggah. Menunggu konfirmasi admin.');
                }
                redirect('dashboard.php');
            } else { $errors[] = 'Gagal menyimpan file.'; }
        }
    }
}

$pageTitle = 'Upload Bukti Pembayaran - TK Harapan Bunda'; $activePage = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
<section class="page-title">
    <div class="container">
        <h1>💰 Upload Bukti Pembayaran</h1>
        <p>Unggah bukti transfer daftar ulang untuk diverifikasi admin.</p>
    </div>
</section>

<div class="container section">
    <?php if ($errors): ?>
        <div class="alert alert-error" style="margin-bottom:24px;border-radius:12px;padding:20px;display:flex;align-items:start;gap:12px">
            <span style="font-size:28px">⚠️</span>
            <div style="flex:1">
                <h3 style="margin:0 0 12px 0">Terjadi Kesalahan:</h3>
                <ul style="margin:0;padding-left:20px">
                    <?php foreach ($errors as $err): ?>
                        <li style="margin-bottom:4px"><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($pembayaran && $pembayaran['status'] !== 'ditolak'): ?>
        <div class="card" style="max-width:680px;margin:auto;padding:40px;text-align:center;border-radius:16px">
            <div style="width:80px;height:80px;margin:0 auto 20px;background:<?=$pembayaran['status']==='dikonfirmasi'?'linear-gradient(135deg,#10b981,#059669)':'linear-gradient(135deg,#f59e0b,#d97706)'?>;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:40px">
                <?= $pembayaran['status'] === 'dikonfirmasi' ? '✅' : '⏳' ?>
            </div>
            <h2 style="margin-bottom:12px;color:var(--ink)">
                <?= $pembayaran['status'] === 'dikonfirmasi' ? 'Pembayaran Dikonfirmasi' : 'Menunggu Konfirmasi' ?>
            </h2>
            <p style="color:var(--muted);margin-bottom:24px">
                <?= $pembayaran['status'] === 'dikonfirmasi' 
                    ? 'Pembayaran Anda telah dikonfirmasi oleh admin.' 
                    : 'Bukti pembayaran Anda sedang diverifikasi oleh admin.' ?>
            </p>
            
            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:20px;text-align:left;margin-bottom:24px">
                <div style="display:grid;gap:12px">
                    <div style="display:flex;justify-content:space-between;border-bottom:1px solid #e5e7eb;padding-bottom:8px">
                        <span style="color:var(--muted)">Nominal:</span>
                        <strong style="color:var(--ink)">Rp <?= number_format((float)$pembayaran['nominal'],0,',','.') ?></strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;border-bottom:1px solid #e5e7eb;padding-bottom:8px">
                        <span style="color:var(--muted)">Nama Pengirim:</span>
                        <strong style="color:var(--ink)"><?= e($pembayaran['nama_pengirim']) ?></strong>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <span style="color:var(--muted)">Status:</span>
                        <span style="background:<?=$pembayaran['status']==='dikonfirmasi'?'#10b981':'#f59e0b'?>;color:#fff;padding:6px 14px;border-radius:999px;font-size:13px;font-weight:900">
                            <?= e(pembayaran_label($pembayaran['status'])) ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <?php if ($pembayaran['status'] === 'dikonfirmasi'): ?>
                <a class="btn" href="daftar-ulang.php" style="padding:14px 32px;margin-bottom:12px">Lakukan Daftar Ulang</a>
            <?php endif; ?>
            <a class="btn btn-outline" href="dashboard.php" style="padding:14px 32px">Kembali ke Dashboard</a>
        </div>
    <?php else: ?>
        <?php if ($pembayaran && $pembayaran['status'] === 'ditolak'): ?>
            <div class="alert alert-error" style="margin-bottom:24px;max-width:680px;margin-left:auto;margin-right:auto;border-radius:12px;padding:24px">
                <div style="display:flex;align-items:start;gap:16px">
                    <div style="width:56px;height:56px;background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:32px;flex-shrink:0">❌</div>
                    <div style="flex:1">
                        <h3 style="margin:0 0 8px 0;color:#991b1b;font-size:20px">Pembayaran Ditolak</h3>
                        <p style="margin:0 0 8px 0"><strong>Alasan:</strong> <?= e($pembayaran['catatan_admin'] ?? 'Tidak ada keterangan') ?></p>
                        <p style="margin:0;font-size:14px">Silakan upload ulang bukti transfer yang benar di bawah ini.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="card" style="max-width:680px;margin:auto;padding:40px;border-radius:16px">
            <div style="text-align:center;margin-bottom:32px">
                <div style="width:80px;height:80px;margin:0 auto 16px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:40px">💰</div>
                <h2 style="margin:0 0 8px 0;color:var(--ink)">Informasi Pembayaran</h2>
                <p style="margin:0;color:var(--muted)">Silakan transfer ke rekening berikut</p>
            </div>
            
            <div class="plain-box" style="margin-bottom:32px;border-radius:12px;padding:24px;background:linear-gradient(135deg,#f9fafb,#f3f4f6)">
                <div style="display:grid;gap:20px">
                    <div>
                        <h3 style="margin:0 0 12px 0;color:var(--ink);font-size:16px;text-transform:uppercase;letter-spacing:0.5px">🏦 Rekening Tujuan</h3>
                        <div style="background:#fff;border-radius:8px;padding:16px;border:1px solid #e5e7eb">
                            <p style="margin:0 0 8px 0;font-size:14px;color:var(--muted)">Bank:</p>
                            <p style="margin:0 0 12px 0;font-size:20px;font-weight:900;color:var(--ink)">Bank BRI</p>
                            <p style="margin:0 0 8px 0;font-size:14px;color:var(--muted)">Nomor Rekening:</p>
                            <p style="margin:0 0 12px 0;font-size:24px;font-weight:900;color:#3b82f6;letter-spacing:2px">1234-5678-9012</p>
                            <p style="margin:0 0 8px 0;font-size:14px;color:var(--muted)">Atas Nama:</p>
                            <p style="margin:0;font-size:18px;font-weight:900;color:var(--ink)">TK Harapan Bunda</p>
                        </div>
                    </div>
                    <div>
                        <h3 style="margin:0 0 12px 0;color:var(--ink);font-size:16px;text-transform:uppercase;letter-spacing:0.5px">💵 Nominal Daftar Ulang</h3>
                        <div style="background:#fff;border-radius:8px;padding:16px;border:1px solid #e5e7eb;text-align:center">
                            <p style="margin:0;font-size:32px;font-weight:900;color:#10b981">Rp 500.000,-</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <form method="post" enctype="multipart/form-data" style="text-align:left">
                <h3 style="margin:0 0 20px 0;color:var(--ink);font-size:18px">📝 Upload Bukti Transfer</h3>
                
                <div style="margin-bottom:20px">
                    <label style="display:block;font-weight:900;margin-bottom:8px;color:var(--ink)">Nominal yang Ditransfer <span style="color:#ef4444">*</span></label>
                    <input type="number" name="nominal" required placeholder="Contoh: 500000" style="width:100%;padding:14px;border:2px solid #e5e7eb;border-radius:10px;font-size:16px;transition:border-color 0.3s" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                    <small style="color:var(--muted);font-size:13px;display:block;margin-top:4px">Masukkan nominal sesuai yang Anda transfer</small>
                </div>
                
                <div style="margin-bottom:20px">
                    <label style="display:block;font-weight:900;margin-bottom:8px;color:var(--ink)">Nama Pengirim <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nama_pengirim" required placeholder="Nama sesuai rekening pengirim" style="width:100%;padding:14px;border:2px solid #e5e7eb;border-radius:10px;font-size:16px;transition:border-color 0.3s" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
                    <small style="color:var(--muted);font-size:13px;display:block;margin-top:4px">Nama harus sesuai dengan rekening yang digunakan untuk transfer</small>
                </div>
                
                <div style="margin-bottom:20px">
                    <label style="display:block;font-weight:900;margin-bottom:8px;color:var(--ink)">Bukti Transfer <span style="color:#ef4444">*</span></label>
                    <input type="file" name="bukti" required accept="image/jpeg,image/png,application/pdf" style="width:100%;padding:14px;border:2px solid #e5e7eb;border-radius:10px;font-size:14px;background:#fff">
                    <small style="color:var(--muted);font-size:13px;display:block;margin-top:4px">Format: JPG, PNG, atau PDF. Maksimal 2MB</small>
                </div>
                
                <div style="margin-bottom:28px">
                    <label style="display:block;font-weight:900;margin-bottom:8px;color:var(--ink)">Catatan (Opsional)</label>
                    <textarea name="catatan" rows="3" placeholder="Catatan tambahan jika ada..." style="width:100%;padding:14px;border:2px solid #e5e7eb;border-radius:10px;font-size:16px;transition:border-color 0.3s;resize:vertical" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                </div>
                
                <button type="submit" class="btn" style="width:100%;padding:16px;font-size:16px;font-weight:900;margin-bottom:12px">🚀 Upload Bukti Transfer</button>
                <a class="btn btn-outline" href="dashboard.php" style="width:100%;text-align:center;display:block;padding:16px;font-size:16px">Kembali ke Dashboard</a>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
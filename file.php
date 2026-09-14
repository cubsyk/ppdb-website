<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
$user = current_user();
if (!$user) { http_response_code(403); exit('Forbidden'); }

$f = trim($_GET['f'] ?? '');
if ($f === '' || strpos($f, '..') !== false || !str_starts_with($f, 'uploads/')) {
    http_response_code(400); exit('Invalid');
}
$abs = __DIR__ . '/' . $f;
if (!file_exists($abs) || !is_file($abs)) { http_response_code(404); exit('Not found'); }

// Admin boleh semua; orang_tua hanya milik sendiri
if ($user['role'] !== 'admin') {
    // Cek dokumen
    $stmt = $pdo->prepare('SELECT d.id FROM dokumen d JOIN pendaftaran p ON p.id=d.pendaftaran_id WHERE d.path_file=? AND p.user_id=? LIMIT 1');
    $stmt->execute([$f, $user['id']]);
    $match = $stmt->fetch();
    // Cek pembayaran
    if (!$match) {
        $stmt = $pdo->prepare('SELECT b.id FROM pembayaran b JOIN pendaftaran p ON p.id=b.pendaftaran_id WHERE b.bukti_file=? AND p.user_id=? LIMIT 1');
        $stmt->execute([$f, $user['id']]);
        $match = $stmt->fetch();
    }
    if (!$match) { http_response_code(403); exit('Forbidden'); }
}

$ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
$mime = match($ext) {
    'pdf' => 'application/pdf',
    'jpg','jpeg' => 'image/jpeg',
    'png' => 'image/png',
    default => 'application/octet-stream',
};
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($abs));
header('Content-Disposition: inline; filename="' . basename($abs) . '"');
header('X-Content-Type-Options: nosniff');
readfile($abs);

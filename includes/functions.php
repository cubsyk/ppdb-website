<?php
declare(strict_types=1);

function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function redirect(string $path): never { header('Location: ' . $path); exit; }
function base_path(string $path = ''): string { return __DIR__ . '/../' . ltrim($path, '/'); }
function flash(string $key, ?string $message = null): ?string {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if ($message !== null) { $_SESSION['flash'][$key] = $message; return null; }
    $value = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $value;
}
function post(string $key): string { return trim((string)($_POST[$key] ?? '')); }
function status_label(string $status): string {
    return [
        'draft'=>'Draft','menunggu_pengajuan'=>'Menunggu Pengajuan','menunggu_verifikasi'=>'Menunggu Verifikasi','perlu_perbaikan'=>'Perlu Perbaikan','terverifikasi'=>'Terverifikasi','diterima'=>'Diterima','tidak_diterima'=>'Tidak Diterima','daftar_tunggu'=>'Daftar Tunggu','daftar_ulang'=>'Daftar Ulang','siswa_terdaftar'=>'Siswa Terdaftar'
    ][$status] ?? $status;
}
function pembayaran_label(string $status): string {
    return [
        'menunggu'=>'Menunggu Verifikasi','dikonfirmasi'=>'Dikonfirmasi','ditolak'=>'Ditolak'
    ][$status] ?? $status;
}
function registration_progress(string $status): int {
    return ['draft'=>15,'menunggu_pengajuan'=>25,'menunggu_verifikasi'=>40,'perlu_perbaikan'=>45,'terverifikasi'=>60,'daftar_tunggu'=>70,'tidak_diterima'=>100,'diterima'=>80,'daftar_ulang'=>90,'siswa_terdaftar'=>100][$status] ?? 10;
}
function generate_registration_number(): string { return 'PPDB-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)),0,6)); }

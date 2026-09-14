<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/database.php';

function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!current_user()) redirect('login.php'); }
function require_admin(): void { if (!current_user() || current_user()['role'] !== 'admin') redirect('../login.php'); }
function login_user(array $user): void { $_SESSION['user'] = ['id'=>(int)$user['id'],'nama'=>$user['nama'],'email'=>$user['email'],'role'=>$user['role']]; }
function logout_user(): void { $_SESSION = []; if (ini_get('session.use_cookies')) { $p=session_get_cookie_params(); setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']); } session_destroy(); }

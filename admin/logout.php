<?php
require __DIR__ . '/../includes/auth.php';
logout_user();
redirect('../login.php');

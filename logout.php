<?php
require_once __DIR__ . '/includes/auth.php';

logout_user();
session_start();
set_flash('success', 'You have been logged out successfully.');
header('Location: login.php');
exit;

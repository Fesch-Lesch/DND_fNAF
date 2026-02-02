<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /dnd-site/login.php');
        exit;
    }
}

function requireRole($roles) {
    requireLogin();
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    if (!in_array($_SESSION['role'], $roles)) {
        header('Location: /dnd-site/dashboard.php?error=access_denied');
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isTeacher() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'teacher';
}

function isCaptain() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'captain';
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

function setCurrentUserForTriggers($pdo) {
    $userId = getCurrentUserId();
    $username = getCurrentUsername();
    $pdo->exec("SET @current_user_id = " . ($userId ? (int)$userId : 'NULL'));
    $pdo->exec("SET @current_username = " . ($username ? "'" . addslashes($username) . "'" : 'NULL'));
}
?>
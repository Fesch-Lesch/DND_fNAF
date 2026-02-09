<?php
session_start();

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../index.php');
        exit;
    }
}

function checkRole($allowedRoles) {
    checkAuth();
    if (!in_array($_SESSION['role'], $allowedRoles)) {
        die("У вас нет доступа к этой странице.");
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

function logout() {
    session_destroy();
    header('Location: ../index.php');
    exit;
}
?>
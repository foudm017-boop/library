<?php
// inc/functions.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * تحديد مسار الأساس للمشروع تلقائياً
 */
if (!defined('BASE_URL')) {
    $project_root = realpath(__DIR__ . '/..');
    $doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;

    if ($project_root && $doc_root && strpos($project_root, $doc_root) === 0) {
        $base = substr($project_root, strlen($doc_root));
        $base = str_replace('\\', '/', $base);
        define('BASE_URL', rtrim($base, '/'));
    } else {
        define('BASE_URL', '');
    }
}

function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

function clean_input($value) {
    return htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/* -------------------- المصادقة والصلاحيات -------------------- */

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_logged_in() {
    return current_user() !== null;
}

function is_admin() {
    $user = current_user();
    return $user && ($user['role'] ?? '') === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . url('login.php'));
        exit;
    }
}

function require_admin() {
    require_login();
    if (!is_admin()) {
        header('Location: ' . url('dashboard.php'));
        exit;
    }
}

function flash_set($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function flash_get() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/* -------------------- دوال مساعدة إضافية للكتب والخصومات -------------------- */

/**
 * حساب السعر بعد الخصم بناءً على جدول الأسعار المخصص
 */
function get_discounted_price($rawPrice) {
    $discountMap = [
        750 => 550,
        600 => 450,
        500 => 380,
        450 => 350,
        400 => 300,
        380 => 290,
        320 => 250,
        300 => 230,
        250 => 190,
        220 => 170,
        180 => 140
    ];
    return array_key_exists($rawPrice, $discountMap) ? $discountMap[$rawPrice] : $rawPrice;
}

/**
 * فلترة الكتب حسب التصنيف (عربي، إنجليزي، برمجة)
 */
function filter_books_by_category($books, $filter) {
    return array_filter($books, function($b) use ($filter) {
        $text = strtolower(($b['title'] ?? '') . ' ' . ($b['author'] ?? ''));
        if ($filter === 'arabic') {
            return preg_match('/[\x{0600}-\x{06FF}]/u', $b['title'] ?? '');
        }
        if ($filter === 'english') {
            return preg_match('/^[a-zA-Z0-9\s\-\:\.\,\!\?]+$/', $b['title'] ?? '');
        }
        if ($filter === 'programming') {
            return strpos($text, 'برمجة') !== false || strpos($text, 'php') !== false || strpos($text, 'code') !== false;
        }
        return true;
    });
}
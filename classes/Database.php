<?php
// classes/Database.php

class Database {
    private static $instance = null;
    private $conn;

    private $host = 'localhost';
    private $db   = 'books'; // اسم قاعدة البيانات التي أنشأناها
    private $user = 'root';  // اسم المستخدم للسيرفر (افتراضي في XAMPP هو root)
    private $pass = '';      // كلمة المرور (افتراضياً فارغة في XAMPP)

    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4",
                $this->user,
                $this->pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->conn;
    }
}
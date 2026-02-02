<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // ตรวจสอบ username
        $stmt = $pdo->prepare("SELECT * FROM ath_member WHERE mem_username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // เช็คว่ามี User ไหม และ Password ตรงไหม (ใช้แบบธรรมดาเพื่อความง่ายในขั้นตอนนี้)
        if ($user && $user['mem_password'] == $password) {
            
            // Login สำเร็จ! เก็บค่าลง Session
            $_SESSION['mem_id'] = $user['mem_id'];
            $_SESSION['mem_name'] = $user['mem_name'];
            $_SESSION['mem_position'] = $user['mem_position'];
            
            // ไปหน้าแรก
            header("Location: index.php");
            exit();

        } else {
            // Login พลาด
            $_SESSION['error'] = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
            header("Location: login.php");
            exit();
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
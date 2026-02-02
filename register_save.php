<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['mem_name']);
    $position = trim($_POST['mem_position']);
    $department = trim($_POST['mem_department']);
    $username = trim($_POST['mem_username']);
    $password = $_POST['mem_password'];
    $confirm_password = $_POST['confirm_password'];

    // เช็ครหัสผ่าน
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน";
        header("Location: register.php");
        exit();
    }

    try {
        // เช็คชื่อซ้ำ
        $check = $pdo->prepare("SELECT COUNT(*) FROM ath_member WHERE mem_username = ?");
        $check->execute([$username]);
        
        if ($check->fetchColumn() > 0) {
            $_SESSION['error'] = "ชื่อผู้ใช้งานนี้ (Username) ถูกใช้ไปแล้ว";
            header("Location: register.php");
            exit();
        }

        // บันทึก (Password ไม่เข้ารหัสตามโจทย์ เพื่อให้ง่ายต่อการทดสอบ)
        $sql = "INSERT INTO ath_member (mem_name, mem_position, mem_department, mem_username, mem_password) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$name, $position, $department, $username, $password])) {
            $_SESSION['success'] = "สมัครสมาชิกเรียบร้อยแล้ว! กรุณาเข้าสู่ระบบ";
            header("Location: login.php");
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "ระบบขัดข้อง: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
}
?>
<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mem_id = $_POST['mem_id'];
    $fam_name = trim($_POST['fam_name']);
    $fam_birthdate = $_POST['fam_birthdate'];
    $fam_relationship = 1; // 1 หมายถึง เป็นบุตร

    try {
        $sql = "INSERT INTO ath_member_family (mem_id, fam_name, fam_birthdate, fam_relationship) 
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$mem_id, $fam_name, $fam_birthdate, $fam_relationship])) {
            echo "<script>
                alert('เพิ่มข้อมูลบุตรเรียบร้อยแล้ว!');
                window.location.href = 'create_request.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>
            alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "');
            window.history.back();
        </script>";
    }
}
?>
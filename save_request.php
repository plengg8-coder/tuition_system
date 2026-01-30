<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $mem_id = $_POST['mem_id'];
    $fam_id = $_POST['fam_id'];
    $school_name = $_POST['school_name'];
    $school_level = $_POST['school_level'];
    $grade = $_POST['grade'];
    $semester = $_POST['semester'];
    $year = $_POST['academic_year'];
    $amount = $_POST['amount'];
    
    // ตรวจสอบปุ่มที่กด (Draft หรือ Submit)
    $status = ($_POST['action'] == 'submit') ? 'submitted' : 'draft';

    try {
        $sql = "INSERT INTO ath_tuition_request 
                (mem_id, fam_id, req_school_name, req_school_level, req_grade, req_semester, req_academic_year, req_tuition_amount, req_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$mem_id, $fam_id, $school_name, $school_level, $grade, $semester, $year, $amount, $status]);

        // ถ้าสำเร็จ ให้กลับไปหน้า Dashboard พร้อมแจ้งเตือน (แบบง่าย)
        echo "<script>alert('บันทึกข้อมูลเรียบร้อยแล้ว'); window.location='index.php';</script>";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();


        
    }
}
?>
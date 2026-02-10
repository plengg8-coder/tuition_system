<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // 1. รับค่าจากฟอร์ม
        $mem_id = $_POST['mem_id'];
        $fam_id = $_POST['fam_id'];
        $school_name = $_POST['school_name'] ?? '';
        $school_level = $_POST['school_level'] ?? '';
        $amount = $_POST['request_amount'] ?? 0;
        $request_date = $_POST['request_date'] ?? date('Y-m-d');
        $remark = $_POST['remark'] ?? '';
        
        // ค่า Default
        $grade = '-';
        $semester = 1;
        $academic_year = date('Y') + 543;
        $status = ($_POST['action'] == 'submit') ? 'submitted' : 'draft';

        // 2. บันทึกลงฐานข้อมูล
        $sql = "INSERT INTO ath_tuition_request 
                (mem_id, fam_id, req_school_name, req_school_level, req_grade, req_semester, req_academic_year, req_tuition_amount, req_request_date, req_notes, req_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $mem_id, $fam_id, $school_name, $school_level, $grade, 
            $semester, $academic_year, $amount, $request_date, $remark, $status
        ]);

        if ($result) {
            // --- [จุดสำคัญ] หา ID ล่าสุดที่เพิ่งบันทึก ---
            $new_req_id = $pdo->lastInsertId(); 

            // ส่งไปหน้าพิมพ์ทันที
            echo "<script>
                alert('บันทึกข้อมูลเรียบร้อยแล้ว'); 
                window.location='print_request.php?id=$new_req_id'; 
            </script>";
        }

    } catch (PDOException $e) {
        echo "<h3>เกิดข้อผิดพลาด:</h3>" . $e->getMessage();
        echo "<br><a href='create_request.php'>กลับไปหน้าฟอร์ม</a>";
    }
} else {
    header("Location: index.php");
}
?>
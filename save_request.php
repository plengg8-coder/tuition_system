<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // 1. รับค่าจากฟอร์ม (ตามชื่อ name="..." ใน create_request.php ล่าสุด)
        $mem_id = $_POST['mem_id'];
        $fam_id = $_POST['fam_id'];
        
        $school_name = $_POST['school_name'] ?? '';
        $school_level = $_POST['school_level'] ?? '';
        
        // *จุดที่แก้: ชื่อตัวแปรต้องตรงกับ input name="request_amount"
        $amount = $_POST['request_amount'] ?? 0; 
        
        // *จุดที่แก้: รับวันที่ขอเบิก
        $request_date = $_POST['request_date'] ?? date('Y-m-d');
        
        // *จุดที่แก้: รับหมายเหตุ
        $remark = $_POST['remark'] ?? '';

        // 2. กำหนดค่า Default สำหรับฟิลด์ที่ Database บังคับต้องมี (แต่ในฟอร์มไม่มี)
        $grade = '-';           // ชั้นเรียน (ไม่ได้กรอก)
        $semester = 1;          // เทอม (สมมติเป็น 1)
        $academic_year = date('Y') + 543; // ปีการศึกษา (ปีปัจจุบัน + 543)
        
        // กำหนดสถานะ
        $status = ($_POST['action'] == 'submit') ? 'submitted' : 'draft';

        // 3. บันทึกลงฐานข้อมูล
        $sql = "INSERT INTO ath_tuition_request 
                (mem_id, fam_id, req_school_name, req_school_level, req_grade, req_semester, req_academic_year, req_tuition_amount, req_request_date, req_notes, req_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $mem_id, 
            $fam_id, 
            $school_name, 
            $school_level, 
            $grade, 
            $semester, 
            $academic_year, 
            $amount, 
            $request_date,
            $remark, 
            $status
        ]);

        if ($result) {
            echo "<script>
                alert('บันทึกข้อมูลเรียบร้อยแล้ว'); 
                window.location='index.php';
            </script>";
        }

    } catch (PDOException $e) {
        // แสดง Error ชัดเจนถ้าบันทึกไม่ได้
        echo "<h3>เกิดข้อผิดพลาด:</h3>";
        echo "Error: " . $e->getMessage();
        echo "<br><br><a href='create_request.php'>กลับไปหน้าฟอร์ม</a>";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
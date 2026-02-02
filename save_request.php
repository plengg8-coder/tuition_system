<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. รับค่าจากฟอร์ม (ไม่มี Array แล้ว รับค่าตรงๆ)
    $mem_id = $_POST['mem_id'] ?? 0;
    $fam_id = $_POST['fam_id'] ?? '';
    $school_name = $_POST['school_name'] ?? '';
    $school_level = $_POST['school_level'] ?? '';
    $grade = $_POST['grade'] ?? '';
    $semester = $_POST['semester'] ?? 1;
    $academic_year = $_POST['academic_year'] ?? date('Y')+543;
    $amount = $_POST['amount'] ?? 0;
    
    // 2. กำหนดสถานะตามปุ่มที่กด
    $status = ($_POST['action'] == 'submit') ? 'submitted' : 'draft';

    try {
        // Validation เบื้องต้น
        if (empty($fam_id) || empty($amount) || empty($school_name)) {
            throw new Exception("กรุณากรอกข้อมูลสำคัญให้ครบถ้วน (บุตร, โรงเรียน, จำนวนเงิน)");
        }

        // 3. เตรียม SQL Insert
        $sql = "INSERT INTO ath_tuition_request 
                (mem_id, fam_id, req_school_name, req_school_level, req_grade, req_semester, req_academic_year, req_tuition_amount, req_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        
        // 4. บันทึกข้อมูล
        $result = $stmt->execute([
            $mem_id, 
            $fam_id, 
            $school_name, 
            $school_level, 
            $grade, 
            $semester, 
            $academic_year, 
            $amount, 
            $status
        ]);

        if ($result) {
            echo "<script>
                alert('บันทึกคำขอเรียบร้อยแล้ว'); 
                window.location='index.php';
            </script>";
        }

    } catch (Exception $e) {
        // กรณี Error ให้แจ้งเตือนและกลับไปหน้าเดิม
        echo "<script>
            alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "'); 
            window.history.back();
        </script>";
    } catch (PDOException $e) {
        echo "<script>
            alert('Database Error: " . $e->getMessage() . "'); 
            window.history.back();
        </script>";
    }
} else {
    // ถ้าเข้าหน้านี้โดยไม่ได้ Submit Form
    header("Location: index.php");
    exit();
}
?>
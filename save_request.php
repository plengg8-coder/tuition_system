<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mem_id = $_POST['mem_id'];
    $status = ($_POST['action'] == 'submit') ? 'submitted' : 'draft';
    $requests = $_POST['req']; // รับค่า Array จากฟอร์ม
    
    $count_success = 0;

    try {
        $sql = "INSERT INTO ath_tuition_request 
                (mem_id, fam_id, req_school_name, req_school_level, req_grade, req_semester, req_academic_year, req_tuition_amount, req_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        // วนลูปข้อมูลที่ส่งมา (สูงสุด 3 รอบ)
        foreach ($requests as $item) {
            // ตรวจสอบว่ามีการเลือกบุตรหรือไม่ (ถ้า fam_id ว่าง แสดงว่าไม่ได้กรอกช่องนี้)
            if (!empty($item['fam_id']) && !empty($item['amount'])) {
                $stmt->execute([
                    $mem_id,
                    $item['fam_id'],
                    $item['school_name'],
                    $item['school_level'],
                    $item['grade'],
                    $item['semester'],
                    $item['academic_year'],
                    $item['amount'],
                    $status
                ]);
                $count_success++;
            }
        }

        if ($count_success > 0) {
            echo "<script>
                alert('บันทึกข้อมูลเรียบร้อยแล้ว จำนวน {$count_success} รายการ'); 
                window.location='index.php';
            </script>";
        } else {
            echo "<script>alert('กรุณากรอกข้อมูลอย่างน้อย 1 รายการ'); window.history.back();</script>";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
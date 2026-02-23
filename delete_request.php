<?php
session_start();
require_once 'db.php';

// เช็คว่ามีการส่ง ID มาหรือไม่ และมี Session ของผู้ใช้หรือไม่
if (isset($_GET['id']) && isset($_SESSION['mem_id'])) {
    $req_id = $_GET['id'];
    $mem_id = $_SESSION['mem_id'];

    try {
        // ลบข้อมูล (เช็คด้วยว่าต้องเป็นของ mem_id นี้เท่านั้น เพื่อป้องกันคนอื่นมาแอบลบ)
        // และยอมให้ลบเฉพาะสถานะ draft (ฉบับร่าง) เท่านั้น
        $sql = "DELETE FROM ath_tuition_request WHERE req_id = ? AND mem_id = ? AND req_status = 'draft'";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$req_id, $mem_id])) {
            // ลบสำเร็จ ให้เด้งกลับไปหน้า index
            echo "<script>
                alert('ลบข้อมูลเรียบร้อยแล้ว');
                window.location.href = 'index.php';
            </script>";
        } else {
            // ลบไม่สำเร็จ (อาจจะไม่มีสิทธิ์ หรือไม่ใช่สถานะ draft)
            echo "<script>
                alert('ไม่สามารถลบข้อมูลได้ หรือคุณไม่มีสิทธิ์ลบรายการนี้');
                window.location.href = 'index.php';
            </script>";
        }

    } catch (PDOException $e) {
        echo "<script>
            alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "');
            window.location.href = 'index.php';
        </script>";
    }
} else {
    // ถ้าไม่มี ID ส่งมา ให้กลับไปหน้า index
    header("Location: index.php");
    exit();
}
?>
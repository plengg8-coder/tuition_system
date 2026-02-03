<?php
require_once 'db.php';

try {
    // 1. ตรวจสอบ/สร้างข้อมูลสมาชิก ID 3 (moremeng) ก่อน (กัน Error Foreign Key)
    $sql_member = "INSERT IGNORE INTO ath_member (mem_id, mem_name, mem_username, mem_position, mem_department) 
                   VALUES (3, 'ทดสอบระบบ', 'moremeng', 'นักวิชาการคอมพิวเตอร์', 'ศูนย์คอมพิวเตอร์')";
    $pdo->exec($sql_member);
    echo "✅ ตรวจสอบสมาชิก ID 3 เรียบร้อย<br>";

    // 2. เพิ่มข้อมูลบุตรให้ ID 3 (2 คน)
    $sql_child = "INSERT INTO ath_member_family (mem_id, fam_name, fam_birthdate, fam_relationship, fam_created_at) 
                  VALUES 
                  (3, 'ด.ช. เรียนดี มีวินัย (ลูกคนโต)', '2015-05-10', 1, NOW()),
                  (3, 'ด.ญ. ตั้งใจ ศึกษา (ลูกคนเล็ก)', '2018-08-20', 1, NOW())";
    
    $pdo->exec($sql_child);
    echo "✅ เพิ่มข้อมูลบุตรตัวอย่าง 2 คน เรียบร้อย!<br>";
    echo "<hr><h3 style='color: green;'>สำเร็จ! กลับไปหน้าฟอร์มแล้วกดรีเฟรช (F5) ได้เลย</h3>";
    echo "<a href='create_request.php'>กลับไปหน้าสร้างคำขอ</a>";

} catch (PDOException $e) {
    echo "❌ เกิดข้อผิดพลาด: " . $e->getMessage();
}
?>
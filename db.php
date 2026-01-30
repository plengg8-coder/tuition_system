<?php
// db.php
$host = 'localhost';
$dbname = 'sql_athweb_athos'; // ตรวจสอบชื่อ DB ให้ตรงกับใน phpMyAdmin
$username = 'root'; // ปกติของ XAMPP คือ root
$password = '';     // ปกติของ XAMPP คือว่าง

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// ฟังก์ชันแปลงสถานะเป็นภาษาไทยและสี Badge
function getStatusBadge($status) {
    $badges = [
        'draft' => '<span class="badge bg-secondary">ฉบับร่าง</span>',
        'submitted' => '<span class="badge bg-primary">ส่งเรื่องแล้ว</span>',
        'finance_received' => '<span class="badge bg-info text-dark">การเงินรับเรื่อง</span>',
        'approved' => '<span class="badge bg-success">อนุมัติ</span>',
        'pending_payment' => '<span class="badge bg-warning text-dark">รอจ่ายเงิน</span>',
        'completed' => '<span class="badge bg-success">โอนเงินแล้ว</span>',
        'cancelled' => '<span class="badge bg-danger">ยกเลิก</span>',
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}
?>
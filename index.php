<?php 
session_start(); 
require_once 'db.php';

// --- [ส่วนที่เพิ่ม] บังคับใช้รหัสสมาชิก 3 (ตามรูปที่คุณส่งมา) ---
// เพื่อให้หน้า Dashboard แสดงข้อมูลของ User ID 3 ที่คุณเพิ่งบันทึกไป
$_SESSION['mem_id'] = 3; 
$_SESSION['mem_name'] = 'ทดสอบระบบ (User ID 3)';
$_SESSION['mem_position'] = 'นักวิชาการคอมพิวเตอร์';
// -----------------------------------------------------------

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบเบิกค่าเล่าเรียนบุตร - Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root { --c-coral: #EB5E55; --c-graphite: #3A3335; --c-raspberry: #D81E5B; --c-papaya: #FDF0D5; --c-ash: #C6D8D3; }
        body { background-color: var(--c-papaya); color: var(--c-graphite); font-family: 'Sarabun', sans-serif; }
        .navbar-custom { background-color: var(--c-graphite); }
        .card-custom { border: none; border-radius: 12px; color: white; transition: transform 0.2s; }
        .card-custom:hover { transform: translateY(-5px); }
        .bg-gradient-1 { background: linear-gradient(45deg, #3A3335, #605c5d); }
        .bg-gradient-2 { background: linear-gradient(45deg, #EB5E55, #ff8a82); }
        .bg-gradient-3 { background: linear-gradient(45deg, #D81E5B, #ff5c8d); }
        .table thead { background-color: var(--c-ash); }
        .btn-add { background-color: var(--c-raspberry); border: none; color: white; }
        .btn-add:hover { background-color: #b01648; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#"><i class="fas fa-hospital-user me-2"></i> ATH Tuition</a>
        <div class="d-flex text-white align-items-center">
             <div class="me-3 text-end d-none d-md-block">
                 <div class="fw-bold" style="font-size: 0.9rem;"><?php echo $_SESSION['mem_name']; ?></div>
                 <div class="small opacity-75" style="font-size: 0.75rem;"><?php echo $_SESSION['mem_position']; ?></div>
             </div>
             <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card card-custom bg-gradient-1 h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="opacity-75">วงเงินคงเหลือ (ปี 69)</h6>
                    <h2 class="fw-bold mb-0">20,000 <span class="fs-6 fw-normal">บาท</span></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom bg-gradient-2 h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="opacity-75">รอการอนุมัติ</h6>
                    <?php
                        // นับจำนวนรายการที่รออนุมัติ
                        $count_pending = $pdo->prepare("SELECT COUNT(*) FROM ath_tuition_request WHERE mem_id = ? AND req_status IN ('submitted', 'finance_received')");
                        $count_pending->execute([$_SESSION['mem_id']]);
                        $pending_num = $count_pending->fetchColumn();
                    ?>
                    <h2 class="fw-bold mb-0"><?php echo $pending_num; ?> <span class="fs-6 fw-normal">รายการ</span></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom bg-gradient-3 h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="opacity-75">เบิกจ่ายแล้ว</h6>
                    <h2 class="fw-bold mb-0">0 <span class="fs-6 fw-normal">บาท</span></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold m-0"><i class="fas fa-list me-2"></i> รายการคำขอของฉัน</h4>
        <a href="create_request.php" class="btn btn-add shadow-sm rounded-pill px-4">
            <i class="fas fa-plus-circle me-1"></i> สร้างคำขอใหม่
        </a>
    </div>

    <div class="card shadow-sm border-0 overflow-hidden" style="border-radius: 12px;">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">วันที่ยื่น</th>
                        <th>บุตร</th>
                        <th>สถานศึกษา</th>
                        <th>จำนวนเงิน</th>
                        <th>สถานะ</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php
                    try {
                        // SQL: ดึงข้อมูลคำขอ + ชื่อลูก (JOIN ตาราง) เฉพาะของ mem_id นี้ (คือ 3)
                        $sql = "SELECT r.*, f.fam_name 
                                FROM ath_tuition_request r 
                                LEFT JOIN ath_member_family f ON r.fam_id = f.fam_id 
                                WHERE r.mem_id = :mem_id 
                                ORDER BY r.req_id DESC";
                        
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([':mem_id' => $_SESSION['mem_id']]);
                        $requests = $stmt->fetchAll();

                        if (count($requests) > 0) {
                            foreach ($requests as $req) {
                                // แปลงวันที่เป็นรูปแบบไทย (เช่น 03/02/2569)
                                $date = date('d/m', strtotime($req['req_request_date'])) . '/' . (date('Y', strtotime($req['req_request_date'])) + 543);
                                
                                // จัดการกรณีชื่อลูกหาย
                                $child_name = htmlspecialchars($req['fam_name'] ?? 'ไม่ระบุ');

                                echo "<tr>";
                                echo "<td class='ps-4 fw-bold text-secondary'>{$date}</td>";
                                echo "<td><div class='fw-bold text-dark'>{$child_name}</div></td>";
                                echo "<td><span class='text-muted small'>" . htmlspecialchars($req['req_school_name']) . "</span></td>";
                                echo "<td><b class='text-primary'>" . number_format($req['req_tuition_amount'], 2) . "</b></td>";
                                echo "<td>" . getStatusBadge($req['req_status']) . "</td>"; // ใช้ฟังก์ชันจาก db.php
                                echo "<td class='text-center'>
                                        <div class='btn-group'>
                                            <a href='print_request.php?id={$req['req_id']}' target='_blank' class='btn btn-sm btn-light text-secondary' title='พิมพ์ใบเบิก'><i class='fas fa-print'></i></a>
                                            " . ($req['req_status'] == 'draft' ? "<button class='btn btn-sm btn-light text-danger' title='ลบ'><i class='fas fa-trash'></i></button>" : "") . "
                                        </div>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            // กรณีไม่มีข้อมูล
                            echo "<tr><td colspan='6' class='text-center text-muted py-5'>
                                    <i class='fas fa-folder-open fa-3x mb-3 opacity-50'></i><br>
                                    ยังไม่มีรายการคำขอ
                                  </td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='6' class='text-center text-danger py-3'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
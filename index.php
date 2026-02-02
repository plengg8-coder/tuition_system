<?php 
session_start(); 
if (!isset($_SESSION['mem_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db.php'; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบเบิกค่าเล่าเรียนบุตร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --c-coral: #EB5E55; --c-graphite: #3A3335; --c-raspberry: #D81E5B; --c-papaya: #FDF0D5; --c-ash: #C6D8D3; }
        body { background-color: var(--c-papaya); color: var(--c-graphite); font-family: 'Sarabun', sans-serif; }
        .navbar-custom { background-color: var(--c-graphite); }
        .card-custom-1 { background-color: var(--c-graphite); color: white; }
        .card-custom-2 { background-color: var(--c-coral); color: white; }
        .card-custom-3 { background-color: var(--c-raspberry); color: white; }
        .btn-theme-primary { background-color: var(--c-raspberry); border-color: var(--c-raspberry); color: white; }
        .table thead { background-color: var(--c-ash); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#"><i class="fas fa-hospital-user me-2"></i> ATH Tuition</a>
        <div class="d-flex text-white align-items-center">
             <span class="me-3 d-none d-md-block"><i class="fas fa-user-circle"></i> <?php echo $_SESSION['mem_name']; ?></span>
             <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill">ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-4"><div class="card card-custom-1 mb-3"><div class="card-body"><h5 class="card-title opacity-75">วงเงินคงเหลือ</h5><p class="card-text fs-2 fw-bold">20,000 <span class="fs-6">บาท</span></p></div></div></div>
        <div class="col-md-4"><div class="card card-custom-2 mb-3"><div class="card-body"><h5 class="card-title opacity-75">รอการอนุมัติ</h5><p class="card-text fs-2 fw-bold">2 <span class="fs-6">รายการ</span></p></div></div></div>
        <div class="col-md-4"><div class="card card-custom-3 mb-3"><div class="card-body"><h5 class="card-title opacity-75">เบิกจ่ายแล้ว</h5><p class="card-text fs-2 fw-bold">5,000 <span class="fs-6">บาท</span></p></div></div></div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold"><i class="fas fa-list me-2"></i> รายการคำขอของฉัน</h4>
        <a href="create_request.php" class="btn btn-theme-primary shadow-sm"><i class="fas fa-plus-circle me-1"></i> สร้างคำขอใหม่</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th class="ps-4">วันที่</th><th>บุตร</th><th>สถานศึกษา</th><th>จำนวนเงิน</th><th>สถานะ</th><th class="text-center">จัดการ</th></tr></thead>
                <tbody class="bg-white">
                    <?php
                    // ดึงข้อมูลเฉพาะของ User ที่ Login อยู่
                    $stmt = $pdo->prepare("SELECT r.*, f.fam_name FROM ath_tuition_request r 
                                           JOIN ath_member_family f ON r.fam_id = f.fam_id 
                                           WHERE r.mem_id = ? ORDER BY r.req_id DESC");
                    $stmt->execute([$_SESSION['mem_id']]);
                    $requests = $stmt->fetchAll();

                    if (count($requests) > 0) {
                        foreach ($requests as $req) {
                            echo "<tr>
                                    <td class='ps-4'>" . date('d/m/Y', strtotime($req['req_request_date'])) . "</td>
                                    <td><span class='fw-bold text-secondary'>" . htmlspecialchars($req['fam_name']) . "</span></td>
                                    <td>" . htmlspecialchars($req['req_school_name']) . "</td>
                                    <td>" . number_format($req['req_tuition_amount'], 2) . "</td>
                                    <td>" . getStatusBadge($req['req_status']) . "</td>
                                    <td class='text-center'>
                                        <a href='print_request.php?id={$req['req_id']}' target='_blank' class='btn btn-sm btn-outline-secondary border-0'><i class='fas fa-print text-dark'></i></a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center text-muted py-5'>ไม่พบรายการคำขอ</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
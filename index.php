<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบเบิกค่าเล่าเรียนบุตร - ATH Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fas fa-hospital-user"></i> ATH Tuition System</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">วงเงินคงเหลือ (ปี 69)</h5>
                    <p class="card-text fs-3">20,000 บาท</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title text-dark">รอการอนุมัติ</h5>
                    <p class="card-text fs-3 text-dark">2 รายการ</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">เบิกจ่ายแล้ว</h5>
                    <p class="card-text fs-3">5,000 บาท</p>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-list"></i> รายการคำขอเบิกของฉัน</h4>
        <a href="create_request.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> สร้างคำขอใหม่</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>วันที่ยื่น</th>
                        <th>บุตร</th>
                        <th>สถานศึกษา</th>
                        <th>จำนวนเงิน</th>
                        <th>สถานะ</th>
                        <th style="width: 150px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // ดึงข้อมูลจริงจาก DB
                    // (Mockup data: mem_id = 1 สมมติว่าเป็น User ที่ login อยู่)
                    $stmt = $pdo->prepare("SELECT r.*, f.fam_name FROM ath_tuition_request r 
                                           JOIN ath_member_family f ON r.fam_id = f.fam_id 
                                           WHERE r.mem_id = 1 ORDER BY r.req_id DESC");
                    $stmt->execute();
                    $requests = $stmt->fetchAll();

                    if (count($requests) > 0) {
                        foreach ($requests as $req) {
                            echo "<tr>";
                            echo "<td>" . date('d/m/Y', strtotime($req['req_request_date'])) . "</td>";
                            echo "<td>" . htmlspecialchars($req['fam_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($req['req_school_name']) . "</td>";
                            echo "<td>" . number_format($req['req_tuition_amount'], 2) . "</td>";
                            echo "<td>" . getStatusBadge($req['req_status']) . "</td>";
                            
                            // --- ส่วนที่แก้ไข: เพิ่มปุ่ม Print ---
                            echo "<td>
                                    <div class='btn-group' role='group'>
                                        <a href='#' class='btn btn-sm btn-outline-primary' title='ดูรายละเอียด'>
                                            <i class='fas fa-eye'></i>
                                        </a>
                                        <a href='print_request.php?id={$req['req_id']}' target='_blank' class='btn btn-sm btn-outline-secondary' title='พิมพ์ใบเบิก'>
                                            <i class='fas fa-print'></i>
                                        </a>
                                    </div>
                                  </td>";
                            // ----------------------------------
                            
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center text-muted py-4'>ไม่พบรายการคำขอ</td></tr>";
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
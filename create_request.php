<?php 
session_start();
require_once 'db.php';

// ตรวจสอบว่ามี Session จากการ Login หรือไม่
$user_id = $_SESSION['mem_id'] ?? 3;
$user_name = $_SESSION['mem_name'] ?? 'ทดสอบระบบ';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สร้างคำขอเบิก - ATH System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i> แบบขอเบิกเงินสวัสดิการการศึกษาบุตร</h5>
                </div>
                <div class="card-body p-4">
                    
                    <div class="alert alert-light border mb-4">
                        <small class="text-muted">ผู้ยื่นคำขอ:</small><br>
                        <strong><?php echo htmlspecialchars($user_name); ?></strong> (รหัสสมาชิก: <?php echo $user_id; ?>)
                    </div>

                    <form action="save_request.php" method="POST">
                        <input type="hidden" name="mem_id" value="<?php echo $user_id; ?>">

                        <h6 class="fw-bold text-secondary mb-3">ข้อมูลบุตร</h6>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0">เลือกบุตรที่ต้องการเบิก</label>
                                <a href="add_child.php" class="btn btn-sm btn-outline-danger" style="border-radius: 20px;">
                                    <i class="fas fa-plus"></i> เพิ่มข้อมูลบุตรใหม่
                                </a>
                            </div>
                            
                            <select name="fam_id" class="form-select" required>
                                <option value="">-- กรุณาเลือก --</option>
                                <?php
                                try {
                                    $stmt = $pdo->prepare("
                                        SELECT * FROM ath_member_family 
                                        WHERE mem_id = :mem_id 
                                        AND fam_relationship = 1 
                                        ORDER BY fam_birthdate ASC
                                    ");
                                    $stmt->execute([':mem_id' => $user_id]);
                                    
                                    if ($stmt->rowCount() > 0) {
                                        while ($row = $stmt->fetch()) {
                                            // แปลงปี ค.ศ. เป็น พ.ศ. ให้ดูง่ายขึ้น
                                            $b_year = date('Y', strtotime($row['fam_birthdate'])) + 543;
                                            echo "<option value='" . $row['fam_id'] . "'>";
                                            echo $row['fam_name'] . " (เกิดปี พ.ศ. " . $b_year . ")";
                                            echo "</option>";
                                        }
                                    } else {
                                        echo "<option value='' disabled>ไม่พบข้อมูลบุตร (โปรดเพิ่มข้อมูลก่อน)</option>";
                                    }
                                } catch (PDOException $e) { /* Ignore */ }
                                ?>
                            </select>
                        </div>

                        <h6 class="fw-bold text-secondary mb-3 mt-4">ข้อมูลการศึกษา</h6>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">ชื่อสถานศึกษา</label>
                                <input type="text" name="school_name" class="form-control" placeholder="เช่น โรงเรียนอนุบาลอ่างทอง" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ระดับชั้น</label>
                                <select name="school_level" class="form-select" required>
                                    <option value="ปฐมวัย">ปฐมวัย</option>
                                    <option value="ประถมศึกษา">ประถมศึกษา</option>
                                    <option value="มัธยมศึกษา">มัธยมศึกษา</option>
                                    <option value="อาชีวศึกษา">อาชีวศึกษา</option>
                                    <option value="อุดมศึกษา">อุดมศึกษา</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">จำนวนเงิน (บาท)</label>
                                <input type="number" name="request_amount" class="form-control" placeholder="0.00" step="0.01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">วันที่เบิก</label>
                                <input type="date" name="request_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="remark" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="index.php" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" name="action" value="draft" class="btn btn-outline-primary" formnovalidate>บันทึกแบบร่าง</button>
                            <button type="submit" name="action" value="submit" class="btn btn-primary shadow">ยืนยันส่งคำขอ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
require_once 'db.php';

// สร้าง Mock Session สำหรับการทดสอบ
if (empty($_SESSION)) {
    $_SESSION = array(
        'url' => '/athweb/member',
        'announcement_shown' => 1,
        'ses_id' => 3,
        'ses_user' => 'moremeng',
        'ses_role' => 'cn3',
        'ses_position' => 99,
        'ses_name' => 'ทดสอบระบบ',
        'ses_sec_id' => 123,
        'ses_sec_name' => '(ยกเลิก 2025-11-14) ll',
        'ses_seat' => 'นักวิชาการคอมพิวเตอร์',
        'log_session' => 'sess_69806da196b124.46386653',
        'token' => '452246d0eb7403aab5e3720bb68eced5b3afb7cd'
    );
}

// ดึงข้อมูลสมาชิกจาก session
$ses_id = $_SESSION['ses_id'] ?? null;
$ses_name = $_SESSION['ses_name'] ?? 'ไม่ระบุ';
$ses_user = $_SESSION['ses_user'] ?? 'ไม่ระบุ';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สร้างคำขอเบิก - ATH System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-file-invoice-dollar"></i> แบบขอเบิกเงินสวัสดิการเกี่ยวกับการศึกษาบุตร</h4>
                </div>
                <div class="card-body">
                    <!-- แสดงข้อมูล Session -->
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">ข้อมูลผู้ใช้ปัจจุบัน</h6>
                        <small>
                            <strong>ชื่อผู้ใช้:</strong> <?php echo htmlspecialchars($ses_user); ?> | 
                            <strong>ชื่อเต็ม:</strong> <?php echo htmlspecialchars($ses_name); ?> | 
                            <strong>รหัสสมาชิก:</strong> <?php echo htmlspecialchars($ses_id); ?>
                        </small>
                    </div>

                    <form action="save_request.php" method="POST">
                        
                        <input type="hidden" name="mem_id" value="<?php echo htmlspecialchars($ses_id); ?>">

                        <h5 class="text-secondary mb-3">ข้อมูลบุตร</h5>
                        <div class="mb-3">
                            <label class="form-label">เลือกบุตรที่ต้องการเบิก</label>
                            <select name="fam_id" class="form-select" required>
                                <option value="">-- กรุณาเลือก --</option>
                                <?php
                                // ดึงรายชื่อลูกจาก DB โดยใช้ ses_id เป็น mem_id
                                // ค้นหาเฉพาะความสัมพันธ์ = 1 (บุตร) และยังไม่ถูกลบ (fam_deleted_status = 0)
                                try {
                                    $stmt = $pdo->prepare("
                                        SELECT * FROM ath_member_family 
                                        WHERE mem_id = :mem_id 
                                        AND fam_relationship = 1 
                                        AND fam_deleted_status = 0
                                        ORDER BY fam_name ASC
                                    ");
                                    $stmt->execute([':mem_id' => $ses_id]);
                                    
                                    if ($stmt->rowCount() > 0) {
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value='" . htmlspecialchars($row['fam_id']) . "'>";
                                            echo htmlspecialchars($row['fam_name']);
                                            echo " (เกิด: " . htmlspecialchars($row['fam_birthdate']) . ")";
                                            echo "</option>";
                                        }
                                    } else {
                                        echo "<option value='' disabled>ไม่พบข้อมูลบุตร</option>";
                                    }
                                } catch (PDOException $e) {
                                    echo "<option value='' disabled>เกิดข้อผิดพลาดในการดึงข้อมูล</option>";
                                    error_log("Database Error: " . $e->getMessage());
                                }
                                ?>
                            </select>
                        </div>

                        <h5 class="text-secondary mb-3 mt-4">ข้อมูลการศึกษา</h5>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">ชื่อสถานศึกษา</label>
                                <input type="text" name="school_name" class="form-control" placeholder="ระบุชื่อโรงเรียน/มหาวิทยาลัย" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ระดับการศึกษา</label>
                                <select name="school_level" class="form-select" required>
                                    <option value="">-- เลือก --</option>
                                    <option value="ปฐมวัย">ปฐมวัย (อนุบาล)</option>
                                    <option value="ประถมศึกษา">ประถมศึกษา</option>
                                    <option value="มัธยมศึกษา">มัธยมศึกษา</option>
                                    <option value="ปวช">ปวช (ประเทศศึกษา)</option>
                                    <option value="ปวส">ปวส (อุตสาหกรรม)</option>
                                    <option value="อุดมศึกษา">อุดมศึกษา</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">จำนวนเงินที่ขอเบิก (บาท)</label>
                                <input type="number" name="request_amount" class="form-control" placeholder="0" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">วันที่ขอเบิก</label>
                                <input type="date" name="request_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="remark" class="form-control" rows="3" placeholder="ระบุรายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="member.php" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary">บันทึกคำขอ</button>
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
<?php require_once 'db.php'; ?>
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
                    <form action="save_request.php" method="POST">
                        
                        <input type="hidden" name="mem_id" value="1">

                        <h5 class="text-secondary mb-3">ข้อมูลบุตร</h5>
                        <div class="mb-3">
                            <label class="form-label">เลือกบุตรที่ต้องการเบิก</label>
                            <select name="fam_id" class="form-select" required>
                                <option value="">-- กรุณาเลือก --</option>
                                <?php
                                // ดึงรายชื่อลูกจาก DB
                                $stmt = $pdo->prepare("SELECT * FROM ath_member_family WHERE mem_id = 1 AND fam_relationship = 1");
                                $stmt->execute();
                                while ($row = $stmt->fetch()) {
                                    echo "<option value='{$row['fam_id']}'>{$row['fam_name']} (เกิด: {$row['fam_birthdate']})</option>";
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
                                    <option value="ปฐมวัย">ปฐมวัย (อนุบาล)</option>
                                    <option value="ประถมศึกษา">ประถมศึกษา</option>
                                    <option value="มัธยมศึกษา">มัธยมศึกษา</option>
                                    <option value="อุดมศึกษา">อุดมศึกษา (ป.ตรี)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ชั้นเรียน</label>
                                <input type="text" name="grade" class="form-control" placeholder="เช่น ป.1, ม.4" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ภาคเรียนที่ (เทอม)</label>
                                <select name="semester" class="form-select">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3 (ภาคฤดูร้อน)</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ปีการศึกษา (พ.ศ.)</label>
                                <input type="number" name="academic_year" class="form-control" value="<?php echo date('Y')+543; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">จำนวนเงินที่ขอเบิก (บาท)</label>
                            <input type="number" step="0.01" name="amount" class="form-control form-control-lg" placeholder="0.00" required>
                            <div class="form-text text-danger">* กรุณากรอกตามใบเสร็จรับเงินจริง</div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="index.php" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" name="action" value="draft" class="btn btn-outline-primary">บันทึกแบบร่าง</button>
                            <button type="submit" name="action" value="submit" class="btn btn-primary">ยืนยันส่งคำขอ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
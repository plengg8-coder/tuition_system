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
    <title>สร้างคำขอเบิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --c-coral: #EB5E55; --c-graphite: #3A3335; --c-raspberry: #D81E5B; --c-papaya: #FDF0D5; --c-ash: #C6D8D3; }
        body { background-color: var(--c-papaya); color: var(--c-graphite); font-family: 'Sarabun', sans-serif; }
        .card-form { border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 12px; }
        .card-header-custom { background-color: var(--c-ash); color: var(--c-graphite); font-weight: bold; padding: 15px 20px; }
        .btn-submit { background-color: var(--c-coral); border: none; color: white; padding: 10px 30px; }
        .btn-submit:hover { background-color: #d14940; color: white; }
    </style>
</head>
<body class="py-5">
<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <h3 class="mb-4 fw-bold"><i class="fas fa-file-invoice-dollar me-2" style="color: var(--c-raspberry);"></i> สร้างคำขอเบิกค่าเล่าเรียน</h3>
            
            <form action="save_request.php" method="POST">
                <input type="hidden" name="mem_id" value="<?php echo $_SESSION['mem_id']; ?>"> 

                <div class="card card-form mb-4 bg-white">
                    <div class="card-header-custom"><i class="fas fa-pen-nib me-2"></i> กรอกข้อมูลการเบิก (รายบุคคล)</div>
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            <div class="col-md-7">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0 fw-bold">เลือกบุตร (ลำดับที่ 1-3)</label>
                                    <a href="add_family.php" class="btn btn-sm btn-outline-primary rounded-pill" style="font-size: 0.8rem;">
                                        <i class="fas fa-plus"></i> เพิ่มรายชื่อใหม่
                                    </a>
                                </div>
                                <select name="fam_id" class="form-select form-select-lg" required>
                                    <option value="">-- กรุณาเลือกรายชื่อ --</option>
                                    <?php
                                    // ดึงรายชื่อบุตรเฉพาะของ User ที่ Login
                                    $stmt = $pdo->prepare("SELECT * FROM ath_member_family WHERE mem_id = ? AND fam_relationship = 1 ORDER BY fam_birthdate ASC");
                                    $stmt->execute([$_SESSION['mem_id']]);
                                    $i = 1;
                                    while ($child = $stmt->fetch()) {
                                        echo "<option value='{$child['fam_id']}'>{$i}. {$child['fam_name']} (เกิด: {$child['fam_birthdate']})</option>";
                                        $i++;
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-bold">จำนวนเงิน (บาท)</label>
                                <input type="number" step="0.01" name="amount" class="form-control form-control-lg text-end" placeholder="0.00" required>
                            </div>
                        </div>
                        <hr class="text-muted opacity-25 my-4">
                        <h6 class="fw-bold mb-3 text-secondary"><i class="fas fa-school me-1"></i> ข้อมูลสถานศึกษา</h6>
                        <div class="row mb-3">
                            <div class="col-md-12"><label class="form-label">ชื่อสถานศึกษา</label><input type="text" name="school_name" class="form-control" required></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><label class="form-label">ระดับชั้น</label>
                                <select name="school_level" class="form-select" required>
                                    <option value="ปฐมวัย">ปฐมวัย</option><option value="ประถมศึกษา">ประถมศึกษา</option><option value="มัธยมศึกษา">มัธยมศึกษา</option><option value="อุดมศึกษา">อุดมศึกษา</option>
                                </select>
                            </div>
                            <div class="col-md-4"><label class="form-label">ชั้นปีที่</label><input type="text" name="grade" class="form-control" placeholder="เช่น ป.1" required></div>
                            <div class="col-md-4"><label class="form-label">เทอม</label>
                                <select name="semester" class="form-select"><option value="1">1</option><option value="2">2</option><option value="3">3</option></select>
                            </div>
                        </div>
                        <div class="row"><div class="col-md-4"><label class="form-label">ปีการศึกษา</label><input type="number" name="academic_year" class="form-control" value="<?php echo date('Y')+543; ?>" required></div></div>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center gap-3 mt-4">
                    <a href="index.php" class="btn text-secondary">ยกเลิก</a>
                    <button type="submit" name="action" value="draft" class="btn btn-outline-danger rounded-pill">บันทึกแบบร่าง</button>
                    <button type="submit" name="action" value="submit" class="btn btn-submit rounded-pill shadow">ยืนยันส่งคำขอ</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
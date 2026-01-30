<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สร้างคำขอเบิก - ATH System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h4 class="mb-4"><i class="fas fa-file-invoice-dollar"></i> สร้างคำขอเบิกค่าเล่าเรียน</h4>
            
            <form action="save_request.php" method="POST">
                <input type="hidden" name="mem_id" value="1"> <?php 
                // วนลูปสร้างฟอร์ม 3 ชุด
                for ($i = 0; $i < 3; $i++): 
                    $num = $i + 1;
                    $required = ($i == 0) ? 'required' : ''; // บังคับแค่คนแรก
                    $show = ($i == 0) ? 'show' : ''; // เปิดAccordionแค่คนแรก
                ?>
                
                <div class="accordion mb-3" id="accordionChild<?php echo $i; ?>">
                    <div class="accordion-item shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?php echo ($i > 0) ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $i; ?>">
                                <strong><i class="fas fa-user-graduate"></i> ข้อมูลบุตรคนที่ <?php echo $num; ?></strong> 
                                <?php if($i > 0) echo '<small class="text-muted ms-2">(ระบุเฉพาะกรณีต้องการเบิกเพิ่ม)</small>'; ?>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $i; ?>" class="accordion-collapse collapse <?php echo $show; ?>" data-bs-parent="#accordionChild<?php echo $i; ?>">
                            <div class="accordion-body">
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">เลือกบุตร</label>
                                        <select name="req[<?php echo $i; ?>][fam_id]" class="form-select" <?php echo $required; ?>>
                                            <option value="">-- กรุณาเลือก --</option>
                                            <?php
                                            // ต้อง reset pointer ของ query ทุกครั้ง หรือ fetchAll ไว้ก่อน
                                            $stmt = $pdo->prepare("SELECT * FROM ath_member_family WHERE mem_id = 1 AND fam_relationship = 1");
                                            $stmt->execute();
                                            while ($row = $stmt->fetch()) {
                                                echo "<option value='{$row['fam_id']}'>{$row['fam_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">จำนวนเงินที่ขอเบิก (บาท)</label>
                                        <input type="number" step="0.01" name="req[<?php echo $i; ?>][amount]" class="form-control" placeholder="0.00" <?php echo $required; ?>>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อสถานศึกษา</label>
                                        <input type="text" name="req[<?php echo $i; ?>][school_name]" class="form-control" <?php echo $required; ?>>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">ระดับชั้น</label>
                                        <select name="req[<?php echo $i; ?>][school_level]" class="form-select" <?php echo $required; ?>>
                                            <option value="ปฐมวัย">ปฐมวัย</option>
                                            <option value="ประถมศึกษา">ประถมศึกษา</option>
                                            <option value="มัธยมศึกษา">มัธยมศึกษา</option>
                                            <option value="อุดมศึกษา">อุดมศึกษา</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">ชั้นปีที่</label>
                                        <input type="text" name="req[<?php echo $i; ?>][grade]" class="form-control" placeholder="เช่น ป.1" <?php echo $required; ?>>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">เทอม</label>
                                        <select name="req[<?php echo $i; ?>][semester]" class="form-select">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3 (ฤดูร้อน)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ปีการศึกษา (พ.ศ.)</label>
                                        <input type="number" name="req[<?php echo $i; ?>][academic_year]" class="form-control" value="<?php echo date('Y')+543; ?>" <?php echo $required; ?>>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="index.php" class="btn btn-secondary">ยกเลิก</a>
                    <button type="submit" name="action" value="draft" class="btn btn-outline-primary">บันทึกแบบร่าง</button>
                    <button type="submit" name="action" value="submit" class="btn btn-primary px-4">ยืนยันส่งคำขอ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
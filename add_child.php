<?php 
session_start();
// เช็คว่าล็อกอินหรือยัง (หรือใช้ Mock ID 3)
$user_id = $_SESSION['mem_id'] ?? 3;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มข้อมูลบุตร - ATH System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #FDF0D5; font-family: 'Sarabun', sans-serif; }
        .card-custom { border-radius: 12px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header bg-dark text-white" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-child me-2"></i> เพิ่มข้อมูลบุตร</h5>
                </div>
                <div class="card-body p-4">
                    <form action="save_child.php" method="POST">
                        <input type="hidden" name="mem_id" value="<?php echo $user_id; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อ-นามสกุล บุตร</label>
                            <input type="text" name="fam_name" class="form-control" placeholder="เช่น ด.ช. เรียนดี มีวินัย" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">วัน/เดือน/ปีเกิด (ค.ศ.)</label>
                            <input type="date" name="fam_birthdate" class="form-control" required>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="create_request.php" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary" style="background-color: #EB5E55; border: none;">
                                <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
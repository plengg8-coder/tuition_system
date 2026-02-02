<?php session_start(); ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ATH Tuition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --c-coral: #EB5E55;
            --c-graphite: #3A3335;
            --c-raspberry: #D81E5B;
            --c-papaya: #FDF0D5;
        }
        body {
            background-color: var(--c-papaya);
            color: var(--c-graphite);
            font-family: 'Sarabun', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            background: white;
            overflow: hidden;
        }
        .login-header {
            background-color: var(--c-graphite);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .btn-login {
            background-color: var(--c-coral);
            border: none;
            color: white;
            padding: 12px;
            font-weight: bold;
            width: 100%;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background-color: #d14940;
            transform: translateY(-2px);
        }
        .form-control {
            background-color: #f8f9fa;
            border: 1px solid #eee;
            padding: 12px;
            border-radius: 8px;
        }
        .form-control:focus {
            border-color: var(--c-coral);
            box-shadow: none;
            background-color: white;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <h3 class="mb-0"><i class="fas fa-hospital-user me-2" style="color: var(--c-coral);"></i> ATH System</h3>
            <p class="small text-white-50 mb-0">ระบบเบิกค่าเล่าเรียนบุตร</p>
        </div>
        <div class="card-body p-4">
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger text-center p-2 small">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success text-center p-2 small">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="login_check.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">ชื่อผู้ใช้งาน (Username)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted"></i></span>
                        <input type="text" name="username" class="form-control border-start-0" placeholder="ระบุชื่อผู้ใช้" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">รหัสผ่าน (Password)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0" placeholder="ระบุรหัสผ่าน" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-login shadow-sm">
                    เข้าสู่ระบบ <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>
            
            <div class="text-center mt-4 pt-3 border-top">
                <span class="text-muted small">ยังไม่มีบัญชีผู้ใช้งาน?</span><br>
                <a href="register.php" class="text-decoration-none fw-bold mt-2 d-inline-block" style="color: var(--c-raspberry);">
                    <i class="fas fa-user-plus me-1"></i> สมัครสมาชิกใหม่
                </a>
            </div>
        </div>
    </div>

</body>
</html>
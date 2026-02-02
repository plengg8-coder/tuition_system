<?php session_start(); ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิกใหม่ - ATH Tuition</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --c-coral: #EB5E55;
            --c-graphite: #3A3335;
            --c-papaya: #FDF0D5;
            --c-raspberry: #D81E5B;
        }
        
        body {
            /* พื้นหลัง Gradient เคลื่อนไหว */
            background: linear-gradient(-45deg, var(--c-papaya), #ffe3e1, #fff5d7, #e8f5e9);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            font-family: 'Sarabun', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .register-card {
            width: 100%;
            max-width: 550px;
            border: none;
            border-radius: 20px;
            /* Glassmorphism Effect */
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1), 0 5px 15px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .register-card:hover {
            transform: translateY(-5px);
        }

        .register-header {
            background-color: var(--c-graphite);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        /* วงกลมตกแต่ง */
        .register-header::before {
            content: ''; position: absolute; top: -20px; right: -20px;
            width: 100px; height: 100px; background: var(--c-coral);
            border-radius: 50%; opacity: 0.5;
        }
        .register-header::after {
            content: ''; position: absolute; bottom: -30px; left: -30px;
            width: 80px; height: 80px; background: var(--c-raspberry);
            border-radius: 50%; opacity: 0.5;
        }

        .btn-register {
            background: linear-gradient(45deg, var(--c-coral), var(--c-raspberry));
            border: none; color: white; padding: 12px;
            font-weight: bold; width: 100%; border-radius: 50px;
            box-shadow: 0 4px 15px rgba(235, 94, 85, 0.4);
            transition: all 0.3s;
        }
        .btn-register:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 20px rgba(235, 94, 85, 0.6);
            color: white;
        }

        /* Floating Labels Styling */
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: var(--c-raspberry); font-weight: bold; opacity: 0.8;
        }
        .form-control:focus {
            border-color: var(--c-coral);
            box-shadow: 0 0 0 0.2rem rgba(235, 94, 85, 0.15);
        }

        .password-toggle { cursor: pointer; color: #aaa; z-index: 10; }
        .password-toggle:hover { color: var(--c-graphite); }
    </style>
</head>
<body>

    <div class="register-card animate__animated animate__fadeInUp">
        <div class="register-header">
            <h3 class="mb-1 fw-bold"><i class="fas fa-user-plus me-2"></i> สมัครสมาชิก</h3>
            <p class="small text-white-50 mb-0">ระบบเบิกสวัสดิการการศึกษาบุตร</p>
        </div>
        <div class="card-body p-4 p-md-5">
            
            <form action="register_save.php" method="POST" id="registerForm">
                
                <div class="text-secondary small mb-3 fw-bold"><i class="fas fa-info-circle"></i> ข้อมูลส่วนตัว</div>
                
                <div class="form-floating mb-3">
                    <input type="text" name="mem_name" class="form-control" id="floatingName" placeholder="ชื่อ-นามสกุล" required>
                    <label for="floatingName">ชื่อ-นามสกุล</label>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="mem_position" class="form-control" id="floatingPos" placeholder="ตำแหน่ง" required>
                            <label for="floatingPos">ตำแหน่ง</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="mem_department" class="form-control bg-light" id="floatingDept" value="โรงพยาบาลอ่างทอง" readonly>
                            <label for="floatingDept">สังกัด</label>
                        </div>
                    </div>
                </div>

                <div class="text-secondary small mb-3 fw-bold"><i class="fas fa-lock"></i> บัญชีผู้ใช้งาน</div>

                <div class="form-floating mb-3">
                    <input type="text" name="mem_username" class="form-control" id="floatingUser" placeholder="Username" required>
                    <label for="floatingUser">ชื่อผู้ใช้งาน (Username)</label>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="form-floating flex-grow-1">
                                <input type="password" name="mem_password" class="form-control" id="pass1" placeholder="รหัสผ่าน" required>
                                <label for="pass1">รหัสผ่าน</label>
                            </div>
                            <span class="input-group-text bg-white border-start-0" onclick="togglePass('pass1', 'eye1')">
                                <i class="fas fa-eye password-toggle" id="eye1"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="form-floating flex-grow-1">
                                <input type="password" name="confirm_password" class="form-control" id="pass2" placeholder="ยืนยัน" required>
                                <label for="pass2">ยืนยันรหัสผ่าน</label>
                            </div>
                            <span class="input-group-text bg-white border-start-0" onclick="togglePass('pass2', 'eye2')">
                                <i class="fas fa-eye password-toggle" id="eye2"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-register" id="btnSubmit">
                    <span id="btnText">ยืนยันการสมัคร <i class="fas fa-arrow-right ms-1"></i></span>
                    <span id="btnLoading" class="d-none"><i class="fas fa-spinner fa-spin"></i> กำลังบันทึก...</span>
                </button>

                <div class="text-center mt-4">
                    <span class="text-muted small">มีบัญชีอยู่แล้ว?</span>
                    <a href="login.php" class="text-decoration-none fw-bold ms-1" style="color: var(--c-coral);">เข้าสู่ระบบ</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // 1. กดดูรหัสผ่าน
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye"); icon.classList.add("fa-eye-slash");
                icon.style.color = "var(--c-coral)";
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash"); icon.classList.add("fa-eye");
                icon.style.color = "#aaa";
            }
        }

        // 2. Loading Effect
        document.getElementById('registerForm').addEventListener('submit', function() {
            document.getElementById('btnSubmit').disabled = true;
            document.getElementById('btnText').classList.add('d-none');
            document.getElementById('btnLoading').classList.remove('d-none');
        });

        // 3. แสดง Error Popup (ถ้ามี)
        <?php if(isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'ขออภัย...',
                text: '<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>',
                confirmButtonColor: '#EB5E55'
            });
        <?php endif; ?>
    </script>
</body>
</html>
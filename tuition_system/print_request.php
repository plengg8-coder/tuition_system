<?php
require_once 'db.php';

// ตรวจสอบว่ามีการส่ง ID มาหรือไม่
if (!isset($_GET['id'])) {
    die("Error: ไม่พบรหัสคำขอ");
}

$req_id = $_GET['id'];

// ดึงข้อมูลคำขอ + ข้อมูลบุตร + ข้อมูลสมาชิก
// หมายเหตุ: ในระบบจริงควร Join ตารางตำแหน่ง/สังกัด เพิ่มเติม
$sql = "SELECT r.*, f.fam_name, f.fam_birthdate, m.mem_name 
        FROM ath_tuition_request r 
        JOIN ath_member_family f ON r.fam_id = f.fam_id 
        JOIN ath_member m ON r.mem_id = m.mem_id
        WHERE r.req_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$req_id]);
$data = $stmt->fetch();

if (!$data) {
    die("Error: ไม่พบข้อมูล");
}

// ฟังก์ชันแปลงวันที่เป็นไทย
function thai_date($date) {
    if(!$date) return "................................";
    $months = [
        1=>"มกราคม", 2=>"กุมภาพันธ์", 3=>"มีนาคม", 4=>"เมษายน", 
        5=>"พฤษภาคม", 6=>"มิถุนายน", 7=>"กรกฎาคม", 8=>"สิงหาคม", 
        9=>"กันยายน", 10=>"ตุลาคม", 11=>"พฤศจิกายน", 12=>"ธันวาคม"
    ];
    $d = date("d", strtotime($date));
    $m = date("n", strtotime($date));
    $y = date("Y", strtotime($date)) + 543;
    return "$d $months[$m] $y";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบเบิกเงินสวัสดิการเกี่ยวกับการศึกษาบุตร (แบบ 7223)</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 14pt; /* ขนาดตัวอักษรมาตรฐานราชการ */
            line-height: 1.5;
            color: #000;
        }
        .page-a4 {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 10px auto;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .header-right { text-align: right; }
        .header-center { text-align: center; font-weight: bold; font-size: 16pt; margin-top: 20px; margin-bottom: 20px; }
        .content-line { margin-bottom: 8px; text-align: justify; }
        .dotted-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            padding: 0 5px;
            color: blue; /* สีน้ำเงินเพื่อให้รู้ว่าเป็นข้อมูลจากระบบ (ตอนปริ้นขาวดำจะเป็นสีเทา) */
        }
        .checkbox {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1px solid #000;
            margin-right: 5px;
            vertical-align: middle;
        }
        .checked { background-color: #000; } /* จำลองการติ๊กถูก */
        
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .col-half { width: 48%; }
        
        /* ซ่อนปุ่มตอนสั่งปริ้น */
        @media print {
            body { background: none; -webkit-print-color-adjust: exact; }
            .page-a4 { margin: 0; box-shadow: none; width: auto; height: auto; page-break-after: always; }
            .no-print { display: none; }
            .dotted-line { color: #000; border-bottom: none; text-decoration: underline dotted; } /* ปรับให้ดูเป็นธรรมชาติ */
        }

        .btn-print {
            padding: 10px 20px;
            background-color: #0d6efd;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div style="text-align: center;" class="no-print">
        <br>
        <button onclick="window.print()" class="btn-print">🖨️ พิมพ์ใบเบิก / บันทึกเป็น PDF</button>
        <a href="index.php" style="margin-left: 10px; text-decoration: none; color: #666;">กลับหน้าหลัก</a>
    </div>

    <div class="page-a4">
        <div class="header-right">แบบ 7223</div>
        <div class="header-center">ใบเบิกเงินสวัสดิการเกี่ยวกับการศึกษาบุตร</div>
        <div class="content-line" style="text-align: center;">
            โปรดทำเครื่องหมาย <span class="checkbox"></span> ลงในช่อง <span class="checkbox"></span> พร้อมทั้งกรอกข้อความที่จำเป็น
        </div>

        <div class="content-line">
            ข้าพเจ้า <span class="dotted-line" style="min-width: 200px; text-align: center;"><?php echo $data['mem_name']; ?></span>
            ตำแหน่ง <span class="dotted-line" style="min-width: 150px;">เจ้าพนักงาน (สมมติ)</span>
            สังกัด <span class="dotted-line" style="min-width: 150px;">โรงพยาบาลอ่างทอง</span>
        </div>

        <div class="content-line">
            คู่สมรสของข้าพเจ้าชื่อ <span class="dotted-line" style="min-width: 200px;">............................................................</span>
        </div>
        <div class="content-line" style="padding-left: 20px;">
            <span class="checkbox"></span> ไม่เป็นข้าราชการหรือลูกจ้างประจำ
            <span class="checkbox"></span> เป็นข้าราชการ
            <span class="checkbox"></span> ลูกจ้างประจำ
        </div>

        <div class="content-line">
            ข้าพเจ้าเป็นผู้มีสิทธิและขอใช้สิทธิเนื่องจาก
            <span class="checkbox checked"></span> เป็นบิดาชอบด้วยกฎหมาย
            <span class="checkbox"></span> เป็นมารดา
        </div>

        <div class="content-line">
            ข้าพเจ้าได้จ่ายเงินสำหรับการศึกษาของบุตร ดังนี้
            <span class="checkbox"></span> (1) เงินบำรุงการศึกษา
            <span class="checkbox checked"></span> (2) เงินค่าเล่าเรียน
        </div>

        <div style="border: 1px solid #000; padding: 10px; margin: 10px 0;">
            <div class="content-line">
                บุตรชื่อ <span class="dotted-line"><?php echo $data['fam_name']; ?></span>
                เกิดเมื่อ <span class="dotted-line"><?php echo thai_date($data['fam_birthdate']); ?></span>
            </div>
            <div class="content-line">
                เป็นบุตรลำดับที่ (ของบิดา) <span class="dotted-line" style="width: 30px; text-align: center;">1</span>
                เป็นบุตรลำดับที่ (ของมารดา) <span class="dotted-line" style="width: 30px; text-align: center;">1</span>
            </div>
            <div class="content-line">
                สถานศึกษา <span class="dotted-line"><?php echo $data['req_school_name']; ?></span>
                อำเภอ/เขต <span class="dotted-line">เมือง</span>
                จังหวัด <span class="dotted-line">อ่างทอง</span>
            </div>
            <div class="content-line">
                ชั้นที่ศึกษา <span class="dotted-line"><?php echo $data['req_grade']; ?></span>
                จำนวนเงิน <span class="dotted-line"><?php echo number_format($data['req_tuition_amount'], 2); ?></span> บาท
            </div>
        </div>
        <div class="content-line" style="text-align: right;">
            รวมเงินขอเบิก <span class="dotted-line" style="min-width: 100px; text-align: center; font-weight: bold;"><?php echo number_format($data['req_tuition_amount'], 2); ?></span> บาท
        </div>
        <div class="content-line" style="text-align: right;">
            (ตัวอักษร) <span class="dotted-line" style="min-width: 200px;">(............................................................)</span>
        </div>

        <div class="content-line">
            ข้าพเจ้าขอรับรองว่าข้อความข้างต้นเป็นความจริง และข้าพเจ้ามีสิทธิได้รับตามพระราชกฤษฎีกา
        </div>

        <div class="signature-section">
            <div class="col-half"></div>
            <div class="col-half" style="text-align: center;">
                <br>
                (ลงชื่อ) ........................................................... ผู้ขอรับเงินสวัสดิการ<br>
                ( <span class="dotted-line"><?php echo $data['mem_name']; ?></span> )<br>
                วันที่ ........ เดือน ........................ พ.ศ. ............
            </div>
        </div>

        <hr style="margin: 20px 0;">

        <div class="signature-section">
            <div class="col-half">
                <strong>คำอนุมัติ</strong><br>
                อนุมัติให้เบิกได้<br><br>
                (ลงชื่อ) ...........................................................<br>
                ตำแหน่ง ...........................................................<br>
                วันที่ ......../......../........
            </div>
            <div class="col-half" style="border-left: 1px solid #ccc; padding-left: 10px;">
                <strong>ใบรับเงิน</strong><br>
                ได้รับเงินจำนวน ......................................... บาท<br>
                ไว้ถูกต้องแล้ว<br><br>
                (ลงชื่อ) ........................................................... ผู้รับเงิน<br>
                ( <span class="dotted-line"><?php echo $data['mem_name']; ?></span> )<br>
                วันที่ ......../......../........
            </div>
        </div>

    </div>

</body>
</html>
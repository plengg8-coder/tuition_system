<?php
require_once 'db.php';

// 1. ตรวจสอบ ID
if (!isset($_GET['id'])) { die("Error: ไม่พบรหัสคำขอ"); }
$req_id = $_GET['id'];

// 2. ดึงข้อมูล
try {
    $sql = "SELECT r.*, f.fam_name, f.fam_birthdate, m.mem_name, m.mem_position, m.mem_department 
            FROM ath_tuition_request r 
            JOIN ath_member_family f ON r.fam_id = f.fam_id 
            JOIN ath_member m ON r.mem_id = m.mem_id
            WHERE r.req_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$req_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) { die("Error: ไม่พบข้อมูลใบเบิกนี้"); }

} catch (PDOException $e) { die("Error: " . $e->getMessage()); }

// --- Helper Functions ---
function thai_date($date) {
    if(!$date) return "";
    $months = [
        1=>"มกราคม", 2=>"กุมภาพันธ์", 3=>"มีนาคม", 4=>"เมษายน", 
        5=>"พฤษภาคม", 6=>"มิถุนายน", 7=>"กรกฎาคม", 8=>"สิงหาคม", 
        9=>"กันยายน", 10=>"ตุลาคม", 11=>"พฤศจิกายน", 12=>"ธันวาคม"
    ];
    $d = date("j", strtotime($date));
    $m = date("n", strtotime($date));
    $y = date("Y", strtotime($date)) + 543;
    return "$d $months[$m] $y";
}

function baht_text($number) {
    $number = preg_replace("/[^0-9.]/", "", $number);
    if ($number == "") return "";
    $txt_num = ['ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ'];
    $txt_unit = ['','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน'];
    $number_str = number_format($number, 2, '.', '');
    $parts = explode('.', $number_str);
    $int_part = $parts[0];
    $dec_part = $parts[1];
    if ($int_part == 0 && $dec_part == 0) return "ศูนย์บาทถ้วน";
    $baht_text = "";
    $len = strlen($int_part);
    for ($i = 0; $i < $len; $i++) {
        $n = substr($int_part, $i, 1);
        $digit = $len - $i - 1;
        if ($n != 0) {
            if ($digit == 1 && $n == 1) $baht_text .= "";
            elseif ($digit == 1 && $n == 2) $baht_text .= "ยี่";
            elseif ($digit == 0 && $n == 1 && $len > 1) $baht_text .= "เอ็ด";
            else $baht_text .= $txt_num[$n];
            $baht_text .= $txt_unit[$digit];
        }
    }
    $baht_text .= "บาท";
    if ($dec_part > 0) {
        $len = strlen($dec_part);
        for ($i = 0; $i < $len; $i++) {
            $n = substr($dec_part, $i, 1);
            $digit = $len - $i - 1;
            if ($n != 0) {
                if ($digit == 1 && $n == 1) $baht_text .= "";
                elseif ($digit == 1 && $n == 2) $baht_text .= "ยี่";
                elseif ($digit == 0 && $n == 1 && $len > 1) $baht_text .= "เอ็ด";
                else $baht_text .= $txt_num[$n];
                $baht_text .= $txt_unit[$digit];
            }
        }
        $baht_text .= "สตางค์";
    } else {
        $baht_text .= "ถ้วน";
    }
    return $baht_text;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบเบิกเงินสวัสดิการเกี่ยวกับการศึกษาบุตร (แบบ 7223)</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        @page { size: A4; margin: 15mm 15mm 10mm 15mm; }
        body { font-family: 'Sarabun', sans-serif; font-size: 14pt; line-height: 1.4; color: #000; background-color: #f0f0f0; }
        .page-a4 {
            width: 210mm; min-height: 297mm; padding: 20mm; margin: 10px auto;
            background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); box-sizing: border-box; position: relative;
        }
        @media print {
            body { background: none; margin: 0; }
            .page-a4 { box-shadow: none; margin: 0; width: 100%; padding: 0; }
            .no-print { display: none !important; }
        }
        .header-top { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .form-code { font-weight: bold; font-size: 14pt; }
        .form-title { text-align: center; font-weight: bold; font-size: 16pt; margin-bottom: 5px; }
        
        .dotted { border-bottom: 1px dotted #000; display: inline-block; text-align: center; color: #0000AA; padding: 0 5px; white-space: nowrap; height: 1.2em;}
        @media print { .dotted { color: #000; border-bottom: none; text-decoration: underline dotted; } }
        
        .chk { display: inline-block; width: 18px; height: 18px; border: 1px solid #000; margin: 0 5px; vertical-align: text-bottom; text-align: center; line-height: 14px; font-size: 14px; }
        
        .indent-1 { padding-left: 20px; }
        .indent-2 { padding-left: 40px; }
        .indent-3 { padding-left: 60px; }
        
        .row-flex { display: flex; justify-content: space-between; align-items: baseline; }
        
        .sign-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .sign-table td { vertical-align: top; padding: 5px; }
        
        .btn-action { padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; display: inline-block; margin: 0 5px; cursor: pointer; border: none; }
        .btn-print { background-color: #0d6efd; color: white; }
        .btn-back { background-color: #6c757d; color: white; }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; margin-bottom: 20px; padding-top: 20px;">
        <button onclick="window.print()" class="btn-action btn-print">🖨️ พิมพ์ใบเบิก</button>
        <a href="index.php" class="btn-action btn-back">กลับหน้าหลัก</a>
    </div>

    <div class="page-a4">
        <div class="header-top">
            <div></div>
            <div class="form-code">แบบ 7223</div>
        </div>
        <div class="form-title">ใบเบิกเงินสวัสดิการเกี่ยวกับการศึกษาบุตร</div>
        <div style="text-align: center; margin-bottom: 15px;">
            โปรดทำเครื่องหมาย <span class="chk">/</span> ลงในช่อง <span class="chk"></span> พร้อมทั้งกรอกข้อความที่จำเป็น
        </div>

        <div>
            ข้าพเจ้า <span class="dotted" style="width: 200px;"><?php echo $data['mem_name']; ?></span>
            ตำแหน่ง <span class="dotted" style="width: 150px;"><?php echo $data['mem_position']; ?></span>
            สังกัด <span class="dotted" style="width: 150px;"><?php echo $data['mem_department'] ?: 'โรงพยาบาลอ่างทอง'; ?></span>
        </div>

        <div>
            คู่สมรสของข้าพเจ้าชื่อ <span class="dotted" style="width: 250px;">...........................................................................</span>
        </div>
        <div class="indent-1">
            <span class="chk"></span> ไม่เป็นข้าราชการหรือลูกจ้างประจำ
        </div>
        <div class="indent-1">
            <span class="chk"></span> เป็นข้าราชการ <span class="chk"></span> ลูกจ้างประจำ &nbsp;
            ตำแหน่ง <span class="dotted" style="width: 120px;">..........................</span> สังกัด <span class="dotted" style="width: 120px;">..........................</span>
        </div>
        <div class="indent-1">
            <span class="chk"></span> เป็นพนักงานหรือลูกจ้างใน รัฐวิสาหกิจ/หน่วยงานของทางราชการ/ราชการส่วนท้องถิ่น
        </div>
        <div class="indent-1">
            <span class="chk"></span> กรุงเทพมหานคร องค์กรอิสระ องค์กรมหาชน หรือหน่วยงานอื่นใด
        </div>
        <div class="indent-2">
             ตำแหน่ง <span class="dotted" style="width: 150px;">..........................................</span> สังกัด <span class="dotted" style="width: 150px;">..........................................</span>
        </div>

        <div style="margin-top: 5px;">
            ข้าพเจ้าเป็นผู้มีสิทธิและขอใช้สิทธิเนื่องจาก
        </div>
        <div class="indent-2">
            <span class="chk"></span> เป็นบิดาชอบด้วยกฎหมาย &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="chk"></span> เป็นมารดา
        </div>

        <div style="margin-top: 5px;">
            ข้าพเจ้าได้จ่ายเงินสำหรับการศึกษาของบุตร ดังนี้
        </div>
        <div class="indent-2">
            <span class="chk"></span> (1) เงินบำรุงการศึกษา &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="chk">/</span> (2) เงินค่าเล่าเรียน
        </div>

        <div style="margin-top: 5px;">
            บุตรชื่อ <span class="dotted" style="width: 180px;"><?php echo $data['fam_name']; ?></span>
            เกิดเมื่อ <span class="dotted" style="width: 100px;"><?php echo thai_date($data['fam_birthdate']); ?></span>
        </div>
        <div>
            เป็นบุตรลำดับที่ (ของบิดา) <span class="dotted" style="width: 50px;">.....</span>
            เป็นบุตรลำดับที่ (ของมารดา) <span class="dotted" style="width: 50px;">.....</span>
        </div>
        <div>
            (กรณีเป็นบุตรแทนที่บุตรซึ่งถึงแก่กรรมแล้ว) แทนที่บุตรลำดับที่ <span class="dotted" style="width: 50px;">.....</span>
        </div>
        <div>
            ชื่อ <span class="dotted" style="width: 150px;">...................................</span>
            เกิดเมื่อ <span class="dotted" style="width: 100px;">.....................</span>
            ถึงแก่กรรมเมื่อ <span class="dotted" style="width: 100px;">.....................</span>
        </div>
        <div>
            สถานศึกษา <span class="dotted" style="width: 200px;"><?php echo $data['req_school_name']; ?></span>
            อำเภอ <span class="dotted" style="width: 80px;">เมือง</span>
            จังหวัด <span class="dotted" style="width: 80px;">อ่างทอง</span>
        </div>
        <div>
            ชั้นที่ศึกษา <span class="dotted" style="width: 120px;"><?php echo $data['req_school_level']; ?></span>
            <span class="chk"></span> (1) &nbsp; <span class="chk">/</span> (2) &nbsp;
            จำนวน <span class="dotted" style="width: 100px; text-align: right;"><?php echo number_format($data['req_tuition_amount'], 2); ?></span> บาท
        </div>

        <div style="margin-top: 10px;">
            ข้าพเจ้าขอรับเงินสวัสดิการเกี่ยวกับการศึกษาของบุตร
        </div>
        <div class="indent-2">
            <span class="chk">/</span> ตามสิทธิ &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="chk"></span> เฉพาะส่วนที่ยังขาดจากสิทธิ
        </div>
        <div style="display: flex; align-items: baseline;">
            เป็นเงิน <span class="dotted" style="flex-grow: 1; text-align: center; font-weight: bold;"><?php echo number_format($data['req_tuition_amount'], 2); ?></span> บาท
        </div>
        <div style="display: flex; align-items: baseline;">
            (<span class="dotted" style="flex-grow: 1; text-align: center;"><?php echo baht_text($data['req_tuition_amount']); ?></span>)
        </div>

        <div style="margin-top: 15px;">
            6. เสนอ <span class="dotted" style="width: 250px;">.......................................................................</span>
        </div>
        <div class="indent-2">
            <span class="chk">/</span> ข้าพเจ้ามีสิทธิได้รับเงินช่วยเหลือตามพระราชกฤษฎีกาเงินสวัสดิการเกี่ยวกับการศึกษาของบุตรและข้อความที่ระบุข้างต้นเป็นความจริง
        </div>
        <div class="indent-2">
            <span class="chk">/</span> บุตรของข้าพเจ้าอยู่ในข่ายได้รับการช่วยเหลือตามพระราชกฤษฎีกาเงินสวัสดิการเกี่ยวกับการศึกษาของบุตร
        </div>
        <div class="indent-2">
            <span class="chk"></span> เป็นผู้ใช้สิทธิเบิกเงินช่วยเหลือตามพระราชกฤษฎีกาเงินสวัสดิการเกี่ยวกับการศึกษาของบุตร แต่เพียงฝ่ายเดียว
        </div>
        <div class="indent-2">
            <span class="chk"></span> คู่สมรสของข้าพเจ้าได้รับการช่วยเหลือจากรัฐวิสาหกิจ หน่วยงานของทางราชการ ราชการท้องถิ่น กรุงเทพมหานคร องค์กรอิสระ องค์การมหาชน หรือหน่วยงานอื่นใด ต่ำกว่าจำนวนที่ได้รับจากทางราชการ
        </div>
        <div class="indent-3">
            จำนวน <span class="dotted" style="width: 150px;">..........................................</span> บาท
        </div>
        
        <div class="indent-1" style="margin-top: 5px;">
            ข้าพเจ้าขอรับรองว่ามีสิทธิเบิกได้ตามกฎหมาย ตามจำนวนที่ขอเบิก
        </div>

        <div style="text-align: center; margin-left: 200px; margin-top: 10px;">
            (ลงชื่อ) <span class="dotted" style="width: 200px;">&nbsp;</span> ผู้ขอรับสวัสดิการ<br>
            (<span class="dotted" style="width: 200px;"><?php echo $data['mem_name']; ?></span>)<br>
            วันที่ <span class="dotted" style="width: 50px;">&nbsp;</span> เดือน <span class="dotted" style="width: 100px;">&nbsp;</span> พ.ศ. <span class="dotted" style="width: 60px;">&nbsp;</span>
        </div>

        <table class="sign-table">
            <tr>
                <td style="width: 50%;">
                    <strong>คำอนุมัติ</strong><br>
                    อนุมัติให้เบิกได้<br><br>
                    (ลงชื่อ) <span class="dotted" style="width: 200px;">&nbsp;</span><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<span class="dotted" style="width: 180px;">&nbsp;</span>)<br>
                    ตำแหน่ง <span class="dotted" style="width: 200px;">&nbsp;</span>
                </td>
                <td style="width: 50%; border-left: 1px solid #000; padding-left: 20px;">
                    <strong>ใบรับเงิน</strong><br>
                    ได้รับเงินสวัสดิการเกี่ยวกับการศึกษาของบุตร<br>
                    จำนวน <span class="dotted" style="width: 100px;"><?php echo number_format($data['req_tuition_amount'], 2); ?></span> บาท<br>
                    (<span class="dotted" style="width: 180px; font-size: 10pt;"><?php echo baht_text($data['req_tuition_amount']); ?></span>) ไว้ถูกต้องแล้ว<br><br>
                    (ลงชื่อ) <span class="dotted" style="width: 150px;">&nbsp;</span> ผู้รับเงิน<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<span class="dotted" style="width: 150px;"><?php echo $data['mem_name']; ?></span>)<br>
                    (ลงชื่อ) <span class="dotted" style="width: 150px;">&nbsp;</span> ผู้จ่ายเงิน<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<span class="dotted" style="width: 150px;">&nbsp;</span>)<br>
                    วันที่ <span class="dotted" style="width: 40px;">&nbsp;</span> เดือน <span class="dotted" style="width: 80px;">&nbsp;</span> พ.ศ. <span class="dotted" style="width: 50px;">&nbsp;</span>
                </td>
            </tr>
        </table>
        
        <div style="font-size: 10pt; margin-top: 10px;">
            <strong>คำชี้แจง</strong> ให้ระบุการมีสิทธิเพียงใด เมื่อเทียบกับสิทธิที่ได้รับตามพระราชกฤษฎีกาเงินสวัสดิการเกี่ยวกับการศึกษาของบุตร ให้เสนอต่อผู้มีอำนาจอนุมัติ
        </div>

    </div>
</body>
</html>
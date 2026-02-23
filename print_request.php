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
    if(!$date || $date == '0000-00-00') return "";
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
        @page { size: A4; margin: 0; }
        body { 
            font-family: 'Sarabun', sans-serif; 
            font-size: 14pt; 
            line-height: 1.4; 
            color: #000; 
            background-color: #525659; 
            margin: 0; padding: 20px;
        }
        .page-a4 {
            width: 210mm; min-height: 297mm; padding: 15mm 20mm; margin: 0 auto 20px auto;
            background: white; box-shadow: 0 0 10px rgba(0,0,0,0.5); box-sizing: border-box; position: relative;
        }
        @media print {
            body { background: none; padding: 0; }
            .page-a4 { box-shadow: none; margin: 0; width: 100%; page-break-after: always; padding: 10mm 15mm; }
            .page-a4:last-child { page-break-after: auto; }
            .no-print { display: none !important; }
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        
        .form-box { border: 1.5px solid #000; padding: 15px; margin-top: 10px; }
        .form-section { border-bottom: 1.5px solid #000; padding: 10px 15px; }
        .form-section:last-child { border-bottom: none; }
        .form-box-p2 { border: 1.5px solid #000; padding: 0; margin-top: 10px; }
        
        .dotted { border-bottom: 1px dotted #000; display: inline-block; text-align: center; color: #0000AA; padding: 0 5px; min-height: 1em; }
        @media print { .dotted { color: #000; border-bottom: none; text-decoration: underline dotted; } }
        
        /* สไตล์กล่อง Checkbox ให้เหมือนในรูปเป๊ะๆ */
        .chk { display: inline-block; width: 12px; height: 12px; border: 1.5px solid #000; margin: 0 5px; vertical-align: middle; text-align: center; line-height: 12px; font-size: 14px; font-weight: bold;}
        .chk-letter { display: inline-block; border: 1.5px solid #000; padding: 0 8px; margin: 0 5px; text-align: center; font-size: 12pt;}
        
        .indent-1 { padding-left: 30px; }
        .indent-2 { padding-left: 60px; }
        
        .flex-row { display: flex; align-items: baseline; }
        .flex-grow { flex-grow: 1; }

        .btn-action { padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; display: inline-block; margin: 0 5px; cursor: pointer; border: none; }
        .btn-print { background-color: #0d6efd; color: white; }
        .btn-back { background-color: #6c757d; color: white; }
    </style>
</head>
<body>

    <div class="no-print text-center" style="margin-bottom: 20px;">
        <button onclick="window.print()" class="btn-action btn-print">🖨️ พิมพ์ใบเบิก (A4)</button>
        <a href="index.php" class="btn-action btn-back">กลับหน้าหลัก</a>
    </div>

    <div class="page-a4">
        <div class="text-right bold" style="font-size: 12pt;">แบบ 7223</div>
        <div class="text-center bold" style="font-size: 16pt; margin-top: 10px;">ใบเบิกเงินสวัสดิการเกี่ยวกับการศึกษาบุตร</div>
        <div class="text-center bold" style="margin-top: 5px;">
            โปรดทำเครื่องหมาย <span style="font-size: 16pt;">✔</span> ลงในช่อง <span class="chk"></span> พร้อมทั้งกรอกข้อความที่จำเป็น
        </div>

        <div class="form-box">
            
            <div class="flex-row" style="margin-bottom: 5px;">
                <div style="width: 25px;">1.</div>
                <div>ข้าพเจ้า</div>
                <div class="dotted flex-grow" style="margin: 0 10px;"><?php echo $data['mem_name']; ?></div>
                <div>ตำแหน่ง</div>
                <div class="dotted" style="width: 250px; margin-left: 10px;"><?php echo $data['mem_position']; ?></div>
            </div>
            <div class="flex-row" style="margin-bottom: 15px;">
                <div style="width: 25px;"></div>
                <div>สังกัด</div>
                <div class="dotted flex-grow" style="margin-left: 10px;"><?php echo $data['mem_department'] ?: 'โรงพยาบาลอ่างทอง'; ?></div>
            </div>

            <div class="flex-row" style="margin-bottom: 5px;">
                <div style="width: 25px;">2.</div>
                <div>คู่สมรสของข้าพเจ้าชื่อ</div>
                <div class="dotted flex-grow" style="margin-left: 10px;"></div>
            </div>
            <div class="indent-1">
                <span class="chk"></span> ไม่เป็นข้าราชการประจำหรือลูกจ้างประจำ
            </div>
            <div class="indent-1">
                <span class="chk"></span> เป็นข้าราชการ <span class="chk" style="margin-left: 15px;"></span> ลูกจ้างประจำ ตำแหน่ง<span class="dotted" style="width:150px;"></span>สังกัด<span class="dotted" style="width:150px;"></span>
            </div>
            <div class="indent-1">
                <span class="chk"></span> เป็นพนักงานหรือลูกจ้างใน รัฐวิสาหกิจ/หน่วยงานของทางราชการ ราชการส่วนท้องถิ่น
            </div>
            <div class="indent-1">
                <span class="chk"></span> กรุงเทพมหานคร องค์กรอิสระ องค์กรมหาชน หรือหน่วยงานอื่นใด
            </div>
            <div class="indent-2" style="margin-bottom: 15px;">
                ตำแหน่ง<span class="dotted" style="width: 200px;"></span>สังกัด<span class="dotted flex-grow" style="width: 200px;"></span>
            </div>

            <div class="flex-row">
                <div style="width: 25px;">3.</div>
                <div>ข้าพเจ้าเป็นผู้มีสิทธิและขอใช้สิทธิเนื่องจาก</div>
            </div>
            <div class="indent-1">
                <span class="chk"></span> เป็นบิดาชอบด้วยกฎหมาย
            </div>
            <div class="indent-1" style="margin-bottom: 15px;">
                <span class="chk"></span> เป็นมารดา
            </div>

            <div class="flex-row">
                <div style="width: 25px;">4.</div>
                <div>ข้าพเจ้าได้จ่ายเงินสำหรับการศึกษาของบุตร ดังนี้</div>
            </div>
            <div class="indent-2 text-center" style="margin-bottom: 10px;">
                (1) เงินบำรุงการศึกษา <span style="display:inline-block; width: 40px;"></span> (2) เงินค่าเล่าเรียน
            </div>

            <div class="flex-row" style="margin-bottom: 2px;">
                <div style="width: 30px;"></div>
                <div style="width: 25px;">1)</div>
                <div>บุตรชื่อ</div><div class="dotted flex-grow" style="margin: 0 5px;"><?php echo $data['fam_name']; ?></div>
                <div>เกิดเมื่อ</div><div class="dotted" style="width: 150px; margin-left: 5px;"><?php echo thai_date($data['fam_birthdate']); ?></div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 2px;">
                <div>เป็นบุตรลำดับที่ (ของบิดา)</div><div class="dotted" style="width: 60px;"></div>
                <div style="margin-left: 10px;">เป็นบุตรลำดับที่ (ของมารดา)</div><div class="dotted flex-grow"></div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 2px;">
                <div>(กรณีเป็นบุตรแทนที่บุตรซึ่งถึงแก่กรรมแล้ว) แทนที่บุตรลำดับที่</div><div class="dotted flex-grow"></div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 2px;">
                <div>ชื่อ</div><div class="dotted flex-grow" style="margin: 0 5px;"></div>
                <div>เกิดเมื่อ</div><div class="dotted" style="width: 100px; margin: 0 5px;"></div>
                <div>ถึงแก่กรรมเมื่อ</div><div class="dotted" style="width: 100px;"></div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 2px;">
                <div>สถานศึกษา</div><div class="dotted flex-grow" style="margin: 0 5px;"><?php echo $data['req_school_name']; ?></div>
                <div>อำเภอ</div><div class="dotted" style="width: 80px; margin: 0 5px;">เมือง</div>
                <div>จังหวัด</div><div class="dotted" style="width: 80px;">อ่างทอง</div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 15px;">
                <div>ชั้นที่ศึกษา</div><div class="dotted" style="width: 150px; margin: 0 5px;"><?php echo $data['req_school_level']; ?></div>
                <div>(1) <span class="chk"></span></div>
                <div style="margin-left: 10px;">(2) <span class="chk">✔</span></div>
                <div style="margin-left: 15px;">จำนวน</div><div class="dotted flex-grow" style="margin: 0 5px; text-align: right; font-weight: bold;"><?php echo number_format($data['req_tuition_amount'], 2); ?></div><div>บาท</div>
            </div>

            <div class="flex-row" style="margin-bottom: 2px;">
                <div style="width: 30px;"></div>
                <div style="width: 25px;">2)</div>
                <div>บุตรชื่อ</div><div class="dotted flex-grow" style="margin: 0 5px;"></div>
                <div>เกิดเมื่อ</div><div class="dotted" style="width: 150px; margin-left: 5px;"></div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 2px;">
                <div>เป็นบุตรลำดับที่ (ของบิดา)</div><div class="dotted" style="width: 60px;"></div>
                <div style="margin-left: 10px;">เป็นบุตรลำดับที่ (ของมารดา)</div><div class="dotted flex-grow"></div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 2px;">
                <div>(กรณีเป็นบุตรแทนที่บุตรซึ่งถึงแก่กรรมแล้ว) แทนที่บุตรลำดับที่</div><div class="dotted flex-grow"></div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 2px;">
                <div>ชื่อ</div><div class="dotted flex-grow" style="margin: 0 5px;"></div>
                <div>เกิดเมื่อ</div><div class="dotted" style="width: 100px; margin: 0 5px;"></div>
                <div>ถึงแก่กรรมเมื่อ</div><div class="dotted" style="width: 100px;"></div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 2px;">
                <div>สถานศึกษา</div><div class="dotted flex-grow" style="margin: 0 5px;"></div>
                <div>อำเภอ</div><div class="dotted" style="width: 80px; margin: 0 5px;"></div>
                <div>จังหวัด</div><div class="dotted" style="width: 80px;"></div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 15px;">
                <div>ชั้นที่ศึกษา</div><div class="dotted" style="width: 150px; margin: 0 5px;"></div>
                <div>(1) <span class="chk"></span></div>
                <div style="margin-left: 10px;">(2) <span class="chk"></span></div>
                <div style="margin-left: 15px;">จำนวน</div><div class="dotted flex-grow" style="margin: 0 5px;"></div><div>บาท</div>
            </div>

            <div class="flex-row" style="margin-bottom: 2px;">
                <div style="width: 30px;"></div>
                <div style="width: 25px;">3)</div>
                <div>บุตรชื่อ</div><div class="dotted flex-grow" style="margin: 0 5px;"></div>
                <div>เกิดเมื่อ</div><div class="dotted" style="width: 150px; margin-left: 5px;"></div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 2px;">
                <div>เป็นบุตรลำดับที่ (ของบิดา)</div><div class="dotted" style="width: 60px;"></div>
                <div style="margin-left: 10px;">เป็นบุตรลำดับที่ (ของมารดา)</div><div class="dotted flex-grow"></div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 2px;">
                <div>(กรณีเป็นบุตรแทนที่บุตรซึ่งถึงแก่กรรมแล้ว) แทนที่บุตรลำดับที่</div><div class="dotted flex-grow"></div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 2px;">
                <div>ชื่อ</div><div class="dotted flex-grow" style="margin: 0 5px;"></div>
                <div>เกิดเมื่อ</div><div class="dotted" style="width: 100px; margin: 0 5px;"></div>
                <div>ถึงแก่กรรมเมื่อ</div><div class="dotted" style="width: 100px;"></div>
            </div>
            <div class="indent-2 flex-row" style="margin-bottom: 2px;">
                <div>สถานศึกษา</div><div class="dotted flex-grow" style="margin: 0 5px;"></div>
                <div>อำเภอ</div><div class="dotted" style="width: 80px; margin: 0 5px;"></div>
                <div>จังหวัด</div><div class="dotted" style="width: 80px;"></div>
            </div>
            <div class="indent-2 flex-row">
                <div>ชั้นที่ศึกษา</div><div class="dotted" style="width: 150px; margin: 0 5px;"></div>
                <div>(1) <span class="chk"></span></div>
                <div style="margin-left: 10px;">(2) <span class="chk"></span></div>
                <div style="margin-left: 15px;">จำนวน</div><div class="dotted flex-grow" style="margin: 0 5px;"></div><div>บาท</div>
            </div>

        </div> </div>


    <div class="page-a4">
        
        <div class="form-box-p2">
            
            <div class="form-section">
                <div class="flex-row">
                    <div style="width: 25px;">5.</div>
                    <div>ข้าพเจ้าขอรับเงินสวัสดิการเกี่ยวกับการศึกษาของบุตร</div>
                </div>
                <div class="indent-1 flex-row" style="margin-top: 5px;">
                    <span class="chk">✔</span> ตามสิทธิ <span style="display:inline-block; width: 40px;"></span> 
                    <span class="chk"></span> เฉพาะส่วนที่ยังขาดจากสิทธิ &nbsp; เป็นเงิน <div class="dotted flex-grow text-center bold"><?php echo number_format($data['req_tuition_amount'], 2); ?></div> บาท
                </div>
                <div class="indent-1 flex-row" style="margin-top: 5px;">
                    <div style="width: 100px;"></div>
                    <div>(</div><div class="dotted flex-grow text-center"><?php echo baht_text($data['req_tuition_amount']); ?></div><div>)</div>
                    <div class="chk-letter" style="margin-left: 15px;">ก</div>
                </div>
            </div>

            <div class="form-section">
                <div class="flex-row">
                    <div style="width: 25px;">6.</div>
                    <div>เสนอ</div><div class="dotted flex-grow"></div>
                    <div class="chk-letter" style="margin-left: 15px;">ข</div>
                </div>
                <div class="indent-1 flex-row" style="margin-top: 5px;">
                    <span class="chk" style="margin-top: 5px;">✔</span>
                    <div style="margin-left: 10px;">ข้าพเจ้ามีสิทธิได้รับเงินช่วยเหลือตามพระราชกฤษฎีกาเงินสวัสดิการเกี่ยวกับการศึกษาของบุตรและข้อความที่ระบุข้างต้นเป็นความจริง</div>
                </div>
                <div class="indent-1 flex-row" style="margin-top: 5px;">
                    <span class="chk" style="margin-top: 5px;">✔</span>
                    <div style="margin-left: 10px;">บุตรของข้าพเจ้าอยู่ในข่ายได้รับการช่วยเหลือตามพระราชกฤษฎีกาเงินสวัสดิการเกี่ยวกับการศึกษาของบุตร</div>
                </div>
                <div class="indent-1 flex-row" style="margin-top: 5px;">
                    <span class="chk" style="margin-top: 5px;"></span>
                    <div style="margin-left: 10px;">เป็นผู้ใช้สิทธิเบิกเงินช่วยเหลือตามพระราชกฤษฎีกาเงินสวัสดิการเกี่ยวกับการศึกษาของบุตร แต่เพียงฝ่ายเดียว</div>
                </div>
                <div class="indent-1 flex-row" style="margin-top: 5px;">
                    <span class="chk" style="margin-top: 5px;"></span>
                    <div style="margin-left: 10px;">คู่สมรสของข้าพเจ้าได้รับการช่วยเหลือจากรัฐวิสาหกิจ หน่วยงานของทางราชการ ราชการท้องถิ่น กรุงเทพมหานคร องค์กรอิสระ องค์การมหาชน หรือหน่วยงานอื่นใด ต่ำกว่าจำนวนที่ได้รับจากทางราชการ</div>
                </div>
                <div class="indent-2 flex-row" style="margin-top: 5px;">
                    จำนวน<div class="dotted" style="width: 200px;"></div>บาท
                </div>
                <div class="indent-2 flex-row" style="margin-top: 10px;">
                    <div style="margin-left: 30px;">ข้าพเจ้าขอรับรองว่ามีสิทธิเบิกได้ตามกฎหมาย ตามจำนวนที่ขอเบิก</div>
                </div>
                
                <div style="margin-top: 20px; padding-left: 50%;">
                    <div class="flex-row" style="margin-bottom: 5px;">
                        (ลงชื่อ)<div class="dotted flex-grow"></div>ผู้ขอรับสวัสดิการ
                    </div>
                    <div class="flex-row text-center" style="margin-bottom: 5px;">
                        (<div class="dotted flex-grow"><?php echo $data['mem_name']; ?></div>)
                    </div>
                    <div class="flex-row">
                        วันที่<div class="dotted" style="width: 50px;"></div>
                        เดือน<div class="dotted flex-grow"></div>
                        พ.ศ.<div class="dotted" style="width: 60px;"></div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="flex-row">
                    <div style="width: 25px;">7.</div>
                    <div class="bold">คำอนุมัติ</div>
                </div>
                <div class="indent-2" style="margin-top: 5px;">อนุมัติให้เบิกได้</div>
                
                <div style="margin-top: 15px; padding-left: 40%;">
                    <div class="flex-row" style="margin-bottom: 5px;">
                        <div style="width: 80px; text-align: right; padding-right: 5px;">(ลงชื่อ)</div><div class="dotted flex-grow"></div>
                    </div>
                    <div class="flex-row text-center" style="margin-bottom: 5px;">
                        <div style="width: 80px;"></div>(<div class="dotted flex-grow"></div>)
                    </div>
                    <div class="flex-row">
                        <div style="width: 80px; text-align: right; padding-right: 5px;">ตำแหน่ง</div><div class="dotted flex-grow"></div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="flex-row">
                    <div style="width: 25px;">8.</div>
                    <div class="bold">ใบรับเงิน</div>
                </div>
                <div class="indent-2 flex-row" style="margin-top: 5px;">
                    <div>ได้รับเงินสวัสดิการเกี่ยวกับการศึกษาของบุตร จำนวน</div><div class="dotted flex-grow text-center"><?php echo number_format($data['req_tuition_amount'], 2); ?></div><div>บาท</div>
                </div>
                <div class="indent-1 flex-row" style="margin-top: 5px;">
                    <div>(</div><div class="dotted flex-grow text-center"><?php echo baht_text($data['req_tuition_amount']); ?></div><div>) ไว้ถูกต้องแล้ว</div>
                </div>
                
                <div style="margin-top: 20px; padding-left: 40%;">
                    <div class="flex-row" style="margin-bottom: 5px;">
                        <div style="width: 80px; text-align: right; padding-right: 5px;">(ลงชื่อ)</div><div class="dotted flex-grow"></div>ผู้รับเงิน
                    </div>
                    <div class="flex-row text-center" style="margin-bottom: 5px;">
                        <div style="width: 80px;"></div>(<div class="dotted flex-grow"><?php echo $data['mem_name']; ?></div>)
                    </div>
                    <div class="flex-row" style="margin-bottom: 5px;">
                        <div style="width: 80px; text-align: right; padding-right: 5px;">(ลงชื่อ)</div><div class="dotted flex-grow"></div>ผู้จ่ายเงิน
                    </div>
                    <div class="flex-row text-center" style="margin-bottom: 5px;">
                        <div style="width: 80px;"></div>(<div class="dotted flex-grow"></div>)
                    </div>
                    <div class="flex-row">
                        <div style="width: 80px;"></div>
                        วันที่<div class="dotted" style="width: 40px;"></div>
                        เดือน<div class="dotted flex-grow"></div>
                        พ.ศ.<div class="dotted" style="width: 50px;"></div>
                    </div>
                </div>
            </div>

        </div> <div style="margin-top: 20px;">
            <div class="bold">คำชี้แจง</div>
            <div class="flex-row" style="margin-top: 10px;">
                <div class="chk-letter" style="font-size: 10pt; height: 20px; line-height: 20px;">ก</div>
                <div style="margin-left: 10px;">ให้ระบุการมีสิทธิเพียงใด เมื่อเทียบกับสิทธิที่ได้รับตามพระราชกฤษฎีกาเงินสวัสดิการเกี่ยวกับการศึกษาของบุตร</div>
            </div>
            <div class="flex-row" style="margin-top: 10px;">
                <div class="chk-letter" style="font-size: 10pt; height: 20px; line-height: 20px;">ข</div>
                <div style="margin-left: 10px;">ให้เสนอต่อผู้มีอำนาจอนุมัติ</div>
            </div>
        </div>

    </div>

</body>
</html>
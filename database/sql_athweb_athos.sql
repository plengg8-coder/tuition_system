-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2026 at 10:40 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sql_athweb_athos`
--

-- --------------------------------------------------------

--
-- Table structure for table `ath_member`
--

CREATE TABLE `ath_member` (
  `mem_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสสมาชิก',
  `mem_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อ-สกุล',
  `mem_position` varchar(255) DEFAULT 'เจ้าพนักงาน' COMMENT 'ตำแหน่ง',
  `mem_department` varchar(255) DEFAULT 'โรงพยาบาลอ่างทอง' COMMENT 'สังกัด',
  `mem_username` varchar(50) DEFAULT NULL COMMENT 'ชื่อผู้ใช้งาน',
  `mem_password` varchar(255) DEFAULT '1234' COMMENT 'รหัสผ่าน'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='ข้อมูลสมาชิก' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `ath_member`
--

INSERT INTO `ath_member` (`mem_id`, `mem_name`, `mem_position`, `mem_department`, `mem_username`, `mem_password`) VALUES
(1, 'นายทดสอบ ระบบเบิก', 'เจ้าหน้าที่ธุรการ', 'โรงพยาบาลอ่างทอง', 'admin', '1234'),
(2, 'a', 'a', 'โรงพยาบาลอ่างทอง', 'a', 'a'),
(3, 'ทดสอบระบบ', 'นักวิชาการคอมพิวเตอร์', 'ศูนย์คอมพิวเตอร์', 'moremeng', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `ath_member_family`
--

CREATE TABLE `ath_member_family` (
  `fam_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสสมาชิกในครอบครัว',
  `mem_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสสมาชิก',
  `fam_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อสมาชิกในครอบครัว',
  `fam_birthdate` date DEFAULT NULL COMMENT 'วันเกิด',
  `fam_relationship` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'ความสัมพันธ์ 1=บุตร, 2=สามี/ภรรยา, 3=บิดา/มารดา, 4=อื่นๆ',
  `fam_created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `fam_updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตข้อมูล',
  `fam_deleted_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'สถานะการลบ 0=ปกติ, 1=ลบแล้ว'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='ข้อมูลสมาชิกในครอบครัว' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `ath_member_family`
--

INSERT INTO `ath_member_family` (`fam_id`, `mem_id`, `fam_name`, `fam_birthdate`, `fam_relationship`, `fam_created_at`, `fam_updated_at`, `fam_deleted_status`) VALUES
(1, 3, 'ด.ช. เรียนดี มีวินัย ', '2015-05-10', 1, '2026-02-03 14:06:27', '2026-02-09 08:43:38', 0),
(2, 3, 'ด.ญ. ตั้งใจ ศึกษา ', '2018-08-20', 1, '2026-02-03 14:06:27', '2026-02-09 08:43:45', 0),
(5, 3, 'ชยพัทธ์สรณ์ธนกฤต วงศ์วรโชติโภคินสิริกุล', '2026-02-11', 1, '2026-02-24 11:35:33', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ath_tuition_notification`
--

CREATE TABLE `ath_tuition_notification` (
  `notif_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสแจ้งเตือน',
  `req_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสคำขอเบิก',
  `mem_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสสมาชิกที่จะแจ้งเตือน',
  `notif_type` enum('line','app','both') NOT NULL DEFAULT 'both' COMMENT 'ประเภทแจ้งเตือน',
  `notif_message` text NOT NULL COMMENT 'ข้อความแจ้งเตือน',
  `notif_status` enum('pending','sent','failed') NOT NULL DEFAULT 'pending' COMMENT 'สถานะการส่ง',
  `notif_sent_date` datetime DEFAULT NULL COMMENT 'วันที่ส่ง',
  `notif_created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='บันทึกการแจ้งเตือน' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `ath_tuition_print_log`
--

CREATE TABLE `ath_tuition_print_log` (
  `print_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสบันทึกการพิมพ์',
  `req_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสคำขอเบิก',
  `print_by` int(11) UNSIGNED NOT NULL COMMENT 'รหัสผู้พิมพ์',
  `print_date` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่และเวลาที่พิมพ์',
  `print_notes` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `print_created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่บันทึก'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='บันทึกการพิมพ์ใบเบิก' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `ath_tuition_quota`
--

CREATE TABLE `ath_tuition_quota` (
  `quota_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสการตั้งค่าวงเงิน',
  `dept_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสหน่วยงาน (0 = ทั้งหมด/มาตรฐาน)',
  `school_level` enum('ปฐมวัย','ประถมศึกษา','มัธยมศึกษา','อุดมศึกษา') NOT NULL COMMENT 'ระดับการศึกษา',
  `quota_annual_limit` decimal(10,2) NOT NULL COMMENT 'วงเงินต่อปีต่อคน',
  `quota_per_request_limit` decimal(10,2) NOT NULL COMMENT 'วงเงินสูงสุดต่อครั้ง',
  `quota_effective_year` int(4) NOT NULL COMMENT 'ปีที่มีผล (พ.ศ.)',
  `quota_active_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'สถานะการใช้งาน 1=ใช้งาน, 0=ไม่ใช้งาน',
  `quota_created_by` int(11) UNSIGNED NOT NULL COMMENT 'รหัสผู้สร้าง',
  `quota_created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้าง',
  `quota_updated_by` int(11) UNSIGNED DEFAULT NULL COMMENT 'รหัสผู้แก้ไขล่าสุด',
  `quota_updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='การตั้งค่าวงเงินเบิกค่าเล่าเรียน' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `ath_tuition_request`
--

CREATE TABLE `ath_tuition_request` (
  `req_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสคำขอเบิกค่าเล่าเรียน',
  `mem_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสสมาชิก (ผู้ขอเบิก)',
  `fam_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสสมาชิกในครอบครัว (บุตรที่ขออนุมัติ)',
  `req_school_name` varchar(255) NOT NULL COMMENT 'ชื่อสถานศึกษา',
  `req_school_level` enum('ปฐมวัย','ประถมศึกษา','มัธยมศึกษา','อุดมศึกษา') NOT NULL COMMENT 'ระดับการศึกษา',
  `req_grade` varchar(50) DEFAULT NULL COMMENT 'ชั้นเรียน',
  `req_semester` tinyint(1) UNSIGNED NOT NULL COMMENT 'เทอม 1=เทอม1, 2=เทอม2',
  `req_academic_year` int(4) NOT NULL COMMENT 'ปีการศึกษา (พ.ศ.)',
  `req_tuition_amount` decimal(10,2) NOT NULL COMMENT 'จำนวนเงินค่าเล่าเรียน',
  `req_request_date` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่ยื่นคำขอ',
  `req_status` enum('draft','submitted','finance_received','approved','pending_payment','completed','cancelled') NOT NULL DEFAULT 'draft' COMMENT 'สถานะคำขอ',
  `req_status_updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่อัปเดตสถานะ',
  `req_finance_received_date` datetime DEFAULT NULL COMMENT 'วันที่การเงินรับเรื่อง',
  `req_approved_by` int(11) UNSIGNED DEFAULT NULL COMMENT 'รหัสผู้อนุมัติ',
  `req_approved_date` datetime DEFAULT NULL COMMENT 'วันที่อนุมัติ',
  `req_paid_date` datetime DEFAULT NULL COMMENT 'วันที่จ่ายเงิน',
  `req_paid_by` int(11) UNSIGNED DEFAULT NULL COMMENT 'รหัสผู้จ่ายเงิน',
  `req_cancellation_reason` text DEFAULT NULL COMMENT 'เหตุผลในการยกเลิก',
  `req_cancelled_by` int(11) UNSIGNED DEFAULT NULL COMMENT 'รหัสผู้ยกเลิก',
  `req_cancelled_date` datetime DEFAULT NULL COMMENT 'วันที่ยกเลิก',
  `req_notes` text DEFAULT NULL COMMENT 'หมายเหตุเพิ่มเติม',
  `req_created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างข้อมูล',
  `req_updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขข้อมูล',
  `req_deleted_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'สถานะการลบ 0=ปกติ, 1=ลบแล้ว'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='บันทึกคำขอเบิกค่าเล่าเรียนบุตร' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `ath_tuition_request`
--

INSERT INTO `ath_tuition_request` (`req_id`, `mem_id`, `fam_id`, `req_school_name`, `req_school_level`, `req_grade`, `req_semester`, `req_academic_year`, `req_tuition_amount`, `req_request_date`, `req_status`, `req_status_updated_at`, `req_finance_received_date`, `req_approved_by`, `req_approved_date`, `req_paid_date`, `req_paid_by`, `req_cancellation_reason`, `req_cancelled_by`, `req_cancelled_date`, `req_notes`, `req_created_at`, `req_updated_at`, `req_deleted_status`) VALUES
(1, 3, 1, 'มหาลัย', 'ปฐมวัย', '-', 1, 2569, 5000.00, '2026-02-09 00:00:00', 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '5', '2026-02-09 08:52:08', NULL, 0),
(2, 3, 2, 'มหาลัย', 'ประถมศึกษา', '-', 1, 2569, 5000.00, '2026-02-09 00:00:00', 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '2026-02-09 09:01:06', NULL, 0),
(7, 3, 1, 'มหาลัย', 'ปฐมวัย', '-', 1, 2569, 5111.00, '2026-01-02 00:00:00', 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '2026-02-09 10:52:02', NULL, 0),
(8, 3, 2, 's', 'มัธยมศึกษา', '-', 1, 2569, 100.00, '2026-01-02 00:00:00', 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '2026-02-09 15:39:31', NULL, 0),
(9, 3, 1, 's', '', '-', 1, 2569, 1000.00, '2025-12-11 00:00:00', 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '2026-02-10 13:03:03', NULL, 0),
(10, 3, 1, 's', 'มัธยมศึกษา', '-', 1, 2569, 1.00, '2026-02-10 00:00:00', 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '2026-02-10 13:11:32', NULL, 0),
(13, 3, 5, 'มหาลัย', '', '-', 1, 2569, 50500.00, '2026-02-24 00:00:00', 'submitted', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '5', '2026-02-24 11:35:49', NULL, 0),
(14, 3, 1, 'มหาลัย', 'มัธยมศึกษา', '-', 1, 2569, 5000.00, '2026-02-10 00:00:00', 'submitted', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 's', '2026-02-25 11:13:34', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ath_tuition_status_history`
--

CREATE TABLE `ath_tuition_status_history` (
  `hist_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสประวัติ',
  `req_id` int(11) UNSIGNED NOT NULL COMMENT 'รหัสคำขอเบิก',
  `hist_status_old` enum('draft','submitted','finance_received','approved','pending_payment','completed','cancelled') DEFAULT NULL COMMENT 'สถานะเดิม',
  `hist_status_new` enum('draft','submitted','finance_received','approved','pending_payment','completed','cancelled') NOT NULL COMMENT 'สถานะใหม่',
  `hist_changed_by` int(11) UNSIGNED NOT NULL COMMENT 'รหัสผู้เปลี่ยนแปลง',
  `hist_changed_date` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่เปลี่ยนแปลง',
  `hist_remark` text DEFAULT NULL COMMENT 'หมายเหตุการเปลี่ยนแปลง',
  `hist_created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่บันทึก'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='ประวัติการเปลี่ยนแปลงสถานะ' ROW_FORMAT=DYNAMIC;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ath_member`
--
ALTER TABLE `ath_member`
  ADD PRIMARY KEY (`mem_id`) USING BTREE;

--
-- Indexes for table `ath_member_family`
--
ALTER TABLE `ath_member_family`
  ADD PRIMARY KEY (`fam_id`) USING BTREE,
  ADD KEY `idx_mem_id` (`mem_id`) USING BTREE,
  ADD KEY `idx_deleted_status` (`fam_deleted_status`) USING BTREE;

--
-- Indexes for table `ath_tuition_notification`
--
ALTER TABLE `ath_tuition_notification`
  ADD PRIMARY KEY (`notif_id`) USING BTREE,
  ADD KEY `idx_req_id` (`req_id`) USING BTREE,
  ADD KEY `idx_mem_id` (`mem_id`) USING BTREE,
  ADD KEY `idx_status` (`notif_status`) USING BTREE;

--
-- Indexes for table `ath_tuition_print_log`
--
ALTER TABLE `ath_tuition_print_log`
  ADD PRIMARY KEY (`print_id`) USING BTREE,
  ADD KEY `idx_req_id` (`req_id`) USING BTREE;

--
-- Indexes for table `ath_tuition_quota`
--
ALTER TABLE `ath_tuition_quota`
  ADD PRIMARY KEY (`quota_id`) USING BTREE,
  ADD KEY `idx_dept_id` (`dept_id`) USING BTREE,
  ADD KEY `idx_school_level` (`school_level`) USING BTREE,
  ADD KEY `idx_effective_year` (`quota_effective_year`) USING BTREE;

--
-- Indexes for table `ath_tuition_request`
--
ALTER TABLE `ath_tuition_request`
  ADD PRIMARY KEY (`req_id`) USING BTREE,
  ADD KEY `idx_mem_id` (`mem_id`) USING BTREE,
  ADD KEY `idx_fam_id` (`fam_id`) USING BTREE,
  ADD KEY `idx_status` (`req_status`) USING BTREE,
  ADD KEY `idx_request_date` (`req_request_date`) USING BTREE,
  ADD KEY `idx_academic_year` (`req_academic_year`) USING BTREE;

--
-- Indexes for table `ath_tuition_status_history`
--
ALTER TABLE `ath_tuition_status_history`
  ADD PRIMARY KEY (`hist_id`) USING BTREE,
  ADD KEY `idx_req_id` (`req_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ath_member`
--
ALTER TABLE `ath_member`
  MODIFY `mem_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'รหัสสมาชิก', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ath_member_family`
--
ALTER TABLE `ath_member_family`
  MODIFY `fam_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'รหัสสมาชิกในครอบครัว', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ath_tuition_notification`
--
ALTER TABLE `ath_tuition_notification`
  MODIFY `notif_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'รหัสแจ้งเตือน';

--
-- AUTO_INCREMENT for table `ath_tuition_print_log`
--
ALTER TABLE `ath_tuition_print_log`
  MODIFY `print_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'รหัสบันทึกการพิมพ์';

--
-- AUTO_INCREMENT for table `ath_tuition_quota`
--
ALTER TABLE `ath_tuition_quota`
  MODIFY `quota_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'รหัสการตั้งค่าวงเงิน';

--
-- AUTO_INCREMENT for table `ath_tuition_request`
--
ALTER TABLE `ath_tuition_request`
  MODIFY `req_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'รหัสคำขอเบิกค่าเล่าเรียน', AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ath_tuition_status_history`
--
ALTER TABLE `ath_tuition_status_history`
  MODIFY `hist_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'รหัสประวัติ';

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ath_member_family`
--
ALTER TABLE `ath_member_family`
  ADD CONSTRAINT `fk_family_member` FOREIGN KEY (`mem_id`) REFERENCES `ath_member` (`mem_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ath_tuition_notification`
--
ALTER TABLE `ath_tuition_notification`
  ADD CONSTRAINT `fk_notif_member` FOREIGN KEY (`mem_id`) REFERENCES `ath_member` (`mem_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notif_request` FOREIGN KEY (`req_id`) REFERENCES `ath_tuition_request` (`req_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ath_tuition_print_log`
--
ALTER TABLE `ath_tuition_print_log`
  ADD CONSTRAINT `fk_print_request` FOREIGN KEY (`req_id`) REFERENCES `ath_tuition_request` (`req_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ath_tuition_request`
--
ALTER TABLE `ath_tuition_request`
  ADD CONSTRAINT `fk_tuition_family` FOREIGN KEY (`fam_id`) REFERENCES `ath_member_family` (`fam_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tuition_member` FOREIGN KEY (`mem_id`) REFERENCES `ath_member` (`mem_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ath_tuition_status_history`
--
ALTER TABLE `ath_tuition_status_history`
  ADD CONSTRAINT `fk_history_request` FOREIGN KEY (`req_id`) REFERENCES `ath_tuition_request` (`req_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

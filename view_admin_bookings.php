<?php
session_start();
include('db_connect.php');  // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการล็อกอิน (แอดมินเท่านั้น)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "คุณไม่สามารถเข้าถึงหน้านี้ได้";
    exit();
}

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'ThaiMassageDB');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการอนุมัติ/ยกเลิกการจอง
if (isset($_POST['approve_booking_id'])) {
    $booking_id = $_POST['approve_booking_id'];
    $sql = "UPDATE bookings SET status = 'Approved' WHERE booking_id = '$booking_id'";
    $conn->query($sql);
}

if (isset($_POST['cancel_booking_id'])) {
    $booking_id = $_POST['cancel_booking_id'];
    $sql = "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = '$booking_id'";
    $conn->query($sql);
}

// ดึงข้อมูลการจองทั้งหมดจากตาราง bookings
$sql = "SELECT * FROM bookings";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองคิวทั้งหมด</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="view-bookings">
        <h2>การจองคิวทั้งหมด</h2>

        <?php
        if ($result->num_rows > 0) {
            // แสดงตารางการจองทั้งหมด
            echo '<table>';
            echo '<tr><th>ชื่อบริการ</th><th>วันที่</th><th>เวลา</th><th>สถานะ</th><th>ชื่อผู้จอง</th><th>เบอร์โทรศัพท์</th><th>อีเมล</th><th>หลักฐานการโอน</th><th>การกระทำ</th></tr>';

            // แสดงข้อมูลการจองแต่ละแถว
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['service_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['booking_date']) . '</td>';
                echo '<td>' . htmlspecialchars($row['booking_time']) . '</td>';
                echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td><a href="uploads/transfer_proofs/' . htmlspecialchars($row['transfer_proof']) . '" target="_blank">ดูสลิปการโอน</a></td>';
                echo '<td>';

                // แสดงปุ่ม "อนุมัติ" และ "ยกเลิก" ตลอดเวลา
                echo '<form method="POST" style="display:inline-block;">
                        <input type="hidden" name="approve_booking_id" value="' . $row['booking_id'] . '">
                        <input type="submit" value="อนุมัติ">
                      </form> | 
                      <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="cancel_booking_id" value="' . $row['booking_id'] . '">
                        <input type="submit" value="ยกเลิก">
                      </form>';

                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>ไม่มีการจองคิว</p>';
        }

        $conn->close();
        ?>
    </div>
</body>
</html>

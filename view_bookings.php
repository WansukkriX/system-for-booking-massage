<?php
session_start();
include('db_connect.php');  // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    echo "กรุณาล็อกอินก่อนทำการดูการจอง";
    exit();
}

$user_id = $_SESSION['user_id']; // ID ของผู้ใช้จาก session

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'ThaiMassageDB');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลการจองทั้งหมดจากตาราง bookings
$sql = "SELECT * FROM bookings WHERE user_id = '$user_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองคิวของคุณ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="view-bookings">
        <h2>การจองคิวของคุณ</h2>

        <?php
        if ($result->num_rows > 0) {
            // แสดงตารางการจอง
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
                // ถ้าเป็นสถานะ "Pending" สามารถอนุมัติหรือยกเลิกการจองได้
                if ($row['status'] == 'Pending') {
                    echo '<a href="approve_booking.php?booking_id=' . $row['booking_id'] . '">อนุมัติ</a> | ';
                    echo '<a href="cancel_booking.php?booking_id=' . $row['booking_id'] . '">ยกเลิก</a>';
                } else {
                    echo 'ไม่สามารถเปลี่ยนแปลงได้';
                }
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>ยังไม่มีการจองคิว</p>';
        }

        $conn->close();
        ?>
    </div>
</body>
</html>

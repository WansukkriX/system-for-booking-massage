<?php
session_start();
include('db_connect.php');
include('header.php');

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

// ดึงข้อมูลการจองทั้งหมดจากตาราง bookings ของผู้ใช้
$sql = "SELECT * FROM bookings WHERE user_id = '$user_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองคิวของคุณ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Sarabun', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f5f5 0%, #e8ece5 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .view-bookings {
            background: #fffcf4;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
            padding: 40px;
            width: 100%;
            max-width: 1000px;
            border: 1px solid #e0d8b0;
        }

        h2 {
            color: #4a704a;
            font-size: 30px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #f8f4e6;
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid #e0d8b0;
        }

        th, td {
            padding: 15px;
            text-align: center;
            color: #6b876b;
            font-size: 15px;
        }

        th {
            background: #4a704a;
            color: #fffcf4;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background: #fffcf4;
        }

        tr:hover {
            background: #f0ead8;
        }

        a {
            color: #b89c4a;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #9c843f;
            text-decoration: underline;
        }

        p {
            text-align: center;
            color: #6b876b;
            font-size: 16px;
            margin-top: 20px;
        }

        /* สีสถานะ */
        .status-pending {
            color: #b89c4a; /* ทองอ่อน - รอการยืนยัน */
            font-weight: 500;
        }

        .status-confirmed {
            color: #4a704a; /* เขียวมะกอก - ยืนยันแล้ว */
            font-weight: 500;
        }

        .status-cancelled {
            color: #a94442; /* แดงเข้ม - ยกเลิก */
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .view-bookings {
                padding: 25px;
            }

            h2 {
                font-size: 26px;
            }

            th, td {
                font-size: 14px;
                padding: 10px;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="view-bookings">
        <h2><i class="fas fa-calendar-check"></i> การจองคิวของคุณ</h2>

        <?php
        if ($result->num_rows > 0) {
            // แสดงตารางการจอง
            echo '<table>';
            echo '<tr><th>ชื่อบริการ</th><th>วันที่</th><th>เวลา</th><th>สถานะ</th><th>ชื่อผู้จอง</th><th>เบอร์โทรศัพท์</th><th>อีเมล</th><th>หลักฐานการโอน</th></tr>';

            // แสดงข้อมูลการจองแต่ละแถว
            while ($row = $result->fetch_assoc()) {
                // แปลงสถานะเป็นภาษาไทย
                $status_display = '';
                $status_class = '';
                switch (strtolower($row['status'])) {
                    case 'pending':
                    case 'รอการยืนยัน':
                        $status_display = 'รอการยืนยัน';
                        $status_class = 'status-pending';
                        break;
                    case 'approved':
                    case 'ยืนยันแล้ว':
                        $status_display = 'ยืนยันแล้ว';
                        $status_class = 'status-approved';
                        break;
                    case 'cancelled':
                    case 'ยกเลิก':
                        $status_display = 'ยกเลิก';
                        $status_class = 'status-cancelled';
                        break;
                    default:
                        $status_display = htmlspecialchars($row['status']); // กรณีสถานะอื่น ๆ
                        $status_class = '';
                }

                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['service_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['booking_date']) . '</td>';
                echo '<td>' . htmlspecialchars($row['booking_time']) . '</td>';
                echo '<td class="' . $status_class . '">' . $status_display . '</td>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td><a href="uploads/transfer_proofs/' . htmlspecialchars($row['transfer_proof']) . '" target="_blank">ดูสลิปการโอน</a></td>';
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
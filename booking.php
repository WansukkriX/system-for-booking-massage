<?php
session_start();
include('db_connect.php');  // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการรับข้อมูลจาก URL
if (isset($_GET['service']) && isset($_GET['price']) && isset($_GET['deposit'])) {
    $service = $_GET['service'];
    $price = $_GET['price'];
    $deposit = $_GET['deposit'];
} else {
    echo "ข้อมูลไม่ครบถ้วน";
    exit();
}

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    echo "กรุณาล็อกอินก่อนทำการจอง";
    exit();
}

$user_id = $_SESSION['user_id']; // ID ของผู้ใช้จาก session

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'ThaiMassageDB');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_user = "SELECT name, email, phone_number FROM users WHERE user_id = '$user_id'";
$result_user = $conn->query($sql_user);
$user = $result_user->fetch_assoc();

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $transfer_proof = ''; // รหัสหลักฐานการโอน
    $date = $_POST['booking_date'];
    $time = $_POST['booking_time'];

    // ตรวจสอบการอัปโหลดรูปภาพสลิปการโอน
    if (isset($_FILES['transfer_proof']) && $_FILES['transfer_proof']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['transfer_proof']['name'];
        $file_tmp = $_FILES['transfer_proof']['tmp_name'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // ตรวจสอบประเภทไฟล์
        if (in_array($file_extension, $allowed_extensions)) {
            $new_file_name = uniqid('transfer_', true) . '.' . $file_extension;
            $upload_dir = 'uploads/transfer_proofs/'; // โฟลเดอร์ที่เก็บสลิปการโอน
            $upload_path = $upload_dir . $new_file_name;

            // อัปโหลดไฟล์
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $transfer_proof = $new_file_name; // เก็บชื่อไฟล์ในฐานข้อมูล
            } else {
                echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์.";
            }
        } else {
            echo "ไฟล์ที่อัปโหลดไม่ถูกต้อง กรุณาอัปโหลดไฟล์รูปภาพเท่านั้น.";
        }
    } else {
        echo "กรุณาอัปโหลดสลิปการโอน.";
    }

    // ตรวจสอบข้อมูล
    if (empty($name) || empty($phone) || empty($email) || empty($date) || empty($time) || empty($transfer_proof)) {
        echo "กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        // SQL สำหรับการจองบริการ
        $sql = "INSERT INTO bookings (user_id, service_name, booking_date, booking_time, name, phone, email, transfer_proof, price, deposit, status) 
                VALUES ('$user_id', '$service', '$date', '$time', '$name', '$phone', '$email', '$transfer_proof', '$price', '$deposit', 'Pending')";

        if ($conn->query($sql) === TRUE) {
            echo "<p>จองบริการเรียบร้อยแล้ว!</p>";
        } else {
            echo "เกิดข้อผิดพลาดในการจอง: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จองบริการ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="booking-form">
        <h2>กรุณากรอกข้อมูลเพื่อจองบริการ: <?php echo htmlspecialchars($service); ?></h2>

        <p><strong>บริการที่เลือก:</strong> <?php echo htmlspecialchars($service); ?></p>
        <p><strong>ราคา:</strong> ฿<?php echo number_format($price, 2); ?></p>
        <p><strong>ค่ามัดจำ:</strong> ฿<?php echo number_format($deposit, 2); ?></p>

        <form method="POST" enctype="multipart/form-data">
            <label for="name">ชื่อ-นามสกุล:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br>

            <label for="phone">เบอร์โทรศัพท์:</label>
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required><br>

            <label for="email">อีเมล:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

            <label for="transfer_proof">หลักฐานการโอน (อัปโหลดรูปสลิป):</label>
            <input type="file" name="transfer_proof" accept="image/*" required><br>

            <label for="booking_date">วันที่:</label>
            <input type="date" name="booking_date" required><br>

            <label for="booking_time">เวลา:</label>
            <input type="time" name="booking_time" required><br>

            <input type="submit" value="จองบริการ">
        </form>
    </div>
</body>
</html>

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
    header('Location: login.php');
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
    <title>จองบริการนวดแผนไทย</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- เพิ่ม SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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

        .booking-container {
            background: #fffcf4;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
            padding: 40px;
            width: 100%;
            max-width: 650px;
            border: 1px solid #e0d8b0;
        }

        .booking-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .booking-header h2 {
            color: #4a704a;
            font-size: 30px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .booking-header p {
            color: #6b876b;
            font-size: 16px;
            font-weight: 300;
        }

        .service-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 35px;
            background: #f8f4e6;
            padding: 20px;
            border-radius: 15px;
            border: 1px solid #e0d8b0;
        }

        .service-detail-item {
            text-align: center;
        }

        .service-detail-item i {
            color: #b89c4a;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .service-detail-item h3 {
            color: #4a704a;
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .service-detail-item p {
            color: #6b876b;
            font-size: 15px;
        }

        .booking-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            color: #4a704a;
            font-weight: 500;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group input {
            padding: 12px;
            border: 1px solid #e0d8b0;
            border-radius: 10px;
            font-size: 15px;
            background: #fffcf4;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #b89c4a;
            box-shadow: 0 0 5px rgba(184, 156, 74, 0.2);
        }

        .submit-btn {
            background: #b89c4a;
            color: #fffcf4;
            padding: 14px 20px;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            background: #9c843f;
        }

        @media (max-width: 600px) {
            .booking-container {
                padding: 25px;
            }

            .booking-header h2 {
                font-size: 26px;
            }

            .service-details {
                grid-template-columns: 1fr;
                padding: 15px;
            }

            .submit-btn {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <div class="booking-header">
            <h2><i class="fas fa-spa"></i> จองบริการนวดแผนไทย</h2>
            <p>กรุณากรอกข้อมูลเพื่อทำการจอง</p>
        </div>

        <div class="service-details">
            <div class="service-detail-item">
                <i class="fas fa-tag icon"></i>
                <h3>บริการที่เลือก</h3>
                <p><?php echo htmlspecialchars($service); ?></p>
            </div>
            <div class="service-detail-item">
                <i class="fas fa-coins icon"></i>
                <h3>ราคา</h3>
                <p>฿<?php echo number_format($price, 2); ?></p>
            </div>
            <div class="service-detail-item">
                <i class="fas fa-money-bill-wave icon"></i>
                <h3>ค่ามัดจำ</h3>
                <p>฿<?php echo number_format($deposit, 2); ?></p>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data" class="booking-form" id="bookingForm">
            <div class="form-group">
                <label for="name"><i class="fas fa-user icon"></i>ชื่อ-นามสกุล</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone"><i class="fas fa-phone icon"></i>เบอร์โทรศัพท์</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope icon"></i>อีเมล</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="transfer_proof"><i class="fas fa-receipt icon"></i>หลักฐานการโอน (อัปโหลดรูปสลิป)</label>
                <input type="file" name="transfer_proof" accept="image/*" required>
            </div>

            <div class="form-group">
                <label for="booking_date"><i class="fas fa-calendar icon"></i>วันที่</label>
                <input type="date" name="booking_date" required>
            </div>

            <div class="form-group">
                <label for="booking_time"><i class="fas fa-clock icon"></i>เวลา</label>
                <input type="time" name="booking_time" required>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-check-circle"></i> ยืนยันการจอง
            </button>
        </form>
    </div>

    <!-- เพิ่ม SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault(); // ป้องกันการรีเฟรชหน้าเพื่อทดสอบ

            // สมมติว่าเช็คการส่งข้อมูลไปยังเซิร์ฟเวอร์ (เช่น PHP)
            // ในตัวอย่างนี้ใช้ setTimeout จำลองการรอผลลัพธ์
            setTimeout(() => {
                // ตัวอย่างสถานะจำลอง (สำเร็จหรือล้มเหลว)
                const isSuccess = Math.random() > 0.3; // 70% โอกาสสำเร็จ

                if (isSuccess) {
                    Swal.fire({
                        title: 'จองสำเร็จ!',
                        text: 'การจองของคุณได้รับการยืนยันแล้ว ขอบคุณที่ใช้บริการ',
                        icon: 'success',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#b89c4a'
                    }).then(() => {
                        // รีเซ็ตฟอร์มหลังสำเร็จ (ถ้าต้องการ)
                        document.getElementById('bookingForm').reset();
                    });
                } else {
                    Swal.fire({
                        title: 'จองไม่สำเร็จ',
                        text: 'เกิดข้อผิดพลาด กรุณาตรวจสอบข้อมูลและลองใหม่',
                        icon: 'error',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#b89c4a'
                    });
                }
            }, 1000); // จำลองการหน่วงเวลา 1 วินาที
        });
    </script>
</body>
</html>
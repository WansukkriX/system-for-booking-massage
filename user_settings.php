<?php
session_start();
include('db_connect.php');

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // ตรวจสอบรหัสผ่านใหม่ (ถ้ามีการกรอก)
    if ((!empty($new_password) || !empty($confirm_password)) && ($new_password !== $confirm_password)) {
        $error = "รหัสผ่านใหม่ไม่ตรงกัน!";
    } else {
        // หากมีการกรอกรหัสผ่านใหม่ ให้แฮชและอัปเดต
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $password_sql = "UPDATE users SET password = ? WHERE user_id = ?";
            $password_stmt = $conn->prepare($password_sql);
            $password_stmt->bind_param("si", $hashed_password, $user_id);
            $password_stmt->execute();
            $password_stmt->close();
        }
    }

    // ตรวจสอบและอัปโหลดรูปโปรไฟล์ (ถ้ามี)
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        if (in_array($_FILES['profile_image']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $new_filename = "profile_" . $user_id . "_" . time() . "." . $ext;
            $target_dir = "uploads/profile_pics/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . $new_filename;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                // อัปเดตรูปโปรไฟล์ในฐานข้อมูล
                $update_pic_sql = "UPDATE users SET profile_image = ? WHERE user_id = ?";
                $update_pic_stmt = $conn->prepare($update_pic_sql);
                $update_pic_stmt->bind_param("si", $new_filename, $user_id);
                $update_pic_stmt->execute();
                $update_pic_stmt->close();
            } else {
                $error = "เกิดข้อผิดพลาดในการอัปโหลดรูปโปรไฟล์";
            }
        } else {
            $error = "โปรดอัปโหลดรูปภาพในรูปแบบ JPEG, PNG หรือ GIF เท่านั้น";
        }
    }

    // หากไม่มี error ให้ทำการอัปเดตข้อมูลส่วนตัว
    if (empty($error)) {
        $update_sql = "UPDATE users SET name = ?, email = ?, phone_number = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $name, $email, $phone, $user_id);
        if ($update_stmt->execute()) {
            $success = "ข้อมูลของคุณถูกอัปเดตเรียบร้อยแล้ว!";
        } else {
            $error = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล!";
        }
        $update_stmt->close();
    }
    
    // ดึงข้อมูลผู้ใช้ที่อัปเดตแล้ว
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งค่าบัญชี</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .settings-container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .profile-preview {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-preview img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .button {
            padding: 10px 20px;
            background-color: #2ecc71;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
        .success-message {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <h2>ตั้งค่าบัญชีของคุณ</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="profile-preview">
            <?php
            // ถ้ามีรูปโปรไฟล์จากผู้ใช้ ให้นำมาแสดง, มิฉะนั้นใช้ default.png
            $profilePic = !empty($user['profile_image']) ? 'uploads/profile_pics/' . $user['profile_image'] : 'img/default.jpg';
            ?>
            <img src="<?php echo $profilePic; ?>" alt="Profile Picture">
        </div>
        
        <form method="POST" action="user_settings.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profile_image">เปลี่ยนรูปโปรไฟล์ (ถ้าต้องการ):</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
            </div>
            
            <div class="form-group">
                <label for="name">ชื่อ-นามสกุล:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">อีเมล:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone">เบอร์โทรศัพท์:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">รหัสผ่านใหม่ (ถ้าต้องการเปลี่ยน):</label>
                <input type="password" id="new_password" name="new_password">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">ยืนยันรหัสผ่านใหม่:</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
            
            <button type="submit" class="button">บันทึกการเปลี่ยนแปลง</button>
        </form>
    </div>
</body>
</html>

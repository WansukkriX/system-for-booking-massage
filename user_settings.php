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
    <title>ตั้งค่าบัญชี - นวดแผนไทย</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #8B4513;
            --gold: #DAA520;
            --deep-gold: #B8860B;
            --cream: #FDF5E6;
            --light-brown: #DEB887;
            --dark-brown: #654321;
            --error-red: #8B0000;
            --success-green: #006400;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Noto Sans Thai', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(
                rgba(253, 245, 230, 0.95), 
                rgba(253, 245, 230, 0.95)
            ),
            url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 5C15.9 5 5 15.9 5 30s10.9 25 25 25 25-10.9 25-25S44.1 5 30 5zm0 45c-11 0-20-9-20-20s9-20 20-20 20 9 20 20-9 20-20 20z' fill='rgba(139, 69, 19, 0.1)'/%3E%3C/svg%3E");
            padding: 2rem;
        }

        .main-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }

        .settings-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(139, 69, 19, 0.1);
            overflow: hidden;
            position: relative;
        }

        /* Thai Pattern Border */
        .thai-border {
            position: absolute;
            width: 100%;
            height: 10px;
            background: linear-gradient(90deg, var(--gold), var(--deep-gold));
        }

        .thai-border-top { top: 0; }
        .thai-border-bottom { bottom: 0; }

        .settings-header {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-brown));
            color: var(--cream);
            padding: 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .settings-header h2 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .settings-header p {
            color: var(--light-brown);
            font-size: 1.1rem;
        }

        /* Thai Pattern Background */
        .thai-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            background-image: url("data:image/svg+xml,%3Csvg width='44' height='44' viewBox='0 0 44 44' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M22 0C9.8 0 0 9.8 0 22s9.8 22 22 22 22-9.8 22-22S34.2 0 22 0zm0 40c-9.9 0-18-8.1-18-18S12.1 4 22 4s18 8.1 18 18-8.1 18-18 18z' fill='%23FFFFFF'/%3E%3C/svg%3E");
        }

        .settings-content {
            padding: 3rem;
            background: white;
        }

        .profile-section {
            text-align: center;
            margin-bottom: 3rem;
        }

        .profile-container {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto;
            border-radius: 50%;
            padding: 8px;
            background: linear-gradient(135deg, var(--gold), var(--deep-gold));
        }

        .profile-image {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            transition: transform 0.3s ease;
        }

        .upload-button {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 45px;
            height: 45px;
            background: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .upload-button:hover {
            transform: scale(1.1);
            background: var(--deep-gold);
        }

        .upload-button i {
            color: white;
            font-size: 1.2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: var(--dark-brown);
            font-weight: 500;
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
        }

        .form-group input {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid var(--light-brown);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: var(--cream);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(218, 165, 32, 0.1);
        }

        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--dark-brown);
            transition: color 0.3s ease;
        }

        .submit-button {
            grid-column: 1 / -1;
            background: linear-gradient(135deg, var(--gold), var(--deep-gold));
            color: white;
            padding: 1.2rem;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(218, 165, 32, 0.3);
        }

        .message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-weight: 500;
            text-align: center;
        }

        .error-message {
            background-color: rgba(139, 0, 0, 0.1);
            color: var(--error-red);
            border: 1px solid var(--error-red);
        }

        .success-message {
            background-color: rgba(0, 100, 0, 0.1);
            color: var(--success-green);
            border: 1px solid var(--success-green);
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .settings-content {
                padding: 2rem;
            }

            .profile-container {
                width: 150px;
                height: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="settings-container">
            <div class="thai-border thai-border-top"></div>
            
            <div class="settings-header">
                <div class="thai-pattern"></div>
                <div class="header-content">
                    <h2>
                        <i class="fas fa-spa"></i>
                        ตั้งค่าบัญชีผู้ใช้
                    </h2>
                    <p>ปรับแต่งข้อมูลส่วนตัวของคุณ</p>
                </div>
            </div>

            <div class="settings-content">
                <?php if (!empty($error)): ?>
                    <div class="message error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="message success-message">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="user_settings.php" enctype="multipart/form-data">
                    <div class="profile-section">
                        <div class="profile-container">
                            <?php
                            $profilePic = !empty($user['profile_image']) ? 'uploads/profile_pics/' . $user['profile_image'] : 'img/default.jpg';
                            ?>
                            <img src="<?php echo $profilePic; ?>" alt="รูปโปรไฟล์" id="preview-image" class="profile-image">
                            <label class="upload-button" for="profile_image">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(this)" style="display: none;">
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-user"></i>
                                ชื่อ-นามสกุล
                            </label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-phone"></i>
                                เบอร์โทรศัพท์
                            </label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-envelope"></i>
                                อีเมล
                            </label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                    

                        <div class="form-group">
                            <label>
                                <i class="fas fa-lock"></i>
                                รหัสผ่านใหม่
                            </label>
                            <div class="password-group">
                                <input type="password" id="new_password" name="new_password">
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password')"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-lock"></i>
                                ยืนยันรหัสผ่านใหม่
                            </label>
                            <div class="password-group">
                                <input type="password" id="confirm_password" name="confirm_password">
                                <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
                            </div>
                        </div>

                        <button type="submit" class="submit-button">
                            <i class="fas fa-save"></i>
                            บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </form>
            </div>

            <div class="thai-border thai-border-bottom"></div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
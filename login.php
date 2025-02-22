<?php
session_start();
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // สืบค้นอีเมลในฐานข้อมูล
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // ตรวจสอบรหัสผ่าน
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['name'];

            // ใช้ role จากฐานข้อมูลเพื่อกำหนดการเข้าถึง
            $_SESSION['role'] = $row['role'];

            // ถ้าเป็น admin ให้ไปที่ dashboard
            if ($_SESSION['role'] == 'admin') {
                header("Location: dashboard.php");  // ไปที่หน้า Dashboard ของแอดมิน
                exit();
            } else {
                header("Location: index.php");  // ไปที่หน้า Dashboard ของผู้ใช้
                exit();
            }
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง!";
        }
    } else {
        $error = "ไม่พบผู้ใช้ที่มีอีเมลนี้!";
    }
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ร้านนวดแผนไทย</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Sarabun', sans-serif;
        }

        body {
            background: #2c3e50;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .logo::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: #f1c40f;
            border-radius: 2px;
        }

        .logo img {
            width: 120px;
            height: auto;
        }

        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 1.8rem;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .error-message {
            background: #e74c3c;
            color: white;
            padding: 0.8rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
            font-size: 1rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            color: #2c3e50;
        }

        .form-group input:focus {
            border-color: #2c3e50;
            outline: none;
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 0.9rem;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }

        .submit-btn:hover {
            background: #34495e;
            transform: translateY(-2px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .links {
            margin-top: 1.5rem;
            text-align: center;
            display: flex;
            justify-content: center;
            gap: 1.5rem;
        }

        .links a {
            color: #2c3e50;
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 1px;
            background: #2c3e50;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .links a:hover::after {
            transform: scaleX(1);
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem;
            }

            h2 {
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .form-group {
                margin-bottom: 1rem;
            }

            .links {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="logo.png" alt="โลโก้ร้านนวดแผนไทย">
        </div>
        <h2>เข้าสู่ระบบ</h2>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="email">อีเมล</label>
                <input type="email" id="email" name="email" required 
                       placeholder="กรุณากรอกอีเมลของคุณ">
            </div>

            <div class="form-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required
                       placeholder="กรุณากรอกรหัสผ่านของคุณ">
            </div>

            <button type="submit" class="submit-btn">เข้าสู่ระบบ</button>
        </form>

        <div class="links">
            <a href="user_register.php">สร้างบัญชีใหม่</a>
            <a href="forgot_password.php">ลืมรหัสผ่าน?</a>
        </div>
    </div>

    <script>
        function validateForm() {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!email || !password) {
                alert('กรุณากรอกข้อมูลให้ครบถ้วน');
                return false;
            }

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                alert('กรุณากรอกอีเมลให้ถูกต้อง');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
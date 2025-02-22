<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียน - ร้านนวดแผนไทย</title>
    <link rel="stylesheet" href="style/login.css">
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <img src="logo.png" alt="โลโก้ร้านนวดแผนไทย">
        </div>
        <h2>ลงทะเบียนสมาชิกใหม่</h2>

        <?php
        include('db_connect.php');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            // ตรวจสอบอีเมลซ้ำ
            $check_email = "SELECT * FROM users WHERE email = '$email'";
            $result = $conn->query($check_email);
            
            if ($result->num_rows > 0) {
                echo "<div class='error-message'>อีเมลนี้ถูกใช้งานแล้ว กรุณาใช้อีเมลอื่น</div>";
            } else {
                $sql = "INSERT INTO users (name, phone_number, email, password) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $name, $phone, $email, $password);
                
                if ($stmt->execute()) {
                    echo "<div class='success-message'>
                            ลงทะเบียนสำเร็จ! 
                            <br><a href='login.php' style='color: white; text-decoration: underline;'>คลิกที่นี่เพื่อเข้าสู่ระบบ</a>
                          </div>";
                } else {
                    echo "<div class='error-message'>เกิดข้อผิดพลาด: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
            $conn->close();
        }
        ?>

        <form method="POST" action="user_register.php" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="name">ชื่อ-นามสกุล</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="phone_number">เบอร์โทรศัพท์</label>
                <input type="tel" id="phone_number" name="phone_number" pattern="[0-9]{10}" title="กรุณากรอกเบอร์โทรศัพท์ 10 หลัก" required>
            </div>

            <div class="form-group">
                <label for="email">อีเมล</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" minlength="6" required>
            </div>

            <button type="submit" class="submit-btn">ลงทะเบียน</button>
        </form>

        <div class="links">
            <p>มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>
        </div>
    </div>

    <script>
        function validateForm() {
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone_number').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // ตรวจสอบชื่อ
            if (name.length < 2) {
                alert('กรุณากรอกชื่อ-นามสกุลให้ถูกต้อง');
                return false;
            }

            // ตรวจสอบเบอร์โทร
            const phonePattern = /^[0-9]{10}$/;
            if (!phonePattern.test(phone)) {
                alert('กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง (10 หลัก)');
                return false;
            }

            // ตรวจสอบอีเมล
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                alert('กรุณากรอกอีเมลให้ถูกต้อง');
                return false;
            }

            // ตรวจสอบรหัสผ่าน
            if (password.length < 6) {
                alert('รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
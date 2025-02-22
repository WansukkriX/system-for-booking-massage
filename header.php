<?php
// session_start();
include('db_connect.php');

// ตรวจสอบการล็อกอิน
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // ดึงข้อมูลโปรไฟล์จากฐานข้อมูล
    $sql = "SELECT profile_image FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // ถ้าไม่มีการอัปโหลดรูปโปรไฟล์ ใช้รูป default.png
    $profile_image = !empty($row['profile_image']) ? 'uploads/profile_pics/' . $row['profile_image'] : 'img/default.png';
} else {
    $profile_image = 'img/default.png'; // ถ้ายังไม่ได้ล็อกอิน ใช้ default.png
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Sarabun', sans-serif;
        }

        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #2c3e50;
            padding: 0.8rem 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo-text {
            color: #fff;
            font-size: 1.6rem;
            font-weight: 600;
            position: relative;
            padding: 0.3rem 0;
        }

        .logo-text::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: #f1c40f;
            transform: scaleX(0.7);
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-menu li a {
            color: #fff;
            text-decoration: none;
            font-size: 1.1rem;
            padding: 0.6rem 1rem;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .nav-menu li a:hover {
            background-color: #34495e;
        }

        .nav-menu li a.active {
           border-radius:20px;
            background-color:rgba(241, 196, 15, 0.63);
            border-bottom: 2px solid #f1c40f;
        }
        .nav-menu li a.active:hover {
           
            background-color:#f1c40f;
           
        }

        

        .user-profile {
            position: relative;
            padding: 0.3rem;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #f1c40f;
            object-fit: cover;
            cursor: pointer;
        }

        .profile-img:hover{
            transform: scale(1.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
             box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background-color: #f1c40f;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            min-width: 180px;
        }

        .dropdown-content a {
            display: block;
            padding: 0.8rem 1.2rem;
            text-decoration: none;
            color:rgb(0, 136, 255);
            transition: all 0.2s ease;
            font-size: 1rem;
        }

        .dropdown-content a:hover {
            background-color: #f5f6fa;
            color: #2c3e50;
        }

        .show {
            display: block;
        }

        .hamburger {
            display: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            border: 1px solid transparent;
        }

        .hamburger:hover {
            border-color: rgba(255,255,255,0.3);
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 0.6rem 1rem;
            }

            .hamburger {
                display: block;
            }

            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: #2c3e50;
                padding: 1rem;
                flex-direction: column;
                gap: 0.8rem;
                border-top: 1px solid rgba(255,255,255,0.1);
            }

            .nav-menu.active {
                display: flex;
                
            }

            .nav-menu li {
                width: 100%;
            }

            .nav-menu li a {
                display: block;
                text-align: center;
                padding: 0.8rem;
                width: 100%;
                border-radius: 4px;
            }

            .nav-menu li a:hover {
                background-color: #34495e;
            }

            .user-profile {
                display: flex;
                justify-content: center;
                width: 100%;
                padding: 0.5rem 0;
            }

            .dropdown-content {
                width: 90%;
                position: static;
                margin: 0.5rem auto;
                box-shadow: none;
                border: 1px solid rgba(255,255,255,0.1);
                background-color: #34495e;
            }

            .dropdown-content a {
                color: #fff;
                text-align: center;
            }

            .dropdown-content a:hover {
                background-color: #2c3e50;
                color: #fff;
            }

            .logo-text {
                font-size: 1.4rem;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 0.5rem;
            }

            .logo-text {
                font-size: 1.2rem;
            }

            .nav-menu li a {
                font-size: 1rem;
                padding: 0.7rem;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <div class="logo">
            <span class="logo-text">นวดแผนไทย</span>
        </div>
        <ul class="nav-menu">
            <li><a href="index.php">หน้าหลัก</a></li>
            <li><a href="#services">บริการของเรา</a></li>
            <!-- <li><a href="booking.php">จองคิว</a></li> -->
            <li><a href="contact.php">ติดต่อเรา</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="user-profile">
                    <img src="<?php echo $profile_image; ?>" alt="Profile Picture" class="profile-img" onclick="toggleDropdown()">
                    <div class="dropdown-content" id="dropdownMenu">
                        <a href="user_settings.php">ตั้งค่าบัญชี</a>
                        <a href="logout.php">ออกจากระบบ</a>
                    </div>
                </li>
            <?php else: ?>
                <li><a href="login.php" class="active login">เข้าสู่ระบบ</a></li>
            <?php endif; ?>
        </ul>
        <div class="hamburger">☰</div>
    </div>
</nav>

<script>
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    const profileImg = document.querySelector('.profile-img');

    hamburger.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });

    function toggleDropdown() {
        var dropdownMenu = document.getElementById('dropdownMenu');
        dropdownMenu.classList.toggle('show');
    }

    // ปิด dropdown เมื่อคลิกนอกพื้นที่
    window.addEventListener('click', (e) => {
        if (!e.target.matches('.profile-img')) {
            const dropdown = document.getElementById('dropdownMenu');
            if (dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    });

    // ปิดเมนูเมื่อคลิกลิงก์ในโหมดมือถือ
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                navMenu.classList.remove('active');
            }
        });
    });
</script>
</body>
</html>
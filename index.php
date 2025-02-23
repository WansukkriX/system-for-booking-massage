<?php
session_start();
include('header.php');  // รวม header.php
include('db_connect.php');  // เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลบริการจากฐานข้อมูล
$sql = "SELECT * FROM services";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บริการนวดแผนไทย | Thai Massage & Spa</title>
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
            color: #6b876b;
            line-height: 1.6;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('img/banner.jpg') no-repeat center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fffcf4;
        }

        .hero-content h1 {
            font-size: 40px;
            font-weight: 600;
            color: #fffcf4;
            margin-bottom: 15px;
        }

        .hero-content p {
            font-size: 18px;
            margin-bottom: 25px;
        }

        .cta-button {
            background: #b89c4a;
            color: #fffcf4;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .cta-button:hover {
            background: #9c843f;
        }

        /* Services Section */
        .services {
            padding: 60px 20px;
            background: #fffcf4;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h2 {
            color: #4a704a;
            font-size: 30px;
            font-weight: 600;
        }

        .section-title p {
            color: #6b876b;
            font-size: 16px;
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .service-card {
            background: #f8f4e6;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e0d8b0;
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-10px);
        }

        .service-image {
            height: 200px;
            background-size: cover;
            background-position: center;
        }

        .service-content {
            padding: 20px;
            text-align: center;
        }

        .service-content h3 {
            color: #4a704a;
            font-size: 20px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .service-content p {
            color: #6b876b;
            font-size: 15px;
            margin-bottom: 15px;
        }

        .price {
            color: #b89c4a;
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .book-button {
            display: inline-block;
            background: #b89c4a;
            color: #fffcf4;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .book-button:hover {
            background: #9c843f;
        }

        /* Features Section */
        .features {
            padding: 60px 20px;
            background: #f8f4e6;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature {
            text-align: center;
        }

        .feature-icon {
            font-size: 40px;
            color: #b89c4a;
            margin-bottom: 15px;
        }

        .feature h3 {
            color: #4a704a;
            font-size: 20px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .feature p {
            color: #6b876b;
            font-size: 15px;
        }

        /* Footer */
        footer {
            background: #4a704a;
            color: #fffcf4;
            text-align: center;
            padding: 20px;
        }

        footer a {
            color: #b89c4a;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #fffcf4;
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #b89c4a;
            color: #fffcf4;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: none;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .back-to-top:hover {
            background: #9c843f;
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 30px;
            }

            .hero-content p {
                font-size: 16px;
            }

            .section-title h2 {
                font-size: 26px;
            }

            .service-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <div class="hero" id='index'>
        <div class="hero-content">
            <h1>สัมผัสประสบการณ์การนวดแผนไทยที่แท้จริง</h1>
            <p>ผ่อนคลายด้วยศาสตร์การนวดไทยโบราณ โดยผู้เชี่ยวชาญมืออาชีพ</p>
            <a href="#services" class="cta-button">จองบริการเลย</a>
        </div>
    </div>

    <section class="services" id="services">
        <div class="section-title">
            <h2>บริการของเรา</h2>
            <p>เลือกบริการที่ตรงใจคุณ</p>
        </div>

        <div class="service-grid">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $image_url = !empty($row['image']) ? 'uploads/service_images/' . $row['image'] : '/api/placeholder/400/320';
                    echo '<div class="service-card">';
                    echo '<div class="service-image" style="background-image: url(\'' . $image_url . '\')"></div>';
                    echo '<div class="service-content">';
                    echo '<h3>' . htmlspecialchars($row['service_name']) . '</h3>';
                    echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                    echo '<div class="price">฿' . number_format($row['price'], 2) . '</div>';
                    echo '<a href="booking.php?service=' . urlencode($row['service_name']) . '&price=' . $row['price'] . '&deposit=' . $row['deposit'] . '" class="book-button">จองบริการ</a>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </section>

    <section class="features">
        <div class="features-grid">
            <div class="feature">
                <div class="feature-icon"><i class="fas fa-hands"></i></div>
                <h3>ผู้เชี่ยวชาญมืออาชีพ</h3>
                <p>ทีมนวดของเราผ่านการอบรมและมีประสบการณ์สูง</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><i class="fas fa-leaf"></i></div>
                <h3>ผลิตภัณฑ์ธรรมชาติ</h3>
                <p>ใช้สมุนไพรไทยแท้คุณภาพสูง</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><i class="fas fa-spa"></i></div>
                <h3>บรรยากาศผ่อนคลาย</h3>
                <p>สถานที่สะอาด สวยงาม น่าใช้บริการ</p>
            </div>
        </div>
    </section>

    <footer>
        <p>© 2025 บริการนวดแผนไทย. สงวนลิขสิทธิ์.</p>
        <p><a href="privacy.php">นโยบายความเป็นส่วนตัว</a> | <a href="terms.php">เงื่อนไขการใช้บริการ</a></p>
    </footer>

    <div class="back-to-top" id="backToTop"><i class="fas fa-arrow-up"></i></div>

    <script>
        // แสดงปุ่มเมื่อเลื่อนลงมา
        window.addEventListener("scroll", function () {
            var backToTop = document.getElementById("backToTop");
            if (window.scrollY > 300) {
                backToTop.style.display = "flex";
            } else {
                backToTop.style.display = "none";
            }
        });

        // เมื่อคลิกปุ่ม Back to Top
        document.getElementById("backToTop").addEventListener("click", function () {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    </script>   
</body>
</html>
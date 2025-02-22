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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/index.css">  
</head>
<body>

    <div class="hero" id='index' >
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
                    // echo '<div class="deposit">ค่ามัดจำ: ฿' . number_format($row['deposit'], 2) . '</div>';
                    // เพิ่มลิงก์สำหรับไปที่หน้า booking.php โดยส่งข้อมูล service, price, และ deposit
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
                <div class="feature-icon">👐</div>
                <h3>ผู้เชี่ยวชาญมืออาชีพ</h3>
                <p>ทีมนวดของเราผ่านการอบรมและมีประสบการณ์สูง</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🌿</div>
                <h3>ผลิตภัณฑ์ธรรมชาติ</h3>
                <p>ใช้สมุนไพรไทยแท้คุณภาพสูง</p>
            </div>
            <div class="feature">
                <div class="feature-icon">✨</div>
                <h3>บรรยากาศผ่อนคลาย</h3>
                <p>สถานที่สะอาด สวยงาม น่าใช้บริการ</p>
            </div>
        </div>
    </section>

    <footer>
        <p>© 2025 บริการนวดแผนไทย. สงวนลิขสิทธิ์.</p>
        <p><a href="privacy.php">นโยบายความเป็นส่วนตัว</a> | <a href="terms.php">เงื่อนไขการใช้บริการ</a></p>
    </footer>

    <div class="back-to-top" id="backToTop">↑</div>

<script>
    // แสดงปุ่มเมื่อเลื่อนลงมา
    window.addEventListener("scroll", function () {
        var backToTop = document.getElementById("backToTop");
        if (window.scrollY > 300) {
            backToTop.style.display = "block";
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

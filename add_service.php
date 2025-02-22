<?php
session_start();
include('db_connect.php');

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $service_name = $_POST['service_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $deposit = $_POST['deposit'];  // ค่ามัดจำ

    // ตรวจสอบการกรอกค่ามัดจำให้ถูกต้อง
    if (empty($service_name) || empty($description) || empty($price) || empty($category)) {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน!";
    } else {
        // ตรวจสอบและอัปโหลดรูปภาพ (ถ้ามี)
        $image_filename = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
            if (in_array($_FILES['image']['type'], $allowed_types)) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_filename = "service_" . time() . "." . $ext;
                $target_dir = "uploads/service_images/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $target_file = $target_dir . $image_filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    // อัปโหลดรูปภาพสำเร็จ
                } else {
                    $error = "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ!";
                }
            } else {
                $error = "โปรดอัปโหลดรูปภาพในรูปแบบ JPEG, PNG หรือ GIF เท่านั้น";
            }
        }

        // SQL เพื่อเพิ่มบริการใหม่
        if (empty($error)) {
            $sql = "INSERT INTO services (service_name, description, price, category, deposit, image) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdsds", $service_name, $description, $price, $category, $deposit, $image_filename);

            if ($stmt->execute()) {
                $success = "บริการใหม่ถูกเพิ่มเรียบร้อยแล้ว!";
            } else {
                $error = "เกิดข้อผิดพลาดในการเพิ่มบริการ!";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มบริการใหม่</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            width: 80%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .button {
            padding: 10px 20px;
            background-color: #2ecc71;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #27ae60;
        }
        .error-message, .success-message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .error-message {
            background-color: #f2dede;
            color: #a94442;
        }
        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>เพิ่มบริการใหม่</h2>

        <!-- แสดงข้อความผิดพลาด -->
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- แสดงข้อความสำเร็จ -->
        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="add_service.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="service_name">ชื่อบริการ:</label>
                <input type="text" id="service_name" name="service_name" value="<?php echo isset($service_name) ? htmlspecialchars($service_name) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="description">คำอธิบายบริการ:</label>
                <textarea id="description" name="description" rows="4" required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="price">ราคา:</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo isset($price) ? htmlspecialchars($price) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="category">ประเภทบริการ:</label>
                <select id="category" name="category" required>
                    <option value="นวดแผนไทย" <?php echo isset($category) && $category == 'นวดแผนไทย' ? 'selected' : ''; ?>>นวดแผนไทย</option>
                    <option value="นวดอโรม่า" <?php echo isset($category) && $category == 'นวดอโรม่า' ? 'selected' : ''; ?>>นวดอโรม่า</option>
                    <option value="นวดฝ่าเท้า" <?php echo isset($category) && $category == 'นวดฝ่าเท้า' ? 'selected' : ''; ?>>นวดฝ่าเท้า</option>
                </select>
            </div>

            <div class="form-group">
                <label for="deposit">ค่ามัดจำ:</label>
                <input type="number" id="deposit" name="deposit" step="0.01" value="<?php echo isset($deposit) ? htmlspecialchars($deposit) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="image">เลือกรูปภาพบริการ:</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <button type="submit" class="button">เพิ่มบริการ</button>
        </form>
    </div>
</body>
</html>

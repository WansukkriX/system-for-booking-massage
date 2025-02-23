<?php
session_start();
include('db_connect.php');

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// ถ้าผู้ใช้งานเป็นแอดมิน
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// ดึงข้อมูลจำนวนผู้ใช้งานทั้งหมด
$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $conn->query($sql_users);
$row_users = $result_users->fetch_assoc();
$total_users = $row_users['total_users'];

// ดึงข้อมูลจำนวนการจองคิวทั้งหมด
$sql_bookings = "SELECT COUNT(*) AS total_bookings FROM bookings";
$result_bookings = $conn->query($sql_bookings);
$row_bookings = $result_bookings->fetch_assoc();
$total_bookings = $row_bookings['total_bookings'];

// ดึงข้อมูลจำนวนหมอนวดทั้งหมดจากตาราง therapists
$sql_therapists = "SELECT COUNT(*) AS total_therapists FROM therapists";
$result_therapists = $conn->query($sql_therapists);
$row_therapists = $result_therapists->fetch_assoc();
$total_therapists = $row_therapists['total_therapists'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - นวดแผนไทย</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Sarabun', sans-serif;
        }

        body {
            background-color: #f5f6fa;
            padding-top: 2rem;
            min-height: 100vh;
        }

        .dashboard {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .dashboard h2 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1c40f;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-box {
            background: #2c3e50;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            color: white;
            transition: transform 0.2s ease;
        }

        .info-box:hover {
            transform: translateY(-5px);
        }

        .info-box h3 {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #f1c40f;
        }

        .info-box p {
            font-size: 2rem;
            font-weight: 600;
            text-align: center;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }

        .action-buttons a {
            background: #34495e;
            color: white;
            padding: 1rem 1.5rem;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .action-buttons a:hover {
            background: #2c3e50;
            transform: translateY(-2px);
        }

        .search-box {
            background: white;
            padding: 1rem;
            margin-bottom:20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .search-box form {
            display: flex;
            gap: 1rem;
        }

        .search-box input {
            flex: 1;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .search-box button {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .search-box button:hover {
            background: #34495e;
        }

        @media (max-width: 768px) {
            .dashboard {
                padding: 1rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .search-box form {
                flex-direction: column;
            }

            .search-box button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    
    <div class="dashboard">
        <h2>ยินดีต้อนรับสู่ Dashboard ของแอดมิน</h2>
        <div class="search-box">
            <form method="GET" action="search_results.php">
                <input type="text" name="search_query" placeholder="ค้นหาข้อมูล..." required>
                <button type="submit">ค้นหา</button>
            </form>
        </div>
        <div class="stats-container">
            <div class="info-box">
                <h3>จำนวนผู้ใช้งานทั้งหมด</h3>
                <p><?php echo number_format($total_users); ?></p>
            </div>

            <div class="info-box">
                <h3>จำนวนการจองคิวทั้งหมด</h3>
                <p><?php echo number_format($total_bookings); ?></p>
                
            </div>

            <div class="info-box">
                <h3>จำนวนหมอนวดทั้งหมด</h3>
                <p><?php echo number_format($total_therapists); ?></p>
            </div>

            <div class="info-box">
                <h3>บริการทั้งหมด</h3>
                <p><?php echo number_format($total_therapists); ?></p>
            </div>
        </div>

        <div class="action-buttons">
            <a href="manage_users.php">จัดการผู้ใช้</a>
            <a href="view_admin_bookings.php">จัดการคิว</a>
            <a href="manage_therapists.php">จัดการหมอนวด</a>
            <a href="manage_therapists.php">จัดการบริการ</a>
        </div>

       
    </div>
</body>
</html>
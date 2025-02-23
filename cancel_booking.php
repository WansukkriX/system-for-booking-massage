<?php
session_start();
include('db_connect.php');

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "คุณไม่สามารถเข้าถึงหน้านี้ได้";
    exit();
}

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    // เชื่อมต่อฐานข้อมูล
    $conn = new mysqli('localhost', 'root', '', 'ThaiMassageDB');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // อัปเดตสถานะการจอง
    $sql = "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = '$booking_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<p>การจองถูกยกเลิกแล้ว!</p>";
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตสถานะ: " . $conn->error;
    }

    $conn->close();
}
?>

<?php
$booking_id = $_GET['id'];
$conn = new mysqli('localhost', 'root', '', 'ThaiMassageDB');
$sql = "UPDATE bookings SET status='Cancelled' WHERE booking_id='$booking_id'";
$conn->query($sql);
$conn->close();
echo "Booking cancelled!";
?>

<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
  die("Access Denied!");
}

$conn = new mysqli('localhost', 'root', '', 'ThaiMassageDB');
$sql = "SELECT * FROM bookings";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
  echo "Booking ID: " . $row['booking_id'] . " Service: " . $row['service_name'] . " Date: " . $row['booking_date'] . "<br>";
  echo "<a href='approve_booking.php?id=" . $row['booking_id'] . "'>Approve</a> | ";
  echo "<a href='cancel_booking.php?id=" . $row['booking_id'] . "'>Cancel</a><br>";
}
$conn->close();
?>

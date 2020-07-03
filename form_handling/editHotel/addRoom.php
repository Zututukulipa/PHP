<?php
include '../../config.php';
$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);
$stmt = $conn->prepare("INSERT INTO Room(name, price, capacity, room_idHotel) VALUES(:name, :price, :capacity, :hotel)");
$stmt->bindParam(':name', $_POST['roomName']);
$stmt->bindParam(':price', $_POST['roomPrice']);
$stmt->bindParam(':capacity', $_POST['roomCapacity']);
$stmt->bindParam(':hotel', $_POST['hotelId']);
$stmt->execute();
header('Location: http://localhost/editHotel.php?hotelId='.$_POST['hotelId']);
exit;
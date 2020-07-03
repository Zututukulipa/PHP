<?php
include '../../config.php';
$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);

$stmt = $conn->prepare("DELETE FROM Hotel_employees WHERE Hotel_id =:hotelId");
$stmt->bindParam(':hotelId', $_POST['hotelId']);
$stmt->execute();

$stmt = $conn->prepare("DELETE FROM Hotel_administrators WHERE administrator_hotelId =:hotelId");
$stmt->bindParam(':hotelId', $_POST['hotelId']);
$stmt->execute();

$stmt = $conn->prepare("DELETE FROM Room WHERE room_idHotel =:hotelId");
$stmt->bindParam(':hotelId', $_POST['hotelId']);
$stmt->execute();

$stmt = $conn->prepare("DELETE FROM Hotel WHERE idHotel =:hotelId");
$stmt->bindParam(':hotelId', $_POST['hotelId']);
$stmt->execute();

header('Location: http://localhost/hotelAdministration.php');
exit;
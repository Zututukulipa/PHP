<?php
include '../../config.php';
$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);

if (isset($_POST['deleteItem']) and is_numeric($_POST['deleteItem'])) {
$stmt = $conn->prepare("DELETE FROM Reservation WHERE idReservation = :reservationId");
$stmt->bindParam(':reservationId', $_POST['deleteItem']);
$stmt->execute();
Header('Location: '.$_SERVER['PHP_SELF']);
}

<?php
include '../../config.php';
ob_start();
session_start();
$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);
$valid = false;

foreach ($_SESSION["adminOf"] as $privilege) {
    if($privilege[0] == $_POST["hotel"]){
        $valid = true;
    }
}
if($valid) {
    $stmt = $conn->prepare("SELECT hotelName FROM Hotel WHERE idHotel=:idHotel");
    $stmt->bindParam(":idHotel", $_POST["hotel"]);
    $stmt->execute();
    $hotelName = $stmt->fetch();
    $stmt = $conn->prepare("SELECT checkIn, checkOut, idRoom, idUser, length, total, name, firstName, surname 
                                    FROM Room r JOIN Reservation R2 on r.idRoom = R2.Room_idRoom 
                                                JOIN User U on R2.Guest_idGuest = U.idUser 
                                    WHERE r.room_idHotel=:idHotel");
    $stmt->bindParam(":idHotel", $_POST["hotel"]);
    $stmt->execute();
    $reservationsData = $stmt->fetchAll(PDO::FETCH_OBJ);

    $myJSON = json_encode($reservationsData);
    $data = fopen('php://output', 'w');

    fwrite($data, $myJSON);

    fclose($data);
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="'. $hotelName[0] . ' ' . date('d-m-Y') . '.json');
}

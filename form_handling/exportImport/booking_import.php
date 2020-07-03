<?php
ob_start();
session_start();
include "../../config.php";
if (isset($_FILES['file'])) {
// get the csv file and open it up
$file = $_FILES['file']['tmp_name'];
$handle = fopen($file, "r");
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);

    $query_ip = $conn->prepare("
            INSERT INTO Reservation(checkIn, checkOut, Room_idRoom, Guest_idGuest, length, total) 
            VALUES (:checkIn,:checkOut,:idRoom,:idGuest,:length,:total)");

    $dat = json_decode(file_get_contents($file));
    fclose($handle);
    foreach ($dat as $item) {
        print_r($item);
        $query_ip->bindParam(':checkIn', $item->checkIn);
        $query_ip->bindParam(':checkOut', $item->checkOut);
        $query_ip->bindParam(':idRoom', $item->idRoom);
        $query_ip->bindParam(':idGuest', $item->idUser);
        $query_ip->bindParam(':length', $item->length);
        $query_ip->bindParam(':total', $item->total);
        $query_ip->execute();
    }

} catch(PDOException $e) {
die($e->getMessage());
}


}
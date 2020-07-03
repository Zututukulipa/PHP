<?php
include '../../config.php';
$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);
if(!empty($_POST) && $_POST['action'] == 'editRoom'){
    $names = $_POST['names'];
    $prices = $_POST['prices'];
    $capacities = $_POST['capacities'];
    $ids = $_POST['ids'];
    for ($i = 0; $i < count($_POST['names']); ++$i){
        $name = $names[$i];
        $price = $prices[$i];
        $capacity = $capacities[$i];
        $id = $ids[$i];

        $stmt = $conn->prepare("UPDATE Room SET name=:newName, price=:newPrice, capacity=:newCapacity
                                            WHERE room_idHotel=:idHotel AND idRoom=:idRoom");
        $stmt->bindParam(':idHotel', $_POST["hotelId"]);
        $stmt->bindParam(':newName', $name);
        $stmt->bindParam(':newPrice', $price);
        $stmt->bindParam(':newCapacity', $capacity);
        $stmt->bindParam(':idRoom', $id);
        $stmt->execute();
    }

    if($_POST['roomDelete']){
        foreach ($_POST['roomDelete'] as $marked) {
            $stmt = $conn->prepare("DELETE FROM Room WHERE idRoom =:roomId ");
            $stmt->bindParam(':roomId', $marked);
        }
    }
}
header('Location: http://localhost/editHotel.php?hotelId='. $_POST["hotelId"]);
exit;

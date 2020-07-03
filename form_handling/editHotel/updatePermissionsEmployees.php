<?php
include '../../config.php';
$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);
if (!empty($_POST) && $_POST['action'] == 'editEmployees') {
    foreach ($_POST['ids'] as $id) {
        $stmt = $conn->prepare("SELECT count(*) FROM Hotel_administrators WHERE administrator_userId = :userId");
        $stmt->bindParam(':userId', $id);
        $stmt->execute();
        $count = $stmt->fetch();

        if ($count == 0 || $id == 1)
            continue;
        foreach ($_POST['userAdmin'] as $adminId) {
            if ($id == $adminId) {
                $stmt = $conn->prepare("INSERT IGNORE INTO Hotel_administrators(administrator_hotelId, administrator_userId)
                                                    VALUES (:hotelId, :userId)");
                $stmt->bindParam(':hotelId', $_POST['hotelId']);
                $stmt->bindParam(':userId', $id);
                $stmt->execute();
                break;
            } else {
                $stmt = $conn->prepare("DELETE FROM Hotel_administrators WHERE administrator_userId=:userId AND administrator_hotelId=:hotelId");
                $stmt->bindParam(':hotelId', $_POST['hotelId']);
                $stmt->bindParam(':userId', $id);
                $stmt->execute();
            }
        }
    }
    if($_POST['userDelete']) {
        $markedForDeletion = $_POST['userDelete'];
        foreach ($markedForDeletion as $marked) {
            $stmt = $conn->prepare("DELETE FROM Hotel_employees WHERE User_id=:userId AND Hotel_id = :hotelId");
            $stmt->bindParam(':userId', $marked);
            $stmt->bindParam(':hotelId', $_POST['hotelId']);
            $stmt->execute();
        }
    }
}

header('Location: http://localhost/editHotel.php?hotelId=' . $_POST['hotelId']);
exit;

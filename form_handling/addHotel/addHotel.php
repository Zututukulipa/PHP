<?php
include "../../config.php";
if(!empty($_POST)) {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);

    //HOTEL ADDING
    $stmt = $conn->prepare("INSERT INTO Address(street, houseNr, city, zip) VALUES (:street, :houseNr, :city, :zip)");
    $stmt->bindParam(':street', $_POST['hotelStreet']);
    $stmt->bindParam(':houseNr', $_POST['hotelStreetNr']);
    $stmt->bindParam(':city', $_POST['hotelCity']);
    $stmt->bindParam(':zip', $_POST['hotelZIP']);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO Hotel(hotelName, Address_idAddress, groundPlanPath) VALUES (:hotelName,
        (SELECT idAddress FROM Address WHERE street=:street AND houseNr=:houseNr AND city=:city AND zip=:zip), :path)");
    $stmt->bindParam(':hotelName', $_POST['hotelName']);
    $stmt->bindParam(':street', $_POST['hotelStreet']);
    $stmt->bindParam(':houseNr', $_POST['hotelStreetNr']);
    $stmt->bindParam(':city', $_POST['hotelCity']);
    $stmt->bindParam(':zip', $_POST['hotelZIP']);
    $stmt->bindParam(':path', $_POST['fileToUpload']);
    $stmt->execute();

    $stmt = $conn->prepare("SELECT idHotel FROM Hotel WHERE `hotelName`=:hotelName AND `Address_idAddress`=
                                            (SELECT `idAddress` FROM Address WHERE `street`=:street AND
                                                    `houseNr`=:houseNr AND `city`=:city AND `zip`=:zip)");
    $stmt->bindParam(':hotelName', $_POST['hotelName']);
    $stmt->bindParam(':street', $_POST['hotelStreet']);
    $stmt->bindParam(':houseNr', $_POST['hotelStreetNr']);
    $stmt->bindParam(':city', $_POST['hotelCity']);
    $stmt->bindParam(':zip', $_POST['hotelZIP']);
    $stmt->execute();
    $fetchedId = $stmt->fetch();

    $hotelId = $fetchedId[0];
    $role = 2;

    //USER ADDING
    if ($_SESSION['roleId'] == 1) {
        $role = 2;
        if ($_POST["name"]) {
            $stmt = $conn->prepare("INSERT INTO Address(street, houseNr, city, zip) VALUES (:street, :houseNr, :city, :zip)");
            $stmt->bindParam(':street', $_POST['hotelStreet']);
            $stmt->bindParam(':houseNr', $_POST['hotelStreetNr']);
            $stmt->bindParam(':city', $_POST['hotelCity']);
            $stmt->bindParam(':zip', $_POST['hotelZIP']);
            $stmt->execute();

            $pass = md5($_POST['pw']);
            $stmt = $conn->prepare("INSERT INTO
                            `User`(`firstName`,`surname`,`birth`,`email`,`password`,`Role_idRole`, `Address_idAddress`)
                            VALUES (:fName,:surname,:birth,:email, :password, :roleId,
                            (SELECT `idAddress` FROM `Address` WHERE `city` = :city AND
                            `houseNr` = :houseNr AND `street` = :street AND `zip` = :zip))");
            $stmt->bindParam(':fName', $_POST['name']);
            $stmt->bindParam(':surname', $_POST['surname']);
            $stmt->bindParam(':birth', $_POST['birth']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':password', $pass);
            $stmt->bindParam(':roleId', $role);
            $stmt->bindParam(':street', $_POST['street']);
            $stmt->bindParam(':houseNr', $_POST['houseNr']);
            $stmt->bindParam(':city', $_POST['city']);
            $stmt->bindParam(':zip', $_POST['zip']);
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO Hotel_administrators(administrator_hotelId, administrator_userId) VALUES (:idHotel, 1)");
            $stmt->bindParam(':idHotel', $hotelId);
            $stmt->execute();

            $stmt = $conn->prepare("UPDATE User SET Role_idRole=:roleId WHERE email=:email");
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':roleId', $role);
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO Hotel_administrators(administrator_hotelId, administrator_userId)
                                                    VALUES (:idHotel, (SELECT idUser FROM User WHERE email=:email))");
            $stmt->bindParam(':idHotel', $hotelId);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->execute();
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO Hotel_administrators(administrator_hotelId, administrator_userId) VALUES (:idHotel, 1)");
        $stmt->bindParam(':idHotel', $hotelId);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO Hotel_administrators(administrator_hotelId, administrator_userId) VALUES (:idHotel, :idUser)");
        $stmt->bindParam(':idHotel', $hotelId);
        $stmt->bindParam(':idUser', $_SESSION['user_id']);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE User SET Role_idRole=:roleId WHERE idUser=:idUser");
        $stmt->bindParam(':idUser', $_SESSION['user_id']);
        $stmt->bindParam(':roleId', $role);
        $stmt->execute();

        $_SESSION['roleId'] = $role;
    }

    //ADDING ROOMS
    if ($_POST['names']) {
        $names = $_POST['names'];
        $prices = $_POST['prices'];
        $capacities = $_POST['capacities'];
        for ($i = 0; $i <= count($_POST['names']); ++$i){
            $name = $names[$i];
            $price = $prices[$i];
            $capacity = $capacities[$i];
            $stmt = $conn->prepare("INSERT INTO Room(name, price, capacity, room_idHotel) VALUES (:name, :price, :capacity, :idHotel)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':capacity', $capacity);
            $stmt->bindParam(':idHotel', $hotelId);
            $stmt->execute();
        }
    }
}

$target_dir = "../../uploads/";
if(!empty($_FILES["fileToUpload"]["name"])) {
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
    }
    if (file_exists($target_file)) {
        $uploadOk = 2;
    }
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        $uploadOk = 0;
    }
    if ($uploadOk != 0) {
        {
            move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
        }
        if ($uploadOk > 0) {
            echo $target_file;
            $stmt = $conn->prepare("UPDATE Hotel SET groundPlanPath=:groundPlanPath WHERE idHotel=:hotelId");
            $stmt->bindParam(':groundPlanPath', $target_file);
            $stmt->bindParam(':hotelId', $hotelId);
            $stmt->execute();
        }
    }
}
header('Location: http://localhost/hotelAdministration.php');


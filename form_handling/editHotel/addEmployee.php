<?php
ob_start();
session_start();
include '../../config.php';
$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);
if (!empty($_POST)) {
    if(!empty($_POST['email'])){
    $stmt = $conn->prepare("SELECT count(*) FROM User WHERE email =:email");
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->execute();
    $userCount = $stmt->fetch();

    if ($userCount > 0) {
        $_SESSION['Err'] = 1;
//        header('Location: http://localhost/editHotel.php?hotelId=' . $_POST['hotelId']);
    }
    } if($userCount[0] < 1 || empty($userCount)) {
        echo "ding";
        print_r($_POST);
        $role = 3;
        if (isset($_POST["userId"]) && !empty($_POST["userId"])) {
            echo "ding";
            $stmt = $conn->prepare("INSERT INTO Hotel_employees(Hotel_id, User_id) VALUES (:idHotel, :idUser)");
            $stmt->bindParam(':idHotel', $_POST['hotelId']);
            $stmt->bindParam(':idUser', $_POST['userId']);
            $stmt->execute();

            $stmt = $conn->prepare("UPDATE User SET Role_idRole = :roleId WHERE idUser=:userId");
            $stmt->bindParam(':userId', $_POST['userId']);
            $stmt->bindParam(':roleId', $role);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT IGNORE INTO Address(street, houseNr, city, zip) 
                                            VALUES (:street, :houseNr, :city, :zip)");
            $stmt->bindParam(':street', $_POST['street']);
            $stmt->bindParam(':houseNr', $_POST['houseNr']);
            $stmt->bindParam(':city', $_POST['city']);
            $stmt->bindParam(':zip', $_POST['zip']);
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
            $stmt = $conn->prepare("INSERT INTO Hotel_employees(Hotel_id, User_id) 
                                        VALUES (:hotelId, (SELECT idUser FROM User WHERE email = :email));");
            $stmt->bindParam(':hotelId', $_POST['hotelId']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->execute();
        }
    }
}
//    header('Location: http://localhost/editHotel.php?hotelId=' . $_POST['hotelId']);
    exit;

<?php
ob_start();
session_start();
include '../../config.php';
$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);
    if (!empty($_POST)) {
        $role = 4;

        $stmt = $conn->prepare("SELECT count(*) FROM User WHERE email =:email");
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->execute();
        $userCount = $stmt->fetch();

        if ($userCount == 0) {
            $stmt = $conn->prepare("INSERT IGNORE INTO Address(street, houseNr, city, zip) 
                                            VALUES (:street, :houseNr, :city, :zip)");
            $stmt->bindParam(':street', $_POST['street']);
            $stmt->bindParam(':houseNr', $_POST['houseNr']);
            $stmt->bindParam(':city', $_POST['city']);
            $stmt->bindParam(':zip', $_POST['zip']);
            $stmt->execute();
            $pass = md5($_POST['pw']);
            $stmt = $conn->prepare("INSERT IGNORE INTO 
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
            header('Location: http://localhost/login.php');
            exit;
        } else {
            $_SESSION['Err'] = 1;
            header('Location: http://localhost/register.php');
        }
    }


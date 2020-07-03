<?php
include '../../config.php';
$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);
if (!empty($_POST)) {
    $target_dir = "../../uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    if(!empty($_FILES["fileToUpload"]["name"])) {
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
    }
    if ($uploadOk != 0) {
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
        $stmt = $conn->prepare("UPDATE Hotel SET hotelName=:hotelName WHERE idHotel=:hotelId");
        $stmt->bindParam(':hotelName', $_POST['hotelName']);
        $stmt->bindParam(':hotelId', $_POST['hotelId']);
        $stmt->execute();
        $stmt = $conn->prepare("UPDATE Address SET street=:hotelStreet, houseNr=:hotelHouseNr, city=:hotelCity,
                                            zip=:hotelZIP WHERE idAddress=:hotelIdAddress");
        $stmt->bindParam(':hotelStreet', $_POST['hotelStreet']);
        $stmt->bindParam(':hotelHouseNr', $_POST['hotelStreetNr']);
        $stmt->bindParam(':hotelCity', $_POST['hotelCity']);
        $stmt->bindParam(':hotelZIP', $_POST['hotelZIP']);
        $stmt->bindParam(':hotelIdAddress', $_POST['idAddr']);
        $stmt->execute();
    }
    if ($uploadOk > 0 && !empty($_FILES["fileToUpload"]["name"])) {
        $stmt = $conn->prepare("UPDATE Hotel SET groundPlanPath=:groundPlanPath WHERE idHotel=:hotelId");
        $stmt->bindParam(':groundPlanPath', $target_file);
        $stmt->bindParam(':hotelId', $_POST['hotelId']);
        $stmt->execute();
    }

}

header('Location: http://localhost/editHotel.php?hotelId='.$_POST['hotelId']);

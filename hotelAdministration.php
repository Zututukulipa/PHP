<?php
ob_start();
session_start();
include "config.php";

$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);
$stmt = $conn->prepare("SELECT * FROM Hotel h JOIN Hotel_administrators a WHERE h.idHotel = a.administrator_hotelId AND a.administrator_userId = :uid");
$stmt->bindParam(':uid', $_SESSION["user_id"]);
$stmt->execute();
$hotels = $stmt->fetchAll(PDO::FETCH_OBJ);

$stmt = $conn->prepare("SELECT * FROM Room");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_OBJ);

$stmt = $conn->prepare("SELECT * FROM User u JOIN Hotel_employees He WHERE He.User_id = u.idUser");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Hotel Administration</title>

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <link type="text/css" rel="stylesheet" href="styles.css"/>
</head>
<body class="mainWindow">
<?php
include 'modules/navBar.php';
echo '<div class="booking-form">';
foreach ($hotels as $hotel) {
    echo '<div class="form-header"><h1>' . $hotel->hotelName . '</div></h1>' .
        '<div class="row">' .
        '<div class="form-group column">' .
        '<table class="container" id="upcoming">'
        . "<tr>" . "<th>Rooms</th>" . "<th>Staff</th>" . "<th>Address</th>" . "</tr>"
        . "<tr>" . "<td>" .
        '<table class="container" id="upcoming">'
        . "<tr>" .
        "<th>Name</th>" . "<th>Price</th>" . "<th>Capacity</th>"
        . "</tr>";
    foreach ($rooms as $room) {
        if ($room->room_idHotel == $hotel->idHotel)
            echo "<tr><td>" . $room->name . "</td><td>" . $room->price . "</td><td>" . $room->capacity . "</td></tr>";
    }
    echo "</table>"
        . "</td>"
        . "<td>"
        . '<table class="container" id="upcoming">'
        . "<tr>"
        . "<th>First Name</th>"
        . "<th>Last Name</th>"
        . "<th>Email</th>"
        . "<th>Birth Date</th>"
        . "</tr>";
    foreach ($users as $user) {
        if ($user->Hotel_id == $hotel->idHotel)
            echo "<tr><td>" . $user->firstName . "</td><td>" . $user->surname . "</td><td>" . $user->email . "</td><td>" . $user->birth . "</td></tr>";
    }
    echo "</table>"
        . "</td>"
        . "<td>"
        . '<table class="container" id="upcoming">'
        . "<tr>"
        . "<th>Street</th>"
        . "<th>#</th>"
        . "<th>City</th>"
        . "<th>ZIP</th>"
        . "</tr>";
    $stmt = $conn->prepare("SELECT * FROM Address WHERE idAddress = :Address_idAddress");
    $stmt->bindParam(':Address_idAddress', $hotel->Address_idAddress);
    $stmt->execute();
    $address = $stmt->fetch(PDO::FETCH_OBJ);
    echo "<tr><td>" . $address->street . "</td><td>" . $address->houseNr . "</td><td>" . $address->city . "</td><td>" . $address->zip . "</td></tr>"
        . "</table>"
        . "</td>"
        . "</tr>"
        . "</table>"
        . '</div>'
        . '<div class="form-group column-wider">'
        . '<div id="imageHolder" style="alignment: right">'
        . '<img src="' . $hotel->groundPlanPath . '" width="150px" alt="" id="hotelPic">'
        . '</div>'
        . '</div>'
        . '</div>' . '<button class="submit-btn" type="button" style="float: right; margin-right: 3%" onclick="editHotel(' . $hotel->idHotel . ')"> Edit </button>';

}
echo '</div>';


?>
<script>
    function editHotel(i) {
        window.open("/editHotel.php?hotelId=" + i);
    }
</script>
</body>
</html>

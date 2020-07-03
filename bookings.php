<?php
ob_start();
session_start();
include "config.php";

if (empty($_SESSION["roleId"]) || $_SESSION["roleId"] == null || $_SESSION["roleId"] == "")
    header("Location: login.php");
$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);

if (strval($_SESSION["roleId"]) == 4) {
    $stmt = $conn->prepare("SELECT A.idReservation, A.Room_idRoom, A.checkIn, A.checkOut, B.name, A.total, H.hotelName, H.idHotel FROM Reservation A
    JOIN Room B ON A.Room_idRoom = B.idRoom 
    JOIN Hotel H on B.room_idHotel = H.idHotel
    WHERE A.Guest_idGuest = :userId ORDER BY H.idHotel ASC,  A.checkIn DESC");
    $stmt->bindParam(':userId', $_SESSION["user_id"]);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_OBJ);

} else if (strval($_SESSION["roleId"]) == 1) {
    $stmt = $conn->prepare("SELECT A.idReservation, A.Room_idRoom, A.checkIn, A.checkOut, B.name, A.total, H.hotelName, H.idHotel FROM Reservation A
    JOIN Room B ON A.Room_idRoom = B.idRoom
    JOIN Hotel H on B.room_idHotel = H.idHotel
    ORDER BY H.idHotel ASC, A.checkIn DESC");
    $stmt->bindParam(':userId', $_SESSION["user_id"]);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_OBJ);
} else if (strval($_SESSION["roleId"]) < 4) {
    $stmt = $conn->prepare("SELECT DISTINCT A.idReservation, A.Room_idRoom, A.checkIn, A.checkOut, B.name, A.total, H.hotelName, H.idHotel FROM Reservation A
    JOIN Room B ON A.Room_idRoom = B.idRoom
    JOIN Hotel H on B.room_idHotel = H.idHotel
    LEFT JOIN Hotel_employees He on H.idHotel = He.Hotel_id
    LEFT JOIN Hotel_administrators Ha on H.idHotel = Ha.administrator_hotelId 
    WHERE Hotel_employeesId=:userId OR administrator_userId=:userId ORDER BY H.idHotel ASC, A.checkIn DESC");
    $stmt->bindParam(':userId', $_SESSION["user_id"]);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_OBJ);
}
$stmt = $conn->prepare("SELECT * from Hotel");
$stmt->execute();
$hotels = $stmt->fetchAll(PDO::FETCH_OBJ);
$hotelNames = [];
?>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Booking</title>

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <link type="text/css" rel="stylesheet" href="styles.css"/>
</head>

<body class="mainWindow">
<?php
include 'modules/navBar.php'
?>
<section>
    <form method="post" id="current" class="booking-form" action="form_handling/bookings/deleteBooking.php">
        <div class="form-header"><h1>Upcoming</h1></div>
        <table class="container" id="upcoming">
            <tr>
                <th>Hotel</th>
                <th>Room</th>
                <th>Dates</th>
                <th>Price</th>
                <th>Options</th>
            </tr>
            <?php
            $now = new DateTime();
            foreach ($bookings as $value) {
                $hotelNames[$value->idHotel] = $value->hotelName;
                try {
                    $startDate = new DateTime($value->checkIn);
                    $endDate = new DateTime($value->checkOut);
                } catch (Exception $e) {
                    echo $value->checkIn;
                    echo $value->checkOut;
                }
                if ($startDate > $now) {
                    echo "<tr>";
                    echo "<td>$value->hotelName</td>";
                    echo "<td>$value->name</td>";
                    echo "<td>$value->checkIn" . " - " . $value->checkOut . "</td>";
                    echo "<td>$value->total</td>";
                    echo "<td><button type='submit' name='deleteItem' value='$value->idReservation'>Delete</button></td>";
                    echo "</tr>";
                } else
                    if (strval($_SESSION["roleId"]) < 3)
                        continue;
                    else
                        break;
            }
            ?>
        </table>
    </form>
</section>
<section>
    <div class="booking-form">
        <div class="form-header"><h1>Current and History</h1></div>
        <table class="container">
            <tr>
                <th>Hotel</th>
                <th>Room</th>
                <th>Dates</th>
                <th>Price</th>
                <th>Options</th>
            </tr>

            <?php
            $now = new DateTime();
            foreach ($bookings as $value) {
                try {
                    $startDate = new DateTime($value->checkIn);
                    $endDate = new DateTime($value->checkOut);
                } catch (Exception $e) {
                    echo $value->checkIn;
                    echo $value->checkOut;
                }
                if ($startDate <= $now) {
                    echo "<tr>";
                    echo "<td>$value->hotelName</td>";
                    echo "<td>$value->name</td>";
                    echo "<td>$value->checkIn" . " - " . $value->checkOut . "</td>";
                    echo "<td>$value->total</td>";

                    if ($endDate < $now && $startDate < $now) {
                        echo "<td><button class='submit-btn' id='options' type='button' disabled>History</button></td>";
                    } else
                        echo "<td><button class='submit-btn' id='options' type='button' disabled>Current</button></td>";
                    echo "</tr>";
                }
            }
            ?>

        </table>
    </div>
</section>
<section>
    <?php
    if ($_SESSION['roleId'] < 4)
        include "modules/exportImport.php";
    ?>
</section>
</body>

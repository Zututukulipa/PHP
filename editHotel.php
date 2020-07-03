<?php
ob_start();
session_start();
include "config.php";
$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);

$stmt = $conn->prepare("SELECT * FROM Hotel_administrators WHERE administrator_hotelId=:idHotel AND administrator_userId=:idUser");
$stmt->bindParam(':idHotel', $_GET['hotelId']);
$stmt->bindParam(':idUser', $_SESSION['user_id']);
$stmt->execute();
$permissions = $stmt->fetch(PDO::FETCH_OBJ);

$granted = false;
if (empty($permissions))
    die("You have no permission to access this page");

foreach ($permissions as $permission) {
    if ($permission == $_GET['hotelId']) {
        $granted = true;
    }
}

if (!$granted)
    die("You have no permission to access this page");

$stmt = $conn->prepare("SELECT * FROM Hotel h JOIN Address a WHERE idHotel = :idHotel AND idAddress = h.Address_idAddress");
$stmt->bindParam(':idHotel', $_GET['hotelId']);
$stmt->execute();
$hotel = $stmt->fetch(PDO::FETCH_OBJ);

$stmt = $conn->prepare("SELECT DISTINCT * FROM User u LEFT JOIN Hotel_administrators a ON u.idUser = a.administrator_userId LEFT JOIN Hotel_employees e ON e.User_id = u.idUser WHERE a.administrator_hotelId = :idHotel OR e.Hotel_id = :idHotel;");
$stmt->bindParam(':idHotel', $_GET['hotelId']);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_OBJ);

$stmt = $conn->prepare("SELECT * FROM Room WHERE room_idHotel=:idHotel");
$stmt->bindParam(':idHotel', $_GET['hotelId']);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_OBJ);

?>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Hotel</title>

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <link type="text/css" rel="stylesheet" href="styles.css"/>
    <script type="text/javascript" src="js/formValidations.js"></script>
</head>

<body class="mainWindow">

<?php include "modules/navBar.php";
echo '<form method = "post" action="form_handling/editHotel/deleteHotel.php" name="deletion">
    <input type="text" name="hotelId" value="' . $_GET['hotelId'] . '" style="display: none"/>
    <button type="submit" class="add-btn" style="float: right">X</button>
    </form>';
?>
<div class="booking-form">
    <section>
    <div class="form-header"><h1>Info</h1></div>
    <form action="form_handling/editHotel/updateHotel.php" method="post" name="EditHotel" enctype="multipart/form-data">
        <?php include 'modules/hotelInfoForm.php' ?>
        <input type="submit" class="submit-btn"/>
    </form>
    <div class="form-header"><h1>Rooms</h1></div>
    <?php
    if (!empty($rooms)) {
        echo '<form method = "post" action="form_handling/editHotel/editRooms.php">';
        echo '<table >';
        echo '<tr>';
        echo '<th>Name</th >';
        echo '<th>Price</th >';
        echo '<th>Capacity</th >';
        echo '<th>Delete?</th >';
        echo '</tr >';
        foreach ($rooms as $room) {
            echo '<tr><td><input type="text" value="' . $room->name . '" name="names[]" required/></td>
               <td><input type="text" value="' . $room->price . '" name="prices[]" required/></td>
               <td><input type="text" value="' . $room->capacity . '" name="capacities[]" required/></td>
               <td><input type="checkbox" value="' . $room->idRoom . '" name="roomDelete[]"/></td>
               <td><input type="number" value="' . $room->idRoom . '" name="ids[]" required hidden/></td></tr>';
        }
    }
    echo '</table>';
    echo '<input type="number" value="' . $room->idRoom . '" name="action" hidden/>';
    echo '<input type="text" value="editRoom" name="action" hidden/>';
    echo '<input type="number" value="' . $_GET['hotelId'] . '" name="hotelId" hidden/>';
    echo '<input type="submit" class="submit-btn"/>';
    echo '</form>';
    ?>
    </section>
    <section>
    <div class="form-header"><h1>Add Room</h1></div>
    <form method="post" action="form_handling/editHotel/addRoom.php">
        <input type="text" name="roomName" placeholder="Name" required/>
        <input type="number" name="roomPrice" placeholder="Price" required/>
        <input type="number" name="roomCapacity" placeholder="Capacity" required/>
        <?php echo '<input type="number" value="' . $_GET['hotelId'] . '" name="hotelId" hidden/>'; ?>
        <input type="text" name="action" value="addRoom" hidden/>
        <input type="submit" class="submit-btn"/>
    </form>
    <?php
    if (!empty($employees)) {
        echo '<h1 > Employees</h1 >';
        echo '<form action="form_handling/editHotel/updatePermissionsEmployees.php" method="post">';
        echo '<table>';
        echo '<tr >';
        echo '<th > Name</th >';
        echo '<th > Email</th >';
        echo '<th > Administrator</th >';
        echo '<th > Delete ?</t>';
        echo '</tr >';
        foreach ($employees as $employee) {
            echo '<tr><td style="display: none"><input type="text" name="ids[]" value="'. $employee->idUser .'"/></td><td><input type="text" value="' . $employee->firstName . ' ' . $employee->surname . '" disabled>' . '</td><td><input type="text" value="' . $employee->email . '"></td><td><input type="checkbox" name="userAdmin[]' . $employee->idUser . '" value="' . $employee->idUser . '"';
            if($employee->administratorId)
                echo 'checked';
            echo '/></td><td><input type="checkbox" name="userDelete[]' . $employee->idUser . '" value="' . $employee->idUser . '"/></td></tr>';
        }
        echo '</table>';
        echo '<input type="text" value="editEmployees" name="action" hidden/>';
        echo '<input type="number" value="' . $_GET['hotelId'] . '" name="hotelId" hidden/>';
        echo '<input type="submit" class="submit-btn"/>';
        echo '</form>';
    }
    ?>
    </section>
    <section>
    <div class="form-header"><h1>Add Employee</h1></div>
    <?php include 'modules/addEmployeeForm.php' ?>
    </section>
</div>
</body>
</html>

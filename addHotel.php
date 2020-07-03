<?php
ob_start();
session_start();
include "config.php";

?>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Booking</title>

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

    <link type="text/css" rel="stylesheet" href="css/style.css"/>
</head>

<body class="mainWindow">
<?php
if ($_SESSION["roleId"] > 4 || empty($_SESSION))
    include 'login.php';
else {
    include 'modules/navBar.php';
    include 'modules/addHotelBase.php';
}
?>
</body>
</html>

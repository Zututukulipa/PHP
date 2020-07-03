<?php
ob_start();
session_start();
include "modules/navBar.php";
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login</title>

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

    <link type="text/css" rel="stylesheet" href="css/style.css"/>
</head>
<body>
<form action="form_handling/register/addUser.php" method="post" class="booking-form">
    <section>
    <div class="form-header">
        <h1>Room Booking</h1>
    </div>
    </section>
    <section>
<?php
include "modules/userAddForm.php";
?>
    </section>
    <input type="submit" class="submit-btn" value="Register">

</form>
<?php
if($_SESSION['Err'] = 1) {
    echo "<b>Email is already in use</b>";
    $_SESSION['Err'] = 0;
}
?>
</body>
</html>
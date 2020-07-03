<?php
include "config.php";
session_start();
if (!empty($_POST) && !empty($_POST["username"]) && !empty($_POST["loginPassword"])) {
    $password = md5($_POST["loginPassword"]);
    $uName = strtolower($_POST["username"]);
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    );
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);

    $stmt = $conn->prepare("SELECT A.email, A.firstname, A.surname, A.Role_idRole, A.idUser, B.name, C.street, C.houseNr, C.zip, C.city FROM User A 
                                     JOIN Role B ON A.Role_idRole = B.idRole
                                     JOIN Address C on A.Address_idAddress = C.idAddress 
                                     WHERE A.email = :email and A.password = :password");
    $stmt->bindParam(':email', $uName);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION["user_id"] = $user["idUser"];
        $_SESSION["firstname"] = $user["firstname"];
        $_SESSION["lastname"] = $user["surname"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["roleId"] = $user["Role_idRole"];
        $_SESSION["street"] = $user["street"];
        $_SESSION["houseNr"] = $user["houseNr"];
        $_SESSION["zip"] = $user["zip"];
        $_SESSION["city"] = $user["city"];
        $_SESSION["roleName"] = $user["name"];

        $stmt = $conn->prepare("SELECT administrator_hotelId FROM Hotel_administrators WHERE administrator_userId=:uid");
        $stmt->bindParam(":uid", $user["idUser"]);
        $stmt->execute();
        $_SESSION["adminOf"] = $stmt->fetchAll();

        header("Location: index.php");
    }

}

?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login</title>

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

    <link type="text/css" rel="stylesheet" href="css/style.css"/>
</head>
<body class="mainWindow">
<?php
include 'modules/navBar.php'
?>

<form method="post" id="regForm" class="booking-form">
    <div class="booking-form">
        <div class="form-header"><h1>Login</h1></div>
        <div class="form-group">
            <input type="text" name="username" id="username" placeholder="Email" required>
        </div>
        <div class="form-group">
            <input type="password" name="loginPassword" placeholder="Enter your password" required>
        </div>
        <input class="submit-btn" type="button" value="Log In" onclick="validateForm()">
    </div>

</form>
<div><a href="register.php"><i>Create new user?</i></a></div>


<script>
    function validateForm() {
        let y, i;
        y = document.getElementsByTagName("input");
        let valid = true;
        for (i = 0; i < y.length; i++) {
            if (y[i].value === "") {
                y[i].className = "invalid";
                y[i].value = "";
                valid = false;
            }
        }
        if (valid)
            document.getElementById("regForm").submit();
    }
</script>
</body>
</html>

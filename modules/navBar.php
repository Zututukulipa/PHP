<?php
echo '<div class="topnav" id="topNav">';
echo '<a class="active" href="/index.php">Booking</a>';
if (empty($_SESSION["roleId"]) || $_SESSION["roleId"] == null || $_SESSION["roleId"] == "") {
    echo '<a href="/login.php">Login</a>';
} else {
    if ($_SESSION["roleId"] < 4)
        echo '<a href="/bookings.php">Booking Administration</a>';
    else if ($_SESSION["roleId"] == 4)
        echo '<a href="/bookings.php">My Bookings</a>';
    if ($_SESSION["adminOf"])
        echo '<a href="/hotelAdministration.php">Hotel Administration</a>';
    echo '<a href="/logout.php" style="float: right">Logout</a>';
}
echo '<a href="/addHotel.php" style="float: right ">Add Your Hotel</a>';
echo '</div>';

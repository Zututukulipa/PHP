<div class="booking-form">
    <div class="form-header"><h1>Hotel Owner</h1></div>
    <form action="/form_handling/addHotel/addHotel.php" method="post" enctype="multipart/form-data">
        <?php
        if ($_SESSION['roleId'] == 1) {
            include 'userAddForm.php';
        } else {
            echo '<table>';
            echo '<tr><td>Name:</td><td>' . $_SESSION["firstname"] . '</td><td>' . $_SESSION["lastname"] . '</td></tr>';
            echo '<tr><td>Email:</td><td>' . $_SESSION["email"] . '</td></tr>';
            echo '<tr><td>Street:</td><td>' . $_SESSION["street"] . '<td>' . $_SESSION["houseNr"] . '</td>' . '</td></tr>';
            echo '<tr><td>City:</td><td>' . $_SESSION["zip"] . '</td><td>' . $_SESSION["city"] . '</td></tr>';
            echo '</table>';
        }
        ?>
        <div class="form-header"><h1>Hotel Info</h1></div>
        <div>
            <?php
            include 'hotelInfoForm.php';
            ?>
            <h1>Rooms</h1>
            <button type="button" onclick="add_fields()" class="add-btn">Add Room</button>
            <table id="roomTable">
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Capacity</th>
                </tr>
            </table>
        </div>
        <div class="row"><input type="submit" class="submit-btn"></div>
    </form>
</div>
<script>
    function add_fields() {
        document.getElementById("roomTable").insertRow(-1).innerHTML = '<td><input type="text" name="names[]" /></td>' +
            '<td><input type="text" name="prices[]" /></td>' +
            '<td><input type="text" name="capacities[]" /></td></tr>';
    }
</script>

<?php

?>
<?php
ob_start();
session_start();
include "config.php";
$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
);

$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, $options);

$stmt = $conn->prepare("SELECT * FROM Hotel");
$stmt->execute();
$hotelsInfo = $stmt->fetchAll(PDO::FETCH_OBJ);

$stmt = $conn->prepare("SELECT checkIn, checkOut, Room_idRoom FROM Reservation;");
$stmt->execute();
$resultSet = $stmt->fetchAll(PDO::FETCH_OBJ);

$stmt = $conn->prepare("SELECT * FROM Room");
$stmt->execute();
$fetchedRooms = $stmt->fetchAll(PDO::FETCH_OBJ);

if (!empty($_POST) && !empty($_POST["hotel"]) && !empty($_POST["room"]) && !empty($_POST["daterange"]) && !empty($_POST["email"]) && !empty($_POST["firstName"]) && !empty($_POST["lastName"]) && !empty($_POST["street"])
    && !empty($_POST["houseNr"]) && !empty($_POST["zip"]) && !empty($_POST["city"])) {

    $str_arr = explode("-", $_POST['daterange']);
    $to = strtotime($str_arr[1]);
    $toDate = date("Y-m-d", $to);
    $from = strtotime($str_arr[0]);
    $fromDate = date("Y-m-d", $from);
    $dateDifference = $to - $from;
    $daysBetween = ($dateDifference / (60 * 60 * 24));
    $totalPrice = $daysBetween * doubleval($fetchedRooms[(int)$_POST['room']]->price);

    $street = $_POST["street"];
    $houseNr = $_POST["houseNr"];
    $city = $_POST["city"];
    $zip = $_POST["zip"];
    $stmt = $conn->prepare("INSERT IGNORE INTO `Address`(`street`,`houseNr`,`city`,`zip`) VALUES ('$street', '$houseNr', '$city', '$zip')");
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':houseNr', $houseNr);
    $stmt->bindParam(':street', $street);
    $stmt->bindParam(':zip', $zip);


    $stmt->execute();


    $name = $_POST['firstName'];
    $surname = $_POST['lastName'];
    $birth = $_POST['birth'];
    $email = strtolower($_POST['email']);
    $password = md5($_POST['password']);
    $role = 4;
    if (!empty($password)) {
        $stmt = $conn->prepare("INSERT IGNORE INTO `User`(`firstName`,`surname`,`birth`,`email`,`password`,`Role_idRole`, `Address_idAddress`) VALUES (:fName,:surname,:birth,:email, :password, :roleId, (SELECT `idAddress` FROM `Address` WHERE `city` = :city AND `houseNr` = :houseNr AND `street` = :street AND `zip` = :zip));");
        $stmt->bindParam(':fName', $name);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':birth', $birth);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':roleId', $role);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':houseNr', $houseNr);
        $stmt->bindParam(':street', $street);
        $stmt->bindParam(':zip', $zip);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT IGNORE INTO `User`(`firstName`,`surname`,`birth`,`email`,`Role_idRole`, `Address_idAddress`) VALUES (:fName,:surname,:birth,:email, :roleId, (SELECT `idAddress` FROM `Address` WHERE `city` = :city AND `houseNr` = :houseNr AND `street` = :street AND `zip` = :zip));");
        $stmt->bindParam(':fName', $name);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':birth', $birth);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':roleId', $role);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':houseNr', $houseNr);
        $stmt->bindParam(':street', $street);
        $stmt->bindParam(':zip', $zip);
        $stmt->execute();
    }
    $stmt = $conn->prepare("INSERT INTO Reservation(checkIn,checkOut,Room_idRoom, `length`, total ,Guest_idGuest)
                                VALUES
                                 (:checkIn,:checkOut,:roomId, :length, :total,
                                 (SELECT idUser FROM User WHERE email = :email))");
    $stmt->bindParam(':checkIn', $fromDate);
    $stmt->bindParam(':checkOut', $toDate);
    $stmt->bindParam(':roomId', $_POST['room']);
    $stmt->bindParam(':length', $daysBetween);
    $stmt->bindParam(':total', $totalPrice);
    $stmt->bindParam(':email', $email);

    $stmt->execute();

    $stmt = $conn->prepare("INSERT IGNORE INTO `Address`(`street`,`houseNr`,`city`,`zip`) VALUES ('$street', '$houseNr', '$city', '$zip')");
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':houseNr', $houseNr);
    $stmt->bindParam(':street', $street);
    $stmt->bindParam(':zip', $zip);
} else if (!empty($_POST) && $_SESSION["roleId"] < 5) {
    $str_arr = explode(" - ", $_POST['daterange']);
    $to = strtotime($str_arr[1]);
    $toDate = date("Y-m-d", $to);
    $from = strtotime($str_arr[0]);
    $fromDate = date("Y-m-d", $from);
    $dateDifference = $to - $from;
    $daysBetween = ($dateDifference / (60 * 60 * 24));
    $pricing = 0;
    foreach ($fetchedRooms as $fetchedRoom) {
        if ($fetchedRoom->idRoom == $_POST['room'])
            $pricing = $fetchedRoom->price;
    }
    $totalPrice = $daysBetween * $pricing;

    $stmt = $conn->prepare("INSERT INTO Reservation(checkIn,checkOut,Room_idRoom, `length`, total ,Guest_idGuest)
                                VALUES (:checkIn, :checkOut, :roomId, :length, :total, :userId)");
    $stmt->bindParam(':checkIn', $fromDate);
    $stmt->bindParam(':checkOut', $toDate);
    $stmt->bindParam(':roomId', $_POST['room']);
    $stmt->bindParam(':length', $daysBetween);
    $stmt->bindParam(':total', $totalPrice);
    $stmt->bindParam(':userId', $_SESSION['user_id']);

    $stmt->execute();
}
?>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Booking</title>

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="js/formValidations.js"></script>

</head>

<body class="mainWindow">
<?php
include 'modules/navBar.php'
?>
<div id="booking">
    <form name="booking" class="booking-form" method="post">
            <div class="form-header">
                <h1>Room Booking</h1>
            </div>
        <article>
            <section class="tab">
                <div class="booking-layout">
                    <div class="form-group">
                        <select class="form-control" name="hotel" id="hotelSelection"
                                onchange="changeImageAndRefresh()">
                            <?php
                            foreach ($hotelsInfo as $row) {
                                echo '<option value="' . $row->idHotel . '">';
                                echo $row->hotelName;
                                echo '</option>';
                            }
                            ?>
                        </select>
                        <span class="select-arrow"></span>
                        <span class="form-label">Select Hotel</span>
                    </div>
                        <div class="form-group">
                            <select class="form-control" name="room" id="roomSelection"
                                    onchange="refreshDates()">
                            </select>
                            <span class="select-arrow"></span>
                            <span class="form-label">Select Room</span>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="roomCapacity" id="roomCapacity" disabled
                            value="<?php echo $fetchedRooms[0]->capacity?>"/>
                            <span class="select-arrow"></span>
                            <span class="form-label">Capacity</span>
                        </div>
                        <div class="form-group">
                            <input class="form-control" type="text" id="nightlyPrice" disabled
                                   value="<?php echo $fetchedRooms[0]->price; ?>"/>
                            <span class="form-label">Price per night</span>
                        </div>

                    <time class="form-group column">
                        <input class="form-control" type="text" name="daterange" id="daterange"
                               onchange="pricingRefresh()" required
                               value="<?php echo date("m/d/Y"); ?>"/>
                        <span class="form-label">Dates</span>
                    </time>
                    <div class="form-group column-wider">
                        <input class="form-control" type="text" id="total" disabled
                               value="<?php echo $fetchedRooms[0]->price; ?>"/>
                        <span class="form-label">Total</span>
                    </div>
                </div>

                    <div class="booking-btns">
                        <?php
                        if (!isset($_SESSION["roleId"])) {
                            echo '<button class="submit-btn" id="nextBtn" type="button" onclick="nextPrevStep(1)" disabled> Check availability </button>';
                        } else {
                            echo '<button class="submit-btn" id="nextBtn" type="button" onclick="finishOrder()"> Book </button>';
                        }
                        ?>
                    </div>
            </section>

            <?php
            if (!isset($_SESSION['roleId'])) {
                echo '
            <section class="tab">
                <div>
                    <div class="row">
                        <div class="form-group column">
                            <input class="form-control" type="text" name="firstName" id="firstName" required
                                   oninput="this.className=\'\'"/>
                            <span class="form-label">First Name</span>
                        </div>

                        <div class="form-group column">
                            <input class="form-control" type="text" name="lastName" id="lastName" required
                                   oninput="this.className = \'\'"/>
                            <span class="form-label">Last Name</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group column">
                            <input class="form-control required" type="text" name="email" id="email" required
                                   oninput="this.className = \'\'"/>
                            <span class="form-label">Email</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group column">
                            <input class="form-control required" placeholder="YYYYMMDD" type="number" name="birth" id="birth" required
                                   oninput="this.className = \'\'" onfocusout="isBirthValid();"/>
                            <span class="form-label">Birth Date</span>
                        </div>
                    </div>
                    <div id="ifYes" style="display: none" class="row">
                        <div class="form-group">
                            <input class="form-control column" type="text" name="password" id="password" value=""/>
                            <span class="form-label">Password</span>
                        </div>
                    </div>
                    <b>register?</b>
                    <div class="row">
                    <label for="yesCheck">Yes</label>
                         <input class="form-checkbox" type="radio" onclick="yesnoCheck();" name="yesno"
                                   id="yesCheck">
                        No <input class="form-checkbox" type="radio" onclick="yesnoCheck();" name="yesno" id="noCheck"
                                  checked>
                    </div>
                    <div class="row">
                        <div>
                            <button class="submit-btn" id="prevBtn" type="button" onclick="nextPrevStep(-1)">Back
                            </button>
                        </div>
                        <div>
                            <button class="submit-btn" id="nextBtn" type="button" onclick="nextPrevStep(1)">Next
                            </button>
                        </div>
                    </div>
                </div>
            </section>
            <section class="tab">
                <div class="row">
                    <div class="form-group column">
                        <input class="form-control" type="text" name="street" id="street" required
                               oninput="this.className = \'required\'"/>
                        <span class="form-label">Street</span>
                    </div>
                    <div class="form-group column">
                        <input class="form-control" type="number" name="houseNr" id="houseNr" required
                               oninput="this.className = \'required\'"/>
                        <span class="form-label">House Nr.</span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group column">
                        <input class="form-control" type="number" name="zip" id="zip" required
                               oninput="this.className = \'required\'"/>
                        <span class="form-label">ZIP</span>
                    </div>

                    <div class="form-group column">
                        <input class="form-control" type="text" name="city" id="city" required
                               oninput="this.className = \'required\'"/>
                        <span class="form-label">City</span>
                    </div>
                </div>

                <div class="row">
                    <div>
                        <button class="submit-btn" id="prevBtn" type="button" onclick="nextPrev(-1)">Back</button>
                    </div>
                    <div>
                        <button class="submit-btn" type="button" onclick="finishOrder()">Finish</button>
                    </div>
                </div>
            </section>
            ';
            }
            echo '
                <section class="tab">
                <div class="row" id="summary">
                    <!-- GENERATED BY JS -->
                </div>
                <div class="row">
                    <div>
                        <button class="submit-btn" id="prevBtn" type="button" onclick="editOrderDetails()">Edit</button>
                    </div>
                    <div>
                        <button class="submit-btn" type="submit">Submit</button>
                    </div>
                </div>
            </section>
            ';
            ?>
            <div id="imageHolder" style="alignment: right">
                <img src="<?php echo $hotelsInfo[0]->groundPlanPath; ?>" width="512px" alt="" id="hotelPic">
            </div>
        </article>
        <div style="text-align:center;margin-top:40px;">
            <span class="step"></span>
            <span class="step"></span>
            <?php
            if (!$_SESSION['roleId']) {
                echo
                '<span class="step"></span>
                     <span class="step"></span>';
            }
            ?>

        </div>
    </form>
</div>

<script>
    let hotels = <?php echo json_encode($hotelsInfo); ?>;
    let rooms = <?php echo json_encode($resultSet); ?>;
    let beds = <?php echo json_encode($fetchedRooms); ?>;
    let str = JSON.stringify(hotels, null, 2);
    let str2 = JSON.stringify(beds, null, 2);
    let submittedDate;
    let submittedRoom;
    let submittedRoomId;
    let submittedHotel;
    let submittedHotelId;
    let submitted;
    <?php
    if (!empty($_SESSION)) {
        echo 'let submittedFname = "' . $_SESSION["firstname"] . '";';
        echo 'let submittedLname = "' . $_SESSION["lastname"] . '";';
        echo 'let submittedEmail = "' . $_SESSION["email"] . '";';
        echo 'let submittedBirthDate = "' . $_SESSION["birth"] . '";';
        echo 'let submittedStreet = "' . $_SESSION["street"] . '";';
        echo 'let submittedHouseNr = ' . intval($_SESSION["houseNr"]) . ';';
        echo 'let submittedZIP = ' . intval($_SESSION["zip"]) . ';';
        echo 'let submittedCity = "' . $_SESSION["city"] . '";';
    } else {
        echo '
    let submittedFname;
    let submittedLname;
    let submittedEmail;
    let submittedBirthDate;
    let submittedStreet;
    let submittedHouseNr;
    let submittedZIP;
    let submittedCity;
    let submittedPw;
    ';
    }
    ?>
    let page = 0;
    let image = document.getElementById("imageHolder");

    let data = <?php echo json_encode($fetchedRooms);?>;
    let str3 = JSON.stringify(data, null, 2);
    let dropdown = $('#roomSelection');

    dropdown.empty();
    dropdown.prop('selectedIndex', 0);

    $.each(data, function (key, entry) {
        let e = document.getElementById("hotelSelection");
        if (entry.room_idHotel == e.options[e.selectedIndex].value) {
            dropdown.append($('<option></option>').attr('value', entry.idRoom).text(entry.name));
        }
    });

    Date.prototype.addDays = function (days) {
        let dat = new Date(this.valueOf())
        dat.setDate(dat.getDate() + days);
        return dat;
    };

    function getDates(startDate, stopDate) {
        let dateArray = new Array();
        let currentDate = startDate;
        while (currentDate <= stopDate) {
            dateArray.push(currentDate);
            currentDate = currentDate.addDays(1);
        }
        return dateArray;
    }


    $('.form-control').each(function () {
        floatedLabel($(this));
    });

    //https://stackoverflow.com/questions/9493760/how-initialize-dropdown-select-with-preselected-value-and-then-change-it
    $('#roomSelection').change(function () {
        pricingRefresh();
    });

    let opt_sel = $('#selectId option:selected');
    opt_sel.val(99);
    opt_sel.text('Changed option');

    $('.form-control').on('input', function () {
        floatedLabel($(this));
    });

    function floatedLabel(input) {
        let $field = input.closest('.form-group');
        if (input.val()) {
            $field.addClass('input-not-empty');
        } else {
            $field.removeClass('input-not-empty');
        }
    }

    let images = [];
    for (i in hotels) {
        let im = new Image();
        im.src = hotels[i].groundPlanPath;
        images.push(im);
    }
    let e = document.getElementById("hotelSelection");
    let strUser = images[e.selectedIndex].src;
    let reservations = {room: []};
    for (i = 0; i < beds.length; ++i) {
        reservations.room[i] = {};
        reservations.room[i].roomId = beds[i].idRoom;
        reservations.room[i].price = beds[i].price;
        reservations.room[i].reservedDates = [];
    }

    let days;
    for (let i = 0; i < rooms.length; ++i) {
        days = getDates(new Date(rooms[i].checkIn), new Date(rooms[i].checkOut));
        reservations.room.find(e => e.roomId == rooms[i].Room_idRoom).reservedDates = [...(reservations.room.find(e => e.roomId == rooms[i].Room_idRoom).reservedDates || []), ...(days || [])];
    }
    let invalid_dates = [];

    function refreshInvalidDates() {
        let e = document.getElementById("roomSelection");
        let val = e.options[e.selectedIndex].value;
        let index = parseInt(val);
        let rm = reservations.room.find(e => e.roomId == index);
        let startDates = rm.reservedDates;
        invalid_dates = [];
        for (let r = 0; r < startDates.length; ++r) {
            let today = startDates[r];
            let dd = String(today.getDate()).padStart(2, '0');
            let mm = String(today.getMonth() + 1).padStart(2, '0');
            let yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;
            invalid_dates.push(today.toString());
        }
    }

    function fillCapacity() {
        let e = document.getElementById("roomCapacity");
        let selectedRoomId = document.getElementById("roomSelection").value;
        e.value = beds.find(e => e.idRoom == selectedRoomId).capacity;
    }

    function fillPicker() {
        $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                locale: {
                    "format": "MM/DD/YYYY",
                    "separator": " - ",
                },
                isInvalidDate: function (date) {
                    return (invalid_dates.indexOf(date.format('YYYY-MM-DD')) > -1);
                }
            }
        );
    }

    function fillPriceTextField() {
        let priceTextField = document.getElementById('nightlyPrice');
        let e = document.getElementById("roomSelection");
        let val = e.options[e.selectedIndex].value;
        let index = parseInt(val);
        let rm = reservations.room.find(e => e.roomId == index);
        priceTextField.value = rm.price;
    }

    function refreshDates() {
        refreshInvalidDates();
        fillPriceTextField();
        fillPicker();
        fillCapacity()
    }

    $(function () {
        refreshDates();
    });

    function changeImageAndRefresh() {
        let e = document.getElementById("hotelSelection");
        let holder = document.getElementById("imageHolder");
        holder.innerHTML = "<img src=" + images[e.selectedIndex].src + " width='512px'" + " id='hotelPic'>";
        $("#roomSelection").empty();
        for (let i = 0; i < data.length; ++i) {
            if (data[i].room_idHotel == e.options[e.selectedIndex].value) {
                dropdown.append($('<option></option>').attr('value', data[i].idRoom).text(data[i].name));
            }
        }
        refreshInvalidDates();
        fillPicker();
        fillPriceTextField();
        fillCapacity()
    }

    let currentTab = 0; // Current tab is set to be the first tab (0)
    showTab(currentTab); // Display the current tab

    //https://www.w3schools.com/howto/howto_js_form_steps.asp
    function showTab(n) {
        let x = document.getElementsByClassName("tab");
        x[n].style.display = "block";
        fixStepIndicator(n)
    }

    function nextPrevStep(n) {
        let input;
        page += n;
        if (page == 1) {
            input = document.getElementById("daterange").value;
            if (input != "" || input.length != 0 || input != null) {
                submittedDate = input;
            }
            input = document.getElementById("hotelSelection");
            if (input != "" || input.length != 0 || input != null) {
                submittedHotel = input.options[input.selectedIndex].innerHTML;
                submittedHotelId = input.value;
            }
            input = document.getElementById("roomSelection");
            if (input != "" || input.length != 0 || input != null) {
                submittedRoomId = input.value;
                submittedRoom = input.options[input.selectedIndex].innerHTML;
            }
        } else if (page == 2) {
            input = document.getElementById("firstName").value;
            if (input != "" || input.length != 0 || input != null) {
                submittedFname = input;
            }
            input = document.getElementById("lastName").value;
            if (input != "" || input.length != 0 || input != null) {
                submittedLname = input;
            }
            input = document.getElementById("email").value;
            if (input != "" || input.length != 0 || input != null) {
                submittedEmail = input;
            }
            input = document.getElementById("birth").value;
            if (input != "" || input.length != 0 || input != null) {
                submittedBirthDate = input;
            }
            input = document.getElementById("password").value;
            if (input != "" || input.length != 0 || input != null) {
                submittedPw = input;
            }
        } else if (page == 3) {
            input = document.getElementById("street").value;
            if (input != "" || input.length != 0 || input != null) {
                submittedStreet = input;
            }
            input = document.getElementById("houseNr").value;
            if (input != "" || input.length != 0 || input != null) {
                submittedHouseNr = input;
            }
            input = document.getElementById("zip").value;
            if (input != "" || input.length != 0 || input != null) {
                submittedZIP = input;
            }
            input = document.getElementById("city").value;
            if (input != "" || input.length != 0 || input != null) {
                submittedCity = input;
            }
        }
        // This function will figure out which tab to display
        let x = document.getElementsByClassName("tab");
        // Exit the function if any field in the current tab is invalid:
        if (n == 1 && !validateForm()) return false;
        // Hide the current tab:
        x[currentTab].style.display = "none";
        // Increase or decrease the current tab by 1:
        currentTab = currentTab + n;
        // if you have reached the end of the form... :
        if (currentTab >= x.length) {
            //...the form gets submitted:
            document.getElementById("regForm").submit();
            return false;
        }
        // Otherwise, display the correct tab:
        showTab(currentTab);
    }


    function finishOrder() {
        if ((submittedFname && submittedLname) || (document.getElementById('street').value != '' &&
            document.getElementById('houseNr').value != '' &&
            document.getElementById('zip').value != '' &&
            document.getElementById('city').value != '')) {
            nextPrevStep(1);
            image.style.display = "none";
            let accommodationDates = submittedDate.split(" - ");
            let checkIn = new Date(accommodationDates[0]);
            let checkOut = new Date(accommodationDates[1]);
            let stayLengthInMs = checkOut.getTime() - checkIn.getTime();
            let stayLength = stayLengthInMs / (1000 * 3600 * 24);
            if (stayLength <= 0)
                stayLength = 1;
            let pricing = parseFloat(reservations.room.find(e => e.roomId == submittedRoomId).price);
            let finalPrice = stayLength * pricing;
            document.getElementById("summary").innerHTML = '' +
                    '<div>
                '<div class="booking-btns">' +
                '<h2>Hotel: ' + submittedHotel + '</h2>' +
                '<h1>Room: ' + submittedRoom + '</h1>' +
                '</div><div class="booking-btns"> ' +
                '<table>' +
                '<tr><td>Name:</td><td>' + submittedFname + "&nbsp;" + submittedLname + '</td></tr>' +
                '<tr><td>Email:</td><td>' + submittedEmail + '</td></tr>' +
                '<tr><td>Address:</td><td>' + submittedStreet + "&nbsp;" + submittedHouseNr + '<br>' +
                                            submittedZIP + "&nbsp;&nbsp;" + submittedCity + '</td></tr>' +
                '</table></div>' +
                '<hr>' +
                '</div>' +
                '<div style=" grid-row: 1 / span 2;">' +
                '</div>' +
                '</div>' +
                '<div class="footerDark">' +
                '<h2>Dates: ' + submittedDate + '</h2>' +
                '<h2>Price: ' +
                +finalPrice + '</h2>';
        }
    }

    function fixStepIndicator(n) {
        // This function removes the "active" class of all steps...
        let i, x = document.getElementsByClassName("step");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
        }
        //... and adds the "active" class to the current step:
        x[n].className += " active";
    }

    function editOrderDetails() {
        nextPrevStep(-1);
        document.getElementById("summary").innerHTML = '';
        image.style.visibility = "visible";
    }

    function pricingRefresh() {
        let e = document.getElementById("roomSelection");
        let val = e.options[e.selectedIndex].value;
        let index = parseInt(val);
        let rm = reservations.room.find(e => e.roomId == index);
        let datePicker = document.getElementById("daterange");
        let datesUnclean = datePicker.value;
        let dates = datesUnclean.split(" - ");
        let checkIn = new Date(dates[0]);
        let checkOut = new Date(dates[1]);
        let timeDiff = checkOut.getTime() - checkIn.getTime();
        let timeDiffInDays = timeDiff / (1000 * 3600 * 24);
        let totalPrice = document.getElementById("total");
        totalPrice.value = timeDiffInDays * rm.price;
        document.getElementById("nextBtn").disabled = totalPrice.value == 0;
    }

</script>
</body>
</html>
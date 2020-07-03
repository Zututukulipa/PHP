    <div class="form-group" id="ifYes">
        <input name="name" id="name" type="text" placeholder="Name" required>
        <input name="surname" id="surname" type="text" placeholder="Surname" required>
        <input name="pw" id="pw" type="text" placeholder="Password" required>
        <input name="birth" id="birth" type="number" placeholder="YYYYMMDD" onfocusout="isBirthValid()" required>
        <input name="email" id="email" type="email" placeholder="Email" required>
        <input name="street" id="street" type="text" placeholder="Street" required>
        <input name="houseNr" id="houseNr" type="text" placeholder="HouseNr" required>
        <input name="city" id="city" type="text" placeholder="City" required>
        <input name="zip" id="zip" type="number" placeholder="ZIP" required>
    <?php echo '<input type="number" value="' . $_GET['hotelId'] . '" name="hotelId" hidden/>'; ?>
    </div>
    <div class="form-group" id="ifNo" style="display: none">
        <input name="userId" id="userId" type="number" placeholder="userId">
    </div>
<div id="ifNo" style="display:none" class="row">
    <input name="userId" id="userId" type="text" placeholder="UserId">
</div>
<input type="text" value="addEmployee" hidden>
<script type="text/javascript" src="../js/formValidations.js"></script>

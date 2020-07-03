
<form action="../form_handling/editHotel/addEmployee.php" method="post" name="AddEmployee">
    <?php include 'userAddForm.php';
    $_SESSION['Err'] = 0?>
    <b>register new user?</b>
    <div class="row">
        Yes <input class="form-checkbox" type="radio" onclick="newUserCheck();" name="yesno"
                   id="yesCheck" checked>
        No <input class="form-checkbox" type="radio" onclick="newUserCheck();" name="yesno" id="noCheck">
    </div>
    <input type="submit" class="submit-btn">
</form>
<?php
 if($_SESSION['Err'] == 1){
     echo "<b>User Email already exists</b>";
     $_SESSION['Err'] = 0;
 }
?>
<script>
    function newUserCheck() {
        let checkmark = document.getElementById('yesCheck');
        let divYes = document.getElementById('ifYes');
        let divNo= document.getElementById('ifNo');
        if (checkmark.checked) {
            document.getElementById('name').className = "required";
            document.getElementById('name')..required = true;
            div.style.display = 'block';
        } else {
            field.className.replace("required", "");
            div.style.display = 'none';
            field.required = false;
        }
    }
</script>

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
        let divNo   = document.getElementById('ifNo');
        if (checkmark.checked) {
            document.getElementById('name').className = "required";
            document.getElementById('name').required = true;
            document.getElementById('surname').className = "required";
            document.getElementById('surname').required = true;
            document.getElementById('pw').className = "required";
            document.getElementById('pw').required = true;
            document.getElementById('birth').className = "required";
            document.getElementById('birth').required = true;
            document.getElementById('email').className = "required";
            document.getElementById('email').required = true;
            document.getElementById('street').className = "required";
            document.getElementById('street').required = true;
            document.getElementById('houseNr').className = "required";
            document.getElementById('houseNr').required = true;
            document.getElementById('city').className = "required";
            document.getElementById('city').required = true;
            document.getElementById('zip').className = "required";
            document.getElementById('zip').required = true;
            divYes.style.display = 'block';
            document.getElementById('userId').required = false;
            document.getElementById('userId').className = "";
            divNo.style.display = 'none';
        } else {
            document.getElementById('name').className = "";
            document.getElementById('name').required = false;
            document.getElementById('surname').className = "";
            document.getElementById('surname').required = false;
            document.getElementById('pw').className = "";
            document.getElementById('pw').required = false;
            document.getElementById('birth').className = "";
            document.getElementById('birth').required = false;
            document.getElementById('email').className = "";
            document.getElementById('email').required = false;
            document.getElementById('street').className = "";
            document.getElementById('street').required = false;
            document.getElementById('houseNr').className = "";
            document.getElementById('houseNr').required = false;
            document.getElementById('city').className = "";
            document.getElementById('city').required = false;
            document.getElementById('zip').className = "";
            document.getElementById('zip').required = false;
            divYes.style.display = 'none';
            document.getElementById('userId').required = true;
            document.getElementById('userId').className = "required";
            divNo.style.display = 'block';
        }
    }
</script>
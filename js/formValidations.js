function validDateCheck(checkedDateInteger) {
    let today = new Date();
    let todayInteger = today.getFullYear() * 10000 + (today.getMonth() + 1) * 100 + today.getDate();
    let hundredYearsAgoInteger = (today.getFullYear() - 100) * 10000 + (today.getMonth() + 1) * 100 + today.getDate();
    if (checkedDateInteger < todayInteger || checkedDateInteger > hundredYearsAgoInteger) {
        let day = parseInt(checkedDateInteger.toString().slice(-2));
        let month = parseInt(checkedDateInteger.toString().slice(4, 6));
        if (month < 13 && day < 31) {
            switch (month) {
                case 1:
                    if (day < 32)
                        return true;
                    break;
                case 2:
                    if (day < 30)
                        return true;
                    break;
                case 3:
                    if (day < 32)
                        return true;
                    break;
                case 4:
                    if (day < 31)
                        return true;
                    break;
                case 5:
                    if (day < 32)
                        return true;
                    break;
                case 6:
                    if (day < 31)
                        return true;
                    break;
                case 7:
                    if (day < 32)
                        return true;
                    break;
                case 8:
                    if (day < 32)
                        return true;
                    break;
                case 9:
                    if (day < 31)
                        return true;
                    break;
                case 10:
                    if (day < 32)
                        return true;
                    break;
                case 11:
                    if (day < 31)
                        return true;
                    break;
                case 12:
                    if (day < 32)
                        return true;
                    break;
            }
        }
    }
    return false;

}

function validateForm() {
    // This function deals with validation of the form fields
    let x, y, i;
    let valid = true;
    let mailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    x = document.getElementsByClassName("tab");
    y = x[currentTab].getElementsByTagName("input");
    // A loop that checks every input field in the current tab:
    for (i = 0; i < y.length; i++) {
        // If a field is empty...
        if (y[i].value === "" && y[i].required !== false) {
            // add an "invalid" class to the field:
            y[i].className = "invalid";
            // and set the current valid status to false:
            valid = false;
        }
        if (y[i].name == "email" && !mailRegex.test(y[i].value)) {
            y[i].value = "";
            y[i].placeholder = "invalid email";
            y[i].className = "invalid";
            // and set the current valid status to false:
            valid = false;
        }
        if (y[i].name == "birth") {
            if(!validDateCheck(y[i].value)){
                y[i].value = "";
                y[i].className = "invalid";
                // and set the current valid status to false:
                valid = false;
            }
        }
    }
    // If the valid status is true, mark the step as finished and valid:
    if (valid) {
        let fin = document.getElementsByClassName("step")[currentTab];
        fin.className += " finish";
    }
    return valid; // return the valid status
}

function yesnoCheck() {
    let checkmark = document.getElementById('yesCheck');
    let div = document.getElementById('ifYes');
    let field = document.getElementById('password');
    if (checkmark.checked) {
        field.className = "required";
        div.style.display = 'block';
        field.required = true;
    } else {
        field.className.replace("required", "");
        div.style.display = 'none';
        field.required = false;
    }
}

function isBirthValid() {
    let birthDate = document.getElementById('birth').value;

    if (!validDateCheck(birthDate)) {
        y[i].value = "";
        y[i].className = "invalid";
        // and set the current valid status to false:
        valid = false;
    }
}
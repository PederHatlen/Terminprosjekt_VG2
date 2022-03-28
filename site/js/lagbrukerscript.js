// Declarring/finding ellements used later
let usernameEL = document.getElementById("username");
let pwdEL = document.getElementById("password");
let pwdConEL = document.getElementById("passwordControll");
let submitbtn = document.getElementById("submit");

// Checking the input on the second password field if it matches the first, then unlock submit button
function checkinput() {
    if (usernameEL.value != "" && pwdEL.value != "" && pwdEL.value === pwdConEL.value) {
        // submitbtn.style.backgroundColor = "white";
        submitbtn.disabled = false;
    }else{
        // submitbtn.style.backgroundColor = "";
        submitbtn.disabled = true;
    }
}

// Cleaning the input with regEx
function cleanInput(inn){
    return inn.replace(/[^10]+/g, '');
}

Array.from(document.getElementsByClassName("input")).forEach(element => {
    // If a key other than 1, 0 or some other comfort buttons are pressed, prevent it from being registered
    element.addEventListener("input", function(e) {
        e.target.value = cleanInput(e.target.value);
        checkinput();
    });
});
checkinput();
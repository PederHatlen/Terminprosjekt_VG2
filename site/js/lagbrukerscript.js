let usernameEL = document.querySelector("#username");

let pwdEL = document.querySelector("#password");
let pwdConEL = document.querySelector("#passwordControll");

let submitbtn = document.querySelector("#submit");

usernameEL.oninput = checkinput;
pwdEL.oninput = checkinput;
pwdConEL.oninput = checkinput;
checkinput()

function checkinput() {
    if (usernameEL.value != "" && pwdEL.value != "" && pwdEL.value === pwdConEL.value) {
        submitbtn.style.backgroundColor = "";
        submitbtn.disabled = false;
    }else{
        submitbtn.style.backgroundColor = "Gray";
        submitbtn.disabled = true;
    }
}
let usernameEL = document.querySelector("#username");

let pwdEL = document.querySelector("#password");
let pwdConEL = document.querySelector("#passwordControll");

let submitbtn = document.querySelector("#submit");


usernameEL.addEventListener('keydown', function(e) {
    if(e.keyCode != 48 && e.keyCode != 49 && e.keyCode != 8 && e.keyCode != 97 && e.keyCode != 96 && e.keyCode) {
        e.preventDefault();
    }
});

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
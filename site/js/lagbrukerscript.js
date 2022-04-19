// Declarring/finding ellements used later
let usernameEL = document.getElementById("username");
let pwdEL = document.getElementById("password");
let pwdConEL = document.getElementById("passwordControll");
let submitbtn = document.getElementById("submit");

// Checking the input on the second password field if it matches the first, then unlock submit button
function checkinput() {
	if (usernameEL.value != "" && pwdEL.value != "" && pwdEL.value == pwdConEL.value) {submitbtn.disabled = false;}
	else{submitbtn.disabled = true;}
}
checkinput();
let messageFormEL = document.getElementById("messageForm");
let messageEL = document.getElementById("usrmsg");
let chatWindow = document.getElementById("chatWindow");
console.log(window.location.host);

messageFormEL.onsubmit = function (e) {
    e.preventDefault();
    send()
}
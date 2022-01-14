let messageFormEL = document.getElementById("messageForm");
let messageEL = document.getElementById("usrmsg");
let chatWindow = document.getElementById("chatWindow");
console.log(window.location.host);

messageFormEL.onsubmit = function (e) {
    e.preventDefault();
    send()
}

let socket = new WebSocket("ws://" + window.location.host + ":5678");
socket.onopen = function () {
    chatWindow.innerHTML += "Status: Connected\n";
};
socket.onmessage = function (e) {
    chatWindow.innerHTML += e.data;
};


function send(message = messageEL.value) {
    socket.send(message);
    input.value = "";
}
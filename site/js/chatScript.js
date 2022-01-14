let messageFormEl = document.getElementById("messageForm");
let chatWindow = document.getElementById("chatWindow");
console.log(window.location.host);

let socket = new WebSocket("ws://" + window.location.host + ":5678");

socket.onopen = function () {
    chatWindow.innerHTML += "Status: Connected\n";
};

socket.onmessage = function (e) {
    chatWindow.innerHTML += "Server: " + e.data + "\n";
};
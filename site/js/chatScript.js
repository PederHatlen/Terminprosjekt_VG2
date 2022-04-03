let chatWindow = document.getElementById("chatWindow");
let messageFormEL = document.getElementById("messageForm");
let messageEL = document.getElementById("usrmsg");
let connectionInfoEL = document.getElementById("connectionInfo");
let socket = new WebSocket("ws://" + window.location.host + ":5678?");

let settCEl = document.getElementById("settingsContent");
let menuIconBTN = document.getElementById("menuIconButton");

socket.onopen = function () {
    connectionInfoEL.innerHTML += "Connected!";
    connectionInfoEL.style.backgroundColor = "green";
    
    socket.send(initData);
    
    socket.onmessage = function (e) {
        chatWindow.innerHTML += e.data;
        chatWindow.scrollTop = chatWindow.scrollHeight;
    };
    
    messageFormEL.onsubmit = function (e) {
        e.preventDefault();
        send();
    }
};
socket.onerror = function (e) {connectionInfo.innerHTML = "Someone forgot to start the websocket server.";}
socket.onclose = function (e) {
    connectionInfo.innerHTML = "Disconnected :(";
    connectionInfoEL.style.backgroundColor = "red";
    messageFormEL.onsubmit = null;
}

function send(message = messageEL.value) {
    socket.send(message);
    messageEL.value = "";
}
function togglesettings(){
    if (settCEl.style.display == "none"){
        settCEl.style.display = "inline-block";
        menuIconBTN.style.background = "var(--page-main2)";
    }else{
        settCEl.style.display = "none";
        menuIconBTN.style.background = "";
    }
}
chatWindow.scrollTop = chatWindow.scrollHeight;
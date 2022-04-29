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
	connectionInfoEL.style.display = "";
	
	socket.send(JSON.stringify(initData));

	socket.onclose = function (e) {
		connectionInfoEL.innerHTML = "Disconnected :(";
		connectionInfoEL.style.backgroundColor = "red";
		messageFormEL.onsubmit = null;
	}
	
	socket.onmessage = function (e) {
		let data = JSON.parse(e.data);
		
		let time = new Date(data["time"]);
		let ftime = ('00'+time.getHours()).slice(-2)+":"+('00'+time.getMinutes()).slice(-2)+":"+('00'+time.getSeconds()).slice(-2)

		console.log(`[${ftime}] MSG from ${data["user"]}`);

		chatWindow.innerHTML += `<p><span ${(unicorn? '':'style="color: '+data["color"]+'"')};><span class='time'>[${ftime}]</span> ${data["user"]}:</span> ${data["msg"]}</p>`;
		chatWindow.scrollTop = chatWindow.scrollHeight;
	};
	
	messageFormEL.onsubmit = function (e) {
		e.preventDefault();
		send();
	}
};

function send(message = messageEL.value) {
	socket.send(JSON.stringify({msg:message}));
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
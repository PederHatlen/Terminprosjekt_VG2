let clockEl = document.getElementById("clock");
let keystring = "";

window.addEventListener("keydown", function(e){
	let char = e.key.toLowerCase();

	if(char.match(/[a-z]/g) == null && char != "backspace") return;

	if(char == "backspace") keystring = keystring.slice(0, -1);
	else keystring += char;

	//console.log(char, keystring);
	if(keystring.slice(-7) == "unicorn"){window.location.replace(location.origin+location.pathname + '?unicorn')}
	if(keystring.slice(-8) == "hexclock"){window.location.replace(location.origin+location.pathname + '?hexclock')}
});

function checkinput() {return true;}
function cleanInput(inn){return inn.replace(/[^10]+/g, '');}

function inputFunc(e){
	e.target.value = cleanInput(e.target.value);
	checkinput();
}

// Calling the inputFunction when input on every input tag
Array.from(document.getElementsByClassName("input")).forEach(element => {
	element.addEventListener("input", inputFunc);
});

function luminance(color) {
	if (color[0] == '#') color.slice(1);
	if (color.length == 3) color = color.replace(/./g, '$&$&'); // https://stackoverflow.com/a/40358066
	c = color.split(/(..)/g).filter(s => s); // https://stackoverflow.com/a/63887162
	c = c.map((x)=>{return parseInt(x, 16)});
	return Math.round(0.2126*c[0] + 0.7152*c[1] + 0.0722*c[2]);
}

let doHexClock = false;
toggleHexClock = ()=>{
	if(doHexClock) doHexClock=false;
	else doHexClock=true; hexClock();
}

let oldSeconds;
function hexClock() {
	if(doHexClock) window.requestAnimationFrame(hexClock);
	let t = new Date;
	if(t.getSeconds != oldSeconds){
		oldSeconds = t.getSeconds();
		let hex = "#"+(('00'+t.getHours().toString(16)).slice(-2) + ('00'+t.getMinutes().toString(16)).slice(-2) + ('00'+t.getSeconds().toString(16)).slice(-2)).toUpperCase();
		document.documentElement.style.setProperty('--header-color', hex);
		clockEl.innerHTML = hex;
	}
}
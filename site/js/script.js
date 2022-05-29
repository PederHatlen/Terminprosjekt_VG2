let clockEl = document.getElementById("clock");
let keystring = "";

// Checking if hexclock is activated, 
window.addEventListener("keydown", function(e){
	let char = e.key.toLowerCase();

	if(char == "backspace") {keystring = keystring.slice(0, -1);}
	else if(char.match(/^.{1}$/g) == null) {return;} // Insures string is only one character

	keystring += char;
	// refreshes with needed get atributes
	if(keystring.slice(-8) == "hexclock"){window.location.replace(location.origin+location.pathname + '?hexclock')}
});

function cleanInput(inn){return inn.replace(/[^10]+/g, '');} // Regex to clean a string to only be binary
function checkinput() {return true;} // Checkinput used by some pages, but it is specified there, and should then be empty

// replace text in the input with binary, checkinput documented over
function inputFunc(e){
	e.target.value = cleanInput(e.target.value);
	checkinput();
}

// Calling the inputFunction when input on every input tag
Array.from(document.getElementsByClassName("input")).forEach(element => {
	element.addEventListener("input", inputFunc);
});

// Luminance function, put together from some different articles
// Takes color and ensures it's 6 characters long, then converts to rgb and uses relative luminance mappings (https://en.wikipedia.org/wiki/Relative_luminance)
function luminance(color) {
	if (color[0] == '#') color.slice(1);
	if (color.length == 3) color = color.replace(/./g, '$&$&'); // https://stackoverflow.com/a/40358066
	c = color.split(/(..)/g).filter(s => s); // https://stackoverflow.com/a/63887162
	c = c.map((x)=>{return parseInt(x, 16)});
	return Math.round(0.2126*c[0] + 0.7152*c[1] + 0.0722*c[2]);
}

// Hex clock part
let doHexClock = false;
toggleHexClock = ()=>{
	if(doHexClock) doHexClock=false;
	else doHexClock=true; hexClock();
}

let oldSeconds;
// Main hex clock loop function
function hexClock() {
	if(doHexClock) window.requestAnimationFrame(hexClock);
	let t = new Date;
	if(t.getSeconds != oldSeconds){
		oldSeconds = t.getSeconds();
		// Building the color code from time, slice to get a length of 2
		let hex = "#"+(('00'+t.getHours().toString(16)).slice(-2) + ('00'+t.getMinutes().toString(16)).slice(-2) + ('00'+t.getSeconds().toString(16)).slice(-2)).toUpperCase();
		// Setting root css values to integrate into css
		document.documentElement.style.setProperty('--header-color', hex);
		clockEl.innerHTML = hex;
	}
}
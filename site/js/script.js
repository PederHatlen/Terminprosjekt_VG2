let legalChars = "unicor";
let keystring = "";
let mainArea = document.getElementsByTagName("main");

window.addEventListener("keydown", function(e){
	let char = e.key.toLowerCase();
	// console.log(legalChars.indexOf(char));
	if(legalChars.indexOf(char) == -1 && char != "backspace") return;
	if(char == "backspace"){keystring = keystring.slice(0, -1)}
	else{keystring += char;}

	//console.log(char, keystring);
	if(keystring.slice(-7) == "unicorn"){window.location.replace(location.origin+location.pathname + '?unicorn')}
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
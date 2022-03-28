// Main script document, it will expand when i start work on more client side features
// For every input tag
Array.from(document.getElementsByClassName("input")).forEach(element => {
    // If a key other than 1, 0 or some other comfort buttons are pressed, prevent it from being registered
    element.addEventListener("input", function(e) {
        element.value = cleanInput(element.value);
    });
});

function cleanInput(inn){
    return inn.replace(/[^10]+/g, '');
}

let legalChars = "unicor";
let keystring = "";
let mainArea = document.getElementsByTagName("main");

window.addEventListener("keydown", function(e){
    let char = e.key.toLowerCase();
    if(legalChars.indexOf(char) == -1 && char != "backspace") return;
    if(char == "backspace"){keystring = keystring.slice(0, -1)}
    else{keystring += char;}

    //console.log(char, keystring);
    if(keystring.slice(-7) == "unicorn"){window.location.replace(location.origin+location.pathname + '?unicorn')}
});
// Main script document, it will expand when i start work on more client side features
// For every input tag
Array.from(document.getElementsByClassName("input")).forEach(element => {
    // If a key other than 1, 0 or some other comfort buttons are pressed, prevent it from being registered
    element.oninput = function(e) {
        console.log(e);
        element.value = cleanInput(element.value);
    };
});

function cleanInput(inn){
    return inn.replace(/[^10]+/g, '');
}
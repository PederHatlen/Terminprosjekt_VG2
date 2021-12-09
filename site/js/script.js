// Main script document, it will expand when i start work on more client side features
// For every input tag
document.querySelectorAll("input").forEach(element => {
    // If a key other than 1, 0 or some other comfort buttons are pressed, prevent it from being registered (Does not currently work on mobile)
    element.onkeydown = function(e) {
        if(e.key != 0 && e.key != 1 && e.code != "Backspace" && e.code != "Tab" && e.code != "Enter" && !e.ctrlKey ) {
            console.log(e.code);
            e.preventDefault();
        }
    };
    // Clear all charracters other than 1 or 0 from paste data (IE compatible)
    element.onpaste = function(e) {
        let paste = (e.clipboardData || window.clipboardData).getData('text/plain');
        paste = paste.replace(/[^10]+/g, '')
        element.value = paste;
        e.preventDefault();
    };
});
document.querySelectorAll("input").forEach(element => {
    element.addEventListener('keydown', function(e) {
        if(e.key != 0 && e.key != 1 && e.code != "Backspace" && !e.ctrlKey ) {
            console.log(e.code);
            e.preventDefault();
        }
    });
    element.onpaste = function(e) {
        let paste = (e.clipboardData || window.clipboardData).getData('text/plain');
        paste = paste.replace(/[^10]+/g, '')
        element.value = paste;
        e.preventDefault();
    };
});
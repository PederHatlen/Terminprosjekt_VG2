document.querySelectorAll("input").forEach(element => {
    element.addEventListener('keydown', function(e) {
        if(e.key != 0 && e.key != 1 && e.code != "Backspace") {
            e.preventDefault();
        }
    });
});


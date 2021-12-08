messageFormEl = document.forms[0];

document.querySelectorAll("input").forEach(element => {
    element.addEventListener('keydown', function(e) {
        if(e.key != 0 && e.key != 1 && e.code != "Backspace" && e.code != "Enter") {
            e.preventDefault();
        }else if(e.code == "Enter"){
            messageFormEl.submit();
        }
    });
});
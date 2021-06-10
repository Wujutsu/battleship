const $ = require('jquery');

//Btn play => allows to start a game
$(".btnPlay").click(function (e) { 
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: "/ajax/startGame",
        data: {
            token: $("#token").val()
        },
        success: function (token) {
            window.location.replace('https://127.0.0.1:8000/game/' + token);
        }
    });
});
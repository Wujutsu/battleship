const $ = require('jquery');

var token = $("#token").val();
var player = $("#player").val();

//PlayerOne => Reload page to wait the second player
if (player == 0 && $("#startGame").val() == "false") {
    setInterval(function() { // this code is executed every 2s:
        location.reload();
    }, 2000);
}

//Click on ennemy cellule
$(".cellule").click(function (e) { 
    e.preventDefault();
    var cellule = $(this).attr("data-cellule");

    $.ajax({
        type: "post",
        url: "/ajax/touchCellule",
        data: {
            token: token,
            player: player,
            cellule: cellule
        },
        success: function () {
            location.reload();
        }
    });
});
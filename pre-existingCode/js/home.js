var board;
function load()
{
    initialize();
}

function initialize()
{
    $.Chessboard = $("#board");
    handleEvents();
}

function handleEvents()
{
    if (!wait_for_script) {
        document.addEventListener("DOMContentLoaded", initializeGame);
    }
    
    $("#btnNewGame").click(function() {
        newGame();
    });
    
    $("#decreaseBoardSize").click(function() {
        var width = $.Chessboard.width();
        $.Chessboard.width(width-25);
    });
    
    $("#increaseBoardSize").click(function() {
        var width = $.Chessboard.width();
        $.Chessboard.width(width+25);
    });
    
    $("#skillLevel").change(function(){
        game.setSkillLevel(parseInt(this.value, 10));
    });
}

function customMessage(message)
{
    bootbox.alert(message);
}
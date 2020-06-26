var fen
{
    function getStartingFenPosition(gametype)
    {
        var fenMap = new Map();
        fenMap.set("classic", "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1");
        fenMap.set("960", eval("getRandomStartingFenPosition()"));
        return fenMap.get(gametype);
    }
    
    function getRandomStartingFenPosition()
    {
        var bottomArr = ['r','n','b','q','k','b','n','r'];
        bottomArr = shuffle(bottomArr);
        var topArr = ['R','N','B','Q','K','B','N','R'];
        topArr = shuffle(topArr);
        return bottomArr.join("") + "/pppppppp/8/8/8/8/PPPPPPPP/" + topArr.join("") + " w KQkq - 0 1";
    }
    
    function shuffle(array)
    {
        var currentIndex = array.length, temporaryValue, randomIndex;

        // While there remain elements to shuffle...
        while (0 !== currentIndex) {

            // Pick a remaining element...
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex -= 1;

            // And swap it with the current element.
            temporaryValue = array[currentIndex];
            array[currentIndex] = array[randomIndex];
            array[randomIndex] = temporaryValue;
        }

        return array;
    }

}



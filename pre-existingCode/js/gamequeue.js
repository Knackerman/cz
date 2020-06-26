var gameQueue
{
    var interval;
    function addToQueue(userName, gameType, timeControl)
    {
        var parameterNames = ["action", "userName", "gameType", "timeControl"];
        var parameterValues = [ADDUSERTOQUEUE, userName, gameType, timeControl];
        var queryString = getQueryString(parameterNames, parameterValues);
        xmlhttp=new XMLHttpRequest();
        xmlhttp.open("POST","./php/GameQueue.php?"+queryString,true);
        xmlhttp.send();
    }

    function waitForQueue(userName)
    {
        interval = setInterval(function(){ return getOpponent(userName); }, 5000);
    }

    function getOpponent(userName)
    {
        var parameterNames = ["action", "userName"];
        var parameterValues = [FINDOPPONENT, userName];
        var queryString = getQueryString(parameterNames, parameterValues);
        xmlhttp=new XMLHttpRequest();
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState===4 && xmlhttp.status===200)
            {
                var response = JSON.parse(this.responseText);
                if(response.GameID !== null)
                {
                    clearInterval(interval);
                    removeUserFromQueue(userName);
                    prepareGame(response);
                }
            }
        };
        xmlhttp.open("GET","./php/GameQueue.php?"+queryString,true);
        xmlhttp.send();
    }

    function removeUserFromQueue(userName)
    {
        var parameterNames = ["action", "userName"];
        var parameterValues = [REMOVEUSERFROMQUEUE, userName];
        var queryString = getQueryString(parameterNames, parameterValues);
        xmlhttp=new XMLHttpRequest();
        xmlhttp.open("POST","./php/GameQueue.php?"+queryString,true);
        xmlhttp.send();
    }
}
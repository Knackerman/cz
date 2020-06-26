
function getParameterByName(name, url)
{
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function getQueryString(parameterNames, parameterValues)
{
    var queryString = "";
    var length = parameterNames.length;
    for(var i=0; i<length; i++)
    {
        queryString += parameterNames[i] + "=" + parameterValues[i];
        if(i!==length-1)
        {
            queryString += "&";
        }
    }
    return queryString;
}

function validateEmail(mail) 
{
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail))
    {
        return true;
    }
    return false;
}

function GetNewRating(currentPlayerRating, opponentRating, result, draw)
{
    var k = 30;
    var p1 = (1.0 / (1.0 + Math.pow(10, ((currentPlayerRating-opponentRating) / 400))));
    if(draw)
    {
        return Math.floor(currentPlayerRating + k*(.5 - p1));
    }
    if(result)
    {
        return Math.floor(currentPlayerRating + k*(1 - p1));
    }
    else
    {
        return Math.floor(currentPlayerRating + k*(0 - p1));
    }
}



var clickCounter
{
    function updateClickCount(pageId)
    {
        var parameterNames = ["pageId"];
        var parameterValues = [pageId];
        var queryString = getQueryString(parameterNames, parameterValues);
        xmlhttp=new XMLHttpRequest();
        xmlhttp.open("POST","./php/ClickCounter.php?"+queryString,true);
        xmlhttp.send();
    }
}
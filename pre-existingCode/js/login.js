var login 
{
    function initialize()
    {
        handleEvents();
    }

    function handleEvents()
    {
        $("#btnLogin").click(function(){
            login();
        });
    }

    function getLoggedInUser(callback)
    {
        var parameterNames = ["action"];
        var parameterValues = [GETLOGGEDINUSER];
        var queryString = getQueryString(parameterNames, parameterValues);
        xmlhttp=new XMLHttpRequest();
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState===4 && xmlhttp.status===200)
            {
                var response = JSON.parse(this.responseText);
                if(response.ErrorMessage !== null)
                {
                    customMessage(response.ErrorMessage);
                }
                else
                {
                    callback(response.UserName);
                }
            }
        };
        xmlhttp.open("GET","./php/Login.php?"+queryString,true);
        xmlhttp.send();
    }

    function login()
    {
        var userName = $("#inputUserName").val();
        var password = $("#inputPassword").val();
        if(userName.length===0 || password.length===0)
        {
            customMessage(ALLFIELDSMUSTBEFILLEDOUT);
            return;
        }
        var parameterNames = ["userName", "password", "action"];
        var parameterValues = [userName, password, LOGIN];
        var queryString = getQueryString(parameterNames, parameterValues);
        xmlhttp=new XMLHttpRequest();
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState===4 && xmlhttp.status===200)
            {
                var response = JSON.parse(this.responseText);
                if(response.ErrorMessage !== null)
                {
                    customMessage(response.ErrorMessage);
                }
                else
                {
                    customMessage(response.Message);
                    window.location.href = './dashboard.html';
                }
            }
        };
        xmlhttp.open("GET","./php/Login.php?"+queryString,true);
        xmlhttp.send();
    }

    function customMessage(message)
    {
        bootbox.alert(message);
    }
}

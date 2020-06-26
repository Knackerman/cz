function createAccount()
{
    var username = $("#txtAccountUsername").val();
    var password = $("#txtAccountPassword").val();
    var isAdmin = $("#chkLoginAdmin").checked;
    var admin = "";
    if(isAdmin)
        admin = "Y";
    else
        admin = "N";
    if(username.length<6)
    {
        customMessage("Username must be at least 6 characters");
        return;
    }
    if(password.length<6)
    {
        customMessage("Password must be at least 6 characters");
        return;
    }
    xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState===4 && xmlhttp.status===200)
        {
            var response = xmlhttp.responseText;
            customMessage(response);
        }
    };
    xmlhttp.open("GET","./php/Account.php?username="+username+"&password="+password+"&admin="+admin,true);
    xmlhttp.send();
}
var dashboard
{
    var loggedInUser = "";
    function initialize()
    {
        handleEvents();
        getLoggedInUser(setLoggedInUser);
    }

    function handleEvents()
    {
        $("#classicPVPFifteen").click(function()
        {
            window.location.href = "./game.html?gt=classic&gm=pvp&tc=quick&un=" + loggedInUser;
        });

        $("#classicPVPFive").click(function()
        {
            window.location.href = "./game.html?gt=classic&gm=pvp&tc=blitz&un=" + loggedInUser;
        });
    }

    function setLoggedInUser(userName)
    {
        loggedInUser = userName;
        $("#welcomeInput").html("Welcome Back, "+ userName);
    }
}
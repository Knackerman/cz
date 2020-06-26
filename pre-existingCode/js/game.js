var game 
{
     var clockInterval;
     var moveInterval;
     var game;
     var wait_for_script;
     var gameType;
     var gameMode;
     var timeControl;
     var userName;
     var startingFenPosition;
     var white;
     var black;
     var turn = false;
     var whiteTimeRemaining;
     var blackTimeRemaining;
     var started;
     var gameID;
     var color;
     var currentPlayerRating;
     var opponentRating;
     var newEngineGame = function () { };
     function initializeGame() 
     {
          load();
          gameType = getParameterByName(GAMETYPEPARAMETER, window.location.href);
          gameMode = getParameterByName(GAMEMODEPARAMETER, window.location.href);
          timeControl = getParameterByName(TIMECONTROLPARAMETER, window.location.href);
          userName = getParameterByName(USERNAMEPARAMETER, window.location.href);
          startingFenPosition = getStartingFenPosition(gameType);
          if(gameMode===PVE)
          {
               game = engineGame(null, startingFenPosition);
               newEngineGame = function newEngineGame()
               {
                    var baseTime = parseFloat($('#timeBase').val()) * 60;
                    var inc = parseFloat($('#timeInc').val());
                    var skill = parseInt($('#skillLevel').val());
                    game.reset(startingFenPosition);
                    game.setTime(baseTime, inc);
                    game.setSkillLevel(skill);
                    game.setPlayerColor($('#color-white').hasClass('active') ? 'white' : 'black');
                    game.setDisplayScore($('#showScore').is(':checked'));
                    game.start();
               },
               newEngineGame();
          }
          else
          {
               addToQueue(userName, gameType, timeControl);
               waitForQueue(userName);
          }
     }

     function prepareGame(activeGameObject)
     {
          gameID = activeGameObject.GameID;
          white = activeGameObject.White;
          black = activeGameObject.Black;
          whiteTimeRemaining = activeGameObject.WhiteTimeRemaining;
          blackTimeRemaining = activeGameObject.BlackTimeRemaining;
          timeControl = activeGameObject.TimeControl;
          gameType = activeGameObject.GameType;
          if(timeControl === BLITZ)
          {
               currentPlayerRating = activeGameObject.CurrentPlayerBlitzRating;
               opponentRating = activeGameObject.OpponentPlayerBlitzRating;
          }
          else
          {
               currentPlayerRating = activeGameObject.CurrentPlayerQuickRating;
               opponentRating = activeGameObject.OpponentPlayerQuickRating;
          }
          $("#currentPlayerRating").text(currentPlayerRating);
          $("#opponentRating").text(opponentRating);
          $("#currentPlayer").text(userName);
          if(white===userName)
          {
               color = "w";
               turn = true;
               opponent = black;
               $("#opponent").text(black);
               $("#playerTime").text(whiteTimeRemaining);
               $("#opponentTime").text(blackTimeRemaining);
               newGame(startingFenPosition, 'w');
          }
          else
          {
               color = "b";
               opponent = white;
               $("#opponent").text(white);
               $("#playerTime").text(blackTimeRemaining);
               $("#opponentTime").text(whiteTimeRemaining);
               newGame(startingFenPosition, 'b');
               flipBoard();
          }
          startClock();
     }

     function handleCurrentPlayerMove(source, destination)
     {
          turn = !turn;
          started = true;
          var parameterNames = ["action", "gameId", "userName", "turn", "source", "destination", "whiteTimeRemaining", "blackTimeRemaining"];
          var parameterValues = [CURRENTPLAYERMOVE, gameID, userName, color, source, destination, whiteTimeRemaining, blackTimeRemaining];
          var queryString = getQueryString(parameterNames, parameterValues);
          xmlhttp=new XMLHttpRequest();
          xmlhttp.open("POST","./php/ActiveGame.php?"+queryString,true);
          xmlhttp.send();
          waitForPlayerMove();
     }

     function handleOpponentMove(responseObject)
     {
          started = true;
          if(color=="w")
          {
               $("#playerTime").text(responseObject.WhiteTimeRemaining);
               $("#opponentTime").text(responseObject.BlackTimeRemaining);
          }
          else
          {
               $("#playerTime").text(responseObject.BlackTimeRemaining);
               $("#opponentTime").text(responseObject.WhiteTimeRemaining);
          }
          makeMove(responseObject.Source, responseObject.Destination);
          turn = true;
     }

     function waitForPlayerMove()
     {
          interval = setInterval(function(){ return getOpponentMove(); }, 1000);
     }

     function getOpponentMove()
     {
          var parameterNames = ["action", "gameId", "userName", "turn", "whiteTimeRemaining", "blackTimeRemaining"];
          var parameterValues = [GETOPPONENTMOVE, gameID, opponent, color, whiteTimeRemaining, blackTimeRemaining];
          var queryString = getQueryString(parameterNames, parameterValues);
          xmlhttp=new XMLHttpRequest();
          xmlhttp.onreadystatechange=function()
          {
               if (xmlhttp.readyState===4 && xmlhttp.status===200)
               {
                    var response = JSON.parse(this.responseText);
                    if(response.GameID !== null && response.Destination != null && response.Turn==color)
                    {
                         clearInterval(interval);
                         handleOpponentMove(response);
                    }
               }
          };
          xmlhttp.open("GET","./php/ActiveGame.php?"+queryString,true);
          xmlhttp.send();
     }

     function startClock()
     {
          if(!turn)
          {
               waitForPlayerMove();
          }
          clockInterval = setInterval(function(){ return clockTick(); }, 1000);
     }

     function clockTick()
     {
          if(started)
          {
               if(color == "w" && turn)
               {
                    whiteTimeRemaining-=1;
                    $("#playerTime").text(whiteTimeRemaining);
               }
               else if(color == "b" && turn)
               {
                    blackTimeRemaining-=1;
                    $("#playerTime").text(blackTimeRemaining);
               }
               else if(color == "w" && !turn)
               {
                    blackTimeRemaining-=1;
                    $("#opponentTime").text(blackTimeRemaining);
               }
               else
               {
                    whiteTimeRemaining-=1;
                    $("#opponentTime").text(whiteTimeRemaining);
               }
          }
          if(whiteTimeRemaining == 0)
          {
               handleGameOver("b");
               clearInterval(clockInterval);
               return;
          }
          if(blackTimeRemaining == 0)
          {
               handleGameOver("w");
               clearInterval(clockInterval);
               return;
          }
     }

     function handleGameOver(winner, draw)
     {
          var parameterNames = [];
          var parameterValues = [];
          var queryString = "";
          var gameWon = winner === color;
          clearInterval(clockInterval);
          var tempCurrentPlayerRating = currentPlayerRating;
          currentPlayerRating = GetNewRating(currentPlayerRating, opponentRating, gameWon, false);
          opponentRating = GetNewRating(opponentRating, tempCurrentPlayerRating, !gameWon, false);
          if(color == "w")
          {
               var result = "1-0";
               if(winner == "b")
               {
                    result = "0-1";
               }
               if(draw)
               {
                    result = "0-0";
               }
               parameterNames = ["action", "gameId", "result"];
               parameterValues = [GAMEOVER, gameID, result];
               queryString = getQueryString(parameterNames, parameterValues);
               xmlhttp=new XMLHttpRequest();
               xmlhttp.open("POST","./php/GameOver.php?"+queryString,true);
               xmlhttp.send();
          }
          $("#currentPlayerRating").text(currentPlayerRating);
          $("#opponentRating").text(opponentRating);
          var parameterNames = ["action", "userName", "rating", "timeControl"];
          var parameterValues = [UPDATERATING, userName, currentPlayerRating, timeControl];
          var queryString = getQueryString(parameterNames, parameterValues);
          xmlhttp=new XMLHttpRequest();
          xmlhttp.open("POST","./php/User.php?"+queryString,true);
          xmlhttp.send();
     }
}



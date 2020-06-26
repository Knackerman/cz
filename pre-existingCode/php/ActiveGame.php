<?php
    require_once('../php/Includes.php');

    $action = $_GET['action'];
    $response = new Response();
    $activeGame;
    if($action==CURRENTPLAYERMOVE)
    {
        $activeGame = new ActiveGame($_GET['gameId'], $_GET['userName'], $_GET['turn'], $_GET['source'], $_GET['destination'], $_GET['whiteTimeRemaining'], $_GET['blackTimeRemaining']);
        $activeGame->MakePlayerMove();
    }
    if($action==GETOPPONENTMOVE)
    {
        $activeGame = new ActiveGame($_GET['gameId'], $_GET['userName'], $_GET['turn'], "", "", "", "");
        $to = $activeGame->GetPlayerMove();
        if($to != null)
        {
            $response->GameID = $to->game_id;
            $response->Source = $to->source;
            $response->Destination = $to->destination;
            $response->WhiteTimeRemaining = $to->white_time_remaining;
            $response->BlackTimeRemaining = $to->black_time_remaining;
            $response->Turn = $to->turn;
            echo json_encode($response);
        }
    }

    class ActiveGame
    {
        private $_gameID;
        private $_userName;
        private $_source;
        private $_destination;
        private $_whiteTimeRemaining;
        private $_blackTimeRemaining;
        private $_turn;
        private $_activeGameDAO;
        private $_gameBaseDAO;

        function __construct($gameID, $userName, $turn, $source, $destination, $whiteTimeRemaining, $blackTimeRemaining)
        {
            $this->_gameID = $gameID;
            $this->_userName = $userName;
            $this->_source = $source;
            $this->_destination = $destination;
            $this->_whiteTimeRemaining = $whiteTimeRemaining;
            $this->_blackTimeRemaining = $blackTimeRemaining;
            $this->_turn = $turn;
            $this->_activeGameDAO = new ActiveGameDAO();
            $this->_gameBaseDAO = new GameBaseDAO();
        }

        function MakePlayerMove()
        {
            $turn = "w";
            if($this->_turn == "w")
            {
                $turn = "b";
            }
            $columns = [ActiveGameTO::SOURCE, ActiveGameTO::DESTINATION, ActiveGameTO::TURN, ActiveGameTO::WHITETIMEREMAINING, ActiveGameTO::BLACKTIMEREMAINING, ActiveGameTO::MOVENUMBER, ActiveGameTO::STARTED];
            $values = [$this->_source, $this->_destination, $turn, $this->_whiteTimeRemaining, $this->_blackTimeRemaining, sprintf("%s +1", ActiveGameTO::MOVENUMBER), 1];
            $this->_activeGameDAO->UpdateGame($columns, $values, $this->_gameID);
            $move = sprintf("%s;%s;", $this->_source, $this->_destination);
            $sql = sprintf("update %s set %s = CONCAT(COALESCE(%s, ''), '%s') where %s = %s", GameBaseTO::TABLENAME, GameBaseTO::MOVES, GameBaseTO::MOVES, $move, GameBaseTO::GAMEID, $this->_gameID);
            $this->_gameBaseDAO->UpdateGameRaw($sql);
        }

        function GetPlayerMove()
        {
            $to = $this->_activeGameDAO->GetActiveGame($this->_userName);
            return $to;
        }
    }
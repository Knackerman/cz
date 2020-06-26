<?php
    require_once('../php/Includes.php');

    $action = $_GET['action'];
    $response = new Response();
    $gameOver;
    if($action == GAMEOVER)
    {
        $gameOver = new GameOver($_GET['gameId'], $_GET['result']);
        $gameOver->PurgeActiveGame();
        $gameOver->UpdateGameBase();
    }

    class GameOver
    {
        private $_gameID;
        private $_result;
        private $_activeGameDAO;
        private $_gameBaseDAO;

        function __construct($gameID, $result)
        {
            $this->_gameID = $gameID;
            $this->_result = $result;
            $this->_activeGameDAO = new ActiveGameDAO();
            $this->_gameBaseDAO = new GameBaseDAO();
        }

        function PurgeActiveGame()
        {
            $this->_activeGameDAO->DeleteActiveGame($this->_gameID);
        }

        function UpdateGameBase()
        {
            $sql = sprintf("update %s set %s='%s', %s=0 where %s=%s",
            GameBaseTO::TABLENAME, GameBaseTO::RESULT, $this->_result, GameBaseTO::ACTIVE, GameBaseTO::GAMEID, $this->_gameID);
            $this->_gameBaseDAO->UpdateGameRaw($sql);
        }
    }
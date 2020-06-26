<?php
    class Response
    {
        function __construct(){}
        public $Message = null;
        public $ErrorMessage = null;
        public $UserName = null;
        public $Opponent = null;
        public $GameType = null;
        public $GameMode = null;
        public $TimeControl = null;
        public $GameID = null;
        public $White = null;
        public $Black = null;
        public $WhiteTimeRemaining = null;
        public $BlackTimeRemaining = null;
        public $Turn = null;
        public $Source = null;
        public $Destination = null;
        public $Started = null;
        public $CurrentPlayerQuickRating = null;
        public $OpponentQuickRating = null;
        public $CurrentPlayerBlitzRating = null;
        public $OpponentBlitzRating = null;
    }
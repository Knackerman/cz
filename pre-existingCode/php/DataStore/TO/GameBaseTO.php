<?php

    class GameBaseTO
    {
        function __construct(){}
        
        const TABLENAME = "gamebase";
        const GAMEID = "game_id";
        const WHITE = "white";
        const BLACK = "black";
        const ACTIVE = "active";
        const DATETIMEINSERT = "datetime_insert";
        const GAMETYPE = "game_type";
        const TIMECONTROL = "time_control";
        const GAMEMODE = "game_mode";
        const RESULT = "result";
        const MOVES = "moves";
       
        public $game_id;
        public $white;
        public $black;
        public $active;
        public $datetime_insert;
        public $game_type;
        public $time_control;
        public $game_mode;
        public $result;
        public $moves;
    }
<?php

    class UserTO
    {
        function __construct(){}
        
        const TABLENAME = "user";
        const ROWID = "row_id";
        const USERNAME = "user_name";
        const DATETIMEINSERT = "datetime_insert";
        const FIRSTNAME = "first_name";
        const LASTNAME = "last_name";
        const PASSWORD = "password";
        const EMAIL = "email";
        const SESSIONID = "session_id";
        const LOGGEDIN = "logged_in";
        const QUICKRATING = "quick_rating";
        const BLITZRATING = "blitz_rating";
       
        public $row_id;
        public $user_name;
        public $datetime_insert;
        public $first_name;
        public $last_name;
        public $password;
        public $email;
        public $session_id;
        public $logged_in;
        public $quick_rating;
        public $blitz_rating;
    }

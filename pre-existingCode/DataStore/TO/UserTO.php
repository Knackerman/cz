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
        const EMAIL = "EMAIL";
       
        public $row_id;
        public $user_name;
        public $datetime_insert;
        public $first_name;
        public $last_name;
        public $password;
        public $email;
    }

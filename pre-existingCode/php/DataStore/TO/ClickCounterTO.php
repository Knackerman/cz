<?php

    class ClickCounterTO
    {
        function __construct(){}
        
        const TABLENAME = "user";
        const ROWID = "row_id";
        const PAGEID = "page_id";
        const COUNT = "count";
       
        public $row_id;
        public $page_id;
        public $count;
    }
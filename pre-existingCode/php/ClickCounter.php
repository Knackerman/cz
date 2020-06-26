<?php
    require_once('../php/Includes.php');

    $pageId = $_GET['pageId'];
    echo $_GET['pageId'];

    class CickCounter
    {
        private $_pageId;

        function __construct($pageId)
        {
            $this->_pageId = $pageId;
        }
    }
<?php
    require_once('../php/Includes.php');

    $action = $_GET['action'];
    $user;
    if($action == UPDATERATING)
    {
        $user = new User($_GET['userName'], $_GET['rating'], $_GET['timeControl']);
        $user->UpdateRating();
    }

    class User
    {
        private $_userName;
        private $_rating;
        private $_timeControl;
        private $_userDAO;

        function __construct($userName, $rating, $timeControl)
        {
            $this->_userName = $userName;
            $this->_rating = $rating;
            $this->_timeControl = $timeControl;
            $this->_userDAO = new UserDAO();
        }

        function UpdateRating()
        {
            $timeControlColumn = "";
            if($this->_timeControl == BLITZ)
            {
                $timeControlColumn = UserTO::BLITZRATING;
            }
            else
            {
                $timeControlColumn = UserTO::QUICKRATING;
            }
            $sql = sprintf("update %s set %s=%s where %s='%s'",
            UserTO::TABLENAME, $timeControlColumn, $this->_rating, UserTO::USERNAME, $this->_userName);
            $this->_userDAO->UpdateUserRaw($sql);
        }
    }
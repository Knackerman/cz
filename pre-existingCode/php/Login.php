<?php
    require_once('../php/Includes.php');

    $action = $_GET['action'];
    $response = new Response();
    if($action==LOGIN)
    {
        $login = new Login($_GET['userName'], $_GET['password']);
        if($login->ValidUser())
        {
            $login->PerformLogIn();
            $response->Message = SUCCESSFULLOGIN;
            echo json_encode($response);
        }
        else
        {
            $response->ErrorMessage = LOGINFAILED;
            echo json_encode($response);
        }
    }
    if($action==GETLOGGEDINUSER)
    {
        $login = new Login("", "");
        $userName = $login->GetLoggedInUser();
        $response->UserName = $userName;
        echo json_encode($response);
    }

    class Login
    {
        private $_userName;
        private $_password;
        private $_userDAO;

        function __construct($userName, $password)
        {
            $this->_userName = $userName;
            $this->_password = $password;
            $this->_userDAO = new UserDAO();
        }

        public function ValidUser()
        {
            $userTO = $this->_userDAO->FindUserPassword($this->_userName, $this->_password);
            return strLen($userTO->user_name) > 0;
        }

        public function PerformLogIn()
        {
            try {
                $session = new Session();
                $session->StartSession();
                $columns = [UserTO::LOGGEDIN, UserTO::SESSIONID];
                $values = [1, sprintf("%s", session_id())];
                $this->_userDAO->AddFilter(sprintf("%s=?", UserTO::USERNAME));
                $this->_userDAO->AddCommandParameter("s", $this->_userName);
                $this->_userDAO->UpdateUser($columns, $values);
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
            
        }

        public function GetLoggedInUser()
        {
            $session=new Session();
            $session->StartSession();
            return $this->_userDAO->FindUserBySession(session_id());
        }
    }
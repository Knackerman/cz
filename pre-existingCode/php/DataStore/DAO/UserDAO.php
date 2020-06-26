<?php
    require_once('Includes.php');
    class UserDAO extends BaseDAO
    {   
        function __construct(){}
        
        function AddQueryItem($queryItem)
        {
            array_push($this->QueryItems, $queryItem);
        }
        
        function AddFilter($filter)
        {
            array_push($this->Filters, $filter);
        }

        function AddCommandParameter($type, $value)
        {
            $commandParameter = new CommandParameter($type, $value);
            array_push($this->CommandParameters, $commandParameter);
        }
        
        function FindUser($userName)
        {
            $this->Connect();
            $to = new UserTO();
            $this->AddQueryItem($to::USERNAME);
            $this->AddQueryItem($to::QUICKRATING);
            $this->AddQueryItem($to::BLITZRATING);
            $this->AddFilter(sprintf("%s=?", $to::USERNAME));
            $this->AddCommandParameter("s", $userName);
            $results = $this->Select($to::TABLENAME);
            if(count($results)>0)
            {
                $to->user_name = $results[0][$to::USERNAME];
                $to->quick_rating = $results[0][$to::QUICKRATING];
                $to->blitz_rating = $results[0][$to::BLITZRATING];
            }
            $this->CleanUp();
            return $to;
        }

        function FindUserPassword($userName, $password)
        {
            $this->Connect();
            $to = new UserTO();
            $this->AddQueryItem($to::USERNAME);
            $this->AddFilter(sprintf("%s=?", $to::USERNAME));
            $this->AddFilter(sprintf("%s=?", $to::PASSWORD));
            $this->AddCommandParameter("s", $userName);
            $this->AddCommandParameter("s", Utilities::Encrypt($password));
            $results = $this->Select($to::TABLENAME);
            if(count($results)>0)
            {
                $to->user_name = $results[0][$to::USERNAME];
            }
            $this->CleanUp();
            return $to;
        }

        function FindUserBySession($session)
        {
            $ret = "";
            $this->Connect();
            $this->AddQueryItem(UserTO::USERNAME);
            $this->AddFilter(sprintf("%s=?", UserTO::SESSIONID));
            $this->AddCommandParameter("s", $session);
            $results = $this->Select(UserTO::TABLENAME);
            if(count($results)>0)
            {
                $ret = $results[0][UserTO::USERNAME];
            }
            $this->CleanUp();
            return $ret;
        }

        function InsertUser($to)
        {
            $this->Connect();
            $columns = [$to::USERNAME, $to::FIRSTNAME, $to::LASTNAME, $to::EMAIL, $to::PASSWORD];
            $values = [$to->user_name, $to->first_name, $to->last_name, $to->email, $to->password];
            $this->Insert($columns, $values, $to::TABLENAME);
            $this->CleanUp();
        }

        function UpdateUser($columns, $values)
        {
            $this->Connect();
            $this->Update($columns, $values, UserTO::TABLENAME);
            $this->CleanUp();
        }

        function UpdateUserRaw($sql)
        {
            $this->Connect();
            $this->UpdateRaw($sql);
            $this->CleanUp();
        }
    }

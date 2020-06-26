<?php
    require_once('BaseDAO.php');
    require_once(dirname(__DIR__, 2) . '/DataStore/TO/UserTO.php');
    require_once(dirname(__DIR__, 2) . '/DataStore/CommandParameter.php');
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
            $this->AddQueryItem($to::LASTNAME);
            $this->AddFilter(sprintf("%s=?", $to::USERNAME));
            $this->AddCommandParameter("s", $userName);
            $results = $this->Select($to::TABLENAME);
            if(count($results)>0)
            {
                $to->user_name = $results[0][$to::USERNAME];
            }
            return $to;
            $this->CleanUp();
        }

        function InsertUser($to)
        {
            $this->Connect();
            $sql = sprintf("insert into %s(%s,%s,%s,%s,%s)values('%s','%s','%s','%s','%s')",
            $to::TABLENAME, $to::USERNAME, $to::FIRSTNAME, $to::LASTNAME, $to::EMAIL, $to::PASSWORD,
            $to->user_name, $to->first_name, $to->last_name, $to->email, $to->password);
            $this->Insert($sql);
            $this->CleanUp();
        }
    }

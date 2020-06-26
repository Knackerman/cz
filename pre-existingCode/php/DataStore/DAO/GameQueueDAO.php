<?php
    require_once('Includes.php');
    class GameQueueDAO extends BaseDAO
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

        function AddSortItem($column, $order)
        {
            $sortItem = new SortItem($column, $order);
            array_push($this->SortItems, $sortItem);
        }

        function InsertUser($to)
        {
            $this->Connect();
            $columns = [$to::USERNAME, $to::GAMETYPE, $to::TIMECONTROL];
            $values = [$to->user_name, $to->game_type, $to->time_control];
            $this->Insert($columns, $values, $to::TABLENAME);
            $this->CleanUp();
        }

        function DeleteUser($userName)
        {
            $this->Connect();
            $this->AddFilter(sprintf("%s=?", GameQueueTO::USERNAME));
            $this->AddCommandParameter("s", $userName);
            $this->Delete(GameQueueTO::TABLENAME);
            $this->CleanUp();
        }

        function FindMatchingUser($userName, $gameType, $timeControl)
        {
            $this->Connect();
            $this->AddQueryItem(GameQueueTO::USERNAME);
            $this->AddFilter(sprintf("%s!=?", GameQueueTO::USERNAME));
            $this->AddCommandParameter("s", $userName);
            $this->AddFilter(sprintf("%s=?", GameQueueTO::OPPONENT));
            $this->AddCommandParameter("s", "");
            if($gameType != ANY)
            {
                $this->AddFilter(sprintf("%s=?", GameQueueTO::GAMETYPE));
                $this->AddCommandParameter("s", $gameType);
            }
            if($timeControl != ANY)
            {
                $this->AddFilter(sprintf("%s=?", GameQueueTO::TIMECONTROL));
                $this->AddCommandParameter("s", $timeControl);
            }
            $results = $this->Select(GameQueueTO::TABLENAME);
            $this->CleanUp();
            if(count($results)>0)
            {
               return $results[0][GameQueueTO::USERNAME];
            }
            return "";
        }

        function GetOpponent($userName)
        {
            $to = new GameQueueTO();
            $this->Connect();
            $this->AddQueryItem(GameQueueTO::OPPONENT);
            $this->AddQueryItem(GameQueueTO::GAMETYPE);
            $this->AddQueryItem(GameQueueTO::TIMECONTROL);
            $this->AddFilter(sprintf("%s=?", GameQueueTO::USERNAME));
            $this->AddCommandParameter("s", $userName);
            $this->AddFilter(sprintf("%s!=?", GameQueueTO::OPPONENT));
            $this->AddCommandParameter("s", "");
            $results = $this->Select(GameQueueTO::TABLENAME);
            $this->CleanUp();
            if(count($results)>0)
            {
               $to->opponent = $results[0][GameQueueTO::OPPONENT];
               $to->game_type = $results[0][GameQueueTO::GAMETYPE];
               $to->time_control = $results[0][GameQueueTO::TIMECONTROL];
               return $to;
            }
            return null;
        }

        function UpdateMatchingUser($columns, $values)
        {
            $this->Connect();
            $this->Update($columns, $values, GameQueueTO::TABLENAME);
            $this->CleanUp();
        }

        function GetUnmatchedUsers()
        {
            $tos = [];
            $this->Connect();
            $this->AddQueryItem(GameQueueTO::USERNAME);
            $this->AddQueryItem(GameQueueTO::OPPONENT);
            $this->AddQueryItem(GameQueueTO::GAMETYPE);
            $this->AddQueryItem(GameQueueTO::TIMECONTROL);
            $this->AddFilter(sprintf("%s=?", GameQueueTO::OPPONENT));
            $this->AddCommandParameter("s", "");
            $this->AddSortItem(GameQueueTO::ROWID, "asc");
            $results = $this->Select(GameQueueTO::TABLENAME);
            foreach ($results as &$result)
            {
                $to = new GameQueueTO();
                $to->user_name = $result[GameQueueTO::USERNAME];
                $to->game_type = $result[GameQueueTO::GAMETYPE];
                $to->time_control = $result[GameQueueTO::TIMECONTROL];
                array_push($tos, $to);
            }
            $this->CleanUp();
            return $tos;
        }
    }
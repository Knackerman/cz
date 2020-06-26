<?php
    require_once('Includes.php');
    class ActiveGameDAO extends BaseDAO
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

        function UpdateGame($columns, $values, $gameID)
        {
            $this->Connect();
            $this->AddFilter(sprintf("%s=?", ActiveGameTO::GAMEID));
            $this->AddCommandParameter("i", $gameID);
            $this->Update($columns, $values, ActiveGameTO::TABLENAME);
            $this->CleanUp();
        }

        function GetActiveGame($user)
        {
            $this->Connect();
            $to = new ActiveGameTO();
            $this->AddQueryItem(ActiveGameTO::GAMEID);
            $this->AddQueryItem(ActiveGameTO::BLACK);
            $this->AddQueryItem(ActiveGameTO::WHITE);
            $this->AddQueryItem(ActiveGameTO::TURN);
            $this->AddQueryItem(ActiveGameTO::SOURCE);
            $this->AddQueryItem(ActiveGameTO::DESTINATION);
            $this->AddQueryItem(ActiveGameTO::MOVENUMBER);
            $this->AddQueryItem(ActiveGameTO::WHITETIMEREMAINING);
            $this->AddQueryItem(ActiveGameTO::BLACKTIMEREMAINING);
            $this->AddQueryItem(ActiveGameTO::STARTED);
            $this->AddFilter(sprintf("%s=? or %s=?", ActiveGameTO::BLACK, ActiveGameTO::WHITE));
            $this->AddCommandParameter("s", $user);
            $this->AddCommandParameter("s", $user);
            $results = null;
            $results = $this->Select(ActiveGameTO::TABLENAME);
            if($results != null)
            {
                $to->game_id = $results[0][ActiveGameTO::GAMEID];
                $to->white = $results[0][ActiveGameTO::WHITE];
                $to->black = $results[0][ActiveGameTO::BLACK];
                $to->turn = $results[0][ActiveGameTO::TURN];
                $to->source = $results[0][ActiveGameTO::SOURCE];
                $to->destination = $results[0][ActiveGameTO::DESTINATION];
                $to->move_number = $results[0][ActiveGameTO::MOVENUMBER];
                $to->white_time_remaining = $results[0][ActiveGameTO::WHITETIMEREMAINING];
                $to->black_time_remaining = $results[0][ActiveGameTO::BLACKTIMEREMAINING];
                $to->started = $results[0][ActiveGameTO::STARTED];
            }
            $this->CleanUp();
            return $to;
        }

        function DeleteActiveGame($gameID)
        {
            $this->Connect();
            $this->AddFilter(sprintf("%s=?", ActiveGameTO::GAMEID));
            $this->AddCommandParameter("i", $gameID);
            $this->Delete(ActiveGameTO::TABLENAME);
            $this->CleanUp();
        }
    }
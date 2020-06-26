<?php
    require_once('Includes.php');
    class GameBaseDAO extends BaseDAO
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

        function CreateGame($to)
        {
            $this->Connect();
            $sql = sprintf("insert into %s(%s, %s, %s, %s, %s) 
            select '%s' as %s, '%s' as %s, '%s' as %s, '%s' as %s, '%s' as %s
            from %s where %s = 1 and (%s = '%s' or %s = '%s')
            HAVING COUNT(*) = 0", $to::TABLENAME, $to::WHITE, $to::BLACK, $to::GAMETYPE, $to::TIMECONTROL, $to::GAMEMODE,
            $to->white, $to::WHITE, $to->black, $to::BLACK, $to->game_type, $to::GAMETYPE, $to->time_control, $to::TIMECONTROL, $to->game_mode, $to::GAMEMODE,
            $to::TABLENAME, $to::ACTIVE, $to::WHITE, $to->white, $to::WHITE, $to->black);
            $this->InsertRaw($sql);
            $this->CleanUp();
        }

        function UpdateGame($columns, $values)
        {
            $this->Connect();
            $this->Update($columns, $values, GameBaseTO::TABLENAME);
            $this->CleanUp();
        }

        function UpdateGameRaw($sql)
        {
            $this->Connect();
            $this->UpdateRaw($sql);
            $this->CleanUp();
        }
    }
<?php
    require_once('Connection.php');
    require_once('Constants.php');
    class BaseDAO
    {
        public $con;
        function __construct(){}
        
        public $QueryItems = [];
        public $Filters = [];
        public $CommandParameters = [];
        
        function Connect()
        {
            $this->con=mysqli_connect(connection::url(),connection::user(),connection::pass(),connection::dbName());
        }
        
        function Disconnect()
        {
            mysqli_close($this->con);
        }
        
        function Select($tableName)
        {
            $sql = "";
            foreach ($this->QueryItems as &$value)
            {
                $sql = sprintf("%s %s,", $sql, $value);
            }
            $sql = sprintf("select %s from %s", substr($sql, 0, -1), $tableName);
            if(count($this->Filters)>0)
            {
                $filterSql = "";
                foreach ($this->Filters as &$value)
                {
                    $filterSql = sprintf("%s %s and ", $filterSql, $value);
                }
                $sql = sprintf("%s where %s", $sql, substr($filterSql, 0, -4));
            }
            return $this->ExecuteSelect($sql);
        }

        function ExecuteSelect($sql)
        {
            if ($stmt = $this->con->prepare($sql))
            {

                if(count($this->CommandParameters)>0)
                {
                    $types = "";
                    $values = "";
                    $params = [];
                    foreach ($this->CommandParameters as &$value)
                    {
                        $types = sprintf("%s%s", $types, $value->GetType());
                        $values = sprintf("%s%s,", $values, $value->GetValue());
                        array_push($params, $value->GetValue());
                    }
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $bindResults = $this->QueryItems;
                $stmt->bind_result(...$bindResults);
                $results = [];
                while ($stmt->fetch())
                {
                    $result = [];
                    $tempArr = [];
                    array_push($tempArr, $bindResults);
                    if(!is_null($tempArr))
                    {
                        for($i=0; $i<count($this->QueryItems); $i++)
                        {
                            $result[$this->QueryItems[$i]] = $bindResults[$i];
                        }
                        array_push($results, $result);
                    }
                }
                $stmt->close();
                return $results;
            }
        }

        function Insert($sql)
        {
            if ($stmt = $this->con->prepare($sql)) 
            {
                $stmt->execute();
                $stmt->close();
            }
        }

        function CleanUp()
        {
            $this->QueryItems = [];
            $this->CommandParameters = [];
            $this->Filters = [];
            $this->Disconnect();
        }
    }


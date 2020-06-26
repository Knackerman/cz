<?php
    require_once('Includes.php');
    class BaseDAO
    {
        public $con;
        function __construct(){}
        
        public $QueryItems = [];
        public $Filters = [];
        public $CommandParameters = [];
        public $SortItems = [];
        
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
            if(count($this->SortItems)>0)
            {
                $sortSql = "";
                foreach ($this->SortItems as &$value)
                {
                    $sortSql = sprintf("%s %s %s,", $sortSql, $value->GetColumn(), $value->GetOrder());
                }
                $sql = sprintf("%s order by %s", $sql, substr($sortSql, 0, -1));
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
            else
            {
                $err = $this->con->error;
                $err = mysqli_connect_error();
            }
        }

        function Insert($columns, $values, $tableName)
        {
            $columnSql = "";
            $valueSql = "";
            foreach ($columns as &$value)
            {
                $columnSql = sprintf("%s %s,", $columnSql, $value);
            }
            foreach ($values as &$value)
            {
                $valueSql = sprintf("%s '%s',", $valueSql, $value);
            }
            $sql = sprintf("insert into %s(%s)values(%s)", $tableName, substr($columnSql, 0, -1), substr($valueSql, 0, -1));
            if ($stmt = $this->con->prepare($sql)) 
            {
                $stmt->execute();
                $stmt->close();
            }
        }

        function Update($columns, $columnValues, $tableName)
        {
            $sql = "";
            $setSql = "";
            $filterSql = "";
            $types = "";
            $params = [];
            for($i = 0; $i < count($columns); $i++)
            {
                $setSql = sprintf("%s %s='%s',",$setSql, $columns[$i], $columnValues[$i]);
            }
            if(count($this->Filters)>0)
            {
                foreach ($this->Filters as &$value)
                {
                    $filterSql = sprintf("%s %s and ", $filterSql, $value);
                }
                $sql = sprintf("update %s set %s where %s",$tableName, substr($setSql, 0, -1), substr($filterSql, 0, -4));
                
                $values = "";
                foreach ($this->CommandParameters as &$value)
                {
                    $types = sprintf("%s%s", $types, $value->GetType());
                    $values = sprintf("%s%s,", $values, $value->GetValue());
                    array_push($params, $value->GetValue());
                }
            }
            else
            {
                $sql = sprintf("update %s set %s",$tableName, substr($setSql, 0, -1));
            }
            if ($stmt = $this->con->prepare($sql)) 
            {
                if(count($params)>0)
                {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $stmt->close();
            }
            else {
                echo "failed";
            }
        }

        function Delete($tableName)
        {
            $types = "";
            $filterSql = "";
            $params = [];
            foreach ($this->Filters as &$value)
            {
                $filterSql = sprintf("%s %s and ", $filterSql, $value);
            }
            $sql = sprintf("delete from %s where %s",$tableName, substr($filterSql, 0, -4));
            $values = "";
            foreach ($this->CommandParameters as &$value)
            {
                $types = sprintf("%s%s", $types, $value->GetType());
                $values = sprintf("%s%s,", $values, $value->GetValue());
                array_push($params, $value->GetValue());
            }
            if ($stmt = $this->con->prepare($sql)) 
            {
                if(count($params)>0)
                {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $stmt->close();
            }
        }

        function InsertRaw($sql)
        {
            if ($stmt = $this->con->prepare($sql)) 
            {
                $stmt->execute();
                $stmt->close();
            }
        }

        function UpdateRaw($sql)
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
            $this->SortItems = [];
            $this->Disconnect();
        }
    }


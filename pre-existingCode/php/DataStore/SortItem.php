<?php

    class SortItem
    {
        private $_column;
        private $_order;
        
        function __construct($column, $order)
        {
            $this->_column = $column;
            $this->_order = $order;
        }
        
        function GetColumn()
        {
            return $this->_column;
        }
        
        function GetOrder()
        {
            return $this->_order;
        }
    }
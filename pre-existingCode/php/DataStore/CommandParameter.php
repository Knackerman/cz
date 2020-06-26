<?php

    class CommandParameter
    {
        private $_type;
        private $_value;
        
        function __construct($type, $value)
        {
            $this->_type = $type;
            $this->_value = $value;
        }
        
        function GetType()
        {
            return $this->_type;
        }
        
        function GetValue()
        {
            return $this->_value;
        }
    }

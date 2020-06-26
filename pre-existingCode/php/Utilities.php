<?php
    class Utilities
    {
        public static function Encrypt($val)
        {
            $EncryptObj = new Encryption();
            return $EncryptObj->Encrypt($val);
        }

        public static function GetRandomNumber($min, $max)
        {
            return rand($min, $max);
        }

        public static function GetGameType($type1, $type2)
        {
            if($type1 != ANY)
            {
                return $type1;
            }
            if($type2 != ANY)
            {
                return $type2;
            }
            return Utilities::GetRandomType();
        }

        private static function GetRandomGameType()
        {
            $randomNumber = Utilities::GetRandomNumber(0, 1);
            if($randomNumber == 0)
            {
                return CLASSIC;
            }
            if($randomNumber == 1)
            {
                return 960;
            }
        }

        public static function GetTimeControl($type1, $type2)
        {
            if($type1 != ANY)
            {
                return $type1;
            }
            if($type2 != ANY)
            {
                return $type2;
            }
            return Utilities::GetRandomTimeControl();
        }

        private static function GetRandomTimeControl()
        {
            $randomNumber = Utilities::GetRandomNumber(0, 1);
            if($randomNumber == 0)
            {
                return BLITZ;
            }
            if($randomNumber == 1)
            {
                return QUICK;
            }
        }
    }
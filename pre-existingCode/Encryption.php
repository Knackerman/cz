<?php
    class Encryption
    {
        function __construct(){}
        function Encrypt($password)
        {
            $encrypt_method = "AES-256-CBC";
            $secret_key = 'rx4mcatkey';
            $secret_iv = 'rx4mcativ';

            // hash
            $key = hash('sha256', $secret_key);

            // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
            $iv = substr(hash('sha256', $secret_iv), 0, 16);


            $output = openssl_encrypt($password, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
            return $output;
        }
    }

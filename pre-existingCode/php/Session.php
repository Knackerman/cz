<?php
    class Session
    {
        function StartSession()
        {
            session_start();
        }
        function DestroySession()
        {
            session_destroy();
        }
    }

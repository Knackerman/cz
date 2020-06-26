<?php
    require_once('../php/Includes.php');

    $action = $_GET['action'];
    $response = new Response();
    $gameQueue;
    if($action == ADDUSERTOQUEUE)
    {
        $gameQueue = new GameQueue($_GET['userName'], $_GET['gameType'], $_GET['timeControl']);
        $gameQueue->RemoveUserFromQueue();
        $gameQueue->AddUserToQueue();
    }
    if($action == FINDOPPONENT)
    {
        $userName = $_GET['userName'];
        $gameQueue = new GameQueue($userName, "", "", "");
        $to = $gameQueue->GetOpponent();
        if($to != null)
        {
            $activeGameDAO = new ActiveGameDAO();
            $activeGameTO = $activeGameDAO->GetActiveGame($userName);
            if($activeGameTO->game_id != null)
            {
                $userDAO = new UserDAO();
                $userTO = $userDAO->FindUser($userName);
                $currentQuickRating = $userTO->quick_rating;
                $currentBlitzRating = $userTO->blitz_rating;
                $opponentQuickRating = "";
                $opponentBlitzRating = "";
                $userDAO = new UserDAO();
                if($userName == $activeGameTO->white)
                {
                    $userTO = $userDAO->FindUser($activeGameTO->black);
                    $opponentQuickRating = $userTO->quick_rating;
                    $opponentBlitzRating = $userTO->blitz_rating;
                }
                else
                {
                    $userTO = $userDAO->FindUser($activeGameTO->white);
                    $opponentQuickRating = $userTO->quick_rating;
                    $opponentBlitzRating = $userTO->blitz_rating;
                }
                $response->GameID = $activeGameTO->game_id;
                $response->White = $activeGameTO->white;
                $response->Black = $activeGameTO->black;
                $response->Turn = $activeGameTO->turn;
                $response->MoveNumber = $activeGameTO->move_number;
                $response->Source = $activeGameTO->source;
                $response->Destination = $activeGameTO->destination;
                $response->WhiteTimeRemaining = $activeGameTO->white_time_remaining;
                $response->BlackTimeRemaining = $activeGameTO->black_time_remaining;
                $response->Started = $activeGameTO->started;
                $response->GameType = $to->game_type;
                $response->TimeControl = $to->time_control;
                $response->CurrentPlayerQuickRating = $currentQuickRating;
                $response->CurrentPlayerBlitzRating = $currentBlitzRating;
                $response->OpponentPlayerQuickRating = $opponentQuickRating;
                $response->OpponentPlayerBlitzRating = $opponentBlitzRating;
                echo json_encode($response);
            }
        }
        else
        {
            $response->Message = STILLSEARCHING;
            echo json_encode($response);
        }
    }
    if($action == REMOVEUSERFROMQUEUE)
    {
        $gameQueue = new GameQueue($_GET['userName'], "", "", "");
        $gameQueue->RemoveUserFromQueue();
    }
    $gameQueue->PerformMatching();

    class GameQueue
    {
        private $_userName;
        private $_gameType;
        private $_timeControl;
        private $_gameQueueDAO;

        function __construct($userName, $gameType, $timeControl)
        {
            $this->_userName = $userName;
            $this->_gameType = $gameType;
            $this->_timeControl = $timeControl;
            $this->_gameQueueDAO = new GameQueueDAO();
        }

        function AddUserToQueue()
        {
            $to = new GameQueueTO();
            $to->user_name = $this->_userName;
            $to->game_type = $this->_gameType;
            $to->time_control = $this->_timeControl;
            $this->_gameQueueDAO->InsertUser($to);
        }

        function FindMatchingUser()
        {
            return $this->_gameQueueDAO->FindMatchingUser($this->_userName, $this->_gameType, $this->_timeControl);
        }

        function GetOpponent()
        {
            return $this->_gameQueueDAO->GetOpponent($this->_userName);
        }

        function RemoveUserFromQueue()
        {
            $this->_gameQueueDAO->DeleteUser($this->_userName);
        }

        function SetMatchingUser($userName, $opponent)
        {
            $columns = [GameQueueTO::OPPONENT];
            $values = [$opponent];
            $this->_gameQueueDAO->AddFilter(sprintf("%s=?", GameQueueTO::USERNAME));
            $this->_gameQueueDAO->AddCommandParameter("s", $userName);
            $this->_gameQueueDAO->UpdateMatchingUser($columns, $values);
        }

        function PerformMatching()
        {
            $tos = $this->_gameQueueDAO->GetUnmatchedUsers();
            if(count($tos) > 1)
            {
                for($i=0; $i<count($tos); $i++)
                {
                    $to1 = $tos[$i];
                    if($to1->opponent == "")
                    {
                        for($j=$i+1; $j<count($tos); $j++)
                        {
                            $to2 = $tos[$j];
                            if($to2->opponent == "" && $this->IsMatch($to1, $to2))
                            {
                                $this->SetMatchingUser($to1->user_name, $to2->user_name);
                                $this->SetMatchingUser($to2->user_name, $to1->user_name);
                                $tos[$i]->opponent = $to2->user_name;
                                $tos[$j]->opponent = $to1->user_name;
                                $this->PrepareGame($to1, $to2);
                                break;
                            }
                        }
                    }
                }
            }
        }

        function PrepareGame($to1, $to2)
        {
            $gbDAO = new GameBaseDAO();
            $gbTO = new GameBaseTO();
            $randomNumber = Utilities::GetRandomNumber(0, 1);
            if($randomNumber == 0)
            {
                $gbTO->white = $to1->user_name;
                $gbTO->black = $to2->user_name;
            }
            else
            {
                $gbTO->white = $to2->user_name;
                $gbTO->black = $to1->user_name;
            }
            $gbTO->game_type = Utilities::GetGameType($to1->game_type, $to2->game_type);
            $gbTO->time_control = Utilities::GetTimeControl($to1->time_control, $to2->time_control);
            $gbTO->game_mode = PVP;
            $gbDAO->CreateGame($gbTO);
        }

        function IsMatch($to1, $to2)
        {
            $match = true;
            if($to1->game_type != $to2->game_type && $to1->game_type != ANY && $to2->game_type != ANY)
            {
                return false;
            }
            if($to1->time_control != $to2->time_control && $to1->time_control != ANY && $to2->time_control != ANY)
            {
                return false;
            }
            return $match;
        }
    }
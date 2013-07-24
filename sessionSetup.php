<?php
session_start();
include "define.php";

if(trim($_POST["username"])==""){
    $_SESSION["errorUsr"]="Please enter a screen name !";
    header("Location: ".$homepage);
}
else if(strlen(trim($_POST["username"])) > 16){
    $_SESSION["errorUsr"]="Screen name too long, screen name needs to be 16 characters or less !";
    header("Location: ".$homepage);
}
else{
    $_SESSION["nick"] = $_POST["username"];
    /*Starting new session*/
    if(isset($_POST['sessionStart'])){
    	$lengthOfID = 8;
    	//print("Starting a new session.");
    	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $lengthOfID; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        mkdir($nextDir."/".$randomString, 0777);
        file_put_contents($nextDir."/".$randomString."/no", 0, LOCK_EX);
        file_put_contents($nextDir."/".$randomString."/currentlyPlaying", 0, LOCK_EX);
        file_put_contents($nextDir."/".$randomString."/peopleHere", 1, LOCK_EX);

        //chmod($nextDir."/".$randomString."/no", 0777);
        //chmod($nextDir."/".$randomString."/currentlyPlaying", 0777);
        //chmod($nextDir."/".$randomString."/peopleHere", 0777);

        $_SESSION["sno"] = $randomString;
        //print("<br>Your session Id is $randomString.");
        header( "Location: $nextDir?id=".$_SESSION["sno"]);
    }
    /*Joining existing session*/
    else{
    	if(rtrim($_POST["sessionURL"])==""){
            $_SESSION["errorURL"]="Please enter a session URL !";
            header("Location: ".$homepage); 
        }
        else{
            $URL = $_POST["sessionURL"];
            //Without URL rewriting

            
            $temp = explode("?", $URL);
            $temp = explode("&", $temp[1]);

            foreach($temp as &$arg){
                if($arg[0]=='i' && $arg[1]=='d' && $arg[2] == '='){
                    $_SESSION["sno"] = substr($arg, 3);
                    print($_SESSION["sno"]);

                    if(!is_dir($nextDir."/".$_SESSION["sno"])){
                        $_SESSION["errorURL"]="No such session exists, the URL may be incorrect or the session may have expired due to lack of participants !";
                        header("Location: ".$homepage); 
                    }
                    else{
                        $pplHere = file_get_contents($nextDir."/".$_SESSION["sno"]."/peopleHere", LOCK_EX);
                        $pplHere++;
                        file_put_contents($nextDir."/".$_SESSION["sno"]."/peopleHere", $pplHere, LOCK_EX);
                        header( "Location: $nextDir?id=".$_SESSION["sno"]);
                    }
                    break;
                }
            }         
        }
    }
}
?>
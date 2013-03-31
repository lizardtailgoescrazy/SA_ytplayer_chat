<?php
session_start();
$nextDir = "session";
if(rtrim($_POST["username"])==""){
    $_SESSION["errorUsr"]="Please enter a username !";
    header("Location: http://localhost/sugar");

}
else{
    $_SESSION["nick"] = $_POST["username"];

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
        $_SESSION["sno"] = $randomString;
        //print("<br>Your session Id is $randomString.");
        header( "Location: $nextDir?id=".$_SESSION["sno"]);
    }
    else{
    	if(rtrim($_POST["sessionURL"])==""){
            $_SESSION["errorURL"]="Please enter a session URL !";
            header("Location: http://localhost/sugar"); 
        }
        else{
            $URL = $_POST["sessionURL"];
            $temp = explode("?", $URL);
            $temp = explode("&", $temp[1]);

            foreach($temp as &$arg){
                if($arg[0]=='i' && $arg[1]=='d' && $arg[2] == '='){
                    $_SESSION["sno"] = substr($arg, 3);
                    print($_SESSION["sno"]);
                    //Check if dir exists
                    header( "Location: $nextDir?id=".$_SESSION["sno"]);
                    break;
                }
            }
        }
    }
}



?>
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
            //Without URL rewriting

            
            $temp = explode("?", $URL);
            $temp = explode("&", $temp[1]);

            foreach($temp as &$arg){
                if($arg[0]=='i' && $arg[1]=='d' && $arg[2] == '='){
                    $_SESSION["sno"] = substr($arg, 3);
                    print($_SESSION["sno"]);

                    if(!is_dir($nextDir."/".$_SESSION["sno"])){
                        $_SESSION["errorURL"]="No such session exists, the URL may be incorrect or the session may have expired due to lack of participants !";
                        header("Location: http://localhost/sugar"); 
                    }
                    else{
                        header( "Location: $nextDir?id=".$_SESSION["sno"]);
                    }
                    break;
                }
            }
            //If you reach here invalid session URL

            
            //With URL rewriting
            /*
            $URL = str_replace('\\', '/', $URL);
            $temp = explode("/", $URL);
            $i = 0;
            foreach($temp as &$arg){
                if($arg == $nextDir){
                    break;
                }
                $i++;
            }
            if($i >= sizeof($temp)){
                $_SESSION["errorURL"]="No such session exists, the URL may be incorrect or the session may have expired due to lack of participants !";
                header("Location: http://localhost/sugar"); 
            }
            else{

                $_SESSION["sno"] = $temp[$i+1];

                if(!is_dir($nextDir."/".$_SESSION["sno"])){
                    $_SESSION["errorURL"]="No such session exists, the URL may be incorrect or the session may have expired due to lack of participants !";
                    header("Location: http://localhost/sugar"); 
                }
                else{
                    header( "Location: $nextDir/".$_SESSION["sno"]);
                }
                
            }  */         
        }
    }
}


?>
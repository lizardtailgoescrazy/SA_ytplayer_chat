<?php    
	session_start();
	print($_SESSION["sid"]."/peopleHere");
    $pplHere = file_get_contents($_SESSION["sid"]."/peopleHere", LOCK_EX);
    $pplHere--;
    file_put_contents($_SESSION["sid"]."/peopleHere", $pplHere, LOCK_EX);
?>
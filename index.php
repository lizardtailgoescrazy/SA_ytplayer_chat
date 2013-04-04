<?php
session_start();

$directURLFlag = false;
if(isset($_SESSION["directURL"])){
	$directURLFlag = true;
	$URL = $_SESSION["directURL"];
	unset($_SESSION["directURL"]);
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Sugary Asphalt</title>
	<link rel="icon" type="image/png" href="res/icon.png" >
	<link type="text/css" rel="stylesheet" href="style/main.css" />
	<link href='http://fonts.googleapis.com/css?family=Noto+Sans' rel='stylesheet' type='text/css'>
	<script type="text/javascript">
		function checkForJoin(){
			if($("#username").val() == ""){
				alert("Please enter a username.");
				return false;
			}
			if($("#sessionURL").val() == ""){
				alert("Please enter a session URL.");
				return false;
			}

			return true;
		}

		function checkForNew(){
			if($("#username").val() == ""){
				alert("Please enter a username.");
				return false;
			}
			return true;
		}

	</script>


</head>

<body>
	<div id="content">
		<h2>Sugary Asphalt</h2>
		<?php if(isset($_SESSION["errorUsr"])){
			print("<div id=\"errorSpace\">");
			print("**".$_SESSION["errorUsr"]);
			print("</div>");
			unset($_SESSION["errorUsr"]);
		} ?>
		<form id="sessionStuff" method="POST" action="sessionSetup.php">
			<input type="text" name="username" id="username" placeholder="Please enter a username..."></input><br><hr>
			<?php if($directURLFlag == false){ ?>
			<input type="submit" name="sessionStart" id="sessionStart" onclick="return checkForNew();" value="Start a new session" ></input><br><hr>
			<?php } ?>
			<?php if(isset($_SESSION["errorURL"])){
				print("<div id=\"errorSpace\">");
				print("**".$_SESSION["errorURL"]);
				print("</div>");
				unset($_SESSION["errorURL"]);
			} ?>

			<input type="text" name="sessionURL" id="sessionURL" placeholder="Enter session URL" <?php if($directURLFlag == true){print("value=".$URL);} ?> ></input><br><br>
			<input type="submit" name="sessionJoin" onclick="return checkForJoin();"  value="Join this session" ></input>
		</form>
	</div>
	<!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
</body>


</html>
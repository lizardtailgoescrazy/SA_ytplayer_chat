<?php
session_start();

$directURLFlag = false;
if(isset($_SESSION["directURL"])){
	$directURLFlag = true;
	$URL = $_SESSION["directURL"];
	unset($_SESSION["directURL"]);
}

$usernameMsg = "";
$urlMsg = "";

if(isset($_SESSION["errorUsr"])){
		$usernameMsg = $usernameMsg."<div class=\"errorSpace\">";
		$usernameMsg = $usernameMsg."**".$_SESSION["errorUsr"];
		$usernameMsg = $usernameMsg."</div>";
		unset($_SESSION["errorUsr"]);
	}

if(isset($_SESSION["errorURL"])){
		$urlMsg = $urlMsg."<div class=\"errorSpace\">";
		$urlMsg = $urlMsg."**".$_SESSION["errorURL"];
		$urlMsg = $urlMsg."</div>";
		unset($_SESSION["errorURL"]);
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Sugary Asphalt</title>
	<link rel="icon" type="image/png" href="res/favicon.png" >
	<link type="text/css" rel="stylesheet" href="style/main.css" />
	<link href="style/bootstrap.css" rel="stylesheet">
    <link href="style/bootstrap-responsive.css" rel="stylesheet">
</head>
	<body>
		<div class="container-narrow">
			<div class="jumbotron">
				<img class="margin_1em" src="res/logo2.png" />
				<h4 style="color:white;">Watch youtube with your friends, collaborate on a playlist together</h4>
				<h4 style="color:white;">Just pick a screen name and start watching!</h4>
				<br>
				<form class="form-horizontal" id="sessionStuff" method="POST" action="sessionSetup.php">
				<?php print $usernameMsg; ?>
					<div class="control-group">
						<div class="input-prepend" style="width: 80%;">
							<span class="add-on" style="padding: 0.5em">Screen name&nbsp;&nbsp;&nbsp;</span>
							<input type="text" name="username" id="username" placeholder="Enter screen name here"></input>
						</div>
					</div>
					<?php if($directURLFlag == false){ 
					//Dont show below if the user did not come here directly
						?>
					<div class="control-group">
						<button class="btn btn-large btn-inverse" type="submit" onClick="return checkForNew();" value="Start a new session" name="sessionStart" id="sessionStart">Start a new room!</button>
					</div>
					
					<?php 
					//End of Dont show below if the user did not come here directly
					} 
					else{
					?>
					<?php print $urlMsg;?>
					<div class="control-group">
						<div class="input-prepend" style="width: 80%;">
							<span class="add-on" style="padding: 0.5em">Room address&nbsp;</span>
							<input type="text" name="sessionURL" id="sessionURL" placeholder="Enter session URL here..." <?php if($directURLFlag == true){print("value=".$URL);} ?> ></input>
						</div>
					</div>
					<div class="control-group">
						<button class="btn btn-large btn-inverse" type="submit" onClick="return checkForJoin();" name="sessionJoin">Join this room...!</button>
					</div>
					<?php
					}
					?>
				</form>
				<div id="warningArea">
				</div>
			</div>
		</div>
		<!-- Le javascript
	    ================================================== -->
	    <!-- Placed at the end of the document so the pages load faster -->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
		<script type="text/javascript" src="js/helper.js"></script>
		<!--<script src="http://arbiter:1337/socket.io/socket.io.js"></script>-->
		<script src="http://54.218.12.208:1337/socket.io/socket.io.js"></script>
		<script type="text/javascript">
			if (checkForWebsockets() == false){
				$("#warningArea").html("<p class='errorSpace'><b>Sorry, your browser does not support websockets. Please use the latest version of Firefox/Chrome/Safari to use this web app.</b></p>");
				$("button").attr('disabled', 'disabled');
				$("input").attr('disabled', 'disabled');
			}
			if (typeof io == 'undefined') {  
			    $("#warningArea").html("<p class='errorSpace'><b>Sorry, there seems to be a problem with our servers, please refresh this page. If this problem persists, please try again after some time, we are trying to fix the problem.</b></p>");
			    $("button").attr('disabled', 'disabled');
			    $("input").attr('disabled', 'disabled');
			}
		</script>
	</body>
</html>

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
			<div class="jumbotron jumbotron_margin">
				<img class="margin_1em" src="res/logo_128.png" />
				<?php print $usernameMsg; ?>
				<form class="form-horizontal" id="sessionStuff" method="POST" action="sessionSetup.php">
					<div class="control-group">
						<input type="text" name="username" id="username" placeholder="pick a username"></input>
					</div>
					<?php if($directURLFlag == false){ 
					//Dont show below if the user did not come here directly
						?>
					<div class="control-group">
						<button class="btn" type="submit" onClick="return checkForNew();" value="Start a new session" name="sessionStart" id="sessionStart">Start a new session</button>
					</div>
					<?php 
					//End of Dont show below if the user did not come here directly
					} ?>
					<?php print $urlMsg;?>
					<div class="control-group">
						<input type="text" name="sessionURL" id="sessionURL" placeholder="Enter session URL" <?php if($directURLFlag == true){print("value=".$URL);} ?> ></input>
					</div>
					<div class="control-group">
						<button class="btn" type="submit" onClick="return checkForJoin();" name="sessionJoin">Join this session</button>
					</div>
				</form>
			</div>
			<div id="warningArea">
			</div>

		</div>
		<!-- Le javascript
	    ================================================== -->
	    <!-- Placed at the end of the document so the pages load faster -->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
		<script type="text/javascript" src="js/helper.js"></script>
		<script type="text/javascript">
			if (checkForWebsockets() == false){
				$("#warningArea").html("<p class='errorSpace'><b>Sorry, your browser does not support websockets. You will be unable to use the chat but the YouTube player will still work !</b></p>");
				$("#warningArea").addClass("jumbotron");
			}
		</script>
	</body>
</html>
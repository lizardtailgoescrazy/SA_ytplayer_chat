<?php 
session_start();
$lengthOfID = 8;

if(!isset($_SESSION["nick"]) || !isset($_SESSION["sno"])){
	$_SESSION["directURL"] =  $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	header("Location: http://localhost/sugar"); 
}
else{
$_SESSION["sid"] = $_SESSION["sno"];
unset($_SESSION["sno"]);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">

	<title>Sugary Asphalt</title>

	<link rel="icon" type="image/png" href="../res/favicon.png" >
	<link type="text/css" rel="stylesheet" href="../style/main.css" />
	<link href="../style/bootstrap.css" rel="stylesheet">
    <link href="../style/bootstrap-responsive.css" rel="stylesheet">

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
	<script type="text/javascript" src="../js/helper.js"></script>
	<!-- Javascript stuff -->
	<script type="text/javascript">
			if (checkForWebsockets() == false){
				$("#board").html("<p class='errorSpace'><b>Sorry, your browser does not support websockets. You will be unable to use the chat but the YouTube player will still work !</b></p>");
				$("#chatBox").hide();
				$("#board").attr('disabled', 'disabled');
			}

			// open connection
			var connection = new WebSocket('ws://54.244.117.108:1337');

			connection.onopen = function () {
				//enable and clear chatbox
				$("#chatBox").removeAttr('disabled');
				$("#chatBox").val("");
				var msg = {
				    type: "setup",
				    id: <?php print("\"".$_GET["id"]."\""); ?>,
				    username:   <?php print("\"".$_SESSION["nick"]."\""); ?>,
				    date: Date.now()
				};
				connection.send(JSON.stringify(msg));
			};

			connection.onerror = function (error) {
				$("#board").html("<p class='errorSpace'><b>Sorry, unable to contact the chat server.</b></p>");
			};
		</script>

		<script type="text/javascript" src="../js/websocketStuff.js"></script>
	<script type="text/javascript" src="../js/ytplayerStuff.js"></script>
	<script type="text/javascript" src="../js/overlay.js"></script>
</head>
<body>
	<div id="content" >
		<div class="jumbotron">
			<!-- Heading banner table str -->
			<table class="table_25_75">
				<tr>
					<td>
						<img src="../res/logo_32.png" />
					</td>
					<td>
						<h3 id="sessionURL">Session URL: <?php echo "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; ?></h3>
					</td>
				</tr>
			</table>
			<div id="searchStuff" style="display: none;"></div>
			<!-- Heading content table str -->
			<table>
				<tr>
					<!-- Youtube stuffs -->
					<td style="text-align: center;">
						<div id="bubble">
							<div id="ytplayer">...</div>
							<div id="controls" class="margin_1em">
								<span unselectable="on" id="vol_up" class="vol_controls">&nbsp&nbsp+&nbsp&nbsp</span>
								<span unselectable="on">vol: </span>
								<span unselectable="on" id="vol_value">100%</span>
								<span unselectable="on" id="vol_down" class="vol_controls">&nbsp&nbsp-&nbsp&nbsp</span>
								<span unselectable="on">&nbsp&nbsp&nbsp&nbsp</span>
								<span unselectable="on" id="vol_mute" class="vol_controls">&nbspmute&nbsp</span>
							</div>
						</div>
						<div id="message">Initalizing...</div>
						<div id="builder">
								<input type="text" name="URLAdd" id="URLAdd" placeholder="Enter YouTube URL here..." /><br><br>
								<button id="playlistBuilder" class="btn" onclick='addThings();' disabled="disabled">Initalizing...</button>
								<button id="searchButton" class="btn" onclick='searchThings();' >Search for a video</button>
						</div>
						<div id="videoDetails" class="margin_1em">&nbsp</div>
					</td>
					<!-- Chat stuffs -->
					<td>
						<div id="messageBoard">
							<form id="chatForm" name="chatForm" method="post" action="" onsubmit="return false;">
								<div id="board">

								</div>
								<input type="text" id="chatBox" cols=2 placeholder="Write message here...." value="Connecting to chat server..." disabled="disabled" />
							</form>
						</div>
				</td>
		</div> 
	</div>
</body>
</html>

<?php
}
?>
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

	<link rel="icon" type="image/png" href="../res/icon.png" >
	<link type="text/css" rel="stylesheet" href="../style/main.css" />
	<link href="../style/bootstrap.css" rel="stylesheet">
    <link href="../style/bootstrap-responsive.css" rel="stylesheet">

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
	<script type="text/javascript" src="stuff.js"></script>
	<script type="text/javascript">
			// Check if browser supports WebSockets
			window.WebSocket = window.WebSocket || window.MozWebSocket;

			// if browser doesn't support WebSocket
			if (!window.WebSocket) {
				$("#board").html($('<p>', { text: 'Sorry, but your browser doesn\'t '
					+ 'support WebSockets.'} ));
				$("#chatBox").hide();
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
				content.html($('<p>', { text: 'Sorry, but there\'s some problem with the connection or the server is down.</p>' } ));
			};
		</script>

		<script type="text/javascript" src="chatStuff.js"></script>
</head>
<body>
	<div id="content" >
		<div class="jumbotron">
			<!-- Heading banner table str -->
			<table class="table_25_75">
				<tr>
					<td>
						<img src="../res/logo_64.png" />
					</td>
					<td>
						<h3 id="sessionURL">Session URL: <?php echo "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; ?></h3>
					</td>
				</tr>
			</table>
			<!-- Heading content table str -->
			<table>
				<tr>
					<!-- Youtube stuffs -->
					<td style="text-align: center;">
						<div id="ytplayer">...</div>
						<div id="message">Initalizing...</div>
						<div id="builder">
								<input type="text" name="URLAdd" id="URLAdd" /><br><br>
								<button class="btn" onclick='addThings();'>Add to playlist</button>
						</div>
						<div id="videoDetails">&nbsp</div>
					</td>
					<!-- Chat stuffs -->
					<td>
						<div id="messageBoard">
							<form id="chatForm" name="chatForm" method="post" action="" onsubmit="return false;">
								<div id="board">

								</div>
								<input type="text" id="chatBox" cols=2 placeholder="write message here...." value="Connecting to chat server..." disabled="disabled" />
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
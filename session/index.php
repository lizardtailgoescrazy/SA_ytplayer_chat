<?php 
session_start();
include "../define.php";

if(!isset($_SESSION["nick"]) || !isset($_SESSION["sno"])){
	$_SESSION["directURL"] =  $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	header("Location: ".$homepage); 
}
else{
$_SESSION["sid"] = $_SESSION["sno"];
unset($_SESSION["sno"]);
$sessionURL = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
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
    <link rel="stylesheet" href="../style/autocomplete.css" />
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>

</head>
<body>
	<div id="content">
		<div class="jumbotron">
			<table class="table_25_75">
				<tr>
					<td>
						<img src="../res/logo_32.png" />
					</td>
					<td>
						<h4 id="sessionURL">Session URL: <?php echo $sessionURL; ?></h4>
					</td>
				</tr>
			</table>
			<!-- Overlay div -->
			<div id="searchStuff" style="display: none;"></div>
			<div class="row-fluid">
				<!-- Details div -->
			  	<div class="span2">
			  		<div id="activePpl" class="soft_box padding_top_bottom_1">
			  			Active Participants
			  			<hr>
			  			<ul id="activeUsers" class="no_bullets">
			  			</ul>
			  		</div>
			  		<div class="soft_box padding_top_bottom_1">
			  			Current DJ
			  			<hr>
			  			<p  id="currentDJ">&nbsp---&nbsp</p>
			  			<div id="ifImTheDJ">

			  			</div>
			  		</div>
			  	</div>
				<!-- Video div -->
			  	<div class="span5" id="video">
			  		<div id="message" class="soft_box">Initalizing...</div>
					<div id="bubble">
						<div id="ytplayer">...</div>
						<div id="controls" class="margin_1em">
							<span unselectable="on" id="vol_down" class="vol_controls">&nbsp&nbsp-&nbsp&nbsp</span>
							<span unselectable="on" id="vol_value">100%</span>
							<span unselectable="on" id="vol_up" class="vol_controls">&nbsp&nbsp+&nbsp&nbsp</span>
							<span unselectable="on">&nbsp&nbsp&nbsp&nbsp</span>
							<span unselectable="on" id="vol_mute" class="vol_controls">&nbspmute&nbsp</span>
						</div>
					</div>
					<div id="builder">
							<input type="text" name="URLAdd" id="URLAdd" placeholder="Enter YouTube URL here..." disabled="disabled" /><br><br>
							<button id="playlistBuilder" class="btn" onclick='addThings();' disabled="disabled">Initalizing...</button>
							<button id="searchButton" class="btn" onclick='searchThings();' disabled="disabled">Initalizing...</button>
					</div>
					<div id="videoDetails" class="margin_1em">&nbsp</div>
			  	</div>
			  	<!-- Chat div -->
			  	<div class="span5" id="chat-box">
			  		<div class="row-fluid">
						<div id="messageBoard">
							<form id="chatForm" name="chatForm" method="post" action="" onsubmit="return false;">
								<div id="board"><!-- Chat logs loaded here --></div>
							</form>
						</div>
							<div>
								<input type="text" id="chatBox" placeholder="Write message here...." value="Connecting to chat server..." disabled="disabled" />
							</div>
					</div>
				</div>
			</div> 
		</div>
	</div>

	<!-- Le javascript
	================================================== -->
	<script type="text/javascript" src="http://malsup.github.io/jquery.blockUI.js"></script>
	<script type="text/javascript" src="../js/helper.js"></script>
	<script type="text/javascript">
		var playlistState = "ERROR_1";
		var canWebsocket = "true";
		var checkPlaylist;
		var sessionUsername = <?php print("\"".$_SESSION["nick"]."\""); ?>;
		var connection;
		var tag;
		var firstScriptTag;
	</script>
	<script type="text/javascript" src="../js/ytplayerStuff.js"></script>
	<script type="text/javascript" src="../js/websocketStuff.js"></script>
	<script type="text/javascript" src="../js/overlay.js"></script>

</body>
</html>

<?php
}
?>

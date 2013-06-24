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
	<script type="text/javascript">
		var playlistState = "ERROR_1";
		var canWebsocket = "true";
		var checkPlaylist;
		var sessionUsername = <?php print("\"".$_SESSION["nick"]."\""); ?>;
		var socket;
		var tag;
		var firstScriptTag;
	</script>

</head>
<body>

	<!-- Top navbar -->
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner noisy_net">
			<div class="row-fluid">
				<div class="span3" style="padding: 0.5em 0px 0px 1em;" >...</div>
				<div class="span6"><h6 class="grey_text">Session URL: <?php echo $sessionURL; ?>&nbsp&nbsp <button class="btn btn-small push_up_3px" id="copy"> Copy </button></h6></div>
				<div class="span3"><h6 class="grey_text">Logged in as  <i class="icon-user icon-white"></i> <?php print($_SESSION["nick"]); ?></h6></div>
			</div>
		</div>
    </div>

    <!-- Sidebar -->

    <div id="sideyBar" class="noisy_net">
    	<div class="logo"><img style="margin: auto;" src="../res/logo.png" /></div>
    	<div class="grey_text sideyItem">
			<h6>Videos in queue: <span id="inQueue">0</span></h6>
		</div>
		<div class="grey_text sideyItem">
			<h6>Current DJ: <span id="currentDJ">&nbsp---&nbsp</span></h6>
		</div>
		<div class="grey_text sideyItem no_border" style="height:30%">
			<h6>Recent Activty: </h6>
			<div id="footerMessage" class="box_it" ><i class="icon-chevron-right"></i>  Welcome to Sugary Asphalt !</div>
		</div>
		<div class="grey_text sideyItem no_border">
			<h6>Talk about us </h6>
			<div class="row-fluid">
				<div class="span4">
					<img src="../img/twitter.png" />
				</div>
				<div class="span4">
					<img src="../img/facebook.png" />
				</div>
				<div class="span4">
					<img src="../img/reddit.png" />
				</div>
			</div>
		</div>
   	</div>
	
	<div id="bubble"></div>

	<div id="content">
		<div id="stuffs" class="jumbotron push_down">
			<!-- Overlay div -->
			<div id="searchStuff" style="display: none;">
				<div id="container">
					<div class="jumbotron">
						<div class="row-fluid">
							<div class="offset2 span6" ><input class="btn_padding full_width" type="text" placeholder="Search query or youtube URL here..." id="searchTerm" /></div>
							<div class="span2" ><button class="btn btn-inverse push_up_3px"id="searchThis" ><i class="icon-search icon-white"></i> Search</button></div>
							<div id="overlayClose" class="offset1 span1"> <i class="icon-remove-sign push_to_corner"></i> </p></div>
						</div>
						<hr>
						<div id="searchResultDiv">
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">				
				<!-- Video div -->
			  	<div class="span6" id="video">
					<div id="ytplayer">...</div>
					<div class="soft_box margin_1em grey_text"><h6 id="message">Initalizing...<h6></div>
				
					<!-- Volume Controls -->
					
					<div id="controls" class="margin_1em row-fluid">
						<button id="vol_down"  class="btn btn-inverse offset2 span2">&nbsp&nbsp-&nbsp&nbsp</button>
						<button id="vol_value" class="vol_value span2">100%</button>
						<button id="vol_up"    class="btn btn-inverse span2">&nbsp&nbsp+&nbsp&nbsp</button>
						<button id="vol_mute"  class="btn btn-inverse span2">&nbspmute&nbsp</button>
					</div>
					
					<div id="builder"class="margin_1em row-fluid">
						<button id="searchButton" class="btn btn-inverse span4 offset2" onclick='searchThings();' disabled="disabled">Initalizing...</button>
						<button id="ifImTheDJ"    class="btn btn-inverse span4" onclick="skipThis();"     disabled="disabled">Skip this video</button>
					</div>
					<div id="builder"class="margin_1em row-fluid">
						<button id="exportPl" class="btn btn-inverse span4 offset2">Export this playlist </button>
					</div>
					<div>
						...<br>
						Space for more stuffs<br>
						...<br>

							
					</div>
			  	</div>
			  	<!-- Chat div -->
			  	<div class="span6" id="chat-box">
			  		<div class="row-fluid white_bg padding_top_bottom_1 thick_border">
						<div id="messageBoard" class="span8 border_right">
							<form id="chatForm" name="chatForm" method="post" action="" onsubmit="return false;">
								<div id="board"><!-- Chat logs loaded here --></div>
							</form>
						</div>
						<div id="activePpl" class="span4">
				  			<h6>Online Now...</h6>
				  			<ul id="activeUsers" class="no_bullets">
				  			</ul>
				  		</div>
					</div>
					<div>
						<textarea cols=2 class="thick_border" style="border-radius: 0px;" id="chatBox" placeholder="Write message here...." value="Connecting to chat server..." disabled="disabled" /></textarea>
					</div>
				</div>
			</div> 
		</div>
	</div>

	<!-- Le javascript
	================================================== -->
	<script src="http://54.218.12.208:1337/socket.io/socket.io.js"></script>
	<script type="text/javascript" src="ZeroClipboard.js"></script>
	<script type="text/javascript" src="../js/helper.js"></script>
	<script type="text/javascript" src="../js/ytplayerStuff.js"></script>
	<script type="text/javascript" src="../js/socket_io.js"></script>
	<script type="text/javascript" src="../js/overlay.js"></script>
	<script type="text/javascript" >
		var clip = new ZeroClipboard.Client();
		clip.setText('<?php echo $sessionURL; ?>');
		clip.glue('copy');
	</script>

</body>
</html>

<?php
}
?>

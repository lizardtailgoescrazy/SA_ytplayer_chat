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
	<link href="style/bootstrap.css" rel="stylesheet">
    <link href="style/bootstrap-responsive.css" rel="stylesheet">
</head>

<body>
	<div class="container-narrow">
		<div class="jumbotron jumbotron_margin">
			<img src="res/logo_128.png" />
			<?php if(isset($_SESSION["errorUsr"])){
				print("<div class=\"errorSpace\">");
				print("**".$_SESSION["errorUsr"]);
				print("</div>");
				unset($_SESSION["errorUsr"]);
			} ?>
			<form class="form-horizontal" id="sessionStuff" method="POST" action="sessionSetup.php">
				<div class="control-group">
					<input type="text" name="username" id="username" placeholder="pick a username"></input>
				</div>
				<?php if($directURLFlag == false){ ?>
				<div class="control-group">
					<button class="btn" type="submit" onClick="return checkForNew();" value="Start a new session" name="sessionStart" id="sessionStart">Start a new session</button>
				</div>
				<?php } ?>
				<?php if(isset($_SESSION["errorURL"])){
					print("<span class=\"errorSpace\">");
					print("**".$_SESSION["errorURL"]);
					print("</span>");
					unset($_SESSION["errorURL"]);
				} ?>
				<div class="control-group">
					<input type="text" name="sessionURL" id="sessionURL" placeholder="Enter session URL" <?php if($directURLFlag == true){print("value=".$URL);} ?> ></input>
				</div>
				<div class="control-group">
					<button class="btn" type="submit" onClick="return checkForJoin();" name="sessionJoin">Join this session</button>
				</div>
			</form>
		</div>
	</div>
	<!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
	<script type="text/javascript">

		function trimStuff (str) {
		    str = str.replace(/^\s+/, '');
		    for (var i = str.length - 1; i >= 0; i--) {
		        if (/\S/.test(str.charAt(i))) {
		            str = str.substring(0, i + 1);
		            break;
		        }
		    }
		    return str;
		}

		function checkForJoin(){
			if(trimStuff($("#username").val()) == ""){
				$("#username").val("");
				alert("Please enter a username.");
				$("#username").focus();
				return false;
			}
			if(trimStuff($("#sessionURL").val()) == ""){
				$("#sessionURL").val("");
				alert("Please enter a session URL.");
				$("#sessionURL").focus();
				return false;
			}

			return true;
		}

		function checkForNew(){
			if(trimStuff($("#username").val()) == ""){
				$("#username").val("");
				alert("Please enter a username.");
				$("#username").focus();
				return false;
			}
			return true;
		}

	</script>
</body>


</html>
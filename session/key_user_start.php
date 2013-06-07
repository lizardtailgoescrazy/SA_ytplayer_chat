<?php
session_start();
$charSet='0123456789aAbB_cCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsSt_TuUvVwWxXyYzZ';
$key="";
for($i=0;$i<32;$i++)
{
	$key=$key.$charSet[mt_rand(0,strlen($charSet)-1)];
}
$_SESSION['KEY']=$key;

if(isset($_SESSION['LOGIN_USER']))
{
	$_SESSION['SCRNAME']=$_SESSION['LOGIN_USER'];	
	header('Location: index.php?view=Check&mode=start');
}

?>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="ZeroClipboard.js"></script>
<p><?php echo $_SESSION['KEY']; ?></p>
<div id="copy" >Copy Key to clipboard</div>
<script language="JavaScript">
	var clip = new ZeroClipboard.Client();
	clip.setText('<?php echo $_SESSION['KEY']; ?>');
	clip.glue('copy');
</script>

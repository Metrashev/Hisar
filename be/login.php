<?
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['user_data']);
require_once(dirname(__FILE__)."/../config/config.php");

require_once(dirname(__FILE__)."/../lib/db.php");
require_once(dirname(__FILE__)."/../lib/be/users.php");
Users::logout();
$_SESSION = array();
//echo $_SESSION['userID'];

function login($username,$pass) {
	$db=getDB();
	if(empty($pass)||empty($username)) {
		return 0;
	}
	
	return Users::login($username,$pass);
}

if($_POST[do_login])
{
	
	$logged = login($_POST[username], $_POST[userpassword]);
	
	if($logged)
	{		
		$_COOKIE['username']=$_POST['username'];
		header("Location: ".BE_DIR."indexMenu.php");
		exit;
	}
	

}

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?=BE_CSS_DIR;?>lib.css">	
	<script type="text/javascript">
		if (window.top!=window.self) {
			window.top.location.href = '/be/login.php';
		}
	</script>
</head>
<body topmargin="0" LeftMargin="0" rightmargin="0" marginwidth="0" marginheight="0" bgcolor="white" text="#000000" link="#336699" alink="#660000" vlink="#336699" onLoad="document.loginForm.username.focus();">
<a name="top"></a>


<form method=post name="loginForm" action="login.php">
<input type=hidden name="r" value="<?=$r?>">
<table border="0" width="100%" height="100%"  cellpadding="10" cellspacing="5">
<tr>
<td width="100%" height="100%" align="center" valign="middle">


<table style="width:300px;" align="center" class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1" ><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle" align="left" style="text-align:left">Login</td>
		<td width="1"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
	</tr>
	<tr><td  class="viewList" colspan="3"></td></tr>
</table>

<table border="0" cellspacing="0" cellpadding="15" width="300" style="width:300px" class="table" align="center" vAlign='middle'  >
<tr><td align="right">
<b>User:</b></td><td align="left"><input type="text" name="username" size="15" id=LoginInput value="<?=$_COOKIE['username']?>">
</td></tr>
<tr><td align="right" style="padding-right:5px;">
<b>Password:</b></td><td align="left"><input type="password" name="userpassword" size="15" id=LoginInput>
</td></tr>
	<tr valign=bottom>
		<td></td>
<!--		<td><a href="/?pid=23&spid=4">forgotten<br>password?</a></td>-->
		<td align="right"><input class="submit" type="submit" name="do_login" value="Login"></td>
	</tr>
	</table>
</form>

</td></tr>
</table>

</td>
</tr>
</table>


</body>
</html>

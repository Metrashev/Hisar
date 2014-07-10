<?php
session_start();
$url="about:blank";

?>
<html>
<head>

</head>
<frameset cols="*" rows="30,*" framespacing="0" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
<frame src="site_logo.php" border="0" frameborder="no"></frame>
	<frameset cols="200,*" border="0" FRAMESPACING="0" TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0" MARGINWIDTH="0">
      <frame name="menu" src="menu.php" FRAMEBORDER="0" BORDER="0"></FRAME>
      <frame name="main" src="<?=$url;?>"  scrolling="Yes" FRAMEBORDER="no" BORDER="0" ></FRAME>
    </frameset>
</frameset>
</html>

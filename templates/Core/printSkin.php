<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LNG_CURRENT?>" lang="<?=LNG_CURRENT?>">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<title><?php echo $data['PageTitle'];?></title>
	<link rel="stylesheet" type="text/css" href="<?=CSS_DIR;?>lib.css" />
	<script language="JavaScript" type="text/javascript" src="<?=JS_DIR;?>swfobject.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?=JS_DIR;?>lib.js"></script>
	<?php echo  $data['Header'] ?>
	<script type="text/javascript">
	<!--
  function PrintIt(){
  	var ns4 = (document.layers)? true:false
  	var ie4 = (document.all)? true:false
  	var ns6 = (document.getElementById)? true:false
  	if(ie4 || ns4 || ns6){
  		self.print();
  		self.close();
  	} else {
  		alert('Now right/apple click and choose the \'print\' option.')
  	}
  }
	-->
	</script>
</head>

<body onload="PrintIt()" class="Print">
<?php

echo $data['body'];

?>
</body>
</html>
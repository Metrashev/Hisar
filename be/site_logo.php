<?php

$font_size=15;
$text=$_SERVER['HTTP_HOST'];
//$text="abcdefghijklmnopqastuvxyz1234567890-=";
//$text=strtoupper($text);
$font=dirname(__FILE__)."/../lib/fe/fonts/times.ttf";
$bgr_width=30;
$bgr_height=25;

$logo_height=25;
	
?>




<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
</head>
<body style="margin:0px 10px;padding:0px;background:url(i/design/logo_bgr.png) repeat-x;">
<?php
if(!is_file(dirname(__FILE__)."/../files/site_logo.png")) {	
	$text="www.".str_replace("www.","",$text);
	$bgr=imagecreatefrompng(dirname(__FILE__)."/i/design/logo_bgr.png");
	
	$res=imagettfbbox($font_size,0,$font,$text);
	
	
	$h=abs($res[7])-abs($res[1]);
	
	$center=$logo_height-(int)($h/2)-2;
	
	
	$width=$res[2]-$res[0]+10;	
	$png=imagecreatetruecolor($width,$logo_height);
	
	$start=0;
	while ($start<$width) {
		imagecopy($png,$bgr,$start,0,0,0,$bgr_width,$bgr_height);
		$start+=$bgr_width;
	}
	
	
	//92,132,178
	$color=imagecolorallocate($png,92,132,178);
	$white=imagecolorallocate($png,255,255,255);
	imagettftext($png,$font_size,0,1,$center+1,$white,$font,$text);
	imagettftext($png,$font_size,0,0,$center,$color,$font,$text);
	imagepng($png,dirname(__FILE__)."/../files/site_logo.png");
}
?>
<a href="/" target="_blank"><img border="none" src="/files/site_logo.png" /></a>
</body>
</html>
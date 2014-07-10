<?php
$current_img_id = 0;
$gallery_first = $data['gallery'][$current_img_id];

$GLOBALS['FESkinPage']->css[] = '/templates/Gallery/gallery.css';

$str=<<<EOD
<div class="GalNavBtns">
<a href="#" onClick="PrevImg(); return false;" id="GPrevBtn"> << </a> &nbsp;
<a href="#" onClick="NextImg(); return false;" id="GNextBtn"> >> </a>
</div>



<table cellspacing=0 cellpadding=0 border='0' class="LargeImageTable">

<tr>
<td>

<a href="{$gallery_first['img']['3']}" target="_blank" id="LargeImageA"><img id="LargeImage" src="{$gallery_first['img']['2']}" alt="{$gallery_first['text']}" class="img_large"></a>

</td>
</tr></table>


<div class="GalleryContainer">
<table cellspacing=0 cellpadding=0 border="0">
<tr>
EOD;

$language='bg';
$JSArray = '';
foreach ($data['gallery'] as $k=>$v) {
	$str.=<<<EOD
<td><a href="#img{$k}" id="TNimg{$k}" onClick="ShowImageN($k); return false;"><img src="{$v['img']['1']}" alt="{$v['text']}" /></a></td>
EOD;
	$alt = str_replace("'", "\'", html_entity_decode($v['text'], ENT_QUOTES));
	$JSArray .= <<<EOD
a[$k] = new Array('{$alt}', '{$v['img']['2']}', '{$v['img']['3']}');

EOD;
}

$str.=<<<EOD
</tr>
</table>
</div>

<script>
<!--

var a = new Array();
{$JSArray}
var current = {$current_img_id};
var oLargeImg = document.getElementById("LargeImage");
var oLargeImgA = document.getElementById("LargeImageA");

var oGPrevBtn = document.getElementById("GPrevBtn");
var oGNextBtn = document.getElementById("GNextBtn");




function showImgByHash(){
	MyRe = /#img([0-9]+)/
	var b = MyRe.exec(window.location.hash);
	
	if(b && b[1]){
		ShowImageN(b[1]);
	} else {
		ShowImageN(current);
	}
}


showImgByHash();

function ShowImage(img){

		oLargeImg.src = img[1];
		oLargeImg.alt = img[0];
		oLargeImgA.href=img[2];
}

function ShowImageN(n){
  if(n<0 || n>=a.length) return;
	current = n;
	ShowImage(a[current]);
	window.location = '#img'+current;
	document.getElementById('TNimg'+current).scrollIntoView(false);
}

function NextImg(){
	current++;
	if(current>=a.length ) current=0;
	ShowImageN(current);
}

function PrevImg(){
	current--;
	if(current<0) current=a.length -1;
	ShowImageN(current);
}

-->
</script>
EOD;


echo $str;

?>
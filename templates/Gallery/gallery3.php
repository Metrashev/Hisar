<?php

$GLOBALS['FESkinPage']->css[] = '/templates/Gallery/gallery3.css';

$small = array(); $big = array(); $text = array();
$jsSmall = array(); $jsBig = array(); $jsText = array();

foreach ($data['gallery'] as $item)
{
	$small[] = $item['img'][1];
	$big[] = $item['img'][2];
	$text[] = $item['text'];
	
	$jsSmall[] = '"'.$item['img'][1].'"';
	$jsBig[]   = '"'.$item['img'][2].'"';
	$jsText[] = str_replace("'", "\'", html_entity_decode($v['text'], ENT_QUOTES));
}

$jsSmall = implode(',',$jsSmall);
$jsBig = implode(',',$jsBig);
$jsText = implode(',',$jsText);

foreach ($small as $k=>&$item)
{	
	$item = <<<EOD
		<td id="thumb{$k}"><a href="#img{$k}" onclick="ShowImage({$k})"><img src="{$item}" alt="" /></a></td>
EOD;
}
$small = implode('',$small);


?>


<script type="text/javascript" src="<?=JS_DIR;?>jquery-1.3.2.js"></script>
<script type="text/javascript" src="<?=JS_DIR;?>ui/ui.core.js"></script>
<script type="text/javascript" src="<?=JS_DIR;?>ui/ui.draggable.js"></script>
<script type="text/javascript" src="<?=JS_DIR;?>gallery3.js"></script>
<script type="text/javascript">
	var small = [<?=$jsSmall?>];
	var big = [<?=$jsBig?>];
	var text = [<?=$jsText?>];
</script>


<div id="gallery1234">
	<div class="gallery-nav">
		<a href="javascript:PrevImg();"><< prev</a>
		|
		<a href="javascript:NextImg();">next >></a>
	</div>
	<a href="javascript:NextImg();" class="picture-container"></a>

	<div class="scroll-bar">
		<div class="scroll-handler"></div>
	</div>
	
	<div class="thumbs-container">
		<div class="thumbs">
			<table cellspacing="0" cellpadding="0">
			<tr>
				<?=$small?>
				<td id="gallery-thumbs-bottom"></td>
			</tr>
			</table>		
		</div>
	</div>


</div>


<div class="galleryCont" >
<div style="font-weight:bold;">Галерия</div>
<?php

$GLOBALS['FESkinPage']->css[] = '/moo/css/slimbox.css';
$GLOBALS['FESkinPage']->js[] = '/moo/js/mootools.js';
$GLOBALS['FESkinPage']->js[] = '/moo/js/slimbox.js';
	



$table = array();
foreach ($data['gallery'] as $row){
	if(empty($row['text'])) $row['text'] = 'Увеличи';
	$table[] = <<<EOD
	<a href='{$row['img']['2']}' rel='lightbox[atomium]' ><img src='{$row['img']['1']}' alt='{$row['text']}' title='{$row['text']}' /></a>
EOD;
}

$table = convert_1d_to_2d_array($table, "Horizontal", 6);
$table = arrayToTableStyle($table);

if(empty($data['PageBar']['prev'])) {
	$data['PageBar']['prev'] = '#';
	$stylePrev = 'class="disabled" onClick="return false;"';
}
if(empty($data['PageBar']['next'])) {
	$data['PageBar']['next'] = '#';
	$styleNext = 'class="disabled" onClick="return false;"';
}

echo <<<EOD
<table class="Gallery" cellspacing="14" cellpadding="0">
<COLGROUP  span="6" width="127" />
$table
</table>

<table class="galleryPB">
<col span="3" width="1*" />
<tr>
<td><a href="{$data['PageBar']['prev']}" $stylePrev>« Prev</a></td>
<td align="center">{$data['PageBar']['current']}/{$data['PageBar']['total']}</td>
<td align="right"><a href="{$data['PageBar']['next']}" $styleNext>Next »</a></td>
</tr>
</table>
EOD;


?>
</div>
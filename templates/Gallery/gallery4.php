<script type="text/javascript" src="<?=JS_DIR;?>jquery-1.3.2.js"></script>
<script type="text/javascript" src="<?=JS_DIR;?>jquery.lightbox-0.5.js"></script>
    <script type="text/javascript">
    $(function() {
        $('#thumbs-container a').lightBox();
    });
    </script>

<?php
$GLOBALS['FESkinPage']->css[] = '/templates/Gallery/gallery4.css';
$GLOBALS['FESkinPage']->css[] = '/templates/Gallery/lightbox.css';

$small = array(); $big = array(); $text = array();
$jsSmall = array(); $jsBig = array(); $jsText = array();

foreach ($data['gallery'] as $item)
{
	$small[] = $item['img'][1];
	$big[] = $item['img'][2];
	$text[] = $item['text'];
		
	$jsSmall[] = '"'.$item['img'][1].'"';
	$jsBig[]   = '"'.$item['img'][2].'"';
	$jsText[] = str_replace("'", "\'", html_entity_decode($item['text'], ENT_QUOTES));
}

$jsSmall = implode(',',$jsSmall);
$jsBig = implode(',',$jsBig);
$jsText = implode(',',$jsText);

foreach ($small as $k=>&$item)
{	
	$item = <<<EOD
		<a href= s"{$big[$k]}" title="{$text[$k]}"><img src="{$item}" alt="{$text[$k]}" /></a>
EOD;
}
$small = implode('',$small);

?>

<div id="thumbs-container">

	<div style="padding-left: 20px">
	<a><?=$small?></a>
	</div>
</div>
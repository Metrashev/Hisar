<?php

<div id="gallerySlider">
<script type="text/javascript" src="<?=JS_DIR;?>jquery.lightbox-0.5.js"></script>
<table cellspacing="0" cellpadding="0">
<tr>
<td>
<?php
if(LNG_CURRENT=='bg'){
	$lng = '';
}
else{
	$lng = '_en';
}
$GLOBALS['FESkinPage']->css[] = '/templates/Gallery/lightbox.css';
$db = getdb();
$getGalleries = $db->getAll("SELECT * FROM gallery_head");
$count = 0;




foreach($getGalleries as $k=>$v){
	$getImg = $db->getAll("SELECT * FROM gallery WHERE page_id={$v['id']}");
	if($count>2){
		echo <<<EOD
			</tr><tr>
EOD;
		$count = 0;
	}
	echo <<<EOD
		<td>

<script type="text/javascript">
	$(function() {
   		$('a.lightbox{$v['id']} ').lightBox();
	});
</script>
		
EOD;

	$flag = false;
	foreach($getImg as $n=>$i){
		if($flag==false){
			echo <<<EOD
				<a class="lightbox{$v['id']}" href="/files/mf/gallery/{$i['id']}_img_1{$i['img']}"><img src="/files/mf/gallery_head/{$v['id']}_lead_pic_pic{$v['lead_pic']}"/></a>
EOD;
		}
		else{
			echo <<<EOD
			    <a class="lightbox{$v['id']}" href="/files/mf/gallery/{$i['id']}_img_1{$i['img']}" title="{$i['text']}"><img src="/files/mf/gallery/{$i['id']}_img_1{$i['img']}" alt="{$i['text']}" style="display:none;"/></a>
EOD;
		}
		$flag = true;
	}
	echo <<<EOD

			<h3 class="galleryH3">{$v['name']}</h3>
		</td>
EOD;
	$count++;
}
?>
</td>
	</tr>
</table>
</div>


<div>
<h1 class="subMenu"><?=$data['node']['value']?></h1>

</div>

<div id="gallerySlider">

<table cellspacing="0" cellpadding="0">
<?php
	function renderImg($cid, $galleryId, $id, $ext, $name) {
		
		$class = '';
		if(!empty($galleryId)) {
			$href = "/?cid=$cid&amp;galleryId={$galleryId}";
		}
		else{
			$href = "/files/mf/gallery/{$id}_img_2{$ext}";
			$class = 'lightBoxLink';
		}
		
		return <<<EOD
	<div class="borderWrap">
		<div class="borderImg">
		<a href="{$href}" class="{$class}" title="{$name}"><img src="/files/mf/gallery/{$id}_img_1{$ext}" alt="{$name}" class="gallerySlider"/></a>
				</div>
		<h3>{$name}</h3>
		</div>
EOD;
	}

	$sufix = LNG_CURRENT=='bg' ? '' : '_'.LNG_CURRENT;
	
	$GLOBALS['FESkinPage']->css[] = '/templates/Gallery/lightbox.css';
	$db = getdb();
	
	$cid = (int)$_GET['cid'];
	
	$galleryId = (int)$_GET['galleryId'];
	
	$result = array();
	if($galleryId) {
		$imgs = $db->getAll("SELECT * FROM gallery WHERE page_id=? ORDER BY order_field" , array($galleryId));
		if(empty($imgs)) return;
		
		foreach($imgs as $img) {	
			$result[] = renderImg($cid, 0, $img['id'], $img['img'], $img['text'.$sufix]);
		}
	}
	else {
		$getGalleries = $db->getAll("SELECT * FROM gallery_head");
		foreach($getGalleries as $k=>$v){
			$img = $db->getRow("SELECT * FROM gallery WHERE page_id={$v['id']} ORDER BY order_field LIMIT 1");
			if(empty($img)) continue;
			$result[] = renderImg($cid, $img['page_id'], $img['id'], $img['img'], $v['name'.$sufix]);
		}
	}
	
	
	$result = convert_1d_to_2d_array($result, "Horizontal", 3);
	echo arrayToTableStyle($result);
?>
</table>
</div>

<script type="text/javascript" src="<?=JS_DIR;?>jquery-1.3.2.js"></script>
<script type="text/javascript" src="<?=JS_DIR;?>jquery.lightbox-0.5.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function($){
	
		$('#gallerySlider a.lightBoxLink').lightBox();
		
	});
</script>
<?php


function renderAdOnPosition($pos,$add_sizes=true){
	$pos = (int)$pos;
	$db= getdb();
	
	$row = $db->getRow("SELECT * FROM adverts WHERE position_id='$pos' AND NOW() BETWEEN active_from_date AND active_to_date ORDER BY RAND() LIMIT 1");
	if(empty($row)) return;
	$db->Query("UPDATE adverts SET num_views = num_views + 1 WHERE id={$row['id']}");
	
	if($row['ad_type_id']==1){
		if($add_sizes) {
		$res = <<<EOD
<img src="/files/mf/adverts/{$row['id']}_ad_image_img{$row['ad_image']}" width="{$GLOBALS['AdsPositionsSize'][$row['position_id']][0]}" height="{$GLOBALS['AdsPositionsSize'][$row['position_id']][1]}" alt=""/>
EOD;
}
else {
			$res = <<<EOD
<img src="/files/mf/adverts/{$row['id']}_ad_image_img{$row['ad_image']}" alt=""/>
EOD;
}

		if($row['ad_link']) {
			if($row['target']){
				$row['target'] = "target='{$row['target']}'";
			}
			
			$row['ad_link'] = "/adsClick.php?id={$row['id']}";
			
			$res = <<<EOD
<a href="{$row['ad_link']}" {$row['target']}>$res</a>
EOD;
		}
		$res = <<<EOD
<div class="banner banner{$pos}" id="banner1{$row['id']}">$res</div>
EOD;
		return $res;
	}
	
	
	if($row['ad_type_id']==2){
		$row['ad_file']=getFileExt($row['ad_file']);
		return $res = <<<EOD
			<div class="banner banner{$pos}" id="banner1{$row['id']}">
This content requires the Macromedia Flash Player.<br/>
<a href="http://www.macromedia.com/go/getflash/">Get Flash</a>
			</div>
			<script type="text/javascript">
				var fo = new FlashObject("/files/mf/adverts/{$row['id']}_ad_file{$row['ad_file']}", "flash", "{$GLOBALS['AdsPositionsSize'][$row['position_id']][0]}", "{$GLOBALS['AdsPositionsSize'][$row['position_id']][1]}", "6", "#FFFFFF",true,"best");
				fo.useExpressInstall('/flash/expressinstall.swf');
				fo.addParam("scale", "noscale");
				fo.addParam("wmode", "transparent");
				fo.write("banner1{$row['id']}");
			</script>
EOD;
	}
	
	if($row['ad_type_id']==3){
		$res = $row['ad_text'];
		if($row['ad_link']) {
			if($row['target']){
				$row['target'] = "target='{$row['target']}'";
			}
			
			$row['ad_link'] = "/adsClick.php?id={$row['id']}";
			
			$res = <<<EOD
<a href="{$row['ad_link']}" {$row['target']}>$res</a>
EOD;
		}
		
		return $res = <<<EOD
<div class="banner banner{$pos}" id="banner1{$row['id']}">$res</div>
EOD;
	}
}

?>
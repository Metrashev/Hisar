<?php
$cids=array(
	LNG_BG => 2,
	LNG_EN => 3
);

$categories = getdb()->getArray('SELECT id,weight,value,l FROM categories WHERE pid=? AND visible=1 ORDER BY l ASC', array($cids[LNG_CURRENT]));
foreach ($categories as $key => $value) {
	if($value['weight']!=0) {
		$subcategories = getdb()->getArray('SELECT id,value,l FROM categories WHERE pid=? AND visible=1 ORDER BY l ASC', array($value['id'])); 
		foreach ($subcategories as $k => $v) {
			$categories[$key]['subcategories'][] = $v; 
		}
	}
}

$HTML = <<<EOD
<div class="siteMap">
	<ul class="tree" id="tree">
EOD;
foreach ($categories as $value) {
	$endLiTag = (!empty($value['subcategories'])) == true ? '' : '</li>';
	$classSupMenu = $endLiTag != true ? ' class="sub"' : '';
	
	$HTML .= <<<EOD
		<li><a {$classSupMenu} href="/?cid={$value['id']}" title="{$value['value']}">{$value['value']}</a>{$endLiTag}
EOD;
	if (!$endLiTag) {
		$HTML .= <<<EOD
		<ul>	
EOD;
		foreach ($value['subcategories'] as $v) {
			$HTML .= <<<EOD
			<li><a href="/?cid={$v['id']}" title="{$v['value']}">{$v['value']}</a></li>
EOD;
		}
		$HTML .= <<<EOD
		</ul></li>	
EOD;
	}
}
$HTML .= <<<EOD
</ul>
EOD;

echo $HTML;
?>
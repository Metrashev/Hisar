<div class="menu">

<?php

$nodes = $GLOBALS['FESkinPage']->MenuItems;
	
foreach($nodes as $k=>$node){
	if($node['level']!=2) continue;
	if($node['visible']!=1) continue;
	$classes = array();
	
	if($node['selected']){
			$classes[] = 'selected';
	}
	
	if($css[$node['id']])
		$classes[] = $css[$node['id']];
	

	$classes = empty($classes) ? '' : ' class="'.implode(' ',$classes).'"';
	
	echo "<a{$classes} href=\"{$node['href']}\"{$node['target']}><span>{$node['value']}</span></a>";
}
?>
	<div class="flags">
		<a href="/?cid=4" target="_self" title="Български"><img src="i/flag_bg.png" alt="BG flag" /></a>
		<a href="/?cid=9" target="_self" title="English"><img src="i/flag_en.png" alt="EN flag" /></a>
	</div>
</div>
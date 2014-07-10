
<a href="<?=LNG_CURRENT==LNG_BG ? '/' : '/?cid=9'?>" title="{#Община Хисаря#}"><img class="footerLogo" src="i/footer_logo.png" alt="{#Община Хисаря#}" /></a>
<div class="footerMenu">
<?php


$nodes = $GLOBALS['FESkinPage']->MenuItems;
//echo '<pre>'.print_r($nodes, true).'</pre>';
foreach($nodes as $k=>$node){
	if($node['level']!=2) continue;
	if($node['id']==22 || $node['id']==23) continue;
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
</div>
<div class="forEU"><p>{#Оперативна програма#}<br/>{#Отговорност#}</p></div>
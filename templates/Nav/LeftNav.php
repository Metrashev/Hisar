<div class="menu">
<?php

$MenuItems = $GLOBALS['FESkinPage']->MenuItems;
	
	
	$startLevel = 2;
	$level = $startLevel-1;
	
	foreach ($MenuItems as $i=>$item){
		if($item['visible']!=1) continue;
		if($item['level']<$startLevel) continue;
		
		$css = array();
		
		$css[] = "level".$item['level'];
		
		if($item['selected']) $css[] = "selected";
		
		if($item['level']>$level){
			$css[] = "first";
		}
		
		if($item['level']<$MenuItems[$i+1]['level']){
			$css[] = "open";
		}
		
		if($item['level']<$level){
			//for($level; $level>$item['level']; $level--)
			$css[] = "close";
		}
		
		if($item['level']>$MenuItems[$i+1]['level']){
			$css[] = "last";
		}

		$level = $item['level'];
		
		$css = implode(' ', $css);
		echo <<<EOD
<a href="{$item['href']}"{$node['target']} class="$css">{$item['value']}</a>
EOD;
	}
?>
</div>
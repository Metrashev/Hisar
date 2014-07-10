<?php
$NavLevel2 = '';
foreach($GLOBALS['FESkinPage']->MenuItems as $key => $value) {
	$select = $GLOBALS['fc']->node['id']==$value['id'] ? 'class="selected"' : '';
	if($value['level']!=3) continue;
	$NavLevel2 .= <<<EOD
		<a href="/?cid={$value['id']}" {$select}><span><span>{$value['value']}</span></span></a>
EOD;
}
if($NavLevel2) echo '<div class="subMenu">'.$NavLevel2.'</div>';
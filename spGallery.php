<gallery>
<?php

  require_once('config/config.php');
  require_once('lib/db.php');
  require_once('lib/SysUtils.php');
  require_once('lib/ErrorHandling.php');
  require_once('lib/fe/lib.php');

$cid = (int)$_GET['cid'];
$pid = (int)$_GET['pid'];

$db=getdb();
 $l=$db->getAll("select id,cid,img from gallery where cid='{$cid}' and img!=''");
 foreach ($l as $img_id=>$img){
 	echo "<img src='/files/mf/gallery/{$img['id']}_img_s{$img['img']}'/>";
 }
?>
</gallery>
















<?php
$lng = LNG_CURRENT==LNG_BG ? '' : '_en';
$db = getdb();
$gal = $db->getAssoc('SELECT id, name, name_en, parameters FROM gallery_head');


$data['gallery'] = array();

if(isset($_GET['gal']){
	$getPics = $db->$getAll("SELECT * FROM gallery WHERE page_id={$_GET['gal']} ORDER BY 'order_field' ASC");
	
	return;
}

foreach ($gal as $k=>$v)
{	
	echo <<<EOD
		<a href="/?cid=28&gal={$k}" >{$v['name'.$lng]}</a>
EOD;
	$getPics = $db->$getAll("SELECT * FROM gallery WHERE page_id={$k} ORDER BY 'order_field' ASC");
	foreach($getPics as $p=>$d){
		<img src="/files/mf/galle" />
		
	}
}

include_once 'gallery4.php';

?>

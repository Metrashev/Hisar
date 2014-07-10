<?php
if(!empty($data['title'])) $GLOBALS['FESkinPage']->PageTitle[] = $data['title'];

echo <<<EOD
<h1>{$data['title']}</h1>
{$data['body']}
EOD;


if($data['node']['php_data']['parameters']['add_coments']) {
	echo CComments::renderComments($data['id'],$data['cid'],true);
}

if($data['gallery_head_id']){
	CBOGallery::getHTML($data);
	/*
	$bo = new CBOGallery();
	$data['gallery'] = $bo->getList('id,img,text',"cid=1 AND page_id=".$data['gallery_head_id'],'order_field');
	include(dirname(__FILE__).'/../Gallery/gallery.php');
	*/
}
?>
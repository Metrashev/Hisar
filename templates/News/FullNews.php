<?php

$GLOBALS['FESkinPage']->PageTitle[] = $data['title'];

echo <<<EOD
	<h1>{$data['title']}</h1>
	{$data['body']}
EOD;

if($data['node']['php_data']['parameters']['add_coments']) {
	echo CComments::renderComments($data['id'],$data['cid'],true);
}

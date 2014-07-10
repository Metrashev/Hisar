<?php

include(dirname(__FILE__).'/config/config.php');
include(dirname(__FILE__).'/lib/db.php');

$db = getdb();
$url = $db->getOne("SELECT ad_link FROM adverts WHERE id=?",array((int)$_GET['id']));
if(empty($url)) exit();
$db->Query("UPDATE adverts SET num_clicks = num_clicks + 1 WHERE id=?",array((int)$_GET['id']));
header("Location: $url");
?>
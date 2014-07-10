<?php

  require_once('config/config.php');
  require_once('lib/db.php');
  
  $db = getdb();
  $res = $db->Query("SELECT id FROM categories WHERE visible=1");
  foreach ($res as $row){
  	echo "/?cid={$row['id']}\n";
  }
  
  $res = $db->Query("SELECT id,cid FROM static_pages WHERE def=0");
  foreach ($res as $row){
  	echo "/?cid={$row['cid']}&spid={$row['id']}\n";
  }

  $res = $db->Query("SELECT id,cid FROM news_pages WHERE is_visible=1 AND due_date<=NOW()");
  foreach ($res as $row){
  	echo "/?cid={$row['cid']}&NewsId={$row['id']}\n";
  }

?>
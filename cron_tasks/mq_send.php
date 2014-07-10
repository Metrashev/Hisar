<?php
set_time_limit(3600*24);

//tozi define e zaduljitelen
define("CRON_PRINT_MESSAGES",1);

require_once(dirname(__FILE__).'/../be/libCommon.php');

$mq=new CMQ();
$db=getdb();

if((int)date('H')==23) {
	$mq->purge();
}

$has_locked=(int)$db->getOne("select count(*) from mq_mail_heads where is_locked =1");

if($has_locked) {
	if(CRON_PRINT_MESSAGES) {
		if(is_array($result)) {
			echo "Attempt for multithreading. Mail heads have locked record";
		}		
	}
	return;
}

//do {
	$qId=(int)$db->getone("select id from mq_mail_heads where start_date<now() and is_approved=1 and status_id in (0,1) and is_locked=0 order by start_date,id limit 1");
	if($qId) {
		$db->Execute("update mq_mail_heads set is_locked=1 where id='{$qId}'");
		try {
			$result=$mq->sendQue($qId);
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}
		$db->Execute("update mq_mail_heads set is_locked=0 where id='{$qId}'");
		if(CRON_PRINT_MESSAGES) {
			if(is_array($result)) {
				echo "<hr />ERROR MAIL QUE ID={$qId}";
				echo "<br />";
				echo "<pre>";
				print_r($result);
				echo "</pre>";
			}
			else {
				echo "<hr />";
				echo "MAIL QUE ID:<b>{$qId} - {$result} items sent</b>";
				echo "<br />";	
			}
		}
		
		/*if(is_array($result)) {
			if(isset($result['stop'])&&$result['stop']) {
				break;
			}
		}*/
	}
//}while ($qId);

?>
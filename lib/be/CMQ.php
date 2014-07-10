<?php

define("MAIL_DEBUG",0);

class CMQ {

	public $head_table="mq_mail_heads";
	public $items_table="mq_mail_items";
	public $sent_table="mq_mail_sent_items";
	public $settings_table="mq_mail_config";
	public $group_table="mq_mail_groups";
	
	public $settings_id=1;
	
	function __construct() {
	}
	
	public function create($name,$start_date="") {
		if(empty($start_date)) {
			$start_date=date("Y-m-d H:i:s");
		}
		$db=getdb();
		$db->Execute("insert into {$this->head_table} (name,start_date,created_date)
			values(?,?,now())
		",array((string)$name,$start_date));
		return $db->get_id();
	}
	
	public function purge() {
		$db=getdb();
		$date=$db->getone("select DATE_SUB( now( ) , INTERVAL 7 DAY )");
		$db->execute("delete from {$this->sent_table} where date_sent < '{$date}'");		
	}
	
	public function getMailData($data) {
		require_once(dirname(__FILE__).'/../mail.php');
		$mail = new htmlMimeMail();
		$mail->setHtmlCharset('UTF-8');
		$mail->setHeadCharset('UTF-8');
		$mail->setHtmlEncoding("base64");
		
		if(!empty($data['from'])) {
			$mail->setFrom($data['from']);
		}
		if(!empty($data['subject'])) {
			$mail->setSubject($data['subject']);
		}
	
		if($data['return_path'])
			$mail->setReturnPath($data['return_path']);

		if($data['cc']!="")
			$mail->setCc($data['cc']);

		if($data['bcc']!="")
			$mail->setBcc($data['bcc']);

		if(is_array($data['attachments'])) {
			foreach ($data['attachments'] as $att) {
				$mail->addAttachment($att['file'],$att['name']);
			}
		}

		$data['body'] = eregi_replace(' href="/', " HREF=\"HTTP://{$_SERVER['HTTP_HOST']}/", $data['body']);
	
		$mail->setHtml($data['body'], null, dirname(__FILE__).'/../../');
	
		return $mail->getEncodedMail(array($data['to']));
	}
	
	private function _die() {
		throw new Exception("Invalid Que ID!");
	}
	
	public function addEmailGroup($qId,$group_id,$from,$subject,$body,$date_to_send="",$cc,$bcc,$attachments=array(),$return_path="") {
		$emails=CMailListProvider::getMLRecepients($group_id);
		$keys=CMailListProvider::getMLKeys($group_id);
		$email_column=CMailListProvider::getMLEmailColumn($group_id);
		if(!is_array($keys)) {
			foreach ($emails as $k=>$v) {
				$this->addEmail($qId,$from,$v[$email_column],$subject,$body,$date_to_send,$cc,$bcc,$attachments,$return_path);
			}
		}
		else {
			foreach ($emails as $k=>$v) {
				$this->addEmail($qId,$from,$v[$email_column],
					str_replace($keys,$v,$subject),
					str_replace($keys,$v,$body),
					$date_to_send,$cc,$bcc,$attachments,$return_path);
			}
		}		
	}
	
	public function addEmail($qId,$from,$to,$subject,$body,$date_to_send="",$cc="",$bcc="",$attachments=array(),$return_path="") {
		if(!$qId) {
			$this->_die();
		}
		if(!CValidation::is_valid_email_address($to)) {
			return;
		}
		
		$to=str_replace(array("\r\n","\n",";"),array(",",",",","),$to);
		$cc=str_replace(array("\r\n","\n",";"),array(",",",",","),$cc);
		$bcc=str_replace(array("\r\n","\n",";"),array(",",",",","),$bcc);
		$data=array(
			'to'=>$to,
			'from'=>$from,
			'subject'=>$subject,
			'body'=>$body,
			'cc'=>$cc,
			'bcc'=>$bcc,
			'return_path'=>$return_path,
			'attachments'=>$attachments
		);
		$msg=$this->getMailData($data);
		$msg=serialize($msg);
		
		$db=getdb();
		
		if(empty($date_to_send)) {
			$date_to_send=$db->getone("select start_date from {$this->head_table} where id='{$qId}'");
		}
		
		$db->execute("insert into {$this->items_table} 
		(mail_head_id,subject,from_email,to_email,cc,bcc,date_to_send)
		values(?,?,?,?,?,?,?)
		",
		array($qId,$subject,$from,$to,$cc,$bcc,$date_to_send)
		);
		$i_id=$db->get_id();
		if(!is_dir(dirname(__FILE__)."/../../files/mf/")) {
			@$d=mkdir(dirname(__FILE__)."/../../files/mf/",0777);
			chmod(dirname(__FILE__)."/../../files/mf/",0777);
		}
		if(!is_dir(dirname(__FILE__)."/../../files/mf/mq_mail_items/")) {
			@$d=mkdir(dirname(__FILE__)."/../../files/mf/mq_mail_items/",0777);
			if(!$d) {
				throw new Exception("Cannot created items folder");
				continue;
			}
			chmod(dirname(__FILE__)."/../../files/mf/mq_mail_items/",0777);
		}
		$bytes=file_put_contents(dirname(__FILE__)."/../../files/mf/mq_mail_items/{$qId}_{$i_id}.txt",$msg);
		if(!$bytes) {
			die("Недостатъчно дисково пространство");
		}
		$db->execute("update {$this->head_table} set emails_count=emails_count+1 where id='{$qId}'");
		return $i_id;
	}
	
	public function deleteEmail($qId) {
		$db=getdb();
		$items=$db->getcol("select * from {$this->items_table} where mail_head_id='{$qId}'");
		foreach ($items as $item_id) {
			@unlink(dirname(__FILE__)."/../../files/mf/mq_mail_items/{$qId}_{$item_id}.txt");
		}
		$db->execute("delete from {$this->items_table} where mail_head_id='{$qId}'");
		$db->execute("delete from {$this->head_table} where id='{$qId}'");
	}
	
	public function deleteMailItem($item_id) {
		$db=getdb();
		$qId=(int)$db->getone("select mail_head_id from {$this->items_table} where id='{$item_id}'");
		$db->execute("delete from {$this->items_table} where id='{$item_id}'");
		@unlink(dirname(__FILE__)."/../../files/mf/mq_mail_items/{$qId}_{$item_id}.txt");
		$db->execute("update {$this->head_table} set emails_count=emails_count-1 where id='{$qId}'");
	}
	
	public function getSettings() {
		return getdb()->getrow("select * from {$this->settings_table} where id='{$this->settings_id}'");
	}
	
	public function getSentCountInHour() {
		$db=getdb();
		return (int)$db->getone("select count(*) from {$this->sent_table} where date_sent >= DATE_SUB( now( ) , INTERVAL 1 HOUR ) ");
	}
	
	public function sendSingleItemTest($mail_item_id,$to) {
		$db=getdb();
		$row=$db->getrow("select * from {$this->items_table} where id='{$mail_item_id}'");
		@$b=$row['mail_body']=file_get_contents(dirname(__FILE__)."/../../files/mf/mq_mail_items/{$row['mail_head_id']}_{$row['id']}.txt");
		if(!$b) {
			return array("Cannot read /files/mf/mq_mail_items/{$row['mail_head_id']}_{$row['id']}.txt");
		}
		@$msg=unserialize($row['mail_body']);
		$pos=strpos($msg['additional_headers'],"Cc: ");
		if($pos) {
			$msg['additional_headers'] = substr($msg['additional_headers'], 0, $pos).
				substr($msg['additional_headers'], strpos($msg['additional_headers'], "\n", $pos)+1);
		}
		$pos=strpos($msg['additional_headers'],"Bcc: ");
		if($pos) {
			$msg['additional_headers'] = substr($msg['additional_headers'], 0, $pos).
				substr($msg['additional_headers'], strpos($msg['additional_headers'], "\n", $pos)+1);
		}
				
		$result=mail($to, $msg['subject'], $msg['message'], $msg['additional_headers'], $msg['additional_params']);
				
		return $result;
	}
	
	public function sendQue($qId) {
		$qId=(int)$qId;
		if(!$qId) {
			$this->_die();
		}
		
		$db=getdb();
		
		$main_row=$db->getrow("select is_approved,status_id from {$this->head_table} where id='{$qId}'");
		if(!$main_row['is_approved']) {
			return array(
				'Not approved',
			);
		}
		
		if(!in_array($main_row['status_id'],array(0,1))) {
			return array(
				'Que status does not allow sending',
			);
		}
		
		$settings=$this->getSettings();
		$limit=(int)$settings['emails_per_hour'];
		$SQL="select * from {$this->items_table} where status_id=0 and date_to_send<now() and mail_head_id='{$qId}' order by date_to_send,id";
		if($limit) {
			$limit-=$this->getSentCountInHour();
			if($limit<=0) {
				return array("Hourly Quata exceeded -> Que ID={$qId}!",'stop'=>true);
			}
			
			$SQL.=" limit {$limit}";
		}
		if($main_row['status_id']!=1) {
			$db->execute("update {$this->head_table} set status_id=1 where id='{$qId}'");	//send start
		}
		if(MAIL_DEBUG) {
			echo "<hr />";
			echo htmlspecialchars($SQL);
			echo "<hr />";
		}
		$res=$db->Query($SQL);
		$counter=0;		
		foreach ($res as $row) {
			$main_row=$db->getrow("select is_approved,status_id,delete_after_sent from {$this->head_table} where id='{$qId}'");
			if($main_row['is_approved']!=1||$main_row['status_id']!=1) {	//cancel
				$db->execute("update {$this->head_table} set status_id=3 where id='{$qId}'");
				return array(
					'Sending canceled',
				);
			}
			@$b=$row['mail_body']=file_get_contents(dirname(__FILE__)."/../../files/mf/mq_mail_items/{$qId}_{$row['id']}.txt");
			if(!$b) {
				return array("Cannot read /files/mf/mq_mail_items/{$qId}_{$row['id']}.txt");
			}
			@$msg=unserialize($row['mail_body']);
			if(!is_array($msg)||empty($msg['to'])) {
				echo "Main body for mail_item_id={$row['id']} is empty\n";
				var_dump($row);
				var_dump($main_row);
				continue;	//throw exception ???
			}
			if(MAIL_DEBUG) {
				echo "<pre>";
				print_r($msg);
				echo "</pre>";
			}
			else {
				$db->execute("update {$this->items_table} set status_id=1 where id='{$row['id']}'");
				$result=mail($msg['to'], $msg['subject'], $msg['message'], $msg['additional_headers'], $msg['additional_params']);
				if($result===true) {
					$db->execute("insert into {$this->sent_table} 
					(mail_head_id,mail_item_id,date_sent)
					values('{$qId}','{$row['id']}',now())
					");
					if($main_row['delete_after_sent']) {
						$db->execute("delete from {$this->items_table} where id='{$row['id']}'");	
						@unlink(dirname(__FILE__)."/../../files/mf/mq_mail_items/{$qId}_{$row['id']}.txt");											
					}
					else {
						$db->execute("update {$this->items_table} set status_id=2,date_sent=now() where id='{$row['id']}'");
					}
					$db->execute("update {$this->head_table} set sent_emails=sent_emails+1 where id='{$qId}'");
					$counter++;
				}
				else {
					$db->execute("update {$this->items_table} set status_id=4,date_sent=now() where id='{$row['id']}'");
				}
			}
		}
		$c=(int)$db->getone("select count(*) from {$this->items_table} where status_id=0 and mail_head_id='{$qId}'");		
		if(!$c) {
			$db->execute("update {$this->head_table} set status_id=2 where id='{$qId}'");
		}
		return $counter;
	}
	
}

?>
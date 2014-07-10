<?php

class CMailListProvider {
	
	public static $config=array(
		'custom_groups'=>array(
			-1=>"Members+Subscribers",
			-2=>"Members",
			-3=>"Subscribers",
		),
		'custom_templates'=>array(
			-1=>array("{email1}"),
			-2=>array("{email1}"),
			-3=>array("{email}"),
		),
		'custom_email_column'=>array(
			-1=>"email1",
			-2=>"email1",
			-3=>"email",
		)
	);
	
	function getMLList() {
		
		$custom_groups=CMailListProvider::$config['custom_groups'];
		$db=getdb();
		$groups=$db->getassoc("select id,name from mq_mail_groups order by name");
		if(!is_array($groups)) {
			$groups=$custom_groups;
		}
		else {
			$groups=$custom_groups+$groups;
		}
		return $groups;
	}
	
	function getMLTemplates($group_id) {
		$custom_templates=CMailListProvider::$config['custom_templates'];
		if($group_id<1) {
			if(isset($custom_templates[$group_id])) {
				return $custom_templates[$group_id];
			}
			return array();
		}
		$f=getdb()->getone("select email_fields from mq_mail_groups where id=?",array((int)$group_id));
		@$f=unserialize($f);
		$d=array();
		if(is_array($f)) {
			foreach ($f as $k=>$v) {
				$d[]=htmlspecialchars($v);			
			}
		}
		return $d;
	}
	
	function getMLKeys($group_id) {
		$custom_templates=CMailListProvider::$config['custom_templates'];
		if($group_id<1) {
			if(isset($custom_templates[$group_id])) {
				return $custom_templates[$group_id];
			}
			return "";
		}
		$db=getdb();
		$group=$db->getrow("select * from {$this->group_table} where id='{$group_id}'");
		@$keys=unserialize($group['email_fields']);
		return $keys;
	}
	
	function getMLRecepients($group_id) {
		$db=getdb();
		switch ($group_id) {	//mem+sub
			case -1: {
				$email_column=CMailListProvider::getMLEmailColumn(-2);
				$emails=$db->getall("select distinct {$email_column} from members");
				$email_column1=CMailListProvider::getMLEmailColumn(-3);
				$emails1=$db->getall("select distinct {$email_column1} as {$email_column} from subscribers");
				return array_merge($emails,$emails1);
			}
			case -2: {	//mem
				$email_column=CMailListProvider::getMLEmailColumn(-2);
				return $db->getall("select distinct {$email_column} from members");				
			}
			case -3: {	//sub
				$email_column=CMailListProvider::getMLEmailColumn(-3);
				return $db->getall("select distinct {$email_column} from subscribers");				
			}
			case 0: {
				return array();
			}
			default: {	//all other
				$e=array();
				$group=$db->getrow("select * from mq_mail_groups where id='{$group_id}'");
				@$keys=unserialize($group['email_fields']);
				@$emails=@unserialize($group['emails_list']);
				if(!is_array($emails)) {
					throw  new Exception("Invalid Email list");
				}
				/*
				foreach ($emails as $k=>$v) {
					
					$e[$k][$group['email_column']]=$v[$group['email_column']];
				}
*/
				return $emails;
			}
		}
		return array();
	}
	
	function getMLEmailColumn($group_id) {
		$db=getdb();
		$custom_groups=CMailListProvider::$config['custom_email_column'];
		if($group_id<1) {
			return isset($custom_groups[$group_id])?$custom_groups[$group_id]:"";
		}
		$group=$db->getrow("select * from mq_mail_groups where id='{$group_id}'");
		return $group['email_column'];		
	}
}

?>
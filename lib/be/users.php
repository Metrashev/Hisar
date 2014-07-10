<?php

define("GROUP_ADMIN",1);

class Users {
	static function loadUserData($userId) {
		$db=getdb();
		$user_data=$db->getrow("select * from users where id='{$userId}'");
		return $user_data;
	}
	
	static function login($username,$pass) {
		$row=getdb()->getrow("select * from users where is_active=1 and username=? and `userpass`=?",array($username,$pass));
		if(!empty($row)) {
			$_SESSION['user_id']=$row['id'];
			$_SESSION['user_data']=$row;
			$_SESSION['rights']['resources']=CUserRights::getUserResourceRights();
			$_SESSION['rights']['cids']=CUserRights::getUserCidRights();
			$_SESSION['rights']['clusters']=CUserRights::getUserClusterRights();
			setcookie('username',$username);
			return (int)$row['id'];
		}
		return 0;
	}
	
	static function getUserId() {
	
		return (int)$_SESSION['user_id'];
	}
	
	static function getAgentId() {
		$data=Users::getUserData();
		return (int)$data['agent_id'];
	}
	
	static function getUserRightsId() {
		$data=Users::getUserData();
		return (int)$data['user_rights_id'];
	}
	
	static function logout() {
		$_SESSION['user_id']=0;
		$_SESSION['user_data']=array();
	}
	
	static function getUserData() {
		if(!Users::getUserId()) {
			header("Location: ".BE_DIR);
			exit;
		}
		return $_SESSION['user_data'];
	}
	
	static function getUserUsername() {
		$data=Users::getUserData();
		return $data['username'];
	}
	
	static function getUserName() {
		$data=Users::getUserData();
		return $data['name'];
	}
	
	static function getUserEmail() {
		$data=Users::getUserData();
		return $data['emailaddress'];
	}
	
	static function is_active() {
		$data=Users::getUserData();
		return (int)$data['is_active'];
	}
	
	static function getRightResources() {
		return $_SESSION['rights']['resources'];
	}
	static function getRightCids() {
		return $_SESSION['rights']['cids'];
	}
	static function getRightClusters() {
		return $_SESSION['rights']['clusters'];
	}
	
	
}

class CUserRights {
	
	static function getGroupRights($group_id) {
		static $rows=null;
		if(is_null($rows)) {
			$rows=array();
		}
		if(!$group_id) {
			return null;
		}
		if(!isset($rows[$group_id])) {
			$rows[$group_id]=getdb()->getrow("select resources,cids,attribute_cluster_ids from user_group_rights where id='{$group_id}'");
		}
		return $rows[$group_id];
	}
	
	static function getUserResourceRights() {
		static $group_rights=null;
		if(!is_null($group_rights)) {
			return $group_rights;
		}
		$user_rights_id=Users::getUserRightsId();
		$row=CUserRights::getGroupRights($user_rights_id);
		if(is_null($row)||!isset($row['resources'])) {
			return array();
		}
		return explode(',',$row['resources']);
	}
	
	static function getUserCidRights() {
		static $group_rights=null;
		if(!is_null($group_rights)) {
			return $group_rights;
		}
		$user_rights_id=Users::getUserRightsId();
		$row=CUserRights::getGroupRights($user_rights_id);
		if(is_null($row)||!isset($row['cids'])) {
			return array();
		}
		return explode(',',$row['cids']);
	}
	
	static function getUserClusterRights() {
		static $cluster_rights=null;
		if(!is_null($cluster_rights)) {
			return $cluster_rights;
		}
		$user_rights_id=Users::getUserRightsId();
		$row=CUserRights::getGroupRights($user_rights_id);
		if(is_null($row)||!isset($row['attribute_cluster_ids'])) {
			return array();
		}
		return explode(',',$row['attribute_cluster_ids']);
	}
	
	
	
	static function checkResourceRights($resource) {
		if(!USE_OWN_USERS) return true;
		$user_rights_id=Users::getUserRightsId();
		if($user_rights_id==GROUP_ADMIN) {
			return true;
		}
		
		$r=Users::getRightResources();
		if(!is_array($r)) {
			return false;
		}
		return array_search($resource,$r)!==false;
	}
	static function checkCidRights($cid) {
		if(!USE_OWN_USERS) return true;
		$user_rights_id=Users::getUserRightsId();
		if($user_rights_id==GROUP_ADMIN) {
			return true;
		}
		
		$r=Users::getRightCids();
		if(!is_array($r)) {
			return false;
		}
		return array_search($cid,$r)!==false;
	}
	
	static function hasRights($resource,$cid='') {
		if(!USE_OWN_USERS) return true;
		$user_rights_id=Users::getUserRightsId();
		if($user_rights_id==GROUP_ADMIN) {
			return true;
		}
		if(!CUserRights::checkResourceRights($resource)) {
			return false;
		}
		if(!empty($cid)) {
			if(!CUserRights::checkCidRights($cid)) {
				return false;
			}	
		}
		return true;
	}
	
	static function checkRights($resource,$cid='') {
		if(!self::hasRights($resource,$cid='')){
				die("No rights(1)");
		}
	}
	
	
	static function checkClusterRights($ac_id,$die=true) {
		if(!USE_OWN_USERS) return true;
		$user_rights_id=Users::getUserRightsId();
		if($user_rights_id==GROUP_ADMIN) {
			return true;
		}
		$r=Users::getRightClusters();
		if(!is_array($r)) {
			if($die) {
				die("No rights(2)");
			}
			return false;
		}
		if(array_search($ac_id,$r)===false) {
			if($die) {
				die("No rights(2)");
			}
			return false;
		}
		return true;
	}
	
}

?>
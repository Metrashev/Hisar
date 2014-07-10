<?php

class CMembers {
static function loadMemberData($userId) {
		$db=getdb();
		$member_data=$db->getrow("select * from members where id='{$userId}'");
		$_SESSION['member_id']=$member_data['id'];
		$_SESSION['member_data']=$member_data;
		return $member_data;
	}
	
	static function login($username,$pass) {
		$row=getdb()->getrow("select * from members where username=? and `userpass`=? and is_active=1",array($username,$pass));
		if(!empty($row)) {
			CFESession::start();
			$_SESSION['member_id']=$row['id'];
			$_SESSION['member_data']=$row;
			return (int)$row['id'];
		}
		return 0;
	}
	
	static function getMemberId() {
	
		return (int)$_SESSION['member_id'];
	}
	
	static function logout() {
		$_SESSION['member_id']=0;
		$_SESSION['member_data']=array();
	}
	
	static function getMemberData() {
		return $_SESSION['member_data'];
	}
	
	static function getMemberUsername() {
		$data=CMembers::getMemberData();
		return $data['username'];
	}
	
	static function getMemberName() {
		$data=CMembers::getMemberData();
		return $data['name'];
	}
	
	/*static function is_active() {
		$data=Users::getUserData();
		return (int)$data['is_active'];
	}*/
	
	static function isLoged() {
		return CMembers::getMemberId()>0;
	}
}

?>
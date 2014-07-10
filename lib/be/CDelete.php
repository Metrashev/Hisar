<?php

class CBEBOBase {
	public $con;
	public $tbl_name;
	
	function __construct($con, $tbl){
		$this->con=$con;
		$this->tbl_name=$tbl;	
	}

	function delete($id) {
	
		//$this->checkRights();
		$db=getdb();
		ControlValues::deleteManagedImages($id,$this->con['controls'],false);
		ControlValues::deleteManagedFiles($id,$this->con['controls'],false);
		$db->execute("DELETE FROM {$this->tbl_name} WHERE id='{$id}'");		
	}
	
	function checkRights($operation){
		return true;
	}
}

?>
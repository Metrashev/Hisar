<?php

class CBOProducts extends ABOBase {
	
	public $fields = array(
		'id'=> array('type'=>'AutoIncrement', 'label'=>'id'),
		'name'=> array('type'=>'string', 'label'=>'Име', 'size'=>255),
		'ref_number'=> array('type'=>'string', 'label'=>'Име', 'size'=>255),
		'short_description'=> array('type'=>'string', 'label'=>'Име', 'size'=>65535),
		'description '=> array('type'=>'HTML', 'label'=>'Име', 'size'=>65535),
		'producer_id'=> array('type'=>'BORef', 'label'=>'Producer', 'table'=> 'producers'),
		'image'=> array('type'=>'ManagedImage', 'label'=>'Име', 'sizes'=>array('small', 'normal')),
		
		'producer_name'=> array('calculated'=>true, 'type'=>'string', 'label'=>'Име', 'size'=>255, 'ref'=>'rangeList', 'sql'=>'select id,name from producers WHERE id IN (?)', 'refField'=>'producer_id'),
		
		
		'href'=>array('calculated'=>true, 'ref'=>'id', 'fCall'=>array(__CLASS__, 'getDefultHref', array('cid','partner_id')), 'refField'=>'id')
		

	);
	
	public $tableName = 'products';
	public $lngData = array(
	
	'bg' => array(
		'cid'=>12,
		'labelS'=>'Партньори',
	)
	);
	
	
	static function getAttrsForCid($cid){
		$cid = (int)$cid;
		$db = getdb();
		return $db->getAssoc("SELECT id, product_attributes.* FROM product_attributes WHERE cid=$cid");
	}


}

?>

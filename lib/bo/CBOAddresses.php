<?php

class CBOAddresses extends ABOBase {
	
	static $AddressType = array(1=>'Магазин', 2=>'Сервиз');
	
	public $fields = array(
		'id'=> array('type'=>'AutoIncrement', 'label'=>'id'),
		'town'=> array('calculated'=>true, 'type'=>'string', 'label'=>'Град', 'size'=>255, 'ref'=>'fullList', 'fCall'=>array('CBOTowns', 'getList', array('name')), 'refField'=>'town_id'),
		'address'=> array('type'=>'string', 'label'=>'Име', 'size'=>255, 'lng'=>1, 'lngs'=>array( 'de'=>'en')),
		'phones'=> array('type'=>'string', 'label'=>'Име', 'size'=>255, 'lng'=>1, 'lngs'=>array( 'de'=>'en')),
		'description'=> array('type'=>'HTML', 'label'=>'Име', 'size'=>255, 'lng'=>1, 'lngs'=>array( 'de'=>'en')),
		'type'=> array('type'=>'Enum', 'label'=>'Product Type', 'array'=> 'CBOAddresses::$AddressType'),
		
		
		'href'=>array('calculated'=>true, 'ref'=>'id', 'fCall'=>array(__CLASS__, 'getDefultHref', array('cid','address_id')), 'refField'=>'id')
		

	);
	
	public $tableName = 'addresses';
	public $lngData = array(
	
	'bg' => array(
		'cid'=>6,
	),
	'en' => array(
		'cid'=>21,
	),
	'de' => array(
		'cid'=>28,
	)
	);



}

class CBOTowns extends ABOBase {
	
	public $fields = array(
		'id'=> array('type'=>'AutoIncrement', 'label'=>'id'),
		'name'=> array('type'=>'string', 'label'=>'Име', 'size'=>255, 'lng'=>1, 'lngs'=>array('de'=>'en')),
		'x'=> array('type'=>'int'),
		'y'=> array('type'=>'int'),
		'href'=>array('calculated'=>true, 'ref'=>'id', 'fCall'=>array(__CLASS__, 'getDefultHref', array('cid','town_id')), 'refField'=>'id')
				
	);
	
	public $tableName = 'towns';
	
	public $lngData = array(
	
	'bg' => array(
		'cid'=>6,
	),
	'en' => array(
		'cid'=>21,
	),
	'de' => array(
		'cid'=>28,
	)
	);
	
}

?>

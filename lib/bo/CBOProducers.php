<?php

class CBOProducers extends ABOBase {
	
	public $fields = array(
		'id'=> array('type'=>'AutoIncrement', 'label'=>'id'),
		'name'=> array('type'=>'string', 'label'=>'Име', 'size'=>255),
		'url'=> array('type'=>'string', 'label'=>'Име', 'size'=>255),
		'image'=> array('type'=>'ManagedImage', 'label'=>'Име', 'sizes'=>array('view')),
		
		
		'href'=>array('calculated'=>true, 'ref'=>'id', 'fCall'=>array(__CLASS__, 'getDefultHref', array('cid','partner_id')), 'refField'=>'id')
		

	);
	
	public $tableName = 'producers';
	public $lngData = array(
	
	'bg' => array(
		'cid'=>11,
		'labelS'=>'Партньори',
	),
	'en' => array(
		'cid'=>37,
		'labelS'=>'Partners',
	),
	'de' => array(
		'cid'=>37,
		'labelS'=>'Partners',
	)	
	);


}

?>

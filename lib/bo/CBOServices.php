<?php

class CBOServices extends ABOBase {
	public $fields = array(
		'id'=> array('type'=>'AutoIncrement', 'label'=>'id'),
		'name'=> array('type'=>'string', 'label'=>'Име', 'size'=>255, 'lng'=>1),
		'name_bg'=> array('type'=>'string', 'label'=>'Име', 'size'=>255),
		'name_en'=> array('type'=>'string', 'label'=>'Name', 'size'=>255),
		'picture'=> array('type'=>'ImgFile', 'label'=>'Picture', 'size'=>255),
		'order_field'=> array('type'=>'OrdField', 'label'=>'Order'),
		
		'href'=>array('calculated'=>true, 'ref'=>'id', 'fCall'=>array('CBOServices', 'getDefultHref', array('cid','services_id')), 'refField'=>'id')
	);

	public $tableName = 'services';

	public $lngData = array(
	
	'bg' => array(
		'cid'=>7,
		'labelP'=>'Услуги',
		'labelS'=>'Услуга',
	),
	'en' => array(
		'cid'=>26,
		'labelP'=>'Services',
		'labelS'=>'Service',
	));
	
}


?>
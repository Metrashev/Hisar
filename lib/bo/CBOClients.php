<?php

class CBOClients extends ABOBase {
	
	public $fields = array(
		'id'=> array('type'=>'AutoIncrement', 'label'=>'id'),
		'name'=> array('type'=>'string', 'label'=>'Име', 'size'=>255, 'lng'=>1),
		'name_bg'=> array('type'=>'string', 'label'=>'Име', 'size'=>255),
		'name_en'=> array('type'=>'string', 'label'=>'Name', 'size'=>255),
		'description'=> array('type'=>'HTML', 'label'=>'CV', 'size'=>65535, 'lng'=>1),
		'description_bg'=> array('type'=>'HTML', 'label'=>'CV', 'size'=>65535),
		'description_en'=> array('type'=>'HTML', 'label'=>'CV', 'size'=>65535),
		'logo_img'=> array('type'=>'ManagedImage', 'label'=>'Picture', 'sizes'=>array('normal')),
	
		'href'=>array('calculated'=>true, 'ref'=>'id', 'fCall'=>array('CBOClients', 'getDefultHref', array('cid','clients_id')), 'refField'=>'id')
	);

	public $tableName = 'clients';
	
	public $lngData = array(
	
	'bg' => array(
		'cid'=>5,
		'labelS'=>'Клиент',
		'labelP'=>'Клиенти',
	),
	'en' => array(
		'cid'=>28,
		'labelS'=>'Client',
		'labelP'=>'Clients',
	));

}

?>
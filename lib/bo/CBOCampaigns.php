<?php

class CBOCampaigns extends ABOBase {

	public $fields = array(
		'id'=> array('type'=>'AutoIncrement', 'label'=>'id'),
		'name'=> array('type'=>'string', 'label'=>'Име', 'size'=>255, 'lng'=>1),
		'name_bg'=> array('type'=>'string', 'label'=>'Име', 'size'=>255),
		'name_en'=> array('type'=>'string', 'label'=>'Name', 'size'=>255),
		'client_id'=> array('type'=>'BORef', 'label'=>'Client', 'table'=> 'clients'),
		'description'=> array('type'=>'HTML', 'label'=>'CV', 'size'=>65535, 'lng'=>1),
		'description_bg'=> array('type'=>'HTML', 'label'=>'CV', 'size'=>65535),
		'description_en'=> array('type'=>'HTML', 'label'=>'CV', 'size'=>65535),
		//'href'=>array('calculated'=>true, 'refField'=>'id'),
	'href'=>array('calculated'=>true, 'ref'=>'id', 'fCall'=>array('CBOCampaigns', 'getDefultHref', array('cid','camp_id')), 'refField'=>'id')
	);


	public $tableName = 'campaigns';
	public $lngData = array(
	
	'bg' => array(
		'cid'=>10,
		'labelS'=>'Кампания',
		'labelP'=>'Кампании',
	),
	'en' => array(
		'cid'=>36,
		'labelS'=>'Campign',
		'labelP'=>'Campigns',
	));
/*
	function calculateList($data, $fieldsToCalc){
		if(empty($data)) return $data;
		
		$data = parent::calculateList($data, $fieldsToCalc);
		
		if($fieldsToCalc['href']){
			foreach ($data as &$row){
				$row['href'] = "?cid={$this->lngData[LNG_CURRENT]['cid']}&amp;camp_id={$row['href']}";
			}
		}

		return $data;
	}	
*/
}

?>
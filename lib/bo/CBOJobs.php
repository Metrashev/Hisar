<?php

class CBOJobs extends ABOBase {
	public $fields = array(
		'id'=> array('type'=>'AutoIncrement', 'label'=>'id'),
		'name'=> array('type'=>'string', 'label'=>'Име', 'size'=>255),
		'start_date'=> array('type'=>'Date', 'label'=>'Start'),
		'end_date'=> array('type'=>'Date', 'label'=>'End'),
		'is_active'=> array('type'=>'Boolean', 'label'=>'Is active'),
		'ref_number'=> array('type'=>'string', 'label'=>'реф. но', 'size'=>255),
		'description'=> array('type'=>'HTML', 'label'=>'Описание', 'size'=>65535),
		'cities'=> array('type'=>'string', 'label'=>'Градове', 'size'=>65535),
	);


	public $tableName = 'open_jobs';
	public $lngData = array(
	
	'bg' => array(
		'cid'=>9,
		'labelP'=>'Актуални позиции',
	));
	
	function getFEConstrains(){
		return '(is_active AND now() BETWEEN start_date AND end_date)';
	}
	

}

?>
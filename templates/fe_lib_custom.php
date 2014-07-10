<?php

class CFESkinPage extends CFESkinPageBase {
	
	
	function __construct(){
		parent::__construct();
		$this->CrumbsPath[0]['value']	= LNG_CURRENT==LNG_BG ? 'начало' : 'Home';
	}

	function getData(){
		
		if(is_array($this->data['CTX'])){
			$this->data['CTX'] = implode('',$this->data['CTX']);
		} else {
			$this->data['CTX'] = '';
		}
		

		
		return parent::getData();
	}
		
}

function _dump($data) {
	echo '<pre>';
	var_dump($data);
	echo '</pre>';
}

?>
<?php

class FormProcessor{
	/* @var $dom DOMDocument*/
	public $dom;
	private $data=array();
	private $RawData=array();
	private $RawData2=array();

	
	function loadTemplate($str){
		/* @var $dom DOMDocument*/
		$this->dom=new DOMDocument("1.0", "UTF-8");
		@ $this->dom->loadHTML($str);	
	}
	
	function autoProcessFields($markRequired=true,$process_labels=true,$add_required_message=true) {
		$this->setIdAttributes();
		if($markRequired) {
			$this->markRequiredFields();	
		}
		if($process_labels) {
			$this->processMultiselectLabels();
		}
		if($add_required_message) {
			$this->addRequiredMessage();
		}
	}
	
	function setIdAttributes() {
		$xp=new DOMXPath($this->dom);
		$e=$xp->query("//*[@name]");
		if($e->length) {
			for($i=0;$i<$e->length;$i++) {
				$node=$e->item($i);
				$name=$node->getAttribute("name");
				if(empty($name)) {
					continue;
				}
				$id=$node->getAttribute("id");
				if(empty($id)) {
					if(strpos($name,"[]")!==false) {
						if(!$node->hasAttribute("multiple")) {							
							continue;
						}
					}
					$id=str_replace(array("[","]"),array("_","_"),$name);
					$it=$xp->query("//*[@id='{$id}']");
					if($it->length) {
						continue;
					}
					$node->setAttribute("id",$id);
				}
			}
		}
	}
	
	function processMultiselectLabels() {
		$xp=new DOMXPath($this->dom);
		$e=$xp->query("//select[@multiple]");
		if($e->length) {
			for($i=0;$i<$e->length;$i++) {
				$node=$e->item($i);
				$id=$node->getAttribute("id");
				if(empty($id)) {
					continue;
				}
				$label=$xp->query("//label[@for='{$id}']");
				if($label->length) {
					$label=$label->item(0);
				}
				else {
					continue;
				}
				$label->removeAttribute("for");
				$label->setAttribute("onclick","try{document.getElementById('{$id}').focus()}catch(e){}");
			}
		}
	}
	
	function markRequiredFields() {
		$xp=new DOMXPath($this->dom);
		$e=$xp->query("//*[@required]");
		if($e->length) {
			for($i=0;$i<$e->length;$i++) {
				$node=$e->item($i);
				$id=$node->getAttribute("id");
				if(empty($id)) {
					continue;
				}
				$label=$xp->query("//label[@for='{$id}']");
				if($label->length) {
					$label=$label->item(0);
				}
				else {
					continue;
				}
				$span=$this->dom->createElement('span');
				$span->nodeValue="*";
				$span->setAttribute("class","error");
				$next=$label->nextSibling;
				if($next) {
					$next->parentNode->insertBefore($span,$next);
				}
				else {
					$label->parentNode->appendChild($span);					
				}
				
			}
		}
	}
	
	function addRequiredMessage() {
		$xp=new DOMXPath($this->dom);
		$e=$xp->query("//*[@required]");
		if($e->length) {
			for($i=0;$i<$e->length;$i++) {
				$node=$e->item($i);
				$id=$node->getAttribute("id");
				if(empty($id)) {
					continue;
				}
				$required_msg=$node->getAttribute("required_msg");
				if(!empty($required_msg)) {
					continue;
				}
				
				$error_msg=$xp->query("//*[@required_id='{$id}']");
				if($error_msg->length) {
					continue;
				}
				
				$label=$xp->query("//label[@for='{$id}']");
				if($label->length) {
					$label=$label->item(0)->nodeValue;
				}
				else {
					$label='';
				}
				
				$node->setAttribute("required_msg",LNG_CURRENT==LNG_BG? "<b>{$label}</b> е задължително поле!" : "<b>{$label}</b> is required field!");

			}
		}
	}
	
	function getPostValue($data,$name) {
		$v=strpos($name,"[");
		if($v!==false) {
			$v="[".substr_replace($name,"][",$v,1);
		}
		else {
			$v='['.$name.']';
		}
		$str='$data'.$v;
		$str=str_replace("[]","",$str);
		$str=str_replace(array("[","]"),array("['","']"),$str);
		
		@eval("\$h=$str;");
		return $h;
	}
	
	function fillInputs($data,$inputs) {
		if(empty($inputs)) {
			return;
		}
		foreach ($inputs as $v) {
			/* @var $v DOMElement */
			$type=(string)$v->getAttribute("type");
			$type=strtolower($type);
			$name=$v->getAttribute("name");
			if(!$name) {
				continue;
			}
			switch ($type) {
				case "":
				case "text":
				case "password":{
					$v->setAttribute("value",htmlspecialchars(($this->getPostValue($data,$name))));
					break;
				}
				case "checkbox": {
					$val=$this->getPostValue($data,$name);
					
					if(!is_null($val)) {
						$v->setAttribute("checked","checked");
					}
					break;
				}
				case "radio": {
					$val=$this->getPostValue($data,$name);
					$val1=$v->getAttribute("value");
					if("$val"=="$val1") {
						$v->setAttribute("checked","checked");
					}
					break;
				}
			}
		}
	}
	
	function fillSelects($data,$selects) {
		if(empty($selects)) {
			return;
		}
		foreach ($selects as $v) {
			/* @var $v DOMElement */
			$name=$v->getAttribute("name");
			if(!$name) {
				continue;
			}
			if(!$v->hasChildNodes()) {
				continue;
			}
			$val=$this->getPostValue($data,$name);
			if(is_null($val)) {
				continue;
			}
			
			for($i=0;$i<$v->childNodes->length;$i++) {
				$op=$v->childNodes->item($i);
				if(!$op->tagName||$op->tagName!="option") {
					continue;
				}
				if($op->hasAttribute("value")) {
					$op_val=$op->getAttribute("value");
				} else {
					$op_val=trim($op->nodeValue);
				}

				if($op->tagName&&strtolower($op->tagName)=="option") {
					if(is_array($val)) {
						if(in_array($op_val,$val)) {
							$op->setAttribute("selected","true");
						}
					}
					else {
						if("$op_val"=="$val") {
							$op->setAttribute("selected","true");
							break;
						}
					}
				}
			}
		}
	}
	
	function fillText($data,$text_fields) {
		if(empty($text_fields)) {
			return;
		}
		foreach ($text_fields as $v) {
			$name=$v->getAttribute("name");
			if(!$name) {
				continue;
			}
			$val=(string)$this->getPostValue($data,$name);
			if(!empty($val)) {
				//$v->nodeValue=str_replace("&","&amp;",$val);
				/*
					Тук се прави двойно енкодване, заради html_entity_decode при getHTML метода.
				*/
				$val=str_replace(array("&","<",">"),array("&amp;","&lt;","&gt;"),$val);
				$v->nodeValue=htmlspecialchars($val);
			}
		}
	}
	
	function is_valid_email($email) {
		return ereg("^[^@]+@([0-9a-zA-Z][0-9a-zA-Z-]*\.)+[a-zA-Z]{2,4}$", $email);
	}
	
	function fillData($data) {
		$this->data=$data;
		// Za elementi koito sa imenuvani name[] PHP-to ni e precakalo i veche e slozilo indexi name[0] name[1] i t.n.
		$tmp = explode('&',http_build_query($data,'','&'));
		foreach ($tmp as $v){
			list($k, $v) = split('=', $v, 2);
			$k=urldecode($k);
			$v=urldecode($v);
			$this->RawData[$k] = $v;
			/*
			if(substr($k, -3)=='[0]'){
				$k = substr($k, 0, -3);
				if($data[])
			}
			$this->RawData2[$k] = $v;
			*/
		}
		
		$inputs=$this->dom->getElementsByTagName("input");
		$this->fillInputs($data,$inputs);
		$this->fillSelects($data,$this->dom->getElementsByTagName("select"));
		$this->fillText($data,$this->dom->getElementsByTagName("textarea"));
	}
	
	
	function regexpNode($node,$insert_errors=true) {
		
		$errors = array();
		
		$validation = (string)$node->getAttribute("regexp");
		if(!$validation) return $errors;
		
		$value=(string)$node->getAttribute("value");
		if(!$value) return $errors;
		
		$id=(string)$node->getAttribute("id");
		
		$validate_msg=(string)$node->getAttribute("regexp_msg");
		
		switch ($validation){
			case "email" : {
				$validation="/^[^@]+@([0-9a-zA-Z][0-9a-zA-Z-]*\.)+[a-zA-Z]{2,4}$/";
				break;
			}
			case "digits" : {
				$validation="/^[0-9]+$/";
				break;
			}
		}
		
		if(preg_match($validation,$value)==false) {
			$errors[$id]=$validate_msg;

			if($insert_errors) {
				$this->insertError($id, 'validate_id');
			}
			return $errors;
		}
		$this->cleanErrors($id, 'validate_id');
		return $errors;
	}
	
	function cleanErrors($name="", $attributeName='required_id') {		
		$xp=new DOMXPath($this->dom);
		if(empty($name)) {
			$e=$xp->query("//*[@{$attributeName}]");
		}
		else {
			$e=$xp->query("//*[@{$attributeName}='{$name}']");
		}
		if(!$e->length) {
			return;
		}
		for($i=$e->length-1;$i>=0;$i--) {
			$e->item($i)->parentNode->removeChild($e->item($i));
		}
	}
		

	
	function insertError($id, $attributeName='required_id') {
		$xp=new DOMXPath($this->dom);
		$e=$xp->query("//*[@{$attributeName}='{$id}']");
		if(!$e->length) {
			return;
		}
		for($i=0;$i<$e->length;$i++) {
			$error_style=(string)$e->item($i)->getAttribute('error_style');
			if(empty($error_style)) {
				$error_style="display:block";
			}
			$e->item($i)->setAttribute("style",$error_style);
		}
		return;		
	}
	
	function validateInputs($inputs,$insert_errors) {
		if(empty($inputs)) {
			return true;
		}
		$errors=array();

		foreach ($inputs as $v) {
			/* @var $v DOMElement */
			
			$type=(string)$v->getAttribute("type");
			$type=strtolower($type);
			$name=$v->getAttribute("name");
			if(!$name) {
				continue;
			}
			$id=(string)$v->getAttribute("id");
			
			$required_msg=(string)$v->getAttribute("required_msg");
			switch ($type) {
				case "":
				case "text": 
				case "password":{
					$val=(string)$v->getAttribute("value");
					if(empty($val)) {
						if(!$v->hasAttribute("required")) {
							$this->cleanErrors($name);
							continue;
						}
						
						$errors[$id]=$required_msg;

						if($insert_errors) {
							$this->insertError($id);
						}
						continue;
					}
					
					$tmp = $this->regexpNode($v, $insert_errors);
					if(!empty($tmp)) {
						$errors = array_merge($errors, $tmp);
						continue;
					}

					break;
				}
				case "radio":
				case "checkbox": {
					$val=$this->getPostValue($this->data,$name);
					if(is_null($val)) {
						if(!$v->hasAttribute("required")) {
							continue;
						}
						$errors[$id]=$required_msg;

						if($insert_errors) {
							$this->insertError($id);
						}
						continue;
					}
					$this->cleanErrors($id);
					break;
				}
			}
		}

		return $errors;
	}
	
	function validateSelect($selects,$insert_errors) {
		if(empty($selects)) {
			return true;
		}
		$errors=array();

		foreach ($selects as $v) {
			/* @var $v DOMElement */
			if(!$v->hasAttribute("required")) {
				continue;
			}			
			$name=$v->getAttribute("name");
			if(!$name) {
				continue;
			}
			if(!$v->hasChildNodes()) {
				continue;
			}
			$val=$this->getPostValue($this->data,$name);
			$id=(string)$v->getAttribute("id");

			if(is_null($val) || "$val"=="") {
				$errors[$id]=(string)$v->getAttribute("required_msg");

				if($insert_errors) {
					$this->insertError($id);
				}
				continue;
			}

			$this->cleanErrors($id);
		}
		return $errors;
	}
	
	function validateText($text_fields,$insert_errors) {
		if(empty($text_fields)) {
			return true;
		}
		$errors=array();

		foreach ($text_fields as $v) {
			if(!$v->hasAttribute("required")) {
				continue;
			}
			$val=(string)$v->nodeValue;
			$id=(string)$v->getAttribute("id");
			if(empty($val)) {
				$errors[$id]=(string)$v->getAttribute("required_msg");
				if($insert_errors) {
					$this->insertError($id);
				}
				continue;
			}			
			$this->cleanErrors($id);
		}

		return $errors;
	}
	
	function validateGroups($text_fields, $insert_errors=true) {
		$errors=array();

		foreach ($text_fields as $v) {

			$id=(string)$v->getAttribute("id");
			$name=(string)$v->getAttribute("name");
			$min=(int)$v->getAttribute("min");
			$max=(int)$v->getAttribute("max");
			//$val=$this->getPostValue($this->data,$name);
			$val = array();
			foreach ($this->RawData as $k=>$tmp) {
				if(trim($tmp) && preg_match("/$name/",$k)) $val[]=1;
			}
			
			
			if(empty($val)) {
				$errors[$id]=(string)$v->getAttribute("required_msg");
				if($insert_errors) {
					$this->insertError($id);
				}
				continue;
			}
			$this->cleanErrors($id);
			
			if($min && count($val)<$min) {
				$errors[$id]=(string)$v->getAttribute("min_msg");
				$this->insertError($id, 'group_min_id');
			}
			if($max && count($val)>$max) {
				$errors[$id]=(string)$v->getAttribute("max_msg");
				$this->insertError($id, 'group_max_id');
			}
		}

		return $errors;
	}
	
	function validate($insert_errors=true) {
		$xp=new DOMXPath($this->dom);
		/*
		$e=$xp->query("//input[@required|@regexp]");		
		$result_inp=$this->validateInputs($e,$insert_errors);
		
		
		$result_sel=$this->validateSelect($xp->query("//select[@required]"),$insert_errors);
		
		$result_text=$this->validateText($xp->query("//textarea[@required]"),$insert_errors);
		$result_group=$this->validateGroups($xp->query("//group[@required]"),$insert_errors);
		
		
		$arr=array();
		
		if(is_array($result_inp)) {
			$arr=$result_inp;
		}
		if(is_array($result_inp1)) {
			$arr=array_merge($arr,$result_inp1);
		}
		if(is_array($result_sel)) {
			$arr=array_merge($arr,$result_sel);
		}
		if(is_array($result_text)) {
			$arr=array_merge($arr,$result_text);
		}
		
		$arr=array_merge($arr,$result_group);

		return $arr;
		*/
		
		// Promeneno e taka za da vzima controlite po reda po koito sa v stranicata i saotvetno da se podrezdat error messagetata.
		$errors = array();
		$e=$xp->query("//*[@required|@regexp]");
		foreach ($e as $field){
			$tmp_errors = array();
			switch ($field->tagName){
				case 'input':
					$tmp_errors = $this->validateInputs(array($field),$insert_errors);
					break;
				case 'select':
					$tmp_errors = $this->validateSelect(array($field),$insert_errors);
					break;
				case 'textarea':
					$tmp_errors = $this->validateText(array($field),$insert_errors);
					break;
				case 'group':
					$tmp_errors = $this->validateGroups(array($field),$insert_errors);
					break;
			}
			
			if(!empty($tmp_errors)) $errors=array_merge($errors,$tmp_errors);
		}
		return $errors;
	}
	
	function makereadOnlyInputs($inputs) {
		if(!empty($inputs)) {
			for($i=$inputs->length-1;$i>=0;$i--) {
				$span=$this->dom->createElement('span');
				$type=(string)$inputs->item($i)->getAttribute("type");
				$type=strtolower($type);
				switch ($type) {
					case "":
					case "text":
					case "password": {
						$val=(string)$inputs->item($i)->getAttribute("value");;
						$span->nodeValue=str_replace("&","&amp;",$val);
						break;
					}
					case "checkbox": {
						$span->nodeValue=$inputs->item($i)->hasAttribute("checked")?"[x]":"[&nbsp;&nbsp;]";
						break;
					}
					case "radio": {
						$span->nodeValue=$inputs->item($i)->hasAttribute("checked")?"(o)":"(&nbsp;&nbsp;)";
						break;
					}
					default: {
						$span->nodeValue="";
					}
				}
				
				$inputs->item($i)->parentNode->replaceChild($span,$inputs->item($i));
			}
		}
	}
	
	function makereadOnlySelects($selects) {
		if(!empty($selects)) {
			for($i=$selects->length-1;$i>=0;$i--) {
				$span=$this->dom->createElement('span');
				$name=$selects->item($i)->getAttribute("name");
				if(!$name) {
					$name="";
				}
				
				$val=$this->getPostValue($this->data,$name);
				
				$vals=array();
				
				for($ii=0;$ii<$selects->item($i)->childNodes->length;$ii++) {
					$op=$selects->item($i)->childNodes->item($ii);
					if(!$op->tagName||$op->tagName!="option") {
						continue;
					}
					if($op->hasAttribute("value")) {
						$op_val=$op->getAttribute("value");
					} else {
						$op_val=trim($op->nodeValue);
					}
					
					if(is_array($val)&&in_array($op_val,$val)) {
						$vals[]=trim($op->nodeValue);
					}
					else {
						if($val==$op_val) {
							$vals[]=$op->nodeValue;
						}
					}
				}
				
				//if(is_array($val)) {
				//	$span->nodeValue=implode(", ",$val);
				//}
				//else {
				//	$span->nodeValue=(string)$val;
				//}
				$span->nodeValue=implode(", ",$vals);				
				$selects->item($i)->parentNode->replaceChild($span,$selects->item($i));
			}
		}
	}
	
	function makereadOnlyText($text_fields) {
		if(!empty($text_fields)) {
			for($i=$text_fields->length-1;$i>=0;$i--) {
				$div=$this->dom->createElement('div');
				$val=(string)$text_fields->item($i)->nodeValue;
				$text_fields->item($i)->parentNode->replaceChild($div,$text_fields->item($i));
				$val=str_replace("&","&amp;",$val);
				$ex=explode("\n",$val);
				foreach ($ex as $ex_v) {
					$span=$this->dom->createElement('div');
					$span->nodeValue=$ex_v;
					$div->appendChild($span);
				}				
			}
		}
	}
	function makereadOnlyA($a_fields) {
		if(!empty($a_fields)) {
			for($i=$a_fields->length-1;$i>=0;$i--) {
				$span=$this->dom->createElement('span');
				$val=(string)$a_fields->item($i)->nodeValue;
				$span->nodeValue=str_replace("&","&amp;",$val);
				$a_fields->item($i)->parentNode->replaceChild($span,$a_fields->item($i));
			}
		}
	}
	
	
	
	function getReadOnlyVersion($bodyTag="<body>") {
		$this->makereadOnlyInputs($this->dom->getElementsByTagName("input"));
		$this->makereadOnlySelects($this->dom->getElementsByTagName("select"));
		$this->makereadOnlyText($this->dom->getElementsByTagName("textarea"));
		$this->makereadOnlyA($this->dom->getElementsByTagName("a"));
		
		$xp=new DOMXPath($this->dom);
		$e=$xp->query("//*[@readonly='hidden']");
		for($i=$e->length-1;$i>=0;$i--) {
			$e->item($i)->parentNode->removeChild($e->item($i));
		}
		return $this->getHTML($bodyTag);
	}

		
	function getHTML($bodyTag="<body>") {
		
		$s=$this->dom->saveHTML();
		
		$pos=stripos($s,$bodyTag);
		if($pos!==false) {
 			$pos+=strlen($bodyTag);
		}
		else {
			$pos=201;
		}
		$end_body_tag=str_replace("<","</",$bodyTag);
		
		$pos1=strrpos($s,$end_body_tag);
		
		
		if($pos1==false) {
			$pos1=$pos+16;
			$len=strlen($s)-$pos1;
		}
		else {
			$len=$pos1-$pos;
		} 
		//return substr($s,$pos,$len);
		
		/*
		html_entity_decode се прави защото saveHTML за разлика от saveXML винаг енкодва кирилицата като numeric entities.
		За целта се прави пък двойно encode при методите за сетване на стойности.
		*/
		
		return html_entity_decode(substr($s,$pos,$len),ENT_NOQUOTES,"UTF-8");
	}
	
}
?>
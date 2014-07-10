<?php
ob_start();
require_once('../libCommon.php');
CUserRights::checkRights("mail_groups");

$in_edit_table="mq_mail_groups";
$in_edit_id=(int)$_GET['id'];

$in_skip_relations=array($in_edit_table);

function loadList($file) {
	
	
	
	$str=file_get_contents($file);
	$enc=mb_detect_encoding($str,array("utf-8","windows-1251",'ISO-8859-1'),true);
	if($enc!="utf-8") {
		$str=iconv("windows-1251","utf-8",$str);
	}
	if(!$str) {
		return array('errors'=>array("Invalid Email List"));
	}
	$str=explode("\r\n",$str);
	$keys=array();
	$list=array();
	$valid_email_columns=array();
	foreach ($str as $k=>$v) {
		if($k==0) {
			$keys=explode("\t",$v);	
			foreach ($keys as $k1=>$v1){
				$v1 = trim($v1);
				if(empty($v1)){
					return  array('errors'=>array("Column ".($k1+1)." do not have header label!"));
				}
			}
			$valid_email_columns=$keys;
			continue;
		}
		if(empty($v)) {
			continue;
		}
		$d=explode("\t",$v);
		$d1=array();
		foreach ($d as $dk=>$dv) {
			if(isset($keys[$dk])) {
				$dv = trim($dv);
				$d1[$keys[$dk]]=$dv;
				if(isset($valid_email_columns[$dk])&&!CValidation::is_valid_email_address($dv)) {
					
					unset($valid_email_columns[$dk]);
					if(empty($valid_email_columns)) {
						echo "<b>Колона ".$keys[$dk]." в ред ".($k+1)." :</b> ".$dv;
						echo "<br />";
						echo "<br />";
						return array('errors'=>array("There is no column with valid emails! Please, validate your file!"));
					}
				}
				//$MailCnts[$keys[$dk]] += (int)CValidation::is_valid_email_address($dv);
			}
			else {
				return array('errors'=>array("Column ".($dk+1)." do not have header label!"));
			}
		}
		$list[]=$d1;
	}
	foreach ($keys as $k=>$v) {
		$keys[$k]="{{$v}}";
	}
	return array('keys'=>$keys,'values'=>$list,'valid_fields'=>$valid_email_columns);
}

?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<?=BE_Utils::includeDefaultJs();?>

</head>
<body >
<form id='f1' method=POST enctype="multipart/form-data">
<table class="main_table" align="center">
<tr>
<td>

<?php
$GLOBALS['email_fields']=array();
$GLOBALS['show_email_fields']=array();
if($in_edit_id) {
	$fields=getdb()->getone("select email_fields from {$in_edit_table} where id='{$in_edit_id}'");
	@$fields=unserialize($fields);
	
	if(is_array($fields)) {
		foreach ($fields as $k=>$v) {
			$GLOBALS['show_email_fields'][]=htmlspecialchars($v);
			$v=str_replace(array("{","}"),array("",""),$v);
			$GLOBALS['email_fields'][$v]=$v;
			
		}		
	}	
}
include(dirname(__FILE__).'/controls.php');
$con=getmq_mail_groupsControls();

$errors=array();

$errors=array();
if(isset($_POST['btSave'])) {

	$wd=ControlValues::getWriteData($con,$_POST);	
	$db=getdb();
	
	$result=false;
	if(is_uploaded_file($_FILES['emails_list']['tmp_name'])) {
		unset($wd['errors']['email_column']);
	}
	if(empty($wd['errors'])) {
		if(is_uploaded_file($_FILES['emails_list']['tmp_name'])) {
			$result=loadList($_FILES['emails_list']['tmp_name']);
			if(isset($result['errors'])) {
				$wd['errors']=$result['errors'];
			}
		}
	}
	
	
	if(empty($wd['errors']) && $wd['data']['email_column'] ){
		
		$tmp =  is_array($result) ? $result['values'] : unserialize($db->getOne("SELECT emails_list FROM mq_mail_groups WHERE id=?", array($in_edit_id)));
		if(is_array($tmp)){
			foreach ($tmp as $k=>$v){
				if(!CValidation::is_valid_email_address($v[$wd['data']['email_column']])){
					$wd['errors']['import'] .= 'Invalid email on line: '.($k+2).", {$v[$wd['data']['email_column']]}<br/>";
				}
			}
			if(!empty($wd['errors'])) {
				$db->Query("UPDATE mq_mail_groups SET emails_list='' WHERE id=?", array($in_edit_id));
			}
		} else {
			$wd['errors']['import'] = "Please upload a file with valid data.";
		}
		
	}
	
	
//	$wd['errors']=array("sdgf");
	if (empty($wd['errors'])) {
/*
	$order=$wd['data']['order_field'];
	unset($wd['data']['order_field']);
	if(!$in_edit_id) {
		$order=(int)$order;
	}
*/
/*
			if(!$in_edit_id) {
				$wd['data']['created_date']=date('Y-m-d H:i:s');
				$wd['data']['created_by']=Users::getUserId();
			}
			$wd['data']['updated_date']=date('Y-m-d H:i:s');
			$wd['data']['updated_by']=Users::getUserId();
*/
			if($result!==false) {
				$wd['data']['email_fields']=serialize($result['keys']);
				$wd['data']['mails_count']=count($result['values']);
				$wd['data']['emails_list']=serialize($result['values']);
				$valid=array_values($result['valid_fields']);
				$wd['data']['email_column']=$valid[0];
				
			}
			
			$n_id=ControlWriter::Write($in_edit_table,$wd['data'],(int)$_GET['id']);
//			$new_relation=CRelations::processRelationSave($_GET,$n_id);
//			if($new_relation===false) {
/*
				if((string)$order!="") {
	$co=new COrder($in_edit_table,"order_field");
	$co->set_item_order($n_id,$order);
}

*/

//$errors+=ControlValues::processManagedImages($n_id,$_FILES,$con['controls']);
				header("Location: ?id={$n_id}&msg=1&bkp=".urlencode($_GET["bkp"]));
				//header("Location: ".($_GET['bkp']));
				exit;		
//			}
//			if(is_array($new_relation)) {
//				$errors=$new_relation;
//			}
//			else {
//				header("Location: ".($_GET['bkp']));
//				exit;
//			}	
	}
	else {
		$errors=$wd['errors'];
	}
}

//$p=new Page();



if ($_SERVER['REQUEST_METHOD']=='GET') {
	if (isset($_GET['id'])) {
		$db=getdb();
		$array['in_data']=$db->getRow("select * from {$in_edit_table} where id='{$_GET['id']}'");
	}	
}
else {
	$array=$_POST;
}
FE_Utils::getGetMessage($errors);
if (!empty($errors)) {
	echo FE_Utils::renderErrors($errors);
	echo "<br />";
}

echo "<!--   START    -->";
//MasterForm::create($con,$_POST,$p,$array);
echo Master::create($con,$con['template']['dir'],$array);

//echo $hhh= $p->render();

//if($in_edit_id) {	
//	include(dirname(__FILE__).'/../_relations/list.php');		
//	include(dirname(__FILE__).'/../_relations/index.php');
//}

echo "<!--   END    -->";
echo "</td></tr></table>";
echo "</form></body></html>";

ob_end_flush();
?>
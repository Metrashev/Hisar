<?php

define('AUTO_CREATE_SUBTITLE',false);

define("CALENDAR_MONDAY",1);
define("CALENDAR_TUESDAY",2);
define("CALENDAR_WEDNESDAY",3);
define("CALENDAR_THUERSDAY",4);
define("CALENDAR_FRIDAY",5);
define("CALENDAR_SATURDAY",6);
define("CALENDAR_SUNDAY",0);

define("CALENDAR_LINKS_POSITION_NONE",0);
define("CALENDAR_LINKS_POSITION_TOP",1);
define("CALENDAR_LINKS_POSITION_BOTTOM",2);
define("CALENDAR_LINKS_POSITION_BOTH",3);

require_once(dirname(__FILE__).'/../CPictures.php');

class BE_Utils {
	
	public static function includeDefaultJs() {
		$js=JS_DIR;
		$css=BE_CSS_DIR;
		return <<<EOD
		<script src='{$js}UT.js'></script>
		<script src='{$js}lib.js'></script>
		<link rel="stylesheet" href="{$css}lib.css">
		
		<script src='{$js}Calendar/calendar.js'></script>
		<script src='{$js}Calendar/calendar-setup.js'></script>
		<script src='{$js}Calendar/lang/calendar-en.js'></script>
		<link rel="stylesheet" href="{$js}Calendar/calendar-win2k-cold-1.css">
		
		<script src='/js/jquery/jquery-1.3.2.js'></script>
		<script src='/js/jquery/ui/ui.core.js'></script>
		<script src='/js/jquery/ui/ui.draggable.js'></script>
		<script src='/js/jquery/ui/ui.resizable.js'></script>
		<script src='/js/jquery/ui/ui.dialog.js'></script>
		<script src='/js/jquery/external/bgiframe/jquery.bgiframe.js'></script>
		<link rel="stylesheet" href="/js/jquery/themes/base/ui.all.css">
EOD;
	}
	
	function loadTinyMceStyles($tag='styles') {
		$dir=dirname(__FILE__).'/../www'.BE_DIR.'config/';
		$dir=realpath($dir);
		$result=array();
		if(file_exists($dir.'/tiny_mce.xml')) {
			$sxe=simplexml_load_file($dir.'/tiny_mce.xml');
			//$xp=$sxe->xpath("/root/styles");
			foreach ($sxe->styles as $b) {
				foreach ($b as $k=>$v)	{
					$result[]=((string)$k).'='.((string)$v);
				}
			}
		}
		return implode(";",$result);
	}
	
	public static function loadTinyMce($body,$template) {
		$dir=dirname(__FILE__).'/../..'.BE_DIR.'config/tiny_mce/'.$template;
		if(file_exists($dir)) {
			ob_start();
			include($dir);
			//$str=file_get_contents($dir);
			$str=ob_get_clean();
			return str_replace("_#BODY#_",$body,$str);
		}
		return BE_Utils::getTinyMce($body,CSS_DIR.'lib_be.css');
	}
	
	static function getTinyMce($body,$css) {
		$styles=self::loadTinyMceStyles();
		$be=BE_DIR;
		return <<<EOD
<script language="javascript" type="text/javascript" src="{$be}tiny_mce/tiny_mce.js"></script>
<!--<script language="javascript" type="text/javascript" src="{$be}tiny_mce/tiny_mce_gzip.php"></script>-->
<script language="javascript" type="text/javascript">
	// Notice: The simple theme does not use all options some of them are limited to the advanced theme
	tinyMCE.init({
		mode : "exact",
		elements : "{$body}",
		theme : "advanced",
		//plugins : "table,contextmenu,paste,fileman,internallink,templates,advlink,preview,advimage,flash,graphics",
		plugins : "table,contextmenu,paste,fileman,internallink,templates,advlink,preview,advimage,flash",
		theme_advanced_buttons1 : "bold, italic,underline,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,link,unlink,anchor,preview",
		theme_advanced_buttons2 : "formatselect,separator,styleselect,separator,removeformat,separator,pastetext,pasteword,selectall,separator,fileman,table,code,internallink,fileman2,flash",
		//theme_advanced_buttons2 : "formatselect,separator,styleselect,separator,removeformat,separator,pastetext,pasteword,selectall,separator,fileman,table,code,internallink,fileman2,flash,graphics",
		theme_advanced_buttons3 : "",
		content_css : "{$css}",
		remove_script_host : true,
		document_base_url :'/',
		convert_urls : false,
		lang_fileman_desc:'Insert image',
		lang_fileman2_desc:'Insert .doc, .pdf or .zip for download',
		lang_internallink_desc:'Insert internal link',
		theme_advanced_styles : "{$styles}",
		custom_undo_redo : false,
		//verify_html : true,	//za validen html - default e true
		theme_advanced_toolbar_location : "top",
		theme_advanced_path_location : "bottom",
		extended_valid_elements : "a[name|href|target|title|onclick],map[*],area[shape|coords|href|title|target],object[*],param[*],ittiscript[*],input[*],script[*],embed[*],div[*]",
		//valid_elements : "*[*]",
		plugin_preview_width : "500",
		plugin_preview_height : "600"


	});
</script>
EOD;
	}
}

class FE_Utils {

	static function getGetMessage_old(&$errors) {
		if(!is_array($errors)) {
			return;
		}
		if($_SERVER['REQUEST_METHOD']=='GET'&&isset($_GET['msg'])) {
			$msgs=array(
				1=>"Записът е успешен",				
			);
			if(isset($msgs[$_GET['msg']])) {
				$errors[]=$msgs[$_GET['msg']];
			}
		}
	}
	
	static function getGetMessage(&$errors) {
		if(!is_array($errors)) {
			return;
		}
		if($_SERVER['REQUEST_METHOD']=='GET'&&isset($_GET['msg'])) {
			$msgs=array(
				1=>"Record successfully saved!",
			);
			if(isset($msgs[$_GET['msg']])) {
				$errors['_ok_']=array($msgs[$_GET['msg']]);
			}
		}
	}

	static function _die($msg) {
		if(isset($_GET['bkp'])) {		
			$back=<<<EOD
				<input type="button" onclick="self.location='{$_GET['bkp']}'" value="Назад" />
EOD;
		}
		else {
			$back=<<<EOD
			<input type="button" onclick="history.go(-1);" value="Назад" />
EOD;
		}
		die($msg."<br />".$back);
	}
	
	static function getFooter($table,$id) {
		if(!$id) {
			return;
		}
		$db=getdb();
		$row=$db->getrow("select created_by,updated_by,date_format(created_date,'%d/%m/%Y') as created_date,date_format(updated_date,'%d/%m/%Y') as updated_date from `{$table}` where id='{$id}'");
		
		$users=array();
		$users[]=(int)$row['created_by'];
		$users[]=(int)$row['updated_by'];
		$users=implode(',',$users);
		$users=$db->getassoc("select id,concat(first_name,' ',last_name) as name from users where id in ({$users})");
		
		return <<<EOD
		<hr />
		<table width='100%' cellpadding='2' cellspacing='0'>
		<tr>
			<td class="footer_label">Created on</td><td class="footer_label_r">{$row['created_date']}</td>
			<td class="footer_label">Updated on</td><td class="footer_label_r">{$row['updated_date']}</td>
			<td class="footer_label">Created by</td><td class="footer_label_r">{$users[$row['created_by']]}</td>
			<td class="footer_label">Updated by</td><td class="footer_label_r">{$users[$row['updated_by']]}</td>
		</tr>
		</table>
EOD;
	}
	
	static function getAgentId() {
		if((int)$_GET['agent_id']) {
			return (int)$_GET['agent_id'];
		}
		return Users::getAgentId();
	}
	
	static function getFileExt($name) {
		$p=pathinfo($name);
		return empty($p['extension'])?'':'.'.$p['extension'];
	}
	
	static function getBackLink() {
		if(isset($GLOBALS['_custom_bkp'])) {
			return "bkp=".urlencode($GLOBALS['_custom_bkp']);
		}
		
		return "bkp=".urlencode($_SERVER['REQUEST_URI']); 
	}
	
	public static function getMaterials($add_price=false,$force=false) {
		static $mats=array();
		if(empty($mats)||$force) {
			$db=getdb();
			$price=$add_price?",price_buy":"";
		 	$mats=$db->getAssoc("select m.id as id,concat(m.name,'/',ifnull(mu.name,'')) as name,coef{$price} from materials as m left join measure_units as mu on m.measure_unit_id=mu.id  order by m.name,mu.name");		 	
		}
		return $mats;
	}
	
	public static function calculateSums($valuation_head_id,$update_db=false) {
		$db=getdb();
		$percents=$db->getrow("select percent_sum_1,percent_sum_2,percent_sum_3,percent_sum_4,percent_5,percent_total,v_count from valuation_heads where id='{$valuation_head_id}'");
		$SQL="select case when material_type_id=3 then 2 else material_type_id end as mt,sum(price*m_count) from valuation_items as vi
inner join materials as m on m.id=vi.material_id
where valuation_head_id=?
group by mt";
		$rows=$db->getAssoc($SQL,array($valuation_head_id));
		
		$total1=((float)$rows[1]*$percents['percent_sum_1']/100)+((float)$rows[1]);
		$total2=((float)$rows[2]*$percents['percent_sum_2']/100)+((float)$rows[2]);
		$total3=((float)$rows[4]*$percents['percent_sum_3']/100)+((float)$rows[4]);
		
		$total=$total1+$total2+$total3;
		
		$total4=$percents['percent_sum_4']*$total/100+$total;
		
		if((int)$percents['v_count']) {
			$total4/=(int)$percents['v_count'];
			
		}
		else {
			$total4=0;
		}
		
		$p5=(float)(($percents['percent_5']/100)*$total4+$total4);
		
		if($update_db) {
			$db->execute("update valuation_heads set sum4=? where id=?",array($total4,$valuation_head_id));
		}
		
		return array('sum1'=>$total1,'sum2'=>$total2,'sum3'=>$total3,'sum4'=>$total4,'sum5'=>$p5);
		
	}
	
	static function getDeleteColumn($deleteName="hdDelete") {
		return <<<EOD
		<_h_id>
				<header class='header_nor' align="right">
					<caption>Изтрий</caption>
					<order_field></order_field>
					<orderable></orderable>
					<user_func class=''></user_func>
				</header>
				<col>
					<itti_col align='right' >
						<itti_attr datatype='' format_string='' unique_field='id'>id</itti_attr>
						<user_func class=''></user_func>
						<autoload type=''></autoload>
						<Anchor href='#' onclick='if(window.confirm("Сигурни ли сте?")) {document.getElementById("{$deleteName}").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>
							<nodeValue>Изтрий</nodeValue>
						</Anchor>
					</itti_col>
				</col>
			</_h_id>
EOD;
	}
	
	
	static function getStandartBEHeader($add_scripts=false) {
		$be=BE_CSS_DIR;
		$scripts=!$add_scripts?'':
		"<script src='/UT.js'></script>
<script src='/lib.js'></script>
<script src='/Calendar/calendar.js'></script>
<script src='/Calendar/calendar-setup.js'></script>
<script src='/Calendar/lang/calendar-en.js'></script>
<script src='../xml.js'></script>";
		return "<!DOCTYPE html
PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html>
<head>
<meta HTTP-EQUIV=\"content-type\" CONTENT=\"text/html; charset=UTF-8\">
{$scripts}
<link rel=\"stylesheet\" href=\"/Calendar/calendar-win2k-cold-1.css\">
<link rel=\"stylesheet\" href=\"{$be}lib.css\">
</head>
<body>";
	}
	
	static function getStandartFooter() {
		return "</body></html>";
	}
	
	static function getDBImg($ref_id,$ref_table,$alt='',$style='') {
		$db=getdb();
		$id=(int)$db->getone("select id from ".FILES_TABLE." where ref_record_id='{$ref_id}' and ref_table='{$ref_table}'");
		return empty($id)?'':"<img {$style} src='".BE_DIR."showimg.php?id={$id}' alt='{$alt}' />";
	}
	
	static function padDecNumber($number,$padChar='0') {
		return $number<10?$padChar.$number:$number;	
	}
	
	static function prepareDataGrid($ta_xml,$search,$postData,$useSearch=false,$unique_id='dg',$pagebar_id='pb',$pagesize=25,$__templateParams='') {
		$dg=new DataGrid($unique_id);
		$dg->createFromArray($ta_xml,true);
		
		

		$c=new CPageBar($pagebar_id,$dg->DataSource->getCount(),$pagesize);
		$c->_setAttribute('class','page_bar');

		$dg->setCurrentPage($c->getCurrentPage());
		$dg->old_page=$c->old_page;

		$dg->DataSource->Limit="limit ".$dg->getPageSize()*$dg->getCurrentPage().','.$dg->getPageSize();
		/* @var $dg->DataSource DataTable*/
		$templateParams=!empty($__templateParams)&&is_array($__templateParams)?$__templateParams:array();
		if ($useSearch) {
			$dg->DataSource->AddWhere(ControlValues::getSearchString($search,$postData,array(),$templateParams));	
		}

		FE_Utils::setDGAttributes($dg);
		$c->totalItems=$dg->DataSource->getCount();
		$c->alterPageCount();
		
		return array('dg'=>$dg,'pb'=>$c);

	}
	
	function send_mail($data) {
		require_once(dirname(__FILE__).'/../mail.php');
		$mail = new htmlMimeMail();
		$mail->setHtmlCharset('UTF-8');
		$mail->setHeadCharset('UTF-8');
		$mail->setHtmlEncoding("base64");
		
		if(!empty($data['from'])) {
			$mail->setFrom($data['from']);
		}
		if(!empty($data['subject'])) {
			$mail->setSubject($data['subject']);
		}
	
		if($data['return_path'])
			$mail->setReturnPath($data['return_path']);

		if($data['cc']!="")
			$mail->setCc($data['cc']);

		if($data['bcc']!="")
			$mail->setBcc($data['bcc']);


		$data['body'] = eregi_replace(' href="/', " HREF=\"HTTP://{$_SERVER['HTTP_HOST']}/", $data['body']);
	
		$mail->setHtml($data['body'], null, dirname(__FILE__).'/../../');
	
		return $mail->send(array($data['to']));
	}
	
	static function getSeparator() {
		return <<<EOD
<table cellpadding='0' cellspacing='0' border='0'>
<tr><td height='2'></td></tr>
</table>
EOD;
	}
	
	static function createPivot($array,$h_field,$v_field,$date_step="+1 week",$minmaxrow='') {
		$header=$array['header'];
		$rows=$array['rows'];
		if(empty($minmaxrow)) {
			$min_row=$rows[count($rows)-1][$h_field];
			$max_row=$rows[0][$h_field];
			$min_stamp=strtotime(BoundField::decodeFormat('%d/%m/%Y',$min_row));
			$max_stamp=strtotime(BoundField::decodeFormat('%d/%m/%Y',$max_row));
		}
		else {
			$db=getdb();
			$minmax=$db->getrow($minmaxrow);
			$min_stamp=strtotime($minmax['min_date']);	
			$max_stamp=strtotime($minmax['max_date']);	
		}
		$mt=$min_stamp;
		if(!$mt) {
			throw new Exception("Invalid Date!");	
		}
		$headers=array(0=>'');
		$dates=array();
		while ($mt<=$max_stamp) {
			$dates[]=date('Y-m-d',$mt);
			$headers[]=date('d/m/Y',$mt);
			$mt=strtotime($date_step,$mt);
			if($mt==$min_stamp) {
				throw new Exception("Date step is 0! Possible infinite cycling!");	
			}
		}
		
		$v_colomns=array();
		$v_index=0;
		foreach ($rows as $k=>$v) {
			if(!isset($v_colomns[$v[$v_field]])) {
				$v_colomns[$v[$v_field]]=$v_index++;
			}
		}
	
		
		$blocks=array();
		$index=0;
		foreach ($header as $k=>$v) {
			if($k==$h_field||$k==$v_field)
				continue;
			$blocks[$index]['val']=$v;	
			$blocks[$index++]['key']=$k;	
		}
		$bc=count($blocks);
		$a=array();	
		for($i=0;$i<$bc;$i++) {
			$a[$i]['header']=$headers;
			$a[$i]['header'][0]=$blocks[$i]['val'];
		}
		
		$rc=count($rows);
		$hc=count($headers);
	
		for($j=0;$j<$rc;$j++) {
			for($i=1;$i<$hc;$i++) {
			
				if($rows[$j][$h_field]==$headers[$i]) {
					for($k=0;$k<$bc;$k++) {
						$a[$k]['rows'][$v_colomns[$rows[$j][$v_field]]][0]=$rows[$j][$v_field];
						$a[$k]['rows'][$v_colomns[$rows[$j][$v_field]]][$i]=$rows[$j][$blocks[$k]['key']];
					}
				}
			}
		}
		
		return $a;
	
	}
	
	static function createTableCells($array,$columns_count=2,$tdText='<td>',$emptyCellString='&nbsp;') {
		if(!is_array($array)||empty($array))
			return '';
	//	$array=array_values($array);
		$index=-1;
		$result=array();
		$endTd=empty($tdText)?'':'</td>';
		//for($i=0;$i<count($array);$i++) {
		$i=0;
		foreach ($array as $val) {
			if(!($i%$columns_count)) {
				++$index;
			}
			$td=is_array($tdText)?$tdText[$i%$columns_count]:$tdText;
			$result[$index].=$td.$val.$endTd;
			$i++;
		}
		while ($i%$columns_count) {
			$td=is_array($tdText)?$tdText[$i%$columns_count]:$tdText;
			$result[$index].=$td.$emptyCellString.$endTd;
			$i++;
		}
		return "<tr>".implode("</tr><tr>",$result)."</tr>";
	}
	
	
	static function renderErrors_old($errors) {
		$arr=array();
		//$str="<table width='700' align='center' class='tErrors' border='0'><tr><td>";
		foreach ($errors as $k=>$v) {
			$arr[]="<label for='".htmlspecialchars($k)."'>".html_entity_decode($v)."</label>";
		}
		if(empty($arr)) {
			return '';
		}
		return $str.implode("<br />",$arr)."<br /><br />";//"</td></tr></table><br />";
	}
	
	static function renderErrors($errors) {
		$arr=array();
		//$str="<table width='700' align='center' class='tErrors' border='0'><tr><td>";
		$be=BE_IMG_DIR;
		if(isset($errors['_ok_'])&&is_array($errors['_ok_'])) {
			$errors=$errors['_ok_'];
			
			$str=<<<EOD
		<div style="background:#E7FEDC url({$be}ok_icon.gif) 10px 50% no-repeat;border:1px solid #00e000;padding:10px 10px 10px 40px;margin-bottom:10px;">
EOD;
	
		}
		else {
			$str=<<<EOD
		<div style="background:#F8E9E9 url({$be}error_icon.gif) 10px 50% no-repeat;border:1px solid #e00000;padding:10px 10px 10px 40px;margin-bottom:10px;">
EOD;
		}
		
		foreach ($errors as $k=>$v) {
			$arr[]="<div style='padding:2px;'>".html_entity_decode($v)."</div>";
		}
		if(empty($arr)) {
			return '';
		}
		return $str.implode("",$arr)."</div>";//"</td></tr></table><br />";
	}
	
	static function setDGAttributes(&$dg_p) {
		$dg_p->_setAttribute('width','100%');
		$dg_p->_setAttribute('style','border:1px solid #DCD225');
		$dg_p->_setAttribute('class','test1');
		$dg_p->_setAttribute('cellspacing','0');
		$dg_p->_setAttribute('cellpadding','0');	
	}
	
	function getCommonLinks() {
		$q=$_GET;
		$q['print']='on';
		$q=UT_userfunctions::getGetString($q);
		return "<a href='javascript:history.go(-1)'>Back</a> | <a href='#top'>Top</a> | <a href='/?{$q}' target='_blank'>Print</a>";
	}
	
	function getFileRelativePath($file,$throwException=false) {
		$dir=dirname(__FILE__).'/../../../www'.$file;
		
		return $dir;
	}
	
	function getSingleArrayTemplate($array,$formatFields=array()) {
		$keys=array();
		$arr=array();
		if(is_array($array)) {
			foreach ($array as $mkk=>$mvv) {
				$keys[$mkk]='$'.$mkk;
				if(isset($formatFields[$mkk])) {
					$arr[$mkk]=BoundField::translateFormat($formatFields[$mkk],$mvv);
				}
				else {
					$arr[$mkk]=$mvv;
				}
				
			}
		}
		if(isset($arr['picture'])&&!empty($arr['picture'])) {
			$arr['picture']="<img src='{$arr['picture']}' alt='' />";
		}
		return array('k'=>$keys,'a'=>$arr);
	}
	
	function renderTemplateFromArray($template,$array,$formatFields=array()) {
		$str='';
		foreach ($array as $mk=>$mv) {
			if(is_array($mv)) {
				$k=FE_Utils::getSingleArrayTemplate($mv,$formatFields);
				$str.=str_replace($k['k'],$k['a'],$template);
			}
			else {
				$k=FE_Utils::getSingleArrayTemplate($array,$formatFields);
				$str.=str_replace($k['k'],$k['a'],$template);
				break;
			}
		}
		return $str;
	}
	
	function getRewritePath() {
		$path_parts = pathinfo($_SERVER['PATH_INFO']);
		$path='';
		$file='';
		if(empty($path_parts['extension'])) {
			$p=$_SERVER['PATH_TRANSLATED'];
			if(@is_file($p.'index.html')) {
				$file='/index.html';
				$path=$path_parts['dirname'].'/'.$path_parts['basename'];
			}
			else 
				if(@is_file($p.'index.php')) {
					$file='/index.php';
					$path=$path_parts['dirname'].'/'.$path_parts['basename'];
				}
		}
		else {
			if(@is_file($_SERVER['PATH_TRANSLATED'])) {
				$path=$path_parts['dirname'];
				$file='/'.$path_parts['basename'];
			}
		}
		if($file=='')
			return 'Error404.html';
		return $path.$file;		
	}
}


  
class DB_Utils {
	function sql2html($SQL,$params=null,$addNumbers=false,$col_names='',$table_attribs='') {
  	$db=getdb();
  	$result=$db->getAll($SQL,$params);
  	if($table_attribs=='') {
  		$table_attribs="cellpading='0' cellspacing='0' border='1'";
  	}
  	$str.="<table {$table_attribs}>";
  	$header='';
  	if($addNumbers) {
  		$header="<td style='background-color:lightyellow'><b>No</b></td>";
  	}
  	if(!is_array($col_names)||count($col_names)==0) {
  		$col_names=$result[0];
  		if(is_array($col_names))
  			$col_names=array_keys($col_names);
  	}
  	if(is_array($col_names)&&count($col_names)>0) {
  		foreach ($col_names as $v) {
  			$header.="<td style='background-color:lightyellow'><b>{$v}</b></td>";
  		}
  	}
  	$tb='';
  	foreach ($result as $k=>$value) {
  		$tb.="<tr>";
  		if($addNumbers) {
  			$tb.="<td><b>".($k+1)."</b></td>";
  		}
  		foreach ($value as $rk=>$rv) {
  			if(is_null($rv))
  				$rv="NULL";
  			$tb.=$rv==''?"<td>&nbsp;</td>":"<td>{$rv}</td>";
  		}
  		$tb.="</tr>";
  	}
  	return $str.$header.$tb."</table>";
  	
  }
  
  function getType($str) {
  	$a=array();
  	$e=ereg("(^[a-z]+)(\()?([0-9,\']+)?(\))?)?(\ unsigned)?",$str,$a);
  	if($e===false) {
  		return false;
  	}
  	return array('type'=>$a[1],'size'=>($a[3]),'unsigned'=>$a[5]==' unsigned');
  }
  
  function isValidInt($val,$size,$unsigned) {
  	$interval=array(
	  	  'tinyint'     => '256',
	      'smallint'    => '65536',
	      'mediumint'   => '16777216',
	      'int'  => '4294967296',
	      'bigint'      => '18446744073709551616',
      );
      
      $interval = $intervals[$size];
    
    if (extension_loaded('bc_math')) {
      
      if ($unsigned) {
        $min = '0';
        $max = bcsub($interval, '1');
      } else {
        $min = '-'.bcdiv($interval, '2');
        $max = bcsub(bcdiv($interval, '2'), '1');
      }
      
      if (bccomp($min, $val)>0 or bccomp($max, $val)<0)
        return false;
      
    } else {
      
      if ($size=='bigint')
        trigger_error('Extension "bc_math" not loaded, this extension is required for using 64-bit integers', E_USER_ERROR);
      
      if ($unsigned) {
        $min = 0;
        $max = $interval-1;
      } else {
        $min = 0-$interval/2;
        $max = $interval/2 - 1;
      }
      
      if ($min>$val or $max<$val)
        return false;
      
    }
    return true;
  }
  
  static function parseTable($table,$ignoreErrors=false) {
      $data=array();
      $db=getdb();
      $columns=$db->getAll("show columns from {$table}");
      foreach ($columns as $v) {
         $t=DB_Utils::getType($v['Type']);
          if($t==false) {
              if($ignoreErrors)
                  continue;
              throw new Exception("Brake on {$v['Field']}<pre>".print_r($t)."</pre><pre>".print_r($columns)."</pre>");
          }
          $data[$v['Field']]=array();
          $data[$v['Field']]['name']=$v['Field'];
          $data[$v['Field']]['type']=$t['type'];
          $data[$v['Field']]['size']=$t['size'];
          $data[$v['Field']]['unsigned']=$t['unsigned'];
          $data[$v['Field']]['autoincrement']=!empty($v['Extra']);
          $data[$v['Field']]['is_null']=!empty($v['Null']);
          
          switch ($t['type']) {
              case 'char': {
                  $data[$v['Field']]['my_type']=DATA_CHAR;                  
                  break;    
              }
              case 'tinyint':  {
                  $data[$v['Field']]['my_type']=DATA_TINYINT;
                  break;
              }
              case 'smallint': {
                  $data[$v['Field']]['my_type']=DATA_SMALLINT;
                  break;
              }
              case 'medium': {
                  $data[$v['Field']]['my_type']=DATA_MEDIUMINT;
                  break;
              }
              case 'int':     {
                  $data[$v['Field']]['my_type']=DATA_INT;
                  break;
              }
              case 'bigint': {
                  $data[$v['Field']]['my_type']=DATA_BIGINT;
                  break;
              }
              case 'date': {
                  $data[$v['Field']]['my_type']=DATA_DATE;
                  break;
              }
              case 'datetime': {
                  $data[$v['Field']]['my_type']=DATA_DATETIME;
                  break;
              }    
              case 'time': {
                  $data[$v['Field']]['my_type']=DATA_TIME;
                  break;
              }
              case 'year': {
                  $data[$v['Field']]['my_type']=DATA_YEAR;
                  break;  
              }
              case 'set':  {
                  $data[$v['Field']]['my_type']=DATA_SET;
                  break;
              }
              case 'enum': {
                  $data[$v['Field']]['my_type']=DATA_ENUM;
                  break;
              }
              case 'float':      {
                  $data[$v['Field']]['my_type']=DATA_FLOAT;
                  break;
              }
              case 'double':    {
                  $data[$v['Field']]['my_type']=DATA_DOUBLE;
                  break;
              }
              case 'decimal': {
                  $data[$v['Field']]['my_type']=DATA_DECIMAL;
                  break;
              }
              case 'text': {
                  $data[$v['Field']]['my_type']=DATA_TEXT;
                  break;
              }
              case 'varchar': {
                  $data[$v['Field']]['my_type']=DATA_VARCHAR;
                  break;
              }
              default: {
                  $tp=strtoupper($t['type']);
                  eval("\$my_type=DATA_$a;");
                  $data[$v['Field']]['my_type']=$my_type;
                  break;
              }    
          }              
      }
      return $data;
  }
  
  function checkForInsertInTable($table,$data,$error_labels=array(),$required_fields=array(),$strict_check_types=false,$ignoreErrors=false) {
  	$errors=array();
  	$fields=array();
  	$values=array();
  	$db=getdb();
  	$columns=$db->getAll("show columns from {$table}");
  	foreach ($columns as $v) {
  		if(!isset($data[$v['Field']])) {
  			continue;
  		}
  		$text_val=$data[$v['Field']];
  		if($data[$v['Field']]=='') {
  			if(isset($required_fields[$v['Field']])) {
  				$errors[$v['Field']]="Required field {$error_labels[$v['Field']]} left empty!";
  				continue;
  			}
  			$values[$v['Field']]='';
  			$fields[$v['Field']]=$v['Field'];
  			continue;
  		}
  		$t=self::getType($v['Type']);
  		if($t==false) {
  			if($ignoreErrors)
  				continue;
  			throw new Exception("Brake on {$v['Field']}<pre>".print_r($t)."</pre><pre>".print_r($columns)."</pre>");
  		}
  		if(!isset($error_labels[$v['Fields']])) {
  			$e_l=$v['Field'];
  		}
  		else {
  			$e_l=$error_labels[$v['Fields']];
  		}
  		switch ($t['type']) {
  			case 'char': {
  				$value=$data[$v['Field']];
  				if($strict_check_types) {		
  					if(strlen($value)>intval($t['size'])) {
  						$errors[$v['Field']]="Text in field {$e_l} is too long!";
  						continue;
  					}
  				}
  				$values[$v['Field']]=$value;
  				$fields[$v['Field']]=$v['Field'];
  				break;	
  			}
  			case 'tinyint': 
  			case 'smallint':
  			case 'medium':
  			case 'int':
  			case 'bigint': {
  				$value=intval($data[$v['Field']]);
  				if($strict_check_types) {		
  					if($value!=$data[$v['Field']]) {
  						$errors[$v['Field']]="Invalid integer for {$e_l}";
  						continue;
  					}
  					if(!self::isValidInt($value,$v['Type'],$t['unsigned'])) {
  						$errors[$v['Field']]="Invalid value for {$e_l}";
  						continue;
  					}
  				}
  				$values[$v['Field']]=$value;
  				$fields[$v['Field']]=$v['Field'];
  				break;	
  			}
  			case 'date': {
  				$value=$data[$v['Field']];
  				if($value='0000-00-00'||$value='00/00/0000'||$value='') {
  					if(isset($required_fields[$v['Field']])) {
  						$errors[$v['Field']]="Field {$e_l} is required to be valid date!";
  						continue;
  					}
  					$values[$v['Field']]='0000-00-00';
  					$fields[$v['Field']]=$v['Field'];
  					break;
  				}
  				$m=new CFormatedDate();
  				$date=$m->date($value);
  				if($date==false) {
  					$errors[$v['Field']]="Invalid value for {$e_l}";
  					continue;
  				}
  				$values[$v['Field']]=$value;
  				$fields[$v['Field']]=$v['Field'];
  				break;		
  			}
  			case 'datetime': {
  				$value=$data[$v['Field']];
  				if($value='0000-00-00 00:00:00'||$value='00/00/0000 00:00:00'||$value='') {
  					if(isset($required_fields[$v['Field']])) {
  						$errors[$v['Field']]="Field {$e_l} is required to be valid date-time!";
  						continue;
  					}
  					$values[$v['Field']]='0000-00-00 00:00:00';
  					$fields[$v['Field']]=$v['Field'];
  					break;
  				}
  				$m=new CFormatedDate();
  				$date=$m->datetime($value);
  				if($date==false) {
  					$errors[$v['Field']]="Invalid value for {$e_l}";
  					continue;
  				}
  				$values[$v['Field']]=$value;
  				$fields[$v['Field']]=$v['Field'];
  				break;	
  			}	
  			case 'time': {
  				$value=$data[$v['Field']];
  				if($value='00:00:00'||$value='') {
  					if(isset($required_fields[$v['Field']])) {
  						$errors[$v['Field']]="Field {$e_l} is required to be valid time!";
  						continue;
  					}
  					$values[$v['Field']]='00:00:00';
  					$fields[$v['Field']]=$v['Field'];
  					break;
  				}
  				$m=new CFormatedDate();
  				$date=$m->time($value);
  				if($date==false) {
  					$errors[$v['Field']]="Invalid value for {$e_l}";
  					continue;
  				}
  				$values[$v['Field']]=$value;
  				$fields[$v['Field']]=$v['Field'];
  				break;	
  			}
  			case 'year': {
  				$value=intval($data[$v['Field']]);
  				if($data[$v['Field']]!=$value) {
  					$errors[$v['Field']]="Invalid value for {$e_l}";
  					continue;
  				}
  				$values[$v['Field']]=$value;
  				$fields[$v['Field']]=$v['Field'];
  				break;	
  			}
  			case 'set':
  			case 'enum': {
  				$_set=explode(",",$t['size']);
  				$value=$data[$v['Field']];
  				$a=array_search($value,$_set);
  				if($a===false) {
  					$errors[$v['Field']]="Invalid value for {$e_l}";
  					continue;
  				}  				
  				$values[$v['Field']]=$value;
  				$fields[$v['Field']]=$v['Field'];
  				break;
  			}
  			case 'float':
  			case 'double':
  			case 'decimal': {
  				$value=floatval($data[$v['Field']]);
  				if($strict_check_types) {		
  					if($value!=$data[$v['Field']]) {
  						$errors[$v['Field']]="Invalid float value for {$e_l}";
  						continue;
  					}
  					if($t['unsigned']&&$value<0) {
  						$errors[$v['Field']]="Invalid value for {$e_l}";
  						continue;
  					}
  				}
  				$values[$v['Field']]=$value;
  				$fields[$v['Field']]=$v['Field'];
  				break;
  			}
  			default: {
  				$values[$v['Field']]=$value;
  				$fields[$v['Field']]=$v['Field'];
  				break;	
  			}	
  		}	  		
  	}
  	if(count($errors)>0)
  		return array('errors');
  	return array('values'=>$values,'fields'=>$fields);
  	
  }
  
  function getInsertString($array,$table) {
  	if(!is_array($array['fields'])||!is_array($array['values'])||count($array['values'])==0||count($array['values'])!=count($array['fields']))
  		return false;
  	$fields='`'.implode("`,`",$array['fields']).'`';
  	$values=array_fill(0,count($array['values']),"?");
  	$values=implode(",",$values);
  	return "INSERT INTO `{$table}` ({$fields}) values({$values})";
  }
  
  function getUpdateString($array,$table,$strWhere='') {
  	if(!isset($array['fields'])) {
  		$fields=$array;
  	}
  	else {
  		$fields=$array['fields'];
  	}
  	if(!is_array($array)||count($array)==0)
  		return false;
  	$fields='`'.implode("`=?,`",$fields).'`=?';
  	if($strWhere!='')
  		$strWhere='WHERE '.$strWhere; 
  	return "UPDATE {$table} set {$fields} {$strWhere}";
  }
  
}

class pPrado {
	static function beginRequest()
	{
		if(get_magic_quotes_gpc())
		{
			if(isset($_GET))
				$_GET=array_map(array('pPrado','pradoStripSlashes'),$_GET);
			if(isset($_POST))
				$_POST=array_map(array('pPrado','pradoStripSlashes'),$_POST);
			if(isset($_REQUEST))
				$_REQUEST=array_map(array('pPrado','pradoStripSlashes'),$_REQUEST);
			if(isset($_COOKIE))
				$_COOKIE=array_map(array('pPrado','pradoStripSlashes'),$_COOKIE);
		}
	}
	
	/**
 * Strips back slashes from a string or an array.
 * @param mixed the data to be processed
 * @return mixed the processed data
 */
	static function pradoStripSlashes(&$data)
	{
		return is_array($data)?array_map(array('pPrado','pradoStripSlashes'),$data):stripslashes($data);
	}
	
	/**
 * Encodes a string.
 *
 * The string is encoded by HTML-encoding special characters (&, ", ', <, >).
 * 
 * @param string|array the string or array of strings to be encoded
 * @return string|array the encoded result
 * @see pradoDecodeData
 */
	static function pradoEncodeData($data)
	{
		if(is_array($data))
		return array_map(array('pPrado','pradoEncodeData'),$data);
		else
		return strtr($data,array('&'=>'&amp;','"'=>'&quot;',"'"=>'&#039;','<'=>'&lt;','>'=>'&gt;'));
	}

	/**
 * Decodes a string.
 *
 * The string is decoded by HTML-decoding special characters (&, ", ', <, >).
 * 
 * @param string|array the string or array of strings to be encoded
 * @return string|array the encoded result
 * @see pradoEncodeData
 */
	function pradoDecodeData($data)
	{
		if(is_array($data))
		return array_map('pradoDecodeData',$data);
		else
		return strtr($data,array('&amp;'=>'&','&quot;'=>'"','&#039;'=>"'",'&lt;'=>'<','&gt;'=>'>'));
	}
	
}

define(VISIBLE_ONLY,1);
define(NOT_VISIBLE_ONLY,2);
define(VISIBLE_ALL,3);


class CCommon {
	
	function getPageCount($count,$page_size) {
		
		if($page_size==0)
			return '';	
		$ps=(int)($count/$page_size);
		if($count%$page_size==0)
		$ps--;
		return $ps;
	}
	
	function renderTemplate($news,$cid,$language_id,$single_id=0) {		
		$db=getdb();
		if($single_id) {
			$field='single_template_path';
			$template=$db->getOne("select template from news_pages where id='{$single_id}'");
			if(empty($template)) {
				$template=$db->getOne("select single_template_path from templates where cid=".((int)$cid));
			}
		}
		else {
			$template=$db->getOne("select template_path from templates where cid=".((int)$cid));
		}
		if(empty($template)) {
			if($single_id&&defined('DEFAULT_NEWS_SINGLE_TEMPLATE')) {
                $template=DEFAULT_NEWS_SINGLE_TEMPLATE;
			}
			if(!$single_id&&defined('DEFAULT_NEWS_TEMPLATE')) {
				$template=DEFAULT_NEWS_TEMPLATE;	
			}
		}
		if(empty($template)) {
			return '';
		}
		$template=FE_Utils::getFileRelativePath($template);
		if(empty($template))
			return '';
		ob_start();
		include($template);
		$t=ob_get_clean();
		//@$t=file_get_contents($template);
		//$formatFields=array('due_date'=>DATE_FORMAT);
		$formatFields=array('due_date'=>CLanguage::getDateFormat($language_id));
		return FE_Utils::renderTemplateFromArray($t,$news,$formatFields);
	}
	
	function getPager($get,$count,$page_size,$currentPage,$maxpages=10) {
		
		$ps=$this->getPageCount($count,$page_size);
		if($ps<1)
			return '';
		unset($get['p']);
		
		if(count($get)>0)
			$href='?'.UT_userfunctions::getGetString($get)."&p=";
		else 
			$href="?p=";
		return CLib::draw_page_bar($ps,$currentPage,$href,$maxpages,0);
	}
	
	function getPrevIndex($array,$id) {
		$a=array_keys($array);
		$i=array_search($id);
		if($i===false||$i==0)
			return 0;
		return $i-1;	
	}
	
	function getPrevId($array,$id) {
		$a=array_keys($array);
		$i=array_search($id);
		if($i===false)
			return 0;
		return $a[$i-1];	
	}
	
	function getNextId($array,$id) {
		$a=array_keys($array);
		$i=array_search($id);
		if($i===false||$i>=count($a))
			return 0;
		return $a[$i+1];
	}
	
	function getNextIndex($array,$id) {
		$a=array_keys($array);
		$i=array_search($id);
		if($i===false||$i==count($a)-1)
			return 0;
		return $i+1;
	}
}

class CNews extends CCommon {
	private $news;
	private $db;
	private $page_size=10;
	private $count=null;
	private $page_count=0;
	private $is_visible=VISIBLE_ONLY;
	private $is_future_visible=NOT_VISIBLE_ONLY;
	private $readmoreLink='?';
	private $currentPage;
	private $cid;
	public $ids;
	private $page_renderer=null;
	private $news_renderer=null;
	private $customFilter='';
	public $template;
	public $App;	/* @var App Application*/
	
	private $order="due_date desc";
	
	function __construct($App,$current_page=0/*$cid,$current_page=0*/) {
		/* @var App Application*/
		$this->db=getDB();
		$this->news=null;
		$this->currentPage=$current_page;
		$this->App=$App;	
		$this->cid=$App->getPageId();
		$this->LoadIds();
		$this->page_count=parent::getPageCount($this->count,$this->page_size);
		
	}
	
	function renderNews_Sp($cid,$id=0,$current_page=0) {
		$db=getdb();
		if($id==0) {
			$row=$db->getRow("select * from news_sp where cid=".intval($cid)." and visible=1 and case when date_to='0000-00-00' then now()>date_from else now() between date_from and date_to end");
		}
		else {
			$row=$db->getRow("select * from news_sp where id=".((int)$id)." and visible=1 and case when date_to='0000-00-00' then now()>date_from else now() between date_from and date_to end");
		}
		
		if(empty($row))
			return '';
		if($current_page&&(int)$row['first_page'])	//samo 1-va stranica?
			return '';
		return $db->getOne("select body from static_pages where def=1 and cid=".intval($row['cid']));
	}
	
	function setPageRenderer($user_func) {
		$this->page_renderer=$user_func;
	}
	
	function setNewsRenderer($user_func) {
		$this->news_renderer=$user_func;
	}
	
	function renderPage($page,$news_id=0,$showPager=true,$news_count=0) {
		if($this->page_renderer!=null) {
			return call_user_func($this->page_renderer,array('showpager'=>$showPager,'id'=>$news_id,'page'=>$page,'news'=>$this->getPage($page)));
		}
		return $this->rend_page($page,$news_id,$showPager,$news_count);
	}
	
	function getBodyHTML($get) {
		return  self::renderNews_Sp($this->cid,0,$this->currentPage). $this->renderPage($get['p'],$get['news_id']);
	}
	
	function renderNews(&$id=0) {
		if($id==0) {
			$a=$this->getFirstNewsForPage($this->currentPage);
			if(!is_array($a))
				return '';
			$id=array_keys($a);
			$id=$id[0];
			
		}
		else {
			$a[$id]=$this->getNews($id);
		}
		if($this->news_renderer!=null) {
			return call_user_func($this->news_renderer,array('news'=>$a,'id'=>$id));
		}
		$tpl=CCommon::renderTemplate($a,$this->App->getPageId(),$this->App->getLanguage(),$id);
			
		return !empty($tpl)?$tpl:<<<EOD
		<div class='newsTitle'>{$a[$id]['title']}</div>
		<div class='newsBody'><p>{$a[$id]['body']}</p></div>
EOD;
		//return "<h3></h3><p>{$a[$id]['body']}</p><br/>";
	}
	
	
	
	private function rend_page($page,$news_id=0,$showPager=true,$news_count=0) {
		$news=$this->getPage($page);
		$str='';
		if(!is_array($news)||count($news)==0)
			return "<div class='errorMsg'>No news available!</div>";
		$str=$this->renderNews($news_id);
		$counter=0;
		
		foreach ($news as $key=>$value) {
			if($news_count&&$counter>=$news)
				break;
			if($key==$news_id) {
			//	$template=CCommon::renderTemplate($value,$this->cid,$this->App->getLanguage(),true);
			//	$str.=empty($template)?"<div class='p_newsTitle'>".$value['title']."</div>":$template;
				$str.="<div class='p_newsTitle'>".$value['title']."</div>";
			}
			else {
				$str.="<div class='p_newsStaticLink'>{$value['link']}<div/>";
			}
			$counter++;
		}
		if($showPager)
			$str.=$this->getPager($_GET);
		return $str;
	}
	
	private function LoadIds() {
		$this->ids=$this->db->getAssoc("select id,cid,title,subtitle,date_format(due_date,'%d/%m/%Y') as `date`,date_format(due_date,'%H:%i:%s') as `hour`,due_date,picture,is_visible,is_future_visible from news_pages {$this->getFilter()} order by {$this->order}");
		$this->count=count($this->ids);
	}
	
	function setPageSize($pages,$forseRead=false) {
		$this->page_size=$pages;
		$this->page_count=parent::getPageCount($this->count,$this->page_size);
		if($forseRead)
			return $this->getPage($this->currentPage);
	}
	
	function setOrder($order,$reload=false) {
		$this->order=$order;
		if($reload)
			$this->LoadIds();
	}
	
	function setReadMoreLink($link) {
		$this->readmoreLink=$link;
	}
	
	function getCurrentPage() {
		return $this->currentPage;
	}
	
	function getPageSize() {
		return $this->page_size;
	}
	
	function getNewsCount($forceRead=false) {
		if($forceRead)
			$this->count=$this->getCount();
		return $this->count;
	}
	
	function setVisibleFilter($filter,$getNewRows=true) {
		$this->is_visible=$filter;
		if($getNewRows)
			$this->LoadIds();
	}
	
	function setCustom_ids($ids) {
		if(is_array($ids)&&count($ids)>0)
			$ids=implode(",",$ids);
		$s=trim($ids);
		if($s[0]=='(') {
			$this->custom_ids=$s;
		}
		else {
			$this->custom_ids="({$s})";
		}
	}
	
	function setFutureFilter($filter,$getNewRows=true) {
		$this->is_future_visible=$filter;
		if($getNewRows)
			$this->LoadIds();
	}
	
	private function getCount() {
		return intval($this->db->getOne("select count(*) from news_pages {$this->getFilter()}"));
	}
	
	function addCustomFilter($strWhere) {
		$this->customFilter=$strWhere;
	}
	
	private function getFilter($textOnly=true) {
		$where=array();
		$where[]='cid='.$this->cid;
		if($this->customFilter!='')
			$where[]=$this->customFilter;
		switch ($this->is_visible) {
			case VISIBLE_ONLY: {
				$where[]="is_visible=1";
				break;
			}
			case NOT_VISIBLE_ONLY: {
				$where[]="is_visible!=1";
				break;
			}
		}
		switch ($this->is_future_visible) {
			case VISIBLE_ONLY: {
				$where[]="is_future_visible=1";
				break;
			}
			case NOT_VISIBLE_ONLY: {
				$where[]="case when is_future_visible!=1 then now()>=due_date else 1 end";
				break;
			}
		}
		if($this->custom_ids!=null) {
			$where[]="cid in {$this->custom_ids}";
		}
		if(count($where)>0) {
			$strWhere="where ".implode(" AND ",$where);
		}
		else {
			$strWhere='';
		}
		
		if($textOnly)
			return $strWhere;
		return array('text'=>$strWhere,'array'=>$where);
	}
	
	function getNews($id,$linkText='',$createSubtitle=true) {
		$id=intval($id);
		$a=$this->ids[$id];
		$a['body']=$this->db->getOne("select body from news_pages where id={$id}");
		if($linkText=='')
			$a['link']=$this->getNewsLink($id,$a['title']);
		else 
			$a['link']=$this->getNewsLink($id,$linkText);
		$a['href']=$this->getHref($id);
		if(empty($a['subtitle'])&&$createSubtitle&&AUTO_CREATE_SUBTITLE) {
			$a['subtitle']=mb_substr(html_entity_decode(strip_tags($a['body']), ENT_QUOTES, "UTF-8"),0, 100, 'UTF-8').'...';
		}
		return $a;
	}
	
	function getHref($id) {
		$gprm=$_GET;
		$gprm['cid']=$this->cid;
		//$gprm['p']=$this->currentPage;
		$gprm['news_id']=$id;
		$_g=UT_userfunctions::getGetString($gprm);	
		return $this->readmoreLink.$_g;	
	}
	
	function getNewsLink($id,$text) {
		$gprm=$_GET;
		$gprm['cid']=$this->cid;
		//$gprm['p']=$this->currentPage;
		$gprm['news_id']=$id;
		$_g=UT_userfunctions::getGetString($gprm);	
		return "<a href='". $this->readmoreLink.$_g."'>{$text}</a>";
	}
	
	function getPrev($current_id) {
		return parent::getPrevId($this->ids,$current_id);
		
	}
	
	function getNext($current_id) {
		return parent::getNextId($this->ids,$current_id);
	}
	
	function getPage($page,$news_count=0,$linkText='',$createSubtitle=true) {
		if($this->page_count<$page)
			return false;
		if($this->count==0)
			return false;
		if($news_count==0)
			$news_count=$this->page_size;
		$arr=array_keys($this->ids);
		$result=array();
		
		for($index=$page*$this->page_size;$index<$page*$this->page_size+$news_count;$index++) {
			$result[$arr[$index]]=$this->getNews($arr[$index],$linkText,$createSubtitle);
		}
		unset($result['']);
		return $result;
	}
	
	function getFirstNewsForPage($page) {
		return $this->getPage($page,1);
	}
	
	function getFirstPage() {
		return $this->getPage(0);
	}
	
	function getLastPage() {
		return $this->getPage($this->page_count);
	}
	
	function getPager($get) {
		
		return parent::getPager($get,$this->getCount(),$this->page_size,$this->currentPage);
	}
}

class CStaticPage {
	private $cid=0;
	private $body='';
	private $id=0;
	protected  $spid=0;
	protected $data;
	public $App;
	
	function __construct($App,$spid=0) {
		/* @var $App Application*/
		$this->spid=$spid;
		$this->App=$App;
		
		if(($this->cid=$App->getPageId())>0) {
			$db=getDB();
			if($spid>0) {
				$arr=$db->getAll("select * from static_pages where id={$spid}");
			}
			else {
				$arr=$db->getAll("select * from static_pages where cid={$this->cid} and def=1");
			}
			if(count($arr)>0) {
				$this->data=$arr[0];
				$this->id=intval($arr[0]['id']);
				$this->body=$arr[0]['body'];
			}
		}
	}
	
	
	function getCID() {
		return $this->cid;
	}
	
	function getID() {
		return $this->id;
	} 
	
	function translateBody() {
		$_str=$this->body;
		
		if(!empty($this->data['script_file_path'])) {
			$array=unserialize($this->data['script_file_path']);
			
			if(is_array($array)) {
				$__dir=dirname(__FILE__).'/../www';
				$__tmp=array();
				$__rep_tmp=array();
				foreach ($array as $_k=>$_v) {
					//vuv $_v trqbwa da ima key path
					
					if(!is_array($_v)||!isset($_v['path'])) {
						continue;
					}
					extract($_v);
					ob_start();
					include($__dir.$path);
					$__tmp[]=ob_get_clean();
					
					$q=UT_userfunctions::getGetString($_v,' ','=','"');
					$__rep_tmp[]="<ittiscript {$q}></ittiscript>";
					
				}
				$_str=str_replace($__rep_tmp,$__tmp,$_str);
			}
		}
		return $_str;
	}
	
	function getBodyHTML() {
		$body=$this->body;
		$node=$this->App->getNode();
		$title=$node['value'];
		$template=dirname(__FILE__).'/../www/templates/SP/sp.php';
		ob_start();
		include($template);
		return ob_get_clean();
		//return $this->translateBody();
		//return $this->body;
	}
}

class Application {
	const URL_TYPE=0;
	const CID_TYPE=1;
	const DEFAUT_TEMPLATE='template.php';
	
	private $pageID=0;
	
	private $path_url='';
	private $dataModel=null;
	
	private $db;
	private $Tree; /* @var $Tree CURLTree*/
	private $_node=null;
	
	public $templates;
	
	
	static function getInstance($defaultCID) {
		return new Application($defaultCID);
	}
	
	function getTree() {
		return $this->Tree;
	}
	
	function __construct($cid) {
		
		$this->templates=array(
			'news'=>array(1=>'Custom',2=>'CNewsTemplate1',3=>'CNewsTemplate2',4=>'CNewsTemplate3',5=>'CNewsTemplate4'),
			'sp'=>array(1=>'Custom',2=>''),
			'gallery'=>array(1=>'Custom',2=>''),
		);
		
		$this->Tree=$Tree = new CURLTree('categories');
		$this->db=getDB();
		$this->determinePage($cid);
		$this->loadNode();
		$this->language=$this->getLanguage();
	}
	
	function  getLanguage() {
		return $this->_node['language_id'];
	}
	
	function loadNode() {
		if($this->pageID>0) {
			$this->_node=$this->Tree->get_node_by_id($this->pageID);
		}
	}
	
	function loadRequests() {
		if($this->_node!=null) {
			if(!empty($this->_node['request_scripts'])) {
				$str=explode(";",$this->_node);
			}
			if(count($str)>0) {
				foreach ($str as $value) {
			@		require_once($value);
				}
			}
		}
	}
	
	function getPageId() {
		return $this->pageID;
	}
	
	function getPageUrl() {
		return $this->path_url;
	}
	
	private function getFromURL() {
		$path_parts = pathinfo($_SERVER['PATH_INFO']);
		if($path_parts['dirname']=='/'&&empty($path_parts['basename']))
			return $this->pageID=0;
		$path=($_SERVER['PATH_INFO']);
		if($path=='')
			return $this->pageID=0;
		$_SESSION['counter'][]=$path;
		$cid=$this->db->getAll("select id,path from categories where replace(replace(?,'.',''),'/','')=replace(path,'/','')",array($path));
		$_SESSION['counter'][]=$cid;
		if(count($cid)>0) {
			$this->page_url=$cid[0]['path'];
			$this->pageID=$cid[0]['id'];
		}
		return $this->pageID;
	}
	
	function determinePage($defaultCid) {
		$cd=intval($_GET['cid']);
		if(!$cd) {
			$cd=$this->getFromURL();
		}
		if($cd||($this->getFromURL()==0))
		{
			$cid=$cd;
			if($cid==0)
				$cid=intval($defaultCid);
			if($cid>0) {
				$this->path_url='/?cid='.$cid;
				return $this->pageID=$cid;
			}
		}
		if(intval($_GET['spid'])>0) {
			$this->pageID=$db->getOne("select cid from static_pages where id=".intval($_GET['spid']));
			
		}
		return $this->pageID;
	}
	
	function getExecScript() {
		if($this->_node!=null)
			return $this->_node['exec_script'];
		return '';
	}
	
	function getTemplate() {
		if($this->pageID==0)
			return self::DEFAUT_TEMPLATE ;
		$str_template=self::DEFAUT_TEMPLATE;
		if($this->_node==null) {
			$this->loadNode();
		}
		switch ($this->_node['page_template']) {
			case 2: {
				if(!empty($this->_node['exec_script'])) {
					$str_template=$this->_node['exec_script'];
					break;
				}
			}
		}
		return $str_template;
	}
	
	function getTreeValues() {
		return $this->_node;
	}
	
	function getNode() {
		return $this->_node;
	}
	
	function loadBody() {
		$renderer=null;
		$tp=$this->_node['type'];
		if(isset($_GET['spid'])) {
			$tp=1;
		}
		switch ($tp) {
			case 1: {
				$this->dataModel=new CStaticPage($this,intval($_GET['spid']));
				switch ((int)$this->_node['template']) {
					case 1:	//custom
					{
						if(!empty($this->_node['entry_point'])) {
							$str=$this->_node['entry_point'];
							$renderer=new $str($this->dataModel,$this);
						}
						else {
							$renderer=$this->dataModel;
						}
						break;
					}
					default: {
						$renderer=$this->dataModel;
						break;
					}
				}
				break;
			}
			case 2:	//news
			{
				
				$this->dataModel=new CNews($this,$_GET['p']);
				switch ((int)$this->_node['template']) {
					case 1:		//custom
					{
						if(!empty($this->_node['entry_point'])) {
							$str=$this->_node['entry_point'];
							$renderer=new $str($this->dataModel,$this);
						}
						else {
							$renderer=$this->dataModel;
						}
						break;
					}
					default: {
					/*	if(isset($this->templates['news'][(int)$this->_node['template']])&&!empty($this->templates['news'][(int)$this->_node['template']])) {
							$str=$this->templates['news'][(int)$this->_node['template']];
							$renderer=new $str($this->dataModel,$this);
						}
						else {
							$renderer=$this->dataModel;
						}*/
						$renderer=new News($this->dataModel,$this);
						break;
					}
				}
								
				break;
			}
			case 3:	//gallery
			{
				$this->dataModel=new CGallery($this,$_GET['p']);
				switch ((int)$this->_node['template']) {
					case 1:		//custom
					{
						if(!empty($this->_node['entry_point'])) {
							$str=$this->_node['entry_point'];
							$renderer=new $str($this->dataModel,$this);
						}
						else {
							$renderer=$this->dataModel;
						}
						break;
					}
					default: {
						if(isset($this->templates['gallery'][(int)$this->_node['template']])&&!empty($this->templates['gallery'][(int)$this->_node['template']])) {
							$str=$this->templates['gallery'][(int)$this->_node['template']];
							$renderer=new $str($this->dataModel,$this);
						}
						else {
							$renderer=$this->dataModel;
						}
						break;
					}
					break;
				}
				break;
			}
			case 10:	//surveys 
			{
				$renderer=new CSurvey($this);
				break;	
			}
			case 11:	//news_block
			{
				$renderer=new CNewsBlocks($this);
				break;
			}
		}
		if($renderer!=null)
			return $renderer->getBodyHTML($_GET);
		return '';
	}	
}

class CTest {
	private $App;
	
	function __construct($App) {
		$this->App=$App;
	}
	
	function getBodyHTML() {
		print_r($this->App);
	}
}

class CNewsCommon {
	protected $cn;	/* @var $cn CNews*/
	protected $App;	/* @var $App Application*/
	public $addPager=true;
	
	public $header=null;	
	
	function __construct($cn,$App) {
		$this->cn=$cn;
		$this->App=$App;
	}
		
	function renderNews($get,$addPic=true,$addHr=true,$fields=array('date'=>'pvNewsDate','title'=>'pvNewsTitle','subtitle'=>'pvNewsSubTitle','link'=>'pvNewsLinkRight'),$newsCount=0,$linkText=null,$useXMLForLanguage=false) {
		$lg=$this->App->getLanguage()	;
		$db=getdb();
		$title=$db->getone("select value from categories where id='{$get['cid']}'");
		if(isset($this->header)) {
			$title=$this->header;
		}
		
		$style='<div style="color:#FF6600;">';
		$header='<div class="header_grey"><span class="header_blue" >'.$title."</span></div>";
		//$img="<img src='/i/more.png' style='margin-right:5px;'>";
		$class='pvNewsLinkRight';
	
		if(empty($title)) {
			$header='';	
		}	
		if(is_null($linkText)) {
			if($useXMLForLanguage) {
				$linkText=CLanguage::getString(CLanguage::getLanguageString($this->App->getLanguage()),'link',$this->App->getPageId(),CLASS_NEWS,false,null,CLanguage::getXML());
			}
			else {
				$linkText=CLanguage::getString(CLanguage::getLanguageString($this->App->getLanguage()),'link',$this->App->getPageId(),CLASS_NEWS,false,CLanguage::getArray(),null);
			}
		}
		$news=$this->cn->getPage($this->cn->getCurrentPage(),$newsCount,$linkText);
		$str=CCommon::renderTemplate($news,$this->App->getPageId(),$this->App->getLanguage(),0,$title);
		if(empty($str)) {			
			$str.='<div class="news" style="">'.$header;
			
			$counter=0;
			if(is_array($news)&&count($news)>0) {
				foreach ($news as $k=>$v) {
					$counter++;
					if($addPic&&!empty($v['picture'])) {
						$str.="<a href='{$v['link_href']}'><img src='{$v['picture']}' class='pvNewsImg' alt='' /></a>";
					}
					if(is_array($fields)) {
						$str.=$style."{$v['date']}</div>";
						$str.="<div class='pvNewsTitle'>{$v['title']}</div>";
						$str.="<div class='pvNewsSubTitle'>{$v['subtitle']}</div>";
						$str.="<div class='".$class."'>".$img."{$v['link']}</div>";

						if($counter!=count($news)&&$addHr) {
							$str.="<hr />";
						}
					}
				}
			}
			else {
				if(empty($header)) {
					return '';
				}
			}
			$str.="</div>";
			
		}
		if($this->addPager) {
			$str.=$this->cn->getPager($get);
		}
		return CNews::renderNews_Sp($this->App->getPageId(),0,$this->cn->getCurrentPage()).$str;
	}
	
	
	function renderNewsById($id,$get) {
		
		if ($_GET['cid']==7||$tm[1]==7||$_GET['cid']==56||$tm[1]==56||$_GET['cid']==38){$style='<div style="color:#014E99;margin-top:10px;">';}
		else {$style='<div style="color:#FF6600;">';}
		$n=$this->cn->getNews($id);
		$node=$this->App->getTreeValues();
		$str=CCommon::renderTemplate($n,$this->App->getPageId(),$this->App->getLanguage(),$id);
	
		if(empty($str)) {
			$str=$style.$n['date']."</div>";
			$str.="<div><b>".$n['title']."</b></div>";
			$str.="<div class='newsBody'>{$n['body']}</div>";
		}
		return $str;
	}
	

}

class CNewsTemplaterepeater extends CNewsCommon {
	function __construct($cn,$App) {
		parent::__construct($cn,$App);
	}
	function getBodyHTML($get) {
		$id=intval($get['news_id']);
		
		if($id>0) {
			return $this->renderNewsById($id,$get);
		}
		return $this->renderNews($get);
	}
}

//class CNewsTemplate1 extends CNewsCommon {		//full - img(float:left) date,title,subtitle,link
//									
////	private $cn;	/* @var $cn CNews*/
////	private $App;	/* @var $App Application*/
//	
//	function __construct($cn,$App) {
//		parent::__construct($cn,$App);
//	}
//	function getBodyHTML($get,$useOtherCids=true,$otherCids=array()) {
//		static $st_otherCids=null;
//		$id=intval($get['news_id']);
//		
//		if($id>0) {
//			return $this->renderNewsById($id,$get);
//		}
//		
//		$db=getdb();
//		
//		if(!empty($otherCids)) {
//			$st_otherCids=$otherCids;
//		}
//		if($useOtherCids) {
//			if(empty($st_otherCids)) {
//				$st_otherCids=$db->getone("select php_news from categories where id='{$this->App->getPageId()}'");
//				$st_otherCids=unserialize($st_otherCids);
//			}
//			if(is_array($st_otherCids)&&!empty($st_otherCids)) {
//				$str='';
//				foreach ($st_otherCids as $k=>$v) {
//					$_GET['cid']=$k;
//					$title="<div style='background-color:green'>".$db->getone("select value from categories where id='{$k}'")."</div>";
//					$tmpApp=Application::getInstance($k);
//					$cn=new CNews($tmpApp);
//					$cn->setPageSize(1,true);
//					$t1=new CNewsTemplate1($cn,$tmpApp);					
//					$t1->addPager=false;
//				//	$t1->header=$title;
//					//$news=$cn->renderPage(0,0,false,(int)$v['page_size']);
//					$news=$t1->getBodyHTML($_GET,false);
//					if(!empty($news)) {
//						$str.=$title.$news;
//						//$str.=$news;
//					}
//				}
//			}
//			$_GET['cid']=$this->App->getPageId();
//			return $str;
//		}
//		
//		
//		return $this->renderNews($get);
//	/*	switch ($this->App->getPageId()) {
//			case 18:	//novini 
//			{
//				return $this->renderNews($get);
//			}
//			case 19:	//predstoq6ti
//			{
//				return $this->renderNews($get,'due_date>now()');
//			}
//			default: {
//				return $this->renderNews($get);
//			}
//			
//		}*/
//	}
//}

//class CNewsTemplate2 extends CNewsCommon {		//date and title as link
//										
////	private $cn;	/* @var $cn CNews*/
////	private $App;	/* @var $App Application*/
//	
//	function __construct($cn,$App) {
//		parent::__construct($cn,$App);
//	}
//	
//	function renderNews($get) {
//		return parent::renderNews($get,false,false,array('date'=>'pvNewsDate','link'=>'pvNewsTitleLink'),0,'');
//	}
//	
//	function getBodyHTML($get) {
//		$id=intval($get['news_id']);
//		
//		if($id>0) {
//			return $this->renderNewsById($id,$get);
//		}
//		switch ($this->App->getPageId()) {
//			case 18:	//novini 
//			{
//				return $this->renderNews($get);
//			}
//			case 19:	//predstoq6ti
//			{
//				return $this->renderNews($get,'due_date>now()');
//			}
//			default: {
//				return $this->renderNews($get);
//			}
//			
//		}
//	}
//}
//
//class CNewsTemplate3 extends CNewsCommon {	//title only as link
//	function __construct($cn,$App) {
//		parent::__construct($cn,$App);
//	}
//	
//	function renderNews($get) {
//		return parent::renderNews($get,false,false,array('link'=>'pvNewsTitleLink'),0,'');
//	}
//	
//	function getBodyHTML($get) {
//		$id=intval($get['news_id']);
//		
//		if($id>0) {
//			return $this->renderNewsById($id,$get);
//		}
//		return $this->renderNews($get);
//	}
//}
//
//class CNewsTemplate4 extends CNewsCommon {	//title only as link
//	function __construct($cn,$App) {
//		parent::__construct($cn,$App);
//	}
//	
//	function renderNews($get,$news_count=0,$addPic=false,$addHr=false,$link=null) {
//		return parent::renderNews($get,$addPic,$addHr,array('date'=>'pvNewsDate','subtitle'=>'pvNewsSubTitle','link'=>'pvNewsLinkRight'),$news_count,$link);
//	}
//	
//	function getBodyHTML($get,$news_count=0,$addPic=false,$addHr=false,$link=null) {
//		$id=intval($get['news_id']);
//		
//		if($id>0) {
//			return $this->renderNewsById($id,$get);
//		}
//		return $this->renderNews($get,$news_count,$addPic,$addHr,$link);
//	}
//}
//
//class COtherNews {
//	private $cn;	/* @var $cn CNews*/
//	private $App;	/* @var $App Application*/
//	public $view_all=true;
//	public $addhr=false;
//	public $addSubtitle=true;
//	public $addDate=false;
//	public $linkText='';
//	
//	function __construct($cn,$App) {
//		$this->cn=$cn;
//		$this->App=$App;
//		
//	}
//	
//	
//	function renderNews($get) {
//		$news=$this->cn->getPage($this->cn->getCurrentPage(),0,$this->linkText,false);
//		$str='<div class="newsOther">';
//		if(is_array($news)&&count($news)>0) {	
//			foreach ($news as $k=>$v) {
//				if($this->addDate)
//					$str.="<div class='pvNewsDateOther'>{$v['date']}</div>
//					<div class='pvNewsTitleOther'>{$v['title']}</div>";
//				else
//					$str.="<div class='pvNewsLinkOther'>{$v['link']}</div>";
//				$str.="<div class='pvNewsSubtitleOther'>{$v['subtitle']}</div>";
//				if($this->addDate) {
//					$str.="<div class='pvNewsLinkRightOther'>{$v['link']}</div>";
//				}
//				if($this->addhr)
//					$str.="<hr/>";
//			}
//		}
//		$str.="</div>".$this->cn->getPager($get);
//		return $str;
//	}
//	
//	function renderNewsById($id,$get) {
//		$n=$this->cn->getNews($id,'',false);
//		$str="<div class='newsTitleOther'>".$n['title']."</div>";
//		if($this->addSubtitle)
//			$str.="<div class='newsSubtitleOther'>".$n['subtitle']."</div>";
//		if($this->addDate)
//			$str.="<div class='newsDateOther'>".$n['date']."</div>";
//		
//		$str.="<div class='newsBodyOther'> {$n['body']}</div>";
//		return $str;
//	}
//	
//	function getBodyHTML($get) {
//		if($this->cn instanceof CStaticPage ) {
//			return $this->cn->getBodyHTML($get);
//		}
//		$id=intval($get['news_id']);
//		if($this->view_all) {
//			$this->cn->setFutureFilter(VISIBLE_ALL);
//			$this->cn->setVisibleFilter(VISIBLE_ALL);
//		}
//		if($id>0) {
//			
//			return $this->renderNewsById($id,$get);
//		}
//		return CNews::renderNews_Sp($this->App->getPageId(),0,$this->cn->getCurrentPage()). $this->renderNews($get);
//	}
//}

class CFormatedDate{
  
  var $DateFormat;
  var $TimeFormat;
  var $DateTimeFormat;
  var $date;
  var $Separator='/';
  
  function MyFormatedDate($date){
    $this->DateFormat = 'd/m/y';
    $this->TimeFormat = 'H:i';
    $this->DateTimeFormat = 'd/m/y H:i';
    $this->setDateTime($date);
  }
  
  function setSeparator($separator) {
  	$this->Separator=$separator;
  }
  
  function setDateTime($date){
    if(is_int($date)){
      $this->date = $date;
    } elseif(is_string($date)){
      if ($date=='0000-00-00' or $date=='0000-00-00 00:00:00' or $date=='') {
        $this->date = -1;
        return;
      }
      $date = eregi_replace('^ *([0-9]{1,2}) */ *([0-9]{1,2})(.*)', "\\2/\\1\\3", $date);
      if(eregi('[a-z]+', $date))
        $date = ereg_replace("[/-]", " ", $date);
      $this->date = strtotime($date);
    }
  }
  
  function formatedDate(){
    return $this->date>0 ? date($this->DateFormat, $this->date) : '';
  }
  
  function formatedDateTime(){
    return $this->date>0 ? date($this->DateTimeFormat, $this->date) : '';
  }
  
  function formatedTime(){
    return $this->date>0 ? date($this->TimeFormat, $this->date) : '';
  }
  
  function getISODate(){
    return date("d/m/y", $this->date);
  }
  
  function getDateTimeFromDB($input,$format) {
  	$retval='';
  	$arrFormat=split(' ',$format,3);
  	$arr=split(' ',$input,2);
  	if(isset($arr[0])) {
  		$retval=$this->getDateFromDB($arr[0],$arrFormat[0]);
  		if($retval!='') {
  			$retval.=' '.$arr[1];
  		}
  	}
  	return $retval;
  }
  
  function getDateFromDB($input,$format) {
  	$retval = '';
  	$strSep='';
  	if(strpos($input,"/")!==false) {
  		return $input;
  	}
  	$input=str_replace("/","-",$input);
  	if (ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $input, $regs)) {
		$arr=split('[/.-]',$format,3);
		for($i=0;$i<count($arr);$i++) {
			switch ($arr[$i]) {
				case 'd': {
					$retval.=$strSep.(strlen($regs[3])==1?'0'.$regs[3]:$regs[3]);
					$strSep=$this->Separator;
					break;
				}
				case 'm': {
					$retval.=$strSep.(strlen($regs[2])==1?'0'.$regs[2]:$regs[2]);
					$strSep=$this->Separator;
					break;
				}
				case 'y': {
					$year=$regs[1]%100;
					$retval.=$strSep.(strlen($year)==1?'0'.$year:$year);
					$strSep=$this->Separator;
					break;
				}
				case 'Y': {
					$year=$regs[1];
					$retval.=$strSep.(strlen($year)==1?'0'.$year:$year);
					$strSep=$this->Separator;
					break;
				}
			}
		}
		
  	}
  	return $retval;
  }
  
  function getISODateTime(){
    return date("Y-m-d H:i:s", $this->date);
  }
  
  function isLeapYear($year) {
  	if($year%4!=0)
  		return false;
  	if($year%100!=0)	//dali zavur6va na 00 (ex. 1900,2000,2100...)
  		return true;
  	return (($year/100)%4)==0;	// purvite 2 trqbwa da se delqt na 4 ina4e ne e leap
  }
  
  function time($input,$label,$format) {
  	list($h,$m,$s)=split(':',$input,3);
		$h=intval($h);
		$m=intval($m);
		$s=intval($s);
		if($h<0||$h>23)
			return false;
		if($m<0||$m>59)
			return false;		
		if($s<0||$s>59)
			$s=0;
  	$retval.=' '.($h<10?'0'.$h:$h).':'.($m<10?'0'.$m:$m).':'.($s<10?'0'.$s:$s);
  	return $retval;
  }
  
  function datetime($input,$label,$format) {
  	
  	$arr=split(' ',$input,2);
  	if(isset($arr[1])) {
		list($h,$m,$s)=split(':',$arr[1],3);
		$h=intval($h);
		$m=intval($m);
		$s=intval($s);
		if($h<0||$h>23)
			return false;
		if($m<0||$m>59)
			return false;		
		if($s<0||$s>59)
			$s=0;
	//		return false;
  	}
  	else {
  		return false;
  	}
  	$retval=$this->date($arr[0],'');
  	
  	if($retval!==false) {
  		$retval.=' '.($h<10?'0'.$h:$h).':'.($m<10?'0'.$m:$m).':'.($s<10?'0'.$s:$s);
  	}
  	return $retval;
  }
  
  function date($input,$label='',$format='')
    {
    	
    	if($input=='00/00/0000')
    		return '0000-00-00';
        $retval = false;

        //defaults
        $month_array =
array("jan"=>"01","feb"=>"02","mar"=>"03","apr"=>"04","may"=>"05","jun"=>"06
","jul"=>"07","aug"=>"08","sep"=>"09","oct"=>"10","nov"=>"11","dec"=>"12");
        $days_in_month = array(31,28,31,30,31,30,31,31,30,31,30,31);
       if (ereg ("^([0-9]{1,2})/([0-9]{1,2})/((19|20)?[0-9]{2})", $input, $regs)) 
//        if(ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4}))", $input, $regs)) 
//if(eregi("^([0-9]{1,2})-?(1|2|3|4|5|6|7|8|9|10|11|12|01|02|03|04|05|06|07|08|09)-?
//((19|20)?[0-9]{2})$",$input,$regs))

//if(eregi("^([0-9]{1,2})-?(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)-?
//((19|20)?[0-9]{2})$",$input,$regs))
        {
        	
        	
            //convert month to number
//            $regs[2] = strtolower($regs[2]);
           // $month = $month_array[$regs[2]];
           $month = $regs[2];

            //make days two digits if it's not already
            $day = $regs[1];
            if($day < 10 && strlen($day) == 1) { $day = "0" . $day; }
            if($regs[3] < 100)
            {
                //account for 2 digit year
                //< 30 = 20xx
                //> 30 = 19xx
                if($regs[3] < 30) { $year = "20" . $regs[3]; }
                else { $year = "19" . $regs[3]; }
            }
            else { $year = $regs[3]; }
            //verify day does not exceed days in month
            if(($day <= $days_in_month[$month-1] && $day > 0) || ($day==29
				&& $month==2 && $this->isLeapYear($year)))
            {
            	if($format=='')
                	$retval = $year.'-'.$month.'-'.$day;
                else $retval=$day.'/'.$month.'/'.$year;
            }
            else
            {
            	//invalid date
            }
        }
        else
        {
        	//invalid format
        }
        return $retval;
    }
}

/*class CPictures {
	
	static function getImageExtension($src) {
		list($width_orig, $height_orig,$info,$attr) = getimagesize($src);
		if($info==-1)
			return '';
		switch ($info) {
			case IMG_GIF: {
				return '.gif';
			}
			case IMG_JPG:
			case IMG_JPEG: {
				return '.jpg';
			}
			case 3:
			case IMG_PNG: {
				return '.png';
			}
			
			case IMG_WBMP: {
				return '.bmp';
			}
			
			
		}
		return '';
	}
	
	function createTumbnail($type,$src,$dst,$width=120,$height=120,$quality=100) {
		$info=-1;
		$attr=-1;
		list($width_orig, $height_orig,$info,$attr) = getimagesize($src);
		if($info==-1)
			return false;
		$widthScale = $width/$width_orig;
	    $heightScale = $height/$height_orig;

			
	    if($widthScale < $heightScale){
	    	$dst_w = $width;
	    	$dst_h = round($height_orig*$width/$width_orig);
	    } else {
	    	$dst_w = round($width_orig*$height/$height_orig);
	    	$dst_h = $height;
	    }


		// Resample
		$image_p = imagecreatetruecolor($dst_w, $dst_h);
		
		switch ($info) {
			case IMG_GIF: {
				$image = imagecreatefromgif($src);
				break;
			}
			case IMG_JPG:
			case IMG_JPEG: {
				$image = imagecreatefromjpeg($src);
				
				break;
			}
			case 3:
			case IMG_PNG: {
				$image = imagecreatefrompng($src);
				break;
			}
			
			case IMG_WBMP: {
				$image = imagecreatefromwbmp($src);
				break;
			}
			
			default: {
				$image = null;
				break;
			}
		}
		if($image==null||!$image)
			return false;
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $dst_w, $dst_h, $width_orig, $height_orig);

		$quality=(int)$quality;
		if (empty($quality)) {
			$quality=100;
		}
		
		switch ($info) {
			case IMG_GIF: {
				imagegif($image_p,$dst);
				return '.gif';
			}
			case IMG_JPG:
			case IMG_JPEG: {
				imagejpeg($image_p,$dst, $quality);
				return '.jpg';
			}
			case 3:
			case IMG_PNG: {
				imagepng($image_p,$dst);
				return '.png';
			}
			
			case IMG_WBMP: {
				imagewbmp($image_p,$dst);
				return '.bmp';
			}
			
			default: {
				return false;
				break;
			}
		}
		return false;
	}
} */



class CCalendarSimple {
	
	protected $language_id;
	protected $dayFunc='';
	protected $monthFunc='';
	
	function __construct($language_id,$dayFunc='',$monthFunc='') {
		$this->language_id=$language_id;
		$this->dayFunc=$dayFunc;
		$this->monthFunc=$monthFunc;
	}
	
	function drawHeader($startDayOfWeek=CALENDAR_MONDAY) {
		$days=CLanguage::getDayNames($this->language_id);
		$str.="<tr>";
		//foreach ($days as $k=>$v) {
		for($i=0;$i<7;$i++) {
			$v=$days[($i+$startDayOfWeek)%7];
			$str.="<td class='pvCalendarHeader'>{$v}</td>";
		}
		return $str."</tr>";
	}
	
	function formatDate($day,$month,$year) {
		return ($year<1000?2000+$year:(int)$year).'-'.((int)$month<10?'0'.((int)$month):(int)$month).'-'.($day<10?'0'.((int)$day):(int)$day);
	}
	
	function renderDay($day,$month,$year) {
		$today_formated=$this->formatDate($day,$month,$year);
		$days=date('t',mktime(0,0,0,$month,1,$year));
		$is_today=$today_formated==date('Y-m-d')?"class='pvCalendarToday'":"";
		$str=<<<EOD
		<td {$is_today}>
		<table cellpadding='0' cellspacing='0'>
		<tr><td class='pvCDay'>{$day}</td></tr>
		<tr><td class='pvCLink'>
EOD;
		if(function_exists($this->dayFunc)) {
			$str.=call_user_func($this->dayFunc,array('day'=>$day,'month'=>$month,'year'=>$year,'formated_date'=>$today_formated));
		}
		$str.="</td></tr></table></td>";
		return $str;
	}
	
	function getNextMonth($month,$year) {
		$month++;
		$year=$month>12?$year+1:$year;
		$month=$month>12?1:$month;
		return array('month'=>$month,'year'=>$year);
	}
	
	function getPrevMonth($month,$year) {
		$month--;
		$year=$month<1?$year-1:$year;
		$month=$month<1?12:$month;
		return array('month'=>$month,'year'=>$year);
	}
	
	function renderMonth($day,$month,$year,$startDayOfWeek=CALENDAR_MONDAY) {
		$array=array_fill(0,35,'');
		$days=date('t',mktime(0,0,0,$month,1,$year));
		$first_day=date('w',mktime(0,0,0,$month,1,$this->year));
		$f=$first_day-$startDayOfWeek;
		if($f<0) {
			$f=7+$f;
		}
		$last_day=date('w',mktime(0,0,0,$month,$days,$this->year));
		$f1=$last_day-$startDayOfWeek;
		if($f1<0) {
			$f1=7+$f1;
		}
		echo $last_day=$days-$f1;
		for($i=1;$i<=$days;$i++,$f++) {
			if($i==$last_day) {
				if(empty($array[$f1])) {
					$f=0;
				}
			}
			$array[$f]=$this->renderDay($i,$month,$year);
		}
		for($i=0;$i<=$days;$i++) {
			if(empty($array[$i]))
				$array[$i]="<td style='border:none;'>&nbsp;</td>";
		}
		$str="<table width='100%' border='0' cellpadding='0' cellspacing='1' class='pvCalendar'>
			<col width='14%'><col width='15%'><col width='14%'><col width='14%'><col width='15%'>
			<col width='14%'><col width='14%'>";
		$str.=$this->drawHeader($startDayOfWeek);
		$str.=FE_Utils::createTableCells($array,7,'');
		return $str."</table>";
		
	}
	
	/*function renderMonth($day,$month,$year,$startDayOfWeek=CALENDAR_MONDAY) {
		$days=date('t',mktime(0,0,0,$month,1,$year));
		return $this->renderMonthNew($day,$month,$year,$startDayOfWeek);
		$first_day=(date('w',mktime(0,0,0,$month,1,$this->year))-$startDayOfWeek);
		if($first_day<0) $first_day=6;
		echo $first_day;
		echo "<br />";
		$str="<table width='100%' border='0' cellpadding='0' cellspacing='1' class='pvCalendar'>
			<col width='14%'><col width='15%'><col width='14%'><col width='14%'><col width='15%'>
			<col width='14%'><col width='14%'>";
		$str.=$this->drawHeader($startDayOfWeek);
		$str.="<tr>";
		$i=0;
		$h=0;
		$last_day=(date('w',mktime(0,0,0,$month,$days,$this->year))-$startDayOfWeek);
		if($last_day<0) $last_day=6;
		echo $last_day;
		echo "<br />";	
		if($last_day<$first_day&&$month!=2)
		{
			$st=$days;
			while (date('w',mktime(0,0,0,$month,$st,$this->year))-$startDayOfWeek!=0) {
				$st--;			
			}
			$last_day=$st;
			while($st<=$days) {
				$str.=$this->renderDay($st,$month,$year);
				$st++;
				$h++;
				$i++;
			}
			
		}
		else $last_day=$days+1;
		
		while($i<$first_day) {
			$str.="<td></td>";
			$h++;
			$i++;
		}
		for($i=1;$i<$last_day;$i++) {
			if($h%7==0) {
				$str.="</tr><tr>";
			}
			$str.=$this->renderDay($i,$month,$year);
			$h++;
		}
		$str.="</tr></table>";
		return $str;
	}*/
	
	function getLinkForMonth($month,$year) {
		$q=$_GET;
		$q['month']=$month;
		$q['year']=$year;
		return UT_userfunctions::getGetString($q);
	}
	
	function getLinks($month,$year) {
		$prev=$this->getPrevMonth($month,$year);
		$next=$this->getNextMonth($month,$year);
		$str_prev=$this->getLinkForMonth($prev['month'],$prev['year']);
		$str_next=$this->getLinkForMonth($next['month'],$next['year']);
		$str_today=$this->getLinkForMonth(date('m'),date('Y'));
		
		$prev_msg=CLanguage::translate($this->language_id,'Previous');
		$next_msg=CLanguage::translate($this->language_id,'Next');
		$today_month=CLanguage::translate($this->language_id,date('F',mktime(0,0,0,$month,1,$year)));
		$today=CLanguage::translate($this->language_id,date('F')).' '.date('Y');
		
		return <<<EOD
		<table border='0' cellpadding='5' cellspacing='0' class='pvCalendarNav'>
		<col width='33%' align='left'>
		<col width='34%' align='center'>
		<col width='33%' align='right'>
		<tr><td colspan='3' style='padding:5px;color:#0E076F;font-weight:bold' align='center'>{$today_month}</td></tr>
		<tr><td><a href='/?{$str_prev}'>{$prev_msg}</a></td>
		<td><a href='/?{$str_today}'>{$today}</a></td>
		<td><a href='/?{$str_next}'>{$next_msg}</a></td></tr></table>
EOD;
	}
	
	function render($get,$addPrevNextLinks=CALENDAR_LINKS_POSITION_BOTH,$month='',$year='',$startDayOfWeek=CALENDAR_MONDAY) {	
		$month=empty($month)?(isset($get['month'])?(int)$get['month']:date('m')):$month;
		$year=empty($year)?(isset($get['year'])?(int)$get['year']:date('Y')):$year;
		if($addPrevNextLinks) {
			$links=$this->getLinks($month,$year);
		}
		return ($addPrevNextLinks&CALENDAR_LINKS_POSITION_TOP?$links:'').$this->renderMonth(1,$month,$year,$startDayOfWeek).($addPrevNextLinks&CALENDAR_LINKS_POSITION_BOTTOM?$links:'');
	}
	
}

?>
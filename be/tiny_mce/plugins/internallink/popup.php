<?php

if(get_magic_quotes_gpc()||get_magic_quotes_runtime())
{
	if(isset($_GET))
		$_GET=array_map(array('CRemSlashes','_StripSlashes'),$_GET);
	if(isset($_POST))
		$_POST=array_map(array('CRemSlashes','_StripSlashes'),$_POST);
	if(isset($_REQUEST))
		$_REQUEST=array_map(array('CRemSlashes','_StripSlashes'),$_REQUEST);
	if(isset($_COOKIE))
		$_COOKIE=array_map(array('CRemSlashes','_StripSlashes'),$_COOKIE);
}

class CRemSlashes {
	static function _StripSlashes(&$data)
	{
		return is_array($data)?array_map(array('CRemSlashes','_StripSlashes'),$data):stripslashes($data);
	}
}

$pages=array(
	1=>"static_pages",
	2=>"news",
);
if(isset($_POST['type'])) {
	$type=(int)$_POST['type'];
}

if(!$type) {
	$type=1;
}



$GLOBALS['__selector']=$selector=$_GET['selector']='TinyMCE_internallink_insertMyLink(';
$force_search=true;

require_once(dirname(__FILE__).'/../../../libCommon.php');
require_once(dirname(__FILE__).'/../../../common/template_index.php');
$__template_index=new IndexTemplate(0);
$__template_index->hidden['type']="<input type='hidden' name='type' value='{$_POST['type']}'>";

?>
<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
<script>
function TinyMCE_internallink_insertMyLink(href,text) {
	var ed=tinyMCEPopup.editor;
	var n = ed.selection.getNode();
	if(n.nodeName=='A'){
		ed.execCommand('mceInsertLink', false, href);
	} else if(ed.selection.isCollapsed()) {
			href = href.replace('"', "&quot;");
			text = text.replace(/&quot;/g, '"');
			ed.execCommand('mceInsertContent', false, '<a href="'+href+'">'+text+'</a>');
	} else {
		ed.execCommand('mceInsertLink', false, href);
	}
}
</script>
<?
include(dirname(__FILE__)."/{$pages[$type]}/index.php");
return;
?>
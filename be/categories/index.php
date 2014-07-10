<?php
ob_start();
require_once('../libCommon.php');
require_once(dirname(__FILE__).'/CCustomListBox.php');
CUserRights::checkRights("categories");
	$Tree = new CURLTree("categories");

	$node = (int)$_REQUEST['node'];
	
	$tree2=(int)$_REQUEST['tree2'];
	
	function is_deletable($node_id) {
		$db=getdb();
		if(((int)$db->getone("select not_deletable from categories where id=?",array($node_id)))!==0) {
			echo "Operation not allowed!";
			return false;
		}
		return true;
	}
	
	function initNode($id) {
		if(is_array($GLOBALS['CONFIG']['FEPageTypes'])) {
			$a=array_keys($GLOBALS['CONFIG']['FEPageTypes']);
			if(!isset($a[0])) {
				return;
			}
		}
		else {
			return;
		}
		
		$type=$a[0];
		$template=0;
		if(isset($GLOBALS['CONFIG'][$GLOBALS['CONFIG']['FEPageTypes'][$type]['class']])) {
			if(is_array($GLOBALS['CONFIG'][$GLOBALS['CONFIG']['FEPageTypes'][$type]['class']]['templates'])) {
				$t=array_keys($GLOBALS['CONFIG'][$GLOBALS['CONFIG']['FEPageTypes'][$type]['class']]['templates']);
				if(isset($t[0])) {
					$template=$t[0];
				}				
			}
		}
		$db=getdb();
		$db->Execute("update categories set type_id=?,template_id=? where id=?",array($type,$template,$id));
	}
	
	if( isset($_REQUEST['submitdel']) && ( $node > 0 ) )
	{
		if(is_deletable($node)) {
			$Tree->delete_node($node);
		}
		
	}

	if( isset($_REQUEST['submitadd']) && $_POST['newvalue'])
	{
		//header("Location: category_edit.php?pid=$node");
		//exit;
		$node =(int) $Tree->insertAfter($node, trim($_POST['newvalue']));
		if(!$node) {
			$db=getdb();
			$node=$db->getone("select id from categories where pid=1 order by id desc limit 1");
		}
		initNode($node);
	}
	
	if( isset($_REQUEST['submitaddchild']) && $_POST['newvalue'])
	{
		//header("Location: category_edit.php?pid=$node");
		//exit;
		$node = $Tree->add_node($node, trim($_POST['newvalue']));
		initNode($node);
	}

	if( isset($_REQUEST['submitupdate']) && ($node > 0))
	{
		header("Location: edit.php?id=$node");
		exit;
	}
	
	if( isset($_REQUEST['submitup']) && ($node > 0) )
	{
		$Tree->move_node_up($node);
	}
	
	if( isset($_REQUEST['mvNode']) && ($node > 0) )
	{
		$Tree->moveAfter($node,$tree2);
	}
	
	if( isset($_REQUEST['mvChild']) && ($node > 0) )
	{
		$Tree->moveAsFirstChildOf($node,$tree2);
	}

	if( isset($_REQUEST['submitdown']) && ($node > 0) )
	{
		$Tree->move_node_down($node);
	}

	if( isset($_REQUEST['submitleft']) && ($node > 0) )
	{
		if(is_deletable($node)) {
			$Tree->move_node_left($node);
		}
	}

	if( isset($_REQUEST['submitright']) && ($node > 0) )
	{
		if(is_deletable($node)) {
			$Tree->move_node_right($node);
		}
	}
	
	if( isset($_REQUEST['btDelete']) && ($node > 0) )
	{
		if(is_deletable($node)) {
			include(dirname(__FILE__).'/controls.php');
			
			$db=getdb();
			$SQL = "SELECT l, weight FROM categories WHERE id='$node'";
	    	$row = $db->getRow($SQL);
	    
	    	
		    $l = (int)$row["l"];
		    $weight = (int)$row["weight"];
		    $right = $l + $weight;
			$SQL = "SELECT id FROM categories WHERE (l BETWEEN {$l} AND {$right})";
	      	$delete_ids = $db->getCol($SQL);
	      	
	      	foreach ($delete_ids as $del_id) {      
				ControlValues::deleteManagedImages($del_id,$con['controls'],false);			
	      	}
			$Tree->delete_node($node);
		}
	}
	
	$t_options=$Tree->get_tree_options();
	foreach ($t_options as $k=>&$v) {
		$v.="&nbsp;({$k})";
	}
	
	$listbox=new CCustomListBox('node',$node);
		
?>
<html  style="height:100%">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>BE</title>
<link rel="stylesheet" href="<?=BE_CSS_DIR;?>lib.css">
<script src="lib.js"></script>

<script>
function confDelete() {
	var node=document.getElementById('node');
	if(!node||node=='undefined')
		return false;
	if(node.selectedIndex>-1) {
		var val=node.options[node.selectedIndex].text;
		return window.confirm("Node "+val+" will be deleted!\nContinue?");
	}
	else {
		window.status='Select node';
	}
	return false;
}

function confDelete1() {
	var node=document.getElementById('node');
	if(!node_selected) {
		return false;
	}
	var val=node_selected.innerText;
	return window.confirm("Node "+val+" will be deleted!\nContinue?");
	return false;
}


</script>

<?=$listbox->renderScript();?>

</head>
<body style="height:100%">
<form method="post" name=f1>
<table class="viewHeader" cellpadding="0" cellspacing="0" >
	<tr>
		<td width="1"   class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Menu</td>
		<td width="1"  class="viewHeaderRight"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
	</tr>	
</table>
<table cellpadding="5" cellspacing="0" class="table" align="center" border='0' height="90%">
<tr>
	<td valign="top" width="50%" align="left">
	<?/*=$listbox->render($t_options);*/?>
	
	<hr />
		<select  name="node" id='node' style="width:100%;height:98%;" size="30">
			<option value="0">Root
  				<?=CLib::draw_listbox_options($t_options, $node);?>
		</select>
	
	</td>
	<td valign="top" align="left">
	<fieldset>
		<legend>Options</legend>
		<span>All operations are performed over the selected node from the left tree<br />to the selected node in the combo box below<br /></span>
		<div align="left" style="padding:5px">
			<select name="tree2" style="width:95%">
				<?=CLib::draw_listbox_options($t_options,$tree2);?>
			</select>
		</div>
		<div align="left" style="padding:2px">
			<input type="submit" name="mvChild" value="Move as child" style="background:url(<?=BE_IMG_DIR;?>design/move_as_next.png) top left;width:100px;height:20px;border:none;" />
			<input type="submit" name="mvNode" value="Move under as next node" style="background:url(<?=BE_IMG_DIR;?>design/move_as_next_node.png) top left;width:168px;height:20px;border:none;" />
		</div>

	</fieldset>
	<br />
	<table width="100%" cellpadding="0" cellspacing="5">
		<tr>
			<td valign="top" width="200">
				<div style="background:url(<?=BE_IMG_DIR;?>design/new_node_bgr.png) top left no-repeat;padding:3px 5px;font-size:13px;color:#333333;font-weight:bold;width:186px;">Add new node</div>
				<div style="padding:5px 3px 5px 7px;width:186px;border:1px solid #CCCCCC">
					<input type="text" name="newvalue" id="newvalue" style="width:170px;border:1px solid #7F9DB9;font-size:13px;" /><br /><br />
					<input type="submit" style="background:url(<?=BE_IMG_DIR;?>design/node_add_after.png) top left no-repeat;border:none;width:74px;height:20px;" name="submitadd" value="Add After" /> <input type="submit" style="background:url(<?=BE_IMG_DIR;?>design/node_add_child.png) top left no-repeat;border:none;width:92px;height:20px;" name="submitaddchild" value="Add as Child" />
				</div>
			</td>
			<td align="left" height="100%" style="background:url(<?=BE_IMG_DIR;?>design/node_edit_bgr.png) 50% 50% no-repeat;width:96px;padding:5px;">
			<table width="100%" cellpadding="0" cellspacing="0" height="100%" border="0">
			<tr>
				<td colspan="3" align="center" valign="bottom"><input type="submit" name="submitup" value="" style="cursor:hand;width:10px;height:12px;background:url(<?=BE_IMG_DIR;?>design/arrow_up.png) top left no-repeat;border:none;" /></td>
			</tr>
			<tr>
				<td align="right"><input type="submit" name="submitleft" value="" style="cursor:hand;width:12px;height:10px;background:url(<?=BE_IMG_DIR;?>design/arrow_left.png) top left no-repeat;border:none;" /></td>
				<td align="center"><input type="submit" name="submitupdate" value="Edit" style="background:url(<?=BE_IMG_DIR;?>design/button_edit_bgr.png) top left no-repeat;height:20px;width:38px;border:none;"></td>
				<td><input type="submit" name="submitright" value="" style="cursor:hand;width:12px;height:10px;background:url(<?=BE_IMG_DIR;?>design/arrow_right.png) top left no-repeat;border:none;" /></td>
			</tr>
			<tr>
				<td colspan="3" align="center" valign="top"><input type="submit" name="submitdown" value="" style="cursor:hand;width:10px;height:12px;background:url(<?=BE_IMG_DIR;?>design/arrow_down.png) top left no-repeat;border:none;" /></td>
			</tr>
			
			
			</table>
			</td>
			<td>&nbsp;</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<div align=justify>

<h5>Website Structure</h5>
<p>Use this facility to edit and define the structure of the website. Use it with CAUTION as it will affect the structure of the website and how it is presented to the visitors.</p>
<p><em>* Note that this facility is used for editing the website structure and not for editing individual pages. If you want to edit the content of individual pages use the PAGES section.</em></p>
<h5>Help</h5>
<p><strong>UP Arrow</strong> - it will move the selected section up one position in the list.</p>
<p><strong>DOWN Arrow</strong> - it will move the selected section down one position in the list.</p>
<p><strong>LEFT Arrow</strong> - it will move the selected section one level up in the website structure.</p>
<p><strong>RIGHT Arrow</strong> - it will move the selected section and its subsections (if any) one level down level in the website structure.</p>
<p><strong>ADD Arrow</strong> - it will add a new section with a chosen name within the website structure. It will add it as a subsection of the selected section.</p>
<p><strong>EDIT Button</strong> - it will edit the selected section's name or URL path or delete the section altogether.</p>
</div>
</form>
</body>
</html>
<?php
ob_end_flush();
?>
<table class="viewHeader" cellpadding="0" cellspacing="0">
	
	<tr><td  class="viewList" colspan="4"><div>Отговори</div></td></tr>
</table>
<table class="table" cellpadding="5" cellspacing="0" align="center">
<col align="right" width="1%"> 
<col align="center" width="89%">
<col align="center" width="5%">
<col align="center" width="5%">

<tr>
	<th></th>
	<th>Question</th>
	<th>Delete</th>
	<th>Num. votes</th>
</tr>
<?php
$db=getdb();
$poll_id=$in_edit_id;



if(isset($_POST['btOptions'])) {
	foreach($_POST['options'] as $k=>$v) {
		$del_op=(int)$_POST['del'][$k];
		if($del_op) {
			$db->Execute("delete from polls_options where id=?",array($del_op));
		}
		else {
			if($k==0&&trim($v)=="") {
				continue;
			}
			$data=array();
			$data['option_text']=$v;
			$data['poll_id']=$poll_id;
			ControlWriter::Write("polls_options",$data,$k);
		}
	}
}

$options=$db->getAssoc("select * from polls_options where poll_id=?",array($poll_id));
$index=1;
foreach ($options as $k=>$v) {
	$val=htmlspecialchars($v['option_text']);
	echo <<<EOD
	<tr>
		<td>{$index}.</td>
		<td><input name="options[{$k}]" value="{$val}" style="width:90%" /></td>
		<td><input type="checkbox" name="del[{$k}]" value="{$k}" /></td>
		<td>{$v['num_votes']}</td>
	</tr>
EOD;
$index++;
}

?>
<tr>
	<td><?=$index;?>.</td>
	<td><input name="options[0]" value="" style="width:90%" /></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
<td colspan="4" align="center"><input class="submit" type="submit" value="Change" name="btOptions" /></td>
</tr>
</table>
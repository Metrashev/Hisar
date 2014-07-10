<html>
	<table id="dg_mq_mail_groups" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id'  field_name="id" class="header_add"><a href='<?=BE_DIR;?>mq_mail_groups/edit.php?<?=FE_Utils::getBackLink();?>'>New</a></td>
<td in_index="2"  id='_name' field_name="name"  class="header_nor"><a order="name">Name</a></td>
<td in_index="5"  id='_mails_count' field_name="mails_count"  class="header_nor"><a order="mails_count">Mails count</a></td>
<td in_index="6" id='t6' field_name="id"  class="delete" href='#'><a>Delete</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id" style='color:red' href='<?=BE_DIR;?>mq_mail_groups/edit.php?id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Edit</a>
		<?} else { ?>
			<input class="DataGridNew" userfunc="setCheckBox" type="hidden" name="_#CONTROL#_[fields][_hch_sel_][_#UNIQUE#_]" id="_#CONTROL#__hch_sel__#UNIQUE#_" value="" />
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" onclick="document.getElementById('_#CONTROL#__hch_sel__#UNIQUE#_').value=this.checked?'1':'0'" value="1" />
		<? } ?>
		</td>
			<td><ITTI field_name="name"   ></ITTI></td>
			<td><ITTI field_name="mails_count"   ></ITTI></td>
<td><a field_name="id" href='#' onclick='if(window.confirm("Are you shure?")) {document.getElementById("hdDeletemq_mail_groups").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Delete</a></td>
</tr>
	</tbody>
	</table>
</html>
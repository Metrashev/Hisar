<html>
	<table id="dg_users" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id'  field_name="id" class="header_add"><a href='<?=BE_DIR;?>users/edit.php?<?=FE_Utils::getBackLink();?>'>new</a></td>
<td in_index="2"  id='_name' field_name="name"  class="header_nor"><a order="name">Name</a></td>
<td in_index="3"  id='_username' field_name="username"  class="header_nor"><a order="username">User name</a></td>
<td in_index="5"  id='_status_id' field_name="status_id"  class="header_nor"><a order="user_rights_id">Status</a></td>
<td in_index="6"  id='_is_active' field_name="is_active"  class="header_nor"><a order="is_active">Active</a></td>
<td in_index="7" id='t7' field_name="id"  class="delete" href='#'><a>Delete</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id" style='color:red' href='<?=BE_DIR;?>users/edit.php?id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Edit</a>
		<?} else { ?>
			<input class="DataGridNew" userfunc="setCheckBox" type="hidden" name="_#CONTROL#_[fields][_hch_sel_][_#UNIQUE#_]" id="_#CONTROL#__hch_sel__#UNIQUE#_" value="" />
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" onclick="document.getElementById('_#CONTROL#__hch_sel__#UNIQUE#_').value=this.checked?'1':'0'" value="1" />
		<? } ?>
		</td>
			<td><ITTI field_name="name"   ></ITTI></td>
			<td><ITTI field_name="username"   ></ITTI></td>
			
			<td><ITTI field_name="user_rights_id" sql="select name from user_group_rights where id='_#VAL#_'"  ></ITTI></td>
			<td><ITTI field_name="is_active" arrayname="YES_NO"  ></ITTI></td>
<td><a field_name="id" href='#' onclick='if(window.confirm("Сигурни ли сте?")) {document.getElementById("hdDeleteusers").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Delete</a></td>
</tr>
	</tbody>
	</table>
</html>
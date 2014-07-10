<html>
	<table id="dg_user_group_rights" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id'  field_name="id" class="header_add"><a href='<?=BE_DIR;?>user_group_rights/edit.php?<?=FE_Utils::getBackLink();?>'>New</a></td>
<td in_index="2"  id='_user_group_id' field_name="user_group_id"  class="header_nor"><a order="name">User group</a></td>
<td in_index="3"  id='_resources' field_name="resources"  class="header_nor"><a order="resources">Resources</a></td>
<td in_index="4"  id='_cids' field_name="cids"  class="header_nor"><a order="cids">Cids</a></td>
<td in_index="5" id='t5' field_name="id"  class="delete" href='#'><a>Delete</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id" style='color:red' href='<?=BE_DIR;?>user_group_rights/edit.php?id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Edit</a>
		<?} else { 
			if($_GET['search']=='single') { ?>
			<a field_name="id" style='color:red' href="<?=htmlspecialchars($_GET['bkp']);?>&amp;return_key=_#VAL#_&amp;return_point=<?=$_GET['return_point'];?>">Избери</a>
		<?	} else { ?>
			<input class="DataGridNew" userfunc="setCheckBox" type="hidden" name="_#CONTROL#_[fields][_hch_sel_][_#UNIQUE#_]" id="_#CONTROL#__hch_sel__#UNIQUE#_" value="" />
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" onclick="document.getElementById('_#CONTROL#__hch_sel__#UNIQUE#_').value=this.checked?'1':'0'" value="1" />
			<? } ?>
		<? } ?>
		</td>
			<td><ITTI field_name="name"></ITTI></td>
			<td><ITTI field_name="resources" multi_arrayname="resources_array"  ></ITTI></td>
			<td><ITTI field_name="cids"  multi_sql="select value from categories where id in(_#VAL#_) order by l" ></ITTI></td>
<td><a field_name="id" href='#' onclick='if(window.confirm("Are You Sure?")) {document.getElementById("hdDeleteuser_group_rights").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Delete</a></td>
</tr>
	</tbody>
	</table>
</html>
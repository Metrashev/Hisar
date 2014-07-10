<html>
	<table id="dg_members" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id'  field_name="id" class="header_add"><a href='<?=BE_DIR;?>members/edit.php?<?=FE_Utils::getBackLink();?>'>New</a></td>
<td in_index="2"  id='_first_name' field_name="first_name"  class="header_nor"><a order="first_name">First name</a></td>
<td in_index="3"  id='_mid_name' field_name="mid_name"  class="header_nor"><a order="mid_name">Middle name</a></td>
<td in_index="4"  id='_last_name' field_name="last_name"  class="header_nor"><a order="last_name">Last name</a></td>
<td in_index="12"  id='_email' field_name="email"  class="header_nor"><a order="email">Email</a></td>
<td in_index="15"  id='_is_active' field_name="is_active"  class="header_nor"><a order="is_active">Active</a></td>
<td in_index="17" id='t17' field_name="id"  class="delete" href='#'><a>Delete</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id" style='color:red' href='<?=BE_DIR;?>members/edit.php?id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Edit</a>
		<?} else { 
			if($_GET['search']=='single') { ?>
			<a field_name="id" style='color:red' href="<?=htmlspecialchars($_GET['bkp']);?>&amp;return_key=_#VAL#_&amp;return_point=<?=$_GET['return_point'];?>">Избери</a>
		<?	} else { ?>
			<input class="DataGridNew" userfunc="setCheckBox" type="hidden" name="_#CONTROL#_[fields][_hch_sel_][_#UNIQUE#_]" id="_#CONTROL#__hch_sel__#UNIQUE#_" value="" />
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" onclick="document.getElementById('_#CONTROL#__hch_sel__#UNIQUE#_').value=this.checked?'1':'0'" value="1" />
			<? } ?>
		<? } ?>
		</td>
			<td><ITTI field_name="first_name"   ></ITTI></td>
			<td><ITTI field_name="mid_name"   ></ITTI></td>
			<td><ITTI field_name="last_name"   ></ITTI></td>
			<td><ITTI field_name="email"   ></ITTI></td>
			<td><ITTI field_name="is_active" arrayname="YES_NO"   ></ITTI></td>
<td><a field_name="id" href='#' onclick='if(window.confirm("Are You Sure?")) {document.getElementById("hdDeletemembers").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Delete</a></td>
</tr>
	</tbody>
	</table>
</html>
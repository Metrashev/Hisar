<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1"  class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Polls</td>
		<td width="1" class="viewHeaderRight"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
	</tr>
	<tr><td  class="viewList" colspan="4"><div>Edit</div></td></tr>
</table>
<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="4" width="0*">
<col width='15%' align='right'>
<col width='35%' align='left'>
<col width='15%' align='right'>
<col width='35%*' align='left'>
</colgroup>
<tbody>

<td><label for="question">Question</label></td>
<td colspan="3"><ITTI field_name='question' style="width:90%"></ITTI></td>
</tr>
<tr>

<td><label for="active_from_date">Active from</label></td>
<td><ITTI field_name='active_from_date'></ITTI></td>
<td><label for="active_to_date">to</label></td>
<td><ITTI field_name='active_to_date'></ITTI></td>
</tr>
<tr>
<td><label for="position">Possition</label></td>
<td><ITTI field_name='position'></ITTI></td>
<td></td>
<td><ITTI field_name='visible'></ITTI> <label for="visible">Visible</label></td>

</tr>
	<tr>
		<td colspan="4" align="center" style="padding-right:10px;">
							<input class="submit" type="submit" name="btSave" value="Save" />&nbsp;&nbsp;&nbsp;<input class="submit" type="button" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" />
	</td></tr>
</tbody></table>
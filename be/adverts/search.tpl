<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1"  class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Advertisement</td>
		<td width="1" class="viewHeaderRight"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
	</tr>
	<tr><td  class="viewList" colspan="4"><div>Search</div></td></tr>
</table>

<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="2" width="0*">
<col width='50%' align='right'>
<col width='50%*' align='left'>
</colgroup>
<tbody>
<tr>
<td><label for="advertiser">Advertiser</label></td>
<td><ITTI field_name='advertiser'></ITTI></td>
</tr>


<tr>
<td><label onclick="document.getElementById('position_id').focus();">possition</label></td>
<td><ITTI field_name='position_id'></ITTI></td>
</tr>
	
	<tr>
		<td colspan="2" align="center" style="padding-right:10px;">
							<input class="submit" type="submit" name="search" value="Search" />&nbsp;&nbsp;&nbsp;<input class="submit" type="submit" name='btClear' value="Clear" />
	</td></tr>
</tbody></table>
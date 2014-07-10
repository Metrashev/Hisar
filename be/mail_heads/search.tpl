<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8"><table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1"  class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Mail Heads</td>
		<td width="1" class="viewHeaderRight"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
	</tr>
	<tr><td  class="viewList" colspan="3">				<div></div></td></tr>
</table>

<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="4" width="0*">
<col width='25%' align='right'>
<col width='25%' align='left'>
<col width='25%' align='right'>
<col width='25%*' align='left'>
</colgroup>
<tbody><tr>
<td><label for="name">Name</label></td>
<td><ITTI field_name='name'></ITTI></td>
<td><label for="is_approved_s">Approved</label></td>
<td><ITTI field_name='is_approved_s'></ITTI></td>
</tr>
<tr>
<td><label for="start_date">Sending initiation</label></td>
<td><ITTI field_name='start_date'></ITTI></td>
<td><label for="_to_start_date">to</label></td>
<td><ITTI field_name='_to_start_date'></ITTI></td>
</tr>
<tr>
<td><label for="delete_after_sent_s">Delete after Sending</label></td>
<td colspan="3"><ITTI field_name='delete_after_sent_s'></ITTI></td>
</tr>		
	<tr>
		<td colspan="4" align="center" style="padding-right:10px;">
							<input type="submit" class="submit" name="search" value="Search" />&nbsp;&nbsp;&nbsp;<input type="submit" name='btClear' class="submit" value="Clear" />
	</td></tr>
</tbody></table>
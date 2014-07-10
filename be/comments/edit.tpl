<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8"><table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1"  class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Comments</td>
		<td width="1" class="viewHeaderRight"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
	</tr>
	<tr><td  class="viewList" colspan="3">				<div>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td>Edit</td>
					<td align="right">				<input type="submit" class="submit" name="btSave" value="Save" />&nbsp;&nbsp;&nbsp;<input type="button" class="submit" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" /></td>
				</tr>
			</table>
		</div></td></tr>
</table>

<table cellpadding="5" cellspacing="0" class="table" align="center" border='0'>
<colgroup span="4" width="0*">
<col width='15%' align='right'>
<col width='35%' align='left'>
<col width='15%' align='right'>
<col width='35%*' align='left'>
</colgroup>
<tbody>
<tr>
<td><label for="name">Име</label></td>
<td><ITTI field_name='name' style="width:90%"></ITTI></td>
<td><label for="email">E-mail</label></td>
<td><ITTI field_name='email' style="width:90%"></ITTI></td>
</tr>
<tr>
<? /*
<td><label for="phone">Phone</label></td>
<td><ITTI field_name='phone'></ITTI></td>

<td><label for="address">Address</label></td>
<td colspan="3"><ITTI field_name='address' style="width:100%"></ITTI></td>
</tr>
<tr>
<td><label for="subject">Subject</label></td>
<td><ITTI field_name='subject' style="width:90%"></ITTI></td>
*/?>
<td><label for="is_visible">Видим</label></td>
<td colspan="3"><ITTI field_name='is_visible'></ITTI> </td>
</tr>
<tr>
<td colspan="4" align="left"><label for="comment">Коментар</label></td>
</tr>
<tr>
<td colspan="4" align="left"><ITTI field_name='comment' style="width:100%;height:100px;"></ITTI></td>
</tr>
	
	<tr>
		<td colspan="4" align="center" style="padding-right:10px;">
							<input type="submit" class="submit" name="btSave" value="Запис" />&nbsp;&nbsp;&nbsp;<input type="button" class="submit" onclick="self.location='<?=($_GET['bkp']);?>'" value="Назад" />
	</td></tr>
</tbody></table>
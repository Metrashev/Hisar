<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8"><table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1" class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">User Group Rights</td>
		<td width="1" class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_r.png" /></td>
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
<col width='15%'>
<col width='35%' align='left'>
<col width='15%' align='right'>
<col width='35%' align='left'>

</colgroup>
<tbody><tr>
<td align='right'><label for="name">User group</label></td>
<td colspan="3"><ITTI field_name='name'></ITTI></td>
</tr>
<tr>
<td colspan="2"><label for="resources">Resources</label></td>
<td colspan="2"><label for="cids">Cids</label></td>
</tr>
<tr>
<td colspan="2"><ITTI field_name='resources' style="width:100%;height:300px;"></ITTI></td>
<td colspan="2"><ITTI field_name='cids' style="width:100%;height:300px;"></ITTI></td>
</tr>
		
	<tr>
		<td colspan="4" align="center" style="padding-right:10px;">
							<input type="submit" class="submit" name="btSave" value="Save" />&nbsp;&nbsp;&nbsp;<input type="button" class="submit" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" />
	</td></tr>
</tbody></table>
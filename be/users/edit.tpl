<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8"><table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1"  class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Users</td>
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
<col width='25%' align='right'>
<col width='25%' align='left'>
<col width='25%' align='right'>
<col width='25%*' align='left'>
</colgroup>
<tbody><tr>
<td><label for="name">Name</label></td>
<td><ITTI field_name='name'></ITTI></td>
<td><label for="email">email</label></td>
<td><ITTI field_name='email'></ITTI></td>
</tr>
<tr>
<td><label for="username">User name</label></td>
<td><ITTI field_name='username'></ITTI></td>
<td><label for="userpass">Password</label></td>
<td><ITTI field_name='userpass'></ITTI></td>
</tr>
<tr>
<td></td>
<td><ITTI field_name='is_active'></ITTI> <label for="is_active">Active</label></td>
<td><label onclick="document.getElementById('user_rights_id').focus();">Status</label></td>
<td><ITTI field_name='user_rights_id'></ITTI></td>
</tr>		
	<tr>
		<td colspan="4" align="center" style="padding-right:10px;">
							<input type="submit" class="submit" name="btSave" value="Save" />&nbsp;&nbsp;&nbsp;<input type="button" class="submit" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" />
	</td></tr>
</tbody></table>
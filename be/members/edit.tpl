<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8"><table class="viewHeader" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1" class="viewHeaderLeft"><img src="<?=BE_IMG_DIR;?>design/header_l.png" /></td>
		<td width="100%" class="viewHeaderTitle">Members</td>
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
<colgroup span="2" width="0*">
<col width='50%' align='right'>
<col width='50%*' align='left'>
</colgroup>
<tbody><tr>
<td><label for="first_name">First name</label></td>
<td><ITTI field_name='first_name'></ITTI></td>
</tr>
<tr>
<td><label for="mid_name">Middle name</label></td>
<td><ITTI field_name='mid_name'></ITTI></td>
</tr>
<tr>
<td><label for="last_name">Last name</label></td>
<td><ITTI field_name='last_name'></ITTI></td>
</tr>
<tr>
<td><label for="city">City</label></td>
<td><ITTI field_name='city'></ITTI></td>
</tr>
<tr>
<td><label for="zip">Zip</label></td>
<td><ITTI field_name='zip'></ITTI></td>
</tr>
<tr>
<td><label for="address">Address</label></td>
<td><ITTI field_name='address'></ITTI></td>
</tr>
<tr>
<td><label for="country">Country</label></td>
<td><ITTI field_name='country'></ITTI></td>
</tr>
<tr>
<td><label for="home_phone">Home phone</label></td>
<td><ITTI field_name='home_phone'></ITTI></td>
</tr>
<tr>
<td><label for="mobile_phone">Mobile phone</label></td>
<td><ITTI field_name='mobile_phone'></ITTI></td>
</tr>
<tr>
<td><label for="work_phone">Work phone</label></td>
<td><ITTI field_name='work_phone'></ITTI></td>
</tr>
<tr>
<td><label for="email">Email</label></td>
<td><ITTI field_name='email'></ITTI></td>
</tr>
<tr>
<td><label for="username">Username</label></td>
<td><ITTI field_name='username'></ITTI></td>
</tr>
<tr>
<td><label for="userpass">Userpass</label></td>
<td><ITTI field_name='userpass'></ITTI></td>
</tr>
<tr>
<td></td>
<td><ITTI field_name='is_active'></ITTI> <label for="is_active">Is active</label></td>
</tr>
<tr>
<td></td>
<td><ITTI field_name='send_extra_info'></ITTI> <label for="send_extra_info">Send extra info</label></td>
</tr>		
	<tr>
		<td colspan="2" align="center" style="padding-right:10px;">
							<input type="submit" class="submit" name="btSave" value="Save" />&nbsp;&nbsp;&nbsp;<input type="button" class="submit" onclick="self.location='<?=($_GET['bkp']);?>'" value="Back" />
	</td></tr>
</tbody></table>
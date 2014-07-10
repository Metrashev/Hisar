<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<form method="post">
<? if(!$__is_print) { ?>
	<div class="error" required_id="in_data_first_name_" style="display:none;">Име е задължително поле</div>
	<div class="error" required_id="in_data_mid_name_" style="display:none;">Фамилия е задължително поле</div>
	<div class="error" required_id="in_data_last_name_" style="display:none;">Моля, въведете валиден E-mail</div>
	<div class="error" required_id="agree" style="display:none;">Моля, отбележете че сте се запознали с общите условия за ползване</div>
	_#EXTRA#_
<? } ?>
<br />


<table width="70%" cellpadding="5" cellspacing="0" class="formTable">
<col width="25%">
<col width="75%">
<tr>
	<td class="form_tickTitle" colspan="2">Лични данни</td>	
</tr>
<tr>
	<td><label for="in_data_first_name_">Име</label></td>
	<td><input type="text" style="width:95%" name="in_data[first_name]" required="required" /></td>	
</tr>
<tr>
	<td><label for="in_data_mid_name_">Презиме</label></td>
	<td><input type="text" style="width:95%" name="in_data[mid_name]" required="required" /></td>
</tr>
<tr>
	<td><label for="in_data_last_name_">Фамилия</label></td>
	<td><input type="text" style="width:95%" name="in_data[last_name]" required="required" /></td>
</tr>
</table>

<table width="70%" cellpadding="5" cellspacing="0" class="formTable">
<col width="25%">
<col width="75%">
<tr>
	<td class="form_tickTitle" colspan="2">Адрес за доставка</td>
</tr>
<tr>
	<td><label for="in_data_city_">Населено място</label></td>
	<td><input type="text" style="width:54%" name="in_data[city]" />&nbsp;
	<label for="in_data_zip_">п.к.</label>&nbsp;<input type="text" name="in_data[zip]" size="4" />
	</td>
</tr>
<tr>
	<td><label for="in_data_address_">Адрес</label></td>
	<td><textarea name="in_data[address]" style="width:95%;height:50px;"></textarea></td>
</tr>
<?/*
<tr>
	<td><label for="in_data_country_">Държава</label></td>
	<td><input type="text" name="in_data[country]" style="width:95%" /></td>
</tr>
*/?>
</table>

<table width="70%" cellpadding="5" cellspacing="0" class="formTable">
<col width="25%">
<col width="75%">
<tr>
	<td class="form_tickTitle" colspan="2">За контакт</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><label for="in_data_home_phone_">домашен</label></td>	
</tr>
<tr>
	<td><label for="phones">Телефон</label>
<group id="phones" name="^in_data\[(home|work|mobile)_phone\]$" required="true" required_msg="Посочете поне 1 телефон за контакт" />
	
	
	</td>
	<td><input type="text" name="in_data[home_phone]" style="width:95%"  regexp="/^[0-9]+$/" regexp_msg="Невалиден домашен телефон" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><label for="in_data_work_phone_">служебен</label></td>	
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="text" name="in_data[work_phone]" style="width:95%" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><label for="in_data_mobile_phone_">мобилен</label></td>	
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="text" name="in_data[mobile_phone]" style="width:95%" /></td>
</tr>
<tr>
	<td><label for="in_data_email_">Email</label></td>
	<td><input type="text" name="in_data[email]" required="required" style="width:95%" /></td>
</tr>
</table>

<table width="70%" cellpadding="5" cellspacing="0" class="formTable">
<col width="40%">
<col width="60%">
<tr>
	<td class="form_tickTitle" colspan="2">Потребителски данни</td>
</tr>
<tr>
	<td><img src="/i/buttons/user.png" align="middle" />&nbsp;<label for="in_data_username_" style="color:#000">Потребител</label></td>
	<td><input type="text" name="in_data[username]" required="required" style="width:95%" /></td>
</tr>
<tr>
	<td><img src="/i/buttons/lock.png" align="middle" />&nbsp;<label for="in_data_userpass_" style="color:#000">Парола</label></td>
	<td><input type="password" name="in_data[userpass]" required="required" style="width:95%" /></td>
</tr>
<tr>
	<td><img src="/i/buttons/lock.png" align="middle" />&nbsp;<label for="in_data_userpass1_" style="color:#000">Повтори парола</label></td>
	<td><input type="password" name="userpass1" required="required" style="width:95%" /></td>
</tr>
</table>

<table class="formTable" cellpadding="5" cellspacing="0" width="80%">
<tr>
	<td colspan="2" align="center"><input style="border:none;" type="checkbox" name="in_data[send_extra_info]" /> <label style="font-family:arial">Желая да получавам допълнителна информация за списание "Ум и Душа"</label></td>
</tr>
<tr>
	<td colspan="2" align="center"><label style="font-family:arial">Декларирам, че съм запознат с</label> <a style="color:#000;font-size:11px;font-family:arial" href="/?cid=48" target="_blank">Общите условия</a> <label  style="font-family:arial">за ползване</label></td>
</tr>
<tr>
	<td colspan="2" align="center"><input style="border:none;" type="checkbox" required="required" name="agree" />&nbsp;<label for="agree" style="color:#000">Съгласен съм</label></td>
</tr>

<? if(!$__is_print) { ?>
<tr>
<td colspan="2" align="center"><input type="submit" class="reg_button" value="регистрация" name="btSave" /></td>
</tr>
<? } ?>

</table>


</form>
</body>
</html>
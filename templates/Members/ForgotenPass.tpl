<table class='formTable' width='100%' cellpadding='5'>
		<col width='15%'>
		<col width='85%'>
	<tr>
		<th>E-mail:</th>
		<td><input type='text' class='input' style="width:95%" id='email' name='email' value="<?=htmlspecialchars($_POST['email']);?>" /></td>
	</tr>
	
	<tr><td colspan='2' align='center'>
		<input type='submit' class='reg_button' name='btSend' value='Изпрати' />
	</td>
	</tr>
	<tr><td colspan='2' style='font-size:10px;font-weight:bold'>
		* Паролата ще ви бъде изпратена на пощата, която посочите - ако тя съвпада с въведената при регистрацията.
	</td>
	</tr>
</table>
<? if(CMembers::isLoged()) { ?>
<form method="post">
	<input type="hidden" name="logout" value="" id="logout" />
	<h1>Wellcome, <?=CMembers::getMemberName();?></h1>&nbsp;[<a onclick="document.getElementById('logout').value='1';getForm(this).submit()" href="#">logout</a>]
</form>
<? } else { ?>
<? if(isset($_POST['btLogin'])) { ?>
	<div class="error">Invalid User ID/User password</div>
<? } ?>
<form method="post">
	<table cellpadding="5" cellspacing="0">
	<tr>
		<td>User ID:</td>
	 	<td><input type="text" name="username" /></td>
	 </tr>
	 <tr>
		<td>User Password:</td>
		<td><input type="password" name="userpass" /></td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="submit" name="btLogin" value="Login" />
		</td>
	</tr>
	</table>
</form>
<? } ?>
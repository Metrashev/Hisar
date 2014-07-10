<html>
	<table id="dg_mq_mail_bulletins" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
<td><a>Id</a></td>
<td><a>Subject</a></td>
<td><a>From email</a></td>
<td><a>Текст</a></td>
<td><a>Дата за изпращане</a></td>
<td><a>Is sent</a></td>
<td><a>Списък с e-mail-и</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td field_name="id"   ></td>
			<td field_name="subject"   ></td>
			<td field_name="from_email"   ></td>
			<td field_name="body"   ></td>
			<td field_name="date_to_send"   format="%d/%m/%Y %H:%i"></td>
			<td field_name="is_sent"   ></td>
			<td field_name="mail_group_id"  sql="select name from mq_mail_groups where id='_#VAL#_'" ></td>
</tr>
	</tbody>
	</table>
</html>
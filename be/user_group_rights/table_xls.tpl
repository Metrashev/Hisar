<html>
	<table id="dg_user_group_rights" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
<td><a>Id</a></td>
<td><a>User group</a></td>
<td><a>Resources</a></td>
<td><a>Cids</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td field_name="id"   ></td>
			<td field_name="user_group_id"  sql="select name from user_groups where id='_#VAL#_'" ></td>
			<td field_name="resources" arrayname="resources_array"  ></td>
			<td field_name="cids"  sql="select value from categories where id='_#VAL#_' order by l" ></td>
</tr>
	</tbody>
	</table>
</html>
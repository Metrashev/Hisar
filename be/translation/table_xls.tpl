<html>
	<table id="dg_translation" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
<td><a>Id</a></td>
<td><a>Cid</a></td>
<td><a>Lng</a></td>
<td><a>Translation key</a></td>
<td><a>Value</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td field_name="id"   ></td>
			<td field_name="cid"  sql="select  concat(repeat('&nbsp;', (level-1)*2),value) as value FROM categories LEFT JOIN categories_bg ON categories.id=categories_bg.lng_master_id  where id='_#VAL#_' ORDER BY l" ></td>
			<td field_name="lng" arrayname="CONFIG[SiteLanguages"  ></td>
			<td field_name="translation_key"   ></td>
			<td field_name="value"   ></td>
</tr>
	</tbody>
	</table>
</html>
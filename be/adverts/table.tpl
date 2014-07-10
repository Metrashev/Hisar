<html>
	<table id="dg_adverts" class="test1 list_table" cellspacing="0" cellpadding="0" border="0">
	<thead>
	<tr class="list_header">
			<td in_index="1" id='_id' field_name="id" class="header_add"><a href='<?=BE_DIR;?>adverts/edit.php?<?=FE_Utils::getBackLink();?>'>New</a></td>
<td in_index="2"  id='_advertiser' field_name="advertiser" class="header_nor"><a order="advertiser">Advertiser</a></td>
<td in_index="3"  id='_active_from_date' field_name="active_from_date"  class="header_nor"><a order="active_from_date">from</a></td>
<td in_index="4"  id='_active_to_date' field_name="active_to_date"  class="header_nor"><a order="active_to_date">to</a></td>
<td in_index="5"  id='_position_id' field_name="position_id"  class="header_nor"><a order="position_id">possition</a></td>
<td in_index="6"  id='_ad_type_id' field_name="ad_type_id"  class="header_nor"><a order="ad_type_id">type</a></td>



<td in_index="11"  id='_num_views' field_name="num_views"  class="header_nor"><a order="num_views">views</a></td>
<td in_index="12"  id='_num_clicks' field_name="num_clicks"  class="header_nor"><a order="num_clicks">clicks</a></td>
<td in_index="13" id='t13' field_name="id"  class="header_nor"><a>Delete</a></td>
</tr>
	</thead>
	<tbody>
	<tr>
			<td>
<? if(!isset($_GET['search'])) { ?>
			<a field_name="id" href='<?=BE_DIR;?>adverts/edit.php?id=_#VAL#_&amp;<?=FE_Utils::getBackLink();?>'>Edit</a>
		<?} else { ?>
			<input type="checkbox" name="_#CONTROL#_[fields][_ch_sel_][_#UNIQUE#_]" value="1" />
		<? } ?>
		</td>
			<td><ITTI field_name="advertiser"   ></ITTI></td>
			<td><ITTI field_name="active_from_date"   format="%d/%m/%Y"></ITTI></td>
			<td><ITTI field_name="active_to_date"   format="%d/%m/%Y"></ITTI></td>
			<td><ITTI field_name="position_id" arrayname="ADVERT_POSITIONS"  ></ITTI></td>
			<td><ITTI field_name="ad_type_id" arrayname="AdsTypes"  ></ITTI></td>


			<td align="right"><ITTI field_name="num_views"   ></ITTI></td>
			<td align="right"><ITTI field_name="num_clicks"   ></ITTI></td>
<td><a field_name="id" href='#' onclick='if(window.confirm("Are you shure?")) {document.getElementById("hdDelete").value="_#VAL#_";getParentFormElement(this).submit();} else return false;'>Delte</a></td>
</tr>
	</tbody>
	</table>
</html>
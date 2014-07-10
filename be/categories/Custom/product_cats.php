<hr />
<div>Категория продукти: <select name="data[product_category_id]"><?php
$db=getdb();
$pr=array(0=>'')+$db->getAssoc("select id,concat(repeat('&nbsp;',level),value) as value from product_categories order by l");
echo CLib::draw_listbox_options($pr,$data['product_category_id']);
?></select>
</div>
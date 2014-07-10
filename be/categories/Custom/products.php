<hr />
Product Groupr: <select name="data[attribute_cluster_id]"><?php
$vals = array();
echo CLib::draw_listbox_options(getdb()->GetAssoc("SELECT id, name FROM attribute_clusters  ORDER BY name"),$data['attribute_cluster_id']);
?></select>
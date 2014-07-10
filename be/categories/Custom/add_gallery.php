<hr />
<label for="has_gallery">Allow Gallery</label> 
<input value="1" id="has_gallery" type="checkbox" name="data[has_gallery]" <?=isset($data['has_gallery'])?"checked":"";?> />

Render: <select name="data[gallery_render_id]"><?php
$vals = array();
foreach ($GLOBALS['CONFIG']['CFEGallery']['templates'] as $k=>$v) $vals[$k]=$v['name'];
echo CLib::draw_listbox_options($vals,$data['gallery_render_id']);
?></select>
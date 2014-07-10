<?php
require_once(dirname(__FILE__).'/template_edit.php');
$template_edit=isset($__template_edit)?$__template_edit:new EditTemplate($id);
/* @var $template_index EditTemplate*/

$template_edit->removeScript('Calendar');

//$template_edit->pref_table_text=FE_Utils::renderErrors($errors);
echo $template_edit->openTemplate();	//izpisva <html><head><meta><scripts><css></head><body><form><hidden><table><tr><td>


$p=new Page();
if (!empty($errors)) {
	echo FE_Utils::renderErrors($errors);
	echo "<br />";
}

MasterForm::create($con,$_POST,$p,$array);
echo "<!--   START    -->";
echo $hhh= $p->render();
echo "<!--   END    -->";
echo $template_edit->closeTemplate();
?>
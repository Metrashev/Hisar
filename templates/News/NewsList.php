<!-- <h1 style="padding-bottom:10px;"><?//=$GLOBALS['fc']->node['value'];?></h1> -->
<div class="newsList">
<?php

foreach ($data['data_list'] as $news){

	include(dirname(__FILE__)."/BriefNewsBlock.php");

}


include(dirname(__FILE__)."/../Core/PageBar.php");
?>
</div>
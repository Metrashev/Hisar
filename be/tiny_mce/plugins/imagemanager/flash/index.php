<script language="JavaScript" type="text/javascript">
	function callBackSubmit(){reloadPage();}
	function callBackCancel(){reloadPage();}
	function reloadPage() {
		var f = document.getElementById("frm_2");
		if (f!=undefined) {
			f.submit();
		} else {
			window.location.refresh(true);
		}
		return true;
	}
</script>
<?php

$params = array(
	'displaySizes' => '783,783', // size1Width,size1Height,size2Width,size2Height.....
	'outputPath'   => 'flash/export.php?path='.urlencode($fm->file_manager->currentDir),
	'overrideUrl'  => 'flash/has_file.php?path='.urlencode($fm->file_manager->currentDir),
	'nameField'    => 'fileName',
// за задаване на задължително разширение
//'outputExt' => '',
// call back функции при успешен submit или cancel. by default:callBackSubmit, callBackCancel
//'callBackSubmit' => 'hideFlash',
//'callBackCancel' => 'doSomething',
);

$flash = 'flash/Index.swf?' . http_build_query($params);

?>
<div style="width:550px;height:400px;">

<object width="100%" height="100%" id="preloader">
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="allowFullScreen" value="false" />
	<param name="movie" value="<?=$flash;?>" />
	<param name="quality" value="high" />
	<param name="bgcolor" value="#ffffff" />
	<embed src="<?=$flash;?>" quality="high" bgcolor="#ffffff" width="100%" height="100%" name="preloader" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>

</div>
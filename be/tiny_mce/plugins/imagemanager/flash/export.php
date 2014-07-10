<?php
require_once('../conf.php');

if (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
	$img = $GLOBALS["HTTP_RAW_POST_DATA"];
	$fileName = $_GET['fileName'];

	if ($_GET['path']) {
		$root_path = realpath($GLOBALS['FMAN_IMAGES_ABS_PATH']);
		$path = realpath($GLOBALS['FMAN_IMAGES_ABS_PATH'].$_GET['path']);

		if (strpos($fileName, '/')===false && strpos($fileName, '\\')===false && strpos($path, $root_path)===0) {
			file_put_contents($path.'/'.$fileName, $img);
		}
	}
}


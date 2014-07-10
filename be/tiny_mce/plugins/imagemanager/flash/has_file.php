<?php
require_once('../conf.php');

echo file_exists($GLOBALS['FMAN_IMAGES_ABS_PATH'] . $_GET['path'] . $_GET['fileName']);

<?php
require_once('../config/config.php');
if(USE_OWN_USERS){
	header("Location: login.php");
} else {
	header("Location: indexMenu.php");
}
?>
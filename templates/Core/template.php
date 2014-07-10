<?php
$translation = array(
	'bg'=>array(
		'back'=>'Назад',
		'print'=>'Печат',
		'top'=>'Нагоре',
	),
	'en'=>array(
		'back'=>'Back',
		'print'=>'Print',
		'top'=>'Top',
	),
);
$translation = $translation[LNG_CURRENT];
$customCids = $GLOBALS['customCids'][LNG_CURRENT];
$searchCidCurrent = array(
	LNG_BG => 22,
	LNG_EN => 23
);

$printA = $data['HidePrintLink'] ? '' : "| <a href='{$data['PrintLinkHref']}' class='Print' target='_blank' onClick='this.href = getPrintLink();'>{$translation['print']}</a>";
// $backPrintTop = <<<EOD
// <div class="BackPrintTop">
// <a href="{$data['BackLinkHref']}" class="Back">{$translation['back']}</a> | <a href="#" class="Top">{$translation['top']}</a> {$printA}
// </div>
// EOD;

?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LNG_CURRENT?>" lang="<?=LNG_CURRENT?>">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<title><?php echo $data['PageTitle'];?></title>
	<?php echo  $data['Header'] ?>
	<link rel="stylesheet" type="text/css"  href="<?=CSS_DIR;?>lib.css" />
	<script language="JavaScript" type="text/javascript" src="<?=JS_DIR;?>swfobject.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?=JS_DIR;?>lib.js"></script>
</head>

<body>

<div id="headerEu">
	<div id="logoHeader">
		<table>
			<tr>
				<td><img src="i/LogoEU.png" id="logoEU" alt="Лого ЕС"/></td>
				<td><img src="i/BG_logo.png" id="logoBG" alt="Лого БГ" /></td>
				<td><img src="i/OPRR_logo.png"  id="logoOPRR" alt="Лого ОПРР"/></td>
			</tr>
		</table>
	</div>
</div>
<div id="wrap">
	<div id="header" >
		<a href="<?=LNG_CURRENT==LNG_BG?'/':'/?cid=9'?>" title="{#Община Хисаря#}"><img class="logo" src="i/logo.png" alt="{#Община Хисаря#}" /></a>
		<div class="headerTitle">{#slogan#}</div>
			<div class="searchForm">
				<form method="get" action="/">
					<input type="hidden" name="cid" value=<?=$searchCidCurrent[LNG_CURRENT]?> />
					<input id="searchText" type="text" name="q" /><input id="searchButton" type="submit" value=""/>
				</form>
			</div>
		<div class="topMenu">
		<?php include(dirname(__FILE__).'/../Nav/TopNav.php'); ?>
		</div>
	</div>
	
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td id="content">
<?php 

// var_dump($GLOBALS['fc']->nodesPath[1]['id']==5);
	//echo '<pre>' .print_r(, true). '</pre>';
	include(dirname(__FILE__).'/../Nav/SubNav.php');

?>
				<?=$data['body']?>
				<?=$backPrintTop?>
			</td>
			<td id="ctx">
				<?php include(dirname(__FILE__).'/../Nav/ctx.php'); ?>
			</td>
		</tr>
	</table>
	
	
	
	<div id="footer">
		<?php include(dirname(__FILE__).'/Footer.php'); ?>
	</div>

</div>
<?=$data['TrackerCode']?>
</body>
</html>
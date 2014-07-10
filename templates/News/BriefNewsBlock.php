<?
$translation = array(
	'bg'=>array(
		'read_more'=>'Към статията',
	),
	'en'=>array(
		'read_more'=>'Read More',
	),
);
$translation = $translation[LNG_CURRENT];

$day = date('d',$news['due_date']['row']);
$month = LNG_CURRENT==LNG_BG ? mb_strtoupper($GLOBALS['calendar_month_short'][(int)date('m',$news['due_date']['row'])]) : mb_strtoupper(date('M',$news['due_date']['row']));
$img = "";
if($news['picture']['pic']) {	
 	$img = <<<EOD
 		<a href="{$news['href']}"><img src="{$news['picture']['pic']}" alt="{$news['title']}" style="float:left;padding:7px 8px 0 0;" /></a>
EOD;
}
else {
	$img = <<<EOD
 		<a href="{$news['href']}"><img src="i/defaut_news_pic.png" alt="{$news['title']}" style="float:left;padding:7px 8px 0 0;" /></a>
EOD;
}
 
echo <<<EOD
	<article>
		<div class="picBgr">{$img}
			<div class="dateBgr">
				<div class="day">{$day}</div>
				<div class="month">{$month}</div>
			</div>
		</div>
		<h2 style="padding:5px 0 0 0;"><a href="{$news['href']}">{$news['title']}</a></h2>
		<!-- <span class="sub-info">{$news['due_date']['date']}</span> -->
		<div><p>{$news['subtitle']}</p></div>
		<a class="more-read" href="{$news['href']}">{$translation['read_more']}</a>
	</article>
EOD;
?>
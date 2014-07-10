<script language="JavaScript" type="text/javascript" src="<?=JS_DIR;?>jquery-1.11.1.min.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/jquery-1.11.1.min.js"></script>

<script type="text/javascript">
var flag = false;
var clearId;
var isAnimationStarted = false;

function changePic(obj){
	
	if(isAnimationStarted) return;
	
	isAnimationStarted = true;
	
	clearTimeout(clearId);
	
	if(obj==null){
		var currTd = $('a.dot.selected');
		var currId = $(currTd).attr('id').replace('link_',''); //id1
		var nextTd = $(currTd).next().length==0 ? $('a.dot').first() : $(currTd).next();	
		var nextId = $(nextTd).attr('id').replace('link_',''); //id2
	} else {
		if($(obj).hasClass('selected')) { return; }
		var currTd = $('a.dot.selected');
		var currId = $(currTd).attr('id').replace('link_',''); //id1
		var nextTd = $(obj);
		var nextId = $(nextTd).attr('id').replace('link_',''); //id2
	}
	
	$('img#'+currId).fadeOut(500, function(){
			$(currTd).removeClass('selected');
			$(nextTd).addClass('selected');
			$('img#'+nextId).fadeIn(500, function() { isAnimationStarted=false; });
	});
	
	if(flag){
		return;
	}
	
	clearId = setTimeout(function(){changePic(null)}, 5000);
}

$(document).ready(function(){
	
	$('a.dot').click(function(){
		flag = true;
		changePic(this);
	});
	
	clearId = setTimeout(function(){
		changePic(null);
	}, 3000);

	
});
</script>
<div class="galleryWrap">
	<img src="/i/tmp/slade_show1.jpg" class="selected" id="id1" style="display: inline;">
	<img src="/i/tmp/slade_show2.jpg" id="id2" style="display: none;">
	<img src="/i/tmp/slade_show3.jpg" id="id3" style="display: none;">
	<img src="/i/tmp/slade_show4.jpg" id="id4" style="display: none:">
	<div style="position:absolute; right:10px; bottom:10px; width: 120px; height: 25px">
	
		<a class="dot selected" id="link_id1"></a> 			
		<a class="dot" id="link_id2"></a> 
		<a class="dot" id="link_id3"></a>
		<a class="dot" id="link_id4"></a>

	</div>
</div>	
</html>


<?php

$sp_data = getdb()->getRow('SELECT * FROM static_pages WHERE cid=? AND def=1', array($data['node']['id']));

if(is_array($sp_data)){
	$data = $sp_data + $data;
}

include 'StaticPage.php';
?>


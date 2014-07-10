var duration = 800;
var curr = 0;
var thumbsWidth;
var percent;
var pixels;


var pictureContainer;

/*
	thubms width = thumbs container width - actual thumbs width;
	
	on drag:
		percent = scroll left position  / ( scroll area width - scroll bar width ) 
		thumbs left possition = - percent * thumbs width
			
*/	

$(document).ready(function(){
	
	pictureContainer = $("#gallery1234 .picture-container")[0];
	
	//thumbsWidth = $("#gallery1234 .thumbs-container")[0].scrollWidth - $("#gallery1234 .thumbs").width();
	thumbsWidth = $("#gallery-thumbs-bottom").position().left - $("#gallery1234 .thumbs-container").width();
		
	$("#gallery1234 .scroll-handler").draggable({
		containment: '#gallery1234 .scroll-bar',
		drag: function() {
			percent = $(this).position().left / ( $("#gallery1234 .scroll-bar").width() - $(this).width() );
			pixels = - percent * thumbsWidth;
			pixels = pixels + 'px';
			$("#gallery1234 .thumbs").css({'left':pixels});
		}
	});
	
	
	
	//ShowImage(0);
	showImgByHash();
	
});


//picCount

function NextImg(){
	curr++;
	if(curr>=big.length ) curr=0;
	ShowImage(curr);
}

function PrevImg(){
	curr--;
	if(curr<0) curr=big.length -1;
	ShowImage(curr);
}


function ShowImage(n)
{
	$("#gallery1234 .thumbSelected").fadeTo("slow",1);
	$("#gallery1234 .thumbSelected").removeClass("thumbSelected");
	
	
	$("#gallery1234 .picture-container img").fadeOut(duration);
	createImage(big[n]);
	curr = n;
	
	var tx = $("#thumb"+n).position().left;
	var te = $("#thumb"+n).position().left + $("#thumb"+n).width();
	var cx = $("#gallery1234 .thumbs").position().left;
	var ce = $("#gallery1234 .thumbs-container").width();
	 
	 var myPos = 0;
	 var b = false;
	 
	if ( -cx > tx )
	{
		b = true;
		myPos = -tx;
		$("#gallery1234 .thumbs").animate({
			left : "-"+tx+"px"
		},{
			duration: "slow"
		});
	} else if( ( ce - cx ) < te ) {
		b = true;
		var pos = ce - te -8;
		myPos = pos;
		$("#gallery1234 .thumbs").animate({
			left : pos+"px"
			
		},{
			duration: "slow"
		});
	}
	
	if (b) 
	{
		
		var q = myPos / thumbsWidth;
		var xPos =  q * ( $("#gallery1234 .scroll-handler").width() -  $("#gallery1234 .scroll-bar").width());
		$("#gallery1234 .scroll-handler").animate({
				left : xPos+"px"				
			},{
				duration: "slow"
			});	
	}
	
	
	var imgs = pictureContainer.getElementsByTagName("img");
	if (imgs.length > 3) {
		pictureContainer.removeChild(imgs[0]);
	}

	$("#thumb"+curr).fadeTo("slow",0.5);
	$("#thumb"+curr).addClass("thumbSelected");
}


function createImage(src) {
		
	var img = document.createElement("img"); 
	img.setAttribute("src",src);
	$(img).load(function(){
		
		var w = ( $(pictureContainer).width() - $(this).width() ) / 2;
		var h = ( $(pictureContainer).height() - $(this).height() ) / 2;
	
		w += 'px';
		h += 'px';
	
		$(this).css({'left' : w , 'top' : h});		
	});
	pictureContainer.appendChild(img);
	

		var w = ( $(pictureContainer).width() - $(img).width() ) / 2;
		var h = ( $(pictureContainer).height() - $(img).height() ) / 2;
		
		if ($(img).width() == 0)
		{
			w = 40;
			h = 40;
		}
		
	
		w += 'px';
		h += 'px';
	
		$(img).css({'left' : w , 'top' : h});			
	$(img).fadeIn(duration);
}


function showImgByHash(){
	MyRe = /#img([0-9]+)/
	var b = MyRe.exec(window.location.hash);
	
	if(b && b[1]){
		ShowImage(b[1]);
	} else {
		ShowImage(curr);
	}
}
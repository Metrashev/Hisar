function getWidth() {
	return screen.availWidth>800?screen.availWidth-50:screen.availWidth;
}

function getHeight() {
	return screen.availHeight;
}

function switchMenu(obj) {
	obj=document.getElementById(obj);
	if(obj) {
		if(obj.style.display=='none')
			obj.style.display='block';
		else 
			obj.style.display='none';
	}
	
}

function setMenuStyleOver(obj) {
	if(obj.firstChild.className=='normalLI')
		obj.firstChild.className='overLI';
	else
		obj.firstChild.className='overSelectedLI';	
}

function setMenuStyleOut(obj) {
	
	if(obj.firstChild.className=='overLI')
		obj.firstChild.className='normalLI';
	else
		obj.firstChild.className='downLI';	
}

function setMenuStyleClick(obj) {
	var arr=document.getElementsByTagName('LI');
	for(i=0;i<arr.length;i++) {
		arr[i].className='normalLI';
	}
	obj.firstChild.className='downLI';
	try {
	var t=obj.childNodes(0).childNodes(0);
	t.className='selectede';
	}catch(e){}
	
}

function isNum(checkFloat) {
	if(event.keyCode>=48&&event.keyCode<=57)
		return true;
	var val=event.srcElement.value;
	if(event.keyCode==45) {
		var val=event.srcElement.value;
		if(val.length>0)
			return false;
		return true;
			
	}
	if(checkFloat==true) {
		if(event.keyCode=='46'||event.keyCode=='44') {
			if(val.indexOf(',')>=0||val.indexOf('.')>=0)
				return false;
			return true;
		}
		return false;
	}
	return false;
	
}

/*************GALERY*****************/

var tout=null;
var shCurr_img=0;
function startSlide(img,speed,curr_img,str_img,dir) {
	var images=str_img.split(",");
	if(curr_img>=images.length)
		return;
	document.getElementById(img).src=dir+"/pic"+images[curr_img];

	var ni=curr_img+1;
	var sp=document.getElementById(speed);
	
	var spd=sp.options[sp.selectedIndex].value;
	tout=window.setTimeout('startSlide("'+img+'","'+speed+'",'+ni+',"'+images+'","'+dir+'")',spd);
	document.getElementById('lb_pic').innerText=(curr_img+1)+'/'+(images.length);
	shCurr_img=curr_img;
	setPicLinks(images.length-1);
}

function setPicLinks(len) {
	
	if(shCurr_img==len) {
		document.getElementById('a_np').style.display='none';
		document.getElementById('l_a_np').style.display='inline';
	}
	else {
		document.getElementById('l_a_np').style.display='none';
		document.getElementById('a_np').style.display='inline';
	}
	if(shCurr_img==0) {
		document.getElementById('a_pp').style.display='none';
		document.getElementById('l_a_pp').style.display='inline';
	}
	else {
		document.getElementById('a_pp').style.display='inline';
		document.getElementById('l_a_pp').style.display='none';
	}
}

function showPhoto(dir,img,str_img,folder) {
	
	cTimeOut();
	shCurr_img+=dir;
	var images=str_img.split(",");
	
	if(shCurr_img<0||shCurr_img>=images.length) {
		shCurr_img-=dir;
		setPicLinks(images.length-1);
		return;
	}
	setPicLinks(images.length-1);
	document.getElementById(img).src=folder+"/pic"+images[shCurr_img];
	
}

function cTimeOut() {
	if(tout!=null) {
		window.clearTimeout(tout);
		tout=null;
	}	
}

function getSel(doc) {
	var txt='';
	
	if (doc.getSelection)
	{
	//	alert(.rangeCount);
		txt = doc.defaultView.getSelection();
	}
	else if (doc.selection)
	{
		txt = doc.selection.createRange();
	}
	else return;
	return txt;
}


function expandLinkSelection(doc){
	
	var oSel=null;
	try {
  	//	oSel =  doc.selection.createRange();
  		oSel=getSel(doc);
	}catch(e) {
		alert("Cannot expand selection "+e.description);
	}
	if(oSel==null)
		return;
  /*
    The selection is a control
    Maybe should make checks if this control make sense for linkig like IMG but not BUTTON
  */
  if(doc.selection) {
	  if(doc.selection.type=="Control"){
	    return getParentElementByTagName(oSel(0),'A');
	  }
  }
  else {
  	alert(getParentElementByTagName(oSel.anchorNode,'A'));
  	return getParentElementByTagName(oSel.anchorNode,'A');
  }


  var parent=null;  
  if(doc.selection) {
  	if(oSel.item!=undefined&&oSel.item(0)&&oSel.item(0).tagName.toLowerCase()=="img") {
  		oSel=oSel.item(0);
  	}
  	parent=oSel.parentElement();
  }
  else {
  	parent=oSel.anchorNode;
  	
  }
  /*
    The selection is within a link 
  */
  
  var oEl = getParentElementByTagName(parent,'A');
  if(oEl){
    oSel.moveToElementText(oEl);
	  oSel.select();	      
	  return oEl;
  }
  
	var oEl = parent;
	if(oEl){
	  
    oColl = oEl.getElementsByTagName('A');
    if(oColl.length==0)
      return null;
      
    oSelStart = oSel.duplicate();
    oSelStart.collapse();
    
    oSelEnd = oSel.duplicate();
    oSelEnd.collapse(false);
    
    oSelA = oSel.duplicate();
      
    for(var i = 0; i < oColl.length; i++){
      oSelA.moveToElementText(oColl(i));
      
      if(oSelA.inRange(oSelStart)){
        oSel.setEndPoint('StartToStart', oSelA);
        oSel.select();
        return oColl(i);
      }
      
      if(oSel.inRange(oSelA)){
        return oColl(i);
      }
      
      if(oSelA.inRange(oSelEnd)){
        oSel.setEndPoint('EndToEnd', oSelA);
        oSel.select();
        return oColl(i);
      }
      
    }
  }
  
  return null;
}

function createLink(doc, url, text, target,style){
	
  var oSel=null;
  try {
  	//oSel =  doc.selection.createRange();
  	oSel=getSel(doc);
  }
  catch(e) {
  	alert("Cannot expand selection! "+e.description);
  }
  if(oSel==null)
		return;
  if(!text)
  	text=url;
  
  	 if(text=='')
	      text = url;
  	

  var parent=null;
  if(doc.selection) {
  	parent=oSel.parentElement();
  }
  else {
  	parent=oSel.anchorNode;
  }
  
  if(doc.selection) {
	  if(doc.selection.type == "None"){
	    oSel.text = text;
	    oSel.moveStart('character', -text.length);
	  } else {
	    oSel.execCommand('Unlink');
	  }
  }
  else {
  	
  	//alert(parent);
  	oSel=oSel.getRangeAt(0);
  	doc.execCommand('createlink',false,url);
  	if(parent.tagName=='A')
  		parent.execCommand('Unlink');
  	else {
  		
  		oSel.text = text;
	   // rng.moveStart('character', -text.length);
  	}
  }	
  
  
  
 /* if(doc.selection.type == "None"){
    if(text=='')
      text = url;
    oSel.text = text;
    oSel.moveStart('character', -text.length);
  } else {
    oSel.execCommand('Unlink');
  }*/
  
 	if(oSel.execCommand) {
  		oSel.execCommand('CreateLink', false, url);
 	}
 	else {
 		doc.execCommand('CreateLink', false, url);
 	}
  
  
  
  if(target!=''){
    var a = parent;
    if(a.tagName == "A"){
      if(style!=null)
      	a.className=style;
      a.target = target;
    }
  }
}

function getParentElementByTagName(oEl,sTag)
{
  while (oEl!=null && oEl.tagName!=sTag) {
    oEl = oEl.parentElement
  }
  return oEl
}
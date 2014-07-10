function getParentFormElement(elem) {
  form_obj = elem;
  while (form_obj.tagName!='FORM') {
    form_obj = form_obj.parentNode;
    if (!form_obj) {
      alert('Form not found! Please put the list control in a form!'); return 0;
    }
  }
  return form_obj;
}

function getForm(elem) {
	return getParentFormElement(elem);
}

function ADivButton(d){
  var col = d.getElementsByTagName('A');
  if(col.length>0){
    col[0].click();
  }
}


function fixIELabel(){
	var oLabs = document.getElementsByTagName('label');
	for(var i=0; i<oLabs.length; i++){
		var oLab = oLabs[i];
		
		if(!oLab.htmlFor || oLab.onclick) continue;
		var oSel = document.getElementById(oLab.htmlFor);
		if(!oSel) continue;
		if(oSel.tagName=='SELECT'){
			oLab.htmlFor = null;
			oLab.onclick = function(){
				oSel.focus();
			}
		}
		
	}

}


/* COOKIES   */

function SetCookie(sName, sValue)
{
  date = new Date();
  document.cookie = sName + "=" + escape(sValue) + "; expires=Fri, 31 Dec 2020 23:59:59 GMT;";
}

function DelCookie(sName)
{
  var sValue='block';
  document.cookie = sName + "=" + escape(sValue) + "; expires=Fri, 31 Dec 1999 23:59:59 GMT;";
}

function GetCookie(sName)
{
  var aCookie = document.cookie.split("; ");
  for (var i=0; i < aCookie.length; i++)
  {
    var aCrumb = aCookie[i].split("=");
    if (sName == aCrumb[0]) 
      return unescape(aCrumb[1]);
  }
  return null;
}

/* END COOKIES  */

function getBoundingBox(el) {
	var box = {top:0, left:0, right:0, bottom:0, height:0, width:0}

	if (document.getBoxObjectFor) {
		var r = document.getBoxObjectFor(el);
		box = {top:r.y - document.body.scrollTop, left:r.x - document.body.scrollLeft, right:r.x - document.body.scrollLeft + r.width, bottom:r.y - document.body.scrollTop + r.height, height:r.height, width:r.width};
	} else if (el.getBoundingClientRect) {
		var r = el.getBoundingClientRect();
		box = {top:r.top, left:r.left, right:r.right, bottom:r.bottom, height:r.bottom-r.top, width:r.right-r.left};
	}

	return box;
}

function getPrintLink(){
	var url = window.location.protocol +'//'+ window.location.hostname +  window.location.pathname;
	
	if(window.location.search){
		url += window.location.search + '&print=on';
	} else {
		url += '?print=on';
	}
	url += window.location.hash;
	return url;
}

/* autocomplete util functions */

function getMultiSelectValues(id) {
	var d=document.getElementById(id);
	var s=new Array();	
	
	if(d&&d!=undefined) {
		for(var i=0;i<d.options.length;i++) {
			
			if(d.options[i].selected) {
				if(d.options[i].hasAttribute("value")) {
					s.push(d.options[i].value);
				}
				else {
					s.push(d.options[i].text);
				}
			}
		}
	}
	return s.join(",");
}

function getCheckBoxValue(id) {
	var d=document.getElementById(id);	
	if(d&&d!=undefined) {
		return d.checked?1:0;
	}
	return 0;
}

function getAutocompleteValues(id1,id2) {
	id1=id1.split(',');
	var s=new Array();
	for(var i=0;i<id1.length;i++) {
		var t=document.getElementById(id1[i]);
		var v=$(t).val();
		s.push("'"+id1[i]+"'=>'"+v+"'");
	}
	
	id2=id2.split(',');
	for(var i=0;i<id2.length;i++) {
		var t=document.getElementById(id2[i]);
		
		if(t.tagName.toLowerCase()=="input") {	//checkbox
			var v=getCheckBoxValue(id2[i]);
			s.push("'"+id2[i]+"'=>'"+v+"'");
		}
		else {
			var v=getMultiSelectValues(id2[i]);
			
			s.push("'"+id2[i]+"'=>'"+v+"'");
		}
		
	}
	
	
	return "array("+s.join(",")+")";
}

/* autocomplete util functions */

function toggleSearchTable(a,div) {
	
	var d=document.getElementById(div);
	
//	try {
		if(d.style.display!="none") {
			d.style.display="none";
			a.innerHTML="+";
		}
		else {
			d.style.display="block";
			a.innerHTML="-";
		}
		SetCookie(div,d.style.display);
//	}catch(e){}
}

function showFlashUpload(control_id,par) {
	var f=new Array();
	var i;
	for(i=0;i<par.length;i++) {
		f.push(par[i].s1);
		f.push(par[i].s2);
	}
	f=f.join('%2C');
	//alert(f);
	__createUploadDialog(control_id,f);
}

function __createUploadDialog(control_id,sizes) {
	var b=document.getElementsByTagName('body');
	if(b&&b.length) {
		b=b[0];		
	}
	else {
		alert("BODY TAG NOT FOUND!");
		return;
	}
	var d=document.getElementById("upload_dialog");
	var div;
	if(d&&d!=undefined) {
		//div=d;
		try {
		b.removeChild(d);
		}catch(e){}
	}
//	else {
		div=document.createElement("div");
		div.id="upload_dialog";
//	}
	
	var p="displaySizes="+sizes+"&amp;outputPath=%2Fflash%2FimageUpload%2Fcon%2Fexport.php&amp;nameField=filename&amp;overrideUrl=%2Fflash%2FimageUpload%2Fcon%2Fhas_file.php&amp;outputMethod=OUTPUT_METHOD_JS&amp;callBackJsOutput=upoadFlash&amp;sizeField=sizeId&amp;imageIdentifier="+control_id+"&amp;callBackCancel=cancelUpload&amp;callBackSubmit=cancelUpload";
	var flash="/flash/imageUpload/control/Index.swf?"+(p);
	div.innerHTML='<object width="550" height="400" id="preloader">'+
 	'<param name="allowScriptAccess" value="sameDomain" />'+
	'<param name="allowFullScreen" value="false" />'+
 	'<param name="movie" value="'+flash+'" />'+
 	'<param name="quality" value="high" />'+
 	'<param name="bgcolor" value="#ffffff" />'+
 	'<embed src="'+flash+'" quality="high" bgcolor="#ffffff" width="550" height="400" name="preloader" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />'+
	'</object>';
	
	b.appendChild(div);		
	
	//div.setAttribute("style","width:650px;height:400px;");
	
	
	$.ui.dialog.defaults.bgiframe = true;
//	$(div).style("width","550px");
//	$(div).style("height","450px");
	$(div).dialog();
	if(!$(div).dialog('isOpen')) {
		$(div).dialog('open');
	}
	$(div).dialog("option", "height", 480);
	$(div).dialog("option", "width", 580);
}

function cancelUpload() {
	var div=document.getElementById("upload_dialog");
	$(div).dialog( "close" );
}


function upoadFlash(control_id,index,file_name,data) {
	//alert(p1+','+p2+','+p3);
	
	var size=document.getElementById("sz_"+control_id);
	if(!size||size==undefined) {
		alert("Sizes not found");
		return;
	}
	size=size.value.split(',');
	if(size.length<index||!size[index]) {
		alert("Invalid size index");
		return;
	}
	
	var f=document.getElementById("h_"+size[index]+"_fl_"+control_id);	
	if(!f||f==undefined) {
		alert("Image field not found");
		return;
	}
	
	f.value=data;
	if(index==0) {
		document.getElementById("h_fl_"+control_id+"_name").value=file_name;
	}
}
var ITTI={}

ITTI.agent = navigator.userAgent.toLowerCase();
ITTI.is_ie = ((ITTI.agent.indexOf("msie") != -1) && (ITTI.agent.indexOf("opera") == -1));
ITTI.ID_SEPARATOR = '-';

ITTI.ie_version = 0;
if(ITTI.is_ie){
	ITTI.ie_version = parseFloat(ITTI.agent.substr(ITTI.agent.indexOf("msie ")+5));
}

ITTI.addEvent = function(el, evname, func) {
	if (ITTI.is_ie) {
		return el.attachEvent("on" + evname, func);
	} else {
		return el.addEventListener(evname, func, false);
	}
};

ITTI.removeEvent = function(el, evname, func) {
	if (this.is_ie) {
		el.detachEvent("on" + evname, func);
	} else {
		el.removeEventListener(evname, func, true);
	}
};

ITTI.addOnLoad=function(func){
	//var el=document.getElementsByTagName("body");
	ITTI.addEvent(window, 'load', func);
}

ITTI.removeClass=function(el,className) {
	if (!(el && el.className)) {
		return;
	}
	var cls = el.className.split(" ");
	var ar = new Array();
	for (var i = cls.length; i > 0;) {
		if (cls[--i] != className) {
			ar[ar.length] = cls[i];
		}
	}
	el.className = ar.join(" ");
}

ITTI.hasClass = function(el, className) {
	if (!(el && el.className)) {
		return false;
	}
	var cls = el.className.split(" ");
	for (var i = cls.length; i > 0;) {
		if (cls[--i] == className) {
			return true;
		}
	}
	return false;
}

ITTI.addClass=function(el,className) {
	this.removeClass(el, className);
	el.className += " " + className;
}

ITTI.hasAttribute=function(el,name) {
	var n=el.getAttribute(name);
	return n!=null;
}

/* ------------------------------------*/

ITTI.fixIELabel=function(){
	if(!this.is_ie||this.ie_version>=7) {
		return;
	}
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

ITTI.getScrollOffsets=function() {
	var i;
	var off = {x:0, y:0}
	var a=document.getElementsByTagName("html");
	var scy=document.body.scrollTop;
	var scx=document.body.scrollLeft;
	for(i=0;i<a.length;i++) {
		if(a[i].scrollTop) {
			scy+=a[i].scrollTop;
			
		}
		if(a[i].scrollLeft) {
			scx+=a[i].scrollLeft;
		}
	}
	
	if(scx==null||scx==undefined) {
		scx=document.body.scrollLeft;
	}
	if(scy==null||scy==undefined) {
		scy=document.body.scrollTop;
	}
	off={x:scx,y:scy};
	return off;
}

ITTI.getEventObj=function(e) {
	if(e&&e.target) {
		return e;
	}
	return event;
}

ITTI.getBoundingBox=function(el) {
	var off=ITTI.getScrollOffsets();
	var scx=off.x;
	var scy=off.y;
	var box = {top:0, left:0, right:0, bottom:0, height:0, width:0}

	if (document.getBoxObjectFor) {	//mozilla
		var r = document.getBoxObjectFor(el);
		//box = {top:r.y - document.body.scrollTop, left:r.x - document.body.scrollLeft, right:r.x - document.body.scrollLeft + r.width, bottom:r.y - document.body.scrollTop + r.height, height:r.height, width:r.width};
		box = {top:r.y , left:r.x , right:r.x  + r.width, bottom:r.y  + r.height, height:r.height, width:r.width};
	} else if (el.getBoundingClientRect) {	//ie
		var r = el.getBoundingClientRect();
		box = {top:r.top+scy, left:r.left+scx, right:r.right+scx, bottom:r.bottom+scy, height:r.bottom-r.top, width:r.right-r.left};
		//box = {top:r.top, left:r.left, right:r.right, bottom:r.bottom, height:r.bottom-r.top, width:r.right-r.left};
	}
	else {	//chrome
		box.top=el.offsetTop;
		box.left=el.offsetLeft;
		
		box.width=el.clientWidth;
		box.height=el.clientHeight;
		
		while(el = el.offsetParent){
			box.top+=el.offsetTop;
			box.left+=el.offsetLeft;
		}
		
		box.right = box.left+box.width;
		box.bottom = box.top+box.height;	  
		//return box;
		
	}
	return box;
}



/* FUNKCII ZA OCVETEWANE NA ELEMENT PRI CHANGE NA VALUE */


/*  hilite options */



ITTI.hilite={config:{}};

ITTI.hilite.config.isActive=true;
ITTI.hilite.config.color="#F1DAB4";
ITTI.hilite.FormChanged=false;


ITTI.hilite.detachSubmit=function() {
	ITTI.hilite.FormChanged=false;
}

ITTI.hilite.config.detachSubmit=ITTI.hilite.detachSubmit;

ITTI.hilite.getOptions=function(obj,obj2,isval) {
	var j=0;
	var s='';
	var sc='';
	for(j=0;j<obj.options.length;j++) {
		if(isval) {
			s+=sc+obj.options[j].value;
			sc=',';
			var h=0;
			for(h=0;h<obj2.options.length;h++) {
				if(obj2.options[h].value==obj.options[j].value) {
					obj2.options[h].style.backgroundColor=ITTI.hilite.config.color;
					break;
				}
			}
		}
		else
		if(obj.options[j].selected) {
			s+=sc+j;
			sc=',';
			
		}
		if(!isval) {
			if(obj2&&obj2.options.length>j) {
				obj2.options[j].style.backgroundColor=ITTI.hilite.config.color;
			}
		}
		
	}
	return s;
}

ITTI.hilite.create=function(e,detachUnloadFunc) {
	ITTI.hilite.getValues(detachUnloadFunc);
}



ITTI.hilite.getValues=function(detachUnloadFunc) {		
	var inputs=document.getElementsByTagName('input');
	if(inputs.length&&inputs.length!='undefined') {
		var i=0;
		for(i=0;i<inputs.length;i++) {			
			var t=inputs[i].type.toLowerCase();
			if(t=='button'||t=='hidden'||t=='submit') {
				if(t=='button') {
					if(inputs[i].id.indexOf('_bt_select_')==0) {
						ITTI.addEvent(inputs[i],'click',ITTI.hilite.Hilite);
					}
				}
				if(t=='submit') {
					if(detachUnloadFunc) {
						ITTI.hilite.config.detachSubmit=detachUnloadFunc;
						ITTI.addEvent(inputs[i],"click",detachUnloadFunc);
					}
					else {						
						ITTI.addEvent(inputs[i],"click",ITTI.hilite.detachSubmit);
					}
				}
				continue;
			}
			if(t=='checkbox'||t=='radio') {
				inputs[i].setAttribute("old_color",inputs[i].parentNode.style.backgroundColor);
				ITTI.addEvent(inputs[i],'click',ITTI.hilite.Hilite_ch);
				inputs[i].setAttribute("st_value",inputs[i].checked);
			}
			else {				
				inputs[i].setAttribute("st_value",inputs[i].value);				
				ITTI.addEvent(inputs[i],'change',ITTI.hilite.Hilite);				
			}
		}
	}
	inputs=document.getElementsByTagName('select');
	if(inputs.length&&inputs.length!='undefined') {
		var i=0;
		
		for(i=0;i<inputs.length;i++) {
			if(ITTI.hasAttribute(inputs[i],"multiple")) {
				if(inputs[i].id.indexOf('s_l_')==0)
					continue;
				var sl=document.getElementById('s_l_'+inputs[i].id);
				if(sl&&sl!='undefined') {
					inputs[i].setAttribute("st_value",ITTI.hilite.getOptions(inputs[i],sl,true));
					var b1=document.getElementById('bt1_s_l_'+inputs[i].id);
					var b2=document.getElementById('bt2_s_l_'+inputs[i].id);
					if(b1&&b2) {
						ITTI.addEvent(b1,'click',ITTI.hilite.Hilite);
						ITTI.addEvent(b2,'click',ITTI.hilite.Hilite);
						ITTI.removeEvent(sl,'change');
					}
					continue;
				}
				inputs[i].setAttribute("st_value",ITTI.hilite.getOptions(inputs[i],null,false));
			}
			else {
				inputs[i].setAttribute("st_value",inputs[i].selectedIndex);
			}
			ITTI.addEvent(inputs[i],'change',ITTI.hilite.Hilite);	
		}
	}
	inputs=document.getElementsByTagName('textarea');
	if(inputs.length&&inputs.length!='undefined') {
		var i=0;
		for(i=0;i<inputs.length;i++) {
			inputs[i].setAttribute("st_value",inputs[i].innerHTML);
			ITTI.addEvent(inputs[i],'change',ITTI.hilite.Hilite);
		}
	}
}

ITTI.hilite.Hilite_element=function(element) {
	if(!element||element=='undefined') {
		return;  	
	}
   	switch(element.tagName.toLowerCase()) {
   		case 'td': {	//vika se ot calendara   			
   			s_ob=s_ob.params.inputField;
   			if(s_ob) {
   				ITTI.hilite.Hilite_element(s_ob);
			}
   			break;
   		}
   		case 'input': {
	   			if(element.type.toLowerCase()=='button') {	   				
	   					var s=element.id.substr(8);
			   			var so=document.getElementById(s);
			   			if(so) {
			   				if(!ITTI.hilite.checkOptionsVal(so)) {
			   				//if(so.st_value!=so.options.length) {
						    	 so.style.backgroundColor  = ITTI.hilite.config.color;
						    	 ITTI.hilite.FormChanged=true;
						   	 }
						   	 else {
						   	 	so.style.backgroundColor  = "";
						   	 	ITTI.hilite.FormChanged=false;
						   	 }
			   			}
	   				
	   			return;
   			}
   			else {   				
   				if(element.value!=element.getAttribute("st_value")) {
				     element.style.backgroundColor  = ITTI.hilite.config.color;
				     ITTI.hilite.FormChanged=true;
			   	 }
			   	 else {
			   	 	element.style.backgroundColor  = "";
			   	 	ITTI.hilite.FormChanged=false;
			   	 }
			   	 
   			}
   			break;
   		}
   		case 'select': {
   			if(ITTI.hasAttribute(element,"multiple")) {
   				if(!ITTI.hilite.checkOptions(element,false)) {
					element.style.backgroundColor  = ITTI.hilite.config.color;
					ITTI.hilite.FormChanged=true;
			   	 }
			   	 else {
			   	 	element.style.backgroundColor  = "";
			   	 	ITTI.hilite.FormChanged=false;
			   	 }
   			}
   			
   			else {
   				if(element.selectedIndex!=element.getAttribute("st_value")) {
   					element.style.backgroundColor  = ITTI.hilite.config.color;
   					ITTI.hilite.FormChanged=true;
		   	 	}
		   	 	else {
		   	 		element.style.backgroundColor  = "";
		   	 		ITTI.hilite.FormChanged=false;
		   	 	}
   			}
   		   	 break;
	   	}
   		case 'textarea': {   			
   			if(element.value!=element.getAttribute("st_value")) {
		     	element.style.backgroundColor  = ITTI.hilite.config.color;
		     	ITTI.hilite.FormChanged=true;
		   	 }
		   	 else {
		   	 	element.style.backgroundColor  = "";
		   	 	ITTI.hilite.FormChanged=false;
		   	 }
		   	 break;
   		}
   		default: {
   			if(element.value!=element.getAttribute("st_value")) {
		     element.style.backgroundColor  =ITTI.hilite.config.color;
		     ITTI.hilite.FormChanged=true;
	   	 }
	   	 else {
	   	 	element.style.backgroundColor  = "";
	   	 	ITTI.hilite.FormChanged=false;
	   	 }
   		}
   	}
}

ITTI.hilite.Hilite=function(e)
{
	if(!ITTI.hilite.config.isActive)
		return;
	var s_ob=null;
	if(e&&e.target) {
		element=e.target;
		s_ob=arguments[1];
	}
	else {
		element=event.srcElement;
		s_ob=arguments[0];
	}   
	ITTI.hilite.Hilite_element(element,s_ob);
}

ITTI.hilite.checkOptionsVal=function(obj) {
	var ac=0;
	var st_value=obj.getAttribute('st_value');
	if(st_value!='') {
		var a=st_value.split(',');
		if(a.length!=obj.options.length)
			return false;
		if(a.length>0) {
			var i=0;
			for(i=0;i<a.length;i++) {
				var h=0;
				var fl=false;
				for(h=0;h<obj.options.length;h++) {
					if(obj.options[h].value==a[i]) {
						fl=true;
						break;
					}
				}
				if(!fl)
					return false;
			}
		}
	}
	else {
		if(obj.options.length>0)
			return false;
	}
	return true;
}

ITTI.hilite.checkOptions=function(obj) {
	var ac=0;
	var st_value=obj.getAttribute("st_value");
	if(st_value!='') {
		var a=st_value.split(',');
		if(a.length>0) {
			var i=0;
			for(i=0;i<a.length;i++) {
				if(a[i]<obj.options.length) {
					if(!obj.options[a[i]].selected)
						return false;
				}
			}
		}
		ac=a.length;
	}
	var count=0;
	var i=0;
	for(i=0;i<obj.options.length;i++) {
		if(obj.options[i].selected)
			count++;
	}
	return count==ac;
}

ITTI.hilite.Hilite_ch=function(e) {
	var element=e&&e.target?e.target:event.srcElement;
	if(!element||element=='undefined')
		return;	
	switch(element.type.toLowerCase()) {
		case 'radio': {
		//	try {
				var n=document.getElementsByName(element.getAttribute("name"));
				if(n&&n.length&&n.length!='undefined') {
					var i=0;
					for(i=0;i<n.length;i++) {						
						n[i].parentNode.style.backgroundColor=n[i].getAttribute("old_color");
						if(n[i].id==element.id) {
							var e_val=element.getAttribute("st_value");							
							if(e_val!=element.checked&&e_val!=element.checked.toString()) {
								n[i].parentNode.style.backgroundColor=ITTI.hilite.config.color;
								//n[i].style.backgroundColor=ITTI.hilite.config.color;
								//n[i].style="border:1px solid "+ITTI.hilite.config.color;
								
								ITTI.hilite.FormChanged=true;
							}
						}
					}
				}
		//	}catch(e) {
		//		alert(e.description);
		//	}
			return;
		}
		default: {
			var e_val=element.getAttribute("st_value");
			if(e_val!=element.checked&&e_val!=element.checked.toString()) {
				element.parentNode.style.backgroundColor  =ITTI.hilite.config.color;
				ITTI.hilite.FormChanged=true;
			}
			else {				
				element.parentNode.style.backgroundColor  = element.getAttribute("old_color");
				ITTI.hilite.FormChanged=false;
			}
		}
	}
}


// Drugi razni funkcii

ITTI.getParentElementByTagName= function(oEl,sTag)
{
  sTag = sTag.toUpperCase();
  while (oEl!=null && oEl.tagName!=sTag) {
    oEl = ITTI.is_ie ? oEl.parentElement : oEl.parentNode;
  }
  return oEl;
}


ITTI.getParent = function(oEl, tagName, attrName){
  while (oEl!=null && (oEl.tagName!=tagName ||  (oEl.tagName==tagName && !oEl.getAttribute(attrName))) ) {
    oEl = ITTI.is_ie ? oEl.parentElement : oEl.parentNode;
  }
  return oEl;
}


ITTI.getParentView = function(oEl){
	return ITTI.getParent(oEl, 'DIV', 'ViewID');
}

ITTI.getParentTab = function(oEl){
	return ITTI.getParent(oEl, 'DIV', 'tab');
}

ITTI.focusControl=function(obj){
	obj = document.getElementById(obj);
	
	var tab =ITTI.getParentTab(obj);
	if(tab) {
		var tmp = tab.getAttribute('tab');
		$('#'+tmp).tabs('select','#'+tab.getAttribute('id'));
	}
	obj.focus();
}

ITTI.doActionPost=function(ViewID, ActionId, Params){
	var obj = document.getElementById(ViewID);
	var a  = document.createElement('input');
	
	a.type='hidden';
	a.name = ActionId;
	a.value = Params;
	
	obj.appendChild(a);
	
	a.form.submit();
}
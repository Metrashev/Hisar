var default_language=2;
var cached_labels=new Array();
var last_error_type=0;

/*-----------------------------------
	ERROR_TYPES
	
	0-> OK ili unknown
	1->required value
-----------------------------------*/

function getError(language_id,value) {
	//language_id=1 ->bg
	//language_id=2 ->en
	
	var __errors_en=new Array();	//empty. Po default 6te vru6ta value
	
	var __errors_bg=new Array();
	__errors_bg["Invalid number"]= "Невалидно число";
	__errors_bg["Invalid number value"]="Невалидна стойност за число";
	__errors_bg["Required value"]= "Задължително поле";
	__errors_bg["At least one of the fields is required"]= "Поне 1 от полетата е задължително";
	__errors_bg["Invalid Date"]= "Невалидна дата";
	__errors_bg["Invalid DateTime"]= "Невалидна Дата-час";
	__errors_bg["Invalid Time"]= "Невалиден час";
	__errors_bg["Select value"]= "Изберете стойност";
	__errors_bg["Invalid Email Address"]= "Невалиден E-mail";
	__errors_bg["Invalid EGN"]= "Невалидно ЕГН";
	__errors_bg["Number out of range"]= "Стойност извън интервала";

	
	var __err=new Array(
		__errors_bg,	//0 -> po default e bg
		__errors_bg,	//1 ->bg
		__errors_en		//2 ->en
	);
	
	if(__err[language_id][value]&&__err[language_id][value]!=undefined) {
		return __err[language_id][value];
	}
	return value;
}

function checkEmail(value) {
	var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
	return filter.test(value);
}

function checkname(value) {
	var filter=/^[a-zA-Zа-яА-Я\s]*$/i
	return filter.test(value)&&value.length>3;
}

function is_valid_egn(egn) {
	var filter=/^[0-9]{10}$/i
	if(!filter.test(egn))
		return false;
//  if (!ereg("^[0-9]{10}$", $egn))
//		return false;

	var v = parseInt(egn.charAt(0))*2 + parseInt(egn.charAt(1))*4 + parseInt(egn.charAt(2))*8 + parseInt(egn.charAt(3))*5 + parseInt(egn.charAt(4))*10 + parseInt(egn.charAt(5))*9 + parseInt(egn.charAt(6))*7 + parseInt(egn.charAt(7))*3 + parseInt(egn.charAt(8))*6;
	v = (v % 11) % 10;
	return v == parseInt(egn.charAt(9));
}


function parseDate (str, fmt) {
	_MN=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
	var y = 0;
	var m = -1;
	var d = 0;
	var a = str.split(/\W+/);
	if (!fmt) {
		fmt = "%d/%m/%Y";   //defaut format ako ne e zadaden
	}
	var b = fmt.match(/%./g);
	var i = 0, j = 0;
	var hr = 0;
	var min = 0;
	for (i = 0; i < a.length; ++i) {
		if (!a[i])
			continue;
		switch (b[i]) {
		    case "%d":
		    case "%e":
			d = parseInt(a[i], 10);
			break;

		    case "%m":
			m = parseInt(a[i], 10) - 1;
			break;

		    case "%Y":
		    case "%y":
			y = parseInt(a[i], 10);
			(y < 100) && (y += (y > 29) ? 1900 : 2000);
			break;

		    case "%b":
		    case "%B":
			for (j = 0; j < 12; ++j) {
				if (_MN[j].substr(0, a[i].length).toLowerCase() == a[i].toLowerCase()) { m = j; break; }
			}
			break;

		    case "%H":
		    case "%I":
		    case "%k":
		    case "%l":
			hr = parseInt(a[i], 10);
			
			break;

		    case "%P":
		    case "%p":
			if (/pm/i.test(a[i]) && hr < 12)
				hr += 12;
			break;

		    case "%M":
			min = parseInt(a[i], 10);
			break;
		}
	}
	if (y != 0 && m != -1 && d != 0) {
		return new Date(y, m, d, hr, min, 0); 
		
		//this.setDate(new Date(y, m, d, hr, min, 0));
		return;
	}
	y = 0; m = -1; d = 0;
	for (i = 0; i < a.length; ++i) {
		if (a[i].search(/[a-zA-Z]+/) != -1) {
			var t = -1;
			for (j = 0; j < 12; ++j) {
				if (_MN[j].substr(0, a[i].length).toLowerCase() == a[i].toLowerCase()) { t = j; break; }
			}
			if (t != -1) {
				if (m != -1) {
					d = m+1;
				}
				m = t;
			}
		} else if (parseInt(a[i], 10) <= 12 && m == -1) {
			m = a[i]-1;
		} else if (parseInt(a[i], 10) > 31 && y == 0) {
			y = parseInt(a[i], 10);
			(y < 100) && (y += (y > 29) ? 1900 : 2000);
		} else if (d == 0) {
			d = a[i];
		}
	}
	if (y == 0) {
		var today = new Date();
		y = today.getFullYear();
	}
	if (m != -1 && d != 0) {
		return new Date(y, m, d, hr, min, 0);
		//this.setDate(new Date(y, m, d, hr, min, 0));
	}
	return null;
};

function setFieldColor(obj,bgColor) {
	if(obj.type=='hidden') {
		obj=getObjectLabel(obj);
		if(obj==null) {
			return;
		}
	}
	if(bgColor&&bgColor!=undefined) {
		var oc=obj.getAttribute('old_color');
		if(oc==null) {
			obj.setAttribute('old_color',obj.style.backgroundColor);
		}
		obj.style.backgroundColor=bgColor;
	}
}

function resetFieldColor(obj) {
	var oldColor=obj.getAttribute('old_color');
	if(oldColor!=null) {
		obj.style.backgroundColor=oldColor;
	}
}

function getObjectLabel(obj) {
	var label=null;
	var ps=obj.previousSibling;
	var found=false;
	while(ps) {
		if(ps.tagName) {
			var l=ps.tagName.toLowerCase();
			if(l=='label'||l=='span'||l=='b'||'td') {
				label=ps;	
				found=true;			
				break;
			}
			//alert(ps.tagName);	
		}
		ps=ps.previousSibling;
	}
	if(!found) {
		var labels=document.getElementsByTagName('label');
		if(labels.length&&labels.length!=undefined) {
			var i;
			for(i=0;i<labels.length;i++) {
				
				if(labels[i].htmlFor==obj.id) {
					label=labels[i];
				//	break;
				}
			}
		}
	}
	return label;
}

function getLabel(obj) {
	var label='';
	if(cached_labels[obj.id]&&cached_labels[obj.id]!=undefined) {
		return cached_labels[obj.id];
	}
	
	var ps=obj.previousSibling;
	var found=false;
	while(ps) {
		if(ps.tagName) {
			var l=ps.tagName.toLowerCase();
			if(l=='label'||l=='span'||l=='b') {
				label=ps.innerHTML;	
				found=true;			
				break;
			}
			//alert(ps.tagName);	
		}
		ps=ps.previousSibling;
	}
	if(!found) {
		var labels=document.getElementsByTagName('label');
		if(labels.length&&labels.length!=undefined) {
			var i;
			for(i=0;i<labels.length;i++) {
				
				if(labels[i].htmlFor==obj.id) {
					label=labels[i].innerHTML;
				//	break;
				}
			}
		}
	}
	
	cached_labels[obj.id]=label;
	return label;
}

function js_findObjectLabel(obj) {
	var require_one_of=getParameter(obj,'require_one_of');
	
	if(require_one_of&&last_error_type!=0) {
		var label=getLabel(obj);
		var comma=!label?'':', ';
		var s=require_one_of.split(',');
		if(s.length&&s.length!=undefined) {
			var i;
			for(i=0;i<s.length;i++) {
				var t=getLabel(document.getElementById(s[i]));
				if(t) {
					label+=comma+t;
					comma=', ';
				}
			}
		}
		return label;
	}
	else {
		
		return getLabel(obj);
	}
}

function checkInt(value,size,language_id,range_min,range_max,range_text) {
	
	var t=parseInt(value);
    if(isNaN(t))
         return getError(language_id, "Invalid number");
    if(t!=value) {
         return getError(language_id,"Invalid number");
    }
    if(t>size)
         return getError(language_id,"Invalid number value");
    if(range_min!=null) {
    	if(t<range_min)
    		return getError(language_id,"Number out of range")+" ["+range_text+"]";
    }
    if(range_max!=null) {
    	if(t>range_max)
    		return getError(language_id,"Number out of range")+" ["+range_text+"]";
    }
    return true;
}

function checkFloat(value,language_id) {
    var t=parseFloat(value);
    if(isNaN(t))
         return getError(language_id,"Invalid number");
    if(t!=value) {
         return getError(language_id,"Invalid number");
    }
    return true;
}

function js_validate_type(type,obj,language_id) {
	
	var value=getFieldValue(obj);
	var required=getParameter(obj,'required');
	var require_one_of=getParameter(obj,'require_one_of');
	var format=getParameter(obj,'required_format');
	var required_for_value=getParameter(obj,'required_for_value');
	var range=getParameter(obj,'range');
	var range_min=null;
	var range_max=null;
	
	last_error_type=0;
	
	if(range) {
		var r_spl=range.split(',');
		
		if(r_spl.length&&r_spl.length==2) {
			var r_temp=parseInt(r_spl[0]);
			
			if(!isNaN(r_temp)) {
				range_min= r_temp;
			}
			 r_temp=parseInt(r_spl[1]);
			if(!isNaN(r_temp)) {
				range_max= r_temp;
			}
		}
	//	if(range_min>range_max) {
	//		 r_temp=range_min;
	//		 range_min=range_max;
	//		 range_max= r_temp;
	//	}
	}
	
	
	if(required_for_value) {
		
		var spl=required_for_value.split(',');
		var is_required=false;
		if(spl.length&&spl.length!=undefined) {
			var j=0;
			is_required=true;
			for(j=0;j<spl.length;j+=2) {
				var v=getFieldValue(document.getElementById(spl[j]));
				
				if(v!=spl[j+1]) {
					is_required=false;
					continue;
				}
				else {
					is_required=true;
					break;
				}
			}
		}
		if(is_required) {
			required=true;	
		}
	}
	
//	if(!required&&require_one_of) {
	if(require_one_of) {
		required=true;
		var req_other=require_one_of.split(',');
		if(req_other.length&&req_other.length!=undefined) {
			var j=0;
			var has_value=false;
			for(j=0;j<req_other.length;j++) {
				var o=document.getElementById(req_other[j]);
				var v=getFieldValue(o);
				if(v!='') {
				//	var r=js_validate_field(o,language_id);
				//	if(r==true) {
						has_value=true;
						break;
				//	}
				}
			}
			required=!has_value;
		}
	}
	
	if(value=='') {
		last_error_type=1;
		return required?(require_one_of?getError(language_id,'At least one of the fields is required'):getError(language_id,'Required value')):true;
	}
	switch(type) {
		case 'EGN': {
			if(!is_valid_egn(value)) {
				return getError(language_id,'Invalid EGN');
			}
		}
		case 'EMAIL':	{	//specialna proverka za validen email
			if(!checkEmail(value)) {
				return getError(language_id,'Invalid Email Address');
			}
		}
		
	    case 'CHAR': 
		case 'VARCHAR': {
			if(value.length>255) {
			    return getError(language_id,"Invalid length");
			}
			return true;
		}
		case 'TEXT': {
		    return true;
		}
		case 'TINYINT': {
		    return checkInt(value,255,language_id,range_min,range_max,range);
		}
		case 'SMALLINT': {
		    return checkInt(value,65535,language_id,range_min,range_max,range);
		}
		case 'MEDIUMINT': {
		    return checkInt(value,16777216,language_id,range_min,range_max,range);
		}
		case 'INT': {
		    return checkInt(value,4294967296,language_id,range_min,range_max,range);
		}
		case 'BLOB':
		case 'MEDIUMBLOB':
		case 'TINYBLOB':
		case 'LONGBLOB':
		case 'TIMESTAMP':
		case 'TINYTEXT':
		case 'MEDIUMTEXT':
		case 'LONGTEXT':
		case 'BINARY':
		case 'VARBINARY':
		
		case 'ENUM':
		case 'SET':
		
		case 'BIGINT': {    //nqma proverka
		    return true;
		}
		case 'DOUBLE':
		case 'FLOAT': 
		case 'DECIMAL': 
		{
		    return checkFloat(value,language_id);
		}
		
		case 'DATE': {
		    var d=parseDate(value,format);
		    if(d instanceof Date) {
		        return true;
		    }
		    return "Invalid Date";
		}
		case 'DATETIME': {
		    var d=parseDate(value,format);
		    if(d instanceof Date) {
		        return true;
		    }
		    return "Invalid DateTime";
		}
		case 'TIME': {
		    var d=parseDate(value,format);
		    if(d instanceof Date) {
		        return true;
		    }
		    return "Invalid Time";
		}
		case 'YEAR': {
		    return checkInt(value,9999,language_id,range_min,range_max,range);
		}
		
	}
	return true;
}

function getFieldValue(obj) {
	var value='';
	switch(obj.tagName) {
		case 'INPUT':
		case 'input': 
		case 'TEXTAREA':
		case 'textarea':
		{
			if(obj.type=='radio'||obj.type=='checkbox') {
				value=obj.checked?obj.value:null;	
			}
			else {
				value=obj.value;
			}
			break;
		}
		case 'select': 
		case 'SELECT': {
			if(obj.selectedIndex!=-1) {
				value=obj.options[obj.selectedIndex].value;
			}
			break;
		}
	}
	return value;
}

function getParameter(obj,par) {
	var value=obj.getAttribute(par);
	if(value==undefined) {
		value=null;
	}
	return value;
}

function js_validate_radio(span,language_id) {
	var index=0;
	var id_name=span.id;
	var indexes=span.getAttribute('indexes');
	if(indexes&&indexes!=undefined) {
		indexes=indexes.split(',');
		if(indexes.length&&indexes.length!=undefined) {
			var i;
			for(i=0;i<indexes.length;i++) {
				
				var t=document.getElementById(id_name+'_'+indexes[i]);
				if(t&&t!=undefined) {
					if(t.tagName.toLowerCase()=='input'&&t.type.toLowerCase()=='radio') {
						if(t.checked) {
							return true;
						}
					}
				}
			}
			return getError(language_id,'Required value');
		}
	}
	while(1) {
		
		var t=document.getElementById(id_name+'_'+index);
		index++;
		if(t&&t!=undefined) {
			if(t.tagName.toLowerCase()=='input'&&t.type.toLowerCase()=='radio') {
				if(t.checked) {
					return true;
				}
			}
		}
		else {
			return getError(language_id,'Required value');
		}
	}
	return true;
}

function js_validate_field(obj,language_id) {
	if(!obj||obj==undefined) {
		return true;
	}
	var t=obj.getAttribute('required_type');
	if(t&&t!=undefined) {
		
		if(obj.tagName.toLowerCase()=='span'||obj.obj_to_validate=='radio') {
			return js_validate_radio(obj,language_id);
		}
		return js_validate_type(t,obj,language_id);
	}
	return true;
}

function js_validate_page(language_id,bgColor) {
	var obj=null;
	var errors='';
	var err_count=0;
	language_id=parseInt(language_id);
	if(isNaN(language_id)) {
		language_id=default_language;
	}
	if(language_id<1) {
		language_id=default_language;
	}
	
	//var tags=new Array('input','textarea','select');
	var tags=new Array('*');
	var j;
	var objects;
	for(j=0;j<tags.length;j++) {
		objects=document.getElementsByTagName(tags[j]);
		if(objects.length&&objects.length!=undefined) {
			var i=0;
			for(i=0;i<objects.length;i++) {
				var result=js_validate_field(objects[i],language_id);
				if(result!=true) {
					var label=js_findObjectLabel(objects[i]);
					if(label&&label!=undefined) {
						result+=' - '+label;
					}
				    err_count++;
				    errors+=err_count+') '+result+'\n';;
					setFieldColor(objects[i],bgColor);
				    if(!obj) {
				        obj=objects[i];
				    }
				}
				else {
					resetFieldColor(objects[i]);
				}
			}
		}
	}
	
	if(err_count>0) {
		alert(errors);
		if(obj) {
			try {
				obj.focus();
			}catch(e){}
		}
		return false;
	}
	return true;
}
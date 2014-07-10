ns4 = (document.layers)? true:false
ie4 = (document.all)? true:false
ns6 = (document.getElementById)? true:false

function popup_wnd(f,w,h){
var wnd = window.open(f, 'popup_wnd', 'height='+h+',width='+w+',scrollbars=1,resizable=1,menubar=0,toolbar0,status=1,location=0,directories=0,left=0,top=0');
wnd.focus();
return wnd;
}

function popup_wnd2(f,w,h,name){
var wnd = window.open(f, name, 'height='+h+',width='+w+',scrollbars=1,resizable=1,menubar=0,toolbar0,status=1,location=0,directories=0,left=0,top=0');
wnd.focus();
return wnd;
}

function show_html(text,w,h){
var wnd = window.open("", 'show_html', 'height='+h+',width='+w+',scrollbars=1,resizable=1,menubar=0,toolbar0,status=1,location=0,directories=0,left=0,top=0');
wnd.document.write("<head><title>HTML View</title></head><body>"+text);

wnd.focus();
return wnd;
}

function MM_findObj(n, d) { //v3.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); return x;
}

function find_control(ctrl_name){
var ctrl = 0;
	for(i=0; i<document.forms.length; i++)
	{
		if(!ctrl) ctrl = document.forms[i].elements['in_data['+ctrl_name+']'];
		if(!ctrl) ctrl = document.forms[i].elements['in_data['+ctrl_name+'][]'];
		if(!ctrl) ctrl = document.forms[i].elements[ctrl_name];
	}

	return ctrl;
}

function focus_control(ctrl_name){
	var ctrl = find_control(ctrl_name);
	if(ctrl) ctrl.focus();
	return false;
}

function clear_form(f){
	tmpCollection = document.forms[f].all.tags("INPUT")
	for (i=0; i<tmpCollection.length; i++) {
		if(tmpCollection(i).type=="text") tmpCollection(i).value='';
	}

	tmpCollection = document.forms[f].all.tags("SELECT")
	for (i=0; i<tmpCollection.length; i++) {
		if(!tmpCollection(i).multiple) tmpCollection(i).selectedIndex = -1;
	}

	tmpCollection = document.forms[f].all.tags("OPTION")
	for (i=0; i<tmpCollection.length; i++) {
		tmpCollection(i).selected = false;
	}

}

function PrintIt(){
	if(ie4 || ns4 || ns6){
		self.print();
		self.close();
	}
	else{
		alert('Now right/apple click and choose the \'print\' option.')
	}
}


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
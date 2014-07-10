var const_opacity="0.6";
var const_alpha="alpha(opacity=60)";
var dragObj=null;

function loadOrder(table_id) {

	var c=GetCookie(table_id+'_order');
	if(!dragObj.order) {
		dragObj.order=new Array();
	}
	if(c) {
		dragObj.order[table_id]=c.split(',');
	}
	else {
		dragObj.order[table_id]=new Array();
		var t=document.getElementById(table_id);
		if(!isValid(t)) {
			return;
		}
		for(var i=0;i<t.rows[0].cells.length;i++) {
			dragObj.order[table_id][i]=checkAttribute(t.rows[0].cells[i],"id");
		}
	}
}

function movemouse(e)
{
  if (dragObj.isdrag)
  {
	var nx=dragObj.nn6 ? tx + e.clientX - dragObj.m_x : tx + event.clientX - dragObj.m_x;
	if(nx) {
		dragObj.has_move=true;
	}
	
    dragObj.dobj.style.left = nx;
    //dragObj.dobj.style.top  = dragObj.nn6 ? ty + e.clientY - dragObj.m_y : ty + event.clientY - dragObj.m_y;
    return false;
  }
}

function isValid(a) {
	return a&&a!="undefined"&&a!=undefined;
}

function alterOrder(table_id,index_1,index_2) {

	var tmp=dragObj.order[table_id][index_1];
	dragObj.order[table_id][index_1]=dragObj.order[table_id][index_2];
	dragObj.order[table_id][index_2]=tmp;
	
	SetCookie(table_id+"_order",dragObj.order[table_id].join(','));
}

function getParentTable(obj) {
	while(obj&&obj.tagName.toLowerCase()!='table') {
		obj=dragObj.nn6?obj.parentNode : obj.parentElement;
	}
	return obj;
}

function mouseup(e) {
	
	var target=null;
	if(e) {
		target=e.target;
	}
	else {
		target=event.srcElement;
	}
	if(dragObj.real_obj) {
		resetTableTransparent(getParentTable(dragObj.real_obj));
	}
	if(!dragObj.has_move) {
		dragObj.isdrag=false;

		if(isValid(dragObj.dobj)) {
			dragObj.dobj.style.zIndex=0;
			dragObj.dobj.className=dragObj.dobj.class_tmp;
		}
		if(dragObj.real_obj&&dragObj.real_obj.click) {
			dragObj.real_obj.click();
		}
		dragObj.real_obj=null;
		return;
	}
	dragObj.real_obj=null;
	dragObj.isdrag=false;
	dragObj.has_move=false;
	while(target&&(target.tagName.toLowerCase()!='td'||target.tagName.toLowerCase()=='table')) {
		target=dragObj.nn6?target.parentNode : target.parentElement;
	}
	dragObj.dobj.className=dragObj.dobj.class_tmp;
	dragObj.dobj.style.left=0;
	dragObj.dobj.style.top=0;
	//try {
		var i1=dragObj.dobj.cellIndex;	
		var i2=target.cellIndex;
		var parentTable=getParentTable(target);
		swapCells(parentTable,i1,i2);
		alterOrder(checkAttribute(parentTable,'id'),i1,i2);
	//}
	//catch(ex) {
		//alert('Cannot move cells!');
	//}
}

function makeTableTransparent(table_obj) {

	var t=table_obj;
	if(!isValid(t)) {
		return;
	}
	var tr=t.rows[0];
	if(!isValid(tr)) {
		return;
	}
	for(var i=0;i<tr.cells.length;i++) {
		var c=tr.cells[i];
		if(c==dragObj.dobj) {
			continue;
		}
		c.op_temp=c.style.opacity;
		c.fl_temp=c.style.filter;
		c.style.opacity=const_opacity;
		c.style.filter=const_alpha;
	}
}

function resetTableTransparent(table_obj) {
	var t=table_obj;
	if(!isValid(t)) {
		return;
	}
	var tr=t.rows[0];
	if(!isValid(tr)) {
		return;
	}
	for(var i=0;i<tr.cells.length;i++) {
		var c=tr.cells[i];
		c.style.opacity=c.op_temp;
		c.style.filter=c.fl_temp;
	}
}

function checkAttribute(obj,name) {
	if(isValid(obj.hasAttribute)) {
		if(obj.hasAttribute(name)) {
			return obj.getAttribute(name);
		}
		return null;
	}
	return obj.getAttribute(name);

}

function selectmouse(e) 
{

  var fobj       = dragObj.nn6 ? e.target : event.srcElement;
  var topelement = dragObj.nn6 ? "HTML" : "BODY";
	dragObj.real_obj=fobj;

  while (fobj.tagName.toUpperCase() != topelement && checkAttribute(fobj,"candrag")!=1)
  {
    fobj = dragObj.nn6 ? fobj.parentNode : fobj.parentElement;
  }
	
  if (fobj&&checkAttribute(fobj,"candrag")==1)
  {
  	fobj.class_tmp=	fobj.className;
	fobj.className="dragme";
    dragObj.isdrag = true;
    dragObj.dobj = fobj;
    if(!dragObj.nn6) {
		fobj.style.zIndex=-999;
	}
    tx = parseInt(dragObj.dobj.style.left+0);
    ty = parseInt(dragObj.dobj.style.top+0);
    dragObj.m_x = dragObj.nn6 ? e.clientX : event.clientX;
    //dragObj.m_y = dragObj.nn6 ? e.clientY : event.clientY;
    document.onmousemove=movemouse;
	document.onmouseup=mouseup;
	makeTableTransparent(getParentTable(dragObj.dobj));
    return false;
  }
	else {
		dragObj.real_obj=null;
		if(dragObj.mouse_down) {
			dragObj.mouse_down();
		}
	}
}

function reorderRows(order,table_id) {
	var t=document.getElementById(table_id);
	if(!t||t=="undefined"||t==undefined) {
		return;
	}
	var tr0=t.rows[0];
	var tr1=t.rows[1];
	for(var i=0;i<tr0.cells.length;i++) {
		tr0.cells[i].swapNode(tr1.cells[i]);
	}
}

function swapNodes(src_obj,dst_obj) {
	var nextSibling = src_obj.nextSibling;
	var parentNode = src_obj.parentNode;
	dst_obj.parentNode.replaceChild(src_obj, dst_obj);
	parentNode.insertBefore(dst_obj, nextSibling);  
}

function swapCells(table,row1_index,row2_index) {
	
	for(var i=0;i<table.rows.length;i++) {
		if(dragObj.nn6) {
			swapNodes(table.rows[i].cells[row1_index],table.rows[i].cells[row2_index]);
		}
		else {
			table.rows[i].cells[row1_index].swapNode(table.rows[i].cells[row2_index]);
		}
	}
}

function reorderCells(table_id,order) {

	if(!order.length||order.length=="undefined"||order.length==undefined) {
		return;
	}
	var t=document.getElementById(table_id);
	if(!t||t=="undefined"||t==undefined) {
		return;
	}

	if(!t.rows.length||t.rows.length=="undefined"||t.rows.length==undefined) {
		return;
	}
	var c_length=t.rows[0].cells.length;
	if(!c_length||c_length=="undefined"||c_length==undefined) {
		return;
	}
	for(var i=0;i<order.length;i++) {
		var id=order[i];
		var c=document.getElementById(id);
		if(!isValid(c)) {
			continue;
		}
		var ci=c.cellIndex;
		if(ci!=i) {
			swapCells(t,ci,i);
		}
	}	
}

function drag_init() {
	if(!dragObj) {
		dragObj={
		isdrag:false, 
		m_x:null, 
		m_y:null, 
		dobj:null, 
		real_obj:null, 
		has_move:false, 
		nn6:document.getElementById&&!document.all, 
		order:null,
		mouse_down:document.onmousedown,
		mouse_up:document.onmouseup
		};
		document.onmousedown=selectmouse;
	}
}
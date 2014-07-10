function __createDialog(innerHTML) {
	var d=document.getElementById("dialog");
	if(d&&d!=undefined) {
		return d;
	}
	var div=document.createElement("div");			
	div.id="dialog";
	div.innerHTML=innerHTML;
	var b=document.getElementsByTagName('body');
	if(b&&b.length) {
		b[0].appendChild(div);		
	}
	return div;
}

function showDialog(d) {
	$.ui.dialog.defaults.bgiframe = true;
	
	$(d).dialog();
	if(!$(d).dialog('isOpen')) {
		$(d).dialog('open');
	}
}

function showDialog2(d) {
	//d = document.getElementById(d).innerHTML;
	//d = __createDialog(d);
	showDialog(document.getElementById(d));
}

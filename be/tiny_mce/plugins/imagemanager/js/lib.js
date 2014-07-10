
function URLEncode(str) {
	var SAFECHARS = "0123456789" +          // Numeric
	"ABCDEFGHIJKLMNOPQRSTUVWXYZ" +  // Alphabetic
	"abcdefghijklmnopqrstuvwxyz" +
	"-_.!~*'()";                    // RFC2396 Mark characters
	var HEX = "0123456789ABCDEF";
	var encoded = "";

	for (var i=0; i<str.length; i++) {
		var ch = str.charAt(i);
		if (ch == " ") {
			encoded += "+"; // x-www-urlencoded, rather than %20
		} else if (SAFECHARS.indexOf(ch) != -1) {
			encoded += ch;
		} else {
			var charCode = ch.charCodeAt(0);
			if (charCode > 255) {
				alert( "Unicode Character '"
					+ ch
					+ "' cannot be encoded using standard URL encoding.\n"
					+ "(URL encoding only supports 8-bit characters.)\n"
					+ "A space (+) will be substituted.");
				encoded += "+";
			} else {
				encoded += "%";
				encoded += HEX.charAt((charCode >> 4) & 0xF);
				encoded += HEX.charAt(charCode & 0xF);
			}
		}
	} // for

	return encoded;
}

function GoToFolder(folder) {
	jQuery.cookie('imagemanager-settings-lastdir', folder, 1);
	top.fr_folder.document.location = 'folder.php?dir=' + URLEncode(folder);
	jQuery('input[name="dir"]')[0].value = folder;
	return false;
}

function CheckDelete(to_del) {
	tmp = to_del;
	if (tmp.length>1) {
		if (confirm("Are you sure you want to delete this file:\n"+tmp)) {
			document.getElementById('delete').value = to_del;
			document.f2.submit();
			return true;
		}
	}
	return false;
}


function TransferSelected(file_selected, file_size) {
	var href = base_virtual_disk_URL + document.f2.dir.value+file_selected;

	if (tinyMCEPopup.getWindowArg('selector_func')=='customFB') {
		var target_ctrl = tinyMCEPopup.getWindowArg('target_ctrl');
		target_ctrl.value = href;
	} else {
		tinyMCEPopup.editor.execCommand('mceInsertContent',false,'<img src="'+href+'" alt="" />');
	}

	top.close();
}

// thumbnail size functions

thumbs = {

	// gets the size from cookie
	getSize : function() {
		var val = jQuery.cookie('imagemanager-settings-thumbsize');

		switch (val) {
			case 'large' :
			case 'medium' :
				return val;
			default:
				return 'small';
		}
	},

	// sets the size to cookie for 1 day
	setSize : function(size) {
		switch (size) {
			case 'large':
			case 'medium':
			case 'small':
				break;
			default:
				return 1;
		}

		jQuery.cookie('imagemanager-settings-thumbsize', size, 1);

		return 0;
	},

	setSelectedButton : function(size) {
		jQuery('.icon_size_small').removeClass('icon_size_active');
		jQuery('.icon_size_medium').removeClass('icon_size_active');
		jQuery('.icon_size_large').removeClass('icon_size_active');
		jQuery('.icon_size_'+size).addClass('icon_size_active');
	},

	updateFromCookie : function() {
		var size = this.getSize();

		switch (size) {
			case 'small':  var dimensions = {height: 100, width: 100}; break;
			case 'medium': var dimensions = {height: 175, width: 175}; break;
			case 'large':  var dimensions = {height: 250, width: 250}; break;
		}

		jQuery('img.thumb').aeImageResize(dimensions);

		jQuery('div.image_thumb').css('height', dimensions.height+55).css('width', dimensions.width+12);

		this.setSelectedButton(size);
	},

	changeSize : function(size) {
		if (this.setSize(size)==0) { // success
			this.updateFromCookie();
		}
	}

}


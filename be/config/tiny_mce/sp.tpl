<script language="javascript" type="text/javascript" src="<?=BE_DIR;?>tiny_mce/tiny_mce.js"></script>
<!--<script language="javascript" type="text/javascript" src="/be/tiny_mce/tiny_mce_gzip.php"></script>-->
<script language="javascript" type="text/javascript">
	// Notice: The simple theme does not use all options some of them are limited to the advanced theme
	tinyMCE.init({
		mode : "exact",
		elements : "_#BODY#_",
		theme : "advanced",
		//plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
		plugins : "style,table,advhr,advimage,advlink,inlinepopups,preview,media,searchreplace,contextmenu,paste,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist,fileman,internallink,imagemanager",
		//plugins : "inlinepopups,advlist,media,table,contextmenu,paste,fileman,internallink,templates,advlink,preview,advimage,flash,imagemanager",
		theme_advanced_buttons1 : "formatselect,styleselect,|,bold, italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,|,forecolor,backcolor,styleprops,removeformat",
		theme_advanced_buttons2 : "table,visualaid,|,link,unlink,anchor,|,internallink,filemanlink,imagemanager,media,|,sub,sup,|,charmap,|,undo,redo,|,pastetext,pasteword,replace,|,fullscreen,code",
		theme_advanced_buttons3 : "",
		
		theme_advanced_toolbar_location : "top",
		theme_advanced_path_location : "bottom",
		
		content_css : "<?=CSS_DIR;?>lib_be.css",// + new Date().getTime(),
		
		theme_advanced_styles : "Title=spTitle;Subtitle=spSubTitle;ImgLeft=ImgLeft;ImgRight=ImgRight;DownloadLink=DownloadLink",
		
		relative_urls : false,
		remove_script_host : true,
		document_base_url :'<?=BASE_DIR;?>',
		convert_urls : true,
		
		//custom_undo_redo : true,

		//extended_valid_elements : "iframe[*],map[*],area[shape|coords|href|title|target],object[*],param[*],embed[*],ittiscript[*],input[*],script[*],div[*]",
		
		entity_encoding : "raw",//All characters will be stored in non-entity form except these XML default entities: &amp; &lt; &gt; &quot;
		file_browser_callback : 'myFileBrowser'



	});

function myFileBrowser(field_name, url, type, win) {
	var ed = tinyMCE.activeEditor;
	

	var c_url=ed.baseURI.toAbsolute()+'plugins/';
	
	var Title='';
	
	switch(type) {
		case "image": {
			c_url+="imagemanager";
			Title = 'Image Manager';
		    break;
		}
		default: {
			c_url+="fileman";
			Title = 'File Browser';
			
		    break;
		}		
	} 
	
	ed.windowManager.open({
	        file : c_url+'/popup.php',
	        title : Title,
	        width : 600,  // Your dimensions may differ - toy around with them!
	        height : 500,
	        resizable : "yes",
	        inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
	        close_previous : "yes",
	        scrollbars : "yes",
			popup_css : false
	    }, {
			plugin_url : c_url, // Plugin absolute URL
			target_ctrl : win.document.forms[0].elements[field_name],
	        selector_func : 'customFB' 
		});
	
    return false;
}

</script>
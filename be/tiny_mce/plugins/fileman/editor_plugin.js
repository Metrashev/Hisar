/**
 * $Id: editor_plugin_src.js 827 2008-04-29 15:02:42Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	// Load plugin specific language pack
	//tinymce.PluginManager.requireLangPack('example');

	tinymce.create('tinymce.plugins.filemanPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mcefilemanImage', function() {
				
				ed.windowManager.open({
					file : url+'/popup.php',
					width : 600,
					height : 500,
					inline : 1,
					resizable : "yes",
					scrollbars : "yes",
					close_previous : "yes",
					popup_css : false
					
				}, {
					plugin_url : url, // Plugin absolute URL
					selector_func : 'insertMyImage' // Custom argument
				});
			});
			
			ed.addCommand('mcefilemanLink', function() {
				
				ed.windowManager.open({
					file : url+'/popup.php',
					width : 600,
					height : 500,
					inline : 1,
					resizable : "yes",
					scrollbars : "yes",
					close_previous : "yes",
					popup_css : false
				}, {
					plugin_url : url, // Plugin absolute URL
					selector_func : 'insertMyLink' // Custom argument
				});
			});

			// Register example button
			
			ed.addButton('filemanimage', {
				title : 'File Manager Image',
				cmd : 'mcefilemanImage',
				image : url + '/images/fileman.gif'
			});
			ed.addButton('filemanlink', {
				title : 'File Manager Link',
				cmd : 'mcefilemanLink',
				image : url + '/images/fileman2.gif'
			});
			

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('fileman', n.nodeName == 'A');
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'File manager',
				author : 'Studio ITTI',
				authorurl : 'http://studioitti.com',
				infourl : 'http://studioitti.com',
				version : '1.0'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('fileman', tinymce.plugins.filemanPlugin);
})();
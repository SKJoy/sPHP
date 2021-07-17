<?php
	$TinyMCEHTMLHeadCode = "<!-- TinyMCE: BEGIN -->
		<script language='javascript' type='text/javascript' charset='utf-8'>
		<!--//
			tinyMCE.init({
				// General options
				mode: 'none', // none, textareas, exact
				//elements : '__MY_TEXTAREA__',
				theme: '{$Configuration["TinyMCETextareaTheme"]}',
				skin: '{$Configuration["TinyMCETextareaSkin"]}',
				plugins: 'pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave',

				// Fix the paragraph tag
				forced_root_block: '', //forced_root_block : 'p',
				//force_p_newlines: true,
				//force_br_newlines: true,

				// Theme options
				//theme_advanced_buttons1: 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
				//theme_advanced_buttons2: 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview',
				//theme_advanced_buttons3: 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
				//theme_advanced_buttons4: 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,|,forecolor,backcolor,userfilebrowser',

				// Customized by Joy
				theme_advanced_buttons1: 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
				theme_advanced_buttons2: 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview',
				theme_advanced_buttons3: 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
				theme_advanced_buttons4: 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,|,forecolor,backcolor,|,userFileBrowser,userExternalResourcePostImageOrg,userExternalResourceFileDropperCom',

				setup: function(ed){
					// Add a custom button
					ed.addButton('userFileBrowser', {
						title: 'File browser',
						image: './image/icon/filemanager.png',
						onclick: function(){
							// Add you own code to execute something on click
							//OpenUserFileManager();
							alert('Script: TinyMCE Head HTML PHP\\n\\nUpdate TinyMCE loader custom code to launch the file browser.');
						}
					});

					ed.addButton('userExternalResourcePostImageOrg', {title: 'Post Image', image: './image/icon/site/postimage.org.png', onclick: function(){window.open('http://www.PostImage.Org');}});
					ed.addButton('userExternalResourceFileDropperCom', {title: 'File Dropper', image: './image/icon/site/filedropper.com.png', onclick: function(){window.open('http://www.FileDropper.Com');}});
				},

				theme_advanced_toolbar_location: 'top',
				theme_advanced_toolbar_align: 'left',
				theme_advanced_statusbar_location: 'bottom',
				theme_advanced_resizing: true,

				// Example content CSS (should be your site CSS)
				//content_css: 'css/content.css',
				content_css: './style/tinymcecontent.css',

				// Drop lists for link/image/media/template dialogs
				template_external_list_url: 'lists/template_list.js',
				external_link_list_url: 'lists/link_list.js',
				external_image_list_url: 'lists/image_list.js',
				media_external_list_url: 'lists/media_list.js',

				// Style formats
				style_formats : [
					{title: 'Bold text', inline: 'b'},
					{title: 'Red text', inline: 'span', styles: {color : '#ff0000'}},
					{title: 'Red header', block : 'h1', styles: {color : '#ff0000'}},
					{title: 'Example 1', inline: 'span', classes: 'example1'},
					{title: 'Example 2', inline: 'span', classes: 'example2'},
					{title: 'Table styles'},
					{title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
				],

				// Replace values for the template plugin
				template_replace_values: {
					username: 'Some User',
					staffid: '991234'
				}
			});
		//-->
		</script>
		<!-- TinyMCE: END -->\n\n		";

	$TinyMCEHTMLHeadCode = null;
?>
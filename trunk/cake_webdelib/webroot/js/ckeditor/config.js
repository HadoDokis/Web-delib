/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
       config.toolbar = 'MyToolbar';
 
	config.toolbar_MyToolbar =
	[
		{ name: 'document', items : [ 'Source','Save', '-', 'Preview' ] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','Scayt' ] },
                '/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Strike','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Blockquote' ] },
		{ name: 'insert', items : [ 'Image','Table','SpecialChar'] },
            	{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'tools', items : [ 'Maximize','-','About' ] }
	];
};

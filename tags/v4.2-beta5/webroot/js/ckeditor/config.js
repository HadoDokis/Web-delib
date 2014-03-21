/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
        config.language            = 'fr';
	config.uiColor             = '#AADC6E';
        config.toolbar             = 'MyToolbar';
        config.entities            = false;
        config.basicEntities       = false;
        config.entities_additional = false;
        config.entities_latin      = false;
        config.entities_greek      = false;
        config.baseHref            = 'http://webdelib/';
        config.baseUrl            = 'http://webdelib/';

        config.filebrowserBrowseUrl      = '/js/ckfinder/ckfinder.html';
	config.filebrowserImageBrowseUrl = '/js/ckfinder/ckfinder.html?type=Images';
	config.filebrowserUploadUrl      = '/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
	config.filebrowserImageUploadUrl = '/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';

	config.toolbar_MyToolbar =
	[
		{ name: 'document', items : [ 'Source','Save', '-', 'Preview' ] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','Scayt' ] },
                '/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline', 'Strike','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Blockquote' ] },
		{ name: 'insert', items : [ 'Table','SpecialChar'] },
            	{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'tools', items : [ 'Maximize','-','About' ] }
	];
};

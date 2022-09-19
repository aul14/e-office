/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

	config.contentsCss = '/assets/css/wysiwyg.css',
	config.toolbar = [
	                  ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo', '-', 'Table'],
	                  ['Bolt', 'Italic', 'Underline']
	                  
	                  ],
	
	config.enterMode = CKEDITOR.ENTER_BR;
};

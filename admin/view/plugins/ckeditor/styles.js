/**
 * Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

// This file contains style definitions that can be used by CKEditor plugins.
//
// The most common use for it is the "stylescombo" plugin, which shows a combo
// in the editor toolbar, containing all styles. Other plugins instead, like
// the div plugin, use a subset of the styles on their feature.
//
// If you don't have plugins that depend on this file, you can simply ignore it.
// Otherwise it is strongly recommended to customize this file to match your
// website requirements and design properly.

CKEDITOR.stylesSet.add( 'default', [
	/* Block Styles */

	// These styles are already available in the "Format" combo ("format" plugin),
	// so they are not needed here by default. You may enable them to avoid
	// placing the "Format" combo in the toolbar, maintaining the same features.
	/*
	{ name: 'Paragraph',		element: 'p' },
	{ name: 'Heading 1',		element: 'h1' },
	{ name: 'Heading 2',		element: 'h2' },
	{ name: 'Heading 3',		element: 'h3' },
	{ name: 'Heading 4',		element: 'h4' },
	{ name: 'Heading 5',		element: 'h5' },
	{ name: 'Heading 6',		element: 'h6' },
	{ name: 'Preformatted Text',element: 'pre' },
	{ name: 'Address',			element: 'address' },
	*/
       {name: 'Paragraph Lead', element: 'p', attributes: {'class': 'lead'}},

	{ name: 'Italic Title',		element: 'h2', styles: { 'font-style': 'italic' } },
	{ name: 'Subtitle',			element: 'h3', styles: { 'color': '#aaa', 'font-style': 'italic' } },
	{
		name: 'Special Container',
		element: 'div',
		styles: {
			padding: '5px 10px',
			background: '#eee',
			border: '1px solid #ccc'
		}
	},

	/* Inline Styles */

	// These are core styles available as toolbar buttons. You may opt enabling
	// some of them in the Styles combo, removing them from the toolbar.
	// (This requires the "stylescombo" plugin)
	/*
	{ name: 'Strong',			element: 'strong', overrides: 'b' },
	{ name: 'Emphasis',			element: 'em'	, overrides: 'i' },
	{ name: 'Underline',		element: 'u' },
	{ name: 'Strikethrough',	element: 'strike' },
	{ name: 'Subscript',		element: 'sub' },
	{ name: 'Superscript',		element: 'sup' },
	*/

	{ name: 'Marker',			element: 'span', attributes: { 'class': 'marker' } },

	{ name: 'Big',				element: 'big' },
	{ name: 'Small',			element: 'small' },
	{ name: 'Typewriter',		element: 'tt' },

	{ name: 'Computer Code',	element: 'code' },
	{ name: 'Keyboard Phrase',	element: 'kbd' },
	{ name: 'Sample Text',		element: 'samp' },
	{ name: 'Variable',			element: 'var' },

	{ name: 'Deleted Text',		element: 'del' },
	{ name: 'Inserted Text',	element: 'ins' },

	{ name: 'Cited Work',		element: 'cite' },
	{ name: 'Inline Quotation',	element: 'q' },

	{ name: 'Language: RTL',	element: 'span', attributes: { 'dir': 'rtl' } },
	{ name: 'Language: LTR',	element: 'span', attributes: { 'dir': 'ltr' } },
{name: 'Blockquote Reverse', element: 'blockquote', attributes: {'class': 'blockquote-reverse'}},

{
                    name: 'Unstyled List',
                    element: 'ul',
                    attributes:
                            {
                                'class': 'list-unstyled'
                            }
                },
                {
                    name: 'List inline',
                    element: 'ul',
                    attributes:
                            {
                                'class': 'list-inline'
                            }
                },
                {
                    name: 'row active',
                    element: 'tr',
                    attributes:
                            {
                                'class': 'active'
                            }
                },
                {
                    name: 'row success',
                    element: 'tr',
                    attributes:
                            {
                                'class': 'success'
                            }
                },
                {
                    name: 'row warning',
                    element: 'tr',
                    attributes:
                            {
                                'class': 'warning'
                            }
                },
                {
                    name: 'row danger',
                    element: 'tr',
                    attributes:
                            {
                                'class': 'danger'
                            }
                },
                {
                    name: 'row info',
                    element: 'tr',
                    attributes:
                            {
                                'class': 'info'
                            }
                },
                
                {
                    name: 'Responsive Table Container',
                    element: 'div',
                    attributes:
                            {
                                'class': 'table-responsive'
                            }
                },
                 {
                    name: 'Text Muted',
                    element: 'span',
                    attributes:
                            {
                                'class': 'text-muted'
                            }
                }
                ,
                 {
                    name: 'Text Primary',
                    element: 'span',
                    attributes:
                            {
                                'class': 'text-primary'
                            }
                },
                 {
                    name: 'Text Success',
                    element: 'span',
                    attributes:
                            {
                                'class': 'text-success'
                            }
                },
                 {
                    name: 'Text Danger',
                    element: 'span',
                    attributes:
                            {
                                'class': 'text-danger'
                            }
                },
                 {
                    name: 'Text info',
                    element: 'span',
                    attributes:
                            {
                                'class': 'text-info'
                            }
                },
                 {
                    name: 'Text Warning',
                    element: 'span',
                    attributes:
                            {
                                'class': 'text-warning'
                            }
                }
                ,
                 {
                    name: 'Primary Background',
                    element: 'p',
                    attributes:
                            {
                                'class': 'bg-primary'
                            }
                },
                 {
                    name: 'Success Background',
                    element: 'p',
                    attributes:
                            {
                                'class': 'bg-success'
                            }
                },
                 {
                    name: 'Info Background',
                    element: 'p',
                    attributes:
                            {
                                'class': 'bg-info'
                            }
                },
                 {
                    name: 'Warning Background',
                    element: 'p',
                    attributes:
                            {
                                'class': 'bg-warning'
                            }
                },
                 {
                    name: 'Danger Background',
                    element: 'p',
                    attributes:
                            {
                                'class': 'bg-danger'
                            }
                },
                { name: 'Square Bulleted List',	element: 'ul',		styles: { 'list-style-type': 'square' } }
] );


/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function (config) {
    // Define changes to default configuration here.
    // For complete reference see:
    // http://docs.ckeditor.com/#!/api/CKEDITOR.config


    config.toolbarGroups = [
        {name: 'tools', groups: ['tools']},
        {name: 'document', groups: ['mode', 'document', 'doctools']},
        {name: 'clipboard', groups: ['clipboard', 'undo']},
        {name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing']},
        {name: 'links', groups: ['links']},
        {name: 'insert', groups: ['insert']},
        {name: 'forms', groups: ['forms']},
        {name: 'others', groups: ['others']},
        '/',
        {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
        {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
        {name: 'styles', groups: ['styles']},
        {name: 'colors', groups: ['colors']},
        {name: 'about', groups: ['about']}
    ];


    config.uiColor = '#ffffff';

    config.toolbarCanCollapse = true;

    config.removeButtons = 'Font,FontSize,About';

    // Set the most common block elements.
    config.format_tags = 'p;h1;h2;h3;pre';

    // Simplify the dialog windows.
    config.removeDialogTabs = 'link:advanced:image:Upload';

    config.skin = 'moonocolor';

    config.height = '350';     // CSS unit (em).
    config.allowedContent = true;
    CKEDITOR.dtd.$removeEmpty['i'] = false;
    CKEDITOR.dtd.$removeEmpty['span'] = false;
    config.justifyClasses = ['text-left', 'text-center', 'text-right', 'text-justify'];
    
    config.extraPlugins = 'bs3menu,fontawesome';

    config.contentsCss = ['https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'];
    //config.contentsCss = [config.contentsCss, 'view/bootstrap/css/bootstrap.min.css',CKEDITOR.getUrl(this.path + 'contents.css')];
};

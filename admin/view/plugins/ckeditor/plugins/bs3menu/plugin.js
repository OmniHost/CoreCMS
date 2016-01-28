CKEDITOR.config.bs3config_alertTypes = {
    'alert-danger': 'Danger',
    'alert-info': 'Info',
    'alert-success': 'Success',
    'alert-warning': 'Warning'
};

CKEDITOR.config.bs3config_defaultContent = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
CKEDITOR.plugins.add('bs3menu', {
    lang: 'en',
    requires: 'menu,widget',
    icons: 'bs3_bootstrapAlert,bs3_bootstrapButton,bs3_bootstrapGrid',
    defaults: {
        name: 'accordion',
        count: 3,
        activePanel: 1,
        multiExpand: false
    },
    init: function (editor) {
        var lang = editor.lang.bs3menu;


        var allowedFull = editor.config.bs3Buttons_allowedFull != undefined ? editor.config.bs3Buttons_allowedFull : 'p a div span h2 h3 h4 h5 h6 section article iframe object embed strong b i em cite pre blockquote small sub sup code ul ol li dl dt dd table thead tbody th tr td img caption mediawrapper br[href,src,target,width,height,colspan,span,alt,name,title,class,id,data-options]{text-align,float,margin}(*);';
        var allowedWidget = allowedFull;


        var buttonData = {};
        buttonData.bs3_bootstrapAlert = 'Bootstrap Alert';
        buttonData.bs3_bootstrapButton = 'Bootstrap Button';
        /* buttonData.bs3_bootstrapGrid2col = 'Bootstrap 2 Column';*/

        // Get the enabled menu items from editor.config
        if (editor.config.bs3Buttons != undefined) {
            var config = editor.config.bs3Buttons.split(',');
            var buttons = {};
            for (var i = 0; i < config.length; i++) {
                buttons[config[i]] = buttonData[config[i]];
            }
        } else {
            var buttons = buttonData;
        }

        // Build the list of menu items
        var items = {};
        for (var key in buttons) {
            items[key] = {
                label: buttons[key],
                command: key,
                group: 'bs3menu',
                icon: key
            }
        }
        items.bs3_bootstrapGrid2col = {
            label: 'Bootstrap 2 Column',
            command: 'bs3_bootstrapGrid2col',
            group: 'bs3menu',
            icon: 'bs3_bootstrapGrid'
        };
        items.bs3_bootstrapGrid3col = {
            label: 'Bootstrap 3 Column',
            command: 'bs3_bootstrapGrid3col',
            group: 'bs3menu',
            icon: 'bs3_bootstrapGrid'
        };
        items.bs3_bootstrapGrid4col = {
            label: 'Bootstrap 4 Column',
            command: 'bs3_bootstrapGrid4col',
            group: 'bs3menu',
            icon: 'bs3_bootstrapGrid'
        };
        items.bs3_bootstrapGrid6col = {
            label: 'Bootstrap 6 Column',
            command: 'bs3_bootstrapGrid6col',
            group: 'bs3menu',
            icon: 'bs3_bootstrapGrid'
        };
        items.bs3_bootstrapGridRightCol = {
            label: 'Bootstrap Right Column',
            command: 'bs3_bootstrapGridRightCol',
            group: 'bs3menu',
            icon: 'bs3_bootstrapGrid'
        };
        items.bs3_bootstrapGridLeftCol = {
            label: 'Bootstrap Left Column',
            command: 'bs3_bootstrapGridLeftCol',
            group: 'bs3menu',
            icon: 'bs3_bootstrapGrid'
        };

        //bs3_bootstrapGridRightCol
        //bs3_bootstrapGridLeftCol

        // Items must belong to a group.
        editor.addMenuGroup('bs3menu');
        editor.addMenuItems(items);

        editor.ui.add('bs3Menu', CKEDITOR.UI_MENUBUTTON, {
            label: 'Insert Boostrap Template',
            icon: this.path + 'icons/menu.png',
            onMenu: function () {
                // You can control the state of your commands live, every time
                // the menu is opened.

                var returnmenu = {
                    bs3_bootstrapGrid2col: editor.commands.bs3_bootstrapGrid2col.state,
                    bs3_bootstrapGrid3col: editor.commands.bs3_bootstrapGrid3col.state,
                    bs3_bootstrapGrid4col: editor.commands.bs3_bootstrapGrid4col.state,
                    bs3_bootstrapGrid6col: editor.commands.bs3_bootstrapGrid6col.state,
                    bs3_bootstrapGridRightCol: editor.commands.bs3_bootstrapGridRightCol.state,
                    bs3_bootstrapGridLeftCol: editor.commands.bs3_bootstrapGridLeftCol.state,
                    bs3_bootstrapAlert: editor.commands.bs3_bootstrapAlert.state,
                    bs3_bootstrapButton: editor.commands.bs3_bootstrapButton.state
                };
                return returnmenu;
            }
        });


        /** START ALERT **/
        editor.addCommand('openbs3_bootstrapAlert', new CKEDITOR.dialogCommand('bs3_bootstrapAlert'));
        editor.widgets.add('bs3_bootstrapAlert', {
            button: undefined,
            dialog: 'bs3_bootstrapAlert',
            template: '<div class="alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><div class="alert-text"><strong>Sample Title</strong>Sample Body</div></div>',
            editables: {
                alertBox: {
                    selector: '.alert-text',
                    allowedContent: allowedWidget
                },
            },
            allowedContent: allowedFull,
            data: function () {
                var newData = this.data,
                        oldData = this.oldData;


                if (oldData) {
                    var alertTypes = CKEDITOR.config.bs3config_alertTypes;
                    for (alertName in alertTypes) {
                        this.element.removeClass(alertName);
                    }
                }

                if (newData.type)
                    this.element.addClass(newData.type);



                if (oldData && newData.dismissable) {
                    this.element.getChild(0).removeClass('hidden');
                }
                if (oldData && !newData.dismissable) {
                    this.element.getChild(0).addClass('hidden');
                }

                // Save oldData.
                this.oldData = CKEDITOR.tools.copy(newData);
            },
            upcast: function (el, data) {
                if (el.name != 'div' || !el.hasClass('alert'))
                    return;


                var childrenArray = el.children,
                        alertText;



                if (!(alertText = childrenArray[ 1 ]).hasClass('alert-text'))
                    return;

                // Acceptable alert types
                var alertTypes = CKEDITOR.config.bs3config_alertTypes;
                if (el.classes == undefined)
                    el.classes = (el.attributes.class).split(" ");

                el.data = {};

                // Check alert types
                for (var i = 0; i < el.classes.length; i++) {

                    if (el.classes[i] != 'alert') {
                        for (alertName in alertTypes) {
                            if (el.classes[i] == alertName) {
                                el.data.type = alertName;
                            }
                        }
                    }
                }

                return el;
            },
            downcast: function (el) {
                return el;
            }

        });
        CKEDITOR.dialog.add('bs3_bootstrapAlert', this.path + 'dialogs/bs3_bootstrapAlert.js');
        /** END ALERT **/

        /** START BUTTON **/
        editor.addCommand('openbs3_bootstrapButton', new CKEDITOR.dialogCommand('bs3_bootstrapAlert'));
        CKEDITOR.dialog.add('bs3_bootstrapButton', this.path + 'dialogs/bs3_bootstrapButton.js');
        editor.widgets.add('bs3_bootstrapButton', {
            button: lang.buttonTitle,
            dialog: 'bs3_bootstrapButton',
            button:  undefined,
                    init: function () {

                    },
            template: '<a class="btn">' + '<span class="text"></span>' + '</a>',
            data: function () {
                var $el = jQuery(this.element.$);

                if (this.data.btntype) {
                    $el.removeClass('btn-link btn-default btn-primary btn-info btn-success btn-warning btn-danger').addClass(this.data.btntype);
                }

                if (this.data.btnsize) {
                    $el.removeClass('btn-xs btn-sm btn-lg btn-block').addClass(this.data.btnsize);
                }

                if (this.data.href) {
                    $el.attr('href', this.data.href);
                }

                if (this.data.target && this.data.target != '') {
                    $el.attr('target', this.data.target);
                }

                if (this.data.text) {
                    jQuery('.text', $el).text(this.data.text);
                }

                if (this.data.hasOwnProperty('bsiconleft')) {
                    jQuery('.bs-icon-left', $el).remove();
                    if (this.data.bsiconleft) {
                        $el.prepend('<span class="bs-icon-left glyphicon ' + this.data.bsiconleft + '"></span>');
                    }
                }

                if (this.data.hasOwnProperty('bsiconright')) {
                    jQuery('.bs-icon-right', $el).remove();
                    if (this.data.bsiconright) {
                        $el.append('<span class="bs-icon-right glyphicon ' + this.data.bsiconright + '"></span>');
                    }
                }

                if (this.data.hasOwnProperty('faiconleft')) {
                    jQuery('.fa-icon-left', $el).remove();
                    if (this.data.faiconleft) {
                        $el.prepend('<i class="fa fa-icon-left ' + this.data.faiconleft + '"></i>');
                    }
                }

                if (this.data.hasOwnProperty('faiconright')) {
                    jQuery('.fa-icon-right', $el).remove();
                    if (this.data.faiconright) {
                        $el.append('<i class="fa fa-icon-right ' + this.data.faiconright + '"></i>');
                    }
                }
            },
            requiredContent: 'a(btn)',
            upcast: function (element) {
                return element.name == 'a' && element.hasClass('btn');
            }
        });

        /** END BUTTON **/


        /** GRID **/

        editor.widgets.add('bs3_bootstrapGridLeftCol', {
            button: undefined,
            template:
                    '<div class="row two-col-left">' +
                    '<div class="col-md-3 col-sidebar"><p><img src="http://placehold.it/300x250&text=Image" class="img-responsive"/></p></div>' +
                    '<div class="col-md-9 col-main"><p>Content</p></div>' +
                    '</div>',
            editables: {
                col1: {
                    selector: '.col-sidebar',
                    allowedContent: allowedWidget
                },
                col2: {
                    selector: '.col-main',
                    allowedContent: allowedWidget
                }
            },
            allowedContent: allowedFull,
            upcast: function (element) {
                return element.name == 'div' && element.hasClass('two-col-right-left');
            }

        });

        editor.widgets.add('bs3_bootstrapGridRightCol', {
            button: undefined,
            template:
                    '<div class="row two-col-right">' +
                    '<div class="col-md-9 col-main"><p>Content</p></div>' +
                    '<div class="col-md-3 col-sidebar"><p><img src="http://placehold.it/300x250&text=Image" class="img-responsive" /></p></div>' +
                    '</div>',
            editables: {
                col1: {
                    selector: '.col-sidebar',
                    allowedContent: allowedWidget
                },
                col2: {
                    selector: '.col-main',
                    allowedContent: allowedWidget
                }
            },
            allowedContent: allowedFull,
            upcast: function (element) {
                return element.name == 'div' && element.hasClass('two-col-right');
            }

        });



        editor.widgets.add('bs3_bootstrapGrid2col', {
            button: undefined,
            template:
                    '<div class="row two-col">' +
                    '<div class="col-md-6 col-1"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '<div class="col-md-6 col-2"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '</div>',
            editables: {
                col1: {
                    selector: '.col-1',
                    allowedContent: allowedWidget
                },
                col2: {
                    selector: '.col-2',
                    allowedContent: allowedWidget
                }
            },
            allowedContent: allowedFull,
            upcast: function (element) {
                return element.name == 'div' && element.hasClass('two-col');
            }

        });
        editor.widgets.add('bs3_bootstrapGrid3col', {
            button: undefined,
            template:
                    '<div class="row three-col">' +
                    '<div class="col-md-4 col-1"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '<div class="col-md-4 col-2"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '<div class="col-md-4 col-3"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '</div>',
            editables: {
                col1: {
                    selector: '.col-1',
                    allowedContent: allowedWidget
                },
                col2: {
                    selector: '.col-2',
                    allowedContent: allowedWidget
                },
                col3: {
                    selector: '.col-3',
                    allowedContent: allowedWidget
                }
            },
            allowedContent: allowedFull,
            upcast: function (element) {
                return element.name == 'div' && element.hasClass('three-col');
            }

        });

        editor.widgets.add('bs3_bootstrapGrid4col', {
            button: undefined,
            template:
                    '<div class="row four-col">' +
                    '<div class="col-md-3 col-1"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '<div class="col-md-3 col-2"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '<div class="col-md-3 col-3"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '<div class="col-md-3 col-4"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '</div>',
            editables: {
                col1: {
                    selector: '.col-1',
                    allowedContent: allowedWidget
                },
                col2: {
                    selector: '.col-2',
                    allowedContent: allowedWidget
                },
                col3: {
                    selector: '.col-3',
                    allowedContent: allowedWidget
                },
                col4: {
                    selector: '.col-4',
                    allowedContent: allowedWidget
                }
            },
            allowedContent: allowedFull,
            upcast: function (element) {
                return element.name == 'div' && element.hasClass('four-col');
            }

        });

        editor.widgets.add('bs3_bootstrapGrid6col', {
            button: undefined,
            template:
                    '<div class="row six-col">' +
                    '<div class="col-md-2 col-1"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '<div class="col-md-2 col-2"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '<div class="col-md-2 col-3"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '<div class="col-md-2 col-4"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '<div class="col-md-2 col-5"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '<div class="col-md-2 col-6"><p><img src="http://placehold.it/500x280&text=Image" class="img-responsive" /></p><p>content</p></div>' +
                    '</div>',
            editables: {
                col1: {
                    selector: '.col-1',
                    allowedContent: allowedWidget
                },
                col2: {
                    selector: '.col-2',
                    allowedContent: allowedWidget
                },
                col3: {
                    selector: '.col-3',
                    allowedContent: allowedWidget
                },
                col4: {
                    selector: '.col-4',
                    allowedContent: allowedWidget
                },
                col5: {
                    selector: '.col-5',
                    allowedContent: allowedWidget
                },
                col6: {
                    selector: '.col-6',
                    allowedContent: allowedWidget
                }
            },
            allowedContent: allowedFull,
            upcast: function (element) {
                return element.name == 'div' && element.hasClass('six-col');
            }

        });




        /** Add Styles **/
        if (typeof editor.config.contentsCss == 'object') {
            editor.config.contentsCss.push(CKEDITOR.getUrl(this.path + 'contents.css'));
        } else {
            editor.config.contentsCss = [editor.config.contentsCss, CKEDITOR.getUrl(this.path + 'contents.css')];
        }
    }


});

CKEDITOR.on('dialogDefinition', function (ev) {
    var dialogName = ev.data.name;
    var dialogDefinition = ev.data.definition;

    if (dialogName == 'image' || dialogName == 'imageProperties') {
        var adv = dialogDefinition.getContents('advanced');
        var txtGenClass = adv.remove("txtGenClass");
        adv.add({
            type: 'select',
            multiple: 'true',
            /*size: 5,*/
            id: 'selClass',
            label: 'Add Bootstrap class',
            items: [['img'], ['img-responsive'], ['center-block'], ['img-rounded'], ['img-circle'], ['img-thumbnail']],
            'default': '',
            setup: function (a, b) {
                $(this.getInputElement().$).find('option').attr('selected', false);

                if (b.getAttribute("class")) {
                    var classes = b.getAttribute("class").split(" ");
                    for (var i = 0; i < classes.length; i++) {
                        $(this.getInputElement().$).find('option[value="' + classes[i] + '"]').attr('selected', 'selected');
                    }
                } else {
                    this.setValue("");
                }

            },
            commit: function (a, d) {
                //  console.log('commit');
                var opts = $(this.getInputElement().$).find('option:selected');
                if (opts.length) {
                    var imgclass = "";
                    for (var i = 0; i < opts.length; i++) {
                        imgclass += ' ' + opts[i].value;//$(this.getInputElement().$).find('option[value="' + classes[i] + '"]').attr('selected', 'selected');
                    }
                    d.setAttribute("class", imgclass);
                } else {
                    d.removeAttribute("class");
                }
            }
        });
    }

    if (dialogName == 'table' || dialogName == 'tableProperties') {

        var info = dialogDefinition.getContents('info');

        // Remove fields
        var cellSpacing = info.remove('txtCellSpace');
        var cellPadding = info.remove('txtCellPad');
        var border = info.remove('txtBorder');
        var width = info.remove('txtWidth');
        var height = info.remove('txtHeight');
        var align = info.remove('cmbAlign');

        dialogDefinition.removeContents('advanced');

        dialogDefinition.addContents({
            id: 'advanced',
            label: 'Advanced',
            accessKey: 'A',
            elements: [
                {
                    type: 'select',
                    id: 'selClass',
                    label: 'Select the table class',
                    items: [['table'], ['table-striped'], ['table-bordered'], ['table-hover'], ['table-condensed']],
                    'default': 'table',
                    multiple: 'true',
                    setup: function (a, b) {
                        /* this.setValue(a.getAttribute("class") ||
                         "")*/
                        $(this.getInputElement().$).find('option').attr('selected', false);

                        if (b.getAttribute("class")) {
                            var classes = b.getAttribute("class").split(" ");
                            for (var i = 0; i < classes.length; i++) {
                                $(this.getInputElement().$).find('option[value="' + classes[i] + '"]').attr('selected', 'selected');
                            }
                        } else {
                            this.setValue("");
                        }
                    },
                    commit: function (a, d) {
                        /* this.getValue() ? d.setAttribute("class", this.getValue()) : d.removeAttribute("class")*/
                        var opts = $(this.getInputElement().$).find('option:selected');
                        if (opts.length) {
                            var imgclass = "";
                            for (var i = 0; i < opts.length; i++) {
                                imgclass += ' ' + opts[i].value;//$(this.getInputElement().$).find('option[value="' + classes[i] + '"]').attr('selected', 'selected');
                            }
                            d.setAttribute("class", imgclass);
                        } else {
                            d.removeAttribute("class");
                        }
                    }
                }
            ]
        });
    }

});


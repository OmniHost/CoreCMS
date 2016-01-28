CKEDITOR.dialog.add('bs3_bootstrapAlert', function (editor) {

    var lang = editor.lang.bs3menu;

    var alertTypes = CKEDITOR.config.bs3config_alertTypes;

    var clientHeight = document.documentElement.clientHeight,
            alertTypesSelect = [],
            alertName;

    for (alertName in alertTypes) {
        alertTypesSelect.push([alertTypes[ alertName ], alertName]);
    }


    return {
        title: 'Edit Alert Type',
        minWidth: 200,
        minHeight: 100,
        contents: [
            {
                id: 'info',
                elements: [
                    {
                        id: 'type',
                        type: 'select',
                        label: 'Alert Type',
                        items: alertTypesSelect,
                        required: true,
                        default: 'alert-info',
                        validate: CKEDITOR.dialog.validate.notEmpty('Alert type required'),
                        setup: function (widget) {
                            var alertTypes = CKEDITOR.config.bs3config_alertTypes;
                            /*console.log(alertTypes);
                             for (var i = 0; i < alertTypes.length; i++) {
                             for (alertName in alertTypes) {
                             if (widget.element.hasClass(alertName)) {
                             console.log(alertName);
                             this.setValue(alertName);
                             }
                             }
                             }*/

                            //  console.log(widget.element.hasClass('alert-info'));
                            /*  for (var i = 0; i < widget.data.classes.length; i++) {
                             
                             if (widget.data.classes[i] != 'alert') {
                             for (alertName in alertTypes) {
                             if (el.classes[i] == alertName) {
                             console.log(alertName);
                             this.setValue(alertName);
                             }
                             }
                             }
                             }*/
                            if (widget.data.type != undefined) {
                                this.setValue(widget.data.type);
                            } else {

                                console.log(widget.data);
                                for (var atype in widget.data.classes) {
                                    if (atype != 'alert') {
                                        console.log(atype);
                                        if (alertTypes[atype] != undefined) {
                                            this.setValue(atype);
                                        }
                                    }
                                }
                            }
                        },
                        commit: function (widget) {
                            widget.setData('type', this.getValue());
                        }
                    },
                    {
                        id: 'dismissable',
                        type: 'checkbox',
                        label: 'Dismissable',
                        default: '',
                        setup: function (widget, el) {
                            this.setValue(widget.data);
                            //   console.log(widget.data);
                            if (widget.element.getChild(0).hasClass('hidden')) {
                                this.setValue(false);
                            } else {
                                this.setValue(true);
                            }
                        },
                        commit: function (widget) {

                            widget.setData('dismissable', this.getValue());
                        }
                    }
                ]
            }
        ]
    };
});
function getURLVar(key) {
    var value = [];

    var query = String(document.location).split('?');

    if (query[1]) {
        var part = query[1].split('&');

        for (i = 0; i < part.length; i++) {
            var data = part[i].split('=');

            if (data[0] && data[1]) {
                value[data[0]] = data[1];
            }
        }

        if (value[key]) {
            return value[key];
        } else {
            return '';
        }
    }
}

$(document).ready(function () {
    //Form Submit for IE Browser
    $('button[type=\'submit\']').on('click', function () {
        $("form[id*='form-']").submit();
    });

    // Highlight any found errors
    $('.text-danger').each(function () {
        var element = $(this).parent().parent();

        if (element.hasClass('form-group')) {
            element.addClass('has-error');
        }
    });



    //Autocomplete
    $('input[type=\'autocomplete\']').each(function () {

        var $this = $(this);
        //     console.log($(this).attr('data-url'));
        $(this).autoinput({
            'source': function (request, response) {

                $.ajax({
                    url: $this.attr('data-url') + '&filter_name=' + encodeURIComponent(request),
                    dataType: 'json',
                    success: function (json) {
                        response($.map(json, function (item) {
                            return {
                                label: item['name'],
                                value: item['id']
                            }
                        }));
                    }
                });
            },
            'select': function (item) {
                $this.val('');
                console.log(item);
                $('#' + $this.attr('data-target') + '-' + item['value']).remove();

                $('#' + $this.attr('data-target') ).append('<div class="list-group-item" id="' + $this.attr('data-target') + '-' + item['value'] + '"><i class="fa fa-minus-circle text-danger"></i> ' + item['label'] + '<input type="hidden" name="' + $this.attr('data-key') + '[]" value="' + item['value'] + '" /></div>');
            }
        });
    });


    $('.autocomplete-list').delegate('.fa-minus-circle,.btn-minus-circle', 'click', function () {
        $(this).closest('.list-group-item').remove();
    });


    $(document).on("click", ".btn-add-multitext", function(e) { 
        e.preventDefault();
        var val = $('#input-' + $(this).attr('data-target')).val();
        $('#input-' + $(this).attr('data-target')).val('');
        var idkey = Date.now() / 1000;
         $('#' + $(this).attr('data-target') ).append('<div class="list-group-item" id="' + $(this).attr('data-target') + '-' + idkey + '"><i class="fa fa-minus-circle text-danger"></i> ' + val + '<input type="hidden" name="' + $(this).attr('data-key') + '[]" value="' + val + '" /></div>');
    });




    // Image Manager
    $(document).delegate('a[data-toggle=\'image\']', 'click', function (e) {
        e.preventDefault();

        var element = this;

        $(element).popover({
            html: true,
            placement: 'right',
            trigger: 'manual',
            content: function () {
                return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
            }
        });

        $(element).popover('toggle');

        $('#button-image').on('click', function () {
            $('#modal-image').remove();

            $.ajax({
                url: 'index.php?p=common/filemanager&token=' + getURLVar('token') + '&target=' + $(element).parent().find('input').attr('id') + '&thumb=' + $(element).attr('id'),
                dataType: 'html',
                beforeSend: function () {
                    $('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
                    $('#button-image').prop('disabled', true);
                },
                complete: function () {
                    $('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
                    $('#button-image').prop('disabled', false);
                },
                success: function (html) {
                    $('body').append('<div id="modal-image" class="modal">' + html + '</div>');

                    $('#modal-image').modal('show');
                }
            });

            $(element).popover('hide');
        });

        $('#button-clear').on('click', function () {
            $(element).find('img').attr('src', $(element).find('img').attr('data-placeholder'));

            $(element).parent().find('input').attr('value', '');

            $(element).popover('hide');
        });
    });

    // tooltips on hover
    $('[data-toggle=\'tooltip\']').tooltip({container: 'body', html: true});

    // Makes tooltips work on ajax generated content
    $(document).ajaxStop(function () {
        $('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
    });

    //inline on ready calls
    $('.sidebar-menu a[href="' + location.href + '"]').parents('li').addClass('active');
    $.each(onReadyCallbacks, function (i, callback) {
        if (callback instanceof Function) {
            callback();
        }
    });

    if (typeof CKEDITOR != "undefined") {
        CKEDITOR.on('dialogDefinition', function (event) {
            var editor = event.editor;
            var dialogDefinition = event.data.definition;
            console.log(event.editor);
            var dialogName = event.data.name;
            var dialogDefinition = event.data.definition;
            if (dialogName == 'image') {
                // remove Upload tab
                dialogDefinition.removeContents('Upload');

                var tabCount = dialogDefinition.contents.length;
                for (var i = 0; i < tabCount; i++) {
                    var browseButton = dialogDefinition.contents[i].get('browse');

                    if (browseButton !== null) {
                        browseButton.hidden = false;
                        browseButton.onClick = function (dialog, i) {
                            editor._.filebrowserSe = this;
                            $('#modal-image').remove();

                            $.ajax({
                                url: 'index.php?p=common/filemanager&token=' + getURLVar('token') + '&ckeditor=' + editor.name,
                                dataType: 'html',
                                success: function (html) {
                                    $('body').append('<div id="modal-image" class="modal" style="z-index: 99991;">' + html + '</div>');
                                    $('#modal-image').modal('show');
                                }
                            });


                        }
                    }
                }
            }


        });

    }
});

// Autocomplete */
(function ($) {
    function AutoInput(element, options) {
        this.element = element;
        this.options = options;
        this.timer = null;
        this.items = new Array();

        $(element).attr('autocomplete', 'off');
        $(element).on('focus', $.proxy(this.focus, this));
        $(element).on('blur', $.proxy(this.blur, this));
        $(element).on('keydown', $.proxy(this.keydown, this));

        $(element).after('<ul class="dropdown-menu"></ul>');
        $(element).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));
    }

    AutoInput.prototype = {
        getElement: function () {
            return $(this.element);
        },
        focus: function () {
            this.request();
        },
        blur: function () {
            setTimeout(function (object) {
                object.hide();
            }, 200, this);
        },
        click: function (event) {
            event.preventDefault();

            value = $(event.target).parent().attr('data-value');

            if (value && this.items[value]) {
                this.options.select(this.items[value]);
            }
        },
        keydown: function (event) {
            switch (event.keyCode) {
                case 27: // escape
                    this.hide();
                    break;
                default:
                    this.request();
                    break;
            }
        },
        show: function () {
            var pos = $(this.element).position();
            console.log(pos);
            $(this.element).siblings('ul.dropdown-menu').css({
                left: pos.leftm,
                top: pos.top + $(this.element).outerHeight(),
                width: $(this.element).outerWidth()
            });

            $(this.element).siblings('ul.dropdown-menu').show();
        },
        hide: function () {
            $(this.element).siblings('ul.dropdown-menu').hide();
        },
        request: function () {
            clearTimeout(this.timer);

            this.timer = setTimeout(function (object) {
                object.options.source($(object.element).val(), $.proxy(object.response, object));
            }, 200, this);
        },
        response: function (json) {
            html = '';

            if (json.length) {
                for (i = 0; i < json.length; i++) {
                    this.items[json[i]['value']] = json[i];
                }

                for (i = 0; i < json.length; i++) {
                    html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
                }

            }

            if (html) {
                this.show();
            } else {
                this.hide();
            }

            $(this.element).siblings('ul.dropdown-menu').html(html);
        }
    };

    $.fn.autoinput = function (option) {
        return this.each(function () {
            var data = $(this).data('autoinput');

            if (!data) {
                data = new AutoInput(this, option);

                $(this).data('autoinput', data);
            }
        });
    }



})(window.jQuery);

if (!Date.now) {
    Date.now = function() { return new Date().getTime(); }
}
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

function confirmDelete(title, message, callback) {


    swal({
        title: title,
        text: message,
        type: "error",
        showCancelButton: true,
        confirmButtonColor: "#367fa9",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: true
    },
            callback);
}

function builderOn() {

    $('.builder-container-topwrapper').off('scroll');
    $(".builder-container-topwrapper").scroll(function () {
        $(this).closest('.builder-container-wrapper').find(".builder-container")
                .scrollLeft($(this).scrollLeft());
    });

    $('.builder-container').off('scroll');
    $(".builder-container").scroll(function () {
        $(this).closest('.builder-container-wrapper').find(".builder-container-topwrapper")
                .scrollLeft($(this).scrollLeft());
    });

}

$(document).ready(function () {


    //Range Sliders:
    $('input[type="range"]').each(function () {
        //  $(this).before('<span class="rangebefore">' + $(this).attr("min") + '</span>');
        $(this).after('<span class="rangeafter">' + $(this).val() + '</span>');
        $(this).on("input", function () {
            $(this).siblings(".rangeafter").text($(this).val());
        });
    });


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
                if ($this.attr("data-limit") == "1") {
                    $this.val(decodeEntities(item['label']));
                    $('#' + $this.attr('data-target')).val(item['value']);
                } else {
                    $this.val('');
                    console.log(item);
                    $('#' + $this.attr('data-target') + '-' + item['value']).remove();

                    $('#' + $this.attr('data-target')).append('<div class="list-group-item" id="' + $this.attr('data-target') + '-' + item['value'] + '"><div class="input-group"><span class="input-group-btn"><button class="btn btn-default btn-minus-circle" type="button"><i class="fa fa-minus-circle text-danger"></i></button></span><span class="form-control">' + item['label'] + '</span><input type="hidden" name="' + $this.attr('data-key') + '[]" value="' + item['value'] + '" /></div></div>');
                }
            }
        });
    });


    $('.autocomplete-list').delegate('.fa-minus-circle,.btn-minus-circle', 'click', function () {
        $(this).closest('.list-group-item').remove();
    });


    $(document).on("click", ".btn-add-multitext", function (e) {
        e.preventDefault();
        var val = $('#input-' + $(this).attr('data-target')).val();
        $('#input-' + $(this).attr('data-target')).val('');
        var idkey = Date.now() / 1000;
        $('#' + $(this).attr('data-target')).append('<div class="list-group-item" id="' + $(this).attr('data-target') + '-' + idkey + '"><i class="fa fa-minus-circle text-danger"></i> ' + val + '<input type="hidden" name="' + $('#input-' + $(this).attr('data-target')).attr('data-key') + '[]" value="' + val + '" /></div>');
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

        var imageManagerUrl;

        $('#button-image').on('click', function () {
            $('#modal-image').remove();

            var lstFolder = '';

            if (typeof (Storage) !== "undefined" && sessionStorage.getItem("lastFolder")) {
                lstFolder = sessionStorage.getItem("lastFolder");
            }

            //  if (!localStorage.getItem('lastFolder')) {

            imageManagerUrl = 'index.php?p=common/filemanager&token=' + getURLVar('token') + '&target=' + $(element).parent().find('input').attr('id') + '&thumb=' + $(element).attr('id') + '&directory=' + lstFolder;

            /*    } else {
             
             var url = localStorage.getItem('lastFolder');
             var url_splitted = url.split('&');
             var directory = url_splitted[2];
             imageManagerUrl = 'index.php?p=common/filemanager&token=' + getURLVar('token') + '&target=' + $(element).parent().find('input').attr('id') + '&thumb=' + $(element).attr('id') + '&' + directory;
             
             }*/

            $.ajax({
                url: imageManagerUrl,
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


    $(document).on("keyup", ".item-title-input", function () {
        var item_title = ($(this).val() == '') ? 'Item Title' : $(this).val();
        $(this).closest('.box-item').find('.item-title-display').text(item_title);
    });

    $(document).on("click", ".btn-delete-item", function (e) {



        e.preventDefault();
        var $box = $(this).closest('.box-item');

        confirmDelete("Are you Sure?", "", function () {
            $box.slideUp(1000, function (el) {
                $box.remove();
            });
        });
    });








    try {
        $('#pageitems').sortable({
            handle: '.box-title',
            placeholder: "ui-state-highlight",
            start: function () {
                $.each(CKEDITOR.instances, function (i) {
                    CKEDITOR.instances[i].destroy()
                });

            },
            stop: function () {
                $('.editorInput').each(function () {
                    EditorOn($(this).attr('id'));
                });
                BuilderOn();
            }
        });
        $("#pageitems").disableSelection();
    } catch (ERR) {
        //no sortables :-)
    }

    $('.editorInput').each(function () {
        EditorOn($(this).attr('id'));
    });


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
                    html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + decodeEntities(json[i]['label']) + '</a></li>';
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
    Date.now = function () {
        return new Date().getTime();
    }
}

var decodeEntities = (function () {
    // this prevents any overhead from creating the object each time
    var element = document.createElement('div');

    function decodeHTMLEntities(str) {
        if (str && typeof str === 'string') {
            // strip script/html tags
            str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
            str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
            element.innerHTML = str;
            str = element.textContent;
            element.textContent = '';
        }

        return str;
    }

    return decodeHTMLEntities;
})();


function slugify(text)
{
    return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
}


(function ($) {
    'use strict';

    /**
     * jQuery Redirect
     * @param {string} url - Url of the redirection
     * @param {Object} values - (optional) An object with the data to send. If not present will look for values as QueryString in the target url.
     * @param {string} method - (optional) The HTTP verb can be GET or POST (defaults to POST)
     * @param {string} target - (optional) The target of the form. "_blank" will open the url in a new window.
     */
    $.redirect = function (url, values, method, target) {
        method = (method && ["GET", "POST", "PUT", "DELETE"].indexOf(method.toUpperCase()) != -1) ? method.toUpperCase() : 'POST';

        if (!values) {
            var obj = $.parseUrl(url);
            url = obj.url;
            values = obj.params;
        }

        var form = $('<form>')
                .attr("method", method)
                .attr("action", url);

        if (target) {
            form.attr("target", target);
        }

        var submit = {}; //Create a symbol
        form[0][submit] = form[0].submit;
        iterateValues(values, [], form);
        $('body').append(form);
        form[0][submit]();
    };

    //Utility Functions
    /**
     * Url and QueryString Parser.
     * @param {string} url - a Url to parse.
     * @returns {object} an object with the parsed url with the following structure {url: URL, params:{ KEY: VALUE }}
     */
    $.parseUrl = function (url) {
        if (url.indexOf('?') === -1) {
            return {
                url: url,
                params: {}
            };
        }
        var parts = url.split('?'),
                query_string = parts[1],
                elems = query_string.split('&');
        url = parts[0];

        var i, pair, obj = {};
        for (i = 0; i < elems.length; i += 1) {
            pair = elems[i].split('=');
            obj[pair[0]] = pair[1];
        }

        return {
            url: url,
            params: obj
        };
    };

    //Private Functions
    var getInput = function (name, value, parent, array) {
        var parentString;
        if (parent.length > 0) {
            parentString = parent[0];
            var i;
            for (i = 1; i < parent.length; i += 1) {
                parentString += "[" + parent[i] + "]";
            }

            if (array) {
                name = parentString + "[]";
            } else {
                name = parentString + "[" + name + "]";
            }
        }

        return $("<input>").attr("type", "hidden")
                .attr("name", name)
                .attr("value", value);
    };

    var iterateValues = function (values, parent, form, array) {
        var i, iterateParent = [];
        for (i in values) {
            if (typeof values[i] === "object") {
                iterateParent = parent.slice();
                if (array) {
                    iterateParent.push('');
                } else {
                    iterateParent.push(i);
                }
                iterateValues(values[i], iterateParent, form, Array.isArray(values[i]));
            } else {
                form.append(getInput(i, values[i], parent, array));
            }
        }
    };
}(window.jQuery || window.Zepto || window.jqlite));
console.log("admin.js complete");
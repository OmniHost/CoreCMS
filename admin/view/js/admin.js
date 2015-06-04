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
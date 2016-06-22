$(document).ready(function () {
    $.each(onReadyCallbacks, function (i, callback) {
        if (callback instanceof Function) {
            callback();
        }
    });

    try {
        $(".vid-container").fitVids();
    } catch (err) {

    }

    $(document).on("click", "#img_cap_reload", function (e) {
        e.preventDefault();
        $('#captcha').attr('src', 'index.php?p=common/captcha&ts=' + new Date().getTime());
    });

    $("[rel='tooltip']").tooltip();


    $(window).scroll(function () {
        var scrollTop = $(window).scrollTop();
        if (scrollTop != 0)
            $('#oc_fetlb').stop().animate({'opacity': '0.2'}, 400);
        else
            $('#oc_fetlb').stop().animate({'opacity': '1'}, 400);
    });

    $('#oc_fetlb').hover(
            function (e) {
                var scrollTop = $(window).scrollTop();
                if (scrollTop != 0) {
                    $('#oc_fetlb').stop().animate({'opacity': '1'}, 400);
                }
            },
            function (e) {
                var scrollTop = $(window).scrollTop();
                if (scrollTop != 0) {
                    $('#oc_fetlb').stop().animate({'opacity': '0.2'}, 400);
                }
            }
    );
    
     $(document).delegate('.agree', 'click', function (e) {
            e.preventDefault();

            $('#modal-agree').remove();

            var element = this;

            $.ajax({
                url: $(element).attr('href'),
                type: 'get',
                dataType: 'html',
                success: function (data) {
                    html = '<div id="modal-agree" class="modal">';
                    html += '  <div class="modal-dialog">';
                    html += '    <div class="modal-content">';
                    html += '      <div class="modal-header">';
                    html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
                    html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
                    html += '      </div>';
                    html += '      <div class="modal-body">' + data + '</div>';
                    html += '    </div';
                    html += '  </div>';
                    html += '</div>';

                    $('body').append(html);

                    $('#modal-agree').modal('show');
                },
                error: function () {
                    alert("Page Not Found");
                }
            });
        });

});

(function ($) {
    function AutoInput(element, options) {
        this.element = element;
        this.options = options;
        this.timer = null;
        this.items = new Array();

        $(element).attr('autocomplete', 'false');
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


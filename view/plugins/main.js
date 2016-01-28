$(document).ready(function () {
    $.each(onReadyCallbacks, function (i, callback) {
        if (callback instanceof Function) {
            callback();
        }
    });
    
    try {
        $(".vid-container").fitVids();
    }catch(err){
        
    }
    
    $(document).on("click","#img_cap_reload", function(e) { 
        e.preventDefault();
        $('#captcha').attr('src', 'index.php?p=common/captcha&ts=' + new Date().getTime());
    });
    
    $("[rel='tooltip']").tooltip();
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
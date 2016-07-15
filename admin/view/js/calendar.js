/**
 * Calendar Class
 * Initializes the events of the calendar in the front-end.
 *
 * @author Gustavo Fernandes
 * @date 05/17/2016
 * @version 1.0.0
 */

$(document).ready(function () {

    /* Iniciando as cores disponíveis */
    $('input[type="radio"]').each(function () {
        var self = $(this),
                label = self.next(),
                clas = self.attr('class');
        label_text = label.text();
        label.remove();
        self.iCheck({
            radioClass: 'iradio_' + clas,
            insert: '<div class="icheck_line-icon"></div>' + label_text
        });
    });

    /* Iniciando calendário */
    setTimeout(function () {
        new Calendar();
    }, 500);


});

function Calendar() {

    this.options = {
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        timezone: false,
        editable: true,
        eventLimit: true, // allow "more" link when too many events
        dayClick: this.showNewEventScreen,
        events: this.loadEvents,
        lazyFetching: true,
        eventResize: this.resizeAndDragEditEvent,
        eventDrop: this.resizeAndDragEditEvent,
        eventClick: this.viewEvent
    };

    this.loadEventHandlers();


    $('#calendar').fullCalendar(this.options);

}
Calendar.prototype.constructor = Calendar;

Calendar.prototype.loadEventHandlers = function () {

    var obj = this;

    $("#formCalendar").submit(function (e) {
        e.preventDefault();
        obj.createEvent();
    });

    $("#evento_duracao").change(function () {
        $("#evento_hora_inicial").val('').parent().toggle();
        $("#evento_hora_final").val('').parent().toggle();
    });

    $("#evento_hora_inicial").focusout(function () {
        $("#evento_hora_final").val($(this).val());
    });

    $("#evento_data_inicial").focusout(function () {
        $("#evento_data_final").val($(this).val());
    });

    $("#eventoEditar").click(function () {
        $("#modalCalendar").modal('show');
    });

    $("#eventoExcluir").click(function () {
        console.log($(this).data('id'));
        obj.removeEvent($(this).data('id'));
    });

};

Calendar.prototype.showNewEventScreen = function (date, jsEvent, view) {

    var str = date.format();
    var lang = JSON.parse($("#javascriptLang").val());
    var initialDate = $.fullCalendar.moment(str);
    var finalDate = $.fullCalendar.moment(str).add(1, 'hours');
    $("#formCalendar")[0].reset();
    $('input[type="radio"]').iCheck('update');
    $("#evento_id").val(0);

    if (initialDate.hasTime()) {
        $("#evento_duracao").val(1);
        $("#evento_hora_inicial").val(initialDate.format('HH:mm')).parent().show();
        $("#evento_hora_final").val(finalDate.format('HH:mm')).parent().show();
    } else {
        $("#evento_hora_inicial").parent().hide();
        $("#evento_hora_final").parent().hide();
    }

    initialDate.stripTime();
    finalDate.stripTime();

    $("#evento_data_inicial").val(initialDate.format());
    $("#evento_data_final").val(finalDate.format());
    $("#modalCalendar").modal('show').find('.modal-title').text(lang.text_new_event);

};

Calendar.prototype.loadEvents = function (start, end, timezone, callback) {
    var token = $("#tokenVal").val();
    $.ajax({
        url: 'index.php?p=dashboard/calendar/getdata&token=' + token,
        type: 'POST',
        data: {
            start: start.format('YYYY-MM-DD'),
            end: end.format('YYYY-MM-DD')
        },
        success: function (data) {
            data = JSON.parse(data);
            if (data && data != null && data.length > 0) {
                for (var i = 0; i < data.length; i++) {
                    data[i].start = $.fullCalendar.moment(data[i].start);
                    data[i].end = $.fullCalendar.moment(data[i].end);
                }
                callback(data);
            } else {
                callback([]);
            }
        }
    });
};

Calendar.prototype.createEvent = function () {

    var button = $("#formCalendar").find('button[type="submit"]');
    var cor = $("input[name='evento_cor']:checked").val();
    var duracao = $("#evento_duracao").val();
    var lang = JSON.parse($("#javascriptLang").val());
    var event = {
        title: $("#evento_nome").val(),
        start: $("#evento_data_inicial").val(),
        end: $("#evento_data_final").val(),
        allDay: duracao == 0,
        backgroundColor: cor,
        borderColor: cor,
        repetir: $("#evento_repeticao").val(),
        desc: $("#evento_descricao").val(),
        id: $("#evento_id").val()
    };
    var token = $("#tokenVal").val();

    if (duracao == 1) {
        event.start += 'T' + $("#evento_hora_inicial").val() + ':00';
        event.end += 'T' + $("#evento_hora_final").val() + ':00';
    }

    var url = (event.id == 0 ? 'index.php?p=dashboard/calendar/adddata&token=' + token : 'index.php?p=dashboard/calendar/editdata&token=' + token);

    $.ajax({
        url: url,
        type: 'POST',
        data: event,
        beforeSend: function () {
            button.attr('disabled', 'disabled').html('<i class="fa fa-spin fa-spinner"></i>&nbsp; ' + lang.text_wait);
        },
        success: function (data) {
            if (data == 1) {
                $('#calendar').fullCalendar('refetchEvents');
                $(".modal").modal('hide');
                $("#formCalendar")[0].reset();
            } else {
                $("#eventoStatus").html('<div class="col-md-12 text-center text-danger"><small><b>' + lang.text_save_error_desc + '</b></small></div>').show('fast').delay(5000).hide('fast');
            }
            button.removeAttr('disabled').html(lang.button_save);
        },
        error: function (x) {
            console.log(x.responseText);
            $("#eventoStatus").html('<div class="col-md-12 text-center text-danger"><small><b>' + lang.text_save_error_desc + '</b></small></div>').show('fast').delay(5000).hide('fast');
            button.removeAttr('disabled').html(lang.button_save);
        }
    });

};

Calendar.prototype.viewEvent = function (calEvent, jsEvent, view) {
    var modal = $("#modalEvento");
    var body = modal.find('.modal-body');
    var str = '';
    var lang = JSON.parse($("#javascriptLang").val());
    var formatDate = 'YYYY-MMMM-DD';
    var formatTime = 'HH-mm';

    if (typeof lang.format_print_event_date != undefined) {
        formatDate = lang.format_print_event_date;
    }

    if (typeof lang.format_print_event_time != undefined) {
        formatTime = lang.format_print_event_time;
    }

    var elementRepeticao = $("#evento_repeticao");

    var repetir = elementRepeticao.children('option[value="' + calEvent.repetir + '"]').text();

    str += '<p class="lead">' + lang.text_read_event_title_name + ' <span style="color: ' + calEvent.backgroundColor + '"><b>' + calEvent.title + '</b></span></p>' +
            '<p><i>' + calEvent.start.format(formatDate) +
            (calEvent.start.hasTime() ? calEvent.start.format(formatTime) : '') +
            '</i></p>' +
            '<blockquote><p>' + (calEvent.desc ? calEvent.desc : 'Nada anotado...') + '</p></blockquote>' +
            '<p><u>' + lang.label_repeat + ':</u> <b>' + repetir.toLowerCase() + '</b>' +
            '</p>';

    body.html(str);
    modal.modal('show');

    $("#modalCalendar").find('.modal-title').text(lang.text_edit_event);
    $("#formCalendar")[0].reset();
    $("#eventoExcluir").attr('data-id', calEvent.id);
    $("#evento_id").val(calEvent.id);
    $("#evento_nome").val(calEvent.title);
    elementRepeticao.val(calEvent.repetir);
    $("#evento_descricao").val(calEvent.desc);
    $("input[name='evento_cor'][value='" + calEvent.backgroundColor + "']").iCheck('check');
    $('input').iCheck('update');
    $("#evento_data_inicial").val(calEvent.start.format('YYYY-MM-DD'));

    if (!calEvent.allDay) {
        $("#evento_duracao").val(1);
        $("#evento_hora_inicial").val(calEvent.start.format('HH:mm')).parent().show();
        if (calEvent.end != null) {
            $("#evento_data_final").val(calEvent.end.format('YYYY-MM-DD'));
            if (calEvent.end.hasTime()) {
                $("#evento_hora_final").val(calEvent.end.format('HH:mm')).parent().show();
            } else {
                $("#evento_hora_final").val(calEvent.start.add(1, 'hours').format('HH:mm')).parent().show();
            }
        } else {
            $("#evento_data_final").val(calEvent.start.format('YYYY-MM-DD'));
            $("#evento_hora_final").val(calEvent.start.format('HH:mm')).parent().show();
        }
    } else {
        $("#evento_data_final").val(calEvent.start.format('YYYY-MM-DD'));
        $("#evento_duracao").val(0);
    }

};

Calendar.prototype.resizeAndDragEditEvent = function (event, delta, revertFunc, jsEvent, ui, view) {
    var token = $("#tokenVal").val();
    $.ajax({
        url: 'index.php?p=dashboard/calendar/editdata&token=' + token,
        type: 'POST',
        data: {
            id: event.id,
            start: event.start.format(),
            end: (event.end != null ? event.end.format() : event.start.format()),
            title: event.title,
            desc: event.desc,
            backgroundColor: event.backgroundColor,
            repetir: event.repetir
        },
        success: function (data) {
            if (data != 1) {
                revertFunc();
            } else {
                if (delta.asSeconds() < 0 && event.repetir >= 1 && event.repetir <= 4) {
                    $("#calendar").fullCalendar('refetchEvents');
                }
            }
        },
        error: function (x) {
            console.log(x.responseText);
            revertFunc();
        }
    });
};

Calendar.prototype.removeEvent = function (id) {
    var token = $("#tokenVal").val();
    var lang = JSON.parse($("#javascriptLang").val());
    swal({
        title: lang.text_delete_title,
        text: lang.text_delete_desc,
        type: "warning",
        html: true,
        showCancelButton: true,
        closeOnConfirm: false,
        cancelButtonText: lang.text_delete_no,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: lang.text_delete_yes,
        showLoaderOnConfirm: true
    }, function () {

        $.ajax({
            url: 'index.php?p=dashboard/calendar/deldata&token=' + token,
            type: 'POST',
            data: {id: id},
            success: function (data) {
                if (data == 1) {
                    swal({
                        title: lang.text_delete_success_title,
                        type: "success",
                        text: lang.text_delete_success_desc,
                        html: true
                    });
                    $('.modal').modal('hide');
                    $("#calendar").fullCalendar('removeEvents', id);
                } else {
                    swal({
                        title: lang.text_delete_error_title,
                        type: "error",
                        text: lang.text_delete_error_desc,
                        html: true
                    });
                }
            },
            error: function (x) {
                console.log(x.responseText);
                swal({
                    title: lang.text_delete_error_title,
                    type: "error",
                    text: lang.text_delete_error_desc,
                    html: true
                });
            }
        });
    });
};
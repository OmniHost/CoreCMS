$(document).ready(function () {
    $.each(onReadyCallbacks, function (i, callback) {
        if (callback instanceof Function) {
            callback();
        }
    });
});
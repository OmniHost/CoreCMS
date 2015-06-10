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
    
});
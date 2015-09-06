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
        $('#captcha').attr('src', '?p=common/captcha&ts=' + new Date().getTime());
    });
    
});
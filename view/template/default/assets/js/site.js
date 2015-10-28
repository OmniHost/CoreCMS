
$(document).ready(function () {


    //change the integers below to match the height of your upper dive, which I called
    //banner.  Just add a 1 to the last number.  console.log($(window).scrollTop())
    //to figure out what the scroll position is when exactly you want to fix the nav
    //bar or div or whatever.  I stuck in the console.log for you.  Just remove when
    //you know the position.
    $(window).scroll(function () {

        //  console.log($(window).scrollTop());

        if ($(window).scrollTop() > 100) {
            $('#header').addClass('navbar-fixed-top');
            $('#scrolltotop').css('opacity', '1');
        }

        if ($(window).scrollTop() < 100) {
            $('#header').removeClass('navbar-fixed-top');
            $('#scrolltotop').css('opacity', '0');
        }
    });

//one page parallax scrolling
    $('#main-nav > ul > li a').click(function () {
        $("#main-nav > ul > li").each(function () {
            $(this).removeClass("current");
        });
        $(this).parent().addClass("current");
        var goTo = $($(this).attr('href')); // selects element that was clicked

        var offset = goTo.offset(); //grabs position of element
        $('body').animate({
            scrollTop: goTo.offset().top - 64
        }, 1000);

    });

    $('#scrolltotop').click(function () {
        var goTo = $($(this).attr('href')); // selects element that was clicked

        $('body').animate({
            scrollTop: 0
        }, 1000);

    });


    $("[rel='tooltip']").tooltip();



    //$("[rel^='lightbox']").prettyPhoto();


    /* Mobile */
    //$('#main-nav-wrap').prepend(' ');		
    $("#main-nav-trigger").on("click", function () {
        $("#main-nav").slideToggle();
    });

    // iPad
    var isiPad = navigator.userAgent.match(/iPad/i) != null;
    if (isiPad)
        $('#main-nav ul').addClass('no-transition');







    // Progress Load
    if ($(".progress > .progress-bar")[0]) {
        $('.progress > .progress-bar').waypoint(function () {
            $(this).each(function () {
                $(this).animate({
                    width: $(this).attr('rel') + "%"
                }, 500);
            });
        }, {
            triggerOnce: true,
            offset: 'bottom-in-view'
        });
    }
  

  document.getElementById("loading").style.display = "none";
  document.getElementById("body-content").style.display = "block";

});


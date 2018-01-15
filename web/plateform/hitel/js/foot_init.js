$(document).ready(function () {
    $('.bxslider').bxSlider({
        mode: 'horizontal',
        moveSlides: 1,
        slideMargin: 40,
        infiniteLoop: true,
        slideWidth: 660,
        minSlides: 3,
        maxSlides: 3,
        speed: 800,
    });
});
var t_;
var hide_ = function () {
    $(".hide_loading").hide(1000);
    clearInterval(t_);
};

$().ready(function () {
    $('[rel="tooltip"]').tooltip();

});

function rotateCard(btn) {
    var $card = $(btn).closest('.card-container');
    console.log($card);
    if ($card.hasClass('hover')) {
        $card.removeClass('hover');
    } else {
        $card.addClass('hover');
    }
}

function hide_menu() {
    if ($("#header > div > div.stickUpTop.isStuck.state_fixed").hasClass("state_display")) {
        $("#header > div > div.stickUpTop.isStuck.state_fixed").removeClass("state_display")
    } else {
        $("#header > div > div.stickUpTop.isStuck.state_fixed").addClass("state_display")
    }
}

$('.carousel').carousel({
    interval: 8000
});



// Instantiate the Bootstrap carousel
$('.multi-item-carousel').carousel({
    interval: 5000
});

// for every slide in carousel, copy the next slide's item in the slide.
// Do the same for the next, next item.
$('.multi-item-carousel .item').each(function () {
    var next = $(this).next();
    if (!next.length) {
        next = $(this).siblings(':first');
    }
    next.children(':first-child').clone().appendTo($(this));

    if (next.next().length > 0) {
        next.next().children(':first-child').clone().appendTo($(this));
    } else {
        $(this).siblings(':first').children(':first-child').clone().appendTo($(this));
    }
});
function hiddenFormular(e) {
    if (e != 0) {
        $(".menu-tap .item2").addClass("active");
        $(".menu-tap .item1").removeClass("active");

        $(".modal-content1 .hidden-reg").removeClass("hidden");
        $(".modal-content1 .hidden-login").addClass("hidden");
    } else {
        $(".menu-tap .item1").addClass("active");
        $(".menu-tap .item2").removeClass("active");

        $(".modal-content1 .hidden-reg").addClass("hidden");
        $(".modal-content1 .hidden-login").removeClass("hidden");
    }

}
$(document).ready(
        function () {
            $("#index .button-sub").animate(
                    {
                        right: "-10px"
                    },
            3000
                    );
            //$("#index .button-sub").animate({'top': '80%'},800);
        });
$("li.category-item-wrapper").click(
        function () {
//            $(".hide_loading").show();
//
//            t_ = setInterval("hide_()", 4000);
            // $.get(
            //       "google.cm",
            //       {},
            //       function(){
            //          $(".hide_loading").hide();
            //       }
            //    );

        }
);

var a;

function slideCover() {
    // $("#cover").slideUp(1000);
    console.log("fermeture");
    $("#cover").animate({opacity: 0}, 1000, function () {
        $("#cover").css("display", "none");
    });
    clearInterval(a);
}

$(document).ready(function () {
    a = setInterval("slideCover()", 4000);
    $(".fa-heart , .fa-share-square-o ").click(
            function () {
                var a = $(this);
                var val = parseInt(a.html());
                a.html(val + 1);
            }
    );
});

            
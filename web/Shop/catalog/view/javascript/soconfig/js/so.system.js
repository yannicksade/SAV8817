/* ------------------------------------------------------ /
	Magentech jQuery
	Created by Magentech
	All rights reserved.
	+----------------------------------------------------+
		TABLE OF CONTENTS
	+----------------------------------------------------+
	
	[1]		Quickview - jQuery elevateZoom
	[2]		Quantity plus minus - Product Detail
	[3]		Social Widgets Accounts - Slidebar
	[4]		Back To Top - Slidebar
	[5]		Language and Currency Dropdowns
	[6]		Preloading Screen
	[7]		Simple Blog - Magnific Popup
	[8]		Preloader Function
/ ---------------------------------------------------- */

/* Preloading Screen
 ========================================================*/
$(window).load(function() {
	// Animate loader off screen
	$('body').addClass('loaded');
});

/* Quickview - jQuery elevateZoom
 ========================================================*/
$(function ($) {
	$('.products-list').magnificPopup({
		delegate: '.quickview',
		preloader: true,
		tLoading: '',
		type: 'iframe',
		mainClass: 'my-mfp-zoom-in',
		removalDelay: 200,
		gallery: {enabled: true},
		
	});
});

$(function ($) {
    $('.products-list').magnificPopup({
        delegate: '.quickview',
        preloader: true,
        tLoading: '',
        type: 'iframe',
        mainClass: 'my-mfp-zoom-in',
        removalDelay: 200,
        gallery: {enabled: true},
        callbacks: {
        open: function() {
          // Will fire when this exact popup is opened
          // this - is Magnific Popup object
        },
        beforeOpen: function() {
            $('[data-toggle=\'tooltip\']').hover(
                function() {
                },
                function(e) {
                  $('[data-toggle=\'tooltip\']').tooltip('hide');
                }
            );
        },
        afterClose: function() {
            $('[data-toggle=\'tooltip\']').tooltip('hide');
        },
        
      }
    });
});

/* Quantity plus minus - Product Detail
 ========================================================*/
$(function ($) {
    "use strict";
    $.initQuantity = function ($control) {
        $control.each(function () {
            var $this = $(this),
                data = $this.data("inited-control"),
                $plus = $(".input-group-addon:last", $this),
                $minus = $(".input-group-addon:first", $this),
                $value = $(".form-control", $this);
            if (!data) {
                $control.attr("unselectable", "on").css({
                    "-moz-user-select": "none",
                    "-o-user-select": "none",
                    "-khtml-user-select": "none",
                    "-webkit-user-select": "none",
                    "-ms-user-select": "none",
                    "user-select": "none"
                }).bind("selectstart", function () {
                    return false
                });
                $plus.click(function () {
                    var val =
                        parseInt($value.val()) + 1;
                    $value.val(val);
                    return false
                });
                $minus.click(function () {
                    var val = parseInt($value.val()) - 1;
                    $value.val(val > 0 ? val : 1);
                    return false
                });
                $value.blur(function () {
                    var val = parseInt($value.val());
                    $value.val(val > 0 ? val : 1)
                })
            }
        })
    };
    $.initQuantity($(".quantity-control"));
    $.initSelect = function ($select) {
        $select.each(function () {
            var $this = $(this),
                data = $this.data("inited-select"),
                $value = $(".value", $this),
                $hidden = $(".input-hidden", $this),
                $items = $(".dropdown-menu li > a", $this);
            if (!data) {
                $items.click(function (e) {
                    if ($(this).closest(".sort-isotope").length >
                        0) e.preventDefault();
                    var data = $(this).attr("data-value"),
                        dataHTML = $(this).html();
                    $this.trigger("change", {
                        value: data,
                        html: dataHTML
                    });
                    $value.html(dataHTML);
                    if ($hidden.length) $hidden.val(data)
                });
                $this.data("inited-select", true)
            }
        })
    };
    $.initSelect($(".btn-select"))
});

/* Social Widgets Accounts - Slidebar
 ========================================================*/
$(function ($) {
    "use strict";
    var socialItems = $('.social-widgets .items .item');
    var counter = 0;
    socialItems.each(function () {
        counter++;
        var itemclass = "item-0" + counter;
        $(this).addClass(itemclass)
    });
});

$(function ($) {
    "use strict";
    $(".social-widgets .item").each(function () {
        var $this = $(this),
            timer;
        $this.on("mouseenter", function () {
            var $this = $(this);
            if (timer) clearTimeout(timer);
            timer = setTimeout(function () {
                $this.addClass("active")
            }, 200)
        }).on("mouseleave", function () {
            var $this = $(this);
            if (timer) clearTimeout(timer);
            timer = setTimeout(function () {
                $this.removeClass("active")
            }, 100)
        }).on("click", function (e) {
            e.preventDefault()
        })
    })
});


$(function ($) {
    "use strict";
    var loadmap = $(".social-widgets .item a");
    loadmap.click(function (e) {
        e.preventDefault()
    });
    loadmap.hover(function (e) {
        if (!$(this).parent().hasClass("load")) {
            var loadcontainer = $(this).parent().find(".loading");
            $.ajax({
                url: $(this).attr("href"),
                cache: false,
                success: function (data) {
                    setTimeout(function () {
                        loadcontainer.html(data)
                    }, 1000)
                }
            });
            $(this).parent().addClass("load")
        }
    })
});

/* Back To Top - Slidebar
 ========================================================*/
$(function ($) {
    "use strict";
    /*Back to top */
	$(".back-to-top").addClass("hidden-top");
		$(window).scroll(function () {
		if ($(this).scrollTop() === 0) {
			$(".back-to-top").addClass("hidden-top")
		} else {
			$(".back-to-top").removeClass("hidden-top")
		}
	});

	$('.back-to-top').click(function () {
		$('body,html').animate({scrollTop:0}, 1200);
		return false;
	});	
	
	// Hide tooltip when clicking on it
   /* var hasTooltip = $("[data-toggle='tooltip']").tooltip();
	hasTooltip.on('click', function () {
		    $(this).tooltip('hide')
	});*/
});

/* Language and Currency Dropdowns
 ========================================================*/
$(document).ready(function(){
	$screensize = $(window).width();
	if ($screensize > 1300) {
		$('#form-currency, #form-language, #my_account').hover(
			function(){$(this).find('.btn-group').toggleClass('open');}
		)
	}
	
	// Product detial reviews button
	$(".reviews_button,.write_review_button").click(function (){
        var tabTop = $(".producttab").offset().top;
        $("html, body").animate({ scrollTop:tabTop }, 1000);
    });
   
    // Resonsive Sidebar aside
	$(".open-sidebar").click(function(e){
        e.preventDefault();
        $(".sidebar-overlay").toggleClass("show");
        $(".sidebar-offcanvas").toggleClass("active");
    });
      
    $(".sidebar-overlay").click(function(e){
        e.preventDefault();
        $(".sidebar-overlay").toggleClass("show");
        $(".sidebar-offcanvas").toggleClass("active");
    });
    $('#close-sidebar').click(function() {
        $('.sidebar-overlay').removeClass('show');
        $('.sidebar-offcanvas').removeClass('active');
        
    }); 
});

	
	
/* Simple Blog - Magnific Popup
 ========================================================*/
$(function ($) {
	 $('.image-popup').magnificPopup({
	  type: 'image',
	  closeOnContentClick: true,
	  image: {
		verticalFit: false
	  }
	});
	
	$('.blog-listitem').magnificPopup({
	  delegate: '.popup-gallery',
	  type: 'image',
	  tLoading: 'Loading image #%curr%...',
	  mainClass: 'mfp-img-mobile',
	  gallery: {
		enabled: true,
		navigateByImgClick: true,
		preload: [0,1] // Will preload 0 - before current, and 1 after the current image
	  },
	  image: {
		tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
		titleSrc: function(item) {
		  return item.el.attr('title') ;
		}
	  }
	});
});	

/* preloader Function
 ========================================================*/
$(function ($) {
	// Preloader
    if (typeof so_preloader === 'undefined') {
        so_preloader = '';
    }
    if (so_preloader.length == 0) {
            if ($('.so-loader-with-logo').length > 0) {
                move();
            }
            setTimeout(function () {
              $('.so-pre-loader').fadeOut();
            }, 2000);
       
    } // has preloader
    //preloader Function
	
	function move() {
		var elem = document.getElementById("line-load");
		var width = 1;
		var id = setInterval(frame, 10);
		
		function frame() {
			if (width >= 100) {
				clearInterval(id);
			} else {
				width++;
				elem.style.width = width + '%';
				
			}
		   $('.so-pre-loader').find('.run-number').html(width);
		}
	}
});	




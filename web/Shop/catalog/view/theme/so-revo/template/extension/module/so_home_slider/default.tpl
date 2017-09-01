
<div id="so-slideshow" class="module sohomepage-slider">

<div class="modcontent">
	<div id="sohomepage-slider1">
		 <div class="so-homeslider sohomeslider-inner-1 owl2-carousel owl2-theme owl2-loaded">
				<div class="item ">
                    <a href="#" title="Slider 1-1" target="_blank">
                    <img class="responsive" src="http://localhost/vegaMarket2.3.0.2/image/cache/catalog/demo/slideshow/home1/slide1-650x510.jpg" alt="Slider 1-1">
                    </a>
					<div class="sohomeslider-description">
                        <span class="title-slider image-sl11 pos-left font-ct"><font><font> La liste d'achats</font></font></span> 
                        <div class="text pos-right text-sl11 font-ct">
                            <h3 class="tilte modtitle-sl11  title-active"><font><font>5 Regardez nous aimons </font></font><br><font><font>ce mois-ci</font></font></h3>
                            <a class="des des-sl11 des-active" href="#"><i class="fa fa-caret-right"></i><font><font>Voir plus</font></font></a> 
                        </div>
					</div>
				</div>
				<div class="item ">
                    <a href="#" title="Slider 1-3" target="_blank">
                    <img class="responsive" src="http://localhost/vegaMarket2.3.0.2/image/cache/catalog/demo/slideshow/home1/slide3-650x510.jpg" alt="Slider 1-3">
                    </a>
					<div class="sohomeslider-description">
                        <span class="title-slider image-sl11 pos-left font-ct"><font><font> Iphone 6 plus </font></font></span>
                        <div class="text pos-right text-sl11 font-ct">
                            <h3 class="tilte modtitle-sl11 "><font><font>5 Regardez nous aimons </font></font><br><font><font>ce mois-ci</font></font></h3>
                            <a class="des des-sl11" href="#"><i class="fa fa-caret-right"></i><font><font>Voir plus</font></font></a> 
                        </div>
					</div>
				</div>
				<div class="item ">
                    <a href="#" title="Slider 1-2" target="_blank">
                    <img class="responsive" src="http://localhost/vegaMarket2.3.0.2/image/cache/catalog/demo/slideshow/home1/slide2-650x510.jpg" alt="Slider 1-2">
                    </a>
					<div class="sohomeslider-description">
                        <div class="text pos-left text-sl12"> 
                            <a href="#" class="des des-sl11 des-active"><i class="fa fa-caret-right"></i><font><font>Voir plus</font></font></a> 
                        </div>
					</div>
				</div>
		</div>
		<script type="text/javascript">
			var owl = $(".sohomeslider-inner-1");
			var total_item = 3;
			function customCenter() {
				$(".owl2-item.active .item .sohomeslider-description .image ").addClass("img-active");
				$(".owl2-item.active .item .sohomeslider-description .text .tilte ").addClass("title-active");
				$(".owl2-item.active .item .sohomeslider-description .text h4 ").addClass("h4-active");
				$(".owl2-item.active .item .sohomeslider-description .text .des").addClass("des-active");
			}
			function customPager() {
				$(".owl2-item.active .item .sohomeslider-description .image ").addClass("img-active");
				$(".owl2-item.active .item .sohomeslider-description .text .tilte ").addClass("title-active");
				$(".owl2-item.active .item .sohomeslider-description .text h4 ").addClass("h4-active");
				$(".owl2-item.active .item .sohomeslider-description .text .des").addClass("des-active");
			}
                $(".sohomeslider-inner-1").owlCarousel2({
                        animateOut: 'fadeOut',
                        animateIn: 'fadeIn',
                        autoplay: true,
                        autoplayTimeout: 5000,
                        autoplaySpeed:  1000,
                        smartSpeed: 500,
                        autoplayHoverPause: true,
                        startPosition: 0,
                        mouseDrag:  true,
                        touchDrag: true,
                        dots: true,
                        autoWidth: false,
                        margin: 30,
                                            dotClass: "owl2-dot",
                        dotsClass: "owl2-dots",
                        // center:false,
                        loop: true,
                        navText: ["", ""],
                        navClass: ["owl2-prev", "owl2-next"],
                        responsive: {
                        0:{ items: 1,
                            nav: total_item <= 1 ? false : ((false) ? true: false),
                        },
                        480:{ items: 1,
                            nav: total_item <= 1 ? false : ((false) ? true: false),
                        },
                        768:{ items: 1,
                            nav: total_item <= 1 ? false : ((false) ? true: false),
                        },
                        992:{ items: 1,
                            nav: total_item <= 1 ? false : ((false) ? true: false),
                        },
                        1200:{ items: 1,
                            nav: total_item <= 1 ? false : ((false) ? true: false),
                        }
                    },
                    onInitialized : customPager,
                    onTranslated  : customCenter,
                });
	</script>
	</div>
</div> <!--/.modcontent-->
  <div class="loader-mod-box"></div>
</div> <!--/.module-->

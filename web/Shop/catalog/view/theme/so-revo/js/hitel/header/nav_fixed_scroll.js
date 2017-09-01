jQuery(document).ready(function () {
	var $window = $(window);
	function check_if_in_view1() {
	  var window_top_position = $window.scrollTop(); // vaut 0 au debut et augmente avec la scroll
	  if(window_top_position >= 400){
	  	$('.typeheader-1 .logo_fixed').show(500);
	  	$('.header-right ul li:nth-child(1)').show(); // ici nous avons le premier li qui represente le boutton de menu
	  	$('.hide-show-scroll').addClass('animated fadeInDown');
	  	$('.hide-show-scroll').addClass('scroll');
	  }
	  else{
	  	$('.typeheader-1 .logo_fixed').hide();
	  	$('.header-center').removeClass('navbar navbar-default navbar-fixed-top animated fadeInDown');
	  	$('.header-right ul li:nth-child(1)').hide();
	  	$('.hide-show-scroll').removeClass('animated fadeInDown');
	  	$('.hide-show-scroll').removeClass('scroll');
	  }
	}

	$window.on('scroll resize', check_if_in_view1);
	$window.trigger('scroll');
	
/*  block qui affiche et cache un le nom et le code de la boutique de la header-top  */
    $('#show-boutique').click(function(){
    	if($('.hide-show-scroll').hasClass('scroll')){
  			if( $('.header-top').hasClass('navbar navbar-default navbar-fixed-top animated fadeInDown')){
		    	$('.header-top').hide();
		    	$('.header-top').removeClass('navbar navbar-default navbar-fixed-top animated fadeInDown');	
		        $('#show-boutique').html('<span id="iconeHideBtq" class="glyphicon glyphicon-eye-open"></span>');
		    }
		    else{
		    	$('.header-top').show();
		        $('.header-top').addClass('navbar navbar-default navbar-fixed-top animated fadeInDown');
		        $('#show-boutique').html('<span id="iconeHideBtq" class="glyphicon glyphicon-remove"></span>');
		    }
    	}
    	else{
		    if( $('.header-top').is(':visible')){
		        $('.header-top').hide();
		        $('#show-boutique').html('<span id="iconeHideBtq" class="glyphicon glyphicon-eye-open"></span>');
		    }
		    else{
		        $('.header-top').show();
		        $('#show-boutique').html('<span id="iconeHideBtq" class="glyphicon glyphicon-remove"></span>');
		    }
		}
    });
/* end block */
	/* masque et afficher le vertical-wrapper au mouvement de la souris */
  	$('.header-bottom-left.menu-vertical').mouseover(function(){
  		$('.container-megamenu.vertical .vertical-wrapper .container').show();
  	});
  	$('.header-bottom-left.menu-vertical').mouseout(function(){
  		if($('.container-megamenu.vertical .vertical-wrapper .container').is(':visible') && !($('.vertical-wrapper').hasClass('so-vertical-active'))){
  			$('.container-megamenu.vertical .vertical-wrapper .container').hide();
  		}
  	});

  	$('#remove-megamenu').click(function() {
        $('.header-center').removeClass('navbar navbar-default navbar-fixed-top animated fadeInDown');
        return false;
    });		

	$("#show-megamenuHide").click(function () { // le bouton menu qu is'affiche dans le header-bottom quand on scroll sur la page
		if($('.megamenu-wrapper').hasClass('so-megamenu-active')){
			$('.megamenu-wrapper').removeClass('so-megamenu-active');
			$('.header-center').removeClass('navbar navbar-default navbar-fixed-top');
		}
		else{
			$('.megamenu-wrapper').addClass('so-megamenu-active');
			if(window.outerWidth>1006){
				$('.header-center').addClass('navbar navbar-default navbar-fixed-top animated fadeInDown');			
			}
		}
	}); 

	/* sous menu */

	  	$('.header-bottom-left.menu-vertical ul.megamenu li.item-vertical').mouseover(function(){
	  	if(!($('.vertical-wrapper ').hasClass('so-vertical-active')))
  			$(this).find(".sub-menu").show();
	  	});
	  	$('.header-bottom-left.menu-vertical ul.megamenu li.item-vertical').mouseout(function(){
	  		if($(this).find(".sub-menu").is(':visible') && !($('.vertical-wrapper ').hasClass('so-vertical-active'))){
	  			$(this).find(".sub-menu").hide();
	  		}
	  	});

});
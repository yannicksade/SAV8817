var isMobile=false;$(document).ready(function(){wishlistBtn();compareBtn();mobileMenu();$(window).resize(function(){mobileMenu();});featuredProductCarousel();});function wishlistBtn(){var wishlist_lnk=$('header .wishlist-link');var wishlist_div=$('header .wishlist-button');if(wishlist_div&&wishlist_lnk){wishlist_lnk.appendTo(wishlist_div);}}
function compareBtn(){var compare_lnk=$('header .compare-form');var compare_div=$('header .compare-button');if(compare_div&&compare_lnk){compare_lnk.appendTo(compare_div);}}
function mobileMenu(){var menu_mobile;var head_login;var languages;var currencies;var search;var wishlist;var compare;var cart;var mega_menu_container=$('header .top_menu').parent();var top_menu=$('header .top_menu > .menu');if($(document).width()<=1199&&!isMobile){if(top_menu.length){head_login=$('#header-login');languages=$('#languages-block-top');currencies=$('#currencies-block-top');search=$('#tmsearch');wishlist=$('header .wishlist-button .wishlist-link');compare=$('header .compare-button .compare-form');cart=$('header .shopping_cart');top_menu.append('<li class="mobile-items"></li>');menu_mobile=top_menu.find('.mobile-items');if(languages.length){languages.parent().addClass('languages');languages.appendTo(menu_mobile);}
if(currencies.length){currencies.parent().addClass('currencies');currencies.appendTo(menu_mobile);}
if(head_login.length){head_login.parent().addClass('header-login');head_login.appendTo(mega_menu_container);}
if(search.length){search.parent().addClass('search');search.appendTo(mega_menu_container);}
if(wishlist.length){wishlist.parent().addClass('wishlist');wishlist.appendTo(mega_menu_container);}
if(compare.length){compare.parent().addClass('compare');compare.appendTo(mega_menu_container);}
if(cart.length){cart.parent().addClass('cart');cart.appendTo(mega_menu_container);}
isMobile=true;}}else if($(document).width()>1199&&isMobile){menu_mobile=top_menu.find('.mobile-items');languages=menu_mobile.find('#languages-block-top');currencies=menu_mobile.find('#currencies-block-top');head_login=mega_menu_container.find('#header-login');search=mega_menu_container.find('#tmsearch');wishlist=mega_menu_container.find('.wishlist-link');compare=mega_menu_container.find('.compare-form');cart=mega_menu_container.find('.shopping_cart');if(languages.length){languages.appendTo($('header .languages'));}
if(currencies.length){currencies.appendTo($('header .currencies'));}
if(head_login.length){head_login.appendTo($('header .header-login'));}
if(search.length){search.appendTo($('header .search'));}
if(wishlist.length){wishlist.appendTo($('header .wishlist'));}
if(compare.length){compare.appendTo($('header .compare'));}
if(cart.length){cart.appendTo($('header .cart'));}
menu_mobile.remove();isMobile=false;}}
function featuredProductCarousel(){countItemsFeatured();var featuredCarousel=$('.featured-product-carousel #homefeatured');if(featuredCarousel.length&&!!$.prototype.bxSlider){featured_slider=featuredCarousel.bxSlider({minSlides:featured_carousel_items,maxSlides:featured_carousel_items,slideWidth:500,slideMargin:30,pager:false,nextText:'',prevText:'',moveSlides:1,infiniteLoop:true,hideControlOnEnd:true,responsive:true,useCSS:false,autoHover:false,speed:500,pause:3000,controls:true,autoControls:false});}
$(window).resize(function(){if(featuredCarousel.length){resizeCarouselFeatured()}});}
function resizeCarouselFeatured(){countItemsFeatured();featured_slider.reloadSlider({minSlides:featured_carousel_items,maxSlides:featured_carousel_items,slideWidth:500,slideMargin:30,pager:false,nextText:'',prevText:'',moveSlides:1,infiniteLoop:true,hideControlOnEnd:true,responsive:true,useCSS:false,autoHover:false,speed:500,pause:3000,controls:true,autoControls:false});}
function countItemsFeatured(){var featured=$('.featured-product-carousel');if(featured.width()<400){featured_carousel_items=1;}
if(featured.width()>=400){featured_carousel_items=2;}
if(featured.width()>=640){featured_carousel_items=3;}
if(featured.width()>=990){featured_carousel_items=4;}
if(featured.width()>=1880){featured_carousel_items=6;}}
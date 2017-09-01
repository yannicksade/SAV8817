<div class="module <?php echo $direction_class?> <?php echo $class_suffix; ?>">
	<?php if($disp_title_module) { ?>
		<h3 class="modtitle"><?php echo $head_name; ?></h3>
	<?php } ?>

<?php if($pre_text != '')
	{
?>
	<div class="form-group">
		<?php echo html_entity_decode($pre_text);?>
	</div>
<?php
	}
?>
<div class="modcontent">
	<?php if (!empty($products)) {
	$count_item = count($products);
	$cls_btn_page = ($button_page == 'top') ? 'buttom-type1':'button-type2';
	$btn_type 	  = ($button_page == 'top') ? 'button-type1':'button-type2';
	$suffix = rand() . time();
	$tag_id = 'sp_extra_slider_' . $suffix;
	$class_respl = 'preset00-'.$nb_column0.' preset01-'.$nb_column1.' preset02-'.$nb_column2.' preset03-'.$nb_column3.' preset04-'.$nb_column4;
	$btn_prev = ($button_page == 'top') ? '&#171;':'&#139;';
	$btn_next = ($button_page == 'top') ? '&#187;':'&#155;';
	$i = 0;
	?>
	<div id="<?php echo $tag_id ; ?>"
		 class="so-extraslider <?php echo $cls_btn_page; ?> <?php echo $class_respl; ?> <?php echo $btn_type; ?> ">
		<!-- Begin extraslider-inner -->

	<div class="extraslider-inner" data-effect="<?php echo $effect; ?>">
			<?php  foreach ($products as $product) {
			$i++;
			?>
			<?php if ($i % $nb_rows == 1 || $nb_rows == 1) { ?>
			<div class="item ">
				<?php } ?>
				<div class="item-wrap <?php echo $products_style; ?>">
					<div class="item-wrap-inner">
							<div class="item-image">
								<div class="item-img-info">
								<?php if ($product['special'] && $display_sale) : ?>
									<span class="label label-sale"><?php echo $objlang->get('text_sale'); ?></span>
								<?php endif; ?>
								<?php if ($product['productNew'] && $display_new) : ?>
									<span class="label label-new"><?php echo $objlang->get('text_new'); ?></span>
								<?php endif; ?>
									<?php if($product_image_num ==2){?>
										<img src="<?php echo $product['thumb']?>" class="img-thumb1" alt="<?php echo $product['nameFull'] ?>">
										<img src="<?php echo $product['thumb2']?>" class="img-thumb2" alt="<?php echo $product['nameFull'] ?>">
									<?php }else{?>
										<img src="<?php echo $product['thumb']?>" alt="<?php echo $product['nameFull'] ?>">
									<?php }?>
									<?php if ($display_readmore_link == 1 && $products_style == 'style1' || $display_readmore_link == 1 &&  $products_style == 'style2') { ?>
									<a class="quick-view" href="<?php echo $product['href'];?>" title="<?php echo $product['nameFull'];?>" target="<?php echo $item_link_target;?>" >
										<span><?php echo $readmore_text ; ?></span>
									</a>
									<?php } ?>
								</div>
							</div>
						<?php if($display_title == 1 || $display_description == 1 || $display_price == 1 || $display_addtocart == 1 || $display_wishlist == 1 || $display_compare == 1 || $display_readmore_link == 1){ ?>
							<div class="item-info">
								<?php if($display_title) { ?>
									<div class="item-title">
										<a href="<?php echo $product['href'];?>" target="<?php echo $item_link_target;?>"
										   title="<?php echo $product['nameFull']; ?>"  >
											<?php echo html_entity_decode($product['name']); ?>
										</a>
									</div>
								<?php } ?>

								<?php if($display_rating){
										if (!empty($product['rating']))
										{
								?>
								<div class="rating">
									<?php for ($k = 1; $k <= 5; $k++) { ?>
										<?php if ($product['rating'] < $k) { ?>
										<span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
										<?php } else { ?>
										<span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>
										<?php } ?>
									<?php } ?>
								</div>
								<?php }else{ ?>
								<div class="rating">
									<?php for ($j = 1; $j <= 5; $j++) { ?>
									<span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
									<?php } ?>
								</div>
								<?php 	}
									}
								?>

								<?php if($display_description == 1 || $display_price == 1 || $display_addtocart == 1 || $display_wishlist == 1 || $display_compare == 1 || $display_readmore_link == 1) { ?>
									<!-- Begin item-content -->
									<div class="item-content">
										<?php if($display_description) { ?>
											<div class="item-des">
												<?php echo html_entity_decode($product['description']); ?>
											</div>
										<?php } ?>

										<?php if($display_price) { ?>
											<div  class="content_price">
												<?php if (!$product['special']) { ?>
												<span class="price product-price">
													<?php echo $product['price']; ?>
												</span>
												<?php } else { ?>
													<span class="old-price product-price"><?php echo $product['special']; ?></span>&nbsp;&nbsp;
													<span class="price-old"><?php echo $product['price']; ?></span>&nbsp;
												<?php } ?>
												<?php if ($product['tax']) { ?>
													<span class="price-percent-reduction"><?php echo $objlang->get('text_tax'); ?> <?php echo $product['tax']; ?></span>
												<?php } ?>
											</div>
										<?php } ?>

										<?php if($display_addtocart || $display_readmore_link || $display_wishlist || $display_compare) { ?>
											<div class="button-container">
												<?php if ($display_addtocart == 1) { ?>
												<button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-cart"></i>
													<?php if($nb_column0 != 6) { ?>
													<span class="hidden-xs hidden-sm hidden-md"><?php echo $objlang->get('button_cart'); ?></span>
													<?php } ?>
												</button>
												<?php } ?>
												<?php if ($display_wishlist == 1) { ?>
												<button type="button" data-toggle="tooltip" title="<?php echo $objlang->get('button_wishlist'); ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart"></i></button>
												<?php } ?>
												<?php if ($display_compare == 1) { ?>
												<button type="button" data-toggle="tooltip" title="<?php echo $objlang->get('button_compare'); ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button>
												<?php } ?>
											</div>
										<?php } ?>

										<?php if ($display_readmore_link == 1 && $products_style != 'style1' && $products_style !='style2') { ?>
										<div class="item-readmore">
											<a href="<?php echo $product['href'];?>" title="<?php echo $product['name']; ?>" target="<?php echo $item_link_target;?>" >
												<?php echo $readmore_text ; ?>
											</a>
										</div>
										<?php } ?>

									</div>
									<!-- End item-content -->
								<?php } ?>
							</div>
							<!-- End item-info -->
						<?php } ?>

					</div>
					<!-- End item-wrap-inner -->
				</div>
				<!-- End item-wrap -->
				<?php if ($i % $nb_rows == 0 || $i == $count_item) { ?>
			</div>
				<?php } ?>
			<?php } ?>

		</div>
		<!--End extraslider-inner -->
	</div>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function ($) {
			;(function (element) {
				var $element = $(element),
						$extraslider = $(".extraslider-inner", $element),
						_delay = <?php echo $delay; ?>,
						_duration = <?php echo $duration; ?>,
						_effect = '<?php echo $effect; ?>';

				$extraslider.on("initialized.owl.carousel2", function () {
					var $item_active = $(".owl2-item.active", $element);
					if ($item_active.length > 1 && _effect != "none") {
						_getAnimate($item_active);
					}
					else {
						var $item = $(".owl2-item", $element);
						$item.css({"opacity": 1, "filter": "alpha(opacity = 100)"});
					}
					<?php if($dots == "true") { ?>
					if ($(".owl2-dot", $element).length < 2) {
						$(".owl2-prev", $element).css("display", "none");
						$(".owl2-next", $element).css("display", "none");
						$(".owl2-dot", $element).css("display", "none");
					}
					<?php }?>

					<?php if ($button_page == "top"){ ?>
					$(".owl2-controls", $element).insertBefore($extraslider);
					$(".owl2-dots", $element).insertAfter($(".owl2-prev", $element));
					<?php }else{ ?>
					$(".owl2-nav", $element).insertBefore($extraslider);
					$(".owl2-controls", $element).insertAfter($extraslider);
					<?php }?>

				});

				$extraslider.owlCarousel2({
					rtl: <?php echo $direction?>,
					margin: <?php echo $margin;?>,
					slideBy: <?php echo $slideBy;?>,
					autoplay: <?php echo $autoplay;?>,
					autoplayHoverPause: <?php echo $autoplayHoverPause ;?>,
					autoplayTimeout: <?php echo $autoplayTimeout; ?>,
					autoplaySpeed: <?php echo $autoplaySpeed; ?>,
					startPosition: <?php echo $startPosition; ?>,
					mouseDrag: <?php echo $mouseDrag;?>,
					touchDrag: <?php echo $touchDrag; ?>,
					autoWidth: false,
					responsive: {
						0: 	{ items: <?php echo $nb_column4;?> } ,
						480: { items: <?php echo $nb_column3;?> },
						768: { items: <?php echo $nb_column2;?> },
						992: { items: <?php echo $nb_column1;?> },
						1200: {items: <?php echo $nb_column0;?>}
					},
					dotClass: "owl2-dot",
					dotsClass: "owl2-dots",
					dots: <?php echo $dots; ?>,
					dotsSpeed:<?php echo $dotsSpeed; ?>,
					nav: <?php echo $nav; ?>,
					loop: <?php echo $loop; ?>,
					navSpeed: <?php echo $navSpeed; ?>,
					navText: ["<?php echo $btn_prev; ?>", "<?php echo $btn_next; ?>"],
					navClass: ["owl2-prev", "owl2-next"]

				});

				$extraslider.on("translate.owl.carousel2", function (e) {
					<?php if($dots == "true") { ?>
					if ($(".owl2-dot", $element).length < 2) {
						$(".owl2-prev", $element).css("display", "none");
						$(".owl2-next", $element).css("display", "none");
						$(".owl2-dot", $element).css("display", "none");
					}
					<?php } ?>

					var $item_active = $(".owl2-item.active", $element);
					_UngetAnimate($item_active);
					_getAnimate($item_active);
				});

				$extraslider.on("translated.owl.carousel2", function (e) {

					<?php if($dots == "true") { ?>
					if ($(".owl2-dot", $element).length < 2) {
						$(".owl2-prev", $element).css("display", "none");
						$(".owl2-next", $element).css("display", "none");
						$(".owl2-dot", $element).css("display", "none");
					}
					<?php } ?>

					var $item_active = $(".owl2-item.active", $element);
					var $item = $(".owl2-item", $element);

					_UngetAnimate($item);

					if ($item_active.length > 1 && _effect != "none") {
						_getAnimate($item_active);
					} else {

						$item.css({"opacity": 1, "filter": "alpha(opacity = 100)"});

					}
				});

				function _getAnimate($el) {
					if (_effect == "none") return;
					//if ($.browser.msie && parseInt($.browser.version, 10) <= 9) return;
					$extraslider.removeClass("extra-animate");
					$el.each(function (i) {
						var $_el = $(this);
						$(this).css({
							"-webkit-animation": _effect + " " + _duration + "ms ease both",
							"-moz-animation": _effect + " " + _duration + "ms ease both",
							"-o-animation": _effect + " " + _duration + "ms ease both",
							"animation": _effect + " " + _duration + "ms ease both",
							"-webkit-animation-delay": +i * _delay + "ms",
							"-moz-animation-delay": +i * _delay + "ms",
							"-o-animation-delay": +i * _delay + "ms",
							"animation-delay": +i * _delay + "ms",
							"opacity": 1
						}).animate({
							opacity: 1
						});

						if (i == $el.size() - 1) {
							$extraslider.addClass("extra-animate");
						}
					});
				}

				function _UngetAnimate($el) {
					$el.each(function (i) {
						$(this).css({
							"animation": "",
							"-webkit-animation": "",
							"-moz-animation": "",
							"-o-animation": "",
							"opacity": 1
						});
					});
				}

			})("#<?php echo $tag_id ; ?>");
		});
		//]]>
	</script>
	<?php
	} else {
	   echo $objlang->get('text_noitem');
	} ?>
</div> <!-- /.modcontent -->
<?php if($post_text != '')
{
?>
	<div class="form-group">
		<?php echo html_entity_decode($post_text);?>
	</div>
<?php
}
?>

</div>

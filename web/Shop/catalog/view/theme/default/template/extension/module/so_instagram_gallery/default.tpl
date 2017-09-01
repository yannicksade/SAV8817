	<?php 
	$moduleid = "instagram".rand().time();
	$json = @file_get_contents('https://api.instagram.com/v1/users/'.$users_id.'/media/recent?access_token='.$access_token.'&count='.$limit_image);
	$json_output = json_decode($json, true);
	$count  = count($json_output['data']);
	$class_instagram = 'instagram00-' . $nb_column0 . ' instagram01-' . $nb_column1 . ' instagram02-' . $nb_column2 . ' instagram03-' . $nb_column3 . ' instagram04-' . $nb_column4;
	$j=0;
	$cls_btn_page = ($button_page == 'top') ? 'button-type1':'button-type2';
	$btn_type 	  = ($button_page == 'top') ? 'button-type1':'button-type2';
	$btn_prev = ($button_page == 'top') ? '&#171;':'&#139;';
	$btn_next = ($button_page == 'top') ? '&#187;':'&#155;';
	$nb_rows = $row;
	$navs = ($navs == 0 ? 'false' : 'true');
	$dots = ($dots == 0 ? 'false' : 'true');
	?>
	<div class="module <?php echo $direction_class?> <?php echo $class_suffix; ?>">
		<?php if($disp_title_module) { ?>
		<h3 class="modtitle"><?php echo $head_name;?></h3>

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
			<div class="so-instagram-gallery <?php echo $cls_btn_page; ?>" id="<?php echo $moduleid ;?>">
				<?php if(!empty($count)) { ?>
				<?php if ($type_show == 'slider') { ?>
				<div class="instagram-items-inner owl2-carousel">
					<?php } else { ?>
					<div class="instagram-items-inner <?php echo $class_instagram;?>">
						<?php } ?> 
						<?php for($i=0; $i < $count ; $i++) {
							$j++; ?>
							<?php if($type_show == 'slider' && ($j % $nb_rows == 1 || $nb_rows == 1)) { ?>
							<div class="instagram-item">
								<?php }
								if ($type_show == 'simple'){ ?>
								<div class="instagram-item">
									<?php } ?>
									<div class="instagram_users">
										<div class="img_users">
											<a title="<?php echo $json_output['data'][$i]['user']['full_name'] ;?>" data-href="<?php echo $json_output['data'][$i]['link']?>" class="instagram_gallery_image gallery_image_<?php echo $moduleid ;?>" rel="" href="<?php echo $json_output['data'][$i]['images']['low_resolution']['url'] ?>?taken-by=<?php echo $json_output['data'][$i]['user']['username'] ?>">
												<img class="image_users" src="<?php echo $json_output['data'][$i]['images']['low_resolution']['url'] ?>" title="<?php echo $json_output['data'][$i]['user']['full_name'] ;?>" alt="<?php echo $json_output['data'][$i]['user']['full_name'] ;?>" />
											</a>
										</div>
									</div>

									<?php 	if($type_show == 'slider' && ($j % $nb_rows == 0 || $j == $count)) { ?>
								</div>
								<?php }
								if ($type_show == 'simple'){ ?>
							</div>
							<?php } ?>
							<?php } ?>
						</div> <!--/.instagram-items-inner-->
						<?php }else{
							echo $objlang->get('text_noproduct');
						}?>
					</div><!--/.so-instagram-gallery-->
				</div> <!-- /.modcontent-->
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
			<?php if($type_show == "slider"){?>
			<script type="text/javascript">

		//<![CDATA[
		jQuery(document).ready(function ($) {
			;(function (element) {
				var $element = $(element),
				$extraslider = $(".instagram-items-inner", $element),
				_delay = <?php echo $delay; ?>,
				_duration = <?php echo $duration; ?>,
				_effect = '<?php echo $effect; ?>';

				this_item = $extraslider.find('div.media');
				this_item.find('div.item:eq(0)').addClass('head-button');
				this_item.find('div.item:eq(0) .media-heading').addClass('head-item');
				this_item.find('div.item:eq(0) .media-left').addClass('so-block');
				this_item.find('div.item:eq(0) .media-content').addClass('so-block');
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
					autoplayHoverPause: <?php echo $pausehover ;?>,
					autoplayTimeout: <?php echo $autoplay_timeout; ?>,
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
					nav: <?php echo $navs; ?>,
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

		})("#<?php echo $moduleid ; ?>");
	});
		//]]>
	</script>
	<?php }?>
	<script type="text/javascript">
		$(document).ready(function(){
			$(".gallery_image_<?php echo $moduleid ;?>").attr('rel', 'gallery').fancybox({
		        prevMethod : false,
				helpers: {
					thumbs	: {
						width	: 50,
						height	: 50
					},
					<?php if($show_fullname){?>
					title : {
		                type : 'over'
		            }
		            <?php }else{?>
		            title : null
		            <?php }?>
				},
			});
		});
	</script>
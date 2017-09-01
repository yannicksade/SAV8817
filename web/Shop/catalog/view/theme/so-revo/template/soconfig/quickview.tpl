<?php 
/******************************************************
 * @package	SO Theme Framework for Opencart 2.0.x
 * @author	http://www.magentech.com
 * @license	GNU General Public License
 * @copyright(C) 2008-2015 Magentech.com. All rights reserved.
*******************************************************/
?>
<?php // Header Blocks =========================================?>
<?php echo $header; ?>
<style type="text/css">
	body{background:none;}
	#wrapper{box-shadow:none;background:#fff;}
	#wrapper > *:not(.product-detail){display: none;}
	#wrapper .product-view{margin:0;}
	#wrapper .short_description {padding:0}
	.container{width:100%;}
	.no-res{width:auto;}
	.rtl .zoomWindowContainer .zoomWindow{left: auto!important; right: 0!important;}
</style>

<?php
	/*Product Short Description*/
	$limit = 350;
	if(strlen($html_product_shortdesc) >  30){
		$product_description_short = html_entity_decode($html_product_shortdesc, ENT_QUOTES, 'UTF-8');;
	}else{
		$full_description = strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8'));
		$product_description_short = "<h3>". $overview."</h3>";
		$product_description_short .= "<p>". (strlen($full_description) > $limit ? utf8_substr($full_description, 0, $limit) . '...' : $full_description)."</p>";
	}
?>


<?php // Content Detail Blocks ========================================= ?>
<div class="product-detail">
    <div id="product-quick" class="product-info">
      
        <div class="product-view row">
		  <div class="left-content-product ">
			<?php //Img Gallery Block -------?>
			
                <div class="content-product-left class-honizol  col-sm-5">
                   
                        <div class="large-image ">
							<div class="box-label">
								<!--New Label-->
								<?php if (!isset($new_status) || ($new_status)) : ?>
								<?php
								$day_range = 10;
								if ($days == '') $day = $day_range;
								else $day = $days;
								$day_number_to_range = date( "Y-m-d" ,  strtotime("-$day day")  );
								if ($product_info['date_available'] >= $day_number_to_range) :
								?>
									<span class="label-product label-new"><?php echo (isset($new_text) ? $new_text : 'NEW'); ?></span>
								<?php endif; ?>
								<?php endif; ?>
							
								<!--Sale Label-->
								<?php if (!isset($sale_status) || ($sale_status )) : ?>
								<?php if ($product_info['special']) : ?>
									<span class="label-product label-sale">
										<?php  if($discount_status) echo $discount; ?>    
									</span>
								<?php endif; ?>
								<?php endif; ?>
							</div>
							
                            <img itemprop="image" class="product-image-zoom" src="<?php echo $popup; ?>" data-zoom-image="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" />
                        </div>
				
						
						<?php if ($images ) :  ?>
	                        <div id="thumb-slider" class="owl-theme owl-loaded owl-drag <?php echo ((count($images) < 3 ) ? 'not_full_slider' : 'full_slider'); ?> ">
								<?php
								if (sizeof($images) > 0) {
									$firstimg = array('popup' => $popup,'thumb' => $thumb);
									array_unshift($images, $firstimg);
								}
								if ($images) : ?>
									<?php $i=-1; foreach ($images as $image) : $i++ ?>
										<a data-index="<?php echo $i; ?>" class="img thumbnail" data-image="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>">
											<img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" />
										</a>
									<?php endforeach; ?>
								<?php endif; ?>
								
	                        </div>
							<script type="text/javascript"><!--
								jQuery(function ($) {
									var thumbWidth = $(".large-image img").width()+2, thumbHeight = $("#thumb-slider .img").height() ;
									var $nav = $("#thumb-slider");
									
									$nav.each(function () {
										$(this).owlCarousel2({
											nav:true,
											dots: false,
											slideBy: 1,
											margin:10,
											navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
											<?php if($direction=='rtl'):?> rtl:true, <?php endif;?>
											responsive:{
												0:{
													items: 2,
												},
												481:{
													items:2,
												},
												768:{
													items:3,	
												},
												992:{
													items:3
												},
												1200:{
													items:3
												}
											}
										});
									})
									
								});
							//--></script>
	                    <?php endif; ?>
						<?php //End Bottom Thumbnails previews -------?>
					
                </div>
           
			<?php //End Img Gallery Block -------?>

			<?php //Product info Block -------?>
			<div class="content-product-right col-sm-7">
				<div class="title-product">
					<h1><?php echo $heading_title; ?></h1>
				</div>
				
				<div class="row">
					<div class="col-sm-6 col-xs-12">
						<!-- Review ---->
						<?php if ($review_status) { ?>
							<div class="box-review">
							   <div class="ratings">
								  <div class="rating-box">
								  <?php for ($i = 1; $i <= 5; $i++) { ?>
								  <?php if ($rating < $i) { ?>
								  <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
								  <?php } else { ?>
								  <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
								  <?php } ?>
								  <?php } ?>
								  </div>
							  </div>
					 
								<span class="reviews_button"  ><?php echo $reviews; ?></span> 
							</div>
						<?php } ?>
						
						<?php //Product Price -------?>
						<?php if ($price) : ?>
						<div class="product_page_price price" itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer">
							 <?php if (!$special) { ?>
							<span class="price-new"><span itemprop="price" id="price-old"><?php echo $price; ?></span></span>
							<?php } else { ?>
							<span class="price-new"><span itemprop="price" id="price-special"><?php echo $special; ?></span></span> <span class="price-old" id="price-old"><?php echo $price; ?></span>
							<?php } ?>
							<?php if ($tax) { ?>
							<div class="price-tax"><span><?php echo $text_tax; ?></span> <?php echo $tax; ?></div>
							<?php } ?>
							
							<?php if ($discounts) { ?>
							<ul class="list-unstyled">
								<?php foreach ($discounts as $discount) { ?>
								<li><?php echo $discount['quantity']; ?><?php echo $text_discount; ?><?php echo $discount['price']; ?></li>
								<?php } ?>
							</ul>
							<?php } ?>
						</div>
						<?php endif; ?>
						<?php //End Product Price -------?>	
					 
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="product-box-desc">
						<?php if ($manufacturer): ?>
								<div class="brand"><span><?php echo $text_manufacturer; ?></span><a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a></div>
						<?php endif; ?>
						<?php 
						if ($model): ?>
						<div class="model"><span><?php echo $text_model; ?></span> <?php echo $model; ?></div>
						<?php endif; ?>
						
						<?php if ($points) { ?>
						<div class="reward"><span><?php echo $text_points; ?></span> <?php echo $points; ?></div>
						<?php } ?>
						<div class="stock"><span><?php echo $text_stock; ?></span> <i class="fa fa-check-square-o"></i> <?php echo $stock; ?></div>			
					</div>
					</div>
				</div>
				
				
				
				<div class="short_description form-group">
					
					<?php echo $product_description_short;?>
				</div>
				<?php // End Product Description -------?>
				
				
				
				<?php if ($options) { ?>
				<div id="product">	
					<h3><?php echo $text_option; ?></h3>
				<?php foreach ($options as $option) { ?>
					<?php if ($option['type'] == 'select') { ?>
					<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
						<label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
						<select name="option[<?php echo $option['product_option_id']; ?>]" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control">
							<option value=""><?php echo $text_select; ?></option>
							<?php foreach ($option['product_option_value'] as $option_value) { ?>
							<option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
								<?php if ($option_value['price']) { ?>
								(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
								<?php } ?>
							</option>
							<?php } ?>
						</select>
					</div>
					<?php } ?>
				
					<?php if ($option['type'] == 'radio') { ?>
						<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
							<label class="control-label"><?php echo $option['name']; ?></label>
							<div id="input-option<?php echo $option['product_option_id']; ?>">
								<?php 
								$radio_type 	= isset($radio_style) && $radio_style ? ' radio-type-button':'';
								foreach ($option['product_option_value'] as $option_value) { 
								$radio_image 	= isset($option_value['image']) ? 'option_image' : '';
								$radio_price  	= isset($radio_style) && $radio_style ? $option_value['price_prefix']. $option_value['price'] : '';
								?>
									<div class="radio <?php echo $radio_image. $radio_type;?>">
										 <label>
											<input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" />
											<span class="option-content-box" data-title="<?php echo $radio_price;?>" data-toggle='tooltip'>
												<?php if ($option_value['image']) { ?>
												<img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" class="img-thumbnail" /> 
												<?php } ?>
												<span class="option-name"><?php echo $option_value['name']; ?></span>
												<?php if ($option_value['price'] && $radio_style !='1') { ?>
												(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
												<?php } ?>
											  </label>
											</span>
									</div>
									
								<?php } ?>
								<?php if($radio_style) { ?>
								<script type="text/javascript">
									 $(document).ready(function(){
										  $('#input-option<?php echo $option['product_option_id']; ?>').on('click', 'span', function () {
											   $('#input-option<?php echo $option['product_option_id']; ?> span').removeClass("active");
											   $(this).addClass("active");
										  });
									 });
								</script>
								<?php } ?>
							</div>
						</div>
						<?php } ?>
					
						<?php if ($option['type'] == 'checkbox') { ?>
						<div class="box-checkbox form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
							<label class="control-label"><?php echo $option['name']; ?></label>
							<div id="input-option<?php echo $option['product_option_id']; ?>">
								<?php 
								$check_type 	= isset($check_style) && $check_style ? ' radio-type-button':'';
								foreach ($option['product_option_value'] as $option_value) {
								$check_image 	= isset($option_value['image']) ? ' option_image' : '';
								$check_price  	= isset($check_style) && $check_style ? $option_value['price_prefix']. $option_value['price'] : '';
								?>
									<div class="checkbox <?php echo $check_type.$check_image ;?>">
										<label>
											<input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" />
											<span class="option-content-box" data-title="<?php echo $check_price;?>" data-toggle='tooltip'>
												<?php if ($option_value['image']) { ?>
												<img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" class="img-thumbnail" /> 
												<?php } ?>
												<span class="option-name"><?php echo $option_value['name']; ?></span>
												<?php if ($option_value['price'] && $check_style !='1') { ?>
												(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
												<?php } ?>
											</span>
										</label>
									</div>
									<?php } ?>
									
									<?php if($check_style) { ?>
									<script type="text/javascript">
										 $(document).ready(function(){
											  $('#input-option<?php echo $option['product_option_id']; ?>').on('click', 'span', function () {
												   $('#input-option<?php echo $option['product_option_id']; ?> span').removeClass("active");
												   $(this).addClass("active");
											  });
										 });
									</script>
								<?php } ?>
							</div>
						</div>
						
						<?php } ?>
				
					<?php if ($option['type'] == 'image') { ?>
					<div class="image_option_type form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
						<label class="control-label"><?php echo $option['name']; ?></label>
						<ul class="product-options clearfix" id="input-option<?php echo $option['product_option_id']; ?>">
							<?php foreach ($option['product_option_value'] as $option_value) { ?>
							<li class="radio">
								<label>
									<input class="image_radio" type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" />
									<img   src="<?php echo $option_value['image']; ?>" data-original-title="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" class="img-thumbnail icon icon-color" />
									<i class="fa fa-check"></i>
								</label>
							</li>
							<?php } ?>
							<li class="radio selected-option"></li>
						</ul>
					</div>
					<?php } ?>
				
					<?php if ($option['type'] == 'text') { ?>
					<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
						<label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
						<input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
					</div>
					<?php } ?>
					
					<?php if ($option['type'] == 'textarea') { ?>
					<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
						<label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
						<textarea name="option[<?php echo $option['product_option_id']; ?>]" rows="5" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control"><?php echo $option['value']; ?></textarea>
					</div>
					<?php } ?>
					
					<?php if ($option['type'] == 'file') { ?>
					<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
						<label class="control-label"><?php echo $option['name']; ?></label>
						<button type="button" id="button-upload<?php echo $option['product_option_id']; ?>" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-default btn-block"><i class="fa fa-upload"></i> <?php echo $button_upload; ?></button>
						<input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" id="input-option<?php echo $option['product_option_id']; ?>" />
					</div>
					<?php } ?>
				
					<?php if ($option['type'] == 'date') { ?>
					<div class="box-date form-group<?php echo ($option['required'] ? ' required' : ''); ?> col-sm-12 col-xs-12">
						<label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
						<div class="input-group date">
							<input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
							<span class="input-group-btn">
							<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
							</span>
						</div>
					</div>
					<?php } ?>
				
					<?php if ($option['type'] == 'datetime') { ?>
					<div class="box-date form-group<?php echo ($option['required'] ? ' required' : ''); ?> col-sm-12 col-xs-12">
						<label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
						<div class="input-group datetime">
							<input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
							<span class="input-group-btn">
							<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
							</span>
						</div>
					</div>
					<?php } ?>
				
					<?php if ($option['type'] == 'time') { ?>
					<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
						<label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
						<div class="input-group time">
							<input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
							<span class="input-group-btn">
							<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
							</span>
						</div>
					</div>
					<?php } ?>
				
				<?php } ?>
				</div>
				<?php } ?>
				
				
				<?php
					
				if ($recurrings) { ?>
				<hr>
				<h3><?php echo $text_payment_recurring ?></h3>
				<div class="form-group required">
					<select name="recurring_id" class="form-control">
						<option value=""><?php echo $text_select; ?></option>
						<?php foreach ($recurrings as $recurring) { ?>
						<option value="<?php echo $recurring['recurring_id'] ?>"><?php echo $recurring['name'] ?></option>
						<?php } ?>
					</select>
					<div class="help-block" id="recurring-description"></div>
				</div>
				<?php } ?>
				
				
			   <div class="form-group box-info-product">
				    <div class="option quantity">
					  <div class="input-group quantity-control">
						  <label><?php echo $entry_qty; ?></label>
						  <input class="form-control" type="text" name="quantity" value="<?php echo $minimum; ?>" />
						  <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
						  <span class="input-group-addon product_quantity_down fa fa-caret-down"></span>
						  <span class="input-group-addon product_quantity_up fa fa-caret-up"></span>
					  </div>
				    </div>
					<div class="cart">
						<input type="button" data-toggle="tooltip" title="<?php echo $button_cart; ?>" value="<?php echo $button_cart; ?>" data-loading-text="<?php echo $text_loading; ?>" id="button-cart" class="btn btn-mega btn-lg " />
					</div>
					<div class="add-to-links wish_comp">
						<ul class="blank">
							<li class="wishlist">
								<a class="icon" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product_id; ?>');"><i class="fa fa-heart-o"></i></a>
							</li>
							<li class="compare">
								<a class="icon" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product_id; ?>');"><i class="fa fa-retweet"></i></a>
							</li>
							<li class="compare">
								<a href="<?php echo $our_url->link('product/product','product_id='.$product_info['product_id']);?>" data-toggle="tooltip" title="<?php echo $button_detail; ?>" target="_top"  class="icon" ><i class="fa fa-chevron-right"></i> </a>
							</li>
						</ul>
					</div>
			   </div>
			  
				<?php if ($minimum > 1) : ?><p class="minimum" style="clear:both"><?php echo $text_minimum; ?></p><?php endif; ?>

					
			
            </div>
			<?php //End Product info Block -------?>
		  </div><!-- end - left-content-product --->

		</div>

	
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		var zoomCollection = '.large-image img';
		$( zoomCollection ).elevateZoom({
			zoomType        : "inner",
			lensSize    :"350",
			easing:true,
			gallery:'thumb-slider',
			cursor: 'pointer',
			galleryActiveClass: "active"
		});
		
		$('.product-options li').click(function(){
			$(this).addClass(function() {
				if($(this).hasClass("active")) return "";
				return "active";
			});
			
			$(this).siblings("li").removeClass("active");
			$('.product-options .selected-option').html('<span class="label label-success">'+ $(this).find('img').data('original-title') +'</span>');
		})
		
		
	});
			
</script>

<script type="text/javascript"><!--
$('select[name=\'recurring_id\'], input[name="quantity"]').change(function(){
	$.ajax({
		url: 'index.php?route=product/product/getRecurringDescription',
		type: 'post',
		data: $('input[name=\'product_id\'], input[name=\'quantity\'], select[name=\'recurring_id\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#recurring-description').html('');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			
			if (json['success']) {
				$('#recurring-description').html(json['success']);
			}
		}
	});
});
//--></script> 

<script type="text/javascript"><!--
$('#button-cart').on('click', function() {
	$.ajax({
		url: 'index.php?route=extension/soconfig/cart/add',
		type: 'post',
		data: $('#product-quick input[type=\'text\'], #product-quick input[type=\'hidden\'], #product-quick input[type=\'radio\']:checked, #product-quick input[type=\'checkbox\']:checked, #product-quick select, #product-quick textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-cart').button('loading');
		},
		complete: function() {
			$('#button-cart').button('reset');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						var element = $('#input-option' + i.replace('_', '-'));
						
						if (element.parent().hasClass('input-group')) {
							element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						} else {
							element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						}
					}
				}
				
				if (json['error']['recurring']) {
					$('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
				}
				
				// Highlight any found errors
				$('.text-danger').parent().addClass('has-error');
			}
			
			if (json['success']) {
				parent.addProductNotice(json['title'], json['thumb'], json['success'], 'success');
				
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					parent.$('#cart .text-shopping-cart').html(json['total'] );
					$('.text-danger').remove();
				}, 100);
				parent.$('#cart > ul').load('index.php?route=common/cart/info ul li');
			}
			
		},
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
	});
});

var wishlist = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=extension/soconfig/wishlist/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				
				if (json['success']) {
					parent.addProductNotice(json['title'], json['thumb'], json['success'], 'success');
				}
				if (json['info']) {
				  parent.addProductNotice(json['title'], json['thumb'], json['info'], 'warning');
				}
				parent.$('#wishlist-total').html(json['total']);
				parent.$('#wishlist-total').attr('title', json['total']);
			}
		});
	}
}

var compare = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=extension/soconfig/compare/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				if (json['success']) {
					parent.addProductNotice(json['title'], json['thumb'], json['success'], 'success');
					parent.$('#compare-total').html(json['total']);
				}
			}
		});
	}
	
}
//--></script> 
<script type="text/javascript"><!--
$('.product-options li.radio').click(function(){
	$(this).addClass(function() {
		if($(this).hasClass("active")) return "";
		return "active";
	});
	
	$(this).siblings("li").removeClass("active");
	$(this).parent().find('.selected-option').html('<span class="label label-success">'+ $(this).find('img').data('original-title') +'</span>');
})

$('.date').datetimepicker({
	pickTime: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});

$('.time').datetimepicker({
	pickDate: false
});

$('button[id^=\'button-upload\']').on('click', function() {
	var node = this;
	
	$('#form-upload').remove();
	
	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');
	
	$('#form-upload input[name=\'file\']').trigger('click');
    if (typeof timer != 'undefined') {
        clearInterval(timer);
    }
	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);
			
			$.ajax({
				url: 'index.php?route=tool/upload',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$(node).button('loading');
				},
				complete: function() {
					$(node).button('reset');
				},
				success: function(json) {
					$('.text-danger').remove();
					
					if (json['error']) {
						$(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
					}
					
					if (json['success']) {
						alert(json['success']);
						
						$(node).parent().find('input').attr('value', json['code']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});
//--></script> 

<script type="text/javascript">
var ajax_price = function() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=extension/soconfig/liveprice/index',
		data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
		dataType: 'json',
			success: function(json) {
			if (json.success) {
				change_price('#price-special', json.new_price.special);
				change_price('#price-tax', json.new_price.tax);
				change_price('#price-old', json.new_price.price);
			}
		}
	});
}

var change_price = function(id, new_price) {
	$(id).html(new_price);
}
$('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\'], .product-info input[type=\'checkbox\'], .product-info select, .product-info textarea, .product-info input[name=\'quantity\']').on('change', function() {
	ajax_price();
});
</script>


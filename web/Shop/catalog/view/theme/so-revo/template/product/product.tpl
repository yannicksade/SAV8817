<?php 
/******************************************************
 * @package	SO Theme Framework for Opencart 2.0.x
 * @author	http://www.magentech.com
 * @license	GNU General Public License
 * @copyright(C) 2008-2015 Magentech.com. All rights reserved.
*******************************************************/
?>
<?php
	/*Product Short Description*/
	$limit = 150;$getColumn='left';
	if(strlen($html_product_shortdesc) >  30){
		$product_description_short = html_entity_decode($html_product_shortdesc, ENT_QUOTES, 'UTF-8');
	}else{
		$full_description = strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8'));
		$product_description_short = "<h3>". $overview."</h3>";
		$product_description_short .= (strlen($full_description) > $limit ? utf8_substr($full_description, 0, $limit) . '...' : $full_description);
	}
	
	if($column_left && $column_right) $getColumn='3column';
	else if ($column_left)  $getColumn='left';
	else if($column_right)  $getColumn='right';
	else $getColumn='full';
?>

<?php // Header Blocks =========================================?>
<?php echo $header; ?>

<?php // Breadcrumb Blocks =========================================?>
<div class="container ">
	 <!-- BREADCRUMB -->
	<ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
</div>

<?php // Content Detail Blocks ========================================= ?>
<div class="container product-detail <?php echo'product-'. $getColumn;?>">
	<div class="row">
    <?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-8 col-md-9 col-xs-12'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
	
    <div id="content" class="<?php echo $class; ?>">
    	<a href="javascript:void(0)" class="open-sidebar hidden-lg hidden-md"><i class="fa fa-bars"></i> <?php echo $text_sidebar; ?></a>
		<div class="sidebar-overlay "></div>
        <div class="row product-view product-info clearfix" itemprop="offerDetails" itemscope itemtype="http://schema.org/Product">
			<?php //Img Gallery Block -------
			switch ($product_gallerysize){
				case "small":
					$left_gallery 	= "col-md-4 col-sm-12 col-xs-12";
					$right_gallery 	= "col-md-8 col-sm-12 col-xs-12";
					break;
				case "large":
					$left_gallery 	= "col-md-7 col-sm-12 col-xs-12";
					$right_gallery 	= "col-md-5 col-sm-12 col-xs-12";
					break;
				default:
					$left_gallery 	= "col-md-5 col-sm-12 col-xs-12";
					$right_gallery 	= "col-md-7 col-sm-12 col-xs-12";
					break;
			}
			?>
				<div class="content-product-left  <?php if(isset($thumbnails_position) && $thumbnails_position == 'bottom') echo "class-honizol"; ?> <?php echo $left_gallery; ?> ">
					<?php //Left Thumbnails previews -------?>
                    <?php if ($images && isset($thumbnails_position) && $thumbnails_position == 'left') : ?>
                        <div id="thumb-slider" class="thumb-vertical-outer">
							<span class="btn-more prev-thumb nt"><i class="fa fa-chevron-up"></i></span>
							<span class="btn-more next-thumb nt"><i class="fa fa-chevron-down"></i></span>
							<ul class="thumb-vertical">
								<?php 
								if (sizeof($images) > 0) {
									$firstimg = array('popup' => $popup,'thumb' => $thumb);
									array_unshift($images, $firstimg);
								}
								if ($images) : ?>
									<?php $i=-1; foreach ($images as $image) :$i++ ?>
									<li class="owl2-item">
										<a data-index="<?php echo $i; ?>" class="img thumbnail" data-image="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>">
											<img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" />
										</a>
									</li>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
                        </div>
                    <?php endif; ?>
                  

					<div class="large-image  <?php echo  (isset($thumbnails_position) && $thumbnails_position == 'left') ? ' vertical ' : '' ?> ">
						<img itemprop="image" class="product-image-zoom" src="<?php echo $popup; ?>" data-zoom-image="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" />
						<div class="box-label">
							<!--New Label-->
							<?php if (!isset($new_status) || ($new_status)) : ?>
							<?php
							$day_range = 10;
							if(isset($days)):
								if ( $days == '') $day = $day_range;
								else $day = $days;
							$day_number_to_range = date( "Y-m-d" ,  strtotime("-$day day")  );
							if ($product_info['date_available'] >= $day_number_to_range) :
							?>
								<span class="label-product label-new"><?php echo $objlang->get('text_new'); ?></span>
							<?php endif; ?>
							<?php endif; ?>
							<?php endif; ?>
							
							<!--Sale Label-->
							<?php if (isset($sale_status ) && ($sale_status )) :?>
							<?php if ($product_info['special']) : ?>
								<span class="label-product label-sale">
									<?php  if($discount_status) echo $discount; ?>    
								</span>
							<?php endif; ?>
							<?php endif; ?>
						</div>
						<?php if (isset($video1) && $video1 != '') : ?>
							<a class="thumb-video pull-left" href="<?php echo $video1; ?>"><i class="fa fa-youtube-play"></i></a>
						<?php endif; ?>
					</div>
						
				
					
					<?php //Bottom Thumbnails previews -------?>
                    <?php if ($images  && isset($thumbnails_position) && $thumbnails_position == 'bottom') : ?>
                        <div id="thumb-slider" class="<?php echo ((count($images) < 3 ) ? 'not_full_slider' : 'full_slider'); ?> <?php echo ($thumbnails_position == 'left' ? 'flexslider-large visible-xs' : 'owl-carousel'); ?>">
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
						<script type="text/javascript">
							$(function ($) {
								var $nav = $("#thumb-slider");
								
								$nav.each(function () {
									$(this).owlCarousel2({
										nav:true,
										dots: false,
										slideBy: 1,
										margin:10,
										navText: ['<i class="fa fa-caret-left"></i>','<i class="fa fa-caret-right"></i>'],
										<?php if($direction=='rtl'):?> rtl:true, <?php endif;?>
										responsive:{
											0:{
												items:2
											},
											600:{
												items:3
											},
											1000:{
												items:5
											}
										}
									});
								})
								
							});
						</script>
                    <?php endif; ?>
					<?php //End Bottom Thumbnails previews -------?>
                </div>
			<?php //End Img Gallery Block -------?>

			<?php //Product info Block -------?>
			<div class="content-product-right <?php if(isset($thumbnails_position) && $thumbnails_position == 'bottom') echo "info-right"; ?> <?php  echo $right_gallery; ?>">
				<div class="title-product">
					<h1 itemprop="name"><?php echo $heading_title; ?></h1>
				</div>
				 <!-- Review ---->
				 <?php if ($review_status) { ?>
					<div class="box-review" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
					   <div class="ratings">
						  <div class="rating-box" itemprop="ratingValue" content="<?php echo $rating;?>">
						  <?php for ($i = 1; $i <= 5; $i++) { ?>
						  <?php if ($rating < $i) { ?>
						  <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
						  <?php } else { ?>
						  <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
						  <?php } ?>
						  <?php } ?>
						  </div>
					  </div>
			 
					   <a class="reviews_button" href="" onclick="$('a[href=\'#tab-review\']').trigger('click'); return false;" itemprop="reviewCount" content="<?php echo $count_reviews; ?>"><?php echo $reviews; ?></a> | <a class="write_review_button" href="" onclick="$('a[href=\'#tab-review\']').trigger('click'); return false;"><?php echo $text_write; ?></a>
					</div>
				<?php } ?>
				
			
				<?php //Product Description -------?>
				<div class="product-label">
					<?php //Product Price -------?>
					<?php if ($price) : ?>
					<div class="product_page_price price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">											 

				        <?php if (!$special) { ?>
                        <span class="price-new"><span itemprop="price" id="price-old" content="<?php echo $price_value; ?>"><?php echo $price; ?></span><meta itemprop="priceCurrency" content="<?php echo $currency;?>" /></span>
                        <?php } else { ?>
                        <span class="price-new"><span itemprop="price" id="price-special" content="<?php echo $special_value; ?>"><?php echo $special; ?></span><meta itemprop="priceCurrency" content="<?php echo $currency;?>" /></span> <span class="price-old" id="price-old"><?php echo $price; ?></span>
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
				<div class="product-box-desc">
					<div class="inner-box-desc">
						<?php if ($manufacturer): ?>
								<div class="brand"><span><?php echo $text_manufacturer; ?></span><a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a></div>
						<?php endif; ?>
						<?php 
						if ($model): ?>
						<div class="model"><span><?php echo $text_model; ?></span> <?php echo $model; ?></div>
						<?php endif; ?>
						<?php if ($reward): ?>
							<div class="reward"><span><?php echo $text_reward; ?></span> <?php echo $reward; ?></div>
						<?php endif; ?>
						<?php if ($points) { ?>
						<div class="reward"><span><?php echo $text_points; ?></span> <?php echo $points; ?></div>
						<?php } ?>
						<div class="stock"><span><?php echo $text_stock; ?></span> <i class="fa fa-check-square-o"></i> <?php echo $stock; ?></div>	
						
					</div>		
				</div>
				
				<div class="short_description form-group" itemprop="description">
                    <?php echo $product_description_short;?>
                </div>
				<?php // End Product Description -------?>
				
				
			 
				<!--Countdown box-->
				<?php   
					$product['special'] = $special;
					$product['special_end_date'] = $special_end_date;
					$product['product_id'] = $product_id;
					if (file_exists(DIR_TEMPLATE . $theme . '/template/soconfig/countdown.php')) include(DIR_TEMPLATE.$theme.'/template/soconfig/countdown.php'); 
					else echo 'Not found';
				?>
				<!--End countdown box-->
			
				<div id="product">
					<?php if ($options) { ?>
					<div class="options clearfix">
						<h3 ><?php echo $text_option; ?></h3>
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
							<button type="button" id="button-upload<?php echo $option['product_option_id']; ?>" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-default btn-inline"><i class="fa fa-upload"></i> <?php echo $button_upload; ?></button>
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
								<input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>"  data-date-format="YYYY-MM-DD" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
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
					
					<div class="box-cart cart clearfix">
						<?php if ($recurrings) { ?>
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
						
						<!-- QUALYTY -->
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
						   <!-- CART -->
						   <div class="cart">
								<input type="button" data-toggle="tooltip" title="<?php echo $button_cart; ?>" value="<?php echo $button_cart; ?>" data-loading-text="<?php echo $text_loading; ?>" id="button-cart" class="btn btn-mega btn-lg" />
							</div>
							<div class="add-to-links wish_comp">
								<ul class="blank">
									<li class="wishlist">
										<a class="icon" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product_id; ?>');"><i class="fa fa-heart-o"></i></a>
									</li>
									<li class="compare">
										<a class="icon" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product_id; ?>');"><i class="fa fa-retweet"></i></a>
									</li>
								</ul>
							</div>
							<?php if ($minimum > 1) : ?><p class="minimum" style="clear:both; display:none;"><?php echo $text_minimum; ?></p><?php endif; ?>

						</div>
						
						
					</div>
			
			
			<?php if ($product_page_button): ?>
				<div class="form-group share clearfix">
					<?php
					if (isset($product_socialshare) && $product_socialshare != '' && is_string($product_socialshare)) :
						echo html_entity_decode($product_socialshare, ENT_QUOTES, 'UTF-8');
					endif;
					?>
				</div>
			<?php endif; ?>
			
				
			</div><!-- end box info product -->

            </div>
			<?php //End Product info Block -------?>
			
		</div>

		<?php echo $content_top; ?>
		<?php if ($attribute_groups) : ?>
		<div class="product-attribute module">
			<div class="table-responsive">
				<table class="table">
					<?php foreach ($attribute_groups as $attribute_group) { ?>
					<thead>
					<tr>
						<td class="title-attribute" colspan="2"><?php echo $attribute_group['name']; ?></td>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($attribute_group['attribute'] as $attribute) { ?>
					<tr>
						<td class="name-attribute"><?php echo $attribute['name']; ?></td>
						<td class="text-attribute"><?php echo $attribute['text']; ?></td>
					</tr>
					<?php } ?>
					</tbody>
					<?php } ?>
				</table>
			</div>
		</div>
		<?php endif; ?>
		<div class="row product-bottom">
			<?php 
			$related_column = ($related_position =='vertical' && $products && $related_status) ? 'col-md-8 col-sm-12' : 'col-xs-12';
			if($related_position =='vertical' && $products) :?>
			<div class="col-md-4 col-sm-12">
				<!-- TAB RELATED PRODUCT VERTICAL -->
				<?php 
				if (isset($related_status) && $related_status ) :
					if (file_exists(DIR_TEMPLATE . $theme . '/template/soconfig/related.php')) include(DIR_TEMPLATE.$theme.'/template/soconfig/related.php');
					else echo 'Not found';
				endif;
				?>
				
			</div>
			<?php endif;?>
			
			<div class="<?php echo $related_column;?>">
				<?php // Tabs Blocks =========================================?>
				<?php if (isset($tabs_position) && $tabs_position != 3) : ?>
				<div class="producttab ">
					<div class="tabsslider  <?php if ($tabs_position == 1){ echo "vertical-tabs"; }?> col-xs-12">
						<?php if ($tabs_position != 1) : ?>
						<ul class="nav nav-tabs font-sn">
							<?php if ($description) : ?>
							<li class="active"><a data-toggle="tab" href="#tab-1"><?php echo $tab_description; ?></a></li>
							<?php endif; ?>
							
							
							
							<?php if (!empty($html_product_tab)) : ?>
							<li class="<?php echo (!$description && !$attribute_groups && !$review_status && !$tags ? 'active' : 'item_nonactive'); ?>"><a data-toggle="tab" href="#tab-5"><?php echo (!empty($tab_title) ? $tab_title : 'Custom block'); ?></a></li>
							<?php endif; ?>
							
							<?php if ($tags) : ?>
							<li class="<?php echo (!$description && !$attribute_groups && !$review_status ? 'active' : 'item_nonactive'); ?>"><a data-toggle="tab" href="#tab-4"><?php echo (!empty($soconfig_lang[$lang]["tags_tab_title"]) ? $soconfig_lang[$lang]["tags_tab_title"] : 'TAGS'); ?></a></li>
							<?php endif; ?>
							
							<?php if ($review_status) : ?>
							<li class="<?php echo (!$description && !$attribute_groups ? 'active' : 'item_nonactive'); ?>"><a data-toggle="tab" href="#tab-review"><?php echo $tab_review; ?></a></li>
							<?php endif; ?>
							
						</ul>
						<?php endif; ?>
					
					<?php //Tabs Left Position -------?>
						<?php if ($tabs_position == 1) : ?>
						
							<ul class="nav nav-tabs col-lg-3 col-sm-4">
								<?php if ($description) : ?>
								<li class="active"><a data-toggle="tab" href="#tab-1"><?php echo $tab_description; ?></a></li>
								<?php endif; ?>

							

								<?php if ($review_status) : ?>
								<li class="<?php echo (!$description && !$attribute_groups ? 'active' : 'item_nonactive'); ?>"><a data-toggle="tab" href="#tab-review"><?php echo $tab_review; ?></a></li>
								<?php endif; ?>
								
								<?php if ($tags) : ?>
								<li class="<?php echo (!$description && !$attribute_groups && !$review_status ? 'active' : 'item_nonactive'); ?>"><a data-toggle="tab" href="#tab-4"><?php echo (!empty($soconfig_lang[$lang]["tags_tab_title"]) ? $soconfig_lang[$lang]["tags_tab_title"] : 'TAGS'); ?></a></li>
								<?php endif; ?>

								<?php if (!empty($html_product_tab)) : ?>
								<li class="<?php echo (!$description && !$attribute_groups && !$review_status && !$tags ? 'active' : 'item_nonactive'); ?>"><a data-toggle="tab" href="#tab-5"><?php echo (!empty($tab_title) ? $tab_title : 'Custom block'); ?></a></li>
								<?php endif; ?>
							</ul>
							
						<?php endif; ?>
							<div class="tab-content <?php if ($tabs_position == 1){ echo "col-lg-9 col-sm-8"; }?> col-xs-12">
								<?php if ($description) : ?>
								<div id="tab-1" class="tab-pane fade active in">
									<?php echo $description; ?>
								</div>
								<?php endif; ?>

								

								<?php if ($review_status) : ?>
								<div id="tab-review" class="<?php echo (!$description && !$attribute_groups ? 'tab-pane fade active in' : 'tab-pane fade'); ?>">
									<form>
										<div id="review"></div>
										<h2 id="review-title"><?php echo $text_write; ?></h2>
										<?php if ($review_guest) { ?>
											<div class="contacts-form">
												<div class="form-group">
													<span class="icon icon-user"></span>
													<input type="text" name="name" class="form-control" value="<?php echo $entry_name; ?>" onblur="if (this.value == '') {this.value = '<?php echo $entry_name; ?>';}" onfocus="if(this.value == '<?php echo $entry_name; ?>') {this.value = '';}">
												</div>
												<div class="form-group">
													<span class="icon icon-bubbles-2"></span>
													<textarea class="form-control" name="text" onblur="if (this.value == '') {this.value = '<?php echo $entry_review; ?>';}" onfocus="if(this.value == '<?php echo $entry_review; ?>') {this.value = '';}"><?php echo $entry_review; ?></textarea>
												</div>
												<div class="form-group">
												<span style="font-size: 11px;"><?php echo $text_note; ?></span>
												<br />
												<br />
												<b><?php echo $entry_rating; ?></b> <span><?php echo $entry_bad; ?></span>&nbsp;
												<input type="radio" name="rating" value="1" />
												&nbsp;
												<input type="radio" name="rating" value="2" />
												&nbsp;
												<input type="radio" name="rating" value="3" />
												&nbsp;
												<input type="radio" name="rating" value="4" />
												&nbsp;
												<input type="radio" name="rating" value="5" />
												&nbsp;<span><?php echo $entry_good; ?></span><br />
												</div>
												<?php echo $captcha; ?>
												<div class="buttons clearfix"><a id="button-review" class="btn btn-info"><?php echo $button_continue; ?></a></div>

											</div>
										<?php } else { ?>
										<?php echo $text_login; ?>
										<?php } ?>
									</form>

								</div>
								<?php endif; ?>

								<?php if ($tags) : ?>
								<div id="tab-4" class="<?php echo (!$description && !$attribute_groups && !$review_status ? 'tab-pane fade active in' : 'tab-pane fade'); ?>">
									<?php for ($i = 0; $i < count($tags); $i++) { ?>
									<?php if ($i < (count($tags) - 1)) { ?>
									<a class="btn btn-primary btn-sm" href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>
									<?php } else { ?>
									<a class="btn btn-primary btn-sm" href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>
									<?php } ?>
									<?php } ?>
								</div>
								<?php endif; ?>

								<?php if (!empty($html_product_tab)) : ?>
								<div id="tab-5" class="<?php echo (!$description && !$attribute_groups && !$review_status && !$tags ? 'tab-pane fade active in' : 'tab-pane fade'); ?>">
									<?php echo $html_product_tab; ?>
								</div>
								<?php endif; ?>

							</div>
				  </div>
				</div>
				<?php endif; ?>
				
				<?php //Tabs Type = 3 -------?>
				<?php if (isset($tabs_position) && $tabs_position == 3) : ?>
				<div class="producttab panel-group accordion-simple" id="product-accordion">
				
					<?php if ($description) : ?>
						<div class="panel">
							<div class="panel-heading">
								<a data-toggle="collapse" data-parent="#product-accordion" href="#product-description" class="title-head">
									<span class="arrow-up "></span>
									<?php echo $tab_description; ?>
								</a>
							</div>
							<div id="product-description" class="panel-collapse collapse in">
								<div class="panel-body">
									<?php echo $description; ?>
								</div>
							</div>
						</div>
					<?php endif; ?>
				
					<?php if ($attribute_groups) : ?>
					<div class="panel">
						<div class="panel-heading">
							<a data-toggle="collapse" data-parent="#product-accordion" href="#product-attribute" class="title-head collapsed">
								<span class="arrow-up "></span>
								<?php echo $tab_attribute; ?>
							</a>
						</div>
						<div id="product-attribute" class="panel-collapse collapse">
							<div class="panel-body">
								<table class="attribute">
									<?php foreach ($attribute_groups as $attribute_group) { ?>
									<thead>
									<tr>
										<td colspan="2"><?php echo $attribute_group['name']; ?></td>
									</tr>
									</thead>
									<tbody>
									<?php foreach ($attribute_group['attribute'] as $attribute) { ?>
									<tr>
										<td><?php echo $attribute['name']; ?></td>
										<td><?php echo $attribute['text']; ?></td>
									</tr>
									<?php } ?>
									</tbody>
									<?php } ?>
								</table>
							</div>
						</div>
					</div>
					<?php endif; ?>
				
					<?php if ($review_status) : ?>
					<div class="panel">
						<div class="panel-heading">
							<a data-toggle="collapse" data-parent="#product-accordion" href="#tab-review" class="title-head collapsed">
								<span class="arrow-up icon-arrow-up-4"></span>
								<?php echo $tab_review; ?>
							</a>
						</div>
						<div id="tab-review" class="panel-collapse collapse">
							<div class="panel-body">
								<form>
									<div id="review"></div>
									<h2 id="review-title"><?php echo $text_write; ?></h2>
									
									<?php if ($review_guest) { ?>
									<div class="contacts-form">
										<div class="form-group">
											<span class="icon icon-user"></span>
											<input type="text" name="name" class="form-control" value="<?php echo $entry_name; ?>" onblur="if (this.value == '') {this.value = '<?php echo $entry_name; ?>';}" onfocus="if(this.value == '<?php echo $entry_name; ?>') {this.value = '';}">
										</div>
										<div class="form-group">
											<span class="icon icon-bubbles-2"></span>
											<textarea class="form-control" name="text" onblur="if (this.value == '') {this.value = '<?php echo $entry_review; ?>';}" onfocus="if(this.value == '<?php echo $entry_review; ?>') {this.value = '';}"><?php echo $entry_review; ?></textarea>
										</div>
										<span style="font-size: 11px;"><?php echo $text_note; ?></span>
										<br />
										<br />
										<b><?php echo $entry_rating; ?></b> <span><?php echo $entry_bad; ?></span>&nbsp;
										<input type="radio" name="rating" value="1" />
										&nbsp;
										<input type="radio" name="rating" value="2" />
										&nbsp;
										<input type="radio" name="rating" value="3" />
										&nbsp;
										<input type="radio" name="rating" value="4" />
										&nbsp;
										<input type="radio" name="rating" value="5" />
										&nbsp;<span><?php echo $entry_good; ?></span><br />
										
										<?php echo $captcha; ?>
				
										<div class="buttons"><a id="button-review" class="btn btn-mega"><?php echo $button_continue; ?></a></div>
				
									</div>
									<?php } else { ?>
									<?php echo $text_login; ?>
									<?php } ?>
								</form>
							</div>
						</div>
					</div>
					<?php endif; ?>
					
					<?php  if ($tags) : ?>
						<div class="panel">
							<div class="panel-heading">
								<a data-toggle="collapse" data-parent="#product-accordion" href="#product-tags" class="title-head collapsed">
									<span class="arrow-up icon-arrow-up-4"></span>
									<?php echo (!empty($soconfig_lang[$lang]["tags_tab_title"]) ? $soconfig_lang[$lang]["tags_tab_title"] : 'TAGS'); ?>
								</a>
							</div>
							<div id="product-tags" class="panel-collapse collapse">
								<div class="panel-body">
									<?php for ($i = 0; $i < count($tags); $i++) { ?>
									<?php if ($i < (count($tags) - 1)) { ?>
									<a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>,
									<?php } else { ?>
									<a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>
									<?php } ?>
									<?php } ?>
								</div>
							</div>
						</div>
					<?php endif; ?>
				
					<?php if (!empty($html_product_tab) && !empty($tab_title)) : ?>
					<div class="panel">
						<div class="panel-heading">
							<a data-toggle="collapse" data-parent="#product-accordion" href="#product-custom" class="title-head collapsed">
								<span class="arrow-up icon-arrow-up-4"></span>
								<?php echo $tab_title; ?>
							</a>
						</div>
						<div id="product-custom" class="panel-collapse collapse">
							<div class="panel-body">
								<?php echo $html_product_tab; ?>
							</div>
						</div>
					</div>
					<?php endif; ?>
				
				</div>
				<?php endif; ?>
				<?php //End Tabs Type = 3 -------?>
			</div>
			<div class="bottom-product clearfix">
				<ul class="nav nav-tabs">
				<?php if (!empty($products)) : ?>
				  <li><a data-toggle="tab" href="#product-related"><?php echo $text_related; ?></a></li>
				<?php endif; ?>  
				  <li class="active"><a data-toggle="tab" href="#product-upsell">UPSELL PRODUCTS</a></li>
				</ul>

				<div class="tab-content">
				  	<div id="product-related" class="tab-pane fade">
				   		<?php 
						// TAB RELATED PRODUCT HORI 
						if (isset($related_status) && $related_status && $related_position =='horizontal') :
							if (file_exists(DIR_TEMPLATE . $theme . '/template/soconfig/related.php')) include(DIR_TEMPLATE.$theme.'/template/soconfig/related.php');
							else echo 'Not found';
						endif;
						?>
				  	</div>
				  	<div id="product-upsell" class="tab-pane fade in active">
				   		<?php echo $content_bottom; ?>
				  	</div>
				</div>
			</div>
			<?php
			if(isset($product_enablezoom) && $product_enablezoom) {?>
			<script type="text/javascript">
			$(document).ready(function() {
				var zoomCollection = '.large-image img';
				$( zoomCollection ).elevateZoom({
					<?php if( $product_zoommode != 'basic' ) { ?>
					zoomType        : "<?php echo isset($product_zoommode) ? $product_zoommode : 'basic';?>",
					<?php } ?>
					lensSize    :"<?php echo isset($product_zoomlenssize) ? $product_zoomlenssize : '300';?>",
					easing:true,
					gallery:'thumb-slider',
					cursor: 'pointer',
					galleryActiveClass: "active"
				});
				$('.large-image img').magnificPopup({
					items: [
					<?php if ($images) { ?>
						<?php foreach ($images as $image) { ?>{src: '<?php echo $image['popup']; ?>'},<?php } ?>
					<?php }else{ ?>
						<?php if ($thumb) { ?>{src: '<?php echo $popup; ?>'}<?php } ?>
					<?php } ?>
					],
					gallery: { enabled: true, preload: [0,2] },
					type: 'image',
					mainClass: 'mfp-fade',
					callbacks: {
						open: function() {
							<?php if ($images) { ?>
							var activeIndex = parseInt($('#thumb-slider .img.active').attr('data-index'));
							<?php }else{ ?>
							var activeIndex = 0;
							<?php } ?>
							var magnificPopup = $.magnificPopup.instance;
							magnificPopup.goTo(activeIndex);
						}
					}
				});
				
			});
					
			</script>
			<?php }else{?>
			<script type="text/javascript"><!--
			  $(document).ready(function() { 
				$('.thumb-slider .owl2-item').magnificPopup({
					items: [
					<?php if ($images) { ?>
						<?php foreach ($images as $image) { ?>{src: '<?php echo $image['popup']; ?>'},<?php } ?>
					<?php }else{ ?>
						<?php if ($thumb) { ?>{src: '<?php echo $popup; ?>'}<?php } ?>
					<?php } ?>
					],
					navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
					gallery: { enabled: true, preload: [0,2] },
					type: 'image',
					mainClass: 'mfp-fade',
					callbacks: {
						open: function() {
							$cur = this.st.el;
							<?php if ($images) { ?>
							var activeIndex = parseInt($cur.children(".thumbnail").attr('data-index'));
							<?php }else{ ?>
							var activeIndex = 0;
							<?php } ?>
							var magnificPopup = $.magnificPopup.instance;
							magnificPopup.goTo(activeIndex);
						}
					}
				});
			  });

			//--></script>
			<?php } ?>
		</div>
		
    </div>
	
	<?php //Column Right Block -------?>
	<?php echo $column_right; ?>
	<?php //End Column Right Block -------?>
		
	</div>
</div>



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
		data: $('#product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea'),
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
				 addProductNotice(json['title'], json['thumb'], json['success'], 'success');
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart  .total-shopping-cart').html(json['total'] );
					$('.text-danger').remove();
				}, 100);
				$('#cart > ul').load('index.php?route=common/cart/info ul li');
			}
		},
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
	});
});

//--></script> 
<script type="text/javascript"><!--
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
<script type="text/javascript"><!--
$('#review').delegate('.pagination a', 'click', function(e) {
  e.preventDefault();

    $('#review').fadeOut('slow');
    $('#review').load(this.href);
    $('#review').fadeIn('slow');
});

$('#review').load('index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');

$('#button-review').on('click', function() {
	$.ajax({
		url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
		type: 'post',
		dataType: 'json',
		data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : ''),
		beforeSend: function() {
			$('#button-review').button('loading');
		},
		complete: function() {
			$('#button-review').button('reset');
		},
		success: function(json) {
			$('.alert-success, .alert-danger').remove();
			
			if (json['error']) {
				$('#review').after('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}
			
			if (json['success']) {
				$('#review').after('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
				
				$('input[name=\'name\']').val('');
				$('textarea[name=\'text\']').val('');
				$('input[name=\'rating\']:checked').prop('checked', false);
			}
		}
	});
});

//--></script> 


<script type="text/javascript"><!--
	$(document).ready(function() {
		
		$('.product-options li.radio').click(function(){
			$(this).addClass(function() {
				if($(this).hasClass("active")) return "";
				return "active";
			});
			
			$(this).siblings("li").removeClass("active");
			$(this).parent().find('.selected-option').html('<span class="label label-success">'+ $(this).find('img').data('original-title') +'</span>');
		})
		
		// CUSTOM BUTTON
		$(".thumb-vertical-outer .next-thumb").click(function () {
			$( ".thumb-vertical-outer .lSNext" ).trigger( "click" );
		});
		
		$(".thumb-vertical-outer .prev-thumb").click(function () {
			$( ".thumb-vertical-outer .lSPrev" ).trigger( "click" );
		});

		$(".thumb-vertical-outer .thumb-vertical").lightSlider({
			item: 3,
			autoWidth: false,
			vertical:true,
			slideMargin: 10,
			verticalHeight:330,
            pager: false,
			controls: true,
            prevHtml: '<i class="fa fa-angle-up"></i>',
            nextHtml: '<i class="fa fa-angle-down"></i>',
			responsive: [
				{
					breakpoint: 1199,
					settings: {
						verticalHeight: 320,
						item: 3,
					}
				},{
					breakpoint: 1024,
					settings: {
						verticalHeight: 235,
						item: 2,
						slideMargin: 5,
					}
				},{
					breakpoint: 768,
					settings: {
						verticalHeight: 360,
						item: 3,
					}
				},{
					breakpoint: 480,
					settings: {
						verticalHeight: 110,
						item: 1,
					}
				}
				
			]
							
        });
		
		
		$("#thumb-slider .owl2-item").each(function() {
			$(this).find("[data-index='0']").addClass('active');
		});
		
		$('.thumb-video').magnificPopup({
		  type: 'iframe',
		  iframe: {
			patterns: {
			   youtube: {
				  index: 'youtube.com/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).
				  id: 'v=', // String that splits URL in a two parts, second part should be %id%
				  src: '//www.youtube.com/embed/%id%?autoplay=1' // URL that will be set as a source for iframe. 
					},
				}
			}
		});
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


<?php // Footer Blocks =========================================?>
<?php echo $footer; ?>

<div class="module today_deals">
	<h3 class="title-category">
		<?php echo isset($lang_todaysdeal) || !empty($lang_todaysdeal)  ? $lang_todaysdeal : 'Todays Deals' ; ?>
	</h3>
	
	<div class="modcontent ">
		<div class="deals_module_wrapper">
		<?php 
		foreach ($products as $idproduct =>$product) { 
			if (isset($product['special']) && $product['special']) {
			
		?>
		
			<div class="product-item-container">
				<div class="row">
					<div class="left-block col-lg-4 col-md-6">
						<div class="product-image-container ">
							<img  src="<?php echo $product['image_deal']; ?>"  title="<?php echo $product['name']; ?>" class="img-responsive" />
							
						</div>
						
						<!--countdown box-->
						<?php 
						if (file_exists(DIR_TEMPLATE . $theme . '/template/soconfig/countdown.php')) include(DIR_TEMPLATE.$theme.'/template/soconfig/countdown.php');
						else echo 'Not found';
						
						?>
						<!--end countdown box-->
						
						<!--New Label-->
						<div class="box-label">
						<?php if (!isset($new_status) || ($new_status)) : ?>
							<?php
							$day_range = 10;
							if ($days == '') $day = $day_range;
							else $day = $days;
							$day_number_to_range = date( "Y-m-d" ,  strtotime("-$day day")  );
							if ($product['date_available'] >= $day_number_to_range) :
							?>
								<span class="label-product label-new"><?php echo (isset($new_text) && is_string($new_text)) ? $new_text : 'NEW'; ?></span>
							<?php endif; ?>
							<?php endif; ?>
					
							<!--Sale Label-->
							<?php if (!isset($sale_status) || ($sale_status )) : ?>
							<?php if ($product['price'] && $product['special']) : ?>
								<span class="label-product label-sale">
									<?php echo (isset($sale_text) && is_string($sale_text)) ? $sale_text : 'SALE'; ?>
									<?php  if(!isset($discount_status) || $discount_status) echo $product['discount']; ?>    
								</span>
							<?php endif; ?>
						<?php endif; ?>
						</div>
					</div>
					<!-- end left block -->
					
					<div class="right-block col-lg-8 col-md-6">
						<div class="caption">
							<h3 class="title-product"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h3>		
							<div class="row">
								
								<div class="col-sm-12">
									<?php if (isset($rating_status) && $rating_status!= 0) : ?>
									<div class="ratings">
										<div class="rating-box">
										<?php for ($i = 1; $i <= 5; $i++) { ?>
										<?php if ($product['rating'] < $i) { ?>
										<span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
										<?php } else { ?>
										<span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
										<?php } ?>
										<?php } ?>
										</div>
									</div>
									<?php endif; ?>
									<?php if ($product['price']) : ?>
									<div class="price">
										<span class="price-new">
											<?php echo $product['special']; ?>
										</span>
										<span class="price-old">
											<?php echo $product['price']; ?>
										</span>  
									</div>
									<?php endif; ?>
								</div>
								
								
							</div>
							
							<div class="short_description clearfix">
								<p><?php echo $product['description']; ?></p>
							</div>
						</div>
					   
						
						  <div class="clearfix">
							<button class="btn btn-default addToCart" type="button" data-toggle="tooltip" title="<?php echo $button_cart; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><span><?php echo $button_cart; ?></span></button>
							<button class="btn btn-default wishlist" type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart-o"></i></button>
							<button class="btn btn-default compare" type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-retweet"></i></button>
						  </div>
					</div><!-- right block -->
				</div>	
			</div>
        
		
        <?php 
			}
		} ?>
		</div>
	</div>
	
</div>

<script type="text/javascript"><!--
	$(document).ready(function() {
		var $nav = $(".deals_module_wrapper");
		$nav.each(function () {
			$(this).owlCarousel2({
				nav:true,
				dots: false,
				slideBy: 1,
				margin:0,
				<?php if(isset($direction) && $direction =='rtl')  echo 'rtl: true,' ;?>
				navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"> </i>'],
				responsive:{
					0:{items:1},
					600:{items:1},
					1000:{items:1}
				}
			});
		})
		
	});
//--></script>
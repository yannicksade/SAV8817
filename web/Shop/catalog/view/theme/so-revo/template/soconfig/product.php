<div class="product-layout col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="product-item-container">
		<div class="left-block">
			   <div class="product-image-container <?php if($secondimg =='2' && isset($product['thumb2'])){ echo "second_img";} ?> ">
					<img  src="<?php echo $product['thumb']; ?>"  title="<?php echo $product['name']; ?>" class="img-1 img-responsive" />
					<?php if( $secondimg =='2' && isset($product['thumb2']) && !empty($product['thumb2'])) :?>
					<img data-src="<?php echo $product['thumb2']; ?>" src="<?php echo lazyload($product['thumb2']); ?>"  alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-2 img-responsive" />
					<?php endif;?>
				</div>
				<div class="box-label">
					<!--New Label-->
					<?php if (!isset($new_status) || ($new_status)) : ?>
					<?php
					$day_range = 10;
					if ($days == '') $day = $day_range;
					else $day = $days;
					$day_number_to_range = date( "Y-m-d" ,  strtotime("-$day day")  );
					if ($product['date_available'] >= $day_number_to_range) :
					?>
						<span class="label-product label-new"><?php echo (isset($new_text) ? $new_text : 'NEW'); ?></span>
					<?php endif; ?>
					<?php endif; ?>
				
					<!--Sale Label-->
					<?php if (($sale_status )) : ?>
					<?php if ($product['special']) : ?>
						<span class="label-product label-sale">
							
							<?php  if($discount_status) echo $product['discount']; ?>    
						</span>
					<?php endif; ?>
					<?php endif; ?>
				</div>  
				<!-- QUICK VIEW -->
				<?php if ( $quick_status ) : ?>
						<a class="quickview iframe-link visible-lg btn-button" data-fancybox-type="iframe"  href="<?php echo $our_url->link('extension/soconfig/quickview','product_id='.$product['product_id']);?>"> <i class="fa fa-search"></i> </a>
				<?php endif; ?>
			   
				
				
		</div>
		
		<div class="right-block">
			<div class="caption">
			   <h4><a class="preview-image" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
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
				
			   <?php if (!isset($product_catalog_mode) || $product_catalog_mode != 1) : ?>
			   <div class="price">
				  <?php if ($product['price']) : ?>
					 <?php if (!$product['special']) { ?>
						<span class="price-new"><?php echo $product['price']; ?></span>
					 <?php } else { ?>
						<span class="price-old"><?php echo $product['price']; ?></span>
						<span class="price-new"><?php echo $product['special']; ?></span>
					 <?php } ?>
				  <?php endif; ?>
			   </div>
			   <?php endif; ?>
			</div>
			<!-- BOX BUTTON			 -->
			<div class="button-group">
				
				<!-- WISHLIST -->
				<button class="wishlist btn-button" type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart-o"></i></button>
				<!-- CART -->
				<button class="addToCart btn-button" type="button" data-toggle="tooltip" title="<?php echo $button_cart; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>');"><span><?php echo $button_cart; ?></span></button>
				<!-- COMPARE -->
				<button class="compare btn-button" type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-retweet"></i></button>
				
			</div> 
		</div>
  </div>
	
</div>
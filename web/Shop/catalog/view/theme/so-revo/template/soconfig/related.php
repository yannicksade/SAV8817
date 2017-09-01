<?php if ($products):?>
	<?php if($related_position =='horizontal') :?>
    <div class="clearfix module">
			<div class="products-category">
            <div class="releate-products products-list grid">
				<!-- Products list -->
				<?php foreach ($products as $product) : ?>
                        <div class="product-layout">
						  <div class="product-item-container">
								<div class="left-block">
									<div class="product-image-container ">
										<img  src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-1 img-responsive" />
										<?php if( $secondimg =='2' && isset($product['thumb2']) && !empty($product['thumb2'])) :?>
										<img data-src="<?php echo $product['thumb2']; ?>" src="<?php echo lazyload($product['thumb2']); ?>"  alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-2 img-responsive" />
										<?php endif;?>
									</div>
									<!-- QUICK VIEW -->
									<?php if ( $quick_status ) : ?>
										<a class="quickview iframe-link visible-lg btn-button" data-fancybox-type="iframe"  href="<?php echo $our_url->link('extension/soconfig/quickview','product_id='.$product['product_id']);?>"> <i class="fa fa-search"></i> </a>
									<?php endif; ?>
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
								<!-- BOX BUTTON -->
								
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
												<span class="price-new"><?php echo $product['special']; ?></span>
												<span class="price-old"><?php echo $product['price']; ?></span>
												
											 <?php } ?>
										  <?php endif; ?>
									   </div>
									   <?php endif; ?>
									</div>
									<div class="button-group">
										<!-- CART -->
										<button class="addToCart btn-button" type="button" data-toggle="tooltip" title="<?php echo $button_cart; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>');"> <span><?php echo $button_cart; ?></span></button>
										<!-- WISHLIST -->
										<button class="wishlist btn-button" type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart"></i></button>
										<!-- COMPARE -->
										<button class="compare btn-button" type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-refresh"></i></button>
										
									</div> 
								</div>
						  </div>
                            
                        </div>
             <?php endforeach; ?>
            </div>
			</div>
    </div>
	<script>// <![CDATA[
	jQuery(document).ready(function($) {
			$('.releate-products').owlCarousel2({
				pagination: false,
				center: false,
				nav: true,
				dots: false,
				loop: false,
				margin: 30,
				navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
				slideBy: 1,
				<?php if($direction=='rtl'):?> rtl:true, <?php endif;?>
				autoplay: false,
				autoplayTimeout: 2500,
				autoplayHoverPause: true,
				autoplaySpeed: 800,
				startPosition: 0, 
				responsive:{
					0:{
						items:1
					},
					480:{
						items:<?php echo $product_related_column_xs?>
					},
					768:{
						items:<?php echo $product_related_column_sm?>
					},
					991:{
						items:<?php echo $product_related_column_md?>
					},
					1200:{
						items:<?php echo $product_related_column_lg?>
					}
				}
			});
			 
		});
	// ]]></script>
	<?php else:?>
	<div class="module releate-horizontal">
            <h3 class="modtitle"><span><?php echo $text_related; ?></span></h3>
            <div class="releate-products  <?php if(count($products) >4) echo "releate-products-slider";?>">
				<!-- Products list -->
				<?php $j=0; $total_pros = count($products); $k = 0; $nb_row = 4; 
                foreach ($products as $product) : $k++;
				if ($k%$nb_row == 1 || $nb_row == 1) { ?>
				<div class="product-item-container">	
                <?php } ?> 
				
				<div class="item-element clearfix">
					<div class="image">
						<a  href="<?php echo $product['href']; ?>">
							<img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" />
						</a>
					</div> 
					<div class="caption">
						<h4><a class="preview-image" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
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
						<?php if (!isset($product_catalog_mode) || $product_catalog_mode != 1) : ?>
						<div class="price">
							   <?php if ($product['price']) : ?>
								  <?php if (!$product['special']) { ?>
									 <span class="price-new"><?php echo $product['price']; ?></span>
								  <?php } else { ?>
									<span class="price-new"><?php echo $product['special']; ?></span>
									 <span class="price-old"><?php echo $product['price']; ?></span>
									 
								  <?php } ?>
							   <?php endif; ?>
						</div>
						<?php endif; ?>
					</div>
					
			    </div>
							
                <?php if ($k%$nb_row == 0 || $k == $total_pros) { ?>
				</div>
			<?php } ?>	        
             <?php endforeach; ?>

            </div>

    </div>
	<script>// <![CDATA[
	$(document).ready(function($) {
		$('.releate-products-slider').owlCarousel2({
			pagination: false,
			center: false,
			nav: true,
			dots: false,
			loop: false,
			items: 1,
			margin: 30,
			navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
			slideBy: 1,
			<?php if($direction=='rtl'):?> rtl:true, <?php endif;?>
			autoplay: false,
			autoplayTimeout: 2500,
			autoplayHoverPause: true,
			autoplaySpeed: 800,
			startPosition: 0, 
			
		});
		 
	});
	// ]]></script>
	<?php endif;?>
<?php endif; ?>



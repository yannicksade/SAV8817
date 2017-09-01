<div class="products-list row <?php echo $listingType ?>">
	<?php 
	require_once(DIR_SYSTEM . 'soconfig/classes/soconfig.php');
	$this->soconfig = new Soconfig($registry);
	
	function lazyload($src_img){
		global $scroll_animation;
		
		return ($scroll_animation==1) ? 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' : $src_img;
	}
	$lazyload_text = ($scroll_animation) ? 'lazy' : '';
	$device_res  ='';
	
	if($this->soconfig->get_route() =='product/category' || $this->soconfig->get_route()== 'extension/module/so_filter_shop_by/filter_data'){
		foreach ($devices as $subfix => $device) {
			if(isset($soconfig_pages["product_catalog_column_".$subfix]) && $soconfig_pages["product_catalog_column_".$subfix] !=''){
				if ( $soconfig_pages["product_catalog_column_".$subfix]==5){
					$device_res .= 'col-'.$subfix.'-15 ';
				} else{
					$device_res .=  'col-'.$subfix.'-'.ceil(12/$soconfig_pages["product_catalog_column_".$subfix] ).' ';
				}
			}
		}
			
	}else{
		foreach ($devices as $subfix => $device) {
			if(isset($soconfig_pages["other_catalog_column_".$subfix]) && $soconfig_pages["other_catalog_column_".$subfix] !=''){
				if ($soconfig_pages["other_catalog_column_".$subfix]==5){
					$device_res .= 'col-'.$subfix.'-15 ';
				} else{
				  $device_res .=  'col-'.$subfix.'-'.ceil(12/$soconfig_pages["other_catalog_column_".$subfix] ).' ';
				}
			}
			
		}
	}
	
	foreach ($products as $idproduct =>$product) {
	?>
	
	<?php if($listingType =='grid'){?>
	<div class="product-layout <?php echo $device_res;?>">
		<div class="product-item-container">
			<div class="left-block">
				<div class="product-image-container <?php echo $lazyload_text?> <?php if($secondimg =='2' && isset($product['thumb2'])){ echo "second_img";} ?> ">
					<a href="<?php echo $product['href']; ?>" title="<?php echo $product['name']; ?>">
					<img data-src="<?php echo $product['thumb']; ?>" src="<?php echo lazyload($product['thumb']); ?>"  title="<?php echo $product['name']; ?>" class="img-1 img-responsive" />
					<?php if( $secondimg =='2' && isset($product['thumb2']) && !empty($product['thumb2'])) :?>
					<img data-src="<?php echo $product['thumb2']; ?>" src="<?php echo lazyload($product['thumb2']); ?>"  alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-2 img-responsive" />
					<?php endif;?>
					</a>
				</div>
				<!--COUNTDOWN BOX-->
				<?php
					if (file_exists(DIR_TEMPLATE . $theme . '/template/soconfig/countdown.php')) include(DIR_TEMPLATE.$theme.'/template/soconfig/countdown.php');
					else echo 'Not found';
				?>
				<?php if (!isset($quick_status) || $quick_status ) : ?>
					<a class="quickview iframe-link visible-lg btn-button" data-toggle="tooltip" title="<?php echo isset($quick_view_text) && is_string($quick_view_text) ? $quick_view_text : 'Quick View'; ?>" data-fancybox-type="iframe"  href="<?php echo $our_url->link('extension/soconfig/quickview','product_id='.$product['product_id']);?>"> <i class="fa fa-eye"></i> </a>
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
					<span class="label-product label-new"><?php echo (isset($new_text) && is_string($new_text)) ? $new_text : 'NEW'; ?></span>
				<?php endif; ?>
				<?php endif; ?>
			
				<!--Sale Label-->
				<?php if (!isset($sale_status) || ($sale_status )) : ?>
				<?php if ($product['price'] && $product['special']) : ?>
					<span class="label-product label-sale">
						
						<?php  if(!isset($discount_status) || $discount_status) echo $product['discount']; ?> 
					</span>
				<?php endif; ?>
				<?php endif; ?>
			</div>  
			
			
			<div class="right-block">
				<div class="caption">
					<h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>		
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
					
					<?php if ($product['price']) { ?>
					<div class="price">
						<?php if (!$product['special']) { ?>
							<span class="price-new"><?php echo $product['price']; ?></span>
						<?php } else { ?>
							<span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
					<?php } ?>
					</div>
					<?php } ?>
					
					<div class="description <?php if (!isset($lstdescription_status) || $lstdescription_status != 1) : ?> hidden <?php endif; ?>">
						<p><?php echo $product['description']; ?></p>
					</div>
				</div>
				<div class="button-group">
					<button class="addToCart" type="button" data-toggle="tooltip" title="<?php echo $button_cart; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><span><?php echo $button_cart; ?></span></button>
					<button class="wishlist btn-button" type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart-o"></i></button>
					
					<button class="compare btn-button" type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-retweet"></i></button>
					
				</div>
			</div>
		</div>
	</div>
	<?php //Clearfix fluid grid layout
		if($this->soconfig->get_route()=='product/category'){
			foreach ($devices as $subfix => $device) {
				$id = $idproduct+1;
				if(isset($soconfig_pages["product_catalog_column_".$subfix])  && $soconfig_pages["product_catalog_column_".$subfix] !=''){
					if(($id  % $soconfig_pages["product_catalog_column_".$subfix]) == 0) {
						$id = 1;
						echo '<div class="clearfix visible-'.$subfix.'-block"></div>';
						
					}
				}
			}
		}else{
			foreach ($devices as $subfix => $device) {
				$id = $idproduct+1;
				if(isset($soconfig_pages["other_catalog_column_".$subfix]) && $soconfig_pages["other_catalog_column_".$subfix] !=''){
					if(($id  % $soconfig_pages["other_catalog_column_".$subfix]) == 0) {
						$id = 1;
						echo '<div class="clearfix visible-'.$subfix.'-block"></div>';
						
					}	
				}				
			}
		}
		
	?>
	
	<?php }else {?>
	
	<div class="product-list product-layout col-xs-12">
		<div class="product-item-container">
			<div class="left-block col-md-4">
				<div class="product-image-container <?php echo $lazyload_text?> <?php if($secondimg =='2' && isset($product['thumb2'])){ echo "second_img";} ?> ">
					<img data-src="<?php echo $product['thumb']; ?>" src="<?php echo lazyload($product['thumb']); ?>"  title="<?php echo $product['name']; ?>" class="img-1 img-responsive" />
					<?php if( $secondimg =='2' && isset($product['thumb2']) && !empty($product['thumb2'])) :?>
					<img data-src="<?php echo $product['thumb2']; ?>" src="<?php echo lazyload($product['thumb2']); ?>"  alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-2 img-responsive" />
					<?php endif;?>
					
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
						<?php if (!isset($sale_status) || ($sale_status )) : ?>
						<?php if ($product['price'] && $product['special']) : ?>
							<span class="label-product label-sale">
								
								<?php  if(!isset($discount_status) || $discount_status) echo $product['discount']; ?>    
							</span>
						<?php endif; ?>
						<?php endif; ?>
					</div>  
				</div>
				
				<!--Countdown box-->
				<?php 
					if (file_exists(DIR_TEMPLATE . $theme . '/template/soconfig/countdown.php')) include(DIR_TEMPLATE.$theme.'/template/soconfig/countdown.php');
					else echo 'Not found';
				?>
				<!--End countdown box-->
				
					
				<?php if (!isset($quick_status) || $quick_status != 0) : ?>
					<a class="quickview iframe-link visible-lg" data-fancybox-type="iframe"  href="<?php echo $our_url->link('extension/soconfig/quickview','product_id='.$product['product_id']);?>"><i class="fa fa-eye"></i></a>
				<?php endif; ?>
			</div>
			<!-- end left block -->
			
			<div class="right-block col-md-8">
				<!-- NAME -->
				<div class="caption">
					<h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>		
					<?php if (isset($rating_status) && $rating_status!= 0) : ?>
					<!-- RATING -->
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
					<!-- DESCRIP -->
					<div class="description">
						<p><?php echo $product['description']; ?></p>
					</div>
					<!-- PRICE -->
					<?php if ($product['price']) { ?>
					<div class="price">
						  <?php if (!$product['special']) { ?>
						  <span class="price-new"><?php echo $product['price']; ?></span>
						  <?php } else { ?>
						  <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
						  <?php } ?>
						  
					</div>
					<?php } ?>
					
				</div>
				
			  <div class="button-group">
				<button class="addToCart font-sn" type="button" data-toggle="tooltip" title="<?php echo $button_cart; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"> <span><?php echo $button_cart; ?></span></button>
				<button class="wishlist btn-button" type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart-o"></i></button>
				<button class="compare btn-button" type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-retweet"></i></button>
			  </div>
		   
			</div><!-- right block -->

		</div>
	</div>
	<?php }?>
	
<?php } ?>
</div>
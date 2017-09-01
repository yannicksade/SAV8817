<?php
 if ($type_show == 'slider') { ?>
		<div class="ltabs-items-inner owl2-carousel  ltabs-slider ">
<?php }else { ?>
		<div class="ltabs-items-inner <?php echo  $type_show == 'loadmore' ? $class_ltabs. ' '. $effect : '  ' ;?>">
<?php } ?>
<?php 
	if (!empty($child_items)) {
		$i = 0 ;$k = isset($rl_loaded ) ? $rl_loaded : 0; $count = count($child_items);
		foreach ($child_items as $product) {
			$i++; $k++; ?>
			
			<?php if  ($type_show == 'slider' && ($i % $nb_rows == 1 || $nb_rows == 1)) { ?>
				<div class="ltabs-item ">
			<?php } 
			if ($type_show == 'loadmore'){ ?>
				<div class="ltabs-item new-ltabs-item" >
			<?php } ?>
			
				<div class="item-inner product-thumb transition">
					<div class="image">
						<?php if($product_image){ ?>
							<?php if ($product['special'] && $display_sale) : ?>
								<span class="label label-sale"><?php echo $objlang->get('text_sale'); ?></span>
							<?php endif; ?>
							<?php if ($product['productNew'] && $display_new) : ?>
								<span class="label label-new"><?php echo $objlang->get('text_new'); ?></span>
							<?php endif; ?>
							<a class="lt-image" 
							   href="<?php echo $product['href'] ?>" target="<?php echo $item_link_target ?>"
							   title="<?php echo $product['name'] ?>">
								<?php if($product_image_num ==2){?>
									<img src="<?php echo $product['thumb']?>" class="img-thumb1" alt="<?php echo $product['name'] ?>">
									<img src="<?php echo $product['thumb2']?>" class="img-thumb2" alt="<?php echo $product['name'] ?>">
								<?php }else{?>
									<img src="<?php echo $product['thumb']?>" alt="<?php echo $product['name'] ?>">
								<?php }?>
							</a>
						<?php }?>
					</div>
				<?php if($display_title || $display_description || $display_price){?>
					<div class="caption">
						<?php if ($display_title) { ?>
							<h4>
								<a href="<?php echo $product['href'] ?>" 
								   title="<?php echo $product['name'] ?>" target="<?php echo $item_link_target ?>">
								   <?php  echo $product['name_maxlength'];?>
								</a>
							</h4>
						<?php } ?>
							<?php if ($display_rating) { ?>
							<div class="rating">
							  <?php for ($j = 1; $j <= 5; $j++) { ?>
							  <?php if ($product['rating'] < $j) { ?>
							  <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
							  <?php } else { ?>
							  <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>
							  <?php } ?>
							  <?php } ?>
							</div>
							<?php } ?>
							<?php if ($display_description) { ?>
								<?php echo  html_entity_decode($product['description_maxlength']); ?>
							<?php }
							?>
							<?php if ($product['price'] && $display_price) { ?>
							<p class="price">
							  <?php if (!$product['special']) { ?>
							  <?php echo $product['price']; ?>
							  <?php } else { ?>
							  <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
							  <?php } ?>
							  <?php if ($product['tax']) { ?>
							  <span class="price-tax"><?php echo $objlang->get('text_tax'); ?> <?php echo $product['tax']; ?></span>
							  <?php } ?>
							</p>
							<?php } ?>
						
					</div>
				<?php } if($display_add_to_cart || $display_wishlist || $display_compare){?>
					<div class="button-group">
					<?php if($display_add_to_cart){?>
						<button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $objlang->get('button_cart'); ?></span></button>
					<?php } if($display_wishlist){?>
						<button type="button" data-toggle="tooltip" title="<?php echo $objlang->get('button_wishlist'); ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart"></i></button>
					<?php } if($display_compare){?>
						<button type="button" data-toggle="tooltip" title="<?php echo $objlang->get('button_compare'); ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button>
					<?php }?>
					</div>
				<?php }?>
				</div>
			<?php if  ($type_show == 'slider' && ($i % $nb_rows == 0 || $i == $count)) { ?>	
				</div>
			<?php } 
			if ($type_show == 'loadmore'){ ?>
				</div>
			<?php } ?>

			<?php 
			if($type_show == 'loadmore') {
				$clear = 'clr1';
				if ($k % 2 == 0) $clear .= ' clr2';
				if ($k % 3 == 0) $clear .= ' clr3';
				if ($k % 4 == 0) $clear .= ' clr4';
				if ($k % 5 == 0) $clear .= ' clr5';
				if ($k % 6 == 0) $clear .= ' clr6';
				?>
				<div class="<?php echo $clear; ?>"></div>
			<?php  
			} ?>
		<?php
		}
	} ?>
</div>
<?php
 if ($type_show == 'slider') { ?>
<script type="text/javascript">
	jQuery(document).ready(function($){
		var $tag_id = $('#<?php echo $tag_id; ?>'), 
		parent_active = 	$('.items-category-<?php echo $tab_id; ?>', $tag_id),
		total_product = parent_active.data('total'),
		tab_active = $('.ltabs-items-inner',parent_active),
		nb_column0 = <?php echo $nb_column0; ?>,
		nb_column1 = <?php echo $nb_column1; ?>,
		nb_column2 = <?php echo $nb_column2; ?>,
		nb_column3 = <?php echo $nb_column3; ?>,
		nb_column4 = <?php echo $nb_column4; ?>;
		tab_active.owlCarousel2({
			rtl: <?php echo $direction?>,
			nav: <?php echo $display_nav ; ?>,
			dots: false,	
			margin: 0,
			loop:  <?php echo $display_loop ; ?>,
			autoplay: <?php echo $autoplay; ?>,
			autoplayHoverPause: <?php echo $pausehover ; ?>,
			autoplayTimeout: <?php echo $autoplayTimeout ; ?>,
			autoplaySpeed: <?php echo $autoplaySpeed ; ?>,
			mouseDrag: <?php echo  $mousedrag; ?>,
			touchDrag: <?php echo $touchdrag; ?>,
			navRewind: true,
			navText: [ '', '' ],
			rtl: <?php echo $direction; ?>,			
		    responsive: {
				0: {
					items: nb_column4,
					nav: total_product <= nb_column4 ? false : ((<?php echo $display_nav ; ?>) ? true: false),
				},
				480: {
					items: nb_column3,
					nav: total_product <= nb_column3 ? false : ((<?php echo $display_nav ; ?>) ? true: false),
				},
				768: {
					items: nb_column2,
					nav: total_product <= nb_column2 ? false : ((<?php echo $display_nav ; ?>) ? true: false),
				},
				992: {
					items: nb_column1,
					nav: total_product <= nb_column1 ? false : ((<?php echo $display_nav ; ?>) ? true: false),
				},
				1200: {
					items: nb_column0,
					nav: total_product <= nb_column0 ? false : ((<?php echo $display_nav ; ?>) ? true: false),
				},
			}
		});
	});
</script>
<?php } ?>

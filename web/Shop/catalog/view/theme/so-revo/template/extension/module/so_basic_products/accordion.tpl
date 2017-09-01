<?php
$tag_id = 'so_basic_products_'.$moduleid.'_'.rand().time();
$class_respl0 = 'preset00-'.$nb_column0.' preset01-'.$nb_column1.' preset02-'.$nb_column2.' preset03-'.$nb_column3.' preset04-'.$nb_column4;
?>
<?php
	$icon_class=strstr($class_suffix,'fa-');
	$class_suffix_after=str_replace($icon_class, '', $class_suffix)
?>
<div class="module <?php echo $class_suffix_after; ?>">
	<?php if($disp_title_module) {?>
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
	<div class="so-basic-product" id="<?php echo $tag_id ?>">
		<?php if(!empty($products)){?>
		<div class="item-wrap row cf <?php echo $class_respl0; ?>">
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
					
						<?php $j = 0; foreach($products as $product)  { $j++; ?>
						
						
						<div class="panel panel-default">
							<div class="panel-heading" role="tab" id="heading<?php echo $j;?>">
							<?php if($display_title){ ?>
								<a  class="panel-title" role="button" data-toggle="collapse"
									   data-parent="#accordion" href="#collapse<?php echo $j;?>"
									   aria-expanded="true" aria-controls="collapse<?php echo $j;?>">
									<span class="counter"><?php echo $j;?></span>
									<span class="title">
									   <?php echo html_entity_decode($product['name_maxlength']); ?>
									</span>
								</a>
							<?php } ?>
							 
							</div>
							<div id="collapse<?php echo $j;?>" class="panel-collapse collapse <?php if ($j==1){echo "in";}?>" role="tabpanel" aria-labelledby="heading<?php echo $j;?>">
							  <div class="panel-body">
								<div class="item-element ">
									<div class="item-inner">
										<div class="product-thumb transition">
										<?php if($product_image) { ?>
										  <div class="image"><a href="<?php echo $product['href'];?>" target="<?php echo $item_link_target;?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a></div>
										<?php } ?>
										<?php if($display_add_to_cart || $display_wishlist || $display_compare)
										{
										?>
											<div class="button-group">
											<?php if ($display_wishlist) { ?>
												<button class="wishlist btn-button" type="button" data-toggle="tooltip" title="<?php echo $objlang->get('button_wishlist'); ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart"></i></button>
											<?php } ?>
											<?php if ($display_add_to_cart) { ?>
												<button  class="addToCart btn-button" type="button" data-toggle="tooltip" title="<?php echo $objlang->get('button_cart'); ?>"  onclick="cart.add('<?php echo $product['product_id']; ?>');"><?php echo $objlang->get('button_cart'); ?></button>
											<?php } ?>
											
											<?php if ($display_compare) { ?>
												<button class="compare btn-button" type="button" data-toggle="tooltip" title="<?php echo $objlang->get('button_compare'); ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-refresh"></i></button>
											<?php } ?>
											</div>
										<?php 
										}
										?>
										<?php if($display_title || $display_description || $display_price)
										{
										?>
										<div class="caption">
											<h4 class="title-product">
												<a 
													href="<?php echo $product['href']; ?>"
													target="<?php echo $item_link_target;?>">
													<?php echo html_entity_decode($product['name']); ?>
												 </a>
											</h4>
											
											<?php if (isset($product['rating'])) { ?>
											<div class="ratings">
											  <?php for ($i = 1; $i <= 5; $i++) { ?>
											  <?php if ($product['rating'] < $i) { ?>
											  <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
											  <?php } else { ?>
											  <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>
											  <?php } ?>
											  <?php } ?>
											</div>
											<?php } ?>
											<?php if($display_description){ ?>
												<?php echo html_entity_decode($product['description_maxlength']); ?>
											<?php } ?>
											
											<?php if ($display_price) { ?>
												<?php if ($product['price']) { ?>
												<p class="price">
												  <?php if (!$product['special']) { ?>
												  <span class="price-new">	<?php echo $product['price']; ?></span>
												  <?php } else { ?>
												  <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
												  <?php } ?>
												  <?php if ($product['tax']) { ?>
												   <span class="price-tax hidden"><?php echo $objlang->get('text_tax'); ?> <?php echo $product['tax']; ?></span>
												  <?php } ?>
												</p>
												<?php } ?>
											<?php } ?>
											</div>
										<?php 
										}
										?>
											
										</div>
									</div>
								 </div>

							  </div>
							</div>
						  </div>
						
						
						
						 <?php
					   $clear = 'clr1';
					   if ($j % 2 == 0) $clear .= ' clr2';
					   if ($j % 3 == 0) $clear .= ' clr3';
					   if ($j % 4 == 0) $clear .= ' clr4';
					   if ($j % 5 == 0) $clear .= ' clr5';
					   if ($j % 6 == 0) $clear .= ' clr6';
					   ?>
					   <div class="<?php echo $clear; ?>"></div>
						<?php 
						} ?>
					
				</div>
		</div>
		<?php }else{ 
				echo $objlang->get('text_noproduct');
			}?>
	</div>
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

<script type="text/javascript"><!--
jQuery(document).ready(function($) {
	$("div.panel-group .panel").each(function() {
		if($(this).index() == 0) {
			$(this).find(".panel-heading").addClass('active');
		}
		
		var ua = navigator.userAgent,
		event = (ua.match(/iPad/i)) ? "touchstart" : "click";
		$(this).children(".panel-heading").bind(event, function() {
			$(this).addClass(function() {
				if($(this).hasClass("active")) return "";
				return "active";
			});
	
			
			$(this).parent().siblings(".panel").find(".active").removeClass("active");
		});
	});
	
	
	
});
//--></script>
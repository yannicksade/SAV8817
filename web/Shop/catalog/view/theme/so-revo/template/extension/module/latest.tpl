<?php 
	$lazyload_status = $scroll_animation;
	$lazyload_text = ($lazyload_status == 1 )? 'lazy' : '';
?>
<div class="clearfix module">
	<h3 class="modtitle"><?php echo $heading_title; ?></h3>
	<div class="products-list grid">
	 
		<?php foreach ($products as $product) {
			$thumb = ($lazyload_status == 1) ? 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' : $product['thumb'];
		?>
			<?php include(DIR_TEMPLATE.$theme.'/template/soconfig/product.php');?>
		<?php } ?>
	  
	</div>
</div>
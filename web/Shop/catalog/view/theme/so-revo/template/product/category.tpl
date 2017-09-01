<?php echo $header; ?>

<?php
//Variables Category
$devices = array('lg' => ' Desktops','md' => ' Desktops','sm' => ' Tablets','xs' => ' Phones',);
$product_catalog_refine_col_lg = isset($product_catalog_refine_col_lg) ? $product_catalog_refine_col_lg : '';
$product_catalog_refine_col_md = isset($product_catalog_refine_col_md) ? $product_catalog_refine_col_md : '';
$product_catalog_refine_col_sm = isset($product_catalog_refine_col_sm) ? $product_catalog_refine_col_sm : '';
$product_catalog_refine_col_xs = isset($product_catalog_refine_col_xs) ? $product_catalog_refine_col_xs : '';
$product_catalog_column_lg = isset($product_catalog_column_lg) ? $product_catalog_column_lg : '';
$product_catalog_column_md = isset($product_catalog_column_md) ? $product_catalog_column_md : '';
$product_catalog_column_sm = isset($product_catalog_column_sm) ? $product_catalog_column_sm : '';
$product_catalog_column_xs = isset($product_catalog_column_xs) ? $product_catalog_column_xs : '';

$soconfig_pages  = array(
	'product_catalog_refine_col_lg'=>$product_catalog_refine_col_lg,
	'product_catalog_refine_col_md'=>$product_catalog_refine_col_md,
	'product_catalog_refine_col_sm'=>$product_catalog_refine_col_sm,
	'product_catalog_refine_col_xs'=>$product_catalog_refine_col_xs,
	'product_catalog_column_lg'=>$product_catalog_column_lg,
	'product_catalog_column_md'=>$product_catalog_column_md,
	'product_catalog_column_sm'=>$product_catalog_column_sm,
	'product_catalog_column_xs'=>$product_catalog_column_xs,
);

$short_by = !empty ($pagination) ? 'col-md-6 col-sm-8 col-xs-12' : 'col-md-10 col-sm-12';

/*CHECK COOKIE LISTINGTYPE*/	
if(isset($_COOKIE["listingType"])) $listingType = $_COOKIE["listingType"];
else $listingType =  isset($product_catalog_mode) && $product_catalog_mode ? 'list' : 'grid';
?>
<div class="container page-category">
	 <!-- BREADCRUMB -->
	<ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
	<div class="row">
	<?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-md-6 col-sm-4'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-md-9 col-sm-12 col-xs-12'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
	
    <div id="content" class="<?php echo $class; ?>">
		<a href="javascript:void(0)" class="open-sidebar hidden-lg hidden-md"><i class="fa fa-bars"></i><?php echo $text_sidebar; ?></a>
		<div class="sidebar-overlay "></div>
        <div class="products-category">
			<!--// Begin  Today Deals-->
			<?php if ($products) {
					$special_info = 0;
					foreach ($products as $product) {($product['special'] != false) ? $special_info ++ : '';};
					if($special_info && $deals_today) :
						if (file_exists(DIR_TEMPLATE . $theme . '/template/soconfig/category_deals.tpl')) {
							include(DIR_TEMPLATE.$theme.'/template/soconfig/category_deals.tpl');
						}else echo 'Not found';
					endif;
				}
			?>
			
			<?php if ($thumb || $description !='<p><br></p>'): ?>
			<div class="form-group clearfix">
				
				<?php if($lstimg_cate_status):?>
				<div class="category-info row">
					<?php $short_by_category = ($thumb) ? 'col-sm-9 col-xs-12' : 'col-sm-12 col-xs-12'; ?>
					<?php if ($thumb): ?>
						<div class="img-cate col-sm-3 col-xs-12">
							<img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" title="<?php echo $heading_title; ?>" class=" media-object" />
						</div>
					<?php endif; ?>
				
					<?php if ($description): ?>
					<div class="form-group <?php echo $short_by_category;?>">
						<?php echo $description; ?>
					</div>
					<?php endif; ?>
				</div>
				<h3 class="title-category"><?php echo $heading_title; ?></h3> 
				<?php endif;?>
			</div>
			<?php endif; ?>
			
			<?php if (isset($product_catalog_refine) && $product_catalog_refine !='2' && $categories) { ?>
			<div class="refine-search form-group">
				<div class="row">
					<h3 class="col-xs-12 title-category"><?php echo $text_refine; ?></h3>
					<?php
					$device_refine ='form-group ';
					foreach ($devices as $refine => $device) {
						if(isset($soconfig_pages["product_catalog_refine_col_".$refine]) && $soconfig_pages["product_catalog_refine_col_".$refine] !=''){
							if((12%$soconfig_pages["product_catalog_refine_col_".$refine]) == '0'){
								$device_refine .=  'col-'.$refine.'-'.(12/$soconfig_pages["product_catalog_refine_col_".$refine] ).' ';
							}else{
								$device_refine .=  'col-'.$refine.'-15'.' ';
							}
						}
					}
					foreach ($categories as $category) {
						?>
						<div class="<?php echo $device_refine;?>">
						 <?php if ($product_catalog_refine ){?>
								<a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
							  
						 <?php }else{?>
								<a href="<?php echo $category['href']; ?>" class="thumbnail"><img src="<?php echo $category['thumb']; ?>" alt="<?php echo $category['name']; ?>" /> </a>
								<a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
						 <?php }?>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
			
			<?php echo $content_top; ?>
			
			<!--// Begin Select Category Simple -->
			<?php 
				if (file_exists(DIR_TEMPLATE . $theme . '/template/soconfig/category_grid.tpl')) include(DIR_TEMPLATE.$theme.'/template/soconfig/category_grid.tpl');
				else include(DIR_TEMPLATE.'default/template/soconfig/category_grid.tpl');
			?>
				
			<?php if (!$products) { ?>
				<div class="form-group">
					<h4><?php echo $text_empty; ?></h4>
					<div class="buttons">
						<div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
					</div>
				</div>
			<?php } ?>
			<!--End content-->
		
		<script type="text/javascript"><!--
		 $('.view-mode .list-view button').bind("click", function() {
			if ($(this).is(".active")) {return false;}
			$.cookie('listingType', $(this).is(".grid") ? 'grid' : 'list', { path: '/' });
			location.reload();
		});
		//--></script> 
		
		<?php echo $content_bottom; ?>
		</div>
    </div>
	  
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>


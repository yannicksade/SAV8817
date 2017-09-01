<?php echo $header; ?>
<?php
//Variables Category
$devices = array('lg' => ' Desktops','md' => ' Desktops','sm' => ' Tablets','xs' => ' Phones',);
$other_catalog_column_lg = isset($other_catalog_column_lg) ? $other_catalog_column_lg : '';
$other_catalog_column_md = isset($other_catalog_column_md) ? $other_catalog_column_md : '';
$other_catalog_column_sm = isset($other_catalog_column_sm) ? $other_catalog_column_sm : '';
$other_catalog_column_xs = isset($other_catalog_column_xs) ? $other_catalog_column_xs : '';
$soconfig_pages  = array(
	'other_catalog_column_lg'=>$other_catalog_column_lg,
	'other_catalog_column_md'=>$other_catalog_column_md,
	'other_catalog_column_sm'=>$other_catalog_column_sm,
	'other_catalog_column_xs'=>$other_catalog_column_xs,
);
$short_by = !empty ($pagination) ? 'col-md-7 col-sm-8' : 'col-xs-12';
/*CHECK COOKIE LISTINGTYPE*/	
if(isset($_COOKIE["listingType"])) $listingType = $_COOKIE["listingType"];
else $listingType =  isset($data['product_catalog_mode']) && $data['product_catalog_mode'] ? 'list' : 'grid';
?>


<div class="container">
	 <!-- BREADCRUMB -->
	<ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
		<div class="form-group">
			  <h1><?php echo $heading_title; ?></h1>
			  <label class="control-label" for="input-search"><?php echo $entry_search; ?></label>
			  <div class="row">
				<div class="col-sm-3">
				  <input type="text" name="search" value="<?php echo $search; ?>" placeholder="<?php echo $text_keyword; ?>" id="input-search" class="form-control" />
				</div>
				<div class="col-sm-3">
				  <select name="category_id" class="form-control">
					<option value="0"><?php echo $text_category; ?></option>
					<?php foreach ($categories as $category_1) { ?>
					<?php if ($category_1['category_id'] == $category_id) { ?>
					<option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
					<?php } else { ?>
					<option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
					<?php } ?>
					<?php foreach ($category_1['children'] as $category_2) { ?>
					<?php if ($category_2['category_id'] == $category_id) { ?>
					<option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
					<?php } else { ?>
					<option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
					<?php } ?>
					<?php foreach ($category_2['children'] as $category_3) { ?>
					<?php if ($category_3['category_id'] == $category_id) { ?>
					<option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
					<?php } else { ?>
					<option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
					<?php } ?>
					<?php } ?>
					<?php } ?>
					<?php } ?>
				  </select>
				</div>
				<div class="col-sm-12" style="margin: 15px 0;">
				  <label class="checkbox-inline">
					<?php if ($sub_category) { ?>
					<input type="checkbox" name="sub_category" value="1" checked="checked" />
					<?php } else { ?>
					<input type="checkbox" name="sub_category" value="1" />
					<?php } ?>
					<?php echo $text_sub_category; ?></label>
					
					<label class="checkbox-inline" style="margin:0;">
					  <?php if ($description) { ?>
					  <input type="checkbox" name="description" value="1" id="description" checked="checked" />
					  <?php } else { ?>
					  <input type="checkbox" name="description" value="1" id="description" />
					  <?php } ?>
					  <?php echo $entry_description; ?>
					</label>
				</div>
			  </div>

			  <input type="button" value="<?php echo $button_search; ?>" id="button-search" class="btn btn-primary" />
		</div>
      <?php if ($products) { ?>
	<!-- Filters -->
	<div class="product-filter filters-panel">
	  <div class="row">
		<div class="col-sm-2 view-mode hidden-sm hidden-xs">
			<div class="list-view">
				<button class="btn btn-default grid <?php if($listingType =='grid') { echo 'active'; } ?>"  data-toggle="tooltip" title="<?php echo $button_grid; ?>"><i class="fa fa-th-large"></i></button>
				<button class="btn btn-default list <?php if($listingType =='list') { echo 'active'; } ?>"  data-toggle="tooltip" title="<?php echo $button_list; ?>"><i class="fa fa-bars"></i></button>
			</div>
		</div>
		<div class="short-by-show form-inline text-right col-md-10 col-sm-8">
			<div class="form-group short-by">
				<label class="control-label" for="input-sort"><?php echo $text_sort; ?></label>
				<select id="input-sort" class="form-control" onchange="location = this.value;">
				  <?php foreach ($sorts as $sorts2) { ?>
				  <?php if ($sorts2['value'] == $sort . '-' . $order) { ?>
				  <option value="<?php echo $sorts2['href']; ?>" selected="selected"><?php echo $sorts2['text']; ?></option>
				  <?php } else { ?>
				  <option value="<?php echo $sorts2['href']; ?>"><?php echo $sorts2['text']; ?></option>
				  <?php } ?>
				  <?php } ?>
				</select>
			</div>

			<div class="form-group">
				<label class="control-label" for="input-limit"><?php echo $text_limit; ?></label>
				<select id="input-limit" class="form-control" onchange="location = this.value;">
				  <?php foreach ($limits as $limits2) { ?>
				  <?php if ($limits2['value'] == $limit) { ?>
				  <option value="<?php echo $limits2['href']; ?>" selected="selected"><?php echo $limits2['text']; ?></option>
				  <?php } else { ?>
				  <option value="<?php echo $limits2['href']; ?>"><?php echo $limits2['text']; ?></option>
				  <?php } ?>
				  <?php } ?>
				</select>
			</div>
			
			<div class="product-compare form-group"><a href="<?php echo $compare; ?>" id="compare-total"><?php echo $text_compare; ?></a></div>
			
		</div>
		
	  </div>
	</div>
	<!-- //end Filters -->
	
	<!--Changed Listings-->
	<div class="products-category">
	<?php 
		if (file_exists(DIR_TEMPLATE . $theme . '/template/soconfig/listing.php')) include(DIR_TEMPLATE.$theme.'/template/soconfig/listing.php');
		else echo 'Not found';
	?>
	</div>
	<!--// End Changed listings-->
	
	<!-- Filters -->
	<div class="product-filter product-filter-bottom filters-panel" style="float: left; width: 100%;">
	  
	   <div class="short-by-show <?php echo $short_by;?>">
			<div class="form-group" style="margin: 0"><?php echo $results; ?></div>
			
		</div>
		<?php if (!empty($pagination)){?>
			<div class="box-pagination col-md-5 col-sm-4 text-right"><?php echo $pagination; ?></div>
		<?php }?>
	
	</div>
	<!-- //end Filters -->

<?php } ?>
	
	
  <!--end content-->
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
	<script type="text/javascript"><!--
	$('#button-search').bind('click', function() {
		url = 'index.php?route=product/search';

		var search = $('#content input[name=\'search\']').prop('value');

		if (search) {
			url += '&search=' + encodeURIComponent(search);
		}

		var category_id = $('#content select[name=\'category_id\']').prop('value');

		if (category_id > 0) {
			url += '&category_id=' + encodeURIComponent(category_id);
		}

		var sub_category = $('#content input[name=\'sub_category\']:checked').prop('value');

		if (sub_category) {
			url += '&sub_category=true';
		}

		var filter_description = $('#content input[name=\'description\']:checked').prop('value');

		if (filter_description) {
			url += '&description=true';
		}

		location = url;
	});

	$('#content input[name=\'search\']').bind('keydown', function(e) {
		if (e.keyCode == 13) {
			$('#button-search').trigger('click');
		}
	});

	$('select[name=\'category_id\']').on('change', function() {
		if (this.value == '0') {
			$('input[name=\'sub_category\']').prop('disabled', true);
		} else {
			$('input[name=\'sub_category\']').prop('disabled', false);
		}
	});

	$('select[name=\'category_id\']').trigger('change');
	
	$('.view-mode .list-view button').bind("click", function() {
		if ($(this).is(".active")) {return false;}
		$.cookie('listingType', $(this).is(".grid") ? 'grid' : 'list', { path: '/' });
		location.reload();
	});

	--></script>
<?php echo $footer; ?>
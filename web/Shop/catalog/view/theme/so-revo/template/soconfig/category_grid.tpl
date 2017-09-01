<?php if ($products) { ?>
	<!-- Filters -->
	<div class="product-filter filters-panel">
	  <div class="row">
		<div class="box-list col-md-2 hidden-sm hidden-xs">
			<div class="view-mode">
				<div class="list-view">
					<button class="btn btn-default grid <?php if($listingType =='grid') { echo 'active'; } ?>"  data-toggle="tooltip" title="<?php echo $button_grid; ?>"><i class="fa fa-th-large"></i></button>
					<button class="btn btn-default list <?php if($listingType =='list') { echo 'active'; } ?>"  data-toggle="tooltip" title="<?php echo $button_list; ?>"><i class="fa fa-bars"></i></button>
				</div>
			</div>
		</div>
		<div class="short-by-show form-inline text-right col-md-10 col-sm-12">
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
			
			<div class="product-compare form-group"><a href="<?php echo $compare; ?>" id="compare-total" class="btn btn-default"><i class="fa fa-retweet"></i> <?php echo $text_compare; ?></a></div>
			
		</div>
		
	  </div>
	</div>
	<!-- //end Filters -->
	
	<!--Changed Listings-->
	<?php 
		if (file_exists(DIR_TEMPLATE . $theme . '/template/soconfig/listing.php')) include(DIR_TEMPLATE.$theme.'/template/soconfig/listing.php');
		else echo 'Not found';
	?>
	<!--// End Changed listings-->
	
	<!-- Filters -->
	<div class="product-filter product-filter-bottom filters-panel" >
	  <div class="row">
		<div class="box-list col-md-2 hidden-sm hidden-xs">
			<div class="view-mode">
				<div class="list-view">
					<button class="btn btn-default grid <?php if($listingType =='grid') { echo 'active'; } ?>"  data-toggle="tooltip" title="<?php echo $button_grid; ?>"><i class="fa fa-th-large"></i></button>
					<button class="btn btn-default list <?php if($listingType =='list') { echo 'active'; } ?>"  data-toggle="tooltip" title="<?php echo $button_list; ?>"><i class="fa fa-bars"></i></button>
				</div>
			</div>
		</div>
	   <div class="short-by-show text-center <?php echo $short_by;?>">
			<div class="form-group" style="margin:0px"><?php echo $results; ?></div>
		</div>
		<?php if (!empty($pagination)){?>
			<div class="box-pagination col-md-4 col-sm-4 text-right"><?php echo $pagination; ?></div>
		<?php }?>
		
	  </div>
	</div>
	<!-- //end Filters -->

<?php } ?>
	
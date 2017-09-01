<li class="so-filter-options" data-option="<?php echo "Price"?>">
	<div class="so-filter-heading">
		<div class="so-filter-heading-text">
			<span><?php echo $obj_lang->get('text_price');?></span>
		</div>
		<i class="fa fa-chevron-down"></i>
	</div>

	<div class="so-filter-content-opts">
		<div class="so-filter-content-opts-container">
			<div class="so-filter-content-wrapper so-filter-iscroll">
				<div class="so-filter-options">
					<div class="so-filter-option so-filter-price">
						<div class="content_min_max">
							<?php echo $currencies;?> <input type="number" class="input_min form-control" value="<?php echo $minPrice_new?>" min="<?php echo $minPrice?>" max="<?php echo $maxPrice?>">
							- <?php echo $currencies?> <input type="number" class="input_max form-control" value="<?php echo $maxPrice_new?>" min="<?php echo $minPrice?>" max="<?php echo $maxPrice?>">
						</div>
						<div class="content_scroll">
							<div id="slider-range"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</li>

<li class="so-filter-options" data-option="<?php echo "Subcategory";?>">
	<div class="so-filter-heading">
		<div class="so-filter-heading-text">
			<span><?php echo $obj_lang->get('text_subcategory');?></span>
		</div>
		<i class="fa fa-chevron-down"></i>
	</div>

	<div class="so-filter-content-opts">
		<div class="so-filter-content-opts-container">
				<?php
				foreach($subcategory_all as $item)
				{
					if(count($subcate_id) > 0 && $subcate_id != "")
					{
						$class = in_array($item['subcate_id'],explode(",",$subcate_id)) == true ? "opt_active" : "";
					}else
					{
						$class = "";
					}

				?>
					<div class="so-filter-option-sub opt-select <?php echo $class;?> <?php echo $item['subcate_count_product'] >0 ? "opt_enable" : "opt_disable";?>" data-type="subcategory" data-subcategory_value= "<?php echo $item['subcate_id']?>" data-count_product="<?php echo $item['subcate_count_product']?>" data-list_product="<?php echo $item['subcate_list_product']?>">
						<div class="so-option-container">
							<div class="option-input">
								<span class="fa fa-square-o"></i>
							</div>
							<label><img src="<?php echo $item['subcate_image'];?>"> <?php echo $item['subcate_name']?> </label>
							<div class="option-count <?php if(count($subcate_id) > 0) echo in_array($item['subcate_id'],explode(",",$subcate_id)) == true ? "opt_close" : "";?>">
								<span><?php echo $item['subcate_count_product']?></span>
								<i class="fa fa-times"></i>
							</div>
						</div>
					</div>
				<?php
				}
				?>
		</div>

	</div>
</li>

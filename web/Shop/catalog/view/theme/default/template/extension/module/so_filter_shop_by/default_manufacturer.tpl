<li class="so-filter-options" data-option="<?php echo "Manufacturer";?>">
	<div class="so-filter-heading">
		<div class="so-filter-heading-text">
			<span><?php echo $obj_lang->get('text_manufacturer');?></span>
		</div>
		<i class="fa fa-chevron-down"></i>
	</div>

	<div class="so-filter-content-opts">
		<div class="so-filter-content-opts-container">
				<?php
				foreach($manufacturer_all as $item)
				{
					if(count($manu_id) > 0 && $manu_id != "")
					{
						$class = in_array($item['manu_value_id'],explode(",",$manu_id)) == true ? "opt_active" : "";
					}else
					{
						$class = "";
					}
					if($disp_manu['disp_manu_id_'.$item['manu_value_id']])
					{
				?>
					<div class="so-filter-option opt-select <?php echo $class;?> <?php echo $item['manu_count_product'] >0 ? "opt_enable" : "opt_disable";?>" data-type="manufacturer" data-manufacturer_value= "<?php echo $item['manu_value_id']?>" data-count_product="<?php echo $item['manu_count_product']?>" data-list_product="<?php echo $item['manu_list_product']?>">
						<div class="so-option-container">
							<div class="option-input">
								<span class="fa fa-square-o"></i>
							</div>
							<label><img src="<?php echo $item['manu_value_image'];?>"> <?php echo $item['manu_value_name']?> </label>
							<div class="option-count <?php if(count($manu_id) > 0) echo in_array($item['manu_value_id'],explode(",",$manu_id)) == true ? "opt_close" : "";?>">
								<span><?php echo $item['manu_count_product']?></span>
								<i class="fa fa-times"></i>
							</div>
						</div>
					</div>
				<?php
					}
				}
				?>

		</div>

	</div>
</li>

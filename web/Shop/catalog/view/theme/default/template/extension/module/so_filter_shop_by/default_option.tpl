<li class="so-filter-options" data-option="<?php echo $item[0];?>">
	<div class="so-filter-heading">
		<div class="so-filter-heading-text">
			<span><?php echo $item[0];?></span>
		</div>
		<i class="fa fa-chevron-down"></i>
	</div>

	<div class="so-filter-content-opts">
		<div class="so-filter-content-opts-container">

				<?php
				foreach($values as $value)
				{
					if(count($opt_id) > 0 && $opt_id != "")
					{
						$class = in_array($value['opt_value_id'],explode(",",$opt_id)) == true ? "opt_active" : "";
					}else
					{
						$class = "";
					}
				?>
					<div class="so-filter-option opt-select <?php echo $class;?> <?php echo $value['opt_count_product'] >0 ? "opt_enable" : "opt_disable";?>" data-type="option" data-option_value= "<?php echo $value['opt_value_id']?>" data-count_product="<?php echo $value['opt_count_product']?>" data-list_product="<?php echo $value['opt_list_product']?>">
						<div class="so-option-container">
							<div class="option-input">
								<span class="fa fa-square-o"></i>
							</div>
							<label><?php echo $value['opt_value_name']?> </label>
							<div class="option-count <?php if(count($opt_id) > 0 && $opt_id != "") echo in_array($value['opt_value_id'],explode(",",$opt_id)) == true ? "opt_close" : "";?>">
								<span><?php echo $value['opt_count_product']?></span>
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

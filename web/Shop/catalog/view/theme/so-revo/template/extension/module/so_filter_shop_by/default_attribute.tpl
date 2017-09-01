<li class="so-filter-options" data-attribute="<?php echo $item[0];?>">
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
					if(count($att_id) > 0)
					{
						$class = in_array($value['att_value_id'],explode(",",$att_id)) == true ? "opt_active" : "";
					}else
					{
						$class = "";
					}
					if($disp_attributes[$value['att_value_value']])
					{
				?>
					<div class="so-filter-option opt-select <?php echo $class;?> <?php echo $value['att_count_product'] >0 ? "opt_enable" : "opt_disable";?>" data-type="attribute" data-attribute_value= "<?php echo $value['att_value_id']?>" data-count_product="<?php echo $value['att_count_product']?>" data-list_product="<?php echo $value['att_list_product']?>">
						<div class="so-option-container">
							<div class="option-input">
								<i class="fa fa-square-o"></i>
							</div>
							<label><?php echo $value['att_value_name']?> </label>
							<div class="option-count <?php if(count($att_id) > 0) echo in_array($value['att_value_id'],explode(",",$att_id)) == true ? "opt_close" : "";?>">
								<span><?php echo $value['att_count_product']?></span>
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

<div class="ltabs-tabs-wrap">
	<span class='ltabs-tab-selected'></span>
	<span class="ltabs-tab-arrow">▼</span>
	<ul class="ltabs-tabs cf">
		<?php
		
		foreach ($list as $tab) 
		{
			if ($type_source == "0") {
				?>
				<li class="ltabs-tab <?php echo isset($tab['sel']) ? '  tab-sel tab-loaded' : ''; ?> <?php echo ($tab['category_id'] == ('*')) ? ' tab-all' : ''; ?>"
					data-category-id="<?php echo $tab['category_id']; ?>"
					data-active-content-l=".items-category-<?php echo ($tab['category_id'] == "*") ? 'all' : $tab['category_id']; ?>"  
					>
				<?php if ($tab_icon_display == '1') 
				{
					if($tab['category_id'] != "*")
					{
				?>
					<div class="ltabs-tab-img">
						<img src="<?php echo $tab['icon_image'] ?>"
							 title="<?php echo $tab['name']; ?>" alt="<?php echo $tab['name']; ?>"
							 style="width: <?php echo $imgcfgcat_width?>px; height:<?php echo $imgcfgcat_height?>px;background:#fff"/>
					</div>
			<?php 	}else{
			?>
					<div class="ltabs-tab-img">
						<img src="catalog/view/javascript/so_listing_tabs/images/icon-catall.png"
							 title="<?php echo $tab['name']; ?>" alt="<?php echo $tab['name']; ?>"
							 style="width: 30px; height:74px; background:#fff"/>
					</div>
			<?php }?>
		<?php   } ?>
					<span class="ltabs-tab-label">
						<?php if(strlen($tab['name']) > $tab_max_characters && $tab_max_characters != '0') {echo 
							utf8_substr(strip_tags(html_entity_decode($tab['name'], ENT_QUOTES, 'UTF-8')), 0, $tab_max_characters) . '..';}else{ echo $tab['name'];}
							?>
					</span>
				</li>
		<?php 
			}else {
		?>
				<li class="ltabs-tab <?php echo isset($tab['sel']) ? '  tab-sel tab-loaded' : ''; ?> <?php echo ($tab['category_id'] == ('*')) ? ' tab-all' : ''; ?>"
					data-category-id="<?php echo $tab['category_id']; ?>"
					data-active-content-l=".items-category-<?php echo $tab['category_id']; ?>"  >
					<span
						class="ltabs-tab-label"><?php if(strlen($tab['title']) > $tab_max_characters && $tab_max_characters != '0') {echo 
						utf8_substr(strip_tags(html_entity_decode($tab['title'], ENT_QUOTES, 'UTF-8')), 0, $tab_max_characters) . '..';}else{ echo $tab['title'];}
						?>
					</span>
				</li>
			<?php 
			}
		} 
		?>
	</ul>
</div>

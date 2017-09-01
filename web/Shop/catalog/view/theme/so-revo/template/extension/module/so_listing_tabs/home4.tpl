
<div class="module <?php echo $direction_class?> <?php echo $class_suffix; ?>">
	<?php if($disp_title_module) { ?>
		<h3 class="modtitle"><span><?php echo $head_name; ?></span></h3>
	<?php } ?>
	<?php if($pre_text != '')
		{
	?>
		<div class="description">
			<p><?php echo html_entity_decode($pre_text);?></p>
		</div>
	<?php 
		}
	?>
	<div class="modcontent">
	<!--[if lt IE 9]>
	<div id="<?php echo $tag_id; ?>" class="so-listing-tabs msie lt-ie9 first-load module"><![endif]-->
	<!--[if IE 9]>
	<div id="<?php echo $tag_id; ?>" class="so-listing-tabs msie first-load module"><![endif]-->
	<!--[if gt IE 9]><!-->
	<div id="<?php echo $tag_id; ?>" class="so-listing-tabs first-load module"><!--<![endif]-->
		<?php 
		if (!empty($list)) {	
		?>
		<?php $http = $_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://'; ?>
		<div class="ltabs-wrap ">
			<div class="ltabs-tabs-container" data-delay="<?php echo $delay ?>"
				 data-duration="<?php echo $duration ?>"
				 data-effect="<?php echo $effect ?>"
				 data-ajaxurl="<?php echo "$http$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>" data-type_source="<?php echo $type_source;?>"
				 data-type_show="<?php echo $type_show;?>" >
				 
				<!--Begin Tabs-->
					<?php include("home4/default_tabs.tpl");	?>
					
					
				<!-- End Tabs-->
			</div>
			<div class="wap-listing-tabs products-list grid">
				<?php if ($display_banner_image == 1) {
					?>
					<div class="item-cat-image">
						<a href="<?php echo $banner_image_url; ?>" title="" target="<?php echo $item_link_target ?>" >
							<img class="categories-loadimage" title="" alt=""
								 src="<?php echo $banner_image; ?>"/>
						</a>
					</div>
				<?php } ?>
				<div class="ltabs-items-container"><!--Begin Items-->
				<?php 
				foreach ($list as $key => $items) {
					
				$child_items = isset($items['child']) ? $items['child'] : '';
				$cls = (isset($items['sel']) && $items['sel'] == "sel") ? ' ltabs-items-selected ltabs-items-loaded' : '';
				$cls .= ($items['category_id'] == "*") ? ' items-category-all' : ' items-category-' . $items['category_id'];
				$tab_id = isset($list[$key]['sel']) ? $items['category_id'] : '';
				$tab_id = $tab_id == '*' ? 'all' : $tab_id;
				?>
				<div class="ltabs-items <?php echo $cls; ?>" data-total="<?php echo $items['count'] ?>">
					<?php
					if (!empty($child_items)) {	
						include("home4/default_items.tpl");
					} else { ?>
						<div class="ltabs-loading"></div>
					<?php  } ?>
					<?php $classloaded = ($source_limit >= $items['count'] || $source_limit == 0) ? 'loaded' : ''; 
					if($type_show == 'loadmore') {
					?>
					<div class="ltabs-loadmore"
						 data-active-content=".items-category-<?php echo ($items['category_id'] == "*") ? 'all' : $items['category_id']; ?>"
						 data-categoryid	="<?php echo $items['category_id']; ?>"
						 data-rl_start		="<?php echo $source_limit ?>"
						 data-rl_total		="<?php echo $items['count'] ?>"
						 data-rl_allready	="All ready"
						 data-ajaxurl  		="<?php echo "$http$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>"
						 data-rl_load		="<?php echo $source_limit ?>" 
						 data-moduleid		='<?php echo $moduleid; ?>'
						 >
						<div class="ltabs-loadmore-btn <?php echo $classloaded ?>"
							 data-label="<?php echo ($classloaded) ? "All ready" : "Load more"; ?>">
							<span class="ltabs-image-loading"></span>
							<i class="fa fa-plus"></i>
						</div>
					</div>
					<?php } ?>
				</div>
			<?php } ?>
			</div>
			</div>
			
		<!--End Items-->
		</div>
		<?php
			include("home4/default_js.tpl");
		} else {
			echo $objlang->get('text_noproduct');
		} 
		?>
	</div>

	</div> <!-- /.modcontent-->
	<?php if($post_text != '')
	{
	?>
		<div class="form-group">
			<?php echo html_entity_decode($post_text);?>
		</div>
	<?php 
	}
	?>
</div>	
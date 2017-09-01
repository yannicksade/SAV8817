<div class="module so-listing-tabs-ltr custom-listingtab default-nav">
	<div class="box-title font-ct">
		<h2 class="modtitle">Fashion</h2>
	</div>
	<div class="modcontent">
	<div id="so_listing_tabs_213" class="so-listing-tabs first-load module"><!--<![endif]-->
		
		<div class="ltabs-wrap ">
			<div class="ltabs-tabs-container" data-delay="500"
				 data-duration="800"
				 data-effect="none"
				 data-ajaxurl="http://localhost/vegaMarket2.3.0.2/" data-type_source="1"
				 data-type_show="slider" >
				 
				<!--Begin Tabs-->
					<?php include("default/default_tabs.tpl");	?>
					
				<!-- End Tabs-->
			</div>
			<div class="wap-listing-tabs products-list grid">
				<div class="item-cat-image banners">
					<div>
						<a href="#" title="" target="_self" >
							<img class="categories-loadimage" title="" alt=""
								 src="http://localhost/vegaMarket2.3.0.2/image/cache/catalog/demo/banners/home1/6-196x540.jpg"/>
						</a>
					</div>
				</div>
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
					
							<?php include("default/default_items.tpl"); ?>
						
					</div>
			<?php } ?>
				</div>
			</div>
			
		<!--End Items-->
		</div>
		<?php
			include("default/default_js.tpl");
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
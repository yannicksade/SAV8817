<?php if($categories) { ?>
    <div class="blog-category module">
      	
		<?php if((isset($simple_blog_category_search_article)) && ($simple_blog_category_search_article)) { ?>
			<div id="blog-search" class="module">
				<div class="input-group">
					<input type="text" name="article_search" value="<?php echo $blog_search; ?>" placeholder="<?php echo $text_search_article; ?>" class="form-control" style="margin-bottom: 5px;" />
					<span class="input-group-btn" style="vertical-align: top;">
						<a id="button-search" class="button-search btn btn-primary"><i class="fa fa-search"></i></a>
					</span>
					
				</div>
			</div>
		<?php } ?>
		<div class="module">
			<h3 class="modtitle"><span><?php echo $heading_title; ?></span></h3>
			 <ul class="list-group ">
			 <?php $categories = array_reverse($categories); ?>
				<?php foreach ($categories as $category) { ?>
					<?php if ($category['simple_blog_category_id'] == $category_id) { ?>
						<li class="list-group-item"> <a href="<?php echo $category['href']; ?>" class=" active"><?php echo $category['name']; ?></a></li>
						<?php if ($category['children']) { ?>
							<?php foreach ($category['children'] as $child) { ?>
								<?php if ($child['category_id'] == $child_id) { ?>
									<a href="<?php echo $child['href']; ?>" class="active">&nbsp;&nbsp;&nbsp;- <?php echo $child['name']; ?></a>
								<?php } else { ?>
									<a href="<?php echo $child['href']; ?>" >&nbsp;&nbsp;&nbsp;- <?php echo $child['name']; ?></a>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					<?php } else { ?>
						<li class="list-group-item"> <a href="<?php echo $category['href']; ?>" ><?php echo $category['name']; ?></a></li>
					<?php } ?>
				<?php } ?>
			</ul>
		</div>
      	
    </div>
<?php } ?>



<script type="text/javascript">
	$('#blog-search input[name=\'article_search\']').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#button-search').trigger('click');
		}
	});

	$('#button-search').bind('click', function() {
		url = 'index.php?route=simple_blog/search';
		
		var article_search = $('#blog-search input[name=\'article_search\']').val();
		
		if (article_search) {
			url += '&blog_search=' + encodeURIComponent(article_search);
		}
		
		location = url;
	});
</script> 

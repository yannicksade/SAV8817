<div class="item">
	<?php if($type_show == 'simple'){?>
	<?php if( $blog['thumb'] && $blog_image)  { ?>
		<div class="media-left">
			<a href="<?php echo $blog['link'];?>" target="<?php echo $item_link_target?>">
				<img src="<?php echo $blog['thumb'];?>" alt="<?php echo $blog['title'];?>" class="media-object"/>
			</a>
		</div>
	<?php } ?>
	<div class="media-body">
		<?php if ($display_title){ ?>
			<h4 class="media-heading">
				<a href="<?php echo $blog['link'];?>" title="<?php echo $blog['title'];?>" target="<?php echo $item_link_target?>"><?php echo $blog['title_maxlength'];?></a>
			</h4>
		<?php }?>
		<div class="media-content">
			<?php if($display_date_added){ ?>
				<div class="media-date-added"><i class="fa fa-calendar"></i> <?php echo date('F jS, Y', strtotime($blog['date_modified'])) ?></div>
			<?php }?>
			<?php if($display_description){ ?>
				<div class="description">
						<?php $blog['description'] = strip_tags($blog['description']); ?>
						<?php echo utf8_substr( $blog['description'],0, 200 );?>...
				</div>
			<?php }?>
			
			<?php if($display_author || $display_comment || $display_view):?>	
				<div class="media-subcontent">
				<?php if($display_author){ ?>
					<span class="media-author"><?php echo ucwords($blog['author']); ?></span>
				<?php }?>
				<?php if($display_comment){ ?>
					<span class="media-comment"><?php echo $blog['comment_count']; echo $blog['text_comment']?></span>
				<?php }?>
				<?php if($display_view){ ?>
					<span class="media-view"><?php echo $blog['view_count']; echo $blog['text_view']?></span>
				<?php }?>
				</div>
			<?php endif;?>	
			
			<?php if($display_readmore){ ?>
				<div class="readmore">
					<a href="<?php echo $blog['link'];?>" target="<?php echo $item_link_target?>" class="readmore"><?php echo $readmore_text;?></a>
				</div>

			<?php }?>
		</div>
	</div>
	<?php }else {?>
	<div class="thumbnail">
		<?php if($blog_image):?>
		
			<a href="<?php echo $blog['link'];?>" target="<?php echo $item_link_target?>">
				<img src="<?php echo $blog['thumb'];?>" alt="<?php echo $blog['title'];?>" target="<?php echo $item_link_target?>" />
			</a>
		<?php endif; ?>
		<div class="caption">
		<?php if ($display_title){ ?>
			<h4>
				<a href="<?php echo $blog['link'];?>" title="<?php echo $blog['title'];?>" target="<?php echo $item_link_target?>"><?php echo $blog['title_maxlength'];?></a>
			</h4>
		<?php }?>
			<?php if($display_date_added){ ?>
			<p class="media-date-added"><i class="fa fa-calendar"></i> <?php echo date('F jS, Y', strtotime($blog['date_modified'])) ?></p>
			<?php }?>
			<?php if($display_description){ ?>
			<div class="description">
				<?php $blog['description'] = strip_tags($blog['description']); ?>
				<?php echo utf8_substr( $blog['description'],0, 200 );?>...
			</div>
			<?php }?>
			<?php if($display_author || $display_comment || $display_view):?>	
			<div class="media-subcontent">
				<?php if($display_author){ ?>
					<span class="media-author"><?php echo ucwords($blog['author']); ?></span>
				<?php }?>
				<?php if($display_comment){ ?>
					<span class="media-comment"><?php echo $blog['comment_count']; echo $blog['text_comment']?></span>
				<?php }?>
				<?php if($display_view){ ?>
					<span class="media-view"><?php echo $blog['view_count']; echo $blog['text_view']?></span>
				<?php }?>
				</div>
			<?php endif;?>
			<?php if($display_readmore){ ?>
				<div class="readmore">
					<a href="<?php echo $blog['link'];?>" target="<?php echo $item_link_target?>" class="readmore"><?php echo $readmore_text;?></a>
				</div>

			<?php }?>
		</div>
    </div>
	
	<?php }?>
</div>



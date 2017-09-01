<div class="item">
	<?php if( $blog['thumb'] && $blog_image )  { ?>
	<div class="content-img">
		<a href="<?php echo $blog['link'];?>" target="<?php echo $item_link_target?>">
			<img src="<?php echo $blog['thumb'];?>" alt="<?php echo $blog['title'];?>" class="media-object"/>
		</a>
		<div class="entry-date font-ct">
			<?php if($display_date_added){ ?>
				<div class="day-time"><?php echo date('j', strtotime($blog['date_modified'])) ?></div>
				<div class="month-time"><?php echo date('M', strtotime($blog['date_modified'])) ?></div>
			<?php }?>
		</div>
	</div>
	<?php } ?>
	<div class="content-detail">
		<div class="media-content">
			<?php if ($display_title){ ?>
			<h4 class="media-heading font-ct">
				<a href="<?php echo $blog['link'];?>" title="<?php echo $blog['title'];?>" target="<?php echo $item_link_target?>"><?php echo $blog['title_maxlength'];?></a>
			</h4>
		<?php }?>
			<?php if($display_description){ ?>
				<div class="description">
						<?php $blog['description'] = strip_tags($blog['description']); ?>
						<?php echo utf8_substr( $blog['description'],0, 200 );?>...
				</div>
			<?php }?>
			<?php if($display_author || $display_view || $display_readmore ) {?>
				<div class="media-subcontent">
				<?php if($display_author){ ?>
					<span class="media-author"><i class="fa fa-book"></i><?php echo ucwords($blog['author']); ?></span>
				<?php }?>
				<?php if($display_comment){ ?>
					<span class="media-comment"><i class="fa fa-comments"></i><?php echo $blog['comment_count']; echo $blog['text_comment']?></span>
				<?php }?>
				<?php if($display_view){ ?>
					<span class="media-view"><i class="fa fa-eye"></i><?php echo $blog['view_count']; echo $blog['text_view']?></span>
				<?php }?>
				</div>
				<?php if($display_readmore){ ?>
				<div class="readmore">
					<a href="<?php echo $blog['link'];?>" target="<?php echo $item_link_target?>" class="readmores"><i class="fa fa-caret-right"></i><?php echo $readmore_text;?></a>
				</div>

				<?php }?>
			<?php }?>
		</div>
	</div>
</div>



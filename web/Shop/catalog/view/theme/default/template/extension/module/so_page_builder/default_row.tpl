<?php foreach ( $rows_child as $row_child) {
?>
<div class="row <?php echo $row_child->text_class_id;?><?php echo ($row_child->text_class != '' ? ' '.$row_child->text_class : '');?> <?php switch($row_child->background_type){ case "1": echo "row-color"; break; case "2": echo "row-image"; break; case "3": echo "row-video"; break; }?>">
	<?php if($row_child->background_type == 3){ $id_row_video = uniqid('row_').rand().time();?>
	<?php if($row_child->video_type == "0"){ ?>
		<div class="bg-video" id="<?php echo $id_row_video ?>" data-id="<?php echo $id_row_video ?>" data-loop="true" data-muted="true" data-autoplay="true" data-ratio="1.77" data-overlay="" data-swfpath=""  data-youtube="<?php echo $row_child->link_video; ?>"></div>
	<?php }else{?>
		<div class="bg-video" id="<?php echo $id_row_video ?>" data-id="<?php echo $id_row_video ?>" data-loop="true" data-muted="true" data-autoplay="true" data-ratio="1.77" data-overlay="" data-swfpath=""  data-webm="<?php echo $row_child->link_video; ?>">
			<div style="z-index: 0; position: absolute; top: 0px; left: 0px; right: 0px; bottom: 0px; overflow: hidden;">
				<video autoplay="" style="width:100%;height:100%;" loop="" onended="this.play()"><source src="<?php echo $row_child->link_video; ?>" type="video/webm"></video>
			</div>
		</div>
	<?php }?>
	<?php }?>
	<?php foreach( $row_child->cols as $col ) { ?>
		<div class="col-lg-<?php echo $col->lg_col; ?> col-md-<?php echo $col->md_col;?> col-sm-<?php echo $col->sm_col;?> col-xs-<?php echo $col->xs_col;?> <?php echo $col->text_class_id;?><?php echo ($col->text_class != '' ? ' '.$col->text_class : '');?>">
			<?php foreach ( $col->widgets as $widget ){ ?>
				<?php if( isset($widget->content) ) { ?>
						<?php echo $widget->content; ?>
				<?php } ?>
			<?php } ?>
			<?php if (isset($col->rows)&&$col->rows) { ?>
				<?php   
					$rows_child = $col->rows;
					require(DIR_TEMPLATE.$template_row); 
				?>
			<?php } ?>
			<?php if($col->background_type == 3){ $id_col_video = uniqid('col_').rand().time();?>
				<?php if($col->col_video_type == "0"){ ?>
					<div class="bg-video" id="<?php echo $id_col_video ?>" data-id="<?php echo $id_col_video ?>" data-loop="true" data-muted="true" data-autoplay="true" data-ratio="1.77" data-overlay="" data-swfpath=""  data-youtube="<?php echo $col->col_link_video; ?>"></div>
				<?php }else{?>
					<div class="bg-video" id="<?php echo $id_col_video ?>" data-id="<?php echo $id_col_video ?>" data-loop="true" data-muted="true" data-autoplay="true" data-ratio="1.77" data-overlay="" data-swfpath=""  data-webm="<?php echo $col->col_link_video; ?>">
						<div style="z-index: 0; position: absolute; top: 0px; left: 0px; right: 0px; bottom: 0px; overflow: hidden;">
							<video autoplay="" style="width:100%;height:100%;" loop="" onended="this.play()"><source src="<?php echo $col->col_link_video; ?>" type="video/webm"></video>
						</div>
					</div>
				<?php }?>
			<?php } //$col->col_background_type ?>
		</div> <?php //.col?>		
	<?php } //$row_child->cols?>
</div> <?php //.row?>
<?php 
}//$rows as $row 
?> 
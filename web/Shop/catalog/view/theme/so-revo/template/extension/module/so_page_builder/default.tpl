<?php if( !isset($rows) ){ 
	$rows = $font_ends;
	$level = 1;
}?>
	<?php foreach ( $rows as $row) {
	?>
	<?php if($row->row_section){?>
<section id="<?php echo $row->row_section_id; ?>" class="<?php echo $row->row_section_class; ?> <?php switch($row->section_background_type){ case "1": echo "section-color"; break; case "2": echo "section-image"; break; case "3": echo "section-video"; break; }?>">
		<?php if($row->section_background_type == 3){?>
			<div class="bg-content">
		<?php } //$row->section_background_type ?>
	<?php } //$row->row_section ?>
	<div class= "<?php echo ($row->row_container_fluid == 1 ? "container-fluid" : "container")?> page-builder-<?php echo $direction?>">
	<div class="row <?php echo $row->text_class_id;?><?php echo ($row->text_class != '' ? ' '.$row->text_class : '');?> <?php switch($row->background_type){ case "1": echo "row-color"; break; case "2": echo "row-image"; break; case "3": echo "row-video"; break; }?>">
		<?php if($row->background_type == 3){ $id_row_video = uniqid('row_').rand().time();?>
		<?php if($row->video_type == "0"){ ?>
			<div class="bg-video" id="<?php echo $id_row_video ?>" data-id="<?php echo $id_row_video ?>" data-loop="true" data-muted="true" data-autoplay="true" data-ratio="1.77" data-overlay="" data-swfpath=""  data-youtube="<?php echo $row->link_video; ?>"></div>
		<?php }else{?>
			<div class="bg-video" id="<?php echo $id_row_video ?>" data-id="<?php echo $id_row_video ?>" data-loop="true" data-muted="true" data-autoplay="true" data-ratio="1.77" data-overlay="" data-swfpath=""  data-webm="<?php echo $row->link_video; ?>">
				<div style="z-index: 0; position: absolute; top: 0px; left: 0px; right: 0px; bottom: 0px; overflow: hidden;">
					<video autoplay="" style="width:100%;height:100%;" loop="" onended="this.play()"><source src="<?php echo $row->link_video; ?>" type="video/webm"></video>
				</div>
			</div>
		<?php }?>
		<?php }?>
		<?php foreach( $row->cols as $col ) { ?>
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
			
		<?php } //$row->cols?>
		
	</div> <?php //.row?>
	</div> <?php //.container Or container-fluid?>
	<?php if($row->row_section){?>
		<?php if($row->section_background_type == 3){ $id_sec_video = uniqid('sec_').rand().time();?>
		</div>
		<div class="bg-overlay"></div>
		<?php if($row->section_video_type == "0"){?>
			<div class="bg-video" id="<?php echo $id_sec_video ?>" data-id="<?php echo $id_sec_video ?>" data-loop="true" data-muted="true" data-autoplay="true" data-ratio="1.77" data-overlay="" data-swfpath=""  data-youtube="<?php echo $row->section_link_video; ?>"></div>
		<?php }else{?>
			<div class="bg-video" id="<?php echo $id_sec_video ?>" data-id="<?php echo $id_sec_video ?>" data-loop="true" data-muted="true" data-autoplay="true" data-ratio="1.77" data-overlay="" data-swfpath=""  data-webm="<?php echo $row->section_link_video; ?>">
				<div style="z-index: 0; position: absolute; top: 0px; left: 0px; right: 0px; bottom: 0px; overflow: hidden;">
					<video autoplay="" style="width:100%;height:100%;" loop="" onended="this.play()"><source src="<?php echo $row->section_link_video; ?>" type="video/webm"></video>
				</div>
			</div>
		<?php }?>
		<?php }?>
	</section> <?php //section?>
	<?php } //$row->row_section?>

	<?php 
	}//$rows as $row 
	?> 

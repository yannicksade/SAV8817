<?php
if ($modules) :
?>
<aside class="col-md-3 col-sm-4 col-xs-12 content-aside left_column sidebar-offcanvas">
	<span id="close-sidebar" class="fa fa-times"></span>
	<?php foreach ($modules as $module) { ?>
		<?php echo $module; ?>
	<?php } ?>
</aside>
<?php endif; ?>

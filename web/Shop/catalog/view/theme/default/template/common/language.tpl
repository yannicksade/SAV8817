<?php if (count($languages) > 1) { ?>
<div class="btn-group">
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-language">
  <div class="btn-group">
    <button class="btn-link dropdown-toggle" data-toggle="dropdown">
		<?php foreach ($languages as $language) { ?>
		<?php if ($language['code'] == $code) { ?>
		<img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>">
		<span class="hidden-xs"><?php echo $language['name']; ?></span> 
		<?php } ?>
		<?php } ?>
		<i class="fa fa-angle-down"></i>
	</button>
	
    <ul class="dropdown-menu">
      <?php foreach ($languages as $language) { ?>
      <li><button class="btn-block language-select" type="button" name="<?php echo $language['code']; ?>"><img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></button></li>
      <?php } ?>
    </ul>
  </div>
  <input type="hidden" name="code" value="" />
  <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
</form>
</div>
<?php } ?>

<?php 
//Array Muti Color
$mutiColorTheme = explode(';', $mutiColorTheme);
$listColor = array();
foreach($mutiColorTheme as $val){
	if( !(preg_match('/^\s*$/',$val)) ){
		$rule = explode('=', "$val");
		$listColor[$rule[0]] = $rule[1];
	}
}
?>
<div id="sp-cpanel_btn" class="isDown visible-lg">
	<i class="fa fa-cogs"></i>
</div>		

<div id="sp-cpanel" class="sp-delay">
	<div class="cpanel-inner">
		<h2 class="sp-cpanel-title"> Themes Options <span class="sp-cpanel-close"> <i class="fa fa-times"> </i></span></h2>
		<div id="sp-cpanel_settings">
			<div class="panel-group">
				<label>Color Scheme</label>
				<div class="group-schemes" >
					<?php 
					if(!empty($listColor)){
					foreach ($listColor as $schemes => $color){?>
						<a href="index.php" data-toggle="tooltip" title="<?php echo trim($schemes) ?>" data-scheme="<?php echo trim($schemes)?>"  class="item_scheme <?php echo ( trim($schemes) == $themecolor) ? 'selected' : ''?>"><span style="background: <?php echo $color?>"></span></a>
					<?php } 
					}else{
						echo '<i class="fa fa-exclamation-triangle"></i> Put Muti Color Theme in SO Config Theme';
					}
					?>
				 </div>
			</div>
			
			<div class="panel-group ">
				<label>Layout Box</label>
				<div class="group-boxed">
					<select id="cp-layoutbox" name="cplayoutbox" class="form-control" onchange="changeLayoutBox(this.value);">
						<option <?php echo (isset($layoutstyle) && $layoutstyle=="full") ? 'selected' : ''?>  value="full">Wide</option>
						<option <?php echo (isset($layoutstyle) && $layoutstyle=="boxed") ? 'selected' : ''?> value="boxed">Boxed</option>
						<option <?php echo (isset($layoutstyle) && $layoutstyle=="iframed") ? 'selected' : ''?> value="iframed">Iframed</option>
						<option <?php echo (isset($layoutstyle) && $layoutstyle=="rounded") ? 'selected' : ''?> value="rounded">Rounded</option>
					</select>
				</div>
			</div>
			
	        <div class="panel-group">
				<label>Body Image</label>
				<div class="group-pattern">
					<?php for ($i = 28; $i <= 45; $i++) { ?>
					<div data-pattern="<?php echo $i?>"  class="img-pattern <?php echo (isset($soconfig_general["pattern"]) &&  $soconfig_general["pattern"]== $i) ? 'selected' : ''?>"><img src="catalog/view/theme/<?php echo $theme?>/images/patterns/<?php echo $i?>.png" alt="pattern <?php echo $i?>"></div>
					<?php } ?>
				</div>
				<p class="label-sm">Background only applies for Boxed,Framed, Rounded Layout</p>
			</div>
			
			<div class="reset-group">
			    <a href="index.php" class="btn btn-success " onclick="ResetAll()">Reset</a>
			</div>
			
		</div>
	</div>
</div>

<script type="text/javascript"><!--
var themes = '<?php echo $theme?>', $direction = '<?php echo $direction?>';
//--></script>
<link rel='stylesheet' property='stylesheet'  href='catalog/view/javascript/soconfig/css/cpanel.css' type='text/css' media='all' />
<script type="text/javascript" src="catalog/view/javascript/soconfig/js/cpanel.js"></script>

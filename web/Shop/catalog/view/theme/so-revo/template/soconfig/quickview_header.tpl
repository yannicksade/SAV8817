<?php
/******************************************************
 * @package	SO Theme Framework for Opencart 2.0.x
 * @author	http://www.magentech.com
 * @license	GNU General Public License
 * @copyright(C) 2008-2015 Magentech.com. All rights reserved.
*******************************************************/
require_once(DIR_SYSTEM . 'soconfig/classes/soconfig.php');

if(isset($registry)){$this->soconfig = new Soconfig($registry);}
else{ include(DIR_TEMPLATE.'default/template/soconfig/not_registry.php'); exit; } 
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo $title; ?></title>
<meta charset="UTF-8" />
<base href="<?php echo $base; ?>" />
<meta name="format-detection" content="telephone=no" />
<?php if($layouts){?><meta name="viewport" content="width=device-width, initial-scale=1"> <?php }?>
<?php if ($description) { ?><meta name="description" content="<?php echo $description; ?>" /><?php } ?>
<?php if ($keywords) { ?><meta name="keywords" content="<?php echo $keywords; ?>" /><?php } ?>
<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->

<?php // Begin General CSS ----- 
	
	if($direction=='ltr') $this->soconfig->addCss('catalog/view/javascript/bootstrap/css/bootstrap.min.css');
	else if($direction=='rtl') $this->soconfig->addCss('catalog/view/javascript/soconfig/css/bootstrap/bootstrap.rtl.min.css');
	else  $this->soconfig->addCss('catalog/view/javascript/bootstrap/css/bootstrap.min.css');
	if(!$layouts) 	  $this->soconfig->addCss('catalog/view/javascript/soconfig/css/bootstrap/bootstrap.none.css');

	$this->soconfig->addCss('catalog/view/javascript/font-awesome/css/font-awesome.min.css');
	
	$this->soconfig->addCss('catalog/view/javascript/soconfig/css/lib.css');
	$this->soconfig->addCss('catalog/view/theme/'.$theme.'/css/ie9-and-up.css');
	
	foreach ($styles as $style) $this->soconfig->addCss($style['href']);  
	if(isset($cssfile_status) && $cssfile_status ) foreach ($cssfile as $file) $this->soconfig->addCss($file);  
	
?>

<?php // Begin Themes Scripts ----
	$this->soconfig->addJs('catalog/view/javascript/jquery/jquery-2.1.1.min.js');
	$this->soconfig->addJs('catalog/view/javascript/bootstrap/js/bootstrap.min.js');
	$this->soconfig->addJs('catalog/view/javascript/soconfig/js/libs.js');
	$this->soconfig->addJs('catalog/view/javascript/soconfig/js/so.system.js');
	$this->soconfig->addJs('catalog/view/theme/'.$theme.'/js/so.custom.js');
	$this->soconfig->addJs('catalog/view/theme/'.$theme.'/js/common.js');
	if (!isset($toppanel_status) || $toppanel_status != 0) $this->soconfig->addJs('catalog/view/javascript/soconfig/js/toppanel.js');
	if (!isset($scroll_animation) || $scroll_animation != 0) $this->soconfig->addJs('catalog/view/javascript/soconfig/js/jquery.unveil.js');
	if(!defined('OWL_CAROUSEL')){
		$this->soconfig->addCss('catalog/view/javascript/soconfig/css/owl.carousel.css');
		$this->soconfig->addJs('catalog/view/javascript/soconfig/js/owl.carousel.js');
		define('OWL_CAROUSEL', 1);
	}
	foreach ($scripts as $script) $this->soconfig->addJs($script);
	if(isset($jsfile_status) && $jsfile_status) foreach ($jsfile as $file) $this->soconfig->addJs($file);
	
	$this->soconfig->scss_compass();
    $this->soconfig->css_out();
	$this->soconfig->js_out();
	
?>

<?php //Begin Google Fonts -----?>
<?php if (isset($url_body) && $body_status == 'google'):?> <link href='<?php echo $url_body ?>' rel='stylesheet' type='text/css'> <?php endif; ?>	
<?php if (isset($menu_status) && $menu_status == 'google'):?> <link href='<?php echo $url_menu ?>' rel='stylesheet' type='text/css'> <?php endif; ?>	
<?php if (isset($menu_heading) && $menu_heading == 'google'):?> <link href='<?php echo $url_heading ?>' rel='stylesheet' type='text/css'> <?php endif; ?>	

<?php if (isset($selector_body)) :?>
	<style type="text/css"><?php 
		if ($body_status =='google') echo html_entity_decode($selector_body).'{font-family:'. $family_body.'}';
		else echo $selector_body.'{font-family:'. $normal_body.'}';
		?>
	</style>
<?php endif; ?>		
<?php
 if (isset($selector_menu)) :?>
	<style type="text/css"><?php 
		if ($menu_status =='google') echo html_entity_decode($selector_menu).'{font-family:'. $family_menu.'}';
		else echo $selector_menu.'{font-family:'. $normal_menu.'}';
		?>
	</style>
<?php endif; ?>	

<?php if (isset($selector_heading)) :?>
	<style type="text/css"><?php 
		if ($heading_status =='google') echo html_entity_decode($selector_heading).'{font-family:'. $family_heading.'}';
		else echo $selector_heading.'{font-family:'. $normal_heading.'}';
		?>
	</style>
<?php endif; ?>	
<?php // End Google Fonts ----- ?>

<?php // Begin Custom Code ----?>
<?php if(isset($cssinput_status) && $cssinput_status){?><style type="text/css"><?php echo $custom_css;?></style><?php } ?>
<?php if(isset($jsinput_status) && $jsinput_status){?><script type="text/javascript"><?php echo $custom_js;?></script><?php } ?>
<?php if(isset($general_bgcolor) || isset($contentbg) ):?>	
	<style type="text/css">
	body {
			background-color:<?php echo (!empty($general_bgcolor) ? $general_bgcolor : ''); ?>;
		<?php if (isset($contentbg) && $contentbg != '' && $layoutstyle !='full') : ?>
            background-image: url("image/<?php echo $contentbg; ?>");
            background-repeat:<?php echo (!empty($content_bg_mode) ? $content_bg_mode : ''); ?>;
            background-attachment:<?php echo (!empty($content_attachment) ? $content_attachment : ''); ?>;
			background-position:top center; 
		<?php endif?>
	}
	</style>
<?php endif; ?>	

<?php // End Custom Code ----- ?>

<?php foreach ($links as $link) { ?>
	<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>

<?php foreach ($analytics as $analytic) { ?>
	<?php echo $analytic; ?>
<?php } ?>

</head>

<?php
	//Render a class Body
	$cls_body  =  $class .' ';
	$cls_body .=  $direction.' ' ;
	$cls_body .= (($layouts) ? 'res'.'':'no-res').' ';
	$cls_body .='layout-'.(isset($typelayout) ? $typelayout : '0'). ' ';
	if( $layoutstyle!='full' && $contentbg=='' && $pattern !='none' )$cls_body .='pattern-'. $pattern;
	$cls_wrapper = 'wrapper-'.$layoutstyle.' ' ;
	$cls_wrapper .= 'banners-effect-'.$type_banner;
?>
<body class="<?php echo $cls_body; ?> ">
<div id="wrapper" class="<?php echo $cls_wrapper; ?>">   
	
	
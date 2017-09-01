<?php 
	$our_url = $registry->get('url');
	
?>

<script type="text/javascript">
	
	function _SoQuickView(){
		
		var $item_class = $('<?php echo $class_suffix; ?>');
		if ($item_class.length > 0) {
			for (var i = 0; i < $item_class.length; i++) {
				if($($item_class[i]).find('.quickview_handler').length <= 0){
					var $product_id = $($item_class[i]).find('a', $(this)).attr('data-product');
					if($.isNumeric($product_id) ){
						<?php if($quick_status =='1'):?>
						var _quickviewbutton = "<a class='quickview iframe-link visible-lg btn-button' href='<?php echo $our_url->link('extension/soconfig/quickview','product_id=');?>"+$product_id+"' title=\"<?php echo $label_text?>\" data-toggle=\"tooltip\" data-title =\"<?php echo $label_text?>\" data-fancybox-type=\"iframe\" ><i class=\"fa fa-search\"></i></a>";
						<?php else:?>
						var _quickviewbutton = "";
						<?php endif;?>
						$($item_class[i]).append(_quickviewbutton);
					}
				}
			}
		}
		
	}
	jQuery(document).ready(function ($) {
		_SoQuickView();
		// Hide tooltip when clicking on it
		var hasTooltip = $("[data-toggle='tooltip']").tooltip({container: 'body'});
		hasTooltip.on('click', function () {
				$(this).tooltip('hide')
		});
	});
</script>
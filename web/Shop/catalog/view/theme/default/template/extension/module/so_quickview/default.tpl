<?php 
	$our_url = $registry->get('url');
	
?>

<script type="text/javascript">
	function _SoMagnificPopup(){
		$('.quickview_handler').magnificPopup({
			type:'iframe',
			fixedContentPos: false,
			fixedBgPos: true,
			overflowY: 'scroll',
			closeBtnInside: true,
			closeOnContentClick: true,
			preloader: false,
			midClick: true,
			removalDelay: 300,
		});
	}
	function _SoQuickView(){
		var $item_class = $('<?php echo $class_suffix; ?>');
		if ($item_class.length > 0) {
			for (var i = 0; i < $item_class.length; i++) {
				$($item_class[i]).addClass('so-quickview');
				if($($item_class[i]).find('.quickview_handler').length <= 0){
					
					var producturlpath = $($item_class[i]).find('a', $(this)).attr('href');
					if(typeof producturlpath !== 'undefined' && producturlpath.length > 0 ){
						$product_id = producturlpath.split("=").pop();
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
		_SoMagnificPopup();
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
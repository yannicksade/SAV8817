<?php
    $width_popup = ((int)$width) ? $width : '50%';
    if($image_bg_display){
        $bg = 'background: url(\''.HTTP_SERVER.'image/'.$image.'\') no-repeat white';
    }else{
        $bg = 'background-color: #'.$color_bg.'';
    }
	$txtemail_id = 'txtemail' . rand() . time();
?>
<div id="container-module-newletter" class="hidden-md hidden-sm hidden-xs">
<div class="so_newletter_custom_popup_bg"></div>
<div class="module custom-pop <?php echo $class_suffix; ?> so_newletter_custom_popup so_newletter_oca_popup" id="so_newletter_custom_popup">
    <div class="so-custom-popup so-custom-oca-popup" style="width: <?php echo $width_popup; ?>; <?php echo $bg; ?>;  ">
		<?php if($disp_title_module) { ?>
			<h3 class="modtitle"><?php echo $head_name; ?></h3>
		<?php } ?>
		<?php if($pre_text != '')
			{
		?>
			<div class="form-group">
				<?php echo html_entity_decode($pre_text);?>
			</div>
		<?php 
			}
		?>
		
		<div class="modcontent">
			<div class="oca_popup" id="test-popup">
				<div class="popup-content">
					
					<div class="popup-title font-ct">
						<?php if($title_display)
							{
								echo $title;
							}
						?>
					</div>
					<p class="newsletter_promo font-ct"><?php echo $newsletter_promo ;?></p>
					<form method="post"  name="signup" class="form-group signup">
						<div class="input-control">
							<div class="email">
								<input type="email" placeholder="<?php echo $newsletter_placeholder ; ?>" value="" class="form-control" id="<?php echo $txtemail_id;?>" name="txtemail">
							</div>
							<button class="btn btn-default send-mail" type="submit" onclick="return subscribe_newsletter();" name="submit">
								<i class="fa fa-paper-plane" aria-hidden="true"></i>
							</button>
						</div>
					</form>
					<label class="hidden-popup">
						<input type="checkbox" value="1" name="hidden-popup">
						<span class="inline">&nbsp;&nbsp;<?php echo $input_check ?></span>
					</label>
				</div>
			</div>
            <div class="socials-popup">                     
                <div class="socials-newsletter font-title">
                    <p>Follow Us</p>
                    <ul class="social-list">
                    <li><a title="title" href="http://www.facebook.com/MagenTech"><i class="fa fa-facebook"></i><span class="hidden">Facebook</span></a></li>
                    <li><a title="title" href="https://twitter.com/magentech"><i class="fa fa-twitter"></i><span class="hidden">Twitter</span></a></li>
                    <li><a title="title" href="https://plus.google.com/u/0/+Smartaddons"><i class="fa fa-google-plus"></i><span class="hidden">Google</span></a></li>
                    <li><a title="title" href="#"><i class="fa fa-tumblr"></i><span class="hidden">Tumblr</span></a></li>
                    <li><a title="title" href="#"><i class="fa fa-pinterest"></i><span class="hidden">Pinterest</span></a></li>
                    </ul>
                </div>                  
            </div>
		</div> <!--/.modcontent-->
        <button title="Close" type="button" class="popup-close">&times;</button>
		<?php if($post_text != '')
		{
		?>
			<div class="form-group">
				<?php echo html_entity_decode($post_text);?>
			</div>
		<?php 
		}
		?>
    </div>

</div>

<script type="text/javascript">
    (function($) {
        $(window).load(function () {
            $('.common-home').addClass('hidden-scorll');
            $('.so_newletter_custom_popup_bg').addClass('popup_bg');
            $('input[name=\'hidden-popup\']').on('change', function(){
                if ($(this).is(':checked')) {
                    checkCookie();
                } else {
                    unsetCookie("so_newletter_custom_popup");
                }
            });
            function unsetCookie( name ) {
                document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            }
            $('.popup-close').click(function(){
                var this_close = $('.popup-close');
                this_close.parents().find('.common-home').removeClass('hidden-scorll');
                this_close.parents().find('#container-module-newletter').remove();
            });
        });
    })(jQuery);
	function setCookie(cname, cvalue, exdays) {
		var d = new Date();
		console.log(d.getTime());
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires="+d.toUTCString();
		document.cookie = cname + "=" + cvalue + "; " + expires;
	}
	function getCookie(cname) {
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1);
			if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
		}
		return "";
	}
	function checkCookie() {
		var check_cookie = getCookie("so_newletter_custom_popup");
		if(check_cookie == ""){
			setCookie("so_newletter_custom_popup", "Newletter Popup", <?php echo $expired ?> );
		}
	}
    function subscribe_newsletter_popup()
    {
		//alert(email);
        var emailpattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        var email = $('#<?php echo $txtemail_id;?>').val();
		
        var d = new Date();
        var createdate = d.getFullYear() + '-' + (d.getMonth()+1) + '-' + d.getDate() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
        var status   = 0;
        var dataString = 'email='+email+'&createdate='+createdate+'&status='+status;
        if(email != "")
        {
            if(!emailpattern.test(email))
            {
                $('.show-error').remove();
                $('.send-mail').after('<span class="show-error" style="color: red;margin-left: 10px"> Invalid Email </span>')
                return false;
            }
            else
            {
                $.ajax({
                    url: 'index.php?route=extension/module/so_newletter_custom_popup/newsletter',
                    type: 'post',
                    data: dataString,
                    dataType: 'json',
                    success: function(json) {
                        $('.show-error').remove();
                        if(json.message == "Subscription Successfull") {
							checkCookie();
                            $('.send-mail').after('<span class="show-error" style="color: #003bb3;margin-left: 10px"> ' + json.message + '</span>');
                            setTimeout(function () {
                                var this_close = $('.popup-close');
                                this_close.parent().css('display', 'none');
                                this_close.parents().find('.so_newletter_custom_popup_bg').removeClass('popup_bg');
                            }, 3000);

                        }else{
                            $('.send-mail').after('<span class="show-error" style="color: red;margin-left: 10px"> ' + json.message + '</span>');
                        }
                        var x = document.getElementById('signup');
                        for (i = 0; i < x.length; i++) {
                        x[i].reset();
                        }
                    }
                });
                return false;
            }
        }
        else
        {
            alert("Email Is Require");
            $(email).focus();
            return false;
        }
    }
</script>
</div>
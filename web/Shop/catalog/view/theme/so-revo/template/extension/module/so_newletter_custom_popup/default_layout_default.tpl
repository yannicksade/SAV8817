<?php
    $width_popup = ((int)$width) ? $width : '50%';
    if($image_bg_display){
        $bg = 'background: url(\''.HTTP_SERVER.'image/'.$image.'\')';
    }else{
        $bg = 'background-color: #'.$color_bg.'';
    }

?>
<div class="module <?php echo $class_suffix; ?>">
    <div class="newsletter">
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
		<div class="title-block">
			<?php if($title_display) { ?>
			<div class="page-heading">
				<?php echo $title; ?>
			</div>
			<?php } ?>
			<div class="pre-text" >
					<?php echo $newsletter_promo ;?>			
			</div>
		</div>
		<div class="block_content">
			<form method="post"  name="signup" id="signup" class="btn-group form-inline signup">
				<div class="form-group required send-mail">
					<div class="input-box">
						<input type="email" placeholder="<?php echo $newsletter_placeholder ; ?>" value="" class="form-control" id="txtemail" name="txtemail" size="55">
					</div>
					<div class="subcribe">
						<button class="btn btn-default btn-lg" type="submit" onclick="return subscribe_newsletter();" name="submit">
							<?php echo $newsletter_button ;?>
						</button>
					</div>
				</div>
			</form>
		</div> <!--/.modcontent-->
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
<script type="text/javascript">
    function subscribe_newsletter()
    {
        var emailpattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        var email = $('#txtemail').val();
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
                            $('.send-mail').after('<span class="show-error" style="color: #003bb3;margin-left: 10px"> ' + json.message + '</span>');
                            setTimeout(function () {
                                var this_close = $('.popup-close');
                                this_close.parent().css('display', 'none');
                                this_close.parents().find('.so_newletter_custom_popup_bg').removeClass('popup_bg');
                            }, 3000);

                        }else{
                            $('.send-mail').after('<span class="show-error" style="color: red;margin-left: 10px"> ' + json.message + '</span>');
                        }
                        var x = document.getElementsByClassName('signup');
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


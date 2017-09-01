<?php echo $header; ?>
<div class="container">
  	<ul class="breadcrumb">
    	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
    		<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    	<?php } ?>
  	</ul>
  	<?php if ($error_warning) { ?>
  		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
    		<button type="button" class="close" data-dismiss="alert">&times;</button>
  		</div>
  	<?php } ?>
  	<div class="row">
  		<?php echo $column_left; ?>
    	<?php if ($column_left && $column_right) { ?>
    		<?php $class = 'col-sm-6'; ?>
    	<?php } elseif ($column_left || $column_right) { ?>
    		<?php $class = 'col-sm-9'; ?>
    	<?php } else { ?>
    		<?php $class = 'col-sm-12'; ?>
    	<?php } ?>
    	<div id="content" class="<?php echo $class; ?>">
    		<?php echo $content_top; ?>
    		<h1><?php echo $heading_title; ?></h1>
    		<div class="so-onepagecheckout <?php if ($is_logged_in) echo 'is_customer'?> <?php echo ($setting_so_onepagecheckout_general['so_onepagecheckout_layout'] == 2 ? 'layout2' : '')?>">
    			<div class="row">
                    <div class="col-left col-sm-5">
        				<?php if (!$is_logged_in) { ?>
    	    				<div class="checkout-content login-box">
    	    					<h2 class="secondary-title"><i class="fa fa-user"></i><?php echo $text_checkout_create_account_login; ?></h2>
                                <?php if ($setting_so_onepagecheckout_layout_setting['so_onepagecheckout_register_checkout'] == 1) {?>
    	    					<div class="box-inner">
                                    <div class="radio">
        								<label><input type="radio" name="account" value="register" <?php if (@$default_auth === 'register'): ?> checked="checked" <?php endif; ?>><?php echo $text_register; ?></label>
        							</div>
                                    <?php }?>
        							<?php if ($allow_guest_checkout && $setting_so_onepagecheckout_layout_setting['so_onepagecheckout_guest_checkout'] == 1) { ?>
        	                        <div class="radio">
        	                            <label><input type="radio" name="account" value="guest" <?php if ($default_auth === 'guest'): ?> checked="checked" <?php endif; ?> /><?php echo $text_guest; ?></label>
        	                        </div>
        	                        <?php } ?>
                                    <?php if ($setting_so_onepagecheckout_layout_setting['so_onepagecheckout_enable_login'] == 1) {?>
        							<div class="radio">
        								<label><input type="radio" name="account" value="login" <?php if (@$default_auth === 'login'): ?> checked="checked" <?php endif; ?>><?php echo $text_returning_customer; ?></label>
                                    </div>
    							</div>
                                <?php }?>
    						</div>
    					<?php }?>

    					<?php if (!$is_logged_in && $setting_so_onepagecheckout_layout_setting['so_onepagecheckout_enable_login'] == 1) {?>
    	                    <div class="checkout-content checkout-login">
    	                        <fieldset>
    	                            <h2 class="secondary-title"><i class="fa fa-unlock"></i><?php echo $text_returning_customer; ?></h2>
    	                            <div class="box-inner">
                                        <div class="form-group">
        	                                <input type="text" name="login_email" value="" placeholder="<?php echo $entry_email; ?>" id="input-login_email" class="form-control" />
        	                            </div>
        	                            <div class="form-group">
        	                                <input type="password" name="login_password" value="" placeholder="<?php echo $entry_password; ?>" id="input-login_password" class="form-control" />
        	                                <a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>
        	                            </div>
        	                            <div class="form-group">
        	                                <input type="button" value="<?php echo $button_login; ?>" id="button-login" data-loading-text="<?php echo $text_loading; ?>" class="btn-primary button" />
        	                            </div>
                                    </div>
    	                        </fieldset>
    	                    </div>
    	                    <?php echo $register_form; ?>
                        <?php }?>
                        <?php if ($is_logged_in) { ?>
                            <?php echo $payment_address; ?>
                        <?php }?>

                        <?php if ($is_logged_in) { ?>
                            <?php if ($is_shipping_required) { ?>
                                <?php echo $shipping_address; ?>
                            <?php }?>
                        <?php }?>
        			</div>
        			<div class="col-right col-sm-7">
                        <div class="row">
                            <div class="col-md-6 col-xs-12">
                                <?php echo $payment_methods; ?>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <?php if ($is_shipping_required) {?>
                                    <?php echo $shipping_methods; ?>
                                <?php }?>
                            </div>
                        </div>
        				
        				<section class="section-right">
                            <div id="coupon_voucher_reward">
        					   <?php echo $coupon_voucher_reward; ?>
                            </div>

                            <?php echo $cart; ?>
                            
        					<div class="checkout-content confirm-section">
                                <?php if ($setting_so_onepagecheckout_layout_setting['comment_status']) {?>
                                <h2 class="secondary-title"><i class="fa fa-comment"></i><?php echo $text_comments; ?></h2>
								<div class="box-inner">
									<textarea name="comment" rows="8" class="form-control <?php if ($setting_so_onepagecheckout_layout_setting['require_comment_status']) echo 'requried'?>"><?php echo $comment; ?></textarea>
								</div>
                                <?php }?>
                                <div class="box-inner">
                                    <?php if ($entry_newsletter && $setting_so_onepagecheckout_layout_setting['show_newsletter']): ?>
                                    <div class="checkbox check-newsletter">
                                        <label for="newsletter">
                                            <input type="checkbox" name="newsletter" value="1" id="newsletter" />
                                            <?php echo $entry_newsletter; ?>
                                        </label>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($text_privacy && $setting_so_onepagecheckout_layout_setting['show_privacy']): ?>
                                    <div class="checkbox check-privacy">
                                        <label>
                                            <input type="checkbox" name="privacy" value="1" />
                                            <?php echo $text_privacy; ?>
                                        </label>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($text_agree && $setting_so_onepagecheckout_layout_setting['show_term']): ?>
                                    <div class="checkbox check-terms">
                                        <label>
                                            <input type="checkbox" name="agree" value="1" />
                                            <?php echo $text_agree; ?>
                                        </label>
                                    </div>
                                    <?php endif; ?>
                                    <div class="confirm-order">
                                        <button id="so-checkout-confirm-button" data-loading-text="<?php echo $text_loading?>" class="btn btn-primary button confirm-button"><?php echo $text_confirm_order?></button>
                                    </div>
                                </div>
                            </div>
        				</section>
                    </div>
    			</div>
    		</div>
    		<?php echo $content_bottom; ?>
    	</div>
    </div>
</div>
<?php echo $footer; ?>
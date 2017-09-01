<?php if ($setting_so_onepagecheckout_general['so_onepagecheckout_layout'] == 2) {?>
    <div class="panel-group" id="accordion">
        <?php if ($coupon_status): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#collapse-coupon" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion"><i class="fa fa-qrcode"></i> <?php echo $text_enter_coupon_code?> <i class="fa fa-caret-down"></i></a>
                </h4>
            </div>
            <div id="collapse-coupon" class="panel-collapse collapse">
                <div class="panel-body">
                    <label class="control-label" for="input-coupon"><?php echo $text_enter_coupon_code?></label>
                    <div class="input-group">
                        <input type="text" name="coupon" value="<?php echo $coupon; ?>" placeholder="<?php echo $text_enter_coupon_code?>" id="input-coupon" class="form-control">
                        <span class="input-group-btn">
                            <input type="button" value="Apply Coupon" id="button-coupon" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary">
                        </span>
                    </div>                    
                </div>
              </div>
        </div>
        <?php endif;?>
        <?php if ($voucher_status): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#collapse-voucher" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion"><i class="fa fa-gift"></i> <?php echo $text_enter_voucher_code?> <i class="fa fa-caret-down"></i></a>
                </h4>
            </div>
            <div id="collapse-voucher" class="panel-collapse collapse">
                <div class="panel-body">
                    <label class="control-label" for="input-voucher"><?php echo $text_enter_voucher_code?></label>
                    <div class="input-group">
                        <input type="text" name="voucher" value="<?php echo $voucher; ?>" placeholder="<?php echo $text_enter_voucher_code?>" id="input-coupon" class="form-control">
                        <span class="input-group-btn">
                            <input type="button" value="<?php echo $text_apply_voucher?>" id="button-voucher" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary">
                        </span>
                    </div>                    
                </div>
              </div>
        </div>
        <?php endif;?>
        <?php if ($reward_status): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a href="#collapse-reward" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion"><i class="fa fa-gift"></i> <?php echo $text_enter_reward_points?> <i class="fa fa-caret-down"></i></a>
                </h4>
            </div>
            <div id="collapse-reward" class="panel-collapse collapse">
                <div class="panel-body">
                    <label class="control-label" for="input-reward"><?php echo $text_enter_reward_points?></label>
                    <div class="input-group">
                        <input type="text" name="reward" value="<?php echo $reward; ?>" placeholder="<?php echo $text_enter_reward_points?>" id="input-coupon" class="form-control">
                        <span class="input-group-btn">
                            <input type="button" value="<?php echo $text_apply_points?>" id="button-reward" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary">
                        </span>
                    </div>                    
                </div>
              </div>
        </div>
        <?php endif;?>
    </div>
<?php }else {?>
    <div class="checkout-content coupon-voucher">
        <h2 class="secondary-title"><i class="fa fa-gift"></i><?php echo $text_coupon_voucher?></h2>
        <div class="box-inner">
            <?php if ($coupon_status): ?>
            <div class="panel-body checkout-coupon">
                <label class="col-sm-2 control-label" for="input-coupon"><?php echo $text_enter_coupon_code?></label>
                <div class="input-group">
                    <input type="text" name="coupon" value="<?php echo $coupon; ?>" placeholder="<?php echo $text_enter_coupon_code?>" id="input-coupon" class="form-control" />
                    <span class="input-group-btn">
                        <input type="button" value="Apply Coupon" id="button-coupon" data-loading-text="<?php echo $text_loading; ?>"  class="btn-primary button" />
                    </span>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($voucher_status): ?>
            <div class="panel-body checkout-voucher">
                <label class="col-sm-2 control-label" for="input-voucher"><?php echo $text_enter_voucher_code;?></label>
                <div class="input-group">
                    <input type="text" name="voucher" value="<?php echo $voucher; ?>" placeholder="<?php echo $text_enter_voucher_code;?>" id="input-voucher" class="form-control" />
                    <span class="input-group-btn">
                        <input type="button" value="<?php echo $text_apply_voucher?>" id="button-voucher" data-loading-text="<?php echo $text_loading; ?>"  class="btn-primary button" />
                    </span>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($reward_status): ?>
                <div class="panel-body checkout-reward">
                    <label class="col-sm-2 control-label" for="input-reward"><?php echo $text_enter_reward_points?></label>
                    <div class="input-group">
                        <input type="text" name="reward" value="<?php echo $reward; ?>" placeholder="<?php echo $text_enter_reward_points?>" id="input-reward" class="form-control" />
                        <span class="input-group-btn">
                            <input type="button" value="<?php echo $text_apply_points?>" id="button-reward" data-loading-text="<?php echo $text_loading; ?>"  class="btn-primary button" />
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php }?>
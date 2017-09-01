<?php if ($setting_so_onepagecheckout_layout_setting['delivery_method_status'] == 1) {?>
<div class="checkout-content checkout-shipping-methods">
    <?php if ($error_warning) { ?>
        <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
    <?php } ?>
    <?php if ($shipping_methods) { ?>
        <h2 class="secondary-title"><i class="fa fa-location-arrow"></i><?php echo $text_title_shipping_method;?></h2>
        <div class="box-inner">
            <?php foreach ($shipping_methods as $key=>$shipping_method) { ?>
                <p><strong><?php echo $shipping_method['title']; ?></strong></p>
                <?php if (!$shipping_method['error']) { ?>
                    <?php foreach ($shipping_method['quote'] as $quote) { ?>
                        <?php $_status = explode('.', $quote['code']);?>
                        <?php if (isset($setting_so_onepagecheckout_layout_setting[$_status[1].'_status']) && $setting_so_onepagecheckout_layout_setting[$_status[1].'_status'] == 1) {?>
                            <div class="radio">
                                <label>
                                    <?php if ($quote['code'] == $code || !$code || @$setting_so_onepagecheckout_layout_setting['so_onepagecheckout_default_shipping'] == $_status[0]) { ?>
                                        <?php $code = $quote['code']; ?>
                                        <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" checked="checked"/>
                                    <?php } else { ?>
                                        <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>"/>
                                    <?php } ?>
                                    <?php echo $quote['title']; ?> - <?php echo $quote['text']; ?>
                                </label>
                            </div>
                        <?php }?>
                    <?php }?>
                <?php } else { ?>
                    <div class="alert alert-danger"><?php echo $shipping_method['error']; ?></div>
                <?php } ?>
            <?php } ?>
        </div>
    <?php }?>
</div>
<?php }?>
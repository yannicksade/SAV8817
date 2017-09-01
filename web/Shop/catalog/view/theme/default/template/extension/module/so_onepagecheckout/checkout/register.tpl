<div class="checkout-content checkout-register">
    <fieldset id="account">
        <h2 class="secondary-title"><i class="fa fa-user-plus"></i><?php echo $text_your_details; ?></h2>
        <div class="payment-new box-inner">
            <div class="form-group customer-group" style="display: <?php echo(count($customer_groups) > 1 ? 'block' : 'none'); ?>;">
                <label class="control-label"><?php echo $entry_customer_group; ?></label>
                <?php foreach ($customer_groups as $customer_group) { ?>
                    <?php if ($customer_group['customer_group_id'] == $customer_group_id) { ?>
                        <div class="radio">
                            <label>
                                <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>" checked="checked"/>
                                <?php echo $customer_group['name']; ?>
                            </label>
                        </div>
                    <?php } else { ?>
                        <div class="radio">
                            <label>
                                <input type="radio" name="customer_group_id" value="<?php echo $customer_group['customer_group_id']; ?>"/>
                                <?php echo $customer_group['name']; ?>
                            </label>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="form-group input-firstname required" style="width: 49%; float: left;">
                <input type="text" name="firstname" value="<?php echo SoUtils::getProperty($order_data, 'firstname'); ?>" placeholder="<?php echo $entry_firstname; ?> *" id="input-payment-firstname" class="form-control"/>
            </div>
            <div class="form-group input-lastname required" style="width: 49%; float: right;">
                <input type="text" name="lastname" value="<?php echo SoUtils::getProperty($order_data, 'lastname'); ?>" placeholder="<?php echo $entry_lastname; ?> *" id="input-payment-lastname" class="form-control"/>
            </div>
            <div class="form-group required">
                <input type="text" name="email" value="<?php echo SoUtils::getProperty($order_data, 'email'); ?>" placeholder="<?php echo $entry_email; ?> *" id="input-payment-email" class="form-control"/>
            </div>
            <div class="form-group required">
                <input type="text" name="telephone" value="<?php echo SoUtils::getProperty($order_data, 'telephone'); ?>" placeholder="<?php echo $entry_telephone; ?> *" id="input-payment-telephone" class="form-control"/>
            </div>
            <div class="form-group fax-input">
                <input type="text" name="fax" value="<?php echo SoUtils::getProperty($order_data, 'fax'); ?>" placeholder="<?php echo $entry_fax; ?>" id="input-payment-fax" class="form-control"/>
            </div>
            <?php foreach ($custom_fields as $custom_field) { ?>
                <?php if ($custom_field['location'] == 'account') { ?>
                    <?php if ($custom_field['type'] == 'select') { ?>
                        <div id="payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                             class="form-group <?php echo ($custom_field['required'] ? ' required' : ''); ?> custom-field" data-sort="<?php echo $custom_field['sort_order']; ?>">
                            <label class="control-label" for="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
                            <select
                                name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                                class="form-control">
                                <option value=""><?php echo $text_select; ?></option>
                                <?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
                                    <option
                                        value="<?php echo $custom_field_value['custom_field_value_id']; ?>"><?php echo $custom_field_value['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                    <?php if ($custom_field['type'] == 'radio') { ?>
                        <div id="payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                             class="form-group <?php echo ($custom_field['required'] ? ' required' : ''); ?> custom-field" data-sort="<?php echo $custom_field['sort_order']; ?>">
                            <label class="control-label"><?php echo $custom_field['name']; ?></label>

                            <div id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>">
                                <?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>"/>
                                            <?php echo $custom_field_value['name']; ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($custom_field['type'] == 'checkbox') { ?>
                        <div id="payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                             class="form-group <?php echo ($custom_field['required'] ? ' required' : ''); ?> custom-field" data-sort="<?php echo $custom_field['sort_order']; ?>">
                            <label class="control-label"><?php echo $custom_field['name']; ?></label>

                            <div id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>">
                                <?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="custom_field[<?php echo $custom_field['custom_field_id']; ?>][]" value="<?php echo $custom_field_value['custom_field_value_id']; ?>"/>
                                            <?php echo $custom_field_value['name']; ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($custom_field['type'] == 'text') { ?>
                        <div id="payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                             class="form-group <?php echo ($custom_field['required'] ? ' required' : ''); ?> custom-field" data-sort="<?php echo $custom_field['sort_order']; ?>">
                            <label class="control-label" for="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
                            <input type="text"
                                   name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                   value="<?php echo $custom_field['value']; ?>"
                                   placeholder="<?php echo $custom_field['name']; ?>"
                                   id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                                   class="form-control"/>
                        </div>
                    <?php } ?>
                    <?php if ($custom_field['type'] == 'textarea') { ?>
                        <div id="payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                             class="form-group <?php echo ($custom_field['required'] ? ' required' : ''); ?> custom-field" data-sort="<?php echo $custom_field['sort_order']; ?>">
                            <label class="control-label" for="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>
                            <textarea
                                name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                rows="5" placeholder="<?php echo $custom_field['name']; ?>"
                                id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                                class="form-control"><?php echo $custom_field['value']; ?></textarea>
                        </div>
                    <?php } ?>
                    <?php if ($custom_field['type'] == 'file') { ?>
                        <div id="payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                             class="form-group <?php echo ($custom_field['required'] ? ' required' : ''); ?> custom-field" data-sort="<?php echo $custom_field['sort_order']; ?>">
                            <label class="control-label"><?php echo $custom_field['name']; ?></label>
                            <br/>
                            <button type="button"
                                    id="button-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                                    data-loading-text="<?php echo $text_loading; ?>" class="btn btn-default"><i
                                    class="fa fa-upload"></i> <?php echo $button_upload; ?></button>
                            <input type="hidden"
                                   name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                   value=""
                                   id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"/>
                        </div>
                    <?php } ?>
                    <?php if ($custom_field['type'] == 'date') { ?>
                        <div id="payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                             class="form-group <?php echo ($custom_field['required'] ? ' required' : ''); ?> custom-field" data-sort="<?php echo $custom_field['sort_order']; ?>">
                            <label class="control-label"
                                   for="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>

                            <div class="input-group date">
                                <input type="text"
                                       name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                       value="<?php echo $custom_field['value']; ?>"
                                       placeholder="<?php echo $custom_field['name']; ?>" data-date-format="YYYY-MM-DD"
                                       id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                                       class="form-control"/>
                                <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($custom_field['type'] == 'time') { ?>
                        <div id="payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                             class="form-group <?php echo ($custom_field['required'] ? ' required' : ''); ?> custom-field" data-sort="<?php echo $custom_field['sort_order']; ?>">
                            <label class="control-label"
                                   for="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>

                            <div class="input-group time">
                                <input type="text"
                                       name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                       value="<?php echo $custom_field['value']; ?>"
                                       placeholder="<?php echo $custom_field['name']; ?>" data-date-format="HH:mm"
                                       id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                                       class="form-control"/>
                                <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($custom_field['type'] == 'datetime') { ?>
                        <div id="payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                             class="form-group <?php echo ($custom_field['required'] ? ' required' : ''); ?> custom-field" data-sort="<?php echo $custom_field['sort_order']; ?>">
                            <label class="control-label"
                                   for="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>

                            <div class="input-group datetime">
                                <input type="text"
                                       name="custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                       value="<?php echo $custom_field['value']; ?>"
                                       placeholder="<?php echo $custom_field['name']; ?>"
                                       data-date-format="YYYY-MM-DD HH:mm"
                                       id="input-payment-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                                       class="form-control"/>
                                <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </div>
    </fieldset>
    <fieldset id="password" style="display: <?php echo ($setting_so_onepagecheckout_layout_setting['so_onepagecheckout_account_open'] == 'register' ? 'block' : 'none')?>">
        <?php if ($setting_so_onepagecheckout_general['so_onepagecheckout_layout'] != 2) {?>
        <h2 class="secondary-title"><i class="fa fa-lock"></i><?php echo $text_your_password; ?></h2>
        <?php }?>
        <div class="box-inner">
            <div class="form-group required">
                <input type="password" name="password" value="<?php echo SoUtils::getProperty($order_data, 'password'); ?>" placeholder="<?php echo $entry_password; ?> *" id="input-payment-password" class="form-control"/>
            </div>
            <div class="form-group required">
                <input type="password" name="confirm" value="<?php echo SoUtils::getProperty($order_data, 'confirm'); ?>" placeholder="<?php echo $entry_confirm; ?> *" id="input-payment-confirm" class="form-control"/>
            </div>
        </div>
    </fieldset>
    <fieldset id="address">
        <h2 class="secondary-title"><i class="fa fa-map-marker"></i><?php echo $text_your_address; ?></h2>
        <?php echo $payment_address_form; ?>
    </fieldset>
    <?php if ($is_shipping_required): ?>
        <div class="checkbox">
            <label>
                <?php if ($shipping_address == '1'): ?>
                    <input type="checkbox" name="shipping_address" value="1" checked="checked" />
                <?php else: ?>
                    <input type="checkbox" name="shipping_address" value="0" />
                <?php endif; ?>
                <?php echo $entry_shipping; ?>
            </label>
        </div>
        <fieldset id="shipping-address" style="display: <?php echo $shipping_address == '1' ? 'none' : 'block'; ?>">
            <h2 class="secondary-title"><i class="fa fa-map-marker"></i><?php echo $text_shipping_address;?></h2>
            <?php echo $shipping_address_form; ?>
        </fieldset>
    <?php endif; ?>
</div>
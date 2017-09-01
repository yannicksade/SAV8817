<div class="<?php echo $is_logged_in ? 'checkout-content' : ''; ?> checkout-<?php echo $type; ?>-form">
    <?php if (@$is_logged_in): ?>
    <h2 class="secondary-title"><i class="fa fa-user"></i><?php echo $type === 'payment' ? 'Billing Address' : 'Delivery Address'; ?> </h2>
    <?php endif; ?>
    <div class="box-inner">
        <form class="form-horizontal form-<?php echo $type; ?>">
            <?php if ($addresses) { ?>
                <div class="radio">
                    <label>
                        <input type="radio" name="<?php echo $type; ?>_address" value="existing" checked="checked" />
                        <?php echo $text_address_existing; ?>
                    </label>
                </div>
                <div id="<?php echo $type; ?>-existing">
                    <select name="<?php echo $type; ?>_address_id" class="form-control">
                        <?php foreach ($addresses as $address) { ?>
                            <?php if ($address['address_id'] == $address_id) { ?>
                                <option value="<?php echo $address['address_id']; ?>"
                                        selected="selected"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>
                                    , <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>
                                    , <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
                            <?php } else { ?>
                                <option
                                    value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname']; ?> <?php echo $address['lastname']; ?>
                                    , <?php echo $address['address_1']; ?>, <?php echo $address['city']; ?>
                                    , <?php echo $address['zone']; ?>, <?php echo $address['country']; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="<?php echo $type; ?>_address" value="new"/>
                        <?php echo $text_address_new; ?></label>
                </div>
            <?php } ?>
            <div id="<?php echo $type; ?>-new" style="display: <?php echo($addresses ? 'none' : 'block'); ?>;">
                <?php if ($name): ?>
                    <div class="form-group required">
                        <input type="text" name="<?php echo $type; ?>_firstname" value="<?php echo SoUtils::getProperty($order_data, $type . '_firstname'); ?> *" placeholder="<?php echo $entry_firstname; ?>" id="input-<?php echo $type; ?>-firstname" class="form-control" />
                    </div>
                    <div class="form-group required">
                        <input type="text" name="<?php echo $type; ?>_lastname" value="<?php echo SoUtils::getProperty($order_data, $type . '_lastname'); ?>" placeholder="<?php echo $entry_lastname; ?> *" id="input-<?php echo $type; ?>-lastname" class="form-control"/>
                    </div>
                <?php endif; ?>
                <div class="form-group company-input">
                    <input type="text" name="<?php echo $type; ?>_company" value="<?php echo SoUtils::getProperty($order_data, $type . '_company'); ?>" placeholder="<?php echo $entry_company; ?>" id="input-<?php echo $type; ?>-company" class="form-control"/>
                </div>
                <div class="form-group required">
                    <input type="text" name="<?php echo $type; ?>_address_1" value="<?php echo SoUtils::getProperty($order_data, $type . '_address_1'); ?>" placeholder="<?php echo $entry_address_1; ?> *" id="input-<?php echo $type; ?>-address-1" class="form-control"/>
                </div>
                <div class="form-group address-2-input">
                    <input type="text" name="<?php echo $type; ?>_address_2" value="<?php echo SoUtils::getProperty($order_data, $type . '_address_2'); ?>" placeholder="<?php echo $entry_address_2; ?>" id="input-<?php echo $type; ?>-address-2" class="form-control"/>
                </div>
                <div class="form-group required">
                    <input type="text" name="<?php echo $type; ?>_city" value="<?php echo SoUtils::getProperty($order_data, $type . '_city'); ?>" placeholder="<?php echo $entry_city; ?> *" id="input-<?php echo $type; ?>-city" class="form-control"/>
                </div>
                <div class="form-group required">
                    <input type="text" name="<?php echo $type; ?>_postcode" value="<?php echo $postcode; ?>" placeholder="<?php echo $entry_postcode; ?> *" id="input-<?php echo $type; ?>-postcode" class="form-control"/>
                </div>
                <div class="form-group required">
                    <select name="<?php echo $type; ?>_country_id" id="input-<?php echo $type; ?>-country" class="form-control">
                        <option value=""><?php echo $text_select; ?></option>
                        <?php foreach ($countries as $country) { ?>
                            <?php if ($country['country_id'] == $country_id) { ?>
                                <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                            <?php }?>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group required">
                    <select name="<?php echo $type; ?>_zone_id" id="input-<?php echo $type; ?>-zone" class="form-control">
                    </select>
                </div>
                <?php foreach ($custom_fields as $custom_field) { ?>
                    <?php if ($custom_field['location'] == 'address') { ?>
                        <?php if ($custom_field['type'] == 'select') { ?>
                            <div class="form-group<?php echo($custom_field['required'] ? ' required' : ''); ?> custom-field"
                                 data-sort="<?php echo $custom_field['sort_order']; ?>">
                                <label class="col-sm-2 control-label"
                                       for="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>

                                    <select name="<?php echo $type; ?>_custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                            id="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"
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
                            <div class="form-group<?php echo($custom_field['required'] ? ' required' : ''); ?> custom-field"
                                 data-sort="<?php echo $custom_field['sort_order']; ?>">
                                <label class="col-sm-2 control-label"><?php echo $custom_field['name']; ?></label>

                                    <div id="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>">
                                        <?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio"
                                                           name="<?php echo $type; ?>_custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                                           value="<?php echo $custom_field_value['custom_field_value_id']; ?>"/>
                                                    <?php echo $custom_field_value['name']; ?></label>
                                            </div>
                                        <?php } ?>
                                    </div>
                            </div>
                        <?php } ?>
                        <?php if ($custom_field['type'] == 'checkbox') { ?>
                            <div class="form-group<?php echo($custom_field['required'] ? ' required' : ''); ?> custom-field"
                                 data-sort="<?php echo $custom_field['sort_order']; ?>">
                                <label class="col-sm-2 control-label"><?php echo $custom_field['name']; ?></label>

                                    <div id="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>">
                                        <?php foreach ($custom_field['custom_field_value'] as $custom_field_value) { ?>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox"
                                                           name="<?php echo $type; ?>_custom_field[<?php echo $custom_field['custom_field_id']; ?>][]"
                                                           value="<?php echo $custom_field_value['custom_field_value_id']; ?>"/>
                                                    <?php echo $custom_field_value['name']; ?></label>
                                            </div>
                                        <?php } ?>
                                    </div>
                            </div>
                        <?php } ?>
                        <?php if ($custom_field['type'] == 'text') { ?>
                            <div class="form-group<?php echo($custom_field['required'] ? ' required' : ''); ?> custom-field"
                                 data-sort="<?php echo $custom_field['sort_order']; ?>">
                                <label class="col-sm-2 control-label"
                                       for="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>

                                    <input type="text" name="<?php echo $type; ?>_custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                           value="<?php echo $custom_field['value']; ?>"
                                           placeholder="<?php echo $custom_field['name']; ?>"
                                           id="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                                           class="form-control"/>
                            </div>
                        <?php } ?>
                        <?php if ($custom_field['type'] == 'textarea') { ?>
                            <div class="form-group<?php echo($custom_field['required'] ? ' required' : ''); ?> custom-field"
                                 data-sort="<?php echo $custom_field['sort_order']; ?>">
                                <label class="col-sm-2 control-label"
                                       for="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>

                                    <textarea name="<?php echo $type; ?>_custom_field[<?php echo $custom_field['custom_field_id']; ?>]" rows="5"
                                              placeholder="<?php echo $custom_field['name']; ?>"
                                              id="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                                              class="form-control"><?php echo $custom_field['value']; ?></textarea>
                            </div>
                        <?php } ?>
                        <?php if ($custom_field['type'] == 'file') { ?>
                            <div class="form-group<?php echo($custom_field['required'] ? ' required' : ''); ?> custom-field"
                                 data-sort="<?php echo $custom_field['sort_order']; ?>">
                                <label class="col-sm-2 control-label"><?php echo $custom_field['name']; ?></label>

                                    <button type="button"
                                            id="button-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                                            data-loading-text="<?php echo $text_loading; ?>" class="btn btn-default"><i
                                            class="fa fa-upload"></i> <?php echo $button_upload; ?></button>
                                    <input type="hidden" name="<?php echo $type; ?>_custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                           value=""
                                           id="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"/>
                            </div>
                        <?php } ?>
                        <?php if ($custom_field['type'] == 'date') { ?>
                            <div class="form-group<?php echo($custom_field['required'] ? ' required' : ''); ?> custom-field"
                                 data-sort="<?php echo $custom_field['sort_order']; ?>">
                                <label class="col-sm-2 control-label"
                                       for="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>

                                    <div class="input-group date">
                                        <input type="text" name="<?php echo $type; ?>_custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                               value="<?php echo $custom_field['value']; ?>"
                                               placeholder="<?php echo $custom_field['name']; ?>" data-date-format="YYYY-MM-DD"
                                               id="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                                               class="form-control"/>
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
                            </div>
                        <?php } ?>
                        <?php if ($custom_field['type'] == 'time') { ?>
                            <div class="form-group<?php echo($custom_field['required'] ? ' required' : ''); ?> custom-field"
                                 data-sort="<?php echo $custom_field['sort_order']; ?>">
                                <label class="col-sm-2 control-label"
                                       for="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>

                                    <div class="input-group time">
                                        <input type="text" name="<?php echo $type; ?>_custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                               value="<?php echo $custom_field['value']; ?>"
                                               placeholder="<?php echo $custom_field['name']; ?>" data-date-format="HH:mm"
                                               id="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                                               class="form-control"/>
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
                            </div>
                        <?php } ?>
                        <?php if ($custom_field['type'] == 'datetime') { ?>
                            <div class="form-group<?php echo($custom_field['required'] ? ' required' : ''); ?> custom-field"
                                 data-sort="<?php echo $custom_field['sort_order']; ?>">
                                <label class="col-sm-2 control-label"
                                       for="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"><?php echo $custom_field['name']; ?></label>

                                    <div class="input-group datetime">
                                        <input type="text" name="<?php echo $type; ?>_custom_field[<?php echo $custom_field['custom_field_id']; ?>]"
                                               value="<?php echo $custom_field['value']; ?>"
                                               placeholder="<?php echo $custom_field['name']; ?>"
                                               data-date-format="YYYY-MM-DD HH:mm"
                                               id="input-<?php echo $type; ?>-custom-field<?php echo $custom_field['custom_field_id']; ?>"
                                               class="form-control"/>
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </form>    
    </div>
</div>
<input type="hidden" name="default_zone_id" id="default_zone_id" value="<?php echo @$zone_id?>" />
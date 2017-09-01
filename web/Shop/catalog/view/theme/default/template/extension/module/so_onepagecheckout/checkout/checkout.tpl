<?php echo $header; ?>
<div id="container" class="container j-container">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="row"><?php echo $column_left; ?><?php echo $column_right; ?>
        <?php if ($column_left && $column_right) { ?>
            <?php $class = 'col-sm-6'; ?>
        <?php } elseif ($column_left || $column_right) { ?>
            <?php $class = 'col-sm-9'; ?>
        <?php } else { ?>
            <?php $class = 'col-sm-12'; ?>
        <?php } ?>
        <div id="content" class="one-page-checkout <?php echo $class; ?>">
            <h1 class="heading-title"><?php echo $this->journal2->settings->get('one_page_title', 'Quick Checkout'); ?></h1>
            <?php echo $content_top; ?>

            <div class="journal-checkout">
                <div class="left">
                    <?php if (!$is_logged_in): ?>
                    <div class="checkout-content login-box">
                        <h2 class="secondary-title"><?php echo $this->journal2->settings->get('one_page_lang_register_selector', 'Create an Account or Login'); ?></h2>
                        <div class="radio">
                            <label>
                                <input type="radio" name="account" value="register" <?php if ($default_auth === 'register'): ?> checked="checked" <?php endif; ?> />
                                <?php echo $text_register; ?>
                            </label>
                        </div>
                        <?php if ($allow_guest_checkout) { ?>
                        <div class="radio">
                            <label>
                                <input type="radio" name="account" value="guest" <?php if ($default_auth === 'guest'): ?> checked="checked" <?php endif; ?> />
                                <?php echo $text_guest; ?>
                            </label>
                        </div>
                        <?php } ?>
                        <div class="radio">
                            <label>
                                <input type="radio" name="account" value="login" <?php if ($default_auth === 'login'): ?> checked="checked" <?php endif; ?>/>
                                <?php echo $text_returning_customer; ?>
                            </label>
                        </div>
                    </div>
                    <script>
                        $(document).delegate('input[name="shipping_address"]', 'change', function() {
                            var $this = $(this);

                            if ($this.is(':checked')) {
                                $('#shipping-address').hide();
                                $this.val(1);
                                $(document).trigger('journal_checkout_address_changed', 'payment');
                            } else {
                                $('#shipping-address').show().find('input[type="text"]').val('');
                                $(document).trigger('journal_checkout_address_changed', 'payment');
                                $(document).trigger('journal_checkout_address_changed', 'shipping');
                                $this.val(0);
                            }
                        });
                        $(document).delegate('input[name="account"]', 'change', function() {
                            if (this.value === 'login') {
                                $('.checkout-login').slideDown(300);
                                $('.checkout-register').parent().addClass('login-mobile');
                                //$('.checkout-register').slideUp(300);
                            } else {
                                $('.checkout-login').slideUp(300);
                                $('.checkout-register').parent().removeClass('login-mobile');
                                //$('.checkout-register').slideDown(300);
                                if (this.value === 'register') {
                                    $('#password').slideDown(300);
                                } else {
                                    $('#password').slideUp(300);
                                }
                            }

                            $('html').removeClass('checkout-type-login checkout-type-register checkout-type-guest').addClass('checkout-type-' + this.value);
                        });
                    </script>
                    <?php endif; ?>

                    <?php if (!$is_logged_in): ?>
                    <div class="checkout-content checkout-login">
                        <fieldset>
                            <h2 class="secondary-title"><?php echo $text_returning_customer; ?></h2>
                            <div class="form-group">
                                <label class="control-label" for="input-login_email"><?php echo $entry_email; ?></label>
                                <input type="text" name="login_email" value="" placeholder="<?php echo $entry_email; ?>" id="input-login_email" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-login_password"><?php echo $entry_password; ?></label>
                                <input type="password" name="login_password" value="" placeholder="<?php echo $entry_password; ?>" id="input-login_password" class="form-control" />
                                <a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>
                            </div>
                            <div class="form-group">
                                <input type="button" value="<?php echo $button_login; ?>" id="button-login" data-loading-text="<?php echo $text_loading; ?>" class="btn-primary button" />
                            </div>
                        </fieldset>
                    </div>

                    <?php echo $register_form; ?>
                    <?php endif; ?>
                </div>
                <div class="right">
                    <section class="section-left">
                        <?php if ($is_logged_in): ?>
                        <?php echo $payment_address; ?>
                        <?php if ($is_shipping_required): ?>
                        <?php echo $shipping_address; ?>
                        <?php endif; ?>
                        <?php endif; ?>
                        <div class="spw">
                            <?php if ($is_shipping_required): ?>
                            <?php echo $shipping_methods; ?>
                            <?php endif; ?>
                            <?php echo $payment_methods; ?>
                        </div>
                    </section>
                    <section class="section-right">
                        <?php echo $coupon_voucher_reward; ?>
                        <?php echo $cart; ?>
                        <div class="checkout-content confirm-section">
                            <div>
                                <h2 class="secondary-title"><?php echo $this->journal2->settings->get('one_page_lang_comments', $text_comments); ?></h2>
                                <div class="box-inner">
                                    <textarea name="comment" rows="8" class="form-control"><?php echo $comment; ?></textarea>
                                </div>
                            </div>
                            <?php if ($entry_newsletter): ?>
                            <div class="checkbox check-newsletter">
                                <label for="newsletter">
                                    <input type="checkbox" name="newsletter" value="1" id="newsletter" />
                                    <?php echo $entry_newsletter; ?>
                                </label>
                            </div>
                            <?php endif; ?>

                            <?php if ($text_privacy): ?>
                            <div class="radio check-privacy">
                                <label>
                                    <input type="checkbox" name="privacy" value="1" />
                                    <?php echo $text_privacy; ?>
                                </label>
                            </div>
                            <?php endif; ?>

                            <?php if ($text_agree): ?>
                            <div class="radio check-terms">
                                <label>
                                    <input type="checkbox" name="agree" value="1" />
                                    <?php echo $text_agree; ?>
                                </label>
                            </div>
                            <?php endif; ?>
                            <div class="confirm-order">
                                <button id="journal-checkout-confirm-button" data-loading-text="<?php echo $this->journal2->settings->get('one_page_lang_loading_text', 'Loading..'); ?>" class="button confirm-button"><?php echo $this->journal2->settings->get('one_page_lang_confirm_order', 'Confirm Order'); ?></button>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <?php echo $content_bottom; ?>
        </div>
    </div>
</div>
<script>
    window['journal_account_status'] = <?php echo (int)$is_logged_in; ?>;

    $(document).delegate('input[name="shipping_method"]', 'change', function() {
        $(document).trigger('journal_checkout_shipping_changed', this.value);
    });

    $(document).delegate('input[name="payment_method"]', 'change', function() {
        $(document).trigger('journal_checkout_payment_changed', this.value);
    });

    $(document).delegate('#input-login_email, #input-login_password, #button-login', 'keydown', function(e) {
        if (e.keyCode == 13) {
            _do_login();
        }
    });

    $(document).delegate('#button-login', 'click', function() {
        _do_login();
    });

    function _do_login() {
        $.ajax({
            url: 'index.php?route=journal2/checkout/login',
            type: 'post',
            data: {
                email: $('input[name="login_email"]').val(),
                password: $('input[name="login_password"]').val()
            },
            dataType: 'json',
            beforeSend: function() {
                triggerLoadingOn();
                $('#button-login').button('loading');
            },
            complete: function() {
                triggerLoadingOff();
                $('#button-login').button('reset');
            },
            success: function(json) {
                if (json['error'] && json['error']['warning']) {
                    alert(json['error']['warning']);
                }
                if (json['redirect']) {
                    location = json['redirect'];
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

    $(document).delegate('.journal-checkout .confirm-button', 'click', function () {
        var data = { };

        $('.journal-checkout input[type="text"], .journal-checkout input[type="password"], .journal-checkout select, .journal-checkout input:checked, .journal-checkout textarea[name="comment"]').each(function () {
            data[$(this).attr('name')] = $(this).val();
        });

        $.ajax({
            url: 'index.php?route=journal2/checkout/confirm',
            type: 'post',
            data: data,
            dataType: 'json',
            beforeSend: function() {
                triggerLoadingOn();
            },
            success: function(json) {
                $('.text-danger').remove();
                $('.has-error').removeClass('has-error');

                if (json['redirect_cart']) {
                    location = json['redirect_cart'];
                    return;
                }

                window['journal_current_account_status'] = json['account_status'];

                if (json['errors']) {
                    $.each(json['errors'], function (k, v) {
						if (k === 'shipping_method' || k === 'payment_method') {
							return;
						}
                        if ($.inArray(k, ['payment_country', 'payment_zone', 'shipping_country', 'shipping_zone']) !== -1) {
                            k += '_id';
                        } else if (k.indexOf('custom_field') === 0) {
                            k = k.replace('custom_field', '');
                            k = 'custom_field[' + k + ']';
                        } else if (k.indexOf('payment_custom_field') === 0) {
                            k = k.replace('payment_custom_field', '');
                            k = 'payment_custom_field[' + k + ']';
                        } else if (k.indexOf('shipping_custom_field') === 0) {
                            k = k.replace('shipping_custom_field', '');
                            k = 'shipping_custom_field[' + k + ']';
                        }
                        var $element = $('.journal-checkout [name="' + k + '"]');
                        $element.closest('.form-group').addClass('has-error');
                        $element.after('<div class="text-danger">' + v + '</div>');
                    });
                    triggerLoadingOff();
                } else if (json['redirect']) {
                    location = json['redirect'];
                } else {
                    var $btn = $('#payment-confirm-button input[type="button"], #payment-confirm-button input[type="submit"], #payment-confirm-button .pull-right a, #payment-confirm-button .right a, #payment-confirm-button a.button, #button-confirm, #button-pay, #payment-confirm-button.payment-iyzico_checkout_installment .submitButton, #stripe-confirm').first();
                    if ($btn.attr('href')) {
                        location = $btn.attr('href');
                    } else {
                        $btn.trigger('click');
                    }
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $(document).on('journal_checkout_customer_group_changed', function (e, value) {
        $.ajax({
            url: 'index.php?route=journal2/checkout',
            type: 'get',
            data: {
                customer_group_id: value
            },
            beforeSend: function() {
                triggerLoadingOn();
                $('#account, #address').addClass('checkout-loading');
            },
            complete: function() {
                triggerLoadingOff();
                $('#account, #address').removeClass('checkout-loading');
            },
            success: function(html) {
                var $html = $(html);
                $('#account').html($html.find('#account'));
                $('#address').html($html.find('#address'));
                $('#password').html($html.find('#password'));
                <?php if (Front::$IS_OC2): ?>
                $('#account .form-group[data-sort]').detach().each(function() {
                    if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#account .form-group').length) {
                        $('#account .form-group').eq($(this).attr('data-sort')).before(this);
                    }

                    if ($(this).attr('data-sort') > $('#account .form-group').length) {
                        $('#account .form-group:last').after(this);
                    }

                    if ($(this).attr('data-sort') < -$('#account .form-group').length) {
                        $('#account .form-group:first').before(this);
                    }
                });
                <?php endif; ?>
                $(document).trigger('journal_checkout_reload_payment');
                if ($('input[name="shipping_address"]').is(':checked')) {
                    $(document).trigger('journal_checkout_reload_shipping');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $(document).on('journal_checkout_address_changed', function (e, type) {
        var data = { };
        if ($('input[name="' + type + '_address"]:checked').val() === 'existing') {
            data[type + '_address_id'] = $('select[name="' + type + '_address_id"]').val();
        } else {
            data[type + '_country_id'] = $('select[name="' + type + '_country_id"]').val();
            data[type + '_postcode'] = $('input[name="' + type + '_postcode"]').val();
            data[type + '_zone_id'] = $('select[name="' + type + '_zone_id"]').val();
            <?php if (!$is_logged_in): ?>
            if (type === 'payment' && $('input[name="shipping_address"]').is(":checked")) {
                data['shipping_country_id'] = $('select[name="' + type + '_country_id"]').val();
                data['shipping_postcode'] = $('input[name="' + type + '_postcode"]').val();
                data['shipping_zone_id'] = $('select[name="' + type + '_zone_id"]').val();
            }
            <?php endif; ?>
        }
        $.ajax({
            url: 'index.php?route=journal2/checkout/save',
            type: 'post',
            data: data,
            dataType: 'json',
            success: function(json) {
                $(document).trigger('journal_checkout_reload_' + type);
                <?php if (!$is_logged_in): ?>
                if (type === 'payment' && $('input[name="shipping_address"]').is(':checked')) {
                    $(document).trigger('journal_checkout_reload_shipping');
                }
                <?php endif; ?>
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $(document).on('journal_checkout_shipping_changed', function (e, value) {
        $.ajax({
            url: 'index.php?route=journal2/checkout/save',
            type: 'post',
            data: {
                shipping_method: value
            },
            dataType: 'json',
            success: function() {
                $(document).trigger('journal_checkout_reload_payment');
                $(document).trigger('journal_checkout_reload_cart');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $(document).on('journal_checkout_payment_changed', function (e, value) {
        $.ajax({
            url: 'index.php?route=journal2/checkout/save',
            type: 'post',
            data: {
                payment_method: value
            },
            dataType: 'json',
            success: function() {
                $(document).trigger('journal_checkout_reload_cart');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $(document).on('journal_checkout_reload_shipping', function () {
        $.ajax({
            url: 'index.php?route=journal2/checkout/shipping',
            type: 'get',
            dataType: 'html',
            beforeSend: function() {
                triggerLoadingOn();
                $('.checkout-shipping-methods').addClass('checkout-loading');
            },
            complete: function() {
                triggerLoadingOff();
                $('.checkout-shipping-methods').removeClass('checkout-loading');
            },
            success: function(html) {
                $('.checkout-shipping-methods').replaceWith(html);
                $(document).trigger('journal_checkout_reload_cart');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $(document).on('journal_checkout_reload_payment', function () {
        $.ajax({
            url: 'index.php?route=journal2/checkout/payment',
            type: 'get',
            dataType: 'html',
            beforeSend: function() {
                triggerLoadingOn();
                $('.checkout-payment-methods').addClass('checkout-loading');
            },
            complete: function() {
                triggerLoadingOff();
                $('.checkout-payment-methods').removeClass('checkout-loading');
            },
            success: function(html) {
                $('.checkout-payment-methods').replaceWith(html);
                $(document).trigger('journal_checkout_reload_cart');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $(document).on('journal_checkout_reload_cart', function (e, first) {
        $.ajax({
            url: 'index.php?route=journal2/checkout/cart',
            type: 'get',
            dataType: 'html',
            beforeSend: function() {
				if (!first) {
					triggerLoadingOn();
					$('.checkout-cart').addClass('checkout-loading');
				}
            },
            complete: function() {
				if (!first) {
					triggerLoadingOff();
					$('.checkout-cart').removeClass('checkout-loading');
				}
            },
            success: function(html) {
                $('.checkout-cart').replaceWith(html);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });

    $(document).delegate('.checkout-product .input-group .btn-update', 'click', function () {
        var key = $(this).attr('data-product-key');
        var qty  = $('input[name="quantity[' + key + ']"]').val();
        $.ajax({
            url: 'index.php?route=journal2/checkout/cart_update',
            type: 'post',
            data: {
                key: key,
                quantity: qty
            },
            dataType: 'json',
            beforeSend: function() {
                triggerLoadingOn();
                $('#cart > button > a > span').button('loading');
                $('.checkout-cart').addClass('checkout-loading');
            },
            complete: function() {
                triggerLoadingOff();
                $('#cart > button > a > span').button('reset');
            },
            success: function(json) {
                setTimeout(function () {
                    $('#cart-total').html(json['total']);
                }, 100);

                if (json['redirect']) {
                    location = json['redirect'];
                } else {
                    $('#cart ul').load('index.php?route=common/cart/info ul li');

                    $(document).trigger('journal_checkout_reload_payment');
                    $(document).trigger('journal_checkout_reload_shipping');
                }
            }
        });
    });

    $(document).delegate('.checkout-product .input-group .btn-delete', 'click', function () {
        var key = $(this).attr('data-product-key');
        $.ajax({
            url: 'index.php?route=journal2/checkout/cart_delete',
            type: 'post',
            data: {
                key: key
            },
            dataType: 'json',
            beforeSend: function() {
                triggerLoadingOn();
                $('#cart > button > a > span').button('loading');
                $('.checkout-cart').addClass('checkout-loading');
            },
            complete: function() {
                triggerLoadingOff();
                $('#cart > button > a > span').button('reset');
            },
            success: function(json) {
                setTimeout(function () {
                    $('#cart-total').html(json['total']);
                }, 100);

                if (json['redirect']) {
                    location = json['redirect'];
                } else {
                    $('#cart ul').load('index.php?route=common/cart/info ul li');

                    $(document).trigger('journal_checkout_reload_payment');
                    $(document).trigger('journal_checkout_reload_shipping');
                }
            }
        });
    });

    $(document).delegate('#button-voucher', 'click', function() {
        $.ajax({
            <?php if (version_compare(VERSION, '2.1', '>=')): ?>
            url: 'index.php?route=total/voucher/voucher',
            <?php elseif (version_compare(VERSION, '2', '>=')): ?>
            url: 'index.php?route=checkout/voucher/voucher',
            <?php else: ?>
            url: 'index.php?route=journal2/checkout/voucher',
            <?php endif; ?>
            type: 'post',
            data: 'voucher=' + encodeURIComponent($('input[name=\'voucher\']').val()),
            dataType: 'json',
            beforeSend: function() {
                triggerLoadingOn();
                $('#button-voucher').button('loading');
            },
            complete: function() {
                triggerLoadingOff();
                $('#button-voucher').button('reset');
            },
            success: function(json) {
                if (json['error']) {
                    alert(json['error']);
                } else {
                    $('#cart ul').load('index.php?route=common/cart/info ul li');

                    $(document).trigger('journal_checkout_reload_payment');
                    $(document).trigger('journal_checkout_reload_shipping');
                }
            }
        });
    });

    $(document).delegate('#button-coupon', 'click', function() {
        $.ajax({
            <?php if (version_compare(VERSION, '2.1', '>=')): ?>
            url: 'index.php?route=total/coupon/coupon',
            <?php elseif (version_compare(VERSION, '2', '>=')): ?>
            url: 'index.php?route=checkout/coupon/coupon',
            <?php else: ?>
            url: 'index.php?route=journal2/checkout/coupon',
            <?php endif; ?>
            type: 'post',
            data: 'coupon=' + encodeURIComponent($('input[name=\'coupon\']').val()),
            dataType: 'json',
            beforeSend: function() {
                triggerLoadingOn();
                $('#button-coupon').button('loading');
            },
            complete: function() {
                triggerLoadingOff();
                $('#button-coupon').button('reset');
            },
            success: function(json) {
                if (json['error']) {
                    alert(json['error']);
                } else {
                    $('#cart ul').load('index.php?route=common/cart/info ul li');

                    $(document).trigger('journal_checkout_reload_payment');
                    $(document).trigger('journal_checkout_reload_shipping');
                }
            }
        });
    });

    $(document).delegate('#button-reward', 'click', function() {
        $.ajax({
            <?php if (version_compare(VERSION, '2.1', '>=')): ?>
            url: 'index.php?route=total/reward/reward',
            <?php elseif (version_compare(VERSION, '2', '>=')): ?>
            url: 'index.php?route=checkout/reward/reward',
            <?php else: ?>
            url: 'index.php?route=journal2/checkout/reward',
            <?php endif; ?>
            type: 'post',
            data: 'reward=' + encodeURIComponent($('input[name=\'reward\']').val()),
            dataType: 'json',
            beforeSend: function() {
                triggerLoadingOn();
                $('#button-reward').button('loading');
            },
            complete: function() {
                triggerLoadingOff();
                $('#button-reward').button('reset');
            },
            success: function(json) {
                if (json['error']) {
                    alert(json['error']);
                } else {
                    $('#cart ul').load('index.php?route=common/cart/info ul li');

                    $(document).trigger('journal_checkout_reload_payment');
                    $(document).trigger('journal_checkout_reload_shipping');
                }
            }
        });
    });

    var ajax_calls = 0;

    function triggerLoadingOn() {
        ajax_calls++;
        if (ajax_calls === 1) {
            $('#journal-checkout-confirm-button').button('loading');
            $('#journal-checkout-confirm-button, .checkout-register, .checkout-payment-form, .checkout-shipping-form').addClass('checkout-loading');
        }
    }

    function triggerLoadingOff() {
        ajax_calls--;
        if (ajax_calls === 0) {
            $('#journal-checkout-confirm-button').button('reset');
            $('#journal-checkout-confirm-button, .checkout-register, .checkout-payment-form, .checkout-shipping-form').removeClass('checkout-loading');
        }
    }

    <?php if ($is_logged_in): ?>
    $('.journal-checkout [value="existing"]').trigger('change');
    <?php else: ?>
    $('input[name="account"]:checked').trigger('change');
    <?php endif; ?>

    $(document).trigger('journal_checkout_reload_cart', true);

    <?php if ($this->journal2->settings->get('one_page_auto_save', '0') == '1'): ?>
    $('.journal-checkout input').on('blur', function() {
        var data = { };

        $('.journal-checkout input[type="text"], .journal-checkout input[type="password"], .journal-checkout select, .journal-checkout input:checked, .journal-checkout textarea[name="comment"]').each(function () {
            data[$(this).attr('name')] = $(this).val();
        });

        $.ajax({
            url: 'index.php?route=journal2/checkout/confirm&saveOnly=true',
            type: 'post',
            data: data,
            dataType: 'json',
            success: function() {
                $(document).trigger('journal_checkout_reload_cart', true);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
    <?php endif; ?>
</script>
<?php echo $footer; ?>
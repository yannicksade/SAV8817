var count_loading=0;
function ajaxLoadingOn(){
	$('body > .tooltip').remove();
	count_loading++;
	if(count_loading===1){
		$('.so-onepagecheckout #so-checkout-confirm-button').button('loading');
		$('.so-onepagecheckout #so-checkout-confirm-button, .so-onepagecheckout .checkout-register, .so-onepagecheckout .checkout-payment-form, .so-onepagecheckout .checkout-shipping-form, .so-onepagecheckout .checkout-cart, .so-onepagecheckout .confirm-section, .so-onepagecheckout .checkout-shipping-methods, .so-onepagecheckout .checkout-payment-methods, .so-onepagecheckout .coupon-voucher').addClass('checkout-loading');
	}
}

function ajaxLoadingOff(){
	count_loading--;
	if(count_loading===0){
		$('.so-onepagecheckout #so-checkout-confirm-button').button('reset');
		$('.so-onepagecheckout #so-checkout-confirm-button, .so-onepagecheckout .checkout-register, .so-onepagecheckout .checkout-payment-form, .so-onepagecheckout .checkout-shipping-form, .so-onepagecheckout .checkout-cart, .so-onepagecheckout .confirm-section, .so-onepagecheckout .checkout-shipping-methods, .so-onepagecheckout .checkout-payment-methods, .so-onepagecheckout .coupon-voucher').removeClass('checkout-loading');
	}
}

jQuery(document).ready(function($) {
	if ($('.so-onepagecheckout input[name="account"]:checked').length) {
		$('.so-onepagecheckout input[name="account"]:checked').trigger('change', true);
	}
	$('.so-onepagecheckout input[name="payment_method"]:checked').trigger('change', true);
	$('.so-onepagecheckout input[name="shipping_method"]:checked').trigger('change', true);
	$(document).trigger('so_checkout_reload_cart',true);
})

$(document).delegate('.so-onepagecheckout input[name="account"]', 'change', function(){
	if(this.value==='login'){
		$('.so-onepagecheckout .checkout-login').slideDown(250);
		$('.so-onepagecheckout .checkout-register').parent().addClass('login-mobile');
	}else{
		$('.so-onepagecheckout .checkout-login').slideUp(250);
		$('.so-onepagecheckout .checkout-register').parent().removeClass('login-mobile');
		if(this.value==='register'){
			$('.so-onepagecheckout #password').slideDown(250);
		}else{
			$('.so-onepagecheckout #password').slideUp(250);
		}
	}
	$(document).trigger('so_checkout_coupon_voucher_reward_changed', this.value);	
	$('html').removeClass('checkout-type-login checkout-type-register checkout-type-guest').addClass('checkout-type-'+this.value);
});

$(document).delegate('.so-onepagecheckout input[name="payment_method"]','change',function(){
	$(document).trigger('so_checkout_payment_changed',this.value);
});

$(document).on('so_checkout_payment_changed',function(e,value){
	$.ajax({url:'index.php?route=checkout/checkout/save',
		type:'post',
		data:{payment_method:value},
		dataType:'json',
		success:function(){
			$(document).trigger('so_checkout_reload_cart');
		},
		error:function(xhr,ajaxOptions,thrownError){
			alert(thrownError+"\r\n"+xhr.statusText+"\r\n"+xhr.responseText);
		}
	});
});

$(document).delegate('.so-onepagecheckout input[name="shipping_method"]','change',function(){
	$(document).trigger('so_checkout_shipping_changed',this.value);
});

$(document).on('so_checkout_shipping_changed',function(e,value){
	$.ajax({
		url:'index.php?route=checkout/checkout/save',
		type:'post',
		data:{shipping_method:value},
		dataType:'json',
		success:function(){
			$.ajax({
				url:'index.php?route=checkout/checkout/cart_update',
				type:'post',
				dataType:'json',
				success:function(json){
					setTimeout(function(){
						$('#cart-total').html(json['total']);
					},100);
					$('#cart ul').load('index.php?route=common/cart/info ul li');
				}
			});

			$(document).trigger('so_checkout_reload_payment');
			$(document).trigger('so_checkout_reload_cart');
		},
		error:function(xhr,ajaxOptions,thrownError){
			alert(thrownError+"\r\n"+xhr.statusText+"\r\n"+xhr.responseText);
		}
	});
});

$(document).on('so_checkout_reload_cart',function(e,first){
	$.ajax({
		url:'index.php?route=checkout/checkout/cart',
		type:'get',
		dataType:'html',
		beforeSend:function(){
			if(!first){
				ajaxLoadingOn();
				$('.so-onepagecheckout .checkout-cart').addClass('checkout-loading');
			}
		},
		complete:function(){
			if(!first){
				ajaxLoadingOff();
				$('.so-onepagecheckout .checkout-cart').removeClass('checkout-loading');
			}
		},
		success:function(html){
			$('.so-onepagecheckout .checkout-cart').replaceWith(html);
		},
		error:function(xhr,ajaxOptions,thrownError){
			alert(thrownError+"\r\n"+xhr.statusText+"\r\n"+xhr.responseText);
		}
	});
});

$(document).on('so_checkout_reload_payment',function(){
	$.ajax({
		url:'index.php?route=checkout/checkout/payment',
		type:'get',
		dataType:'html',
		beforeSend:function(){
			ajaxLoadingOn();
			$('.so-onepagecheckout .checkout-payment-methods').addClass('checkout-loading');
		},
		complete:function(){
			ajaxLoadingOff();
			$('.so-onepagecheckout .checkout-payment-methods').removeClass('checkout-loading');
		},
		success:function(html){
			$('.so-onepagecheckout .checkout-payment-methods').replaceWith(html);
			$(document).trigger('so_checkout_reload_cart');
		},
		error:function(xhr,ajaxOptions,thrownError){
			alert(thrownError+"\r\n"+xhr.statusText+"\r\n"+xhr.responseText);
		}
	});
});

jQuery(document).delegate('.so-onepagecheckout input[name="shipping_address"]', 'change', function(){
	var $this=jQuery(this);
	if($this.is(':checked')){
		jQuery('.so-onepagecheckout #shipping-address').hide();
		$this.val(1);
		jQuery(document).trigger('so_checkout_address_changed','payment');
	}else{
		jQuery('.so-onepagecheckout #shipping-address').show().find('input[type="text"]').val('');
		jQuery(document).trigger('so_checkout_address_changed','payment');
		jQuery(document).trigger('so_checkout_address_changed','shipping');
		$this.val(0);
	}
});

$(document).on('so_checkout_coupon_voucher_reward_changed',function(e, value){
	$.ajax({
		url:'index.php?route=checkout/checkout/change_coupon_voucher_reward',
		type:'post',
		data:{so_checkout_account:value},
		dataType:'html',
		success:function(html){
			$('.so-onepagecheckout .so-onepagecheckout .section-right #coupon_voucher_reward').html(html);
		},
		error:function(xhr,ajaxOptions,thrownError){
			alert(thrownError+"\r\n"+xhr.statusText+"\r\n"+xhr.responseText);
		}
	});
});

jQuery(document).ready(function($) {
	$('form.form-shipping input[name="shipping_address"]').change(function(){
		if(this.value=='new' || this.value=='1'){
			$('.so-onepagecheckout #shipping-existing').hide();
			$('.so-onepagecheckout #shipping-new').show().find('input[type="text"]').val('');
		}else{
			$('.so-onepagecheckout #shipping-existing').show();
			$('.so-onepagecheckout #shipping-new').hide();
		}
		$(document).trigger('so_checkout_address_changed','shipping');
	});
});

jQuery(document).ready(function($) {
	var default_zone_id = $('#default_zone_id').val();
	$('.so-onepagecheckout form.form-shipping select[name="shipping_country_id"]').on('change',function(e,first){
		if(!this.value) return;
		$.ajax({
			url:'index.php?route=checkout/checkout/country&country_id='+this.value,
			dataType:'json',
			beforeSend:function(){
				$('.so-onepagecheckout form.form-shipping select[name="shipping_country_id"]').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
			},
			complete:function(){
				$('.so-onepagecheckout .fa-spin').remove();
			},
			success:function(json){
				$('.so-onepagecheckout .fa-spin').remove();
				if(json['postcode_required']=='1'){
					$('.so-onepagecheckout form.form-shipping input[name="shipping_postcode"]').parent().addClass('required');
				}else{
					$('.so-onepagecheckout form.form-shipping input[name="shipping_postcode"]').parent().removeClass('required');
				}
				html='<option value=""> --- Please Select --- </option>';
				if(json['zone']!=''){
					for(i=0;i<json['zone'].length;i++){
						html+='<option value="'+json['zone'][i]['zone_id']+'"';
						if(json['zone'][i]['zone_id']==default_zone_id){
							html+=' selected="selected"';
						}
						html+='>'+json['zone'][i]['name']+'</option>';
					}
				}else{
					html+='<option value="0" selected="selected"> --- None --- </option>';
				}
				
				$('.so-onepagecheckout form.form-shipping select[name="shipping_zone_id"]').html(html);
				
				if(!first){
					$(document).trigger('so_checkout_address_changed','shipping');
				}
			},
			error:function(xhr,ajaxOptions,thrownError){
				alert(thrownError+"\r\n"+xhr.statusText+"\r\n"+xhr.responseText);
			}
		});
	}).trigger('change',true);
})

jQuery('.so-onepagecheckout form.form-shipping select[name="shipping_zone_id"]').on('change',function(){
	jQuery(document).trigger('so_checkout_address_changed','shipping');
});

jQuery('.so-onepagecheckout form.form-shipping select[name="shipping_address_id"]').on('change',function(){
	$(document).trigger('so_checkout_address_changed','shipping');
});

var timeout_shipping_postcode=null;
jQuery('.so-onepagecheckout form.form-shipping input[name="shipping_postcode"]').on('keydown',function(){
	if(timeout_shipping_postcode){clearTimeout(timeout_shipping_postcode);}
	timeout_shipping_postcode=setTimeout(function(){
		jQuery(document).trigger('so_checkout_address_changed','shipping');
	},500);
});

jQuery('.so-onepagecheckout .checkout-shipping-form .form-group[data-sort]').detach().each(function(){
	if(jQuery(this).attr('data-sort')>=0 && jQuery(this).attr('data-sort')<=jQuery('.so-onepagecheckout .checkout-shipping-form .form-group').length){
		jQuery('.so-onepagecheckout .checkout-shipping-form .form-group').eq(jQuery(this).attr('data-sort')).before(this);
	}
	
	if(jQuery(this).attr('data-sort')>jQuery('.checkout-shipping-form .form-group').length){
		jQuery('.so-onepagecheckout .checkout-shipping-form .form-group:last').after(this);
	}
	if(jQuery(this).attr('data-sort')<-jQuery('.checkout-shipping-form .form-group').length){
		jQuery('.so-onepagecheckout .checkout-shipping-form .form-group:first').before(this);
	}
});

jQuery(document).ready(function($) {
	$('.so-onepagecheckout .date').datetimepicker({pickTime:false});
	$('.so-onepagecheckout .time').datetimepicker({pickDate:false});
	$('.so-onepagecheckout .datetime').datetimepicker({pickDate:true,pickTime:true});
})

jQuery(document).delegate('.so-onepagecheckout input[name="customer_group_id"]','change',function(){
	jQuery(document).trigger('so_checkout_customer_group_changed',this.value);
});

jQuery('.so-onepagecheckout #account .form-group[data-sort]').detach().each(function(){
	if(jQuery(this).attr('data-sort')>=0 && jQuery(this).attr('data-sort')<=jQuery('.so-onepagecheckout #account .form-group').length){
		jQuery('.so-onepagecheckout #account .form-group').eq(jQuery(this).attr('data-sort')).before(this);
	}
	if(jQuery(this).attr('data-sort')>jQuery('.so-onepagecheckout #account .form-group').length){
		jQuery('.so-onepagecheckout #account .form-group:last').after(this);
	}
	if(jQuery(this).attr('data-sort')<-jQuery('.so-onepagecheckout #account .form-group').length){
		jQuery('.so-onepagecheckout #account .form-group:first').before(this);
	}
});

jQuery('.so-onepagecheckout #button-confirm').on('click',function($){
	$.ajax({
		type:'get',
		url:'index.php?route=extension/payment/cod/confirm',
		cache:false,
		beforeSend:function(){
			$('.so-onepagecheckout #button-confirm').button('loading');
		},
		complete:function(){
			$('.so-onepagecheckout #button-confirm').button('reset');
		},
		success:function(){
			location='index.php?route=checkout/success';
		}
	});
});

jQuery(document).ready(function($) {
	$('.so-onepagecheckout form.form-payment input[name="payment_address"]').change(function(){
		if(this.value=='new' || this.value=='1'){
			$('.so-onepagecheckout #payment-existing').hide();
			$('.so-onepagecheckout #payment-new').show().find('input[type="text"]').val('');
		}else{
			$('.so-onepagecheckout #payment-existing').show();
			$('.so-onepagecheckout #payment-new').hide();
		}
		$(document).trigger('so_checkout_address_changed','payment');
	});
});

jQuery(document).ready(function($) {
	var default_zone_id = $('.so-onepagecheckout #default_zone_id').val();
	$('.so-onepagecheckout form.form-payment select[name="payment_country_id"]').on('change',function(e,first){
		if(!this.value)return;
		$.ajax({
			url:'index.php?route=checkout/checkout/country&country_id='+this.value,
			dataType:'json',
			beforeSend:function(){
				$('.so-onepagecheckout form.form-payment select[name="payment_country_id"]').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
			},
			complete:function(){
				$('.so-onepagecheckout .fa-spin').remove();
			},
			success:function(json){
				$('.so-onepagecheckout .fa-spin').remove();
				if(json['postcode_required']=='1'){
					$('.so-onepagecheckout form.form-payment input[name="payment_postcode"]').parent().addClass('required');
				}else{
					$('.so-onepagecheckout form.form-payment input[name="payment_postcode"]').parent().removeClass('required');
				}
				html='<option value=""> --- Please Select --- </option>';
				if(json['zone']!=''){
					for(i=0;i<json['zone'].length;i++){
						html+='<option value="'+json['zone'][i]['zone_id']+'"';
						if(json['zone'][i]['zone_id']==default_zone_id){
							html+=' selected="selected"';
						}
						html+='>'+json['zone'][i]['name']+'</option>';
					}
				}else{
					html+='<option value="0" selected="selected"> --- None --- </option>';
				}

				$('.so-onepagecheckout form.form-payment select[name="payment_zone_id"]').html(html);
				
				if(!first){
					$(document).trigger('so_checkout_address_changed','payment');
				}
			},
			error:function(xhr,ajaxOptions,thrownError){
				alert(thrownError+"\r\n"+xhr.statusText+"\r\n"+xhr.responseText);
			}
		});
	}).trigger('change',true);
})

jQuery('.so-onepagecheckout form.form-payment select[name="payment_zone_id"]').on('change',function(){
	$(document).trigger('so_checkout_address_changed','payment');
});

jQuery('.so-onepagecheckout form.form-payment select[name="payment_address_id"]').on('change',function(){
	$(document).trigger('so_checkout_address_changed','payment');
});

var timeout_payment_postcode=null;
$('.so-onepagecheckout form.form-payment input[name="payment_postcode"]').on('keydown',function(){
	if(timeout_payment_postcode){
		clearTimeout(timeout_payment_postcode);
	}
	timeout_payment_postcode=setTimeout(function(){$(document).trigger('so_checkout_address_changed','payment');}, 500);
});

$(document).delegate('.so-onepagecheckout #input-login_email, .so-onepagecheckout #input-login_password, .so-onepagecheckout #button-login', 'keydown',function(e){
	if(e.keyCode==13){
		so_ajax_login();
	}
});

$(document).delegate('.so-onepagecheckout #button-login','click',function(){
	so_ajax_login();
});

function so_ajax_login(){
	$.ajax({
		url:'index.php?route=checkout/checkout/login',
		type:'post',
		data:{email:$('.so-onepagecheckout input[name="login_email"]').val(),password:$('.so-onepagecheckout input[name="login_password"]').val()},
		dataType:'json',
		beforeSend:function(){
			ajaxLoadingOn();
			$('.so-onepagecheckout #button-login').button('loading');
		},
		complete:function(){
			ajaxLoadingOff();
			$('.so-onepagecheckout #button-login').button('reset');
		},
		success:function(json){
			if(json['error']&&json['error']['warning']){
				alert(json['error']['warning']);
			}
			if(json['redirect']){
				location=json['redirect'];
			}
		},
		error:function(xhr,ajaxOptions,thrownError){
			alert(thrownError+"\r\n"+xhr.statusText+"\r\n"+xhr.responseText);
		}
	});
}

jQuery(document).delegate('.so-onepagecheckout .confirm-button','click',function(){
	var data={};
	$('.so-onepagecheckout input[type="text"], .so-onepagecheckout input[type="number"], .so-onepagecheckout input[type="password"], .so-onepagecheckout select, .so-onepagecheckout input:checked, .so-onepagecheckout textarea[name="comment"]').each(function(){
		data[$(this).attr('name')]=$(this).val();
	});

	$.ajax({
		url:'index.php?route=checkout/checkout/confirm',
		type:'post',
		data:data,
		dataType:'json',
		beforeSend:function(){
			ajaxLoadingOn();
		},
		success:function(json){
			console.log(json);
			$('.so-onepagecheckout .text-danger').remove();
			$('.so-onepagecheckout .has-error').removeClass('has-error');
			if(json['redirect_cart']){
				location=json['redirect_cart'];return;
			}
			
			if(json['errors']){
				$.each(json['errors'],function(k,v){
					if(k==='shipping_method'||k==='payment_method'){
						return;
					}
					if($.inArray(k,['payment_country','payment_zone','shipping_country','shipping_zone'])!==-1){
						k+='_id';
					}else if(k.indexOf('custom_field')===0){
						k=k.replace('custom_field','');
						k='custom_field['+k+']';
					}else if(k.indexOf('payment_custom_field')===0){
						k=k.replace('payment_custom_field','');
						k='payment_custom_field['+k+']';
					}else if(k.indexOf('shipping_custom_field')===0){
						k=k.replace('shipping_custom_field','');
						k='shipping_custom_field['+k+']';
					}
					var $element=$('.so-onepagecheckout [name="'+k+'"]');
					$element.closest('.form-group').addClass('has-error');
					if ($element.closest('label').length)
						$element.closest('label').after('<div class="text-danger">'+v+'</div>');
					else
						$element.after('<div class="text-danger">'+v+'</div>');
				});
				ajaxLoadingOff();
				try{
					$('html, body').animate({scrollTop:$('.has-error').offset().top},'slow');
				}catch(e){
					if (json['errors']['account'][0]) alert(json['errors']['account'][0]);
				}
				return false;
			}
			else if(json['redirect']){
				location=json['redirect'];
			}else{
				var $btn=$('.so-onepagecheckout #payment-confirm-button input[type="button"], .so-onepagecheckout #payment-confirm-button input[type="submit"], .so-onepagecheckout #payment-confirm-button .pull-right a, .so-onepagecheckout #payment-confirm-button .right a, .so-onepagecheckout #payment-confirm-button a.button, .so-onepagecheckout #button-confirm, .so-onepagecheckout #button-pay, .so-onepagecheckout #payment-confirm-button.payment-iyzico_checkout_installment .submitButton, .so-onepagecheckout #stripe-confirm').first();
				if($btn.attr('href')){
					location=$btn.attr('href');
				}else{
					$btn.trigger('click');
				}
			}
		},
		error:function(xhr,ajaxOptions,thrownError){
			alert(thrownError+"\r\n"+xhr.statusText+"\r\n"+xhr.responseText);
		}
	});
});

$(document).on('so_checkout_customer_group_changed',function(e,value){
	$.ajax({
		url:'index.php?route=checkout/checkout',
		type:'get',
		data:{customer_group_id:value},
		beforeSend:function(){
			ajaxLoadingOn();
			$('.so-onepagecheckout #account, .so-onepagecheckout #address').addClass('checkout-loading');
		},
		complete:function(){
			ajaxLoadingOff();
			$('.so-onepagecheckout #account, .so-onepagecheckout #address').removeClass('checkout-loading');
		},
		success:function(html){
			var $html=$(html);
			$('.so-onepagecheckout #account').html($html.find('#account'));
			$('.so-onepagecheckout #address').html($html.find('#address'));
			$('.so-onepagecheckout #password').html($html.find('#password'));
			$('.so-onepagecheckout #account .form-group[data-sort]').detach().each(function(){
				if($(this).attr('data-sort')>=0 && $(this).attr('data-sort')<=$('.so-onepagecheckout #account .form-group').length){
					$('.so-onepagecheckout #account .form-group').eq($(this).attr('data-sort')).before(this);
				}
				if($(this).attr('data-sort')>$('#account .form-group').length){
					$('.so-onepagecheckout #account .form-group:last').after(this);
				}
				if($(this).attr('data-sort')<-$('#account .form-group').length){
					$('.so-onepagecheckout #account .form-group:first').before(this);
				}
			});
			
			$(document).trigger('so_checkout_reload_payment');

			if($('.so-onepagecheckout input[name="shipping_address"]').is(':checked')){
				$(document).trigger('so_checkout_reload_shipping');
			}
		},
		error:function(xhr,ajaxOptions,thrownError){
			alert(thrownError+"\r\n"+xhr.statusText+"\r\n"+xhr.responseText);
		}
	});
});

$(document).on('so_checkout_address_changed',function(e,type){
	var data={};
	if($('.so-onepagecheckout input[name="'+type+'_address"]:checked').val()==='existing'){
		data[type+'_address_id']=$('select[name="'+type+'_address_id"]').val();
	}else{
		data[type+'_country_id']=$('select[name="'+type+'_country_id"]').val();
		data[type+'_postcode']=$('input[name="'+type+'_postcode"]').val();
		data[type+'_zone_id']=$('select[name="'+type+'_zone_id"]').val();
		if(type==='payment'&&$('input[name="shipping_address"]').is(":checked")){
			data['shipping_country_id']=$('select[name="'+type+'_country_id"]').val();
			data['shipping_postcode']=$('input[name="'+type+'_postcode"]').val();
			data['shipping_zone_id']=$('select[name="'+type+'_zone_id"]').val();
		}
	}
	
	$.ajax({
		url:'index.php?route=checkout/checkout/save',
		type:'post',
		data:data,
		dataType:'json',
		success:function(json){
			$(document).trigger('so_checkout_reload_'+type);
			if(type==='payment'&&$('input[name="shipping_address"]').is(':checked')){
				$(document).trigger('so_checkout_reload_shipping');
			}
		},
		error:function(xhr,ajaxOptions,thrownError){
			alert(thrownError+"\r\n"+xhr.statusText+"\r\n"+xhr.responseText);
		}
	});
});

$(document).on('so_checkout_reload_shipping',function(){
	$.ajax({
		url:'index.php?route=checkout/checkout/shipping',
		type:'get',
		dataType:'html',
		beforeSend:function(){
			ajaxLoadingOn();
			$('.so-onepagecheckout .checkout-shipping-methods').addClass('checkout-loading');
		},
		complete:function(){
			ajaxLoadingOff();
			$('.so-onepagecheckout .checkout-shipping-methods').removeClass('checkout-loading');
		},
		success:function(html){
			$('.so-onepagecheckout .checkout-shipping-methods').replaceWith(html);
			$(document).trigger('so_checkout_reload_cart');
		},
		error:function(xhr,ajaxOptions,thrownError){
			alert(thrownError+"\r\n"+xhr.statusText+"\r\n"+xhr.responseText);
		}
	});
});

$(document).delegate('.so-onepagecheckout .checkout-product .input-group .btn-update','click',function(){
	var key=$(this).attr('data-product-key');
	var qty=$('.so-onepagecheckout input[name="quantity['+key+']"]').val();
	$.ajax({
		url:'index.php?route=checkout/checkout/cart_update',
		type:'post',
		data:{key:key,quantity:qty},
		dataType:'json',
		beforeSend:function(){
			ajaxLoadingOn();
			$('#cart > button > a > span').button('loading');
			$('.so-onepagecheckout .checkout-cart').addClass('checkout-loading');
		},
		complete:function(){
			ajaxLoadingOff();
			$('#cart > button > a > span').button('reset');
		},
		success:function(json){
			setTimeout(function(){
				$('#cart-total').html(json['total']);
			},100);

			if(json['redirect']){
				location=json['redirect'];
			}else{
				$('#cart ul').load('index.php?route=common/cart/info ul li');
				$(document).trigger('so_checkout_reload_payment');
				$(document).trigger('so_checkout_reload_shipping');
			}
		}
	});
});

$(document).delegate('.checkout-product .input-group .btn-delete','click',function(){
	var key=$(this).attr('data-product-key');
	$.ajax({
		url:'index.php?route=checkout/checkout/cart_delete',
		type:'post',
		data:{key:key},
		dataType:'json',
		beforeSend:function(){
			ajaxLoadingOn();
			$('#cart > button > a > span').button('loading');
			$('.so-onepagecheckout .checkout-cart').addClass('checkout-loading');
		},
		complete:function(){
			ajaxLoadingOff();
			$('#cart > button > a > span').button('reset');
		},
		success:function(json){
			setTimeout(function(){
				$('#cart-total').html(json['total']);
			},100);

			if(json['redirect']){
				location=json['redirect'];
			}else{
				$('#cart ul').load('index.php?route=common/cart/info ul li');
				$(document).trigger('so_checkout_reload_payment');
				$(document).trigger('so_checkout_reload_shipping');
			}
		}
	});
});

$(document).delegate('#button-voucher','click',function(){
	$.ajax({
		url:'index.php?route=extension/total/voucher/voucher',
		type:'post',
		data:'voucher='+encodeURIComponent($('input[name=\'voucher\']').val()),
		dataType:'json',
		beforeSend:function(){
			ajaxLoadingOn();
			$('.so-onepagecheckout #button-voucher').button('loading');
		},
		complete:function(){
			ajaxLoadingOff();
			$('.so-onepagecheckout #button-voucher').button('reset');
		},
		success:function(json){
			if(json['error']){
				alert(json['error']);
			}else{
				$('#cart ul').load('index.php?route=common/cart/info ul li');
				$(document).trigger('so_checkout_reload_payment');
				$(document).trigger('so_checkout_reload_shipping');
			}
		}
	});
});

$(document).delegate('#button-coupon','click',function(){
	$.ajax({
		url:'index.php?route=extension/total/coupon/coupon',
		type:'post',
		data:'coupon='+encodeURIComponent($('input[name=\'coupon\']').val()),
		dataType:'json',
		beforeSend:function(){
			ajaxLoadingOn();
			$('.so-onepagecheckout #button-coupon').button('loading');
		},
		complete:function(){
			ajaxLoadingOff();
			$('.so-onepagecheckout #button-coupon').button('reset');
		},
		success:function(json){
			if(json['error']){
				alert(json['error']);
			}else{
				$('#cart ul').load('index.php?route=common/cart/info ul li');
				$(document).trigger('so_checkout_reload_payment');
				$(document).trigger('so_checkout_reload_shipping');
			}
		}
	});
});

$(document).delegate('.so-onepagecheckout #button-reward','click',function(){
	$.ajax({
		url:'index.php?route=extension/total/reward/reward',
		type:'post',data:'reward='+encodeURIComponent($('input[name=\'reward\']').val()),
		dataType:'json',
		beforeSend:function(){
			ajaxLoadingOn();
			$('.so-onepagecheckout #button-reward').button('loading');
		},
		complete:function(){
			ajaxLoadingOff();
			$('.so-onepagecheckout #button-reward').button('reset');
		},
		success:function(json){
			if(json['error']){
				alert(json['error']);
			}else{
				$('#cart ul').load('index.php?route=common/cart/info ul li');
				$(document).trigger('so_checkout_reload_payment');
				$(document).trigger('so_checkout_reload_shipping');
			}
		}
	});
});
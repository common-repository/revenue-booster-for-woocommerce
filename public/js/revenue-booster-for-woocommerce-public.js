(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
    
    $(function() {

        if( dsrbfw_front_vars.debug_mode ) {
            console.log( 'Revenue Booster for WooCommerce Frontend Debug mode: On' );
        }

        /**
         * Frequenly Brought Together Module
         */
        // If product checked then change price and enable add to cart button
        $('.dsrbfw_fbt__checkbox').on('change', function() {

            updateFinalPrice();

            //Check variation selected or not
            $(this).parent().siblings('.dsrbfw_fbt__product-content').find('.dsrbfw_fbt__variation-select').each( function(){
                $(this).trigger('change');
            });
        });

        // on variation select change
        $('.dsrbfw_fbt__variation-select').on('change', function() {
            var attributes = getChosenAttributes( '.' + $(this).attr('name') ),
            currentAttributes = attributes.data,
            $this = $(this);
            
            if ( attributes.count && attributes.count === attributes.chosenCount ) {
                $('.dsrbfw_fbt__body').block( { message: null, overlayCSS: { background: '#fff', opacity: 0.6 } } );
                var variable_id = $this.closest('table').data('variable-id');
                var variation_id = $this.closest('table').data('variation-id');
                var data = {
                    'action': 'dsrbfw_get_variation_price',
                    'dsrbfw_variable_id': variable_id,
                    'dsrbfw_attributes': currentAttributes,
                    'security': dsrbfw_front_vars.dsrbfw_variation_price_nonce
                };
                $.ajax({
                    type: 'POST',
                    url: dsrbfw_front_vars.ajaxurl,
                    data: data,
                    success: function( response ){
                        if( dsrbfw_front_vars.debug_mode ) {
                            console.log( 'Response: ' + data.action );
                            console.log( response );
                        }
                        if( response && response.is_purchasable && response.is_in_stock ) {
                            var pid = variation_id ? variation_id : variable_id;
                            $this.closest('.dsrbfw_fbt__product').find('.dsrbfw_fbt__product-image img').attr('src', response.image.full_src);
                            $this.closest('table').siblings('.dsrbfw_fbt__product-price').html(response.price_html);
                            $this.closest('table').siblings('input[name="dsrbfw-fbt-product-attributes-'+pid+'"]').val(JSON.stringify(currentAttributes));
                            $this.closest('table').siblings('input[name="dsrbfw-fbt-product-variation-'+pid+'"]').val(response.variation_id);
                            $this.closest('table').parent().siblings('.dsrbfw_fbt__checkbox_wrap').find('.dsrbfw_fbt__checkbox').attr('data-variation-id', response.variation_id);
                        } else {
                            $this.closest('table').siblings('.dsrbfw_fbt__product-price').html(dsrbfw_front_vars.dsrbfw_unavailable_text);
                        }
                    },
                    complete: function() {
                        updateFinalPrice();
                        addToCartButtonStatus();
						$('.dsrbfw_fbt__body').unblock();
					}
                });
            } else {
                addToCartButtonStatus();
            }
        });

        // On load page check if all variation selected
        addToCartButtonStatus();
        
        //If no product selected, disable add to cart button
        $('.dsrbfw_fbt__button').click(function (e) {

            if ( $('.dsrbfw_fbt__product').find('.dsrbfw_fbt__checkbox').length < 1 ) {
                return false;
            }

            if ( $(this).hasClass( 'disabled' ) ) {
                e.preventDefault();
                alert(dsrbfw_front_vars.disabled_add_to_cart);
            }
        });

        $(document).on('click', '[dsrbfw-fbt-submit]', function(e) {

            var $this = $(this);
            if ($this.prop('disabled') || $this.hasClass('disabled') || 'yes' !== dsrbfw_front_vars.fbt_ajax_enable) {
                return;
            }
            var update_ids = [];
            e.preventDefault();

            var product_ids = {};
            var data = {
                'action': 'dsrbfw_fbt_add_to_cart',
                'dsrbfw-fbt-add-selected': true,
                'dsrbfw-fbt-product': product_ids,
                'dsrbfw-main-product-id': $('.dsrbfw_main_product_id').val(),
                'security': dsrbfw_front_vars.dsrbfw_fbt_add_to_cart_nonce
            };

            $('.dsrbfw_fbt__product').each(function(){
                if( $(this).find('.dsrbfw_fbt__checkbox').is(':checked') ) {

                    var product_id = $(this).find('.dsrbfw_fbt__checkbox').val();
                    var search_id = product_id;
                    
                    if( 'variation' === $(this).find('.dsrbfw_fbt__checkbox').data('product-type') ) {
                        var variable_id = $(this).find('.dsrbfw_fbt__checkbox').attr('data-variation-id') ? $(this).find('.dsrbfw_fbt__checkbox').attr('data-variation-id') : product_id;
                        // product_ids.push( parseInt(variable_id));
                        product_ids[variable_id] = parseInt( product_id );
                        search_id = variable_id;
                    } else {
                        // product_ids.push( product_id);
                        product_ids[product_id] = parseInt( product_id );
                    }

                    var variation_key = 'dsrbfw-fbt-product-variation-'+search_id;
                    var attribute_key = 'dsrbfw-fbt-product-attributes-'+search_id;
                    update_ids.push( search_id );
                    if( $('input[name="'+variation_key+'"]').val() ) {
                        data[variation_key] = $('input[name="'+variation_key+'"]').val();
                    }
                    if( $('input[name="'+attribute_key+'"]').val() ) {
                        data[attribute_key] = $('input[name="'+attribute_key+'"]').val();
                    }
                }
            });

            $('.dsrbfw_fbt__body').block( { message: null, overlayCSS: { background: '#fff', opacity: 0.6 } } );

            $.ajax({
                type: 'POST',
                url: dsrbfw_front_vars.ajaxurl,
                data: data,
                success: function( response ){
                    if( dsrbfw_front_vars.debug_mode ) {
                        console.log( 'Response: ' + data.action );
                        console.log( response );
                    }
                    
                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
                    $.scroll_to_notices( $( '.woocommerce-notices-wrapper' ) );
                },
                complete: function() {
                    $('.dsrbfw_fbt__body').unblock();
                    $.each(update_ids, function( key, id ) {  
                        if( 'yes' === dsrbfw_front_vars.fbt_cart_exist_enabled ) {
                            $('.dsrbfw_fbt__list').find('[name="dsrbfw-fbt-product['+id+']"]').remove();
                        } 
                        $('.dsrbfw_fbt__list').find('.remove-text-'+id).html('(Exist in cart)');
                    });
                    if( 'yes' === dsrbfw_front_vars.fbt_cart_exist_enabled ) {
                        if( $('.dsrbfw_fbt__list').find('.dsrbfw_fbt__checkbox').length <= 0 ) {
                            $('.dsrbfw_fbt__list').addClass('dsrbfw_fbt__all-exist');
                            $('.dsrbfw_fbt__actions').remove();
                        }
                    }
                }
            });
        });

        /**
         * Customer Also Buy Module
         */
        // Onload page show popup if avaiable in session
        CustomerAlsoBuyModule();
        OrderBumpCheckbox();

        // On aad to cart event show popup
        $(document).on('added_to_cart', function (e, fragments) {
            if (!fragments || !window.wc_cart_fragments_params || !wc_cart_fragments_params.fragment_name) {
                return;
            }
            CustomerAlsoBuyModule();
            
            //Remove flag from cart fragments session so it will not open popup everytime without adding product into cart
            fragments['.dsrbfw_after_add_to_cart_popup_placeholder'] = undefined;
            sessionStorage.setItem(wc_cart_fragments_params.fragment_name, JSON.stringify(fragments));
        });
        
        /**
         * Order Bump at Checkout Module
         */
        $(document).on( 'click', '.dsrbfw_ob_ac_footer', function(e) {

            // We have done this to rpevent doule call of checkbox while select on child element
            if (e.target !== this) {
                return;
            }

            var checkbox = $(this).find('.dsrbfw_ob_ac_footer-checkbox');

            if( checkbox.is(':disabled') ) {
                return;
            }

            checkbox.prop('checked', !checkbox.prop('checked'));
            checkbox.trigger('change');
        });

        $(document).on('change', '.dsrbfw_ob_ac_footer-checkbox', function () {
            var rule_id = $(this).val();
        
            var bump_action_type = $('[name="dsrbfw_ob_ac-bump-data[' + rule_id + '][dsrbfw_ob_ac-bump-action]"');
            
            if ( ! $(this).prop( 'checked' ) ) {
                bump_action_type.val('remove');
            } else {
                bump_action_type.val('add');
            }
            var allVariationSelected = checkAllACVariationSelected($(this).closest('.dsrbfw_ob_ac_main').find('.dsrbfw_ob_ac__variation'), true);
            if(allVariationSelected) {
                $('.woocommerce-notices-wrapper .dsrbfw-notice').remove();
                
                // Update Cart item section
                $(document.body).trigger('update_checkout');

                // Update mini cart fragment for updated cart data
                setTimeout(function() {
                    $(document.body).trigger('wc_fragment_refresh');
                },300);
            } else {
                var div_wrapper = $('<div/>').addClass('dsrbfw-notice woocommerce-error').html(dsrbfw_front_vars.disabled_add_to_cart);
                $( '.woocommerce-notices-wrapper:first' ).html(div_wrapper);
                $.scroll_to_notices( $( '.woocommerce-notices-wrapper' ) );
            }
            
        });

        $(document.body).on('updated_checkout', function () {
            OrderBumpCheckbox();
        });

        $(document).on( 'change', '.dsrbfw_ob_ac__variation-select', function() {
            getACVariation( $(this).closest('.dsrbfw_ob_ac_main').find('.dsrbfw_ob_ac__variation') );
        });

        /**
         * Order bump popup before Order
        */
        
        $(document).on( 'change', '.dsrbfw_ob_bo__variation-select', function() {
            getBOVariation( $(this).closest('.dsrbfw_ob_bo_product_main').find('.dsrbfw_ob_bo__variation') );
        });

        var bumpShown = false;
        if( $('.dsrbfw_modal').length > 0 ) {
            $('form.checkout').on('checkout_place_order.dsbrfwBumpOrder', function() {

                if( bumpShown ) {
                    return true;
                }

                if( $('.dsrbfw_modal').hasClass('hidden') ) {

                    $('.dsrbfw_modal').removeClass('hidden').addClass('shown');
                    $('.dsrbfw_modal').css('display', 'block');
                    $('.dsrbfw_modal').css('opacity', '1');
                    $('.dsrbfw_modal').css('visibility', 'visible');

                    // We need to free this event after openning our popup to submit form
                    bumpShown = true;
                    
                    return false;
                }
            });
        }

        // Make parent click to check checkbox for select product (To-Do: Need to check for child element click)
        $( document ).on('click', '.dsrbfw_ob_bo_product_main', function( e ) {

            // We have done this to rpevent doule call of checkbox while select on child element
            var checkbox = $(this).find('.dsrbfw_ob_bo_checkbox');
            
            // We can only make trigger if anyother element clicked except checkbox
            if ( $(e.target).is('.dsrbfw_ob_bo_checkbox') || $(e.target).is('.dsrbfw_ob_bo__variation-select') ) {
                return;
            }

            if( checkbox.is(':disabled') ) {
                return;
            }

            checkbox.prop('checked', !checkbox.prop('checked'));
            checkbox.trigger('change');
        });

        // Add bump product to order
        $(document).on('click', '.dsrbfw_ob_bo_buy', function(e) {
            e.preventDefault();

            // if block enabled then skip this event
            if ( window?.wc?.blocksCheckout ) {
                return;
            }

            var checkoutForm = $('form.checkout');
            var isFormSubmited = false;

            $('.dsrbfw_modal .dsrbfw_ob_bo_main_inner_wrap').block( { message: null, overlayCSS: { background: '#fff', opacity: 0.6 } } );
            
            $('.dsrbfw_ob_bo_checkbox').each(function(){
                
                var bump_id = $(this).val();

                // if product selected or not
                if( $(this).prop('checked') ) {
                    $('.dsrbfw_ob_bo_checkout_data').find('.dsrbfw_ob_bo_bump_action-'+bump_id).val('add');
                } else {
                    $('.dsrbfw_ob_bo_checkout_data').find('.dsrbfw_ob_bo_bump_action-'+bump_id).val('');
                }
            });

            checkoutForm.trigger('update_checkout');

            $('form.checkout').off('checkout_place_order.dsbrfwBumpOrder');

            $(document).ajaxComplete(function (event, xhr, settings) {
                // ensure this is the update_order_review AJAX request.
                if ( !settings.url.includes( 'update_order_review' ) ) {
                    return;
                }
                if ( isFormSubmited === true ) {
                    return;
                }
                isFormSubmited = true;
                checkoutForm.submit();
            });
        });

        // Skip offer from order bump popup and placed order
        $(document).on('click', '.dsrbfw_ob_bo_skip', function() {
            
            // if block enabled then skip this event
            if ( window?.wc?.blocksCheckout ) {
                return;
            }

            if( ! $('.dsrbfw_modal').hasClass('hidden') ) { 
                $('.dsrbfw_modal').removeClass('shown').addClass('hidden');
                setTimeout(function() {
                    $('.dsrbfw_modal').remove(); 
                },1000);
            }
            
            $('form.checkout').submit();
        });

        // Validation for Order Bump popup buy button
        $(document).on('change', '.dsrbfw_ob_bo_checkbox', function() {
            enableBOButton();
        });

        /**
         * Block Cart and Checkout compatibility
         */
        // Appned Addon term in item name in checkout
        if( window?.wc?.blocksCheckout ) {
            const { registerCheckoutFilters } = wc.blocksCheckout;
            registerCheckoutFilters( 'dotstore-revenue-booster', {
                itemName: ( value, extensions ) => {

                    if( ! extensions?.dotstore_revenue_booster?.added_via_dotstore_ob_ac ){
                        return value;
                    }
                    
                    return '('+dsrbfw_front_vars.dsrbfw_addon_prefix_text+') ' + value;
                }
            });
        }
        
        $(document).on('change', '.dsrbfw_ob_ac_footer-checkbox', function () {
            
            if (!$(this).parents('[data-block-name="woocommerce/checkout"]').length) {
              return;
            }

            if (!wc?.blocksCheckout?.extensionCartUpdate) {
              return;
            }
            var bump_rule_id = $(this).val();
            var main_element = $(this).closest('.dsrbfw_ob_ac_footer');
            var allVariationSelected = checkAllACVariationSelected($(this).closest('.dsrbfw_ob_ac_main').find('.dsrbfw_ob_ac__variation'));
            if( allVariationSelected ) {
                var bump_action_type = 'remove';
                if ( $(this).prop( 'checked' ) ) {
                    bump_action_type = 'add';
                }
                const bump_data = {};
                const data = {};
                bump_data[bump_rule_id] = {
                    'dsrbfw_ob_ac-bump-action': bump_action_type,
                    'dsrbfw_ob_ac-product-id': main_element.find('.dsrbfw_ob_ac_product_id').val(),
                    'dsrbfw_ob_ac-variation-id': main_element.find('.dsrbfw_ob_ac_variation_id').val(),
                    'dsrbfw_ob_ac-variation-data': main_element.find('.dsrbfw_ob_ac_variation_data').val(),
                };
                data['dsrbfw_ob_ac-bump-data'] = bump_data;
                
                $('.wp-block-woocommerce-checkout').block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
                wc.blocksCheckout.extensionCartUpdate({
                    namespace: 'dotstore-revenue-booster-ac-bump',
                    data
                }).then(() => {
                    OrderBumpCheckbox();
                    // Update mini cart fragment for updated cart data
                    setTimeout(function() {
                        $(document.body).trigger('wc_fragment_refresh');
                    },300);
                    $('.wp-block-woocommerce-checkout').unblock();
                });
            } else {
                checkAllACVariationSelected( $(this).closest('.dsrbfw_ob_ac_main').find('.dsrbfw_ob_ac__variation') );
                $.scroll_to_notices($(this).closest('.dsrbfw_ob_ac_main'));
            }
        });

        $('.dsrbfw_ob_ac__variation-select option').each(function () {
            $(this).attr('value', $(this).attr('data-value') || '');
        });

        // Open order bump popup on block checkout page when click place order
        wp.data.subscribe(() => {
            if (!wp.data.select('wc/store/checkout')?.isBeforeProcessing()) {
              return;
            }

            if ( ! $('.dsrbfw_modal').length ) {
              return;
            }

            wp.data.dispatch('wc/store/checkout').__internalSetIdle();

            if( $('.dsrbfw_modal').hasClass('hidden') ) {

                $('.dsrbfw_modal').removeClass('hidden').addClass('shown');
                $('.dsrbfw_modal').css('display', 'block');
                $('.dsrbfw_modal').css('opacity', '1');
                $('.dsrbfw_modal').css('visibility', 'visible');
                
            }
        }, 'wc/store/checkout');

        // Skip offer from order bump popup and placed order for block checkout
        $(document).on('click', '.dsrbfw_ob_bo_skip', function( e ) {
            
            if ( !window?.wc?.blocksCheckout ) {
                return;
            }

            e.preventDefault();

            if( ! $('.dsrbfw_modal').hasClass('hidden') ) { 
                $('.dsrbfw_modal').remove(); 
            }
            
            // continue block checkout process for placing order
            wp.data.dispatch('wc/store/checkout')?.__internalSetBeforeProcessing();
        });

        // Add bump product to order
        $(document).on('click', '.dsrbfw_ob_bo_buy', function(e) {

            // If classic checkout enabled then skip this event
            if ( ! window?.wc?.blocksCheckout ) {
                return;
            }

            e.preventDefault();

            const bump_data = {};
            const data = {};

            $('.dsrbfw_modal .dsrbfw_ob_bo_main_inner_wrap').block( { message: null, overlayCSS: { background: '#fff', opacity: 0.6 } } );
            $('.dsrbfw_ob_bo_buy').text('Processing...');

            $('.dsrbfw_ob_bo_checkbox').each(function(){
                
                var bump_id = $(this).val();
                var bump_action_type = '';

                // if product selected or not
                if( $(this).prop('checked') ) {
                    bump_action_type = 'add';
                }

                var main_element = $('.dsrbfw_ob_bo_checkout_data');
                
                bump_data[bump_id] = {
                    'dsrbfw_ob_bo-bump-action': bump_action_type,
                    'dsrbfw_ob_bo-product-id': main_element.find( '.dsrbfw_ob_bo_product_id-' + bump_id ).val(),
                    'dsrbfw_ob_bo-variation-id': main_element.find( '.dsrbfw_ob_bo_variation_id-' + bump_id ).val(),
                    'dsrbfw_ob_bo-variation-data': main_element.find( '.dsrbfw_ob_bo_variation_data-' + bump_id ).val(),
                };
                data['dsrbfw_ob_bo-bump-data'] = bump_data;
            });

            wc.blocksCheckout.extensionCartUpdate({
                namespace: 'dotstore-revenue-booster-bo-bump',
                data
              }).then(() => {

                if( ! $('.dsrbfw_modal').hasClass('hidden') ) { 
                    $('.dsrbfw_modal').remove(); 
                }
                
                // continue block checkout process for placing order
                wp.data.dispatch('wc/store/checkout')?.__internalSetBeforeProcessing();
              });

        });

    });

    /**
     * Functions declaration
     */

    var updateFinalPrice = function() {
        var product_ids = [''];

        $('.dsrbfw_fbt__product').each(function(){
            if( $(this).find('.dsrbfw_fbt__checkbox').is(':checked') ) {
                if( 'variable' === $(this).find('.dsrbfw_fbt__checkbox').data('product-type') || 'variation' === $(this).find('.dsrbfw_fbt__checkbox').data('product-type')  ) {
                    var variable_id = $(this).find('.dsrbfw_fbt__checkbox').attr('data-variation-id') ? $(this).find('.dsrbfw_fbt__checkbox').attr('data-variation-id') : $(this).find('.dsrbfw_fbt__checkbox').val();
                    product_ids.push( parseInt(variable_id));
                } else {
                    product_ids.push( $(this).find('.dsrbfw_fbt__checkbox').val());
                }
            }
        });

        $('.dsrbfw_fbt__body').block( { message: null, overlayCSS: { background: '#fff', opacity: 0.6 } } );

        var data = {
            'action': 'dsrbfw_update_final_price',
            'dsrbfw_product_ids': product_ids,
            'dsrbfw_main_product': $('.dsrbfw_main_product_id').val(),
            'security': dsrbfw_front_vars.dsrbfw_update_final_price_nonce
        };

        $.ajax({
            type: 'POST',
            url: dsrbfw_front_vars.ajaxurl,
            data: data,
            success: function( response ){
                if( dsrbfw_front_vars.debug_mode ) {
                    console.log( 'Response: ' + data.action );
                    console.log( response );
                }
                if( response.data ) {
                    $('.dsrbfw_fbt__actions .dsrbfw_fbt__total-price-amount').html(response.data.price_html);
                } 
            },
            complete: function() {
                $('.dsrbfw_fbt__body').unblock();
            }
        });
        
    };

    var addToCartButtonStatus = function(){

        if ( $('.dsrbfw_fbt__product').find('.dsrbfw_fbt__checkbox').length < 1 ) {
            $('.dsrbfw_fbt__button').addClass('disabled');
        }

        $('.dsrbfw_fbt__product').each(function(){
            if( $(this).find('.dsrbfw_fbt__checkbox').is(':checked') ){
                var all_data = getChosenAttributes($(this).find('.dsrbfw_fbt__variation-select'));
                if( all_data.count === all_data.chosenCount ) {
                    $('.dsrbfw_fbt__button').removeClass('disabled');
                } else {
                    $('.dsrbfw_fbt__button').addClass('disabled');
                    return false;
                }
            }
        });
    };

    var getChosenAttributes = function(element) {
		var data   = {};
		var count  = 0;
		var chosen = 0;

		$(element).each( function() {
			var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
			var value          = $( this ).val() || '';

			if ( value.length > 0 ) {
				chosen ++;
			}

			count ++;
			data[ attribute_name ] = value;
		});

		return {
			'count'      : count,
			'chosenCount': chosen,
			'data'       : data
		};
	};

    var CustomerAlsoBuyModule = function() {
        
        $(document).ready(function() {
            $('.dsrbfw_cab_modal').hasClass('hidden') ? $('.dsrbfw_cab_modal').removeClass('hidden') : $('.dsrbfw_cab_modal').addClass('hidden');
            $('.dsrbfw_overlay').hasClass('hidden') ? $('.dsrbfw_overlay').removeClass('hidden') : $('.dsrbfw_overlay').addClass('hidden');            
        });

        $(document).on('click', '.dsrbfw_close_modal', function() {
            if( ! $('.dsrbfw_cab_modal').hasClass('hidden') ) { 
                $('.dsrbfw_cab_modal').addClass('hidden'); 
            }
            if( ! $('.dsrbfw_overlay').hasClass('hidden') ) {
                $('.dsrbfw_overlay').addClass('hidden'); 
            }
        });
    };

    window.OrderBumpCheckbox = function() {
        if( $('.dsrbfw_ob_ac_footer .dsrbfw_ob_ac_footer-checkbox').length ) {
            
            $( '.dsrbfw_ob_ac_footer .dsrbfw_ob_ac_footer-checkbox' ).each( function() {
                var checkbox = $( this );
                var main_element = checkbox.closest('.dsrbfw_ob_ac_main');
                
                //if checkbox is already checked then no need to disable it as user already added to cart which passed validation
                if ( checkbox.prop('checked') ) {
                    checkbox.prop('disabled', false);
                    main_element.find('.dsrbfw_ob_ac__variation-select').prop('disabled', true);
                } else {
                    // Now we can check for variation has been selected or not
                    var allVariationSelected = checkAllACVariationSelected(checkbox.closest('.dsrbfw_ob_ac_main').find('.dsrbfw_ob_ac__variation'));
                    checkbox.prop('disabled', ! allVariationSelected);

                    if( allVariationSelected ) {
                        main_element.find('.dsrbfw_ob_ac__variation-select').prop('disabled', false );
                    }
                }
            });
        }
    };

    window.checkAllACVariationSelected = function( variationTable, classChange = false ) {
        var allSelected = true;
        if( variationTable.find('.dsrbfw_ob_ac__variation-select').length ) {
            variationTable.find('.dsrbfw_ob_ac__variation-select').each( function() { 
                if( ! $(this).val() || ( window?.wc?.blocksCheckout && $(this).val() === 'Choose an option' ) ) {
                    allSelected = false;
                    if( window?.wc?.blocksCheckout && classChange ) {
                        $(this).addClass('has-error');
                    }
                } else {
                    if( window?.wc?.blocksCheckout && classChange ) {
                        $(this).removeClass('has-error');
                    }
                }
            });
        }
        return allSelected;
    };

    var getACVariation = function( variationTable ) {
        
        var check_variation_table = variationTable ? variationTable : $('.dsrbfw_ob_ac_main .dsrbfw_ob_ac__variation'); 
        if ( check_variation_table.length <= 0 ) {
            return;
        }
        
        var changeClass = false;
        // for block checkout we need to add class else for classic checkout we have put normal WC JS validation
        if( window?.wc?.blocksCheckout ) {
            changeClass = true;
        }

        check_variation_table.each( function() {
            var main_element = $(this).closest('.dsrbfw_ob_ac_main');
        
            if( checkAllACVariationSelected( $(this), changeClass ) ) {
                main_element.block( { message: null, overlayCSS: { background: '#fff', opacity: 0.6 } } );
                var all_data = getChosenAttributes($(this).find('.dsrbfw_ob_ac__variation-select'));
                var selected_attributes = all_data.data;

                //Set selected variation data in hidden field for AJAX call
                $(this).closest('.dsrbfw_ob_ac_main').find('.dsrbfw_ob_ac_footer .dsrbfw_ob_ac_variation_data').val( JSON.stringify( selected_attributes ) );

                var data = {
                    'action': 'dsrbfw_get_variation_price',
                    'dsrbfw_variable_id': $(this).closest('.dsrbfw_ob_ac_main').find('.dsrbfw_ob_ac__variation').data('variable-id'),
                    'dsrbfw_attributes': selected_attributes,
                    'security': dsrbfw_front_vars.dsrbfw_variation_price_nonce
                };

                $.ajax({
                    type: 'POST',
                    url: dsrbfw_front_vars.ajaxurl,
                    data: data,
                    success: function( response ) {
                        if( dsrbfw_front_vars.debug_mode ) {
                            console.log( 'Response: ' + data.action );
                            console.log( response );
                        }
                        if( response.is_purchasable && response.is_in_stock ) {
                            main_element.find('.dsrbfw_ob_ac_product_img').attr('src', response.image.full_src);
                            main_element.find('.dsrbfw_ob_ac_product_price_html').html(response.price_html);
                            main_element.find('.dsrbfw_ob_ac_variation_id').val(response.variation_id);
                            main_element.find('.dsrbfw_ob_ac_variation_data').val(JSON.stringify(selected_attributes));
                            main_element.find('.dsrbfw_ob_ac_footer-checkbox').prop('disabled', false);
                            // If some smart people manually remove disabled attribute from checkbox then we need to uncheck it
                            main_element.find('.dsrbfw_ob_ac_footer-checkbox').prop('checked', false);
                            main_element.find('.dsrbfw_ob_ac_footer-checkbox').trigger('change');
                        } else {
                            main_element.find('.dsrbfw_ob_ac_product_price_html').html(dsrbfw_front_vars.dsrbfw_unavailable_text);
                            main_element.find('.dsrbfw_ob_ac_footer-checkbox').prop('disabled', true);
                        }
                    },
                    complete: function() {
						main_element.unblock();
					}
                });
            } else {
                main_element.find('.dsrbfw_ob_ac_footer-checkbox').prop('disabled', true);
            }
        });
    };

    window.checkAllBOVariationSelected = function( variationTable, classChange = false ) {
        var allSelected = true;
        if( variationTable.find('.dsrbfw_ob_bo__variation-select').length ) {
            variationTable.find('.dsrbfw_ob_bo__variation-select').each( function() { 
                if( ! $(this).val() || ( window?.wc?.blocksCheckout && $(this).val() === 'Choose an option' ) ) {
                    allSelected = false;
                    if( window?.wc?.blocksCheckout && classChange ) {
                        $(this).addClass('has-error');
                    }
                } else {
                    if( window?.wc?.blocksCheckout && classChange ) {
                        $(this).removeClass('has-error');
                    }
                }
            });
        }
        return allSelected;
    };

    var getBOVariation = function( variationTable ) {
        
        var check_variation_table = variationTable ? variationTable : $('.dsrbfw_ob_bo_product_main .dsrbfw_ob_bo__variation'); 
        if ( check_variation_table.length <= 0 ) {
            return;
        }
        
        var changeClass = false;
        // for block checkout we need to add class else for classic checkout we have put normal WC JS validation
        if( window?.wc?.blocksCheckout ) {
            changeClass = true;
        }

        check_variation_table.each( function() {
            var bump_id = $(this).data('bump-id');
            var main_element = $(this).closest('.dsrbfw_ob_bo_product_main');
            var allVariationSelected = checkAllBOVariationSelected( $(this), changeClass );

            if( allVariationSelected ) {
                main_element.block( { message: null, overlayCSS: { background: '#fff', opacity: 0.6 } } );
                $('.dsrbfw_ob_bo_buy').text('Processing...').prop('disabled', true);
                var all_data = getChosenAttributes($(this).find('.dsrbfw_ob_bo__variation-select'));
                var selected_attributes = all_data.data;

                //Set selected variation data in hidden field for AJAX call
                $(this).closest('.dsrbfw_ob_bo_product_main').find('.dsrbfw_ob_bo_footer .dsrbfw_ob_bo_variation_data').val( JSON.stringify( selected_attributes ) );

                var data = {
                    'action': 'dsrbfw_get_variation_price',
                    'dsrbfw_variable_id': $(this).closest('.dsrbfw_ob_bo_product_main').find('.dsrbfw_ob_bo__variation').data('variable-id'),
                    'dsrbfw_attributes': selected_attributes,
                    'security': dsrbfw_front_vars.dsrbfw_variation_price_nonce
                };

                $.ajax({
                    type: 'POST',
                    url: dsrbfw_front_vars.ajaxurl,
                    data: data,
                    success: function( response ) {
                        if( dsrbfw_front_vars.debug_mode ) {
                            console.log( 'Response: ' + data.action );
                            console.log( response );
                        }

                        var update_data_element = $('form.checkout').find('.dsrbfw_ob_bo_checkout_data'); //for classic checkout

                        //For block checkout
                        if( window?.wc?.blocksCheckout ) {
                            update_data_element = $('.dsrbfw_ob_bo_checkout_data');
                        } 

                        if( response.is_purchasable && ( response.is_in_stock || response.backorders_allowed ) ) {
                            main_element.find('.dsrbfw_ob_bo_product_img').attr('src', response.image.full_src);
                            main_element.find('.dsrbfw_ob_bo_product_price_html').html(response.price_html);
                            update_data_element.find('.dsrbfw_ob_bo_variation_id-' + bump_id).val(response.variation_id);
                            update_data_element.find('.dsrbfw_ob_bo_variation_data-' + bump_id).val(JSON.stringify(selected_attributes));
                            main_element.find('.dsrbfw_ob_bo_checkbox').prop('disabled', false);
                        } else {
                            if( ! response.is_in_stock && ! response.backorders_allowed ) {
                                main_element.find('.dsrbfw_ob_bo_product_price_html').html(response.availability_html);
                            } else {
                                main_element.find('.dsrbfw_ob_bo_product_price_html').html(dsrbfw_front_vars.dsrbfw_unavailable_text);
                            }
                            main_element.find('.dsrbfw_ob_bo_checkbox').prop('disabled', true);
                        }
                        // If some smart people manually remove disabled attribute from checkbox then we need to uncheck it
                        main_element.find('.dsrbfw_ob_bo_checkbox').prop('checked', false);
                        main_element.find('.dsrbfw_ob_bo_checkbox').trigger('change');
                    },
                    complete: function() {
                        $('.dsrbfw_ob_bo_buy').text('Add to Order');
						main_element.unblock();
					}
                });
            } else {
                main_element.find('.dsrbfw_ob_bo_checkbox').prop('disabled', true);
                main_element.find('.dsrbfw_ob_bo_checkbox').prop('checked', false);
                main_element.find('.dsrbfw_ob_bo_checkbox').trigger('change');  
            }
        });
    };

    var enableBOButton = function() {
        $('.dsrbfw_ob_bo_checkbox').each(function(){
            if( $(this).prop('checked') ) {
                $('.dsrbfw_ob_bo_buy').prop('disabled',false);
                return false;
            } else {
                $('.dsrbfw_ob_bo_buy').prop('disabled', true);
            }
        });
    };

})( jQuery );

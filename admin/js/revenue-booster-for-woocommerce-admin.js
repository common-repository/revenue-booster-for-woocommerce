(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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
    $(document).ready(function() { 

        /** tiptip js implementation */
        $( '.woocommerce-help-tip' ).tipTip( {
            'attribute': 'data-tip',
            'fadeIn': 50,
            'fadeOut': 50,
            'delay': 200,
            'keepAlive': true
        } );

        // Activate dotstore logo in admin menu
        $('a[href="admin.php?page=dsrbfw-global-settings"]').parents().addClass( 'current wp-has-current-submenu' );
        $('a[href="admin.php?page=dsrbfw-global-settings"]').addClass('current');
        
        // Colour picker for the plugin settings
        $('input[type="color"]').on( 'input', function() {
            var color_code = $(this).val();
            $(this).next('input[type="text"]').val(color_code);
        });

        $('input[type="text"]').on( 'input', function() {
            var color_code = $(this).val();
            $(this).prev('input[type="color"]').val(color_code);
        });

        // Remove highlighted class from the switch when it is checked
        $('.switch input[type="checkbox"]').on('change', function() {
            if( $(this).prop('checked') ) {
                $(this).closest('.switch').find('.slider').removeClass('dsrbfw-highlight');
            }
        });

        $('.status_switch').change(function(){
            var get_val = $(this).is(':checked');
            var obid = $(this).data('obid');
            var table_post_type = $('#the-list').data('wp-lists');
            const array_data = table_post_type.split(':');
            var current_post_type = array_data[1];
            if( obid > 0 && '' !== obid ) {
                $.ajax({
                    type: 'POST',
                    url: dsrbfw_vars.ajaxurl,
                    data: {
                        'action': 'dsrbfw_change_status_from_list',
                        'dsrbfw_ob_id': obid,
                        'dsrbfw_ob_status': get_val,
                        'dsrbfw_ob_post_type': current_post_type,
                        'security': dsrbfw_vars.dsrbfw_status_change_listing_nonce
                    },
                    beforeSend: function(){
                        dsrbfw_loader_show( '.dsrbfw-section-left .wp-list-table' );
                    },
                    success: function( responce ){
                        $('.dsrbfw-notice').remove();
                        var msg_div = $('<div/>');
                        msg_div.attr('id', 'message');
                        var msg_p = $('<p/>');
                        
                        if( responce.success ) {
                            msg_div.attr('class', 'notice notice-success dsrbfw-notice');
                        } else {
                            msg_div.attr('class', 'notice notice-error dsrbfw-notice');
                        }
                        
                        msg_p.text(responce.data);
                        msg_div.append(msg_p);
                        $(msg_div).insertAfter('.wp-header-end');
                        dsrbfw_loader_hide( '.dsrbfw-section-left .wp-list-table' );
                        setTimeout(function(){
                            msg_div.fadeOut(500, function(){
                                $(this).remove();
                            });
                        }, 5000);
                    },
                    error: function(){
                        $('.dsrbfw-notice').remove();
                        var error_div = $('<div/>');
                        error_div.attr('class', 'notice notice-error dsrbfw-notice');
                        error_div.attr('id', 'message');
                        var error_p = $('<p/>');
                        error_p.text(dsrbfw_vars.dsrbfw_ajax_error_message);
                        error_div.append(error_p);
                        $(error_div).insertAfter('.wp-header-end');
                        dsrbfw_loader_hide( '.dsrbfw-section-left .wp-list-table' );
                        setTimeout(function(){
                            error_div.fadeOut(500, function(){
                                $(this).remove();
                            });
                        }, 5000);
                    }
                });
            }
        });

        // Get confirmation before bulk delete to prevent from accidental delete
        $(document).on( 'click', '#doaction, #doaction2', function( e ) {
            var bulk_action = $(this).prev('select').val();
            if( 'delete' === bulk_action ) {
                if( confirm(dsrbfw_vars.delete_confirmation_message) ) {
                    return true;
                } else {
                    e.preventDefault();
                    return false;
                }
            }
        });

        $(document).on('click', '.delete a', function( e ) {
            if( confirm(dsrbfw_vars.delete_confirmation_message) ) {
                return true;
            } else {
                e.preventDefault();
                return false;
            }
        });

        init_select2();

        /** Rule Filter Start */

        // Add new field for
        $('body').on('click', '#dsrbfw-ob-add-filter', function () {
            
            // Add WC loader
            dsrbfw_loader_show( $('#dsrbfw-ob-filter').closest('.element-shadow') );

			var ob_table = $('#dsrbfw-ob-filter tbody');
            let count = $('#dsrbfw-ob-filter tbody tr').length - 1;

            // Create the new row (tr)
            var tr = $('<tr></tr>');
            tr.attr( 'id', 'row_' + count );
            tr.attr( 'valign', 'top');
			ob_table.append(tr);

            // generate td of 1st column condition
			var td1 = $('<td></td>');
            td1.attr( 'class', 'titledesc th_dsrbfw_ob_condition' );
            td1.attr( 'scope', 'row');
			tr.append(td1);

            // Create select dropdown for condition
            var select1 = $('<select></select>');
            select1.attr( 'rel-id', count );
            select1.attr( 'id', 'dsrbfw_ob_condition_' + count );
            select1.attr( 'name', 'dsrbfw_ob[dsrbfw_ob_condition][]' );
            select1.attr( 'class', 'dsrbfw_ob_condition' );
            td1.append(select1);

            // Add options to the select dropdown
            $.each(dsrbfw_vars.dsrbfw_filter_conditions, function( key, value ) {
                var option = $('<option></option>');
                option.val( key );
                option.html(value).text(); // decode html entities like special characters
                select1.append(option);
            });

            // generate td of 2nd column condition
			var td2 = $('<td></td>');
            td2.attr( 'class', 'select_condition_for_in_notin' );
			tr.append(td2);

            // Create select dropdown for condition
            var select2 = $('<select></select>');
            select2.attr( 'name', 'dsrbfw_ob[dsrbfw_ob_is][]' );
            select2.attr( 'class', 'dsrbfw_ob_is_' + count );
            td2.append(select2);

            // Add options to the select dropdown
            $.each(dsrbfw_vars.dsrbfw_filter_action, function( key, value ) {
                var option = $('<option></option>');
                option.val( key );
                option.html(value).text(); // decode html entities like special characters
                select2.append(option);
            });

            // generate td of 3rd column condition
			var td3 = $('<td></td>');
            td3.attr( 'id', 'column_' + count );
            td3.attr( 'class', 'condition-value' );
			tr.append(td3);

            var selection = $('<select></select>');
            selection.attr( 'id', 'product-filter-' + count );
            selection.attr( 'rel-id', count );
            selection.attr( 'name', 'dsrbfw_ob[dsrbfw_ob_values][value_' + count + '][]' );
            selection.attr( 'class', 'ds-woo-search' );
            selection.attr( 'data-placeholder', dsrbfw_vars.select2_product_placeholder );
            selection.attr( 'data-allow_clear', 'true' );
            selection.attr( 'multiple', 'multiple' );
            selection.attr( 'data-width', '100%' );
            selection.attr( 'data-sortable', 'true' );
            td3.append(selection);

            //Init select2 for newly added HTML
            init_select2();

            // generate td of 4th column condition
			var td4 = $('<td></td>');
			tr.append(td4);

            // generate delete button for rule
            var delete_a = $('<a></a>');
            delete_a.attr( 'rel-id', count );
            delete_a.attr( 'class', 'dsrbfw-delete-filter' );
            delete_a.attr( 'href', 'javascript:void(0);' );
            delete_a.attr( 'title', dsrbfw_vars.rbe_filter_delete_title );
            td4.append(delete_a);

            var deleteicon = $('<i/>');
			deleteicon.attr( 'class', 'dashicons dashicons-trash');
			delete_a.append(deleteicon);

            //No filter toggle based on rule add/remove
            if( ob_table.find('tr').length > 1 ) {
                $('tr.dsrbfw-no-filter-tr').removeClass('dsrbfw-no-filter-tr-show').addClass('dsrbfw-no-filter-tr-hide');
            }

            // Remove WC loader
            dsrbfw_loader_hide( $('#dsrbfw-ob-filter').closest('.element-shadow') );
        });

        // Add condition value based on condition selection
        $(document).on('change', '.dsrbfw_ob_condition', function() {
            var condition = $(this).val();
            var count = $(this).attr('rel-id');
            $('#column_' + count).empty();
            var td = $('#dsrbfw-ob-filter tbody tr td#column_' + count);
            var selection = $('<select></select>');
            selection.attr( 'class', 'ds-woo-search' );
            
            if( 'product' === condition ) {
                selection.attr( 'id', 'product-filter-' + count );
                selection.attr( 'data-placeholder', dsrbfw_vars.select2_product_placeholder );
                
            } else if ( 'category' === condition ) {
                selection.attr( 'id', 'category-filter-' + count );
                selection.attr( 'data-placeholder', dsrbfw_vars.select2_category_placeholder );
                selection.attr( 'data-action', 'dsrbfw_json_search_categories' );
            }
            
            selection.attr( 'rel-id', count );
            selection.attr( 'name', 'dsrbfw_ob[dsrbfw_ob_values][value_' + count + '][]' );
            selection.attr( 'data-allow_clear', 'true' );
            selection.attr( 'multiple', 'multiple' );
            selection.attr( 'data-width', '100%' );
            selection.attr( 'data-sortable', 'true' );
            td.append(selection);

            //Init select2 for newly added HTML
            init_select2();
        });

        // Delete filter rule
        $(document).on('click', '.dsrbfw-delete-filter', function() {

            // Add WC loader
            dsrbfw_loader_show( $('#dsrbfw-ob-filter').closest('.element-shadow') );

            var rel_id = $(this).attr('rel-id');
            var row_id = 'row_' + rel_id;

            $('#'+row_id).remove();
            
            if( $('#dsrbfw-ob-filter tr').length <= 1 ) {
                if( $('tr.dsrbfw-no-filter-tr').hasClass('dsrbfw-no-filter-tr-show') ){
                    $('tr.dsrbfw-no-filter-tr').removeClass('dsrbfw-no-filter-tr-show').addClass('dsrbfw-no-filter-tr-hide');
                } else {
                    $('tr.dsrbfw-no-filter-tr').removeClass('dsrbfw-no-filter-tr-hide').addClass('dsrbfw-no-filter-tr-show');
                
                }
            }

            // Remove WC loader
            dsrbfw_loader_hide( $('#dsrbfw-ob-filter').closest('.element-shadow') );
        });

        /** Rule Filter End */
    });

    //Global function created for select2
    window.init_select2 = function(){

        /**
         * SelectWoo dropdown for product selection
         */
        $('.ds-woo-search').filter(':not(.enhanced)').each(function() {
            var ds_select2 = $(this);
            ds_select2.selectWoo({
                placeholder: ds_select2.data( 'placeholder' ),
                allowClear: ds_select2.data( 'allow_clear' ) ? true : false,
                minimumInputLength: ds_select2.data( 'minimum_input_length' ) ? ds_select2.data( 'minimum_input_length' ) : '3',
                ajax: {
                    url: dsrbfw_vars.ajaxurl,
                    dataType: 'json',
                    delay: 250,
                    cache: true,
                    data: function(params) {
                        return {
                            search          : params.term,
                            action          : ds_select2.data( 'action' ) || 'dsrbfw_json_search_products',
                            display_pid     : ds_select2.data( 'display_id' ) ? true : false,
                            security        : dsrbfw_vars.dsrbfw_woo_search_nonce,
                            posts_per_page  : dsrbfw_vars.select2_per_data_ajax,
                            offset          : params.page || 1,
                        };
                    },
                    processResults: function( data ) {
                        var terms = [];
                        if ( data ) {
                            $.each( data, function( id, text ) {
                                terms.push( { id: id, text: text } );
                            });
                        }
                        var pagination = terms.length > 0 && terms.length >= dsrbfw_vars.select2_per_data_ajax ? true : false;
                        return {
                            results: terms,
                            pagination: {
                                more : pagination
                            } 
                        };
                    }
                }
            });
        });
    };

    // Global function for loader start
    window.dsrbfw_loader_show = function( element ) {
        $(element).block({
            message: null,
            overlayCSS: {
                background: 'rgb(255, 255, 255)',
                opacity: 0.6,
            },
        });
    };

    // Global function for loader stop
    window.dsrbfw_loader_hide = function ( element ) {
        $(element).unblock();
    };
})( jQuery );
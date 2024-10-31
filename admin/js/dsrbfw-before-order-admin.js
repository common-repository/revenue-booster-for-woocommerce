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
    $( document ).ready(function() { 

        // Form validation (Before Order)
        $(document).on('submit', 'form[name="dsrbfw_ob_bo_form"]', function() {
            var form = $(this);
            var valid = true;
            form.find('.dsrbfw-required').each(function() {
                if ( $(this).val() === '' || $(this).val() === null ) {
                    if( $(this).data('select2') ){
                        console.log($(this).parents('.forminp'));
                        $(this).addClass('dsrbfw-error');
                    }
                    $(this).addClass('dsrbfw-error');
                    valid = false;
                } else {
                    if( $(this).data('select2') ){
                        $(this).removeClass('dsrbfw-error');
                    }
                    $(this).removeClass('dsrbfw-error');
                }
            });
            
            if ( ! valid ) {
                window.scrollTo({top: 0, behavior: 'smooth'});
                return false;
            }

            if( ! form.find('#dsrbfw_ob_bo_status').prop('checked') ) {
                if( confirm(dsrbfw_ob_bo_vars.confirm_status_before_submit_msg) ) {
                    return true;
                } else {
                    $('#dsrbfw_ob_bo_status').closest('.switch').find('.slider').addClass('dsrbfw-highlight');
                    window.scrollTo({top: 0, behavior: 'smooth'});
                    return false;
                }
            }    
        });
    });
})( jQuery );
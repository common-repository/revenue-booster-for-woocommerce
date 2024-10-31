<?php
/**
 * Handles plugin global settings page.
 * 
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( DSRBFW_PLUGIN_HEADER_LINK );

$allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];

$menu_page = filter_input(INPUT_GET,'page',FILTER_SANITIZE_SPECIAL_CHARS);
$dsrbfw_submit = filter_input(INPUT_POST,'submit',FILTER_SANITIZE_SPECIAL_CHARS);
if ( isset( $dsrbfw_submit ) && isset( $menu_page ) && $menu_page === 'dsrbfw-global-settings' ) {
    $dsrbfw_save_settings_nonce = filter_input( INPUT_POST, 'dsrbfw_save_global_settings_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    if ( !empty( $dsrbfw_save_settings_nonce ) || wp_verify_nonce( sanitize_text_field( $dsrbfw_save_settings_nonce ), 'dsrbfw_save_global_settings' ) ) {

        $get_dsrbfw_fbt_title               = filter_input( INPUT_POST, 'dsrbfw_fbt_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_dsrbfw_fbt_title_link_enabled  = filter_input( INPUT_POST, 'dsrbfw_fbt_title_link_enabled', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_dsrbfw_fbt_image_enabled       = filter_input( INPUT_POST, 'dsrbfw_fbt_image_enabled', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_dsrbfw_fbt_cart_exist_enabled  = filter_input( INPUT_POST, 'dsrbfw_fbt_cart_exist_enabled', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_dsrbfw_fbt_ajax_cart_enabled   = filter_input( INPUT_POST, 'dsrbfw_fbt_ajax_cart_enabled', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_dsrbfw_fbt_choose_locations    = filter_input( INPUT_POST, 'dsrbfw_fbt_choose_locations', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_dsrbfw_acp_title               = filter_input( INPUT_POST, 'dsrbfw_acp_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_dsrbfw_acp_title_bg_color      = filter_input( INPUT_POST, 'dsrbfw_acp_title_bg_color', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $get_dsrbfw_acp_title_text_color    = filter_input( INPUT_POST, 'dsrbfw_acp_title_text_color', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

        $dsrbfw_fbt_title                  = !empty( $get_dsrbfw_fbt_title ) ? sanitize_text_field( $get_dsrbfw_fbt_title ) : '';
        $dsrbfw_fbt_title_link_enabled     = !empty( $get_dsrbfw_fbt_title_link_enabled ) ? sanitize_text_field( $get_dsrbfw_fbt_title_link_enabled ) : 'no';
        $dsrbfw_fbt_image_enabled          = !empty( $get_dsrbfw_fbt_image_enabled ) ? sanitize_text_field( $get_dsrbfw_fbt_image_enabled ) : 'no';
        $dsrbfw_fbt_cart_exist_enabled     = !empty( $get_dsrbfw_fbt_cart_exist_enabled ) ? sanitize_text_field( $get_dsrbfw_fbt_cart_exist_enabled ) : 'no';
        $dsrbfw_fbt_ajax_cart_enabled      = !empty( $get_dsrbfw_fbt_ajax_cart_enabled ) ? sanitize_text_field( $get_dsrbfw_fbt_ajax_cart_enabled ) : 'no';
        $dsrbfw_fbt_choose_locations       = !empty( $get_dsrbfw_fbt_choose_locations ) ? sanitize_text_field( $get_dsrbfw_fbt_choose_locations ) : 'woocommerce_after_add_to_cart_form';
        $dsrbfw_acp_title                  = !empty( $get_dsrbfw_acp_title ) ? sanitize_text_field( $get_dsrbfw_acp_title ) : '';
        $dsrbfw_acp_title_bg_color         = !empty( $get_dsrbfw_acp_title_bg_color ) ? sanitize_text_field( $get_dsrbfw_acp_title_bg_color ) : '#2b913c';
        $dsrbfw_acp_title_text_color       = !empty( $get_dsrbfw_acp_title_text_color ) ? sanitize_text_field( $get_dsrbfw_acp_title_text_color ) : '#ffffff';

        update_option( 'dsrbfw_fbt_title', $dsrbfw_fbt_title );
        update_option( 'dsrbfw_fbt_title_link_enabled', $dsrbfw_fbt_title_link_enabled );
        update_option( 'dsrbfw_fbt_image_enabled', $dsrbfw_fbt_image_enabled );
        update_option( 'dsrbfw_fbt_cart_exist_enabled', $dsrbfw_fbt_cart_exist_enabled );
        update_option( 'dsrbfw_fbt_ajax_cart_enabled', $dsrbfw_fbt_ajax_cart_enabled );
        update_option( 'dsrbfw_fbt_choose_locations', $dsrbfw_fbt_choose_locations );
        update_option( 'dsrbfw_acp_title', $dsrbfw_acp_title );
        update_option( 'dsrbfw_acp_title_bg_color', $dsrbfw_acp_title_bg_color );
        update_option( 'dsrbfw_acp_title_text_color', $dsrbfw_acp_title_text_color );
    }
}

$dsrbfw_fbt_title              = get_option( 'dsrbfw_fbt_title', '' );
$dsrbfw_fbt_title_link_enabled = get_option( 'dsrbfw_fbt_title_link_enabled', 'no' );
$dsrbfw_fbt_image_enabled      = get_option( 'dsrbfw_fbt_image_enabled', 'no' );
$dsrbfw_fbt_cart_exist_enabled = get_option( 'dsrbfw_fbt_cart_exist_enabled', 'no' );
$dsrbfw_fbt_ajax_cart_enabled  = get_option( 'dsrbfw_fbt_ajax_cart_enabled', 'no' );
$dsrbfw_fbt_choose_locations   = get_option( 'dsrbfw_fbt_choose_locations', 'woocommerce_after_add_to_cart_form' );
$dsrbfw_acp_title              = get_option( 'dsrbfw_acp_title', '' );
$dsrbfw_acp_title_bg_color     = get_option( 'dsrbfw_acp_title_bg_color', '#27ae60' );
$dsrbfw_acp_title_text_color   = get_option( 'dsrbfw_acp_title_text_color', '#ffffff' );

?>
<div class="dsrbfw-section-left">
	<div class="dsrbfw-main-table res-cl">
        <form method="post" action="#" enctype="multipart/form-data">
            <?php wp_nonce_field( 'dsrbfw_save_global_settings', 'dsrbfw_save_global_settings_nonce' ); ?>

            <!-- Frequently Bought Together Section Start -->
            <div class="dsrbfw-frequently-bought-together-section">
                <h2><?php esc_html_e( 'Frequently Bought Together Settings', 'revenue-booster-for-woocommerce' ); ?><?php echo wp_kses( wc_help_tip( esc_html__( 'Boost sales with smart upsells next to \'Add to Cart\' button on product pages. Maximize revenue effortlessly!', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?></h2>
                <table class="form-table table-outer dsrbfw-rule-table dsrbfw-table-tooltip" >
                    <tr>
                        <th scope="row">
                            <label>
                                <?php echo esc_html__( 'Section Title', 'revenue-booster-for-woocommerce' ); ?>
                                <?php echo wp_kses( wc_help_tip( esc_html__( 'We can customise title of section as per our need on frontend side.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" id="dsrbfw_fbt_title" name="dsrbfw_fbt_title" placeholder="<?php esc_attr_e( 'Frequently Bought Together', 'revenue-booster-for-woocommerce' ); ?>" value="<?php echo esc_attr($dsrbfw_fbt_title); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label>
                                <?php echo esc_html__( 'Product title link', 'revenue-booster-for-woocommerce' ); ?>
                                <?php echo wp_kses( wc_help_tip( esc_html__( 'Add product link with title or not', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                            </label>
                        </th>
                        <td>
                            <label class="switch">
                                <input type="hidden" name="dsrbfw_fbt_title_link_enabled" value="no" />
                                <input type="checkbox" id="dsrbfw_fbt_title_link_enabled" name="dsrbfw_fbt_title_link_enabled" value="yes" <?php checked( $dsrbfw_fbt_title_link_enabled, 'yes', true ); ?> />
                                <div class="slider round"></div>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label>
                                <?php echo esc_html__( 'Product Image', 'revenue-booster-for-woocommerce' ); ?>
                                <?php echo wp_kses( wc_help_tip( esc_html__( 'Show product image with product details or not', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                            </label>
                        </th>
                        <td>
                            <label class="switch">
                                <input type="hidden" name="dsrbfw_fbt_image_enabled" value="no" />
                                <input type="checkbox" id="dsrbfw_fbt_image_enabled" name="dsrbfw_fbt_image_enabled" value="yes" <?php checked( $dsrbfw_fbt_image_enabled, 'yes', true ); ?> />
                                <div class="slider round"></div>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label>
                                <?php echo esc_html__( 'Disable if exist in cart', 'revenue-booster-for-woocommerce' ); ?>
                                <?php echo wp_kses( wc_help_tip( esc_html__( 'If product already in cart then dont show it in list', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                            </label>
                        </th>
                        <td>
                            <label class="switch">
                                <input type="hidden" name="dsrbfw_fbt_cart_exist_enabled" value="no" />
                                <input type="checkbox" id="dsrbfw_fbt_cart_exist_enabled" name="dsrbfw_fbt_cart_exist_enabled" value="yes" <?php checked( $dsrbfw_fbt_cart_exist_enabled, 'yes', true ); ?> />
                                <div class="slider round"></div>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label>
                                <?php echo esc_html__( 'AJAX Add to cart', 'revenue-booster-for-woocommerce' ); ?>
                                <?php echo wp_kses( wc_help_tip( esc_html__( 'Do we need to add these group of products with ajax or with reload of page?', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                            </label>
                        </th>
                        <td>
                            <label class="switch">
                                <input type="hidden" name="dsrbfw_fbt_ajax_cart_enabled" value="no" />
                                <input type="checkbox" id="dsrbfw_fbt_ajax_cart_enabled" name="dsrbfw_fbt_ajax_cart_enabled" value="yes" <?php checked( $dsrbfw_fbt_ajax_cart_enabled, 'yes', true ); ?> />
                                <div class="slider round"></div>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label>
                                <?php echo esc_html__( 'Display Position', 'revenue-booster-for-woocommerce' ); ?>
                                <?php echo wp_kses( wc_help_tip( esc_html__( 'This option will show diffrent place on product details page where it will show list of products.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                            </label>
                        </th>
                        <td>
                            <select class="dsrbfw-choose-locations" name="dsrbfw_fbt_choose_locations">
                                <option value="woocommerce_before_single_product" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_before_single_product', true ); ?>><?php esc_html_e( 'Before Single Product', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_before_single_product_summary" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_before_single_product_summary', true ); ?>><?php esc_html_e( 'Before Single Product Summary', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_single_product_summary" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_single_product_summary', true ); ?>><?php esc_html_e( 'Within Single Product Summary', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_before_add_to_cart_form" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_before_add_to_cart_form', true ); ?>><?php esc_html_e( 'Before Add to Cart Form', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_before_variations_form" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_before_variations_form', true ); ?>><?php esc_html_e( 'Before Variations Form', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_before_add_to_cart_button" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_before_add_to_cart_button', true ); ?>><?php esc_html_e( 'Before Add to Cart Button', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_before_single_variation" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_before_single_variation', true ); ?>><?php esc_html_e( 'Before Single Variation', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_single_variation" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_single_variation', true ); ?>><?php esc_html_e( 'Within Single Variation', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_before_add_to_cart_quantity" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_before_add_to_cart_quantity', true ); ?>><?php esc_html_e( 'Before Add to Cart Quantity', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_after_add_to_cart_quantity" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_after_add_to_cart_quantity', true ); ?>><?php esc_html_e( 'After Add to Cart Quantity', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_after_single_variation" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_after_single_variation', true ); ?>><?php esc_html_e( 'After Single Variation', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_after_add_to_cart_button" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_after_add_to_cart_button', true ); ?>><?php esc_html_e( 'After Add to Cart Button', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_after_variations_form" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_after_variations_form', true ); ?>><?php esc_html_e( 'After Variations Form', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_after_add_to_cart_form" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_after_add_to_cart_form', true ); ?>><?php esc_html_e( 'After Add to Cart Form', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_product_meta_start" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_product_meta_start', true ); ?>><?php esc_html_e( 'Before Meta', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_product_meta_end" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_product_meta_end', true ); ?>><?php esc_html_e( 'After Meta', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_share" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_share', true ); ?>><?php esc_html_e( 'Within Share Area', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_after_single_product_summary" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_after_single_product_summary', true ); ?>><?php esc_html_e( 'After Single Product Summary', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="woocommerce_after_single_product" <?php selected( $dsrbfw_fbt_choose_locations, 'woocommerce_after_single_product', true ); ?>><?php esc_html_e( 'After Single Product', 'revenue-booster-for-woocommerce' ); ?></option>          
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- Frequently Bought Together Section End -->

            <div class="spacer-3"></div>

            <!-- after add to cart modal Section Start -->
            <div class="dsrbfw-after-add-to-cart-modal-section">
                <h2><?php esc_html_e( 'After Add to Cart Modal Settings', 'revenue-booster-for-woocommerce' ); ?><?php echo wp_kses( wc_help_tip( esc_html__( 'These are post-addition suggestions that appear in a modal after adding a product to the cart, boosting your shopping experience.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?></h2>
                <table class="form-table table-outer dsrbfw-rule-table dsrbfw-table-tooltip" >
                    <tr>
                        <th scope="row">
                            <label>
                                <?php echo esc_html__( 'Section Title', 'revenue-booster-for-woocommerce' ); ?>
                                <?php echo wp_kses( wc_help_tip( esc_html__( 'Title of add to cart modal can be customise from here.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" id="dsrbfw_acp_title" name="dsrbfw_acp_title" placeholder="<?php esc_attr_e( 'You should try this!', 'revenue-booster-for-woocommerce' ); ?>" value="<?php echo esc_attr($dsrbfw_acp_title); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label>
                                <?php echo esc_html__( 'Popup Title BG Color', 'revenue-booster-for-woocommerce' ); ?>
                                <?php echo wp_kses( wc_help_tip( esc_html__( 'Change background color of modal title section.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                            </label>
                        </th>
                        <td>
                            <div class="dsrbfw-color-wrap">
                                <input type="color" name="dsrbfw_acp_title_bg_color" value="<?php echo esc_attr($dsrbfw_acp_title_bg_color); ?>" />
                                <input type="text" name="dsrbfw_acp_title_bg_color" placeholder="#000000" value="<?php echo esc_attr($dsrbfw_acp_title_bg_color); ?>"/>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label>
                                <?php echo esc_html__( 'Popup Title Color', 'revenue-booster-for-woocommerce' ); ?>
                                <?php echo wp_kses( wc_help_tip( esc_html__( 'Change background color of modal title section.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                            </label>
                        </th>
                        <td>
                            <div class="dsrbfw-color-wrap">
                                <input type="color" name="dsrbfw_acp_title_text_color" value="<?php echo esc_attr($dsrbfw_acp_title_text_color); ?>" />
                                <input type="text" name="dsrbfw_acp_title_text_color" placeholder="#000000" value="<?php echo esc_attr($dsrbfw_acp_title_text_color); ?>"/>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- after add to cart modal Section End -->

            <div class="wcpoa-setting-btn wcpoa-general-submit">
                <?php submit_button(); ?>
            </div>
        </from>
    </div>
</div>
<?php
require_once( DSRBFW_PLUGIN_FOOTER_LINK );
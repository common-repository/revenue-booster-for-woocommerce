<?php

/**
 * Page which can provide add new order bump configuration at checkout page form
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.thedotstore.com/
 * @since      1.0.0
 *
 * @package    Revenue_Booster_For_Woocommerce
 * @subpackage Revenue_Booster_For_Woocommerce/admin/partials
 */


if ( ! defined( 'ABSPATH' ) ) { 
    exit;
}

require_once( DSRBFW_PLUGIN_HEADER_LINK );

$allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];
$dsrbfw_admin_object = new Revenue_Booster_For_Woocommerce_Admin( '', '' );

$dsrbfw_action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$ob_at_checkout_id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

if ( !empty( $dsrbfw_action ) && 'edit' === $dsrbfw_action && !empty( $ob_at_checkout_id ) ) {
    $btnValue = esc_html__( 'Update Rule', 'revenue-booster-for-woocommerce' );
    $dsrbfw_ob_ac_status = get_post_status( $ob_at_checkout_id );
    $dsrbfw_ob_ac_product = get_post_meta( $ob_at_checkout_id, 'dsrbfw_ob_ac_product', true );
    $dsrbfw_ob_ac_box_title = get_post_meta( $ob_at_checkout_id, 'dsrbfw_ob_ac_box_title', true );
    $dsrbfw_ob_ac_box_title = substr($dsrbfw_ob_ac_box_title, 0, DSRBFW_OB_AC_TITLE_LENGTH);
    $dsrbfw_ob_ac_box_description = get_post_meta( $ob_at_checkout_id, 'dsrbfw_ob_ac_box_description', true );
    $dsrbfw_ob_ac_box_title_color = get_post_meta( $ob_at_checkout_id, 'dsrbfw_ob_ac_box_title_color', true );
    $dsrbfw_ob_ac_box_title_bg_color = get_post_meta( $ob_at_checkout_id, 'dsrbfw_ob_ac_box_title_bg_color', true );
    $dsrbfw_ob_ac_box_border_color = get_post_meta( $ob_at_checkout_id, 'dsrbfw_ob_ac_box_border_color', true );
    $dsrbfw_ob_ac_box_border_style = get_post_meta( $ob_at_checkout_id, 'dsrbfw_ob_ac_box_border_style', true );
    $dsrbfw_ob_ac_show_product_img = get_post_meta( $ob_at_checkout_id, 'dsrbfw_ob_ac_show_product_img', true );
    $dsrbfw_ob_ac_show_box_shadow = get_post_meta( $ob_at_checkout_id, 'dsrbfw_ob_ac_show_box_shadow', true );
    $dsrbfw_ob_ac_price_color = get_post_meta( $ob_at_checkout_id, 'dsrbfw_ob_ac_price_color', true );
    $dsrbfw_ob_ac_conditions = get_post_meta( $ob_at_checkout_id, 'dsrbfw_ob_conditions', true );
} else {
    $btnValue = esc_html__( 'Add Rule', 'revenue-booster-for-woocommerce' );
    $dsrbfw_ob_ac_status = 'draft';
    $dsrbfw_ob_ac_product = 0;
    $dsrbfw_ob_ac_box_title = '';
    $dsrbfw_ob_ac_box_description = '';
    $dsrbfw_ob_ac_box_title_color = '#ffffff';
    $dsrbfw_ob_ac_box_title_bg_color = '#2b913c';
    $dsrbfw_ob_ac_box_border_color = '#2b913c';
    $dsrbfw_ob_ac_box_border_style = 'solid';
    $dsrbfw_ob_ac_show_product_img = 'yes';
    $dsrbfw_ob_ac_show_box_shadow = 'no';
    $dsrbfw_ob_ac_price_color = '#2b913c';
    $dsrbfw_ob_ac_conditions = array();
}
?>

<div class="dsrbfw-section-left">
	<div class="dsrbfw-main-table res-cl dsrbfw-add-rule-page">
        <form method="POST" name="dsrbfw_ob_ac_form" action="">
            <?php wp_nonce_field( 'dsrbfw_ob_ac_rule_save_action', 'dsrbfw_ob_ac_rule_save' ); ?>
			<input type="hidden" name="dsrbfw_post_type" value="<?php echo esc_attr(DSRBFW_BEFORE_OB_POST_TYPE); ?>">
			<input type="hidden" name="dsrbfw_post_id" value="<?php echo esc_attr( $ob_at_checkout_id ) ?>">

            <!-- Select product and discount on which offer will apply at checkout -->
            <div class="element-shadow">
                <h2><?php esc_html_e( 'Offer Details', 'revenue-booster-for-woocommerce' ); ?></h2>
                <table class="form-table table-outer dsrbfw-rule-table dsrbfw-table-tooltip">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label>
                                    <?php echo esc_html__( 'Offer Status', 'revenue-booster-for-woocommerce' ); ?>
                                    <?php echo wp_kses( wc_help_tip( esc_html__( 'Activate this offer at checkout order bump or not', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                                </label>
                            </th>
                            <td>
                                <label class="switch">
                                    <input type="hidden" name="dsrbfw_ob_ac_status" value="draft" />
                                    <input type="checkbox" id="dsrbfw_ob_ac_status" name="dsrbfw_ob_ac_status" value="publish" <?php checked( $dsrbfw_ob_ac_status, 'publish', true ); ?> />
                                    <div class="slider round"></div>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th class="titledesc" scope="row">
		                        <label for="dsrbfw_ob_ac_product">
		                        	<?php esc_html_e( 'Product', 'revenue-booster-for-woocommerce' ); ?>
                                    <span class="required">*</span>
		                        	<?php echo wp_kses( wc_help_tip( esc_html__( 'Select a product to offer during checkout if the conditions are met.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
	                        	</label>
							</th>
                            <td class="forminp">
                                <select
                                    class="ds-woo-search dsrbfw-required"
                                    id="dsrbfw_ob_ac_product"
                                    name="dsrbfw_ob_ac_product"
                                    data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'revenue-booster-for-woocommerce' ); ?>"
                                    data-sortable="true">
                                    <?php $product = wc_get_product( $dsrbfw_ob_ac_product );
                                    if ( $product ) : ?>
                                        <option value="<?php echo esc_attr($dsrbfw_ob_ac_product); ?>" selected="selected"><?php echo wp_kses_post( $product->get_formatted_name() ); ?></option>
                                    <?php endif; ?>
                                </select>
							</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="spacer-3"></div>

            <!-- Select filter on which order bump will show at checkout -->
            <div class="element-shadow">
                <div class="section-title">
                    <h2><?php esc_html_e( 'Offer Rules', 'revenue-booster-for-woocommerce' ); ?></h2>
                    <div class="tap">
                        <a id="dsrbfw-ob-add-filter" class="button" href="javascript:;"><?php esc_html_e( '+ Add Rule', 'revenue-booster-for-woocommerce' ); ?></a>
                        <?php echo wp_kses( wc_help_tip( esc_html__( 'This offer will activate when all rules are satisfy. otherwise it will show offer for every products.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                    </div>
                </div>
                <div class="tap">
                    <?php $i = 0; ?>
                    <table id="dsrbfw-ob-filter" class="table-outer">
                        <tbody>
                            <?php 
                            $empty_class = 'dsrbfw-no-filter-tr-show';
                            if ( isset( $dsrbfw_ob_ac_conditions ) && ! empty( $dsrbfw_ob_ac_conditions ) ) { 
                                foreach ( $dsrbfw_ob_ac_conditions as $condition_value ) {
                                    $dsrbfw_ob_condition = isset( $condition_value['dsrbfw_ob_condition'] ) ? $condition_value['dsrbfw_ob_condition'] : '';
                                    $dsrbfw_ob_is    = isset( $condition_value['dsrbfw_ob_is'] ) ? $condition_value['dsrbfw_ob_is'] : '';
                                    $dsrbfw_ob_values  = isset( $condition_value['dsrbfw_ob_values'] ) ? array_map( 'absint', $condition_value['dsrbfw_ob_values'] ) : array();
                                    ?>
                                    <tr id="row_<?php echo intval($i); ?>" valign="top">
										<td class="titledesc th_dsrbfw_ob_condition" scope="row">
											<select rel-id="<?php echo intval($i); ?>" id="dsrbfw_ob_condition_<?php echo intval($i); ?>" name="dsrbfw_ob[dsrbfw_ob_condition][]"  class="dsrbfw_ob_condition">
												<?php
												$condition_spe = $dsrbfw_admin_object->dsrbfw_conditions_list_action();
												foreach ( $condition_spe as $opt_key => $opt_value ) { ?>
                                                    <option value="<?php echo esc_attr( $opt_key ); ?>" <?php selected( $dsrbfw_ob_condition, $opt_key, true ); ?>><?php echo esc_html( $opt_value ); ?></option>
													<?php
                                                } ?>
											</select>
										</td>
										<td class="select_condition_for_in_notin">
                                            <?php $opr_spe = $dsrbfw_admin_object->dsrbfw_operator_list_action(); ?>
											<select name="dsrbfw_ob[dsrbfw_ob_is][]" class="dsrbfw_ob_is_<?php echo intval($i); ?>">
                                                <?php foreach ( $opr_spe as $opr_key => $opr_value ) { ?>
                                                    <option value="<?php echo esc_attr( $opr_key ); ?>" <?php selected( $dsrbfw_ob_is, $opt_key, true ); ?>><?php echo esc_html( $opr_value ); ?></option>
                                                <?php } ?>
											</select>
										</td>
										<td id="column_<?php echo intval($i); ?>" class="condition-value">
                                            <?php 
                                            if( "product" === $dsrbfw_ob_condition ) {
                                                echo wp_kses( $dsrbfw_admin_object->dsrbfw_get_product_list( $i, $dsrbfw_ob_values ), dsrbfw()->dsrbfw_allowed_html_tags() );
                                            } else if( "category" === $dsrbfw_ob_condition ) {
                                                echo wp_kses( $dsrbfw_admin_object->dsrbfw_get_category_list( $i, $dsrbfw_ob_values ), dsrbfw()->dsrbfw_allowed_html_tags() );
                                            } ?>
										</td>
                                        <td>
                                            <a rel-id="<?php echo intval($i); ?>" class="dsrbfw-delete-filter" href="javascript:void(0);" title="Delete">
                                                <i class="dashicons dashicons-trash"></i>
                                            </a>
                                        </td>
									</tr>
                                    <?php
                                    $i++;
                                    $empty_class = 'dsrbfw-no-filter-tr-hide';
                                }
                            } ?>
                            <tr class="dsrbfw-no-filter-tr <?php echo esc_attr($empty_class); ?>" >
                                <td colspan="4">
                                    <span class="dsrbfw-no-filter-text"><?php esc_html_e( 'This offer will apply on all products.','revenue-booster-for-woocommerce' ); ?></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="total_row" id="total_row" value="<?php echo esc_attr( $i ); ?>">
                </div>
            </div>
            <div class="spacer-3"></div>

            <!-- Design customization for order bump at checkout -->
            <div class="element-shadow">
                <h2><?php esc_html_e( 'Order Bump Offer Design', 'revenue-booster-for-woocommerce' ); ?></h2>
                <table class="form-table table-outer dsrbfw-rule-table dsrbfw-table-tooltip">
                    <tbody>
                        <tr>
                            <th class="titledesc" scope="row">
		                        <label for="dsrbfw_ob_ac_box_title">
		                        	<?php esc_html_e( 'Title', 'revenue-booster-for-woocommerce' ); ?>
                                    <span class="required">*</span>
                                    <?php /* translators: %d is replaced with "number" which show title length globally declared on root file */ ?>
		                        	<?php echo wp_kses( wc_help_tip( sprintf( esc_html__( 'Customize order bump box title from here. (Max.: %d Chars.)', 'revenue-booster-for-woocommerce' ), DSRBFW_OB_AC_TITLE_LENGTH) ), array( 'span' => $allowed_tooltip_html ) ); ?>
	                        	</label>
							</th>
                            <td class="forminp">
                                <input type="text" class="dsrbfw-required" id="dsrbfw_ob_ac_box_title" name="dsrbfw_ob_ac_box_title" placeholder="<?php esc_html_e( 'Yes! I want to add this offer to my order', 'revenue-booster-for-woocommerce' ); ?>" value="<?php echo esc_attr($dsrbfw_ob_ac_box_title); ?>" maxlength="<?php echo esc_attr(DSRBFW_OB_AC_TITLE_LENGTH); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th class="titledesc" scope="row">
		                        <label for="dsrbfw_ob_ac_box_description">
		                        	<?php esc_html_e( 'Description', 'revenue-booster-for-woocommerce' ); ?>
                                    <span class="required">*</span>
		                        	<?php echo wp_kses( wc_help_tip( esc_html__( 'Customize order bump box title from here.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
	                        	</label>
							</th>
                            <td class="forminp">
                                <textarea class="dsrbfw-required" rows="4" id="dsrbfw_ob_ac_box_description" name="dsrbfw_ob_ac_box_description" placeholder="<?php esc_html_e( 'Enter additional content for order bump', 'revenue-booster-for-woocommerce' ); ?>" ><?php echo esc_html($dsrbfw_ob_ac_box_description); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label>
                                    <?php echo esc_html__( 'Show Product Image', 'revenue-booster-for-woocommerce' ); ?>
                                    <?php echo wp_kses( wc_help_tip( esc_html__( 'Either we need to show product image in order bump or not', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                                </label>
                            </th>
                            <td>
                                <label class="switch">
                                    <input type="hidden" name="dsrbfw_ob_ac_show_product_img" value="no" />
                                    <input type="checkbox" id="dsrbfw_ob_ac_show_product_img" name="dsrbfw_ob_ac_show_product_img" value="yes" <?php checked( $dsrbfw_ob_ac_show_product_img, 'yes', true ); ?> />
                                    <div class="slider round"></div>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th class="titledesc" scope="row">
		                        <label for="dsrbfw_ob_ac_box_title_color">
		                        	<?php esc_html_e( 'Title color', 'revenue-booster-for-woocommerce' ); ?>
		                        	<?php echo wp_kses( wc_help_tip( esc_html__( 'Customize order bump box title from here.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
	                        	</label>
							</th>
                            <td class="forminp">
                                <div class="dsrbfw-color-wrap">
                                    <input type="color" name="dsrbfw_ob_ac_box_title_color" value="<?php echo esc_attr($dsrbfw_ob_ac_box_title_color); ?>" />
                                    <input type="text" name="dsrbfw_ob_ac_box_title_color" placeholder="#ffffff" value="<?php echo esc_attr($dsrbfw_ob_ac_box_title_color); ?>"/>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="titledesc" scope="row">
		                        <label for="dsrbfw_ob_ac_box_title_bg_color">
		                        	<?php esc_html_e( 'Title Background', 'revenue-booster-for-woocommerce' ); ?>
		                        	<?php echo wp_kses( wc_help_tip( esc_html__( 'Customize order bump box title background color from here.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
	                        	</label>
							</th>
                            <td class="forminp">
                                <div class="dsrbfw-color-wrap">
                                    <input type="color" name="dsrbfw_ob_ac_box_title_bg_color" value="<?php echo esc_attr($dsrbfw_ob_ac_box_title_bg_color); ?>" />
                                    <input type="text" name="dsrbfw_ob_ac_box_title_bg_color" placeholder="#2b913c" value="<?php echo esc_attr($dsrbfw_ob_ac_box_title_bg_color); ?>"/>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="titledesc" scope="row">
		                        <label for="dsrbfw_ob_ac_box_border_color">
		                        	<?php esc_html_e( 'Border Color', 'revenue-booster-for-woocommerce' ); ?>
		                        	<?php echo wp_kses( wc_help_tip( esc_html__( 'Customize order bump box title from here.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
	                        	</label>
							</th>
                            <td class="forminp">
                                <div class="dsrbfw-color-wrap">
                                    <input type="color" name="dsrbfw_ob_ac_box_border_color" value="<?php echo esc_attr($dsrbfw_ob_ac_box_border_color); ?>" />
                                    <input type="text" name="dsrbfw_ob_ac_box_border_color" placeholder="#2b913c" value="<?php echo esc_attr($dsrbfw_ob_ac_box_border_color); ?>"/>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="titledesc" scope="row">
		                        <label for="dsrbfw_ob_ac_box_border_style">
		                        	<?php esc_html_e( 'Border Style', 'revenue-booster-for-woocommerce' ); ?>
		                        	<?php echo wp_kses( wc_help_tip( esc_html__( 'Customize order bump box title from here.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
	                        	</label>
							</th>
                            <td class="forminp">
                            <select class="dsrbfw-choose-locations" name="dsrbfw_ob_ac_box_border_style">
                                <option value="solid" <?php selected( $dsrbfw_ob_ac_box_border_style, 'solid', true ); ?>><?php esc_html_e( 'Solid', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="dashed" <?php selected( $dsrbfw_ob_ac_box_border_style, 'dashed', true ); ?>><?php esc_html_e( 'Dashed', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="dotted" <?php selected( $dsrbfw_ob_ac_box_border_style, 'dotted', true ); ?>><?php esc_html_e( 'Dotted', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="double" <?php selected( $dsrbfw_ob_ac_box_border_style, 'double', true ); ?>><?php esc_html_e( 'Double', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="inset" <?php selected( $dsrbfw_ob_ac_box_border_style, 'inset', true ); ?>><?php esc_html_e( 'Inset', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="outset" <?php selected( $dsrbfw_ob_ac_box_border_style, 'outset', true ); ?>><?php esc_html_e( 'Outset', 'revenue-booster-for-woocommerce' ); ?></option>
                                <option value="ridge" <?php selected( $dsrbfw_ob_ac_box_border_style, 'ridge', true ); ?>><?php esc_html_e( 'Ridge', 'revenue-booster-for-woocommerce' ); ?></option>
                            </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label>
                                    <?php echo esc_html__( 'Show Box Shadow', 'revenue-booster-for-woocommerce' ); ?>
                                    <?php echo wp_kses( wc_help_tip( esc_html__( 'It will add shadow effect in order bump', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                                </label>
                            </th>
                            <td>
                                <label class="switch">
                                    <input type="hidden" name="dsrbfw_ob_ac_show_box_shadow" value="no" />
                                    <input type="checkbox" id="dsrbfw_ob_ac_show_box_shadow" name="dsrbfw_ob_ac_show_box_shadow" value="yes" <?php checked( $dsrbfw_ob_ac_show_box_shadow, 'yes', true ); ?> />
                                    <div class="slider round"></div>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th class="titledesc" scope="row">
		                        <label for="dsrbfw_ob_ac_price_color">
		                        	<?php esc_html_e( 'Price Color', 'revenue-booster-for-woocommerce' ); ?>
		                        	<?php echo wp_kses( wc_help_tip( esc_html__( 'Customize order bump box title from here.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
	                        	</label>
							</th>
                            <td class="forminp">
                                <div class="dsrbfw-color-wrap">
                                    <input type="color" name="dsrbfw_ob_ac_price_color" value="<?php echo esc_attr($dsrbfw_ob_ac_price_color); ?>" />
                                    <input type="text" name="dsrbfw_ob_ac_price_color" placeholder="#2b913c" value="<?php echo esc_attr($dsrbfw_ob_ac_price_color); ?>"/>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="submit">
                <input type="submit" name="submitRule" class="button button-primary" value="<?php echo esc_attr( $btnValue ); ?>">
            </p>
        </form>
    </div>
</div>

<?php
require_once( DSRBFW_PLUGIN_FOOTER_LINK );
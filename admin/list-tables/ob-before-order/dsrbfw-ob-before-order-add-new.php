<?php

/**
 * Page which can provide add new order bump configuration before order form
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
$ob_before_order_id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
if ( !empty( $dsrbfw_action ) && 'edit' === $dsrbfw_action && !empty( $ob_before_order_id ) ) {
    $btnValue = esc_html__( 'Update Rule', 'revenue-booster-for-woocommerce' );
    $dsrbfw_ob_bo_status = get_post_status( $ob_before_order_id );
    $dsrbfw_ob_bo_product = get_post_meta( $ob_before_order_id, 'dsrbfw_ob_bo_product', true );
    $dsrbfw_ob_conditions = get_post_meta( $ob_before_order_id, 'dsrbfw_ob_conditions', true );
} else {
    $btnValue = esc_html__( 'Add Rule', 'revenue-booster-for-woocommerce' );
    $dsrbfw_ob_bo_status = 'draft';
    $dsrbfw_ob_bo_product = 0;
    $dsrbfw_ob_conditions = array();
}
?>

<div class="dsrbfw-section-left">
	<div class="dsrbfw-main-table res-cl dsrbfw-add-rule-page">
        <form method="POST" name="dsrbfw_ob_bo_form" action="">
            <?php wp_nonce_field( 'dsrbfw_ob_bo_rule_save_action', 'dsrbfw_ob_bo_rule_save' ); ?>
			<input type="hidden" name="dsrbfw_post_type" value="<?php echo esc_attr(DSRBFW_AFTER_OB_POST_TYPE); ?>">
			<input type="hidden" name="dsrbfw_post_id" value="<?php echo esc_attr( $ob_before_order_id ) ?>">

            <!-- Select product and discount on which offer will apply at checkout -->
            <div class="element-shadow">
                <h2><?php esc_html_e( 'Create Offer', 'revenue-booster-for-woocommerce' ); ?></h2>
                <table class="form-table table-outer dsrbfw-rule-table dsrbfw-table-tooltip">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label>
                                    <?php echo esc_html__( 'Offer Status', 'revenue-booster-for-woocommerce' ); ?>
                                    <?php echo wp_kses( wc_help_tip( esc_html__( 'Activate this offer before order\'s order bump or not', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                                </label>
                            </th>
                            <td>
                                <label class="switch">
                                    <input type="hidden" name="dsrbfw_ob_bo_status" value="draft" />
                                    <input type="checkbox" id="dsrbfw_ob_bo_status" name="dsrbfw_ob_bo_status" value="publish" <?php checked( $dsrbfw_ob_bo_status, 'publish', true ); ?> />
                                    <div class="slider round"></div>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th class="titledesc" scope="row">
		                        <label for="dsrbfw_ob_bo_product">
		                        	<?php esc_html_e( 'Product', 'revenue-booster-for-woocommerce' ); ?>
                                    <span class="required">*</span>
		                        	<?php echo wp_kses( wc_help_tip( esc_html__( 'Select a product to offer during checkout if the conditions are met.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
	                        	</label>
							</th>
                            <td class="forminp">
                                <select
                                    class="ds-woo-search dsrbfw-required"
                                    id="dsrbfw_ob_bo_product"
                                    name="dsrbfw_ob_bo_product"
                                    data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'revenue-booster-for-woocommerce' ); ?>"
                                    data-sortable="true">
                                    <?php $product = wc_get_product( $dsrbfw_ob_bo_product );
                                    if ( $product ) : ?>
                                        <option value="<?php echo esc_attr($dsrbfw_ob_bo_product); ?>" selected="selected"><?php echo wp_kses_post( $product->get_formatted_name() ); ?></option>
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
                            if ( isset( $dsrbfw_ob_conditions ) && ! empty( $dsrbfw_ob_conditions ) ) { 
                                foreach ( $dsrbfw_ob_conditions as $condition_value ) {
                                    $dsrbfw_ob_condition = isset( $condition_value['dsrbfw_ob_condition'] ) ? $condition_value['dsrbfw_ob_condition'] : '';
                                    $dsrbfw_ob_is    = isset( $condition_value['dsrbfw_ob_is'] ) ? $condition_value['dsrbfw_ob_is'] : '';
                                    $dsrbfw_ob_values  = isset( $condition_value['dsrbfw_ob_values'] ) ? $condition_value['dsrbfw_ob_values'] : array();
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

            <p class="submit">
                <input type="submit" name="submitRule" class="button button-primary" value="<?php echo esc_attr( $btnValue ); ?>">
            </p>
        </form>
    </div>
</div>

<?php
require_once( DSRBFW_PLUGIN_FOOTER_LINK );
<?php

/**
 * Order Bump Before Order HTML content
 *
 * @link       https://thedotstore.com/
 * @since      1.0.0
 *
 * @package    Revenue_Booster_For_Woocommerce
 * @subpackage Revenue_Booster_For_Woocommerce/public/partials
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="dsrbfw_modal hidden">
    <div class="dsrbfw_modal_content">
        <div class="dsrbfw_ob_bo_main">
            <div class="dsrbfw_ob_bo_main_inner">
                <div class="dsrbfw_ob_bo_main_inner_wrap">
                    <ol class="steps">
                        <li class="step is-complete" data-step="1"><?php esc_html_e( 'Checkout', 'revenue-booster-for-woocommerce' ); ?></li>
                        <li class="step is-active" data-step="2"><?php esc_html_e( 'Offers', 'revenue-booster-for-woocommerce' ); ?></li>
                        <li class="step" data-step="3"><?php esc_html_e( 'Place Order', 'revenue-booster-for-woocommerce' ); ?></li>
                    </ol>
                    <h2 class="dsrbfw_ob_bo_title"><?php esc_html_e( 'Wait! Your order is almost complete...', 'revenue-booster-for-woocommerce' ); ?></h2>
                    <p class="dsrbfw_ob_bo_description">
                        <?php echo sprintf( _n( 'Add this top pick to your basket before you check out!', 'Add these %d top picks to your basket before you check out!', number_format_i18n( count( $bump_ids ) ), 'revenue-booster-for-woocommerce' ), number_format_i18n( count( $bump_ids ) ) ); // phpcs:ignore ?>
                    </p>
                    <div class="dsrbfw_ob_bo_body">
                        <?php foreach( $bump_ids as $bump_id ) { // phpcs:ignore
                            $product_id = absint( get_post_meta( $bump_id, 'dsrbfw_ob_bo_product', true ) );
                            $product = wc_get_product($product_id);
                            
                            if ( $product->get_image_id() > 0 ) {
                                $image = wp_get_attachment_image_url( $product->get_image_id() );
                            }
                            $image = $image ? $image : wc_placeholder_img_src();
                            $variation_data = array();

                            // Check if product is a variation with all attributes are set
                            $complete_variation = true;
                            if( $product->is_type('variable') || $product->is_type('variation') ) {
                                if( !empty( $product->get_attributes() ) ) {
                                    foreach( $product->get_attributes() as $element ){
                                        if ( $element instanceof WC_Product_Attribute || empty( $element ) ) {
                                            $complete_variation = false;
                                            break;
                                        }
                                    }
                                }
                            }
                            ?>
                        <div class="dsrbfw_ob_bo_product_main">
                            <div class="dsrbfw_ob_bo_product_data">
                                <input type="checkbox" class="dsrbfw_ob_bo_checkbox" value="<?php echo esc_attr( $bump_id ); ?>" <?php disabled( true, ! $complete_variation ); ?> />
                            </div>
                            <div class="dsrbfw_ob_bo_product_aside">
                                <div class="dsrbfw_ob_bo_product_image">
                                    <img class="dsrbfw_ob_bo_product_img" src="<?php echo esc_url( $image ); ?>" />
                                </div>
                            </div>
                            <div class="dsrbfw_ob_bo_product_content">
                                <h4 class="dsrbfw_ob_bo_product_title"><?php echo esc_html($product->get_name()); ?></h4>
                                <div class="dsrbfw_ob_bo_product_price">
                                    <span class="dsrbfw_ob_bo_product_price_html"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
                                </div>
                                <?php if ( $product->is_type( 'variable' ) || $product->is_type( 'variation' ) ) : ?>
                                    <div class="dsrbfw_ob_bo_product_variable">
                                        <?php
                                        $variable_product = $product->is_type( 'variable' ) ? $product : wc_get_product( $product->get_parent_id() );
                                        $attributes = $variable_product->get_variation_attributes();
                                        
                                        if( !empty( $attributes ) ) { ?>
                                            <table class="dsrbfw_ob_bo__variation" data-variable-id="<?php echo esc_attr( $variable_product->get_id() ); ?>" data-bump-id="<?php echo esc_attr( $bump_id ); ?>">
                                                <?php
                                                foreach ( $attributes as $attribute_name => $options ) : ?>
                                                    <?php $attribute_name_sanitized = 'attribute_' . sanitize_title( $attribute_name ); ?>
                                                    <tr>
                                                        <th class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?></label></th>
                                                        <td class="value">
                                                            <?php
                                                                $selected = false; 
                                                                if ( $variation_data && ( $product->is_type( 'variation' ) || $product->is_type( 'variable' ) ) ) {
                                                                    $selected = isset( $variation_data[ $attribute_name_sanitized ] ) ? $variation_data[ $attribute_name_sanitized ] : false;
                                                                } 

                                                                $args = array(
                                                                    'options'   => $options,
                                                                    'attribute' => $attribute_name,
                                                                    'product'   => $variable_product,
                                                                    'class'     => 'dsrbfw_ob_bo__variation-select dsrbfw-ob_ac-product-variation-' . $bump_id,
                                                                    'id'        => sanitize_title($attribute_name) . '-' . $bump_id,
                                                                    'selected'  => $selected,
                                                                );
                                                                if( $product->is_type( 'variable' ) ) {
                                                                    wc_dropdown_variation_attribute_options( $args );
                                                                } else if( $product->is_type( 'variation' ) ) {
                                                                    $attribute_value = $product->get_attribute( $attribute_name );
                                                                    if ( $attribute_value !== '' ) { ?>
                                                                        <span class='dsrbfw_ob_bo-variation__select_replace_label'><?php echo esc_html( $attribute_value ); ?></span>
                                                                        <input type="hidden" class="dsrbfw_ob_bo__variation-select dsrbfw-ob_ac-product-variation-<?php echo esc_attr( $bump_id ); ?>" data-attribute_name="<?php echo esc_attr( $attribute_name_sanitized ); ?>" value="<?php echo isset( $product->get_attributes()[sanitize_title($attribute_name)] ) ? esc_attr( $product->get_attributes()[sanitize_title($attribute_name)] ) : ''; ?>" />
                                                                        <?php
                                                                    } else {
                                                                        wc_dropdown_variation_attribute_options( $args );
                                                                    }
                                                                }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </table>
                                            <?php
                                        } ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="dsrbfw_ob_bo_final_button">
                        <button class="dsrbfw_ob_bo_buy button wp-element-button wc-block-components-checkout-place-order-button" disabled>
                            <?php esc_html_e( 'Add to Order', 'revenue-booster-for-woocommerce'); ?>
                        </button>
                        <a class="dsrbfw_ob_bo_skip" href="javascript:void(0);">
                            <?php esc_html_e( 'No, I am ok with it.', 'revenue-booster-for-woocommerce'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="dsrbfw_ob_bo_overlay"></div>
    </div>
</div>
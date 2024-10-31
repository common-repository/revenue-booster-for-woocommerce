<?php

/**
 * Order Bump at Checkout HTML content
 *
 * @link       https://thedotstore.com/
 * @since      1.0.0
 *
 * @package    Revenue_Booster_For_Woocommerce
 * @subpackage Revenue_Booster_For_Woocommerce/public/partials
 */

defined( 'ABSPATH' ) || exit;

//We are doing this because skip PHPCS not defined error as this vcriable comes from template extract function
$dsrbfw_offer_id = $dsrbfw_offer_id;
$rules_in_cart = $rules_in_cart;
$variation_data = $variation_data;

$dsrbfw_ob_ac_box_title = get_post_meta( $dsrbfw_offer_id, 'dsrbfw_ob_ac_box_title', true );
$dsrbfw_ob_ac_box_description = get_post_meta( $dsrbfw_offer_id, 'dsrbfw_ob_ac_box_description', true );

$dsrbfw_ob_ac_box_title_color = get_post_meta( $dsrbfw_offer_id, 'dsrbfw_ob_ac_box_title_color', true ) ?: '#ffffff';
$dsrbfw_ob_ac_box_title_bg_color = get_post_meta( $dsrbfw_offer_id, 'dsrbfw_ob_ac_box_title_bg_color', true ) ?: '#27ae60';

$dsrbfw_ob_ac_box_border_color = get_post_meta( $dsrbfw_offer_id, 'dsrbfw_ob_ac_box_border_color', true );
$dsrbfw_ob_ac_box_border_style = get_post_meta( $dsrbfw_offer_id, 'dsrbfw_ob_ac_box_border_style', true ) ?: 'solid';

$dsrbfw_ob_ac_show_product_img = get_post_meta( $dsrbfw_offer_id, 'dsrbfw_ob_ac_show_product_img', true );
$dsrbfw_ob_ac_show_box_shadow = get_post_meta( $dsrbfw_offer_id, 'dsrbfw_ob_ac_show_box_shadow', true );

$dsrbfw_ob_ac_price_color = get_post_meta( $dsrbfw_offer_id, 'dsrbfw_ob_ac_price_color', true );

// For main box border style
$dsrbfw_main_checkout_bump = array(
	"border-style: {$dsrbfw_ob_ac_box_border_style}",
    "border-color: {$dsrbfw_ob_ac_box_border_color}",
);

$dsrbfw_ob_ac_box_shadow_color =dsrbfw()->dsrbfw_hex_to_rgb_convert($dsrbfw_ob_ac_box_border_color);

// Change border width by hook
$dsrbfw_ob_ac_border_width = apply_filters( 'dsrbfw_ob_ac_border_width', 1, $dsrbfw_offer_id );

if( 'yes' === $dsrbfw_ob_ac_show_box_shadow ) {
    $dsrbfw_main_checkout_bump[] = "-webkit-box-shadow: 0 4px 10px rgba({$dsrbfw_ob_ac_box_shadow_color}, 0.4)";
    $dsrbfw_main_checkout_bump[] = "box-shadow: 0 4px 10px rgba({$dsrbfw_ob_ac_box_shadow_color}, 0.4)";
} else {
    $dsrbfw_main_checkout_bump[] = 'box-shadow: none';
}
$dsrbfw_main_checkout_bump[] = "border-width: {$dsrbfw_ob_ac_border_width}px";

$dsrbfw_main_checkout_bump = join( ';', array_filter( $dsrbfw_main_checkout_bump ) );

// For footer top border style
$dsrbfw_ob_ac_border_style = array(
	"border-top-style: {$dsrbfw_ob_ac_box_border_style}",
    "border-color: {$dsrbfw_ob_ac_box_border_color}",
);
$dsrbfw_ob_ac_border_style = join( ';', array_filter( $dsrbfw_ob_ac_border_style ) );

$product_id = absint( get_post_meta( $dsrbfw_offer_id, 'dsrbfw_ob_ac_product', true ) );
$product = wc_get_product($product_id);

if ( $product->get_image_id() > 0 ) {
    $image = wp_get_attachment_image_url( $product->get_image_id() );
}
$image = $image ? $image : wc_placeholder_img_src();

$product_price_obj = isset( $rules_in_cart[$dsrbfw_offer_id] ) ? wc_get_product( $rules_in_cart[$dsrbfw_offer_id] ) : $product;
?>
<div class="dsrbfw_ob_ac_main" style="<?php echo esc_attr( $dsrbfw_main_checkout_bump ); ?>">
    <div class="dsrbfw_ob_ac_body">
        <?php if ( 'yes' === $dsrbfw_ob_ac_show_product_img ) : ?>
			<div class="dsrbfw_ob_ac_product_aside">
				<div class="dsrbfw_ob_ac_product_image">
					<img class="dsrbfw_ob_ac_product_img" src="<?php echo esc_url( $image ); ?>" />
				</div>
			</div>
		<?php endif; ?>
        <div class="dsrbfw_ob_ac_product_main">
			<h4 class="dsrbfw_ob_ac_product_title"><?php echo esc_html($product->get_name()); ?></h4>
			<div class="dsrbfw_ob_ac_description"><?php echo wp_kses_post( $dsrbfw_ob_ac_box_description ); ?></div>
            <div class="dsrbfw_ob_ac_product_price">
                <span class='dsrbfw_ob_ac_product_price_span' style="color: <?php echo esc_attr( $dsrbfw_ob_ac_price_color ); ?>">
                    <?php esc_html_e( 'Price:', 'revenue-booster-for-woocommerce' ); ?> 
                    <span class="dsrbfw_ob_ac_product_price_html"><?php echo wp_kses_post( $product_price_obj->get_price_html() ); ?></span>
                </span>
            </div>
            <?php if ( $product->is_type( 'variable' ) || $product->is_type( 'variation' ) ) : ?>
                <div class="dsrbfw_ob_ac_product_variable">
                    <?php
                    $variable_product = $product->is_type( 'variable' ) ? $product : wc_get_product( $product->get_parent_id() );
                    $attributes = $variable_product->get_variation_attributes();
                    
                    $complete_variation = true;
                    if( !empty( $product->get_attributes() ) ) {
                        foreach( $product->get_attributes() as $element ){
                            if ( $element instanceof WC_Product_Attribute || empty( $element ) ) {
                                $complete_variation = false;
                                break;
                            }
                        }
                    }
                    
                    if( !empty( $attributes ) ) { ?>
                        <table class="dsrbfw_ob_ac__variation" data-variable-id="<?php echo esc_attr( $variable_product->get_id() ); ?>">
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
                                                'class'     => 'dsrbfw_ob_ac__variation-select dsrbfw-ob_ac-product-variation-' . $dsrbfw_offer_id,
                                                'id'        => sanitize_title($attribute_name) . '-' . $dsrbfw_offer_id,
                                                'selected'  => $selected,
                                            );
                                            if( $product->is_type( 'variable' ) ) {
                                                wc_dropdown_variation_attribute_options( $args );
                                            } else if( $product->is_type( 'variation' ) ) {
                                                $attribute_value = $product->get_attribute( $attribute_name );
                                                if ( $attribute_value !== '' ) { ?>
                                                    <span class='dsrbfw_ob_ac-variation__select_replace_label'><?php echo esc_html( $attribute_value ); ?></span>
                                                    <input type="hidden" class="dsrbfw_ob_ac__variation-select dsrbfw-ob_ac-product-variation-<?php echo esc_attr( $dsrbfw_offer_id ); ?>" data-attribute_name="<?php echo esc_attr( $attribute_name_sanitized ); ?>" value="<?php echo isset( $product->get_attributes()[sanitize_title($attribute_name)] ) ? esc_attr( $product->get_attributes()[sanitize_title($attribute_name)] ) : ''; ?>" />
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
                    <div class="dsrbfw_ob_ac_product_overlay" ></div>
                </div>
            <?php endif; ?>
		</div>
    </div>
    <?php 
        // Check product and bump data initailly
        $checked = false;
        $disabled = false;
        
        if( array_key_exists( $dsrbfw_offer_id, $rules_in_cart ) ) {
            
            $cart_product_id = $rules_in_cart[$dsrbfw_offer_id];
            $cart_product = wc_get_product($cart_product_id);

            // If backend offer product is variable and cart product is variation in that case we need to get parent id
            $cart_final_id = $product->is_type('variable') && $cart_product->is_type('variation') ? $cart_product->get_parent_id() : $cart_product_id;

            $checked = $disabled = $cart_final_id === $product_id;
        }
    ?>
    <div class="dsrbfw_ob_ac_footer" style="background-color: <?php echo esc_attr( $dsrbfw_ob_ac_box_title_bg_color ); ?>;<?php echo esc_attr( $dsrbfw_ob_ac_border_style ); ?>">
        <label class="dsrbfw_ob_ac_footer_title" style="color: <?php echo esc_attr( $dsrbfw_ob_ac_box_title_color ); ?>;">    
            <input 
                class="dsrbfw_ob_ac_footer-checkbox"
                type="checkbox"
                id="dsrbfw-checkout-bump-trigger-<?php echo esc_attr( $dsrbfw_offer_id ); ?>"
                value="<?php echo esc_attr( $dsrbfw_offer_id ); ?>"
                <?php checked( true, $checked ); ?>
                <?php disabled( true, 'variable' === $product->get_type() && $disabled ); ?>
            >
			<?php echo esc_html( $dsrbfw_ob_ac_box_title ); ?>
		</label>
		<input type="hidden" name="dsrbfw_ob_ac-bump-data[<?php echo esc_attr( $dsrbfw_offer_id ); ?>][dsrbfw_ob_ac-bump-action]" value="">
		<input type="hidden" name="dsrbfw_ob_ac-bump-data[<?php echo esc_attr( $dsrbfw_offer_id ); ?>][dsrbfw_ob_ac-product-id]" class="dsrbfw_ob_ac_product_id" value='<?php echo esc_attr( $product_id ); ?>'>
		<input type="hidden" name="dsrbfw_ob_ac-bump-data[<?php echo esc_attr( $dsrbfw_offer_id ); ?>][dsrbfw_ob_ac-variation-id]" class="dsrbfw_ob_ac_variation_id" value="<?php echo esc_attr( $product->is_type( 'variation' ) ? $product->get_id() : $product->get_parent_id() ); ?>">
		<input type="hidden" name="dsrbfw_ob_ac-bump-data[<?php echo esc_attr( $dsrbfw_offer_id ); ?>][dsrbfw_ob_ac-variation-data]" class="dsrbfw_ob_ac_variation_data" value="<?php echo isset( $complete_variation ) ? esc_attr( wp_json_encode( $product->get_attributes() ) ) : esc_attr( wp_json_encode( $variation_data ) ); ?>">
    </div>
</div>

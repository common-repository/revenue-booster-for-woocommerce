<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://thedotstore.com/
 * @since      1.0.0
 *
 * @package    Revenue_Booster_For_Woocommerce
 * @subpackage Revenue_Booster_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Revenue_Booster_For_Woocommerce
 * @subpackage Revenue_Booster_For_Woocommerce/public
 * @author     theDotstore <support@thedotstore.com>
 */
class Revenue_Booster_For_Woocommerce_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    /**
	 * This is Order Bump at Checkout meta key which can use to differentiate the order bump product.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $ob_ac_cart_meta_key    Order bump at checkout meta key.
	 */
    private $ob_ac_cart_meta_key = 'dsrbfw_ob_ac_offer';

    /**
	 * This is Order Bump Before Order meta key which can use to differentiate the order bump product.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $ob_bo_cart_meta_key    Order bump before order meta key.
	 */
    private $ob_bo_cart_meta_key = 'dsrbfw_ob_bo_offer';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Revenue_Booster_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Revenue_Booster_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/revenue-booster-for-woocommerce-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name .'-responsive', plugin_dir_url( __FILE__ ) . 'css/revenue-booster-for-woocommerce-public-responsive.css', array(), $this->version, 'all' );

        if( !is_admin() ) {

            add_action( $this->dsrbfw_single_product_fbt_render_hook(), array( $this, 'dsrbfw_fbt_order_bump' ) );
        }
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Revenue_Booster_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Revenue_Booster_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/revenue-booster-for-woocommerce-public.js', array( 'jquery', 'wc-add-to-cart', 'wp-data' ), $this->version, true );
        wp_localize_script( $this->plugin_name, 'dsrbfw_front_vars', array( 
            'debug_mode'                        => defined( 'DSRBFW__DEV_MODE' ) && DSRBFW__DEV_MODE,
            'ajaxurl'                           => admin_url( 'admin-ajax.php' ),
            'fbt_ajax_enable'                   => get_option( 'dsrbfw_fbt_ajax_cart_enabled', 'no' ),
            'fbt_cart_exist_enabled'            => get_option( 'dsrbfw_fbt_cart_exist_enabled', 'no'),
            'dsrbfw_variation_price_nonce'      => wp_create_nonce( 'dsrbfw-variation-price' ),
            'dsrbfw_update_final_price_nonce'   => wp_create_nonce( 'dsrbfw-update-final-price' ),
            'dsrbfw_fbt_add_to_cart_nonce'      => wp_create_nonce( 'dsrbfw-fbt-add-to-cart' ),
            'dsrbfw_unavailable_text'           => esc_html__( 'Sorry, this product is unavailable. Please choose a different combination.', 'revenue-booster-for-woocommerce' ),
            'disabled_add_to_cart'              => esc_html__( 'Please select a variation before adding the selected products to your cart.', 'revenue-booster-for-woocommerce' ),
            'dsrbfw_addon_prefix_text'          => esc_html__( 'Addon', 'revenue-booster-for-woocommerce' ),
        )
    );
	}

    /**
	 * Return hook which render bump section on frontend
	 *
	 * @return string
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_single_product_fbt_render_hook() {

		$hook = get_option( 'dsrbfw_fbt_choose_locations', 'woocommerce_after_add_to_cart_form' );

		return apply_filters( 'dsrbfw_single_product_fbt_render_hook', $hook );
	}

    /**
	 * Display FBT products on single product page
	 *
     * @since 1.0.0
	 */
    public function dsrbfw_fbt_callback() {
        add_action( $this->dsrbfw_single_product_fbt_render_hook(), array( $this, 'dsrbfw_fbt_order_bump' ) );
    }

    /**
	 * Render bump products section on frontend
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_fbt_order_bump( $product_id = null ) {
        
        global $product;

        $dsrbfw_main_product = true;

		// If the product ID has been passed through via a shortcode.
		if ( $product_id ) {
			$product             = wc_get_product( $product_id );
			$dsrbfw_main_product = false;
		}

        if( $product instanceof WC_Product ) {

            // Get all FBT products for current product
            $fbt_ids = $this->dsrbfw_get_bump_products_ids( $product->get_id() );

            // Prepare products object
            $fbt_products = array_map(
                function ( $id ) {
                    return wc_get_product( $id );
                },
                $fbt_ids
            );

            // Check product is purchasable or not
            $fbt_products = array_filter( $fbt_products, array( $this, 'is_valid_products' ) );

            // No FBT products? Return bare hand.
            if( empty( $fbt_products ) ) {
                return;
            }
            
            // Add current product to fbt products
            $fbt_products = array_merge( array( wc_get_product( $product->get_id() ) ), $fbt_products );

            // Prepare list of products which already in cart
            $removed_products = $this->dsrbfw_remove_already_in_cart_products( $fbt_products );
            
            // Prepare list of product ids which already in cart
            $removed_product_ids = array_map(
                function ( $product ) {
                    return $product->get_id();
                },
                $removed_products
            );

            // No FBT products? Return bare hand.
            if ( empty( $fbt_products ) ) {
                return;
            }
            
            //Global Values
            $dsrbfw_global_title        = get_option( 'dsrbfw_fbt_title', '' );
            $dsrbfw_global_link_title   = get_option( 'dsrbfw_fbt_title_link_enabled', 'no' );
            $dsrbfw_global_image        = get_option( 'dsrbfw_fbt_image_enabled', 'no' );
            $dsrbfw_global_cart_exist   = get_option( 'dsrbfw_fbt_cart_exist_enabled', 'no' );
            $dsrbfw_global_ajax_cart    = get_option( 'dsrbfw_fbt_ajax_cart_enabled', 'no' );

            // Product specific values
            $dsrbfw_fbt_title = get_post_meta( $product->get_id(), '_dsrbfw_fbt_title', true );
            $dsrbfw_fbt_desc = get_post_meta( $product->get_id(), '_dsrbfw_fbt_desc', true );

            // Final data to be used
            $dsrbfw_title = !empty( $dsrbfw_fbt_title ) ? sanitize_text_field( $dsrbfw_fbt_title ) : sanitize_text_field( $dsrbfw_global_title );
            $dsrbfw_desc = !empty( $dsrbfw_fbt_desc ) ? sanitize_text_field( $dsrbfw_fbt_desc ) : '';
            $dsrbfw_wrap_with_form = "yes" !== $dsrbfw_global_ajax_cart && $this->should_position_be_wrapped_with_form( $this->dsrbfw_single_product_fbt_render_hook() );

            // As brdefault our current product is also part of FBT products
            $fbt_product_ids = array( $product->get_id() );

            $total_price = $this->dsrbfw_get_price_html_for_bumps( $fbt_product_ids, $product->get_id() );

            // We can show FBT products horizontally, in future we will use it in configuration
            $display_type = apply_filters( 'dsrbfw_show_fbt_product_horizonally', false, $product );

            // Now we have data, So prepare HTML for FBT products
            ob_start();
            if ( $dsrbfw_wrap_with_form ) : ?>
                <form
                    class="cart <?php echo $product->is_type( 'variable' ) ? esc_attr( 'variations_form' ) : ''; ?>"
                    action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" 
                    method="post"
                    enctype='multipart/form-data'
                >
                <?php wp_nonce_field( 'dsrbfw_add_to_cart_nonce_action', 'dsrbfw_add_to_cart_nonce_field' ); ?>
            <?php endif; ?>
            <div class="dsrbfw_fbt__main_section">
                <?php if ( ! empty( $dsrbfw_title ) ) { ?>
                    <div class="dsrbfw_fbt__header">
                        <h3 class="dsrbfw_fbt__title">
                            <?php echo wp_kses_post( $dsrbfw_title ); ?>
                        </h3>
                    </div>
                <?php } ?>
                <div class="dsrbfw_fbt__body">

                    <?php if ( $dsrbfw_desc ) { ?>
                        <div class="dsrbfw_fbt__description">
                            <p><?php echo esc_html( $dsrbfw_desc ); ?></p>
                        </div>
                    <?php } ?>

                    <div class="dsrbfw_fbt__list <?php if( ( 'yes' === $dsrbfw_global_cart_exist ) && empty($removed_product_ids) ) { echo esc_attr('dsrbfw_fbt__all-exist'); } ?> <?php if( $display_type ) { echo 'dsrbfw_horizontal_type'; }?>">
                        <?php foreach ( $fbt_products as $bump_product ) { 
                            $in_cart              = ! in_array( $bump_product->get_id(), $removed_product_ids, true );
                            $variation_attributes = array();
                            if ( $bump_product->is_type( 'variation' ) ) {
                                $variation_attributes = $bump_product->get_variation_attributes();
                            }
                            ?>
                            <div class="dsrbfw_fbt__product">
                                <span class="dsrbfw_fbt__checkbox_wrap">
                                    <?php if( ! ( ( 'yes' === $dsrbfw_global_cart_exist ) && $in_cart ) ) { ?>
                                        <input 
                                            type="checkbox" 
                                            class="dsrbfw_fbt__checkbox input-checkbox" 
                                            name="dsrbfw-fbt-product[<?php echo esc_attr( $bump_product->get_id() ); ?>]" 
                                            value="<?php echo $bump_product->is_type( 'variation' ) ? esc_attr( $bump_product->get_parent_id() ) : esc_attr( $bump_product->get_id() ); ?>"
                                            <?php checked( $bump_product->get_id(), $product->get_id(), true ) ?>
                                            data-variation-id="<?php echo $bump_product->is_type( 'variation' ) ? esc_attr( $bump_product->get_id() ) : ''; ?>"
                                            data-product-type="<?php echo esc_attr( $bump_product->get_type() ); ?>"
                                        />
                                    <?php } ?>
                                </span>

                                <?php if( 'yes' === $dsrbfw_global_image ) { ?>
                                    <div class="dsrbfw_fbt__product-image">
                                        <?php
                                            echo wp_kses_post(
                                                $bump_product->get_image(
                                                    /**
                                                     * Filter the size of the thumbnail shown in the Frequently Bought Together section.
                                                     *
                                                     * @since 1.0.0
                                                     * @hook dsrbfw_fbt_thumbnail_size
                                                     * @param string|array $size         Default: array( 60 x 60 ).
                                                     * @param  WC_Product  $bump_product The product shown in the FBT section.
                                                     * @param  WC_Product  $product      The product shown on the page.
                                                     * @return string|array New value
                                                     */
                                                    apply_filters( 'dsrbfw_fbt_thumbnail_size', array( 60, 60 ), $bump_product, $product )
                                                )
                                            );
                                        ?>
                                    </div>
                                <?php } ?>

                                <div class="dsrbfw_fbt__product-content">

                                    <?php if ( ( $bump_product->get_id() === $product->get_id() ) && $dsrbfw_main_product ) { ?>
                                        <strong class="dsrbfw_fbt__title dsrbfw_fbt__title--this-item"><?php esc_html_e( 'Current product', 'revenue-booster-for-woocommerce' ); ?>: <?php echo esc_html( $bump_product->get_title() ); ?></strong>
                                    <?php } elseif ( 'yes' === $dsrbfw_global_link_title ) { ?>
                                        <a class="dsrbfw_fbt__title dsrbfw_fbt__title--link" href="<?php echo esc_url( $bump_product->get_permalink() ); ?>">
                                            <?php 
                                            $product_summary = '';
                                            if ( ! empty( $variation_attributes ) ) {
                                                $product_summary = $bump_product->get_attribute_summary();
                                                $product_summary = ! empty( $product_summary ) 
                                                /* translators: %s is replaced with "string" which show FBT content */
                                                ? sprintf( esc_html__( '(%s)', 'revenue-booster-for-woocommerce' ), $product_summary ) 
                                                : '';
                                            }
                                            echo esc_html( $bump_product->get_title() ); ?> <?php echo esc_html( $product_summary ); ?>
                                        </a>
                                        <?php
                                    } elseif ( ! empty( $variation_attributes ) ) {
                                        $product_summary = $bump_product->get_attribute_summary();
                                        $product_summary = ! empty( $product_summary ) 
                                        /* translators: %s is replaced with "string" which show FBT content */
                                        ? sprintf( esc_html__( '(%s)', 'revenue-booster-for-woocommerce' ), $product_summary ) 
                                        : '';
                                        ?>
                                        <span class="dsrbfw_fbt__title"><?php echo esc_html( $bump_product->get_title() ); ?> <?php echo esc_html( $product_summary ); ?></span>
                                    <?php } else { ?>
                                        <span class="dsrbfw_fbt__title"><?php echo esc_html( $bump_product->get_title() ); ?></span>
                                    <?php } ?>
                                    
                                    <span class="dsrbfw_fbt__product-exist-in-cart remove-text-<?php echo esc_attr( $bump_product->get_id() ); ?>">
                                        <?php echo $in_cart ? esc_html__( '(Exist in cart)', 'revenue-booster-for-woocommerce' ) : ''; ?>
                                    </span>
                                    
                                    <span class="dsrbfw_fbt__product-price">
                                        <?php echo wp_kses_post( $bump_product->get_price_html() ); ?>
                                    </span>

                                    <?php
                                    if ( $bump_product->is_type( 'variable' ) && ! ( ( 'yes' === $dsrbfw_global_cart_exist ) && $in_cart ) ) {
                                        
                                        $attributes = $bump_product->get_variation_attributes();

                                        if( !empty( $attributes ) ) { ?>
                                            <table class="dsrbfw_fbt__variation variations" data-variable-id="<?php echo esc_attr( $bump_product->get_id() ); ?>">
                                                <?php
                                                foreach ( $attributes as $attribute_name => $options ) : ?>
                                                    <tr>
                                                        <th class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?></label></th>
                                                        <td class="value">
                                                            <?php
                                                                wc_dropdown_variation_attribute_options(
                                                                    array(
                                                                        'options'   => $options,
                                                                        'attribute' => $attribute_name,
                                                                        'product'   => $bump_product,
                                                                        'class'     => 'dsrbfw_fbt__variation-select dsrbfw-fbt-product-variation-' . $bump_product->get_id(),
                                                                        'name'      => 'dsrbfw-fbt-product-variation-' . $bump_product->get_id(),
                                                                    )
                                                                );
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </table>
                                            <?php
                                        } ?>
                                        <input type="hidden" name="dsrbfw-fbt-product-variation-<?php echo esc_attr( $bump_product->get_id() ); ?>" value="" />
                                        <input type="hidden" name="dsrbfw-fbt-product-attributes-<?php echo esc_attr( $bump_product->get_id() ); ?>" value="" />
                                        <?php 
                                    } elseif ( $bump_product->is_type( 'variation' ) && ! ( ( 'yes' === $dsrbfw_global_cart_exist ) && $in_cart ) ) { 

                                        $any_attributes = $this->dsrbfw_get_variation_any_attributes( $bump_product );

                                        if( ! empty( $variation_attributes ) && ! empty( $any_attributes ) ) {
                                            ?>
                                            <table class="dsrbfw_fbt__variation variations" data-variable-id="<?php echo esc_attr( $bump_product->get_parent_id() ); ?>" data-variation-id="<?php echo esc_attr( $bump_product->get_id() ); ?>">
                                                <?php
                                                foreach ( $any_attributes as $attribute_name => $options ) : ?>
                                                    <tr>
                                                        <th class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?></label></th>
                                                        <td class="value">
                                                            <?php
                                                                wc_dropdown_variation_attribute_options(
                                                                    array(
                                                                        'options'   => $options,
                                                                        'attribute' => $attribute_name,
                                                                        'product'   => wc_get_product( $bump_product->get_parent_id() ),
                                                                        'class'     => 'dsrbfw_fbt__variation-select dsrbfw-fbt-product-variation-' . $bump_product->get_id(),
                                                                        'name'      => 'dsrbfw-fbt-product-variation-' . $bump_product->get_id(),
                                                                    )
                                                                );
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </table>
                                            <?php 
                                            $filter_attributes = is_array($variation_attributes) ? array_filter( $variation_attributes ) : array();
                                            if( $filter_attributes ) {
                                                foreach( $filter_attributes as $variation_attribute_key => $variation_attribute_value ) { ?>
                                                    <input type="hidden" class="dsrbfw_fbt__variation-select dsrbfw-fbt-product-variation-<?php echo esc_attr( $bump_product->get_id() ); ?>" data-attribute_name="<?php echo esc_attr( $variation_attribute_key ); ?>" value="<?php echo esc_attr( $variation_attribute_value ); ?>" />
                                                <?php } 
                                            } ?>
                                            <input type="hidden" name="dsrbfw-fbt-product-attributes-<?php echo esc_attr( $bump_product->get_id() ); ?>" value="" />
                                            <?php 
                                        } else { ?>
                                            <input type="hidden" name="dsrbfw-fbt-product-attributes-<?php echo esc_attr( $bump_product->get_id() ); ?>" value="<?php echo esc_attr( wp_json_encode( $variation_attributes ) )?>" />
                                        <?php
                                        } ?>
                                        <input type="hidden" name="dsrbfw-fbt-product-variation-<?php echo esc_attr( $bump_product->get_id() ); ?>" value="<?php echo esc_attr( $bump_product->get_id() ); ?>" />
                                        <?php
                                    } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <?php if( ! ( 'yes' === $dsrbfw_global_cart_exist && empty($removed_product_ids) ) ) { ?>
                        <div class="dsrbfw_fbt__actions">
                            <div class="dsrbfw_fbt__total-price">
                                <span class="dsrbfw_fbt__total-price-label">
                                    <?php esc_html_e( 'Total Price:', 'revenue-booster-for-woocommerce' ); ?>
                                </span>
                                <span class="dsrbfw_fbt__total-price-amount">
                                    <?php echo wp_kses_post( $total_price ); ?>
                                </span>
                            </div>
                            <div class="dsrbfw_fbt__button-wrap">
                                <button 
                                    type="submit"
                                    class="button dsrbfw_fbt__button alt wp-element-button"
                                    dsrbfw-fbt-submit
                                    name="dsrbfw-fbt-add-selected"
                                    data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
                                >
                                    <?php esc_html_e( 'Add Picked to Cart', 'revenue-booster-for-woocommerce' ); ?>
                                </button>
                            </div>
                            <input type="hidden" class="dsrbfw_main_product_id" name="dsrbfw-main-product-id" value="<?php echo esc_attr( $product->get_id() ); ?>" />
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php if ( $dsrbfw_wrap_with_form ) : ?>
                </form>
            <?php endif; ?>
            <?php 
            echo ob_get_clean(); // phpcs:ignore
        }

    }

    /**
     * Validate product by given product object
     * 
     * @param WC_Product $product
     * 
     * @return boolean
     * 
     * @since 1.0.0
     */
    public function is_valid_products( $product ) {
        
        if ( ! ( $product instanceof WC_Product ) ) {
			return false;
		}

        return $product->is_in_stock() && $product->is_purchasable() && $product->is_visible();
    }

    /**
     * Retrive product ids of bump products
     * 
	 * @param int $product_id
	 *
	 * @return array
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_get_bump_products_ids( $product_id ) {

        $bump_ids = (array) get_post_meta( $product_id, '_dsrbfw_fbt_product_ids', true );

		return apply_filters( 'dsrbfw_single_product_order_bump_ids', array_filter( $bump_ids ), $this, $product_id );
    }

    /**
	 * Remove from list products which already in cart
	 *
	 * @param WC_Product[] $bump_products
	 *
	 * @return WC_Product[]
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_remove_already_in_cart_products( $bump_products ) {

		return array_filter(
			$bump_products,
			function ( $bump_product ) {
				if ( $bump_product instanceof WC_Product ) {
					return ! $this->dsrbfw_is_product_in_cart( $bump_product->get_id() );
				}

				return array();
			}
		);
	}

    /**
     * Check if product is already in cart
     * 
     * @param int $product_id
     * @param boolean $return_cart_item
     * 
     * @return boolean|array
     * 
     * @since 1.0.0
     */
    public function dsrbfw_is_product_in_cart( $product_id, $return_cart_item = false ) {

        if ( empty( $product_id ) ) {
			return false;
		}

        //Sometime change type to string so we need it.
        $product_id = absint( $product_id );

        $product_obj = wc_get_product( $product_id );

		if ( ! $product_obj ) {
			return false;
		}

        if ( ! WC()->cart ) {
			return;
		}

        $exist_in_cart = false;

        $cart = WC()->cart->get_cart();

        foreach ( $cart as $cart_item ) {
            
            //Product ID
            $cart_product_id   = $cart_item['product_id'];

            //Variation ID
			$cart_variation_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : 0;

            //Variable ID
			$cart_parent_id    = $cart_variation_id ? wp_get_post_parent_id( $cart_variation_id ) : 0;

            if( $product_obj->is_type( 'variable' ) && $cart_parent_id === $product_id ) {

                //Variable Product check
                $exist_in_cart = true;
            } else if( $product_obj->is_type( 'variation' ) && $cart_variation_id === $product_id ) {

                //Variation Product check
                $exist_in_cart = true;
            } else if( $cart_product_id === $product_id ) {

                //Simple and other product type check
                $exist_in_cart = true;
            }
            if( $exist_in_cart ) {
                if( $return_cart_item ) {
                    return $cart_item;
                } else {
                    return $exist_in_cart;
                }
            }
        }

        return $exist_in_cart;
    }

    /**
	 * AJAX callback: Get all variations and their attributes. 
	 *
     * @since 1.0.0
	 */
    public function dsrbfw_get_variation_price_callback() {
        check_ajax_referer( 'dsrbfw-variation-price', 'security' );
        
        $get_dsrbfw_variable_id = filter_input( INPUT_POST, 'dsrbfw_variable_id', FILTER_SANITIZE_NUMBER_INT );
        $dsrbfw_variable_id = $get_dsrbfw_variable_id ? absint( $get_dsrbfw_variable_id ) : 0;

        $filter = array( 
            'dsrbfw_attributes' => array(	
                'filter'  => array( FILTER_SANITIZE_NUMBER_INT, FILTER_SANITIZE_SPECIAL_CHARS ),
                'flags'   => FILTER_REQUIRE_ARRAY
            ),
        );
        $get_dsrbfw_attributes    = filter_input_array( INPUT_POST, $filter );
        $dsrbfw_attributes = !empty( $get_dsrbfw_attributes ) ? array_map( 'sanitize_text_field', $get_dsrbfw_attributes['dsrbfw_attributes'] ) : array();

		if ( $dsrbfw_variable_id <= 0 ) {
            wp_send_json_error( esc_html__( 'No product ID provided.', 'revenue-booster-for-woocommerce' ) );
		}

		$variable_product = wc_get_product( $dsrbfw_variable_id );

		if ( ! $variable_product ) {
			wp_send_json_error( esc_html__( 'This ID not belong to variable product.', 'revenue-booster-for-woocommerce' ) );
		}

		$data_store   = WC_Data_Store::load( 'product' );
		$variation_id = $data_store->find_matching_product_variation( $variable_product, wp_unslash( $dsrbfw_attributes ) );
		$variation    = $variation_id ? $variable_product->get_available_variation( $variation_id ) : false;
        wp_send_json( $variation );
    }

    /**
	 * Check if the position should be wrapped with a form.
	 *
	 * When the FBT is positioned outsite the `form.cart`,
	 * it won't work if the option `Enable AJAX for "Add Selected to Cart" button?`
	 * is disabled. That way, we need to wrapp the FBT box with a form.
	 *
	 * @param string $position The position selected on the Position field.
	 * @return boolean
     * 
     * @since 1.0.0
	 */
	public function should_position_be_wrapped_with_form( $position ) {
		switch ( $position ) {
			case 'woocommerce_before_single_product':
			case 'woocommerce_before_single_product_summary':
			case 'woocommerce_single_product_summary':
			case 'woocommerce_before_add_to_cart_form':
			case 'woocommerce_after_add_to_cart_form':
			case 'woocommerce_product_meta_start':
			case 'woocommerce_product_meta_end':
			case 'woocommerce_share':
			case 'woocommerce_after_single_product_summary':
			case 'woocommerce_after_single_product':
				return true;

			default:
				return false;
		}
	}

    /**
	 * Get price html for a collection of product bumps.
	 *
	 * @param array $product_ids       Price to be calculated for these product IDs.
	 * @param int   $offer_product_id  Offer product ID.
	 *
	 * @return string
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_get_price_html_for_bumps( $product_ids, $offer_product_id ) {
		$total               = $this->dsrbfw_calculate_product_total( $product_ids );
		$has_price_range     = $this->dsrbfw_product_has_price_range( $product_ids );
		$prefix              = $has_price_range ? sprintf( '%s ', wc_get_price_html_from_text() ) : '';
		$discount_applicable = $this->dsrbfw_validate_cart_discount( $offer_product_id, $product_ids );

		if ( $discount_applicable ) {
            
            $product   = wc_get_product( $offer_product_id );
		    $tax_rates = WC_Tax::get_rates( $product->get_tax_class() );
			$exclude_tax = empty( $tax_rates ) ? true : false;

			$discount   = $this->dsrbfw_calculate_discount( $offer_product_id, $product_ids, $exclude_tax );
            if( $discount ) {
                $price_html = $prefix . wc_format_sale_price( $total, $total - $discount );
                $rule = $this->dsrbfw_get_discount_rule( $offer_product_id );
                
                if( "fixed" === $rule['type'] ) {
                    $price_html .= " (flat ".get_woocommerce_currency_symbol().$discount." off)";
                } else if( "percentage" === $rule['type'] ) {
                    $price_html .= " (".$rule['discount']."% off)";
                }
            } else {
                $price_html = $prefix . wc_price( $total );
            }
		} else {
			$price_html = $prefix . wc_price( $total );
		}

		return $price_html;
	}

    /**
	 * Calculate sum of the prices of given product IDs.
	 *
	 * @param array   $product_ids   Array of product IDs.
	 * @param boolean $excluding_tax To exclude tax or not to exclude.
	 *
	 * @return int|float
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_calculate_product_total( $product_ids, $excluding_tax = false ) {
		$total = 0;

		if ( empty( $product_ids ) ) {
			return;
		}

        // We have filter repeat ids because not goinf to apply discount on same product multiple times
        $product_ids = array_unique( $product_ids );

		foreach ( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );

			if ( ! $product || ! $product->is_in_stock() || ! $product->is_purchasable() ) {
				continue;
			}

			$price  = $excluding_tax ? wc_get_price_excluding_tax( $product ) : wc_get_price_to_display( $product );
			$total += $price;
		}

		return $total;
	}

    /**
	 * Make sure that all the FBT products are present in the cart. For Variable products check if child/variation
	 * exists in cart.
	 *
	 * @param int $product_id
	 *
	 * @return array|false If valid then array of product ids which are present in the cart for the given FBT else false.
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_validate_cart_discount( $offer_product_id, $cart_product_ids ) {

		$fbt_meta_products_ids = $this->dsrbfw_get_bump_products_ids($offer_product_id);

		if ( empty( $fbt_meta_products_ids ) || ! is_array( $fbt_meta_products_ids ) ) {
			return false;
		}

		$fbt_meta_products_ids[] = $offer_product_id;
        $fbt_meta_products_ids = array_map( 'absint', $fbt_meta_products_ids );

		if ( empty( $cart_product_ids ) ) {
			return false;
		}

		$parent_product_ids = array();
		$cart_product_ids   = array_filter( $cart_product_ids );

		foreach ( $cart_product_ids as $cart_product_id ) {
			$cart_product = wc_get_product( $cart_product_id );

			if ( ! $cart_product || ! $cart_product->is_in_stock() || ! $cart_product->is_purchasable() ) {
				continue;
			}

			// We consider the product if either the product ID matches, else the parent ID. (To-Do: check same variation skip disacount)
			if ( in_array( $cart_product_id, $fbt_meta_products_ids, true ) ) {
				$parent_product_ids[] = $cart_product_id;

                $key = array_search( $cart_product_id, $fbt_meta_products_ids, true );
                if ( $key !== false) {
                    unset($fbt_meta_products_ids[$key]);
                }
			} else {
				if ( in_array( $cart_product->get_parent_id(), $fbt_meta_products_ids, true ) ) {
					$parent_product_ids[] = $cart_product->get_parent_id();
                    
                    $key = array_search( $cart_product->get_parent_id(), $fbt_meta_products_ids, true );
                    if ( $key !== false) {
                        unset($fbt_meta_products_ids[$key]);
                    }
				}
			}
		}

		$missing_products = array_diff( $fbt_meta_products_ids, $parent_product_ids );

		// if no product is missing then discount is applicable
		return ! count( $missing_products );
	}

    /**
	 * Does a product have a price range?
	 *
	 * @param array|WC_Product|WC_Product_Variable $products A single product, or array of products/product IDs.
	 *
	 * @return bool
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_product_has_price_range( $products ){
        if ( ! is_array( $products ) ) {
			$products = array( $products );
		}

		$products = array_filter( $products );

		foreach ( $products as $product ) {
			if ( is_numeric( $product ) ) {
				$product = wc_get_product( $product );
			}

			if ( ! $product || ! $product->is_type( 'variable' ) ) {
				continue;
			}

			$prices = $product->get_variation_prices( true );

			if ( empty( $prices['price'] ) ) {
				continue;
			}

			$min_price = current( $prices['price'] );
			$max_price = end( $prices['price'] );

			return $min_price !== $max_price;
		}

		return false;
    }

    /**
	 * Calculates FBT discount for the given $product_id.
	 *
	 * @param int $offer_product_id The ID of the product based on which the discount rull will be applied.
	 * @param arr $cart_product_ids The list of products ID to apply discount on
	 *
	 * @return int|float
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_calculate_discount( $offer_product_id, $cart_product_ids = false, $excluding_tax = false ) {
		
        $cart_product_ids = $cart_product_ids ? $cart_product_ids : $this->dsrbfw_get_bump_products_ids($offer_product_id);

		$rule = $this->dsrbfw_get_discount_rule( $offer_product_id );

		if ( $rule && is_array( $rule ) ) {
			$offer_product  = wc_get_product( $offer_product_id );
			$discount_type  = $rule['type'];
			$discount       = isset( $rule['discount_exc_tax'] ) && ( 'excl' === get_option( 'woocommerce_tax_display_shop' ) || $excluding_tax ) ? $rule['discount_exc_tax'] : $rule['discount'];
			$total_price    = $this->dsrbfw_calculate_product_total( $cart_product_ids, $excluding_tax );

			if ( $offer_product && $discount_type && $discount ) {
				$discount_value = $discount_type === 'percentage' ? ( $total_price / 100 ) * $discount : $discount;

				return apply_filters( 'dsrbfw_fbt_calculate_discount', $discount_value, $offer_product_id, $cart_product_ids );
			}
		}

		return 0;
	}

    /**
	 * Returns the FBT discount rule for given offer product ID
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return array Discount rule.
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_get_discount_rule( $product_id ) {

		$product   = wc_get_product( $product_id );
        $tax_rates = WC_Tax::get_rates( $product->get_tax_class() );

		$rule = array(
			'type'     => get_post_meta( $product_id, '_dsrbfw_fbt_discount_type', true ),
			'discount' => (float) get_post_meta( $product_id, '_dsrbfw_fbt_discount', true ),
		);

		// If prices include tax, we need to get the discount excluding tax.
		if ( 'fixed' === $rule['type'] && ( wc_prices_include_tax() || empty( $tax_rates ) ) ) {
			$base_tax_rates           = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );
			$remove_taxes             = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $rule['discount'], $base_tax_rates, true ) : WC_Tax::calc_tax( $rule['discount'], $tax_rates, true );
			$rule['discount_exc_tax'] = $rule['discount'] - array_sum( $remove_taxes ); // Unrounded since we're dealing with tax inclusive prices. Matches logic in cart-totals class. @see adjust_non_base_location_price.
		}

		return apply_filters( 'dsrbfw_wsb_fbt_discount_rule', $rule, $product_id );
	}

    /**
	 * Returns all possible combination of attributes for those attributes which do not have any specific value.
	 *
	 * @param WC_Product_Variation $product
	 *
	 * @return array|void
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_get_variation_any_attributes( $product ) {
		if ( is_a( $product, 'WC_Product_Variation' ) ) {
			$attributes = $product->get_variation_attributes();
			// get all the terms for all the attributes which have 'any'
			$blank_attributes = array();
			foreach ( $attributes as $attribute_key => $attribute_val ) {
				if ( ! $attribute_val ) {
					$blank_attributes[ $attribute_key ] = wp_get_post_terms(
						$product->get_parent_id(),
						substr($attribute_key, 10 ),
						array(
							'fields' => 'id=>slug',
						)
					);
				}
			}

            $return_arr = array();
            foreach( $blank_attributes as $arr_key => $arr_val ) {
                $return_arr[ str_replace('attribute_', '', $arr_key) ] = array_values( $arr_val );
            }

			return $return_arr;
		}
	}

    /**
	 * Get term's name from it's slug
	 *
	 * @param string $slug     Term slug.
	 * @param string $taxonomy Taxonomy key.
	 *
	 * @return string $term_name Term Name.
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_get_term_name_from_slug( $slug, $taxonomy ) {
		// Remove "attribute_".
		if ( 'attribute_' === substr( $taxonomy, 0, 10 ) ) {
			$taxonomy = substr( $taxonomy, 10 );
		}

		$term = get_term_by( 'slug', $slug, urldecode( $taxonomy ) );

		if ( is_object( $term ) ) {
			return $term->name;
		}
	}

    /**
     * Add FBT products to the cart
     * 
     * @since 1.0.0
     */
    public function dsrbfw_add_to_cart_action(){
        
        // Check submit button is clicked or not for cart
        $is_add_to_cart_submitted = filter_input( INPUT_POST, 'dsrbfw-fbt-add-selected', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

        // Get main product id
        $dsrbfw_main_product_id = filter_input( INPUT_POST, 'dsrbfw-main-product-id', FILTER_SANITIZE_NUMBER_INT );

        // Get all FBT products
        $filter = array(
            'dsrbfw-fbt-product' => array(
                'filter' => array(FILTER_SANITIZE_NUMBER_INT),
                'flags'  => FILTER_REQUIRE_ARRAY,
            ),
        );
        
        $dsrbfw_fbt_product     = filter_input_array( INPUT_POST, $filter );
        $dsrbfw_fbt_products    = isset( $dsrbfw_fbt_product['dsrbfw-fbt-product'] ) && !empty( $dsrbfw_fbt_product['dsrbfw-fbt-product'] ) ? array_map( 'absint', array_keys( $dsrbfw_fbt_product['dsrbfw-fbt-product'] ) ) : array();
        
        if( ! is_null( $is_add_to_cart_submitted ) && ! empty( $dsrbfw_fbt_products ) && ! empty( $dsrbfw_main_product_id ) ) {

            $redirect_after_add = get_option( 'woocommerce_cart_redirect_after_add' );

            add_filter(
                'pre_woocommerce_cart_redirect_after_add',
                function () {
                    return 'no';
                }
            );

            $added_all_to_cart          = true;
		    $products_not_added_to_cart = array();
            
            $result                     = wp_parse_args(
                $this->dsrbfw_add_products_to_cart( $dsrbfw_main_product_id, $dsrbfw_fbt_products ),
                array(
                    'added_all_to_cart'          => true,
                    'products_not_added_to_cart' => array(),
                )
            );
            
            $added_all_to_cart          = $result['added_all_to_cart'];
            $products_not_added_to_cart = $result['products_not_added_to_cart'];
    
            if ( $added_all_to_cart ) {
                $message = esc_html__( 'All products have been added to your cart.', 'revenue-booster-for-woocommerce' );
                $message = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_cart_url(), esc_html__( 'View cart', 'revenue-booster-for-woocommerce' ), $message );
            }

            if ( ! empty( $products_not_added_to_cart ) ) {

                // Only show default woo message if we are not doing ajax.
                if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
                    return;
                }

                $product_title = implode( '", "', $products_not_added_to_cart );

                $message = wp_kses_post( 
                    sprintf( 
                        // translators: %s - product name.
                        _n( 'Sorry, the following product could not be added to the cart: "%s"', 'Sorry, the following products could not be added to the cart: "%s"', count( $products_not_added_to_cart ), 'revenue-booster-for-woocommerce' ), // phpcs:ignore
                        esc_html( rawurldecode( wp_strip_all_tags( $product_title ) ) )
                    ) 
                );
            }

            // To prevent the main product from adding to cart twice.
            unset( $_REQUEST['add-to-cart'] ); // phpcs:ignore
            unset( $_REQUEST['variation_id'] ); // phpcs:ignore

            if ( wp_doing_ajax() ) {
    
                /**
                 * Fires before the refreshed fragments are returned when a product
                 * is added via AJAX in the product page.
                 *
                 * @since 1.14.0
                 * @hook dsrbfw_before_get_cart_fragments_for_ajax_fbt_add_to_cart
                 * @param string $message           The message about add to cart action.
                 * @param bool   $added_all_to_cart Whether all products were added to the cart.
                 */
                do_action( 'dsrbfw_before_get_cart_fragments_for_ajax_fbt_add_to_cart', $message, $added_all_to_cart );

                WC_AJAX::get_refreshed_fragments();
            } else {
                if ( 'yes' === $redirect_after_add ) {
                    wp_safe_redirect( wc_get_cart_url() );
                    exit;
                } else {
                    $message_type = $added_all_to_cart ? 'success' : 'error';
                    wc_add_notice( $message, $message_type );
                }
            }
        }
    }

    /**
	 * Add FBT products to the cart.
	 *
	 * @param int       $main_product The product ID of main product which FBT we used.
	 * @param int[]     $product_ids The array of product IDs to be added to the cart.
	 * @return arary    Returns an array with `added_all_to_cart` and `products_not_added_to_cart` keys.
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_add_products_to_cart( $main_product, $fbt_product_ids ){
    
        $added_all_to_cart          = true;
		$products_not_added_to_cart = array();
        
        foreach ( $fbt_product_ids as $product_add_to_cart ) {
            $product                     = wc_get_product( $product_add_to_cart );
			$quantity                    = 1;
			$add_to_cart                 = false;
            
			$variation_dropdown_name     = 'dsrbfw-fbt-product-variation-' . $product_add_to_cart;
			$variation_attributes_hidden = 'dsrbfw-fbt-product-attributes-' . $product_add_to_cart;
            
            $meta_data                   = array(
				'dsrbfw_fbt' => $main_product,
			);
           
            if ( $product->is_type( 'variable' ) || $product->is_type( 'variation' ) ) {
                
                $variation_id   = absint( filter_input( INPUT_POST, $variation_dropdown_name, FILTER_SANITIZE_NUMBER_INT ) );
                $variation_data = filter_input( INPUT_POST, $variation_attributes_hidden, FILTER_SANITIZE_SPECIAL_CHARS );
                $variation_data = $variation_data ? json_decode( $variation_data, true ) : '';

                // Filter the product metadata to be added to the cart.
                $meta_data = apply_filters( 'dsrbfw_fbt_before_cart_metadata', $meta_data, $variation_id );

                // Filters if an item being added to the cart passed validation checks. Default: true.
                $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_add_to_cart, $quantity, $variation_id, $meta_data );

                if ( ! $passed_validation ) {
                    continue;
                }   
                
                $add_to_cart = WC()->cart->add_to_cart( $product_add_to_cart, $quantity, $variation_id, $variation_data, $meta_data );
                
            } else {
                
                // Filter the product metadata to be added to the cart.
                $meta_data = apply_filters( 'dsrbfw_fbt_before_cart_metadata', $meta_data, $product_add_to_cart );

                // Filters if an item being added to the cart passed validation checks. Default: true.
                $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_add_to_cart, $quantity, 0, $meta_data );
                
                if ( ! $passed_validation ) {
                    continue;
                }
                
                $add_to_cart = WC()->cart->add_to_cart( $product_add_to_cart, $quantity, 0, array(), $meta_data );
            }
            
            if ( false === $add_to_cart ) {
				$added_all_to_cart            = false;
				$products_not_added_to_cart[] = $product->get_title();
			}
        }

        $result = array(
			'added_all_to_cart'          => $added_all_to_cart,
			'products_not_added_to_cart' => $products_not_added_to_cart,
		);

		return $result;
    }

    /**
	 * Add frequently bought together discount.
	 *
	 * @param WC_Cart $cart
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_add_fbt_discount( $cart ) {

        $items = $cart->get_cart();

        if ( empty( $items ) ) {
			return;
		}

		$offer_product_ids = array();

		//Fetch how many product which contatin FBT are there in cart
		foreach ( $items as $item ) {
			if ( isset( $item['dsrbfw_fbt'] ) ) {
				$offer_product_ids[] = $item['dsrbfw_fbt'];
			}
		}
        
        $offer_product_ids = array_unique( $offer_product_ids );

        foreach ( $offer_product_ids as $offer_product_id ) {

			$cart_product_ids = [];
            $bought_together_product_ids = (array) $this->dsrbfw_get_bump_products_ids( $offer_product_id );
            
            foreach ( $items as $item ) {
                if (
                    ( isset( $item['dsrbfw_fbt'] ) && $item['dsrbfw_fbt'] === $offer_product_id ) ||
                    ( 
                        ( isset( $item['product_id'] ) && $item['product_id'] === (int) $offer_product_id ) &&
                        ( in_array( $item['product_id'], $bought_together_product_ids, true ) ) 
                    )
                ) {
                    $check_pid = $item['variation_id'] ? $item['variation_id'] : $item['product_id'];

                    if( $item['quantity'] > 1 ) {
                        for( $i = 1; $i <= $item['quantity']; $i++ ) {
                            $cart_product_ids[] = $check_pid;
                        }
                    } else {

                        $cart_product_ids[] = $item['variation_id'] ? $item['variation_id'] : $item['product_id'];
                    }
                }
            }

            $discount_applicable = $this->dsrbfw_validate_cart_discount( $offer_product_id, $cart_product_ids );

            if ( $discount_applicable ) {

                $discount = $this->dsrbfw_calculate_discount( $offer_product_id, $cart_product_ids, true );
                
                if ( empty( $discount ) ) {
                    return;
                }

                $fee = array(
                    'id'        => '_dsrbfw_fbt_discount_for_'.$offer_product_id,
                    'name'      => esc_html__( 'Bundle discount for ', 'revenue-booster-for-woocommerce' ).get_the_title($offer_product_id),
                    'amount'    => - floatval( apply_filters( 'dsrbfw_fbt_bought_together_discount', $discount, $offer_product_id, $cart_product_ids ) ),
                    'taxable'   => false,
                    'tax_class' => apply_filters( 'dsrbfw_tax_class', '', $offer_product_id ),
                );

                $cart->fees_api()->add_fee( $fee );
            }
		}
    }

    /**
	 * AJAX callback: Update final price after products selected. 
	 *
     * @since 1.0.0
	 */
    public function dsrbfw_update_final_price_callback() {
        
        check_ajax_referer( 'dsrbfw-update-final-price', 'security' );

        $product_ids_arr = array_filter( filter_input( INPUT_POST, 'dsrbfw_product_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY ) );
        $product_ids = array_map( 'absint', $product_ids_arr );

        $main_product = filter_input( INPUT_POST, 'dsrbfw_main_product', FILTER_SANITIZE_NUMBER_INT );
        
        if ( empty( $product_ids ) || empty( $main_product ) ) {
			wp_send_json_error( array( 'price_html' => '') );
		}
        
        $total_price = $this->dsrbfw_get_price_html_for_bumps( $product_ids, $main_product );

        if( $total_price ) {
            wp_send_json_success( array( 'price_html' => wp_kses_post( $total_price ) ) );
        } else {
            wp_send_json_error( array( 'price_html' => '') );
        }
    }

    /**
	 * Add the message to the fragments.
	 *
	 * This is required when the product is added via AJAX
	 * in the product page.
	 *
	 * @param string $message           The message about add to cart action.
	 * @param bool   $added_all_to_cart Whether all products were added to the cart.
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_add_message_to_the_cart_fragments( $message, $added_all_to_cart ) {
		add_filter(
			'woocommerce_add_to_cart_fragments',
			function( $fragments ) use ( $message, $added_all_to_cart ) {
				$class_notice = $added_all_to_cart ? 'woocommerce-message' : 'woocommerce-error';

				$fragments['.woocommerce-notices-wrapper'] = '<div class="woocommerce-notices-wrapper"><div class="' . $class_notice . '">' . $message . '</div></div>';

				return $fragments;
			}
		);
	}

    /**
	 * Add to cart button attributes.
	 *
	 * Add additional attributes to add to cart buttons to trigger the JavaScript After Add to Cart popup for AJAX on archive page.
	 *
	 * @param array  $args    The current button arguments.
	 * @param object $product The current product.
	 *
	 * @return array $args.
     * 
     * @since 1.0.0
     * 
	 */
    public function dsrbfw_add_to_cart_button_attributes( $args, $product ){

        // Uses filter `dsrbfw_show_button_attributes` so customers can decie which product they want to disaply after add to cart popup
        $show_button_attributes = apply_filters( 'dsrbfw_show_button_attributes', true, $product );

        if ( $show_button_attributes ) {
			$args['attributes']['data-show_after_cart_popup'] = $product->get_id();
		}

		return $args;
    }

    /**
	 * Show the After Add to Cart Popup.
	 *
	 * @since 1.0.0
	 */
    public function dsrbfw_show_after_add_to_cart_popup() {

        /**
		 * If `Redirect to the cart page after successful addition` option is enabled, we'll show the popup only on cart page.
		 */
		if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) && ! is_cart() ) {
			return;
		}

        foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

			if ( empty( $cart_item['dsrbfw_show_after_add_to_cart_popup'] ) ) {
				continue;
			}

			unset( WC()->cart->cart_contents[ $cart_item_key ]['dsrbfw_show_after_add_to_cart_popup'] );
			WC()->cart->set_session();

			if ( is_checkout() ) {
				continue;
			}

			if ( is_cart() && 'yes' !== get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				continue;
			}

			$this->dsrbfw_after_cart( $cart_item['product_id'] );

			break;
		}
    }

    /**
	 * Add the After Add to Cart Popup in the cart fragments.
	 *
	 * The content of the fragment `.dsrbfw_after_add_to_cart_popup_placeholder`
	 * is used to render the modal in AJAX requests.
	 *
	 * @param array $fragments The cart fragments.
	 * @return array
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_add_after_add_to_cart_popup_in_cart_fragments( $fragments ) {

        // 'Redirect to the cart page after successful addition' option
		if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
			return $fragments;
		}

		foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
			if ( empty( $cart_item['dsrbfw_show_after_add_to_cart_popup'] ) ) {
				continue;
			}

			unset( WC()->cart->cart_contents[ $cart_item_key ]['dsrbfw_show_after_add_to_cart_popup'] );
			WC()->cart->set_session();

			ob_start();
			$this->dsrbfw_after_cart( $cart_item['product_id'] );

			$modal_html = ob_get_clean();
			$modal_html = '<div class="dsrbfw_after_add_to_cart_popup_placeholder">' . $modal_html . '</div>';

			$fragments['.dsrbfw_after_add_to_cart_popup_placeholder'] = $modal_html;

			break;
		}

		return $fragments;
    }

    /**
	 * After the cart has rendered.
     * 
     * @param int|bool $product_id The product ID.
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_after_cart( $product_id = false ) {

        if ( ! $product_id ) {
            $product_id = filter_input( INPUT_POST, 'variation_id', FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE ) ? filter_input( INPUT_POST, 'variation_id', FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE ) : filter_input( INPUT_POST, 'add-to-cart', FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE );
        }

        if ( false === $product_id || ! is_numeric( $product_id ) ) {
			return;
		}

        $product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );

        $product = wc_get_product( $product_id );

		// see if it is a valid product.
		if ( ! $product instanceof WC_Product ) {
			return;
		}

        // if we have not been passed a product, see if one has just been added to the cart. (To-Do: place it for future direct use.)
		if ( empty( $product ) ) {
			$product = $this->dsrbfw_get_added_to_cart_product();
		}

        // check again if it is a valid product.
		if ( ! $product instanceof WC_Product ) {
			return;
		}

        $bump_products_ids = (array) get_post_meta( $product_id, '_dsrbfw_acp_product_ids', true );

        // Get all CAB products object for current product
        $bump_products = array_map(
			function ( $id ) {
				return wc_get_product( $id );
			},
			$bump_products_ids
		);

        $bump_products = $this->dsrbfw_remove_already_in_cart_products( $bump_products );

        // Check product is purchasable or not
        $bump_products = array_filter( $bump_products, array( $this, 'is_valid_products' ) );

        // see if it is a valid product.
		if ( ! $bump_products ) {
			return;
		}

        // Configuration data to add in popup
        $dsrbfw_acp_title           = get_option( 'dsrbfw_acp_title' ) ? get_option( 'dsrbfw_acp_title' ) : esc_html__( 'You should try this!', 'revenue-booster-for-woocommerce' );
        $dsrbfw_acp_title_bg_color  = get_option( 'dsrbfw_acp_title_bg_color', '#27ae60' );
        $dsrbfw_acp_title_text_color = get_option( 'dsrbfw_acp_title_text_color', '#ffffff' );

        // Prepare HTML of modal to show after add to cart
        $cart_items_count = WC()->cart->get_cart_contents_count();
        ob_start();
        ?>
        <div class="dsrbfw_cab_modal hidden">
            <div class="dsrbfw_cab_modal_header" style="background-color: <?php echo esc_attr( $dsrbfw_acp_title_bg_color ); ?>">
                <h3 class="dsrbfw_cab_modal_header_title" style="color: <?php echo esc_attr( $dsrbfw_acp_title_text_color ); ?>">
                    <?php /* translators: %s is replaced with "string" which show name of product which added to cart */ ?>
                    <?php printf( esc_html__( '%s was added to your cart!', 'revenue-booster-for-woocommerce' ), esc_html( $product->get_title() ) ); ?>
                </h3>
                <span class="dsrbfw_close_modal" style="color: <?php echo esc_attr( $dsrbfw_acp_title_text_color ); ?>">&times;</span>
            </div>
            <div class="dsrbfw_cab_modal_content">
                <div class="dsrbfw_cab_modal_content_wrap">
                    <div class="dsrbfw_cab_modal-product-details">
                        <div class="dsrbfw_cab_modal-product-details-image">
                            <?php echo wp_kses( $product->get_image(), dsrbfw()->dsrbfw_alloed_image_tags() ); ?>
                        </div>
                        <div class="dsrbfw_cab_modal-product-details-info">
                            <h3 class="dsrbfw_cab_modal-product-details-title">
                                <?php echo esc_html( $product->get_name() ); ?>
                            </h3>
                            <div class="dsrbfw_cab_modal-product-details-price">
                                <?php echo wp_kses_post( $product->get_price_html() ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="dsrbfw_cab_modal-cart-details">
                        <div class="dsrbfw_cab_modal-cart-details_cart_subtotal">
                            <?php
                                // translators: %s - the cart subtotal.
                                echo wp_kses_post( sprintf( esc_html__( 'Cart subtotal: %s', 'revenue-booster-for-woocommerce' ), WC()->cart->get_cart_subtotal() ) );
                            ?>
                        </div>
                        <div class="dsrbfw_cab_modal-cart-details_cart_items_count">
                            <?php
                                echo wp_kses_post(
                                    sprintf(
                                        // translators: %d - the number of items in the cart.
                                        _n( 'Total: (%d Item)', 'Total: (%d Items)', $cart_items_count, 'revenue-booster-for-woocommerce' ),
                                        $cart_items_count
                                    )
                                );
                            ?>
                        </div>
                        <?php if( ! is_cart() ) { ?>
                            <div class="dsrbfw_cab_modal-cart-details_view_cart">
                                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="button wp-element-button">
                                    <?php esc_html_e( 'View Cart ', 'revenue-booster-for-woocommerce' ); ?>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- CAB Products -->
                <?php if( !empty( $bump_products ) ) { ?>
                    <div class="dsrbfw_cab_wrap">
                        <h2 class="dsrbfw_cab_section_title">
                            <?php echo wp_kses_post( $dsrbfw_acp_title ); ?>
                        </h2>
                        <div class="dsrbfw_cab_products_list">
                            <?php foreach( $bump_products as $bump_product ) { ?>
                                <div class="dsrbfw_cab_product_wrap">
                                    <div class="dsrbfw_cab_product__image">
										<a href="<?php echo esc_url( $bump_product->get_permalink() ); ?>">
											<?php echo wp_kses( $bump_product->get_image( 'woocommerce_thumbnail', array( 'srcset' => '' ) ), dsrbfw()->dsrbfw_alloed_image_tags() ); ?>
										</a>
									</div>
                                    <h3 class="dsrbfw_cab_product__title">
										<a class="dsrbfw_cab_product__title_link" href="<?php echo esc_url( $bump_product->get_permalink() ); ?>">
											<?php echo esc_html( $bump_product->get_name() ); ?>
										</a>
									</h3>
                                    <div class="dsrbfw_cab_product__price">
										<?php echo wp_kses_post( $bump_product->get_price_html() ); ?>
									</div>
                                    <div class="dsrbfw_cab_product__add_to_cart">
										<?php
                                            $args = array();
                                            $defaults = array(
                                                'quantity'              => 1,
                                                'class'                 => implode(
                                                    ' ',
                                                    array_filter(
                                                        array(
                                                            'button',
                                                            wc_wp_theme_get_element_class_name( 'button' ), // escaped in the template.
                                                            'product_type_' . $bump_product->get_type(),
                                                            $bump_product->is_purchasable() && $bump_product->is_in_stock() ? 'add_to_cart_button' : '',
                                                            $bump_product->supports( 'ajax_add_to_cart' ) && $bump_product->is_purchasable() && $bump_product->is_in_stock() ? 'ajax_add_to_cart' : '',
                                                        )
                                                    )
                                                ),
                                                'aria-describedby_text' => $bump_product->add_to_cart_aria_describedby(),
                                                'attributes'            => array(
                                                    'data-product_id'  => $bump_product->get_id(),
                                                    'data-product_sku' => $bump_product->get_sku(),
                                                    'aria-label'       => $bump_product->add_to_cart_description(),
                                                    'rel'              => 'nofollow',
                                                ),
                                            );
                                
                                            $args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $bump_product );
                                
                                            if ( ! empty( $args['attributes']['aria-describedby'] ) ) {
                                                $args['attributes']['aria-describedby'] = wp_strip_all_tags( $args['attributes']['aria-describedby'] );
                                            }
                                
                                            if ( isset( $args['attributes']['aria-label'] ) ) {
                                                $args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
                                            }

                                            // phpcs:disable
                                            echo apply_filters(
                                                'woocommerce_loop_add_to_cart_link',
                                                sprintf(
                                                    '<a href="%s" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_%s" data-quantity="%s" class="%s" %s>%s</a>',
                                                    esc_url( $bump_product->add_to_cart_url() ),
                                                    esc_attr( $bump_product->get_id() ),
                                                    esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
                                                    esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
                                                    isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
                                                    esc_html( $bump_product->add_to_cart_text() )
                                                ),
                                                $bump_product,
                                                $args
                                            );
                                            // phpcs:enable
										?>
									</div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="dsrbfw_overlay hidden"></div>
        <?php
        echo ob_get_clean(); // phpcs:ignore
    }

    /**
	 * Get added to cart product ID
	 *
	 * @return false|WC_Product
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_get_added_to_cart_product() {

		$add_to_cart  = absint( filter_input( INPUT_POST, 'add-to-cart', FILTER_SANITIZE_NUMBER_INT ) );
		$this_product = absint( filter_input( INPUT_POST, 'revenue-booster-for-woocommerce-fbt-this-product', FILTER_SANITIZE_NUMBER_INT ) );

		if ( ! $add_to_cart && ! $this_product ) {
			return false;
		}

		$product_id   = false;
		$variation_id = absint( filter_input( INPUT_POST, 'variation_id', FILTER_SANITIZE_NUMBER_INT ) );

		if ( $variation_id ) {
			$product_id = $variation_id;
		} elseif ( $add_to_cart ) {
			$product_id = $add_to_cart;
		} elseif ( $this_product ) {
			$product_id = $this_product;
		}

		$product = wc_get_product( $product_id );

		if ( $product instanceof WC_Product ) {
			return $product;
		}

		return false;
	}

    /**
	 * Add the key `dsrbfw_show_after_add_to_cart_popup` to show the popup when it is not added thorugh FBT section 
     * (Just like normal product add).
     * We are excluding Frequently Bought Together (FBT) products here. If multiple FBT products are included, the 
     * 'add to cart' popup will appear multiple times, which can be confusing for the user.
	 *
	 * @param string $cart_item_key ID of the item in the cart.
	 * @param int    $product_id    The product ID.
	 * 
     * @since 1.0.0
	 */
	public function dsrbfw_handle_show_after_add_to_cart_popup( $cart_item_key, $product_id ) {
		
        if ( ! empty( get_post_meta( $product_id, '_dsrbfw_acp_product_ids', true ) ) && ! isset( WC()->cart->cart_contents[ $cart_item_key ]['dsrbfw_fbt'] ) ) {
			WC()->cart->cart_contents[ $cart_item_key ]['dsrbfw_show_after_add_to_cart_popup'] = true;
		}
	}

    /**
	 * Output a placeholder to render the After Add to Cart Popup when the product is added via AJAX.
	 *
	 * @since 1.0.0
	 */
	public function dsrbfw_add_after_add_to_cart_popup_placeholder() {
		?>
		<div class="dsrbfw_after_add_to_cart_popup_placeholder"></div>
		<?php
	}

    /**
     * Prepare the data for at checkout order bump
     * 
     * @since 1.0.0
     */
    public function dsrbfw_show_order_bump_at_checkout() {

        //This should only load on checkout as we need to show order bump on checkout page only (To-Do)
        if( ! is_checkout() ) {
            return;
        }

        // Get all valid order bump offer ids
        $dsrbfw_offer_ids = $this->dsrbfw_get_valid_ob_ac_offer_id(); 

        if( ! $dsrbfw_offer_ids ) {
            return;
        }

        // Block Checkout compatible
        add_filter( 'render_block_woocommerce/checkout-order-summary-block', array( $this, 'dsrbfw_block_include_order_bump_template' ) );

        foreach( $dsrbfw_offer_ids as $dsrbfw_offer_id ) {

            // Offer conditions are met then show order bump at checkout
            if ( ! $this->dsrbfw_order_bump_offer_condition( $dsrbfw_offer_id ) ){
                continue;
            }
            
            if( $dsrbfw_offer_id ) {

                // Classic Checkout compatible
                add_action( 'woocommerce_review_order_before_submit',
                    function() use ( $dsrbfw_offer_id ) {
                        $this->include_order_bump_template( $dsrbfw_offer_id );
                    }
                );
            }

        }
    }

    /**
     * Check on reload for addon product in cart and remove if rule is not active anymore or any garbage value save with our key in cart
     * 
     * @since 1.0.0
     */
    public function dsrbfw_remove_order_bump_on_rule_deactivation() {

        if( WC()->cart->get_cart() ) {

            foreach ( WC()->cart->get_cart() as $key => $cart_item ) {

                if ( isset( $cart_item[ $this->ob_ac_cart_meta_key ] ) ) {

                    // If key exist then cehck for rule is active or not
                    if( empty( $cart_item[ $this->ob_ac_cart_meta_key ] ) || ( ! empty($cart_item[ $this->ob_ac_cart_meta_key ]) && ! $this->is_order_bump_rule_exists( $cart_item[ $this->ob_ac_cart_meta_key ], DSRBFW_BEFORE_OB_POST_TYPE ) ) ) {
                        WC()->cart->remove_cart_item( $key );
                    }
                }
            }
        }
    }

    /**
	 * Include Checkout Bump template.
	 *
	 * @param int $dsrbfw_offer_id The Checkout bump ID.
	 * @return void
     * 
     * @since 1.0.0
	 */
    public function include_order_bump_template( $dsrbfw_offer_id ) {

        $cart_addon_product_id = false;
        $variation_data = array();
        $rules_in_cart = array();

        foreach ( WC()->cart->get_cart() as $cart_item ) {

            if ( isset( $cart_item[ $this->ob_ac_cart_meta_key ] ) ) {

                $cart_addon_product_id = ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
                
                $rules_in_cart[ absint( $cart_item[ $this->ob_ac_cart_meta_key ] ) ] = $cart_addon_product_id;

                // On reload checkout if order bump added then we need to show selected variation data
                if( $cart_item[ $this->ob_ac_cart_meta_key ] === $dsrbfw_offer_id ) {
                    if ( isset( $cart_item['variation'] ) ) {
                        $variation_data = $cart_item['variation'];
                    } elseif ( is_a( $cart_item['data'], 'WC_Product_Variable' ) || is_a( $cart_item['data'], 'WC_Product_Variation' ) ) {
                        $variation_data = $cart_item['data']->get_variation_attributes();
                    }
                }
            } 
        }
        
        // Order Bump at Checkout HTML
        dsrbfw()->include_template( 'public/partials/revenue-booster-for-woocommerce-order-bump-at-checkout.php', array(
            'dsrbfw_offer_id'       => $dsrbfw_offer_id,
            'variation_data'        => $variation_data,
            'rules_in_cart'         => $rules_in_cart,
        ) );
    }

    /**
	 * Add the Order Bump at Checkout in checkout block.
	 *
	 * @param string $block_content The block content.
     * 
	 * @return string
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_block_include_order_bump_template( $block_content ) {
        global $post;

		if ( ! has_block( 'woocommerce/checkout', $post ) ) {
			return $block_content;
		}

        $dsrbfw_offer_ids = $this->dsrbfw_get_valid_ob_ac_offer_id(); 
        
        ob_start();
        foreach( $dsrbfw_offer_ids as $dsrbfw_offer_id ) {
            
            // Offer conditions are met then show order bump at checkout
            if ( ! $this->dsrbfw_order_bump_offer_condition( $dsrbfw_offer_id ) ){
                continue;
            }

            $this->include_order_bump_template( $dsrbfw_offer_id );
        }
        $checkout_bump_html = ob_get_clean();

        return $block_content . $checkout_bump_html;
    }

    /**
	 * Check if product added as offer product in cart (To-Do: Put it for future use if discount added and also check for repeat product in cart)
	 *
	 * @return bool|array   return cart item object if product as offer product else return false
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_is_product_as_offer_in_cart ( $offer_product_id ) {

        $dsrbfw_ob_ac_product_cart_item = $this->dsrbfw_is_product_in_cart( $offer_product_id, true );

        if( $dsrbfw_ob_ac_product_cart_item && isset( $dsrbfw_ob_ac_product_cart_item['dsrbfw_bump_price'] ) ) {
            return $dsrbfw_ob_ac_product_cart_item;
        }

        return false;
    }

    /**
	 * Get Active Order Bumps at Checkout.
	 *
	 * @return WP_Post[]
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_get_ob_ac_rules() {

        $bumps = new WP_Query(
            array(
                'post_per_pages' => - 1,
                'post_type'     => DSRBFW_BEFORE_OB_POST_TYPE,
                'post_status'   => 'publish',
                'orderby'     => 'date',
                'order'       => 'ASC',
            )
        );
    
        return $bumps->posts ? $bumps->posts : array();
    }

    /**
     * Prepare list of valid order bump at checkout offer ids
     * 
     * @return array|bool
     * 
     * @since 1.0.0
     */
    public function dsrbfw_get_valid_ob_ac_offer_id() {

        // Get all order bump at checkout rules
        $order_bump_at_checkout_rules = $this->dsrbfw_get_ob_ac_rules();
        
        if( !empty( $order_bump_at_checkout_rules ) ) {

            $return_ob_ids = array();

            foreach( $order_bump_at_checkout_rules as $order_bump_at_checkout_rule ) {
                
                $ob_ac_id = isset( $order_bump_at_checkout_rule->ID ) && !empty( $order_bump_at_checkout_rule->ID ) ? absint( $order_bump_at_checkout_rule->ID ) : 0; 
                
                if( $ob_ac_id > 0 ) {
                    $ob_ac_offer_product_id = get_post_meta( $ob_ac_id, 'dsrbfw_ob_ac_product', true );

                    if( $ob_ac_offer_product_id > 0 ) {

                        // Get offer product object for current order bump at checkout rule
                        $ob_ac_offer_product = wc_get_product( $ob_ac_offer_product_id );

                        // Check offer product is valid or not
                        if( $this->is_valid_products( $ob_ac_offer_product ) ) {
                            
                            //check if product is already in cart
                            if( ! $this->dsrbfw_is_product_as_offer_in_cart( $ob_ac_offer_product_id ) ) {
                                
                                $return_ob_ids[] = absint( $ob_ac_id ); 
                            }
                        }
                    }
                }
            }

            return $return_ob_ids;
        }

        return false;
    }

    /**
	 * Check either bump offer is valid or not.
	 *
	 * @return boolean
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_order_bump_offer_condition( $bump_id ) {

        if ( is_admin() ){
            return false;
        }

        if( is_null( WC()->cart ) ){
            return false;
        }

        $offer_status = get_post_status( $bump_id );

        if( empty( $offer_status ) || 'publish' !== $offer_status ) {
            return false;
        }

        $dsrbfw_ob_conditions = get_post_meta( $bump_id, 'dsrbfw_ob_conditions', true );

        $final_passed = array();
        $is_passed = array();

        if ( ! empty( $dsrbfw_ob_conditions ) ) {
            $product_array              = array();
            $category_array             = array();

            $general_rule_match      = 'all';
            foreach ( $dsrbfw_ob_conditions as $key => $value ) {
                
                if ( array_search( 'product', $value, true ) ) {
                    $product_array[ $key ] = $value;
                }
                if ( array_search( 'category', $value,true ) ) {
                    $category_array[ $key ] = $value;
                }
            }
            
            //Check if is product exist
            if ( is_array( $product_array ) && ! empty( $product_array ) ) {
                $product_passed = $this->dsrbfw_match_product_rule( $product_array, $general_rule_match );
                if ( $product_passed ) {
                    $is_passed['has_dsrbfw_based_on_product'] = 'yes';
                } else {
                    $is_passed['has_dsrbfw_based_on_product'] = 'no';
                }
            }

            //Check if is category exist
            if ( is_array( $category_array ) && ! empty( $category_array ) ) {
                $category_passed = $this->dsrbfw_match_category_rule( $category_array, $general_rule_match );
                if ( $category_passed ) {
                    $is_passed['has_dsrbfw_based_on_category'] = 'yes';
                } else {
                    $is_passed['has_dsrbfw_based_on_category'] = 'no';
                }
            }
            
            if ( ! empty( $is_passed ) && is_array( $is_passed ) ) {
                $fnispassed = array();
                foreach ( $is_passed as $val ) {
                    if ( '' !== $val ) {
                        $fnispassed[] = $val;
                    }
                }
                if ( 'all' === $general_rule_match ) {
                    if ( in_array( 'no', $fnispassed, true ) ) {
                        $final_passed['passed'] = 'no';
                    } else {
                        $final_passed['passed'] = 'yes';
                    }
                }
            }
        } else {
            $final_passed['passed'] = 'yes';
        }

        if ( isset( $final_passed ) && ! empty( $final_passed ) && is_array( $final_passed ) && ! in_array( 'no', $final_passed, true ) ) {
            return true;
        }
        return false;
    }

    /**
	 * Match products rules
	 *
	 * @param array  $product_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    1.0.0
	 *
	 */
    public function dsrbfw_match_product_rule( $product_array, $general_rule_match ) {
        $is_passed = array();
        $main_is_passed = false;

        if( ! empty( $product_array ) ) {
            foreach( $product_array as $key => $product ) {
                if ( ! empty( $product['dsrbfw_ob_values'] ) ) {

                    if ( 'is_equal_to' === $product['dsrbfw_ob_is'] ) {

                        foreach ( $product['dsrbfw_ob_values'] as $product_id ) {
                            if( $this->dsrbfw_is_product_in_cart( $product_id ) ) {
                                $is_passed[ $key ]['has_dsrbfw_based_on_product'] = true;
                                break;
                            } else {
                                $is_passed[ $key ]['has_dsrbfw_based_on_product'] = false;
                            }
                        }
                    }

                    if ( 'not_in' === $product['dsrbfw_ob_is'] ) {

                        foreach ( $product['dsrbfw_ob_values'] as $product_id ) {
                            if( $this->dsrbfw_is_product_in_cart( $product_id ) ) {
                                $is_passed[ $key ]['has_dsrbfw_based_on_product'] = false;
                                break;
                            } else {
                                $is_passed[ $key ]['has_dsrbfw_based_on_product'] = true;
                            }
                        }
                    }
                }
            }

            // All rules are matched or not
            $flag           = array();
            if ( ! empty( $is_passed ) ) {
                foreach ( $is_passed as $key => $is_passed_value ) {
                    if ( $is_passed_value[ 'has_dsrbfw_based_on_product' ] ) {
                        $flag[ $key ] = true;
                    } else {
                        $flag[ $key ] = false;
                    }
                }
                if( 'all' === $general_rule_match ) {
                    if ( in_array( false, $flag, true ) ) {
                        $main_is_passed = false;
                    } else {
                        $main_is_passed = true;
                    }
                }
            }

        }
        return $main_is_passed;
    }

    /**
	 * Match category rules
	 *
	 * @param array  $category_array
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 *
	 * @since    1.0.0
	 *
	 */
    public function dsrbfw_match_category_rule( $category_array, $general_rule_match ) {
        $is_passed = array();
        $main_is_passed = false;
        $cart_category_id_array = array();
        
        foreach (WC()->cart->get_cart() as $value ) {
            $cart_product_id = ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) ? $value['variation_id'] : $value['product_id'];
            $prod_obj = wc_get_product( $cart_product_id );
            $cart_category_id_array = array_map( 'absint', array_unique( array_merge( $cart_category_id_array, $prod_obj->get_category_ids() ) ) );
        }

        if( ! empty( $category_array ) ) {
            foreach( $category_array as $key => $category ) {
                if ( ! empty( $category['dsrbfw_ob_values'] ) ) {

                    $condition_category = array_map( 'absint', $category['dsrbfw_ob_values'] );

                    if ( 'is_equal_to' === $category['dsrbfw_ob_is'] ) {

                        foreach ( $condition_category as $category_id ) {
                            if( in_array( $category_id, $cart_category_id_array, true ) ) {
                                $is_passed[ $key ]['has_dsrbfw_based_on_category'] = true;
                                break;
                            } else {
                                $is_passed[ $key ]['has_dsrbfw_based_on_category'] = false;
                            }
                        }
                    }

                    if ( 'not_in' === $category['dsrbfw_ob_is'] ) {

                        foreach ( $condition_category as $category_id ) {
                            if( in_array( $category_id, $cart_category_id_array, true ) ) {
                                $is_passed[ $key ]['has_dsrbfw_based_on_category'] = false;
                                break;
                            } else {
                                $is_passed[ $key ]['has_dsrbfw_based_on_category'] = true;
                            }
                        }
                    }
                }
            }

            // All rules are matched or not
            $flag           = array();
            if ( ! empty( $is_passed ) ) {
                foreach ( $is_passed as $key => $is_passed_value ) {
                    if ( $is_passed_value[ 'has_dsrbfw_based_on_category' ] ) {
                        $flag[ $key ] = true;
                    } else {
                        $flag[ $key ] = false;
                    }
                }
                if( 'all' === $general_rule_match ) {
                    if ( in_array( false, $flag, true ) ) {
                        $main_is_passed = false;
                    } else {
                        $main_is_passed = true;
                    }
                }
            }

        }
        return $main_is_passed;
    }

    /**
	 * Handle ajax when on checkout trigger update_checkout
	 *
	 * @param string $post_data
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_add_remove_bump_at_checkout( $post_data ){
        $data = array();

		parse_str( $post_data, $data );

        // If rule deactivate and still user not checkout then remove order bump rule from cart and checkout
        foreach ( WC()->cart->get_cart() as $key => $cart_item ) {
            if ( isset( $cart_item[ $this->ob_ac_cart_meta_key ] ) && ( empty( $cart_item[ $this->ob_ac_cart_meta_key ] ) || ! $this->is_order_bump_rule_exists( $cart_item[ $this->ob_ac_cart_meta_key ], DSRBFW_BEFORE_OB_POST_TYPE ) ) ) {
                WC()->cart->remove_cart_item( $key );
            }
        }

        if( isset( $data['dsrbfw_ob_ac-bump-data'] ) && !empty( $data['dsrbfw_ob_ac-bump-data'] ) ) {

            $bump_data = $data['dsrbfw_ob_ac-bump-data'];

            foreach( $bump_data as $bump_id => $bump ) {
                
                if ( ! empty( $bump['dsrbfw_ob_ac-bump-action'] ) && in_array( $bump['dsrbfw_ob_ac-bump-action'], array( 'add', 'remove' ), true ) && ! empty( $bump_id ) ) {

                    if ( ! $this->is_order_bump_rule_exists( $bump_id, DSRBFW_BEFORE_OB_POST_TYPE ) ) {
                        continue;
                    }

                    $variation_data = null;
                    
                    if ( isset( $bump['dsrbfw_ob_ac-variation-id'] ) && $bump['dsrbfw_ob_ac-variation-id'] ) {
                        $product_id =  absint( $bump['dsrbfw_ob_ac-variation-id'] );
                        $variation_data = json_decode( $bump['dsrbfw_ob_ac-variation-data'], true );
                    } else {
                        $product_id =  absint( $bump['dsrbfw_ob_ac-product-id'] );
                    }
        
                    $product = is_numeric( $product_id ) ? wc_get_product( $product_id ) : $product_id;
                    $variation_id = $product->is_type( 'variation' ) ? $product->get_id() : null;
                    $product_id   = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();

                    if( $product_id ) {
                        
                        $action = $bump['dsrbfw_ob_ac-bump-action'] ?: '';
        
                        if ( $action === 'add' ) {
        
                            $dsrbfw_meta_data = array(
                                "$this->ob_ac_cart_meta_key" => absint( $bump_id ),
                            );
                            
                            WC()->cart->add_to_cart( $product_id, 1, $variation_id, $variation_data, $dsrbfw_meta_data );
                        } elseif( $action === 'remove' ) {
                            
                            foreach ( WC()->cart->get_cart() as $key => $cart_item ) {
                                if ( isset( $cart_item[ $this->ob_ac_cart_meta_key ] ) && $cart_item[ $this->ob_ac_cart_meta_key ] === $bump_id ) {
                                    WC()->cart->remove_cart_item( $key );
                                }
                            }
                        }
                    }
                }       
            }
        }
    }

    /**
	 * Prepend offer text to cart item. It will help to differentiate offer product in cart.
	 *
	 * @param string $product_name  Product Name.
	 * @param array  $cart_item     Cart Item.
     * 
	 * @return string
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_prepend_text_to_cart_item( $product_name, $cart_item ){

        if (  ! isset( $cart_item[$this->ob_ac_cart_meta_key] ) || ! $cart_item[$this->ob_ac_cart_meta_key] ) {
			return $product_name;
		}

		$product_name_text = wp_strip_all_tags( $product_name );

		// Translators: Product Name.
        $product_name_new = sprintf( esc_html__( '(Addon) %s', 'revenue-booster-for-woocommerce' ), $product_name_text );

		// If html.
		if ( $product_name !== $product_name_text ) {
			$product_name = str_replace( $product_name_text, $product_name_new, $product_name );
		} else {
			$product_name = $product_name_new;
		}

		return $product_name;

    }

    /**
     * This function is use to display FBT main product title for linked product in cart
     * 
     * @param array $cart_item Cart item data
     * 
     * @since 1.0.0
     */
    public function dsrbfw_cart_item_sub_title( $cart_item ){
        
        if( ! isset( $cart_item['dsrbfw_fbt'] ) || ! $cart_item['dsrbfw_fbt'] ) {
            return;
        }

        //If customer dont want to show linked product in cart then return change
        $show_linked_product = apply_filters( 'dsrbfw_show_linked_fbt_main_product', true, $cart_item );

        if( $show_linked_product ) {
            $product_id = $cart_item['dsrbfw_fbt'];
            $product = wc_get_product( $product_id );
            ?>
            <div class="fbt_linked_product">
                <strong><?php esc_html_e( 'FBT product', 'revenue-booster-for-woocommerce' ); ?>:</strong>
                <a href="<?php echo esc_url( $product->get_permalink() ); ?>" target="_blank" ><?php echo esc_html( $product->get_name() ); ?></a>
            </div>
            <?php
        }
    }

    /**
     * This function is use to display quantity so it will use after free product auto add to cart implementation
     * 
     * @since 1.0.0
     */
    public function dsrbfw_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
        
        //If not editable means addon product then return quantity as it is
        if( ! $this->is_cart_item_quantity_editable( $cart_item ) ) {
            $product_quantity = sprintf( '%2$s <input type="hidden" name="cart[%1$s][qty]" value="%2$s" />', $cart_item_key, $cart_item['quantity'] );
        }

        return $product_quantity;
    }

    /**
	 * Prevent updating the product offer quantity.
	 *
	 * @param bool   $passed_validation The cart validation.
	 * @param string $cart_item_key     The cart key.
	 * @param array  $cart_item         The cart item.
	 * @param int    $quantity          The new quantity value.
	 * @return bool
     * 
     * @since 1.0.0
	 */
	public function dsrbfw_prevent_updating_addon_product_quantity( $passed_validation, $cart_item_key, $cart_item, $quantity ) {

		if ( $this->is_cart_item_quantity_editable( $cart_item ) ) {
			return $passed_validation;
		}

		if ( $quantity > 1 ) {
			return false;
		}

		return $passed_validation;
	}

    /**
     * Check either cart item quantity is editable or not. Here we 
     * checked our addon key exist in cart and then if key contain bump 
     * rule id which we need to ensure that is active or not.
     *
     * @param array $cart_item The cart item.
     * @return bool
     * 
     * @since 1.0.0
     */
    public function is_cart_item_quantity_editable( $cart_item ) {

        // If key not exist then item quantity editable
        if( ! array_key_exists( $this->ob_ac_cart_meta_key, $cart_item ) ) {
            return true;
        }

        // If key exist and rule is not active then cart item quantity is not editable
        if( $this->is_order_bump_rule_exists( $cart_item[ $this->ob_ac_cart_meta_key ], DSRBFW_BEFORE_OB_POST_TYPE ) ) {
            return false;
        }

        return true;
    }

    /**
     * Check order bump rule is exists or not.
     *
     * @param array $cart_item The cart item.
     * @param string $bump_type bump tule post type.
     * @return bool return true if rule exist else false
     * 
     * @since 1.0.0
     */
    public function is_order_bump_rule_exists( $bump_rule_id, $bump_type ) {
        
        global $wpdb;

        // Query to check for any published posts of the given custom post types
        $query = $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE ID = %d AND post_status = 'publish' AND post_type IN (%s) LIMIT 1",
            $bump_rule_id,
            $bump_type
        );
        $result = $wpdb->get_var($query); // phpcs:ignore
        
        // Check if any published posts exist
        return !empty($result);
    }
    
    /**
     * Add discount data to order item meta 
     * 
     * @since 1.0.0
     */
    public function dsrbfw_add_values_to_order_item_meta( $item, $cart_item_key, $values ) {

        if( isset( $values[$this->ob_ac_cart_meta_key] ) ) {

            $item->update_meta_data( '_'.$this->ob_ac_cart_meta_key, $values[$this->ob_ac_cart_meta_key] );
        }
        if( isset( $values[$this->ob_bo_cart_meta_key] ) ) {

            $item->update_meta_data( '_'.$this->ob_bo_cart_meta_key, $values[$this->ob_bo_cart_meta_key] );
        }
    }

    /**
     * This function is use to add "Addon" label before product name after order placed and we can see it on order summary page.
     * 
     * @since 1.0.0
     */
    public function dsrbfw_prepend_text_to_order_item_name( $product_name, $item ){

        $item_metas = $item->get_formatted_meta_data( '', true );
        if( !empty( $item_metas ) ){
            foreach( $item_metas as $item_meta ){
                if( '_'.$this->ob_ac_cart_meta_key === $item_meta->key ) {
                    $product_name = wp_kses_post( sprintf( '<span class="addon-product">%s</span> - %s', esc_html__( 'Addon', 'revenue-booster-for-woocommerce' ), $product_name ) );
                }
                if( '_'.$this->ob_bo_cart_meta_key === $item_meta->key ) {
                    $product_name = wp_kses_post( sprintf( '<span class="extra-product">%s</span> - %s', esc_html__( 'Extra', 'revenue-booster-for-woocommerce' ), $product_name ) );
                }
            }
        }
        return $product_name;
    }

    /** 
     * Append CSS to all the email for BOGO tag
     * 
     * @since 1.0.0
     */
    public function dsrbfw_append_css_to_emails( $style_return ){
        $style_return .= '
            span.addon-product {
                background: dodgerblue;
                color: #fff;
                padding: 0px 5px 2px;
                border-radius: 5px;
            }
            span.extra-product {
                background: dodgerblue;
                color: #fff;
                padding: 0px 5px 2px;
                border-radius: 5px;
            }
        ';
        return $style_return;
    }

    /**
	 * Add Sales Booster data to the wc/store/cart/items Store API endpoint.
	 *
	 * @return void
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_block_prepend_text_to_cart_item() {
        // We have use _ in namespce becasuse we are using it in JS and JS does not support hyphen in variable name
        woocommerce_store_api_register_endpoint_data(
			array(
				'endpoint'        => Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema::IDENTIFIER,
				'namespace'       => 'dotstore_revenue_booster',
				'data_callback'   => function( $cart_item ) {
					return array(
						'added_via_dotstore_ob_ac' => ! empty( $cart_item[ $this->ob_ac_cart_meta_key ] ),
					);
				},
				'schema_callback' => function() {
					return array(
						'added_via_dotstore_ob_ac' => array(
							'description' => esc_html__( 'Whether a product was added via at checkout bump.', 'revenue-booster-for-woocommerce' ),
							'type'        => 'boolean',
							'readonly'    => true,
						),
					);
				},
				'schema_type'     => ARRAY_A,
			)
		);
    }

    /**
	 * Update the cart item quantity editable.
	 *
	 * `editable` is part of quantity limits.
	 *
	 * @param bool       $is_editable Whether is editable.
	 * @param WC_Product $product     The product.
	 * @param array      $cart_item   The cart item.
	 * @return bool
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_block_prevent_updating_addon_product_quantity( $is_editable, $product, $cart_item) {
        
        if ( $this->is_cart_item_quantity_editable( $cart_item ) ) {
			return $is_editable;
		}

		return false;
    }

    /**
     * Perform action on at checkout order bump product add/remove action (Block Checkout)
     * 
     * @since 1.0.0
     */
    public function dsrbfw_block_add_remove_bump_at_checokut() {
        woocommerce_store_api_register_update_callback(
			array(
				'namespace' => 'dotstore-revenue-booster-ac-bump',
				'callback'  => function( $data ) {

                    // Check if the data is empty or not
                    if ( ! isset( $data['dsrbfw_ob_ac-bump-data'] ) || empty( $data['dsrbfw_ob_ac-bump-data'] ) ) {
						return;
					}

					$this->dsrbfw_add_remove_bump_at_checkout( http_build_query($data) );
				},
			)
		);
    }

    /**
     * Perform action on before order bump product add/remove action (Block Checkout)
     * 
     * @since 1.0.0
     */
    public function dsrbfw_block_add_remove_bump_before_order() {
        woocommerce_store_api_register_update_callback(
			array(
				'namespace' => 'dotstore-revenue-booster-bo-bump',
				'callback'  => function( $data ) {
                    
                    // Check if the data is empty or not
                    if ( ! isset( $data['dsrbfw_ob_bo-bump-data'] ) || empty( $data['dsrbfw_ob_bo-bump-data'] ) ) {
						return;
					}

					$this->dsrbfw_add_bump_before_order( http_build_query($data) );
				},
			)
		);
    }

    /**
	 * Add `data-value` attribute to the variation select option elements.
	 *
	 * Although we use the `wc_dropdown_variation_attribute_options` function
	 * to output the select element, we need to add the `data-value` attribute
	 * because when we output the select element in the Checkout block,
	 * the `value` attribute is removed. It happens on the frontend. It
	 * seems happen as a part of a sanitization process.
	 *
	 * So, we use the `data-value` attribute to re-add the `value` attribute
	 * in the frontend.
	 *
	 * @param string $html The select element HTML markup.
	 * @param array  $args The variation attribute options args.
	 * @return mixed The html markup.
     * 
     * @since 1.0.0
	 */
	public function add_data_value_attribute_to_option_elements( $html, $args ) {
		if ( ! is_string( $html ) ) {
			return $html;
		}

		if ( empty( $args['class'] ) ) {
			return $html;
		}

		if ( ! str_contains( $args['class'], 'dsrbfw_ob_ac__variation-select') ) {
			return $html;
		}

		if ( empty( $args['options'] ) || ! is_array( $args['options'] ) ) {
			return $html;
		}

		$search  = array( 'value=""' );
		$replace = array( 'value="" data-value=""' );
		foreach ( $args['options'] as $value ) {
			if ( ! is_string( $value ) ) {
				continue;
			}

			$search[]  = 'value="' . $value . '"';
			$replace[] = 'value="' . $value . '" data-value="' . $value . '"';
		}

		$html_with_replaced_values = str_replace(
			$search,
			$replace,
			$html
		);

		if ( empty( $html_with_replaced_values ) || ! is_string( $html_with_replaced_values ) ) {
			return $html;
		}

		return $html_with_replaced_values;
	}

    /**
	 * Get Active Order Bumps Before Order.
	 *
	 * @return WP_Post[]
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_get_ob_bo_rules() {

        $bumps = new WP_Query(
            array(
                'post_per_pages' => - 1,
                'post_type'     => DSRBFW_AFTER_OB_POST_TYPE,
                'post_status'   => 'publish',
                'orderby'     => 'date',
                'order'       => 'ASC',
            )
        );

        return $bumps->posts ? $bumps->posts : array();
    }

    /**
     * Prepare list of valid order bump before order offer ids
     * 
     * @return array|bool
     * 
     * @since 1.0.0
     */
    public function dsrbfw_get_valid_ob_bo_offer_id() {

        // Get all order bump at checkout rules
        $ob_bo_rules = $this->dsrbfw_get_ob_bo_rules();
        
        if( !empty( $ob_bo_rules ) ) {

            $return_ob_ids = array();

            foreach( $ob_bo_rules as $ob_bo_rule ) {
                
                $ob_bo_id = isset( $ob_bo_rule->ID ) && !empty( $ob_bo_rule->ID ) ? absint( $ob_bo_rule->ID ) : 0; 
                
                if( $ob_bo_id > 0 ) {
                    $ob_bo_offer_product_id = get_post_meta( $ob_bo_id, 'dsrbfw_ob_bo_product', true );

                    if( $ob_bo_offer_product_id > 0 ) {

                        // Get offer product object for current order bump at checkout rule
                        $ob_bo_offer_product = wc_get_product( $ob_bo_offer_product_id );

                        // Check offer product is valid or not
                        if( $this->is_valid_products( $ob_bo_offer_product ) ) {
                            
                            //check if product is already in cart
                            if( ! $this->dsrbfw_is_product_as_offer_in_cart( $ob_bo_offer_product_id ) ) {

                                $return_ob_ids[] = absint( $ob_bo_id ); 
                            }
                        }
                    }
                }
            }

            return $return_ob_ids;
        }

        return false;
    }

    /**
	 * Render hidden fields for bump at checkout. Used for adding to cart bump offer product
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_render_before_order_modal_html() {

        // Get all valid order bump before order offer ids
        $ob_bo_ids = $this->dsrbfw_get_valid_ob_bo_offer_id();

        // Filter out our rule with conditions
        $ob_bo_ids = ( $ob_bo_ids && is_array( $ob_bo_ids ) ) ? array_filter( array_map(
            function ( $id ) {
                if( $this->dsrbfw_order_bump_offer_condition( $id ) ) {
                    return $id;
                }
            },
            $ob_bo_ids
        ) ) : array();

        if( $ob_bo_ids ) {

            ?>
            <div class="dsrbfw_ob_bo_checkout_data">
                <?php
                foreach( $ob_bo_ids as $ob_bo_id ) {
                    $product_id = absint( get_post_meta( $ob_bo_id, 'dsrbfw_ob_bo_product', true ) );
                    $product = wc_get_product($product_id);

                    // Check if product is a variation with all attributes are set
                    $complete_product_data = true;
                    if( $product->is_type('variable') || $product->is_type('variation') ) {
                        if( !empty( $product->get_attributes() ) ) {
                            foreach( $product->get_attributes() as $element ){
                                if ( $element instanceof WC_Product_Attribute || empty( $element ) ) {
                                    $complete_product_data = false;
                                    break;
                                }
                            }
                        }
                    }
                    $product_id   = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
                    $variation_id = $product->is_type( 'variation' ) ? $product->get_id() : null;
                    $variation_data = null;
                    if( $complete_product_data && $product->is_type( 'variation' ) ) {
                        $variation_data =  wp_json_encode( $product->get_variation_attributes() ) ;
                    }
                    ?>
                    <input type="hidden" name="dsrbfw_ob_bo-bump-data[<?php echo esc_attr( $ob_bo_id ); ?>][dsrbfw_ob_bo-bump-action]" class="dsrbfw_ob_bo_bump_action-<?php echo esc_attr( $ob_bo_id ); ?>" value="" />
                    <input type="hidden" name="dsrbfw_ob_bo-bump-data[<?php echo esc_attr( $ob_bo_id ); ?>][dsrbfw_ob_bo-product-id]" class="dsrbfw_ob_bo_product_id-<?php echo esc_attr( $ob_bo_id ); ?>" value="<?php echo esc_attr( $product_id ); ?>" />
                    <input type="hidden" name="dsrbfw_ob_bo-bump-data[<?php echo esc_attr( $ob_bo_id ); ?>][dsrbfw_ob_bo-variation-id]" class="dsrbfw_ob_bo_variation_id-<?php echo esc_attr( $ob_bo_id ); ?>" value="<?php echo esc_attr( $variation_id ); ?>" />
                    <input type="hidden" name="dsrbfw_ob_bo-bump-data[<?php echo esc_attr( $ob_bo_id ); ?>][dsrbfw_ob_bo-variation-data]" class="dsrbfw_ob_bo_variation_data-<?php echo esc_attr( $ob_bo_id ); ?>" value="<?php echo esc_attr( $variation_data ); ?>" />
                    <?php
                } ?>
            </div>
            <?php

            // Now we are appending the html for order bump popup after checkout form
            add_action(
                'woocommerce_after_checkout_form',
                function () use ( $ob_bo_ids ) {
                    $this->dsrbfw_prepare_data_and_html_for_ob_bo_modal( $ob_bo_ids );                    
                }
            );
        }
    }

    /**
	 * Add After Checkout modal after WooCommerce checkout block.
	 *
	 * @param string $block_content The block content.
	 * @return string
     * 
     * @sicne 1.0.0
	 */
    public function dsrbfw_add_popup_after_checkout_block( $block_content ) {
        global $post;

		if ( ! has_block( 'woocommerce/checkout', $post ) ) {
			return $block_content;
		}

        $ob_bo_ids = $this->dsrbfw_get_valid_ob_bo_offer_id();

        // Filter out our rule with conditions
        $ob_bo_ids = ( $ob_bo_ids && is_array( $ob_bo_ids ) ) ? array_filter( array_map(
            function ( $id ) {
                if( $this->dsrbfw_order_bump_offer_condition( $id ) ) {
                    return $id;
                }
            },
            $ob_bo_ids
        ) ) : array();

        ob_start();
        if( $ob_bo_ids ) {

            ?>
            <div class="dsrbfw_ob_bo_checkout_data">
                <?php
                foreach( $ob_bo_ids as $ob_bo_id ) {
                    $product_id = absint( get_post_meta( $ob_bo_id, 'dsrbfw_ob_bo_product', true ) );
                    $product = wc_get_product($product_id);

                    // Check if product is a variation with all attributes are set
                    $complete_product_data = true;
                    if( $product->is_type('variable') || $product->is_type('variation') ) {
                        if( !empty( $product->get_attributes() ) ) {
                            foreach( $product->get_attributes() as $element ){
                                if ( $element instanceof WC_Product_Attribute || empty( $element ) ) {
                                    $complete_product_data = false;
                                    break;
                                }
                            }
                        }
                    }
                    $product_id   = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
                    $variation_id = $product->is_type( 'variation' ) ? $product->get_id() : null;
                    $variation_data = null;
                    if( $complete_product_data && $product->is_type( 'variation' ) ) {
                        $variation_data =  wp_json_encode( $product->get_variation_attributes() ) ;
                    }
                    ?>
                    <input type="hidden" name="dsrbfw_ob_bo-bump-data[<?php echo esc_attr( $ob_bo_id ); ?>][dsrbfw_ob_bo-bump-action]" class="dsrbfw_ob_bo_bump_action-<?php echo esc_attr( $ob_bo_id ); ?>" value="" />
                    <input type="hidden" name="dsrbfw_ob_bo-bump-data[<?php echo esc_attr( $ob_bo_id ); ?>][dsrbfw_ob_bo-product-id]" class="dsrbfw_ob_bo_product_id-<?php echo esc_attr( $ob_bo_id ); ?>" value="<?php echo esc_attr( $product_id ); ?>" />
                    <input type="hidden" name="dsrbfw_ob_bo-bump-data[<?php echo esc_attr( $ob_bo_id ); ?>][dsrbfw_ob_bo-variation-id]" class="dsrbfw_ob_bo_variation_id-<?php echo esc_attr( $ob_bo_id ); ?>" value="<?php echo esc_attr( $variation_id ); ?>" />
                    <input type="hidden" name="dsrbfw_ob_bo-bump-data[<?php echo esc_attr( $ob_bo_id ); ?>][dsrbfw_ob_bo-variation-data]" class="dsrbfw_ob_bo_variation_data-<?php echo esc_attr( $ob_bo_id ); ?>" value="<?php echo esc_attr( $variation_data ); ?>" />
                    <?php
                } ?>
            </div>
            <?php

            // Now we are appending the html for order bump popup after checkout form
            $this->dsrbfw_prepare_data_and_html_for_ob_bo_modal( $ob_bo_ids );
        }
        $after_checkout_bump_html = ob_get_clean();

		return $block_content . $after_checkout_bump_html;
    }

    /**
     * Prepare data for order bump before order to show in modal
     * 
     * @param array $bump_ids[]  Order bump before order ids
     * 
     * @since 1.0.0
     */
    public function dsrbfw_prepare_data_and_html_for_ob_bo_modal( $bump_ids ) {

        // Order Bump at Checkout HTML
        dsrbfw()->include_template( 'public/partials/revenue-booster-for-woocommerce-order-bump-before-order.php', array(
            'bump_ids'           => $bump_ids,
        ) );
    }

    /**
	 * Handle ajax when on checkout trigger update_checkout event
	 *
	 * @param string $post_data Checkout post data.
     * 
     * @since 1.0.0
	 */
    public function dsrbfw_add_bump_before_order( $post_data ) {
        $data = array();
		parse_str( $post_data, $data );

        if( isset( $data['dsrbfw_ob_bo-bump-data'] ) && !empty( $data['dsrbfw_ob_bo-bump-data'] ) ) {

            $bump_data = $data['dsrbfw_ob_bo-bump-data'];
            foreach( $bump_data as $bump_id => $bump ) {
                
                if ( ! empty( $bump['dsrbfw_ob_bo-bump-action'] ) && in_array( $bump['dsrbfw_ob_bo-bump-action'], array( 'add', 'remove' ), true ) && ! empty( $bump_id ) ) {
                    
                    if ( ! $this->is_order_bump_rule_exists( $bump_id, DSRBFW_AFTER_OB_POST_TYPE ) ) {
                        continue;
                    }

                    $variation_data = null;
                    
                    if ( isset( $bump['dsrbfw_ob_bo-variation-id'] ) && $bump['dsrbfw_ob_bo-variation-id'] ) {
                        $product_id =  absint( $bump['dsrbfw_ob_bo-variation-id'] );
                        $variation_data = json_decode( $bump['dsrbfw_ob_bo-variation-data'], true );
                    } else {
                        $product_id =  absint( $bump['dsrbfw_ob_bo-product-id'] );
                    }
        
                    $product = is_numeric( $product_id ) ? wc_get_product( $product_id ) : $product_id;
                    $variation_id = $product->is_type( 'variation' ) ? $product->get_id() : null;
                    $product_id   = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
                    
                    if( $product_id ) {
                        $action = $bump['dsrbfw_ob_bo-bump-action'] ?: '';
                        if( 'add' === $action ) {
                            $dsrbfw_meta_data = array(
                                "$this->ob_bo_cart_meta_key" => absint( $bump_id ),
                            );
                            
                            WC()->cart->add_to_cart( $product_id, 1, $variation_id, $variation_data, $dsrbfw_meta_data );
                        } else {
                            foreach ( WC()->cart->get_cart() as $key => $cart_item ) {
                                if ( isset( $cart_item[ $this->ob_bo_cart_meta_key ] ) && $cart_item[ $this->ob_bo_cart_meta_key ] === $bump_id ) {
                                    WC()->cart->remove_cart_item( $key );
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

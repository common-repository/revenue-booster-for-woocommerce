<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://thedotstore.com/
 * @since      1.0.0
 *
 * @package    Revenue_Booster_For_Woocommerce
 * @subpackage Revenue_Booster_For_Woocommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Revenue_Booster_For_Woocommerce
 * @subpackage Revenue_Booster_For_Woocommerce/includes
 * @author     theDotstore <support@thedotstore.com>
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Automattic\WooCommerce\Utilities\OrderUtil;

defined( 'ABSPATH' ) or exit;

if( ! class_exists( 'Revenue_Booster_For_Woocommerce' ) ) {
    class Revenue_Booster_For_Woocommerce {

        /**
         * The loader that's responsible for maintaining and registering all hooks that power
         * the plugin.
         *
         * @since    1.0.0
         * @access   protected
         * @var      Revenue_Booster_For_Woocommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
         */
        protected $loader;

        /**
         * The unique identifier of this plugin.
         *
         * @since    1.0.0
         * @access   protected
         * @var      string    $plugin_name    The string used to uniquely identify this plugin.
         */
        protected $plugin_name;

        /**
         * The current version of the plugin.
         *
         * @since    1.0.0
         * @access   protected
         * @var      string    $version    The current version of the plugin.
         */
        protected $version;

        /** 
         * The currenct instance of the plugin
         * 
         * @since   1.0.0
         * @access  protected
         * @var     \Revenue_Booster_For_Woocommerce single instance of this plugin 
         */
        protected static $instance;

        /**
         * Define the core functionality of the plugin.
         *
         * Set the plugin name and the plugin version that can be used throughout the plugin.
         * Load the dependencies, define the locale, and set the hooks for the admin area and
         * the public-facing side of the site.
         *
         * @since    1.0.0
         */
        public function __construct() {
            if ( defined( 'DSRBFW_PLUGIN_VERSION' ) ) {
                $this->version = DSRBFW_PLUGIN_VERSION;
            } else {
                $this->version = '1.0.0';
            }
            $this->plugin_name = 'revenue-booster-for-woocommerce';

            $this->load_dependencies();
            $this->set_locale();
            $this->define_admin_hooks();
            $this->define_public_hooks();

            // Add plugin action links for plugin listing page
            $prefix = is_network_admin() ? 'network_admin_' : '';
            add_filter( "{$prefix}plugin_action_links_" . DSRBFW_PLUGIN_BASENAME, [ $this, 'dsrbfw_plugin_action_links' ], 20 );

            add_filter( 'plugin_row_meta', array( $this, 'dsrbfw_filter_plugin_row_meta' ), 10, 2 );

            // HPOS & Block Cart/Checkout Compatibility declare
            add_action( 'before_woocommerce_init', [ $this, 'dsrbfw_handle_features_compatibility' ] );

        }

        /**
         * Load the required dependencies for this plugin.
         *
         * Include the following files that make up the plugin:
         *
         * - Revenue_Booster_For_Woocommerce_Loader. Orchestrates the hooks of the plugin.
         * - Revenue_Booster_For_Woocommerce_i18n. Defines internationalization functionality.
         * - Revenue_Booster_For_Woocommerce_Admin. Defines all hooks for the admin area.
         * - Revenue_Booster_For_Woocommerce_Public. Defines all hooks for the public side of the site.
         *
         * Create an instance of the loader which will be used to register the hooks
         * with WordPress.
         *
         * @since    1.0.0
         * @access   private
         */
        private function load_dependencies() {

            /**
             * The class responsible for orchestrating the actions and filters of the
             * core plugin.
             */
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revenue-booster-for-woocommerce-loader.php';

            /**
             * The class responsible for defining internationalization functionality
             * of the plugin.
             */
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-revenue-booster-for-woocommerce-i18n.php';

            /**
             * The class responsible for defining all actions that occur in the admin area.
             */
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-revenue-booster-for-woocommerce-admin.php';

            /**
             * The class responsible for defining all actions that occur in the public-facing
             * side of the site.
             */
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-revenue-booster-for-woocommerce-public.php';

            $this->loader = new Revenue_Booster_For_Woocommerce_Loader();

        }

        /**
         * Define the locale for this plugin for internationalization.
         *
         * Uses the Revenue_Booster_For_Woocommerce_i18n class in order to set the domain and to register the hook
         * with WordPress.
         *
         * @since    1.0.0
         * @access   private
         */
        private function set_locale() {

            $plugin_i18n = new Revenue_Booster_For_Woocommerce_i18n();

            // We are directly call it instead of hook because already this class is load thorugh hook
            $plugin_i18n->load_plugin_textdomain();

        }

        /**
         * Register all of the hooks related to the admin area functionality
         * of the plugin.
         *
         * @since    1.0.0
         * @access   private
         */
        private function define_admin_hooks() {

            $page         = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $plugin_admin = new Revenue_Booster_For_Woocommerce_Admin( $this->get_plugin_name(), $this->get_version() );

            $this->loader->add_action( 'admin_menu', $plugin_admin, 'dsrbfw_dot_store_menu' );
            $this->loader->add_action( 'admin_head', $plugin_admin, 'dsrbfw_remove_admin_submenus' );
            $this->loader->add_filter( 'set-screen-option', $plugin_admin, 'dsrbfw_set_screen_options', 10, 3 );
            
            
            $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
            $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
            $this->loader->add_action( 'admin_print_styles', $plugin_admin, 'dsrbfw_admin_inline_css' );
            
            if ( ! empty( $page ) && ( false !== strpos( $page, 'dsrbfw' ) || false !== strpos( $page, 'revenue-booster-for-woocommerce' ) ) ) {
                $this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'dsrbfw_admin_footer_review' );
            }

            
            $this->loader->add_filter( 'woocommerce_json_search_found_products', $plugin_admin, 'dsrbfw_json_search_found_products' );

            /**
             * Admin AJAX Calls
             */
            // Get product data
            $this->loader->add_action( 'wp_ajax_dsrbfw_json_search_products', $plugin_admin, 'dsrbfw_json_search_products_callback' );

            // Get category data
            $this->loader->add_action( 'wp_ajax_dsrbfw_json_search_categories', $plugin_admin, 'dsrbfw_json_search_categories_callback' );

            // Status change from list
            $this->loader->add_action( 'wp_ajax_dsrbfw_change_status_from_list', $plugin_admin, 'dsrbfw_change_status_from_list_callback' );


            /**
             * Classic Product Editor
             */
            $this->loader->add_filter( 'woocommerce_product_data_tabs', $plugin_admin, 'dsrbfw_product_data_tab' );
            $this->loader->add_action( 'woocommerce_product_data_panels', $plugin_admin, 'dsrbfw_product_data_tab_content' );
            $this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'dsrbfw_save_product_data_tab_content' );

            /**
             * Product Block Editor
             */
            $this->loader->add_action( 'woocommerce_block_template_area_product-form_after_add_block_general', $plugin_admin, 'dsrbfw_add_revenue_booster_group' );

            // If we make this to 'init' hook then REST API works but on save rule it throw error.
            $this->loader->add_action( 'init', $plugin_admin, 'dsrbfw_post_type_define' );
            $this->loader->add_action( 'admin_notices', $plugin_admin, 'dsrbfw_display_action_message' );

            $this->loader->add_filter( 'woocommerce_hidden_order_itemmeta', $plugin_admin, 'dsrbfw_hidden_order_itemmeta', 10, 1 );
            $this->loader->add_action( 'woocommerce_before_order_itemmeta', $plugin_admin, 'dsrbfw_before_order_itemmeta', 10, 2 );
        }

        /**
         * Register all of the hooks related to the public-facing functionality
         * of the plugin.
         *
         * @since    1.0.0
         * @access   private
         */
        private function define_public_hooks() {

            $plugin_public = new Revenue_Booster_For_Woocommerce_Public( $this->get_plugin_name(), $this->get_version() );

            $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
            $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

            if ( ! is_admin() ) {
                $this->loader->add_action( 'init', $plugin_public, 'dsrbfw_fbt_callback' );
            }

            /**
             * Public AJAX Calls
             */
            $ajax_events_nopriv = array(
                'get_variation_price',
                'update_final_price',
            );
            foreach ( $ajax_events_nopriv as $ajax_event ) {
                $this->loader->add_action( 'wp_ajax_dsrbfw_' . $ajax_event, $plugin_public, 'dsrbfw_' . $ajax_event . '_callback' );
                $this->loader->add_action( 'wp_ajax_nopriv_dsrbfw_' . $ajax_event, $plugin_public, 'dsrbfw_' . $ajax_event . '_callback' );
            }

            /**
             * Frequently Bought Together Module
             */
            // Add FBT product to cart
            $this->loader->add_action( 'wp_loaded', $plugin_public, 'dsrbfw_add_to_cart_action', 11 );

            // Apply disacount on FBT product in cart
            $this->loader->add_action( 'woocommerce_cart_calculate_fees', $plugin_public, 'dsrbfw_add_fbt_discount' );

            $this->loader->add_action( 'dsrbfw_before_get_cart_fragments_for_ajax_fbt_add_to_cart', $plugin_public, 'dsrbfw_add_message_to_the_cart_fragments', 10, 2 );

            //To display the custom field 'FBT' below the product title on cart page
            $this->loader->add_action( 'woocommerce_after_cart_item_name', $plugin_public, 'dsrbfw_cart_item_sub_title', 10, 1 );

            /**
             * Customer Also Bought Module
             */
            // Add custom attribute for AJAX add to cart for CAB products
            $this->loader->add_filter( 'woocommerce_loop_add_to_cart_args', $plugin_public, 'dsrbfw_add_to_cart_button_attributes', 10, 2 );

            // Show CAB product in cart after AJAX add to cart
            $this->loader->add_filter( 'woocommerce_add_to_cart_fragments', $plugin_public, 'dsrbfw_add_after_add_to_cart_popup_in_cart_fragments' );
            
            // We need to make placeholder for popup in footer which will use in cart fragments
            $this->loader->add_action( 'wp_footer', $plugin_public, 'dsrbfw_add_after_add_to_cart_popup_placeholder' );

            // We need to prepare popup content for CAB product which will use on product details page (for reload add to cart)
            $this->loader->add_action( 'wp_footer', $plugin_public, 'dsrbfw_show_after_add_to_cart_popup', 10);

            // We need to set cart flag on which basis we will show poup after add to cart (for reload add to cart)
            $this->loader->add_action( 'woocommerce_add_to_cart', $plugin_public, 'dsrbfw_handle_show_after_add_to_cart_popup', 10, 2 );

            /**
             * Order Bump at Checkout Module
             */
            // Show order bump HTML on Checkout Load.
            $this->loader->add_action( 'template_redirect', $plugin_public, 'dsrbfw_show_order_bump_at_checkout' );

            // Render order bump HTML on AJAX.
            $this->loader->add_action( 'woocommerce_checkout_update_order_review', $plugin_public, 'dsrbfw_show_order_bump_at_checkout', 99 );

            // Add/Remove order bump product on AJAX.
            $this->loader->add_action( 'woocommerce_checkout_update_order_review', $plugin_public, 'dsrbfw_add_remove_bump_at_checkout' );

            // Append offer text to cart item.
            $this->loader->add_filter( 'woocommerce_cart_item_name', $plugin_public, 'dsrbfw_prepend_text_to_cart_item' , 52, 2 );

            // Disable quantity update for Addon products
            $this->loader->add_filter( 'woocommerce_cart_item_quantity', $plugin_public, 'dsrbfw_cart_item_quantity', 10, 3 );

            // Prevent updating product offer quantity
            $this->loader->add_filter( 'woocommerce_update_cart_validation', $plugin_public, 'dsrbfw_prevent_updating_addon_product_quantity', 101, 4 );

            // Add order item meta to order for order bump product differentiation (deprecated: woocommerce_add_order_item_meta)
            $this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $plugin_public, 'dsrbfw_add_values_to_order_item_meta', 20, 3 );
            $this->loader->add_filter( 'woocommerce_order_item_name', $plugin_public, 'dsrbfw_prepend_text_to_order_item_name', 10, 2 );

            // Add order bump product to order to mail also
            $this->loader->add_filter( 'woocommerce_email_styles', $plugin_public, 'dsrbfw_append_css_to_emails', 9999, 1 );

            // If Rule deactivate then item must be removed from cart if customer added it before deactivation
            $this->loader->add_action( 'template_redirect', $plugin_public, 'dsrbfw_remove_order_bump_on_rule_deactivation' );

            /** 
             * Block Cart & Checkout Compatibility
            */
            // Append "Addon" term to the product name in the cart and checkout
            $this->loader->add_action( 'woocommerce_blocks_loaded', $plugin_public, 'dsrbfw_block_prepend_text_to_cart_item' );

            //Block cart page remove quanity update for order bump product
            $this->loader->add_filter( 'woocommerce_store_api_product_quantity_editable', $plugin_public, 'dsrbfw_block_prevent_updating_addon_product_quantity', 101, 3 );

            // Add/Remove order bump product on block checkout page
            $this->loader->add_action( 'woocommerce_blocks_loaded', $plugin_public, 'dsrbfw_block_add_remove_bump_at_checokut' );

            $this->loader->add_filter( 'woocommerce_dropdown_variation_attribute_options_html', $plugin_public, 'add_data_value_attribute_to_option_elements', 50, 2 );

            /**
             * Order Bump Before Order Module
             */
            // Add order bump before order HTML on Checkout Load.
            $this->loader->add_action( 'woocommerce_review_order_before_submit', $plugin_public, 'dsrbfw_render_before_order_modal_html' );

            // Add/Remove order bump product on AJAX.
            $this->loader->add_action( 'woocommerce_checkout_update_order_review', $plugin_public, 'dsrbfw_add_bump_before_order' );

            // Add order bump before placing order popup for block checkout
            $this->loader->add_filter( 'render_block_woocommerce/checkout', $plugin_public, 'dsrbfw_add_popup_after_checkout_block' );

            // Add/Remove order bump product on block checkout page while placing order
            $this->loader->add_action( 'woocommerce_blocks_loaded', $plugin_public, 'dsrbfw_block_add_remove_bump_before_order' );
        }

        /**
         * Run the loader to execute all of the hooks with WordPress.
         *
         * @since    1.0.0
         */
        public function run() {
            $this->loader->run();
        }

        /**
         * The name of the plugin used to uniquely identify it within the context of
         * WordPress and to define internationalization functionality.
         *
         * @since     1.0.0
         * @return    string    The name of the plugin.
         */
        public function get_plugin_name() {
            return $this->plugin_name;
        }

        /**
         * The reference to the class that orchestrates the hooks with the plugin.
         *
         * @since     1.0.0
         * @return    Revenue_Booster_For_Woocommerce_Loader    Orchestrates the hooks of the plugin.
         */
        public function get_loader() {
            return $this->loader;
        }

        /**
         * Retrieve the version number of the plugin.
         *
         * @since     1.0.0
         * @return    string    The version number of the plugin.
         */
        public function get_version() {
            return $this->version;
        }

        /**
         * Declares plugin list page quick links.
         *
         * @since 1.0.0
         */
        public function dsrbfw_plugin_action_links( $actions ) {

            $custom_actions = array();
            
            if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
                // Define custom action links with appropriate URLs and labels.
                $custom_actions = array(
                    'configure' => sprintf(
                        '<a href="%s">%s</a>',
                        esc_url( add_query_arg( array( 'page' => 'dsrbfw-global-settings' ), admin_url( 'admin.php' ) ) ),
                        esc_html__( 'Settings', 'revenue-booster-for-woocommerce' )
                    ),
                );
            }

            // Merge the custom action links with the existing action links.
            return array_merge( $custom_actions, $actions );
        }

        /**
         * Filters the array of row meta for each plugin in the Plugins list table.
         *
         * @param array     $plugin_meta    An array of the plugin's metadata.
         * @param string    $plugin_file    Path to the plugin file relative to the plugins directory.
         * @return array                    Updated array of the plugin's metadata.
         * 
         * @since 1.0.0
         */
        public function dsrbfw_filter_plugin_row_meta( array $plugin_meta, $plugin_file ) {
            if ( DSRBFW_PLUGIN_BASENAME !== $plugin_file ) {
                return $plugin_meta;
            }

            $row_meta = [
                'docs'    => sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    esc_url( DSRBFW_DOC_LINK ),
                    esc_html__( 'Docs', 'revenue-booster-for-woocommerce' )
                ),
                'support' => sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    esc_url( 'https://www.thedotstore.com/support' ),
                    esc_html__( 'Support', 'revenue-booster-for-woocommerce' )
                ),
            ];
            return array_merge( $plugin_meta, $row_meta );
        }

        /**
         * Declares compatibility with specific WooCommerce features.
         *
         * @since 1.0.0
         */
        public function dsrbfw_handle_features_compatibility(){

            if ( ! class_exists( FeaturesUtil::class ) ) {
                return;
            }
            
            FeaturesUtil::declare_compatibility( 'custom_order_tables', DSRBFW_PLUGIN_BASENAME, true );
            FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', DSRBFW_PLUGIN_BASENAME, true );
        }

        /**
         * Allowed html tags used for wp_kses function
         *
         * @param array add custom tags (Not used)
         *
         * @return array
         * @since     1.0.0
         *
         */
        public static function dsrbfw_allowed_html_tags( ) {
            $allowed_tags = array(
                'a'        => array(
                    'href'         => array(),
                    'title'        => array(),
                    'class'        => array(),
                    'target'       => array(),
                    'data-tooltip' => array(),
                ),
                'ul'       => array( 'class' => array() ),
                'li'       => array( 'class' => array() ),
                'div'      => array( 'class' => array(), 'id' => array() ),
                'select'   => array(
                    'rel-id'            => array(),
                    'id'                => array(),
                    'name'              => array(),
                    'class'             => array(),
                    'multiple'          => array(),
                    'style'             => array(),
                    'data-width'        => array(),
                    'data-placeholder'  => array(),
                    'data-action'       => array(),
                    'data-sortable'     => array(),
                    'data-allow-clear'  => array(),
                    'data-width'        => array(),
                    'data-display_id'  => array(),
                ),
                'input'    => array(
                    'id'         => array(),
                    'value'      => array(),
                    'name'       => array(),
                    'class'      => array(),
                    'type'       => array(),
                    'data-index' => array(),
                ),
                'textarea' => array( 'id' => array(), 'name' => array(), 'class' => array() ),
                'option'   => array( 'id' => array(), 'selected' => array(), 'name' => array(), 'value' => array() ),
                'br'       => array(),
                'p'        => array(),
                'b'        => array( 'style' => array() ),
                'em'       => array(),
                'strong'   => array(),
                'i'        => array( 'class' => array() ),
                'span'     => array( 'class' => array(), 'style' => array() ),
                'small'    => array( 'class' => array() ),
                'label'    => array( 'class' => array(), 'id' => array(), 'for' => array() ),
            );
            return $allowed_tags;
        }

        public function dsrbfw_alloed_image_tags() {
            $allowed_tags = array(
                'img' => array(
                    'src'       => array(),
                    'alt'       => array(),
                    'title'     => array(),
                    'width'     => array(),
                    'height'    => array(),
                    'class'     => array(),
                    'decoding'  => array(),
                    'loading'   => array(),
                    'srcset'    => array(),
                    'sizes'     => array(),
                ),
            );
            return $allowed_tags;
        }

        /**
         * Get message text using type with redirect query parameter which will handle on `dsrbfw_display_action_message` action
         *
         * @param array add custom tags
         *
         * @return array
         * @since     1.0.0
         *
         */
        public function dsrbfw_updated_message( $message ) {

            if ( empty( $message ) ) {
                return false;
            }
            
            $query_args = array(
                'message' => $message,
            );

            if( isset($_SERVER['REQUEST_URI']) ) {
                $current_url    = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL, FILTER_VALIDATE_URL );
            }

            // Redirect to the current URL with the custom query parameter
            wp_safe_redirect( add_query_arg( $query_args, home_url( $current_url ) ) );
            exit();
        }

        /**
         * Gets the main Revenue Booster for WooCommerce instance.
         *
         * Ensures only one instance loaded at one time.
         *
         * @see \dsrbfw()
         *
         * @since 1.0.0
         *
         * @return \Revenue_Booster_For_Woocommerce
         */
        public static function instance() {

            if ( null === self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Include template with arguments
         *
         * @param string $__template
         * @param array  $__variables
         * 
         * @since   1.0.0
         */
        public function include_template( $__template, array $__variables = [] ) {

            $template_file = DSRBFW_PLUGIN_BASE_DIR . $__template;
            
            if ( file_exists( $template_file ) ) {
                extract( $__variables ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
                include $template_file;
            }
        }

        /**
         * Convert HEX color code to RGB color code
         *
         * @param string $hex_code
         * 
         * @return string  $red, $green, $blue - RGB format return
         * 
         * @since 1.0.0
         */
        public function dsrbfw_hex_to_rgb_convert( $hex_code ) {
            
            if( ! preg_match('/^#[a-f0-9]{6}$/i', $hex_code) ) {
                return;
            }

            list($red, $green, $blue) = sscanf($hex_code, "#%02x%02x%02x");

            if( $red !== null && $green !== null && $blue !== null) {
                return "$red, $green, $blue";
            }

            return false;
        }

        /**
         * Prepare rule filter array
         * 
         * @param array $post_data_array
         * 
         * @return array
         * 
         * @since 1.0.0
         */
        public function dsrbfw_prepare_rule_filter_array( $post_data_array ) {
            $dsrbfw_ob_conditions = array();
            if( !empty($post_data_array) ) {
                $dsrbfw_ob_conditions_values_array = array();
                $size = isset($post_data_array['dsrbfw_ob_condition']) && !empty($post_data_array['dsrbfw_ob_condition']) ? count($post_data_array['dsrbfw_ob_condition']) : 0;
                if( !empty( $post_data_array['dsrbfw_ob_values'] ) ) {
                    foreach ( $post_data_array['dsrbfw_ob_values'] as $v ) {
                        $dsrbfw_ob_conditions_values_array[] = $v;
                    }
                }
                for ( $i = 0; $i < $size; $i ++ ) {
                    if( !empty( $dsrbfw_ob_conditions_values_array[ $i ] ) ) {
                        $dsrbfw_ob_conditions[] = array(
                            'dsrbfw_ob_condition' => $post_data_array['dsrbfw_ob_condition'][ $i ],
                            'dsrbfw_ob_is'        => $post_data_array['dsrbfw_ob_is'][ $i ],
                            'dsrbfw_ob_values'    => $dsrbfw_ob_conditions_values_array[ $i ],
                        );
                    }
                }
            }
            return $dsrbfw_ob_conditions;
        }

    }
}

/**
 * Returns the One True Instance of Local Pickup WooCommerce class object.
 *
 * @since 1.0.0
 *
 * @return \Revenue_Booster_For_Woocommerce
 */
if( ! function_exists( 'dsrbfw' ) ) {
    function dsrbfw() {
        return \Revenue_Booster_For_Woocommerce::instance();
    }
}

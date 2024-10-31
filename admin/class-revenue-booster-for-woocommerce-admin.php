<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://thedotstore.com/
 * @since      1.0.0
 *
 * @package    Revenue_Booster_For_Woocommerce
 * @subpackage Revenue_Booster_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Revenue_Booster_For_Woocommerce
 * @subpackage Revenue_Booster_For_Woocommerce/admin
 * @author     theDotstore <support@thedotstore.com>
 */

use Automattic\WooCommerce\Admin\BlockTemplates\BlockInterface;
use \Automattic\WooCommerce\Utilities\FeaturesUtil;

class Revenue_Booster_For_Woocommerce_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {

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
        if( strpos( $hook, '_page_dsrbfw' ) !== false ) {
            wp_enqueue_style( $this->plugin_name . '-header', plugin_dir_url( __FILE__ ) . 'css/dsrbfw-header.css', array(), $this->version, 'all' );
            wp_enqueue_style( $this->plugin_name . '-footer', plugin_dir_url( __FILE__ ) . 'css/dsrbfw-footer.css', array(), $this->version, 'all' );
            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/revenue-booster-for-woocommerce-admin.css', array(), $this->version, 'all' );
            wp_enqueue_style( $this->plugin_name . '-promotional-bar', plugin_dir_url( __FILE__ ) . 'css/dsrbfw-promotional-bar.css', array(), 'all' );
            wp_enqueue_style( $this->plugin_name . '-responsive', plugin_dir_url( __FILE__ ) . 'css/revenue-booster-for-woocommerce-admin-responsive.css', array(), 'all' );
            // We have enqueue the WooCommerce admin styles to make sure the select2 styles are loaded
            wp_enqueue_style( 'woocommerce_admin_styles' );
        }

        wp_enqueue_style( $this->plugin_name.'-product', plugin_dir_url( __FILE__ ) . 'css/dsrbfw-product-tab-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {

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

        if( strpos( $hook, '_page_dsrbfw' ) !== false ) {
            wp_enqueue_script( 'jquery-tiptip' );
            wp_enqueue_script( 'jquery-blockui' );
            wp_enqueue_script( 'jquery-ui-slider' );
            wp_enqueue_script( 'selectWoo' );

            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/revenue-booster-for-woocommerce-admin.js', array( 'jquery', 'selectWoo' ), $this->version, false );
            wp_localize_script( $this->plugin_name, 'dsrbfw_vars', array( 
                    'ajaxurl'                               => admin_url( 'admin-ajax.php' ),
                    'is_product_block_editor_enabled'       => FeaturesUtil::feature_is_enabled( 'product_block_editor' ),
                    'confirm_status_before_submit_msg'      => esc_html__( 'Are you sure you don\'t want to activate this offer?', 'revenue-booster-for-woocommerce' ),
                    'rbe_filter_delete_title'               => esc_html__( 'Delete', 'revenue-booster-for-woocommerce' ), 
                    'dsrbfw_filter_conditions'              => $this->dsrbfw_product_specific_action(),
                    'dsrbfw_filter_action'                  => $this->dsrbfw_operator_list_action(),
                    'dsrbfw_filter_condition_nonce'         => wp_create_nonce( 'dsrbfw-filter-condition' ),
                    'dsrbfw_woo_search_nonce'               => wp_create_nonce( 'dsrbfw-woo-search' ),
                    'dsrbfw_status_change_listing_nonce'    => wp_create_nonce( 'status-change-listing-nonce' ),
                    'select2_product_placeholder'           => esc_html__( 'Search for a product...', 'revenue-booster-for-woocommerce' ),
                    'select2_category_placeholder'          => esc_html__( 'Search for a category...', 'revenue-booster-for-woocommerce' ),
                    'select2_per_data_ajax'                 => absint( apply_filters( 'dsrbfw_json_data_search_limit', 10 ) ),
                    'dsrbfw_ajax_error_message'             => esc_html__( 'Something went wrong!', 'revenue-booster-for-woocommerce' ),
                    'dsrbfw_status_change_listing_message'  => esc_html__( 'Something went wrong!', 'revenue-booster-for-woocommerce' ),
                    'delete_confirmation_message'           => esc_html__( 'Do you really want to proceed with the delete action?', 'revenue-booster-for-woocommerce' ),
                )
            );
        }

        if ( strpos( $hook, '_page_dsrbfw-ob-at-checkout-list' ) !== false ) {
            wp_enqueue_script( $this->plugin_name . '-at-checkout-admin', plugin_dir_url( __FILE__ ) . 'js/dsrbfw-at-checkout-admin.js', array( 'jquery', 'selectWoo', $this->plugin_name ), $this->version, false );
            wp_localize_script( $this->plugin_name . '-at-checkout-admin', 'dsrbfw_ob_ac_vars', array( 
                    'ajaxurl'                           => admin_url( 'admin-ajax.php' ),
                    'confirm_status_before_submit_msg'  => esc_html__( 'Are you sure you don\'t want to activate this offer?', 'revenue-booster-for-woocommerce' ),
                )
            );
        }

        if ( strpos( $hook, '_page_dsrbfw-ob-before-order-list' ) !== false ) {
            wp_enqueue_script( $this->plugin_name . '-before-order-admin', plugin_dir_url( __FILE__ ) . 'js/dsrbfw-before-order-admin.js', array( 'jquery', 'selectWoo', $this->plugin_name ), $this->version, false );
            wp_localize_script( $this->plugin_name . '-before-order-admin', 'dsrbfw_ob_bo_vars', array( 
                    'ajaxurl'                           => admin_url( 'admin-ajax.php' ),
                    'confirm_status_before_submit_msg'  => esc_html__( 'Are you sure you don\'t want to activate this offer?', 'revenue-booster-for-woocommerce' ),
                )
            );
        }

        wp_enqueue_script( $this->plugin_name . '-promotioanl-bar', plugin_dir_url( __FILE__ ) . 'js/dsrbfw-promotional-bar.js', array( 'jquery' ), $this->version, false );
        wp_localize_script( $this->plugin_name . '-promotioanl-bar', 'dsrbfw_pb_vars', array(
                'dpb_api_url'                       => esc_url( DSRBFW_STORE_URL . 'wp-content/plugins/dots-dynamic-promotional-banner/bar-response.php' ),
            )
        );
	}

    /**
	 * Register the plugin menu in the admin area.
	 *
	 * @since    1.0.0
	 */
    public function dsrbfw_dot_store_menu(){
        global $GLOBALS;
        $parent_menu = 'dots_store';
		if ( empty( $GLOBALS['admin_page_hooks']['dots_store'] ) ) {
			add_menu_page( 'DotStore Plugins', esc_html__( 'DotStore Plugins', 'revenue-booster-for-woocommerce' ), 'null', 'dots_store', array(
				$this,
				'dot_store_menu_page',
			), 'dashicons-marker', 25 
            );
		}

        add_submenu_page( $parent_menu, DSRBFW_PLUGIN_NAME, DSRBFW_PLUGIN_NAME, 'manage_options', 'dsrbfw-global-settings', array(
			$this,
			'dsrbfw_global_settings',
		) );

		add_submenu_page( $parent_menu, 'Getting Started', 'Getting Started', 'manage_options', 'dsrbfw-get-started', array(
			$this,
			'dsrbfw_get_started_page',
		) );

		add_submenu_page( $parent_menu, 'Quick info', 'Quick info', 'manage_options', 'dsrbfw-information', array(
			$this,
			'dsrbfw_information_page',
		) );

        // Order Bumps show order bump rule list
        $dsrbfw_hook = add_submenu_page( $parent_menu, 'Order Bumps', 'Order Bumps', 'manage_options', 'dsrbfw-ob-at-checkout-list', array(
            $this,
            'dsrbfw_ob_at_checkout_list_page',
        ) );
        // inlcude screen options
        add_action( "load-$dsrbfw_hook", [ $this, "dsrbfw_ob_at_checkout_screen_options" ] );

        // After Checkout show add order bump rule list
        $dsrbfw_hook = add_submenu_page( $parent_menu, 'After Checkout', 'After Checkout', 'manage_options', 'dsrbfw-ob-before-order-list', array(
            $this,
            'dsrbfw_ob_before_order_list_page',
        ) );
        // inlcude screen options
		add_action( "load-$dsrbfw_hook", [ $this, "dsrbfw_ob_before_order_screen_options" ] );

        //Remove footer WP version
        $get_page   = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$page       = !empty($get_page) ? sanitize_text_field($get_page) : '';
		if ( ! empty( $page ) && ( false !== strpos( $page, 'dsrbfw' ) ) ) {
			remove_filter( 'update_footer', 'core_update_footer' ); 
		}
    }

    /**
	 * Remove submenu from admin screeen
	 *
	 * @since    1.0.0
	 */
    public function dsrbfw_remove_admin_submenus(){

        //Remove inner pages from menu list
		remove_submenu_page( 'dots_store', 'dsrbfw-get-started' );
		remove_submenu_page( 'dots_store', 'dsrbfw-information' );
		remove_submenu_page( 'dots_store', 'dsrbfw-ob-at-checkout-list' );
		remove_submenu_page( 'dots_store', 'dsrbfw-ob-before-order-list' );        
    }

    /**
     * Add custom admin footer text
     * 
     * @since 1.0.0
     */
    public function dsrbfw_admin_inline_css() {

        //CSS for dotstore icon
        echo '<style>
            .toplevel_page_dots_store .dashicons-marker::after{content:"";border:3px solid;position:absolute;top:14px;left:15px;border-radius:50%;opacity: 0.6;}
            li.toplevel_page_dots_store:hover .dashicons-marker::after,li.toplevel_page_dots_store.current .dashicons-marker::after{opacity: 1;}
            .folded .toplevel_page_dots_store .dashicons-marker::after{left:14.5px;}
            @media only screen and (max-width: 960px){
                .toplevel_page_dots_store .dashicons-marker::after{left: 14px;}
            }
        </style>';
    }

    /**
	 * Plugin footer review link with text
	 *
	 * @since    1.0.0
	 */
    public function dsrbfw_admin_footer_review(){
        $url = esc_url( 'https://wordpress.org/plugins/revenue-booster-for-woocommerce/#reviews' );
		$html = sprintf(
			'%s<strong>%s</strong>%s<a href=%s target="_blank">%s</a>', esc_html__( 'If you like installing ', 'revenue-booster-for-woocommerce' ), esc_html( DSRBFW_PLUGIN_NAME ), esc_html__( ', please leave us &#9733;&#9733;&#9733;&#9733;&#9733; ratings on ', 'revenue-booster-for-woocommerce' ), $url, esc_html__( 'DotStore', 'revenue-booster-for-woocommerce' )
		);
		echo wp_kses_post( $html );
    }

    /**
	 * Global settings page
	 *
	 * @since    1.0.0
	 */
	public function dsrbfw_global_settings() {
		require_once( plugin_dir_path( __FILE__ ) . '/partials/dsrbfw-global-settings.php' );
	}

    /**
	 * Quick guide page
	 *
	 * @since    1.0.0
	 */
	public function dsrbfw_get_started_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/dsrbfw-get-started-page.php' );
	}

    /**
	 * Plugin information page
	 *
	 * @since    1.0.0
	 */
	public function dsrbfw_information_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/dsrbfw-information-page.php' );
	}

    /**
	 * Register Admin at checkout order bump list page output.
	 *
	 * @since    1.0.0
	 */
	public function dsrbfw_ob_at_checkout_list_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'list-tables/ob-at-checkout/dsrbfw-ob-at-checkout-list-page.php' );
        $dsrbfw_lising_obj = new DSRBFW_Order_Bump_At_Checkout_Listing();
        $dsrbfw_lising_obj->dsrbfw_listing_output();
	}

    /**
	 * Screen option for at checkout order bump list
	 *
	 * @since    1.0.0
	 */
	public function dsrbfw_ob_at_checkout_screen_options() {
		$args = array(
			'label'   => esc_html__( 'Rules Per Page', 'revenue-booster-for-woocommerce' ),
			'default' => 10,
			'option'  => 'dsrbfw_ob_at_checkout_per_page',
		);
		add_screen_option( 'per_page', $args );

        //For discplay listing table
        if ( ! class_exists( 'DSRBFW_OB_At_Checkout_List_Table' ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/list-tables/ob-at-checkout/class-ob-at-checkout-list-table.php';
        }
        new DSRBFW_OB_At_Checkout_List_Table();
	}

    /**
	 * Add screen option for per page (At Checkout Post Type)
	 *
	 * @param bool   $status
	 * @param string $option
	 * @param int    $value
	 *
	 * @return int $value
	 * @since 1.0.0
	 *
	 */
	public function dsrbfw_set_screen_options( $status, $option, $value ) {
        
		$dpad_screens = array(
			'dsrbfw_ob_at_checkout_per_page',
            'dsrbfw_ob_before_order_per_page'
		);
        
		if ( in_array( $option, $dpad_screens, true ) ) {
            $value = !empty($value) && $value > 0 ? $value : get_option( 'posts_per_page' );
			return $value;
		}
		return $status;
	}

    /**
	 * Register Admin at checkout order bump list page output.
	 *
	 * @since    1.0.0
	 */
	public function dsrbfw_ob_before_order_list_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'list-tables/ob-before-order/dsrbfw-ob-before-order-list-page.php' );
        $dsrbfw_lising_obj = new DSRBFW_Order_Bump_Before_Order_Listing();
        $dsrbfw_lising_obj->dsrbfw_listing_output();
	}

    /**
	 * Screen option for at checkout order bump list
	 *
	 * @since    1.0.0
	 */
	public function dsrbfw_ob_before_order_screen_options() {
		$args = array(
			'label'   => esc_html__( 'Rules Per Page', 'revenue-booster-for-woocommerce' ),
			'default' => 10,
			'option'  => 'dsrbfw_ob_before_order_per_page',
		);
		add_screen_option( 'per_page', $args );

        //For discplay listing table
        if ( ! class_exists( 'DSRBFW_OB_Before_Order_List_Table' ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/list-tables/ob-before-order/class-ob-before-order-list-table.php';
        }
        new DSRBFW_OB_Before_Order_List_Table();
	}

    /**
	 * Add "Revenue Booster" tabs to product edit page.
	 *
	 * @param array $tabs
	 *
	 * @return array
     * 
     * @since   1.0.0
	 */
    public function dsrbfw_product_data_tab( $tabs ) {
        
        $tabs['dsrbfw_data'] = array(
            'label'    => esc_html__( 'Revenue Booster', 'revenue-booster-for-woocommerce' ),
            'priority' => 44,
            'target'   => 'dsrbfw_product_data',
            'class'    => array( 'show_if_simple', 'show_if_variable' ),
        );
        return $tabs;
    }

    /**
     * Outputs the Revenuw booster panel in Product edit screen
     * 
     * @since   1.0.0
     */
    public function dsrbfw_product_data_tab_content() {

        global $post, $post_id;
        
        $product_id = !empty($post_id) ? $post_id : $post->ID;

        $allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];

        //Get data
        $dsrbfw_fbt_title          = get_post_meta( $product_id, '_dsrbfw_fbt_title', true ) ? get_post_meta( $product_id, '_dsrbfw_fbt_title', true ) : '';
        $dsrbfw_fbt_desc           = get_post_meta( $product_id, '_dsrbfw_fbt_desc', true ) ? get_post_meta( $product_id, '_dsrbfw_fbt_desc', true ) : '';
        $dsrbfw_fbt_product_ids    = get_post_meta( $product_id, '_dsrbfw_fbt_product_ids', true ) ? get_post_meta( $product_id, '_dsrbfw_fbt_product_ids', true ) : array();
        $dsrbfw_fbt_discount       = get_post_meta( $product_id, '_dsrbfw_fbt_discount', true ) ? get_post_meta( $product_id, '_dsrbfw_fbt_discount', true ) : '';
        $dsrbfw_fbt_discount_type  = get_post_meta( $product_id, '_dsrbfw_fbt_discount_type', true ) ? get_post_meta( $product_id, '_dsrbfw_fbt_discount_type', true ) : '';
        $dsrbfw_acp_product_ids    = get_post_meta( $product_id, '_dsrbfw_acp_product_ids', true ) ? get_post_meta( $product_id, '_dsrbfw_acp_product_ids', true ) : array();
        ?>
            <div id="dsrbfw_product_data" class="panel woocommerce_options_panel hidden dsrbfw_product_data_tab">
                <div class="dsrbfw_section_title options_group">
                    <div class="dsrbfw_product_data_container">
                        <h3><?php esc_html_e( 'Frequently Bought Together', 'revenue-booster-for-woocommerce' ); ?></h3>
                        <p class="rbs_product_data_desc"><?php esc_html_e( 'Showcase these related products on the product page for easy selection, adding them to your cart with just one click.', 'revenue-booster-for-woocommerce' ); ?></p>
                    </div>
                </div>
                <div class="dsrbfw_fbt_section_fields">
                    <p class="form-field _dsrbfw_fbt_title_field ">
                        <label for="_dsrbfw_fbt_title"><?php esc_html_e( 'Title', 'revenue-booster-for-woocommerce' ); ?></label>
                        <input type="text" class="short" style="" name="_dsrbfw_fbt_title" id="_dsrbfw_fbt_title" placeholder="<?php esc_attr_e( 'Frequently Bought Together', 'revenue-booster-for-woocommerce' ); ?>" value="<?php echo esc_attr($dsrbfw_fbt_title); ?>" /> 
                    </p>
                    <p class="form-field _dsrbfw_fbt_desc_field">
                        <label for="_dsrbfw_fbt_desc"><?php esc_html_e( 'Description', 'revenue-booster-for-woocommerce' ); ?></label>
                        <input type="text" name="_dsrbfw_fbt_desc" id="_dsrbfw_fbt_desc" value="<?php echo esc_attr($dsrbfw_fbt_desc); ?>" />
                        <?php echo wp_kses( wc_help_tip( esc_html__( 'Use this to "pitch" the bundle to your customers.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                    </p>
                    <p class="form-field _dsrbfw_fbt_product_field">
                        <label for="_dsrbfw_fbt_product_ids"><?php esc_html_e( 'Products', 'revenue-booster-for-woocommerce' ); ?></label>
                        <select
                            class="wc-product-search"
                            multiple="multiple"
                            id="_dsrbfw_fbt_product_ids"
                            name="_dsrbfw_fbt_product_ids[]"
                            data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'revenue-booster-for-woocommerce' ); ?>"
                            data-sortable="true"
                            data-width="50%"
                            data-exclude="<?php echo esc_attr($product_id); ?>"
                        >
                            <?php if( !empty($dsrbfw_fbt_product_ids )) { 
                                foreach( $dsrbfw_fbt_product_ids as $dsrbfw_fbt_product_id ){
                                    $product = wc_get_product( $dsrbfw_fbt_product_id );
                                    if ( $product ) : ?>
                                        <option value="<?php echo esc_attr($dsrbfw_fbt_product_id); ?>" selected="selected"><?php echo wp_kses_post( $product->get_formatted_name() ); ?></option>
                                    <?php endif; 
                                }
                            } ?>
                        </select>
                        <?php echo wp_kses( wc_help_tip( esc_html__( 'Should be in stock and have price greater than zero.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                    </p>
                    <p class="form-field _dsrbfw_fbt_discount_field half-fields">
                        <label for="_dsrbfw_fbt_discount"><?php esc_html_e( 'Discount (Optional)', 'revenue-booster-for-woocommerce' ); ?></label>
                        <input type="text" class="wc_input_price" id="_dsrbfw_fbt_discount" name="_dsrbfw_fbt_discount" value="<?php echo esc_attr($dsrbfw_fbt_discount); ?>" />
                        <select name="_dsrbfw_fbt_discount_type" id="_dsrbfw_fbt_discount_type">
                            <option <?php selected( $dsrbfw_fbt_discount_type, 'percentage' ); ?> value="percentage">Percent</option>
                            <option <?php selected( $dsrbfw_fbt_discount_type, 'fixed' ); ?> value="fixed"><?php echo esc_attr( get_woocommerce_currency_symbol() ); ?></option>
                        </select>
                    </p>
                </div>
                <div class="dsrbfw_section_title options_group">
                    <div class="dsrbfw_product_data_container">
                        <h3><?php esc_html_e( 'After Add to Cart Popup', 'revenue-booster-for-woocommerce' ); ?></h3>
                        <p class="rbs_product_data_desc"><?php esc_html_e( 'View suggested products in a convenient popup after adding the main product to your cart, making shopping easier than ever!', 'revenue-booster-for-woocommerce' ); ?></p>
                    </div>
                </div>
                <div class="dsrbfw_acp_section_fields options_group">
                    <p class="form-field _dsrbfw_fbt_product_field">
                        <label for="_dsrbfw_acp_product_ids"><?php esc_html_e( 'Products', 'revenue-booster-for-woocommerce' ); ?></label>
                        <select
                            class="wc-product-search"
                            multiple="multiple"
                            id="_dsrbfw_acp_product_ids"
                            name="_dsrbfw_acp_product_ids[]"
                            data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'revenue-booster-for-woocommerce' ); ?>"
                            data-sortable="true"
                            data-width="50%"
                            data-exclude="<?php echo esc_attr($product_id); ?>"
                        >
                        <?php if( !empty($dsrbfw_acp_product_ids )) { 
                                foreach( $dsrbfw_acp_product_ids as $dsrbfw_acp_product_id ){
                                    $product = wc_get_product( $dsrbfw_acp_product_id );
                                    if ( $product ) : ?>
                                        <option value="<?php echo esc_attr($dsrbfw_acp_product_id); ?>" selected="selected"><?php echo wp_kses_post( $product->get_formatted_name() ); ?></option>
                                    <?php endif; 
                                }
                            } ?>
                        </select>
                        <?php echo wp_kses( wc_help_tip( esc_html__( 'Should be in stock and have price greater than zero.', 'revenue-booster-for-woocommerce' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
                    </p>
                </div>
            </div>
        <?php
    }

    /**
     * Add product stock status in search result
     * 
     * @param WC_Product $product
     * 
     * @return array
     * 
     * @since 1.0.0
     */
    public function dsrbfw_json_search_found_products( $product ) {
        if( empty( $product ) ) {
            return $product;
        }

        foreach( $product as $pid => $product_name ) {

            $stock = get_post_meta( $pid, '_stock_status', true );
            $stock_label = wc_get_product_stock_status_options()[$stock];
            $product[$pid] = esc_html( rawurldecode( wp_strip_all_tags( $stock ? $product_name .' - ' . $stock_label : $product_name ) ) );
        }

        return $product;
    }

    /**
     * Save custom tab content
     * 
     * @param int $product_id
	 *
	 * @return void
     * 
     * @since 1.0.0
     */
    public function dsrbfw_save_product_data_tab_content( $product_id ) {
        $get_dsrbfw_fbt_title          = filter_input( INPUT_POST, '_dsrbfw_fbt_title', FILTER_SANITIZE_SPECIAL_CHARS );
        $dsrbfw_fbt_title              = !empty( $get_dsrbfw_fbt_title ) ? sanitize_text_field($get_dsrbfw_fbt_title) : '';

        $get_dsrbfw_fbt_desc           = filter_input( INPUT_POST, '_dsrbfw_fbt_desc', FILTER_SANITIZE_SPECIAL_CHARS );
        $dsrbfw_fbt_desc               = !empty( $get_dsrbfw_fbt_desc ) ? sanitize_text_field($get_dsrbfw_fbt_desc) : '';

        $filter = array( 
            '_dsrbfw_fbt_product_ids' => array(	
                'filter'  => FILTER_SANITIZE_NUMBER_INT,
                'flags'   => FILTER_REQUIRE_ARRAY
            ),
        );
        $get_dsrbfw_fbt_product_ids    = filter_input_array( INPUT_POST, $filter );
        $dsrbfw_fbt_product_ids        = !empty( $get_dsrbfw_fbt_product_ids['_dsrbfw_fbt_product_ids'] ) ? array_map( 'absint', array_map( 'trim',  $get_dsrbfw_fbt_product_ids['_dsrbfw_fbt_product_ids'] ) ) : array();
        
        $get_dsrbfw_fbt_discount       = filter_input( INPUT_POST, '_dsrbfw_fbt_discount', FILTER_SANITIZE_NUMBER_INT );
        $dsrbfw_fbt_discount           = !empty( $get_dsrbfw_fbt_discount ) ? floatval($get_dsrbfw_fbt_discount) : 0;

        $get_dsrbfw_fbt_discount_type  = filter_input( INPUT_POST, '_dsrbfw_fbt_discount_type', FILTER_SANITIZE_SPECIAL_CHARS );
        $dsrbfw_fbt_discount_type      = !empty( $get_dsrbfw_fbt_discount_type ) ? sanitize_text_field($get_dsrbfw_fbt_discount_type) : 'percentage';

        $filter = array( 
            '_dsrbfw_acp_product_ids' => array(	
                'filter'  => FILTER_SANITIZE_NUMBER_INT,
                'flags'   => FILTER_REQUIRE_ARRAY
            ),
        );
        $get_dsrbfw_acp_product_ids    = filter_input_array( INPUT_POST, $filter );
        $dsrbfw_acp_product_ids        = !empty( $get_dsrbfw_acp_product_ids['_dsrbfw_acp_product_ids'] ) ? array_map( 'absint', array_map( 'trim', $get_dsrbfw_acp_product_ids['_dsrbfw_acp_product_ids'] ) ) : array();

        //We need to use htmlspecialchars_decode for title as it is for block editor
        update_post_meta( $product_id, '_dsrbfw_fbt_title', htmlspecialchars_decode($dsrbfw_fbt_title) );
        update_post_meta( $product_id, '_dsrbfw_fbt_desc', htmlspecialchars_decode($dsrbfw_fbt_desc) );
        update_post_meta( $product_id, '_dsrbfw_fbt_product_ids', $dsrbfw_fbt_product_ids );
        if ( is_numeric( $dsrbfw_fbt_discount ) ) {
            if ( 'percentage' === $dsrbfw_fbt_discount_type && $dsrbfw_fbt_discount < 0 ) {
                $dsrbfw_fbt_discount = 0;
            }
            if ( 'percentage' === $dsrbfw_fbt_discount_type && $dsrbfw_fbt_discount > 100 ) {
                $dsrbfw_fbt_discount = 100;
            }
            update_post_meta( $product_id, '_dsrbfw_fbt_discount', $dsrbfw_fbt_discount );
        }
        update_post_meta( $product_id, '_dsrbfw_fbt_discount_type', $dsrbfw_fbt_discount_type );
        update_post_meta( $product_id, '_dsrbfw_acp_product_ids', $dsrbfw_acp_product_ids );
    }

    /**
     * Add Revenue Booster group in product block editor
     * 
     * @param BlockInterface $general_group
     * 
     * @since 1.0.0
     */
    public function dsrbfw_add_revenue_booster_group( BlockInterface $general_group ){

        // Get Parent group object
        $parent = $general_group->get_parent();

        // Append our group in parent group
        $revenue_booster = $parent->add_group([
            'id'         => 'revenue-booster',
            'order'      => $general_group->get_order() + 15,
            'attributes' => [
                'title' => esc_html__( 'Revenue Booster', 'revenue-booster-for-woocommerce' ),
            ],
            'hideConditions' => [
                [
                    'expression' => 'editedProduct.type === "external" || editedProduct.type === "grouped"',
                ],
            ],
        ]);

        // Add frequently bought together section
        $dsrbfw_fbt_section = $revenue_booster->add_section([
            'id'         => 'dsrbfw-fbt-section',
            'order'      => 10,
            'attributes' => [
                'title' => esc_html__( 'Frequently Bought Together', 'revenue-booster-for-woocommerce' ),
                'description' => esc_html__( 'Showcase these related products on the product page for easy selection, adding them to your cart with just one click.', 'revenue-booster-for-woocommerce' ),
            ],
        ]); 

        // Title of FBT
        $dsrbfw_fbt_section->add_block([
            'id'         => 'dsrbfw-fbt-title-field',
            'order'      => 5,
            'blockName'  => 'woocommerce/product-text-field',
            'attributes' => [
                'property' => 'meta_data._dsrbfw_fbt_title',
                'label'    => esc_html__( 'Title', 'revenue-booster-for-woocommerce' ),
                'placeholder' => esc_html__( 'Frequently Bought Together', 'revenue-booster-for-woocommerce' ),
            ],
        ]);

        // Description of FBT
        $dsrbfw_fbt_section->add_block([
            'id'         => 'dsrbfw-fbt-description-field',
            'order'      => 10,
            'blockName'  => 'woocommerce/product-text-field',
            'attributes' => [
                'property' => 'meta_data._dsrbfw_fbt_desc',
                'label'    => esc_html__( 'Description', 'revenue-booster-for-woocommerce' ),
                'tooltip'  => esc_html__( 'Use this to "pitch" the bundle to your customers.', 'revenue-booster-for-woocommerce' ),
            ],
        ]);

        // Product IDs of FBT
        $dsrbfw_fbt_section->add_block([
            'id'         => 'dsrbfw-fbt-product-ids-field',
            'blockName'  => 'woocommerce/product-linked-list-field',
            'order'      => 40,
            'attributes' => [
                'property'   => 'meta_data._dsrbfw_fbt_product_ids',
                'emptyState' => [
                    'image'         => 'ShoppingBags',
                    'tip'           => esc_html__(
                        'Tip: Upsells are products that are extra profitable or better quality or more expensive. Experiment with combinations to boost revenue.',
                        'revenue-booster-for-woocommerce'
                    ),
                    'isDismissible' => false,
                ],
            ],
        ]);

        // Discount of FBT
        $dsrbfw_fbt_discount_column = $dsrbfw_fbt_section->add_block(
			array(
				'id'        => 'dsrbfw-fbt-discount-columns',
				'blockName' => 'core/columns',
				'order'     => 50,
			)
		);

        // Discount of FBT Column
        $dsrbfw_fbt_discount = $dsrbfw_fbt_discount_column->add_block(
			array(
				'id'         => 'dsrbfw-fbt-discount-column',
				'blockName'  => 'core/column',
				'order'      => 20,
				'attributes' => array(
					'templateLock' => 'all',
				),
			)
		);
        // Discount of FBT Field
        $dsrbfw_fbt_discount->add_block([
            'id'         => 'dsrbfw-fbt-discount-field',
            'blockName'  => 'woocommerce/product-number-field',
            'order'      => 10,
            'attributes' => array(
                'label' => esc_html__( 'Discount', 'revenue-booster-for-woocommerce' ),
                'property' => 'meta_data._dsrbfw_fbt_discount',
                'help'      => esc_html__( 'Save more when you buy these frequently bought together products as a bundle.', 'revenue-booster-for-woocommerce' ),
            ),
        ]);

        // Discount type of FBT Column
        $dsrbfw_fbt_discount_type_column = $dsrbfw_fbt_discount_column->add_block([
            'id'         => 'dsrbfw-fbt-discount-type-column',
            'blockName'  => 'core/column',
            'order'      => 10,
            'attributes' => array(
                'templateLock' => 'all',
            ),
        ]);
        // Discount type of FBT Field
        $dsrbfw_fbt_discount_type_column->add_block([
            'id'         => 'dsrbfw-fbt-discount-type-field',
            'blockName'  => 'woocommerce/product-radio-field',
            'order'      => 20,
            'class'    => 'ds-radio-field',
            'attributes' => [
                'title'    => esc_html__( 'Discount type', 'revenue-booster-for-woocommerce' ),
                'property' => 'meta_data._dsrbfw_fbt_discount_type',
                'options'  => [
                    [
                        'label' => esc_html__( 'Percent', 'revenue-booster-for-woocommerce' ),
                        'value' => 'percentage',
                    ],
                    [
                        'label' => esc_html__( 'Fixed', 'revenue-booster-for-woocommerce' ),
                        'value' => 'fixed',
                    ],
                ],
            ],
        ]);

        // After add to cart modal section
        $dsrbfw_acp_section = $revenue_booster->add_section([
            'id'         => 'dsrbfw-acp-section',
            'order'      => 10,
            'attributes' => [
                'title' => esc_html__( 'After Add to Cart Popup', 'revenue-booster-for-woocommerce' ),
                'description' => esc_html__( 'View suggested products in a convenient popup after adding the main product to your cart, making shopping easier than ever!', 'revenue-booster-for-woocommerce' ),
            ],
        ]); 

        // Product IDs of ACP
        $dsrbfw_acp_section->add_block([
            'id'         => 'dsrbfw-acp-product-ids-field',
            'blockName'  => 'woocommerce/product-linked-list-field',
            'order'      => 40,
            'attributes' => [
                'property'   => 'meta_data._dsrbfw_acp_product_ids',
                'emptyState' => [
                    'image'         => 'ShoppingBags',
                    'tip'           => esc_html__(
                        'Tip: Upsells are products that are extra profitable or better quality or more expensive. Experiment with combinations to boost revenue.',
                        'revenue-booster-for-woocommerce'
                    ),
                    'isDismissible' => true,
                ],
            ],
        ]);
    }


    /**
	 * Custom post type for plugin data store and configuration
	 *
	 * @since    1.0.0
	 */
    public function dsrbfw_post_type_define() {
        $labels_1 = array(
            'name'                  => _x( 'At Checkout Add-Ons', 'Post Type General Name', 'revenue-booster-for-woocommerce' ),
            'singular_name'         => _x( 'At Checkout Add-On', 'Post Type Singular Name', 'revenue-booster-for-woocommerce' ),
        );
        $args_1 = array(
            'label'                 => esc_html__( 'At Checkout Add-On', 'revenue-booster-for-woocommerce' ),
            'labels'                => $labels_1,
            'supports'              => array( 'title' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'show_in_nav_menus'     => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'dsrbfw_at_checkout',
            'show_in_rest'          => true,
            'map_meta_cap'          => true,
            'rewrite'               => false,
			'query_var'             => false,
        );
        register_post_type( DSRBFW_BEFORE_OB_POST_TYPE, $args_1 );

        $labels_2 = array(
            'name'                  => _x( 'Before Order Add-Ons', 'Post Type General Name', 'revenue-booster-for-woocommerce' ),
            'singular_name'         => _x( 'Before Order Add-On', 'Post Type Singular Name', 'revenue-booster-for-woocommerce' ),
        );
        $args_2 = array(
            'label'                 => esc_html__( 'Before Order Add-On', 'revenue-booster-for-woocommerce' ),
            'labels'                => $labels_2,
            'supports'              => array( 'title' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => false,
            'show_in_nav_menus'     => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'dsrbfw_before_order',
            'show_in_rest'          => true,
            'map_meta_cap'          => true,
            'rewrite'               => false,
			'query_var'             => false,
        );
        register_post_type( DSRBFW_AFTER_OB_POST_TYPE, $args_2 );
    }

    /**
     * AJAX callback function for return searched products
     * 
     * @since 1.0.0
     */
    public function dsrbfw_json_search_products_callback() {

        check_ajax_referer( 'dsrbfw-woo-search', 'security' );

        $search = filter_input( INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $search = ! empty( $search ) ? sanitize_text_field( wc_clean( wp_unslash( $search ) ) ) : '';

        if ( empty( $search ) ) {
		    wp_send_json_error( esc_html__( 'No search term provided.', 'revenue-booster-for-woocommerce' ) );
        }

        $posts_per_page = filter_input( INPUT_GET, 'posts_per_page', FILTER_VALIDATE_INT );
		$posts_per_page = ! empty( $posts_per_page ) ? intval( $posts_per_page ) : absint( apply_filters( 'woocommerce_json_search_limit', 30 ) );
		
        $offset         = filter_input( INPUT_GET, 'offset', FILTER_VALIDATE_INT );
		$offset         = ! empty( $offset ) ? intval( $offset ) : 1;

        $display_pid    = filter_input( INPUT_GET, 'display_pid', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$display_pid    = ! empty( $display_pid ) && 'true' === $display_pid  ? true : false;

        $args = array(
            'post_type' => array( 'product', 'product_variation' ),
            's'         => $search,
            'number'    => $posts_per_page,
            'offset'    => $posts_per_page * ( $offset - 1 ),
            'orderby'   => 'title',
			'order'     => 'ASC',
            'fields'    => 'ids'
        );

        add_filter( 'posts_where', array( $this, 'dsrbfw_posts_where'), 10, 2 );
		$products = new WP_Query( $args );
		remove_filter( 'posts_where', array($this, 'dsrbfw_posts_where'), 10, 2 );

        $results = array();

        if( isset( $products->posts ) && !empty( $products->posts ) && count( $products->posts ) > 0 ) {
            foreach ( $products->posts as $id ) {

                $product_object = wc_get_product( $id );

                if ( ! wc_products_array_filter_readable( $product_object ) ) {
                    continue;
                }

                // product validation
                if( ! $product_object->is_in_stock() || ! $product_object->is_purchasable() || ! $product_object->is_visible() ) {
                    continue;
                }

                $formatted_name = $product_object->get_formatted_name();

                /* translators: %1$s is replaced with "string" which show searched product name and %2$d is replaced with "number" which show searched product ID */
                $results[ $product_object->get_id() ] = $display_pid ? sprintf( esc_html__( '#%2$d - %1$s', 'revenue-booster-for-woocommerce' ), html_entity_decode( wp_strip_all_tags( $formatted_name ) ), $product_object->get_id() ) : html_entity_decode( wp_strip_all_tags( $formatted_name ) );
            }
        }

        wp_send_json( $results );
    }

    /**
     * AJAX callback function for return searched categories
     * 
     * @since 1.0.0
     */
    public function dsrbfw_json_search_categories_callback() {

        check_ajax_referer( 'dsrbfw-woo-search', 'security' );

        $search = filter_input( INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $search = ! empty( $search ) ? sanitize_text_field( wc_clean( wp_unslash( $search ) ) ) : '';

        if ( empty( $search ) ) {
		    wp_send_json_error( esc_html__( 'No search term provided.', 'revenue-booster-for-woocommerce' ) );
        }

        $posts_per_page = filter_input( INPUT_GET, 'posts_per_page', FILTER_VALIDATE_INT );
		$posts_per_page = ! empty( $posts_per_page ) ? intval( $posts_per_page ) : 0;
		
        $offset         = filter_input( INPUT_GET, 'offset', FILTER_VALIDATE_INT );
		$offset         = ! empty( $offset ) ? intval( $offset ) : 1;

        $display_pid    = filter_input( INPUT_GET, 'display_pid', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$display_pid    = ! empty( $display_pid ) && 'true' === $display_pid  ? true : false;

        $args               = array(
			'post_type'     => 'product',
			'post_status'   => 'publish',
			'taxonomy'      => 'product_cat',
			'orderby'       => 'name',
			'hierarchical'  => true,
			'hide_empty'    => false,
            'search'        => $search,
			'number'        => $posts_per_page,
            'offset'        => $posts_per_page * ( $offset - 1 ),
		);
		$get_all_categories = get_terms( $args );

        $results = array();
        if(  !empty( $get_all_categories ) && count( $get_all_categories ) > 0 ) {
            foreach ( $get_all_categories as $category ) {
                $show_cat_acenctors = implode( " > ", wp_list_pluck( array_reverse( $this->get_parent_terms( $category ) ), 'name' ) );
                $results[$category->term_id] = $display_pid 
                /* translators: %s is replaced with "string" which show searched category name and %d is replaced with "number" which show searched category ID */
                ? sprintf( esc_html__( '#%2$d - %1$s', 'revenue-booster-for-woocommerce' ), html_entity_decode( wp_strip_all_tags( $show_cat_acenctors ) ), $category->term_id )
                : html_entity_decode( wp_strip_all_tags( $show_cat_acenctors ) );
            }
        }

        wp_send_json( $results );
    }

    /**
     * Search product by title in admin
     * 
     * @since    1.0.0
     */
    public function dsrbfw_posts_where( $where, $wp_query ) {
        global $wpdb;
        $search_term = $wp_query->get( 'search_pro_title' );
        if ( ! empty( $search_term ) ) {
            $search_term_like = $wpdb->esc_like( $search_term );
            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
        }
        return $where;
    }

    /**
	 * List of conditions
	 *
	 * @return array $final_data
	 *
	 * @since  1.0.0
	 */
	public function dsrbfw_conditions_list_action() {
		$final_data = $this->dsrbfw_product_specific_action();
		return $final_data;
	}

    /**
	 * List of Product specific conditions.
	 *
	 * @return array $loca_arr
	 *
	 * @since  1.0.0
     * 
	 */
	public function dsrbfw_product_specific_action() {
		$product_list_cond = array(
			'product'  => esc_html__( 'Cart contains product', 'revenue-booster-for-woocommerce' ),
			'category' => esc_html__( 'Cart contains category\'s product', 'revenue-booster-for-woocommerce' )
		);
        return $product_list_cond;
    }

    /**
	 * List of Operator
	 *
	 * @param string $fees_conditions Check which condition is applying.
	 *
	 * @return array $final_data
	 *
	 * @since  1.0.0
	 */
	public function dsrbfw_operator_list_action() {
		$prd_op_arr  = array(
			'is_equal_to' => esc_html__( 'Equal to ( = )', 'revenue-booster-for-woocommerce' ),
			'not_in'      => esc_html__( 'Not Equal to ( != )', 'revenue-booster-for-woocommerce' ),
		);
		return $prd_op_arr;
	}

    /**
	 * Get product HTML for filter use with select2 dropdown.
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string $html
	 *
	 * @since  1.0.0
	 */
	public function dsrbfw_get_product_list( $count = '', $selected = array() ) {
		ob_start();
        ?>
		<select id="product-filter-<?php echo esc_attr( $count ) ?>" rel-id="<?php echo esc_attr( $count ) ?>" name="dsrbfw_ob[dsrbfw_ob_values][value_<?php echo esc_attr( $count ); ?>][]" class="ds-woo-search" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'revenue-booster-for-woocommerce' ); ?>" data-sortable="true" multiple="multiple" data-width="100%">
        <?php 
        if( !empty( $selected ) ) {
            $args = array( 'include' => $selected, 'type' => array( 'simple', 'variation') );
            $products = wc_get_products($args);
            if ( $products ) {
                foreach ( $products as $product ) { ?>
                    <option value="<?php echo intval($product->get_id()); ?>" selected="selected"><?php echo wp_kses_post( $product->get_formatted_name() ); ?></option>
                    <?php 
                }
            } 
        } ?>
		</select>
        <?php
		return ob_get_clean();
	}

    /**
     * Get trail of parent terms
     * 
     * @param object $term
     * 
     * @return array
     * 
     * @since 1.0.0
     */
    public function get_parent_terms( $term ) {
        $arr = [ $term ];
        while ( $term->parent > 0 ) {
            $term = get_term_by("id", $term->parent, "product_cat");
            if ($term) {
                $arr[] = $term;
            } else {
                break;
            }
        }
        return $arr;
    }

    /**
     * Get category HTML for filter use with select2 dropdown.
     * 
     * @param int $count
     * @param array $selected
     * 
     * @return string
     * 
     * @since 1.0.0
     */
    public function dsrbfw_get_category_list( $count = '', $selected = array() ) {
        ob_start();
        ?>
        <select id="category-filter-<?php echo esc_attr( $count ) ?>" rel-id="<?php echo esc_attr( $count ) ?>" name="dsrbfw_ob[dsrbfw_ob_values][value_<?php echo esc_attr( $count ); ?>][]" class="ds-woo-search" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'revenue-booster-for-woocommerce' ); ?>" data-sortable="true" multiple="multiple" data-width="100%" data-action="dsrbfw_json_search_categories">
        <?php 
        if( !empty( $selected ) ) {
            $args = array( 
                'taxonomy'  => 'product_cat',
                'include'   => $selected,
                'hide_empty' => false,
            );
            $categories = get_terms($args);
            if ( $categories ) {
                foreach ( $categories as $category ) { 
                    $show_cat_acenctors = implode( " > ", wp_list_pluck( array_reverse( $this->get_parent_terms( $category ) ), 'name' ) ); ?>
                    <option value="<?php echo intval( $category->term_id ); ?>" selected="selected"><?php echo wp_kses_post( $show_cat_acenctors ); ?></option>
                    <?php 
                }
            } 
        } ?>
        </select>
        <?php
        return ob_get_clean(); // phpcs:ignore
    }


    /**
     * AJAX callback: Change status of pickup location callback
     * 
     * @since 1.0.0
     */
    public function dsrbfw_change_status_from_list_callback() {

        check_ajax_referer( 'status-change-listing-nonce', 'security' );

        $get_dsrbfw_ob_id          = filter_input( INPUT_POST, 'dsrbfw_ob_id', FILTER_SANITIZE_NUMBER_INT );
		$get_dsrbfw_ob_status      = filter_input( INPUT_POST, 'dsrbfw_ob_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$get_dsrbfw_ob_post_type   = filter_input( INPUT_POST, 'dsrbfw_ob_post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

        $dsrbfw_ob_id          = isset( $get_dsrbfw_ob_id ) ? absint( $get_dsrbfw_ob_id ) : 0;
		$dsrbfw_ob_status      = isset( $get_dsrbfw_ob_status ) ? sanitize_text_field( $get_dsrbfw_ob_status ) : false;
		$dsrbfw_ob_post_type   = isset( $get_dsrbfw_ob_post_type ) ? sanitize_text_field( $get_dsrbfw_ob_post_type ) : false;

        if ( empty( $dsrbfw_ob_id ) ) {
			wp_send_json_error( esc_html__( 'Rule ID is not there!', 'revenue-booster-for-woocommerce' ) );
		}

        if( !$dsrbfw_ob_post_type ) {
            wp_send_json_error( esc_html__( 'Invalid post type!', 'revenue-booster-for-woocommerce' ) );
        }

        if( 'true' === $dsrbfw_ob_status ){
            $post_args   = array(
				'ID'          => $dsrbfw_ob_id,
				'post_status' => 'publish',
				'post_type'   => $dsrbfw_ob_post_type,
			);
            $post_update = wp_update_post( $post_args );
            if ( is_wp_error( $post_update ) ) {
                wp_send_json_error( esc_html__( 'Status not changed! Error occured!', 'revenue-booster-for-woocommerce' ) );
            }
        } else {
            $post_args   = array(
				'ID'          => $dsrbfw_ob_id,
				'post_status' => 'draft',
				'post_type'   => $dsrbfw_ob_post_type,
			);
			$post_update = wp_update_post( $post_args );
            if ( is_wp_error( $post_update ) ) {
                wp_send_json_error( esc_html__( 'Status not changed! Error occured!', 'revenue-booster-for-woocommerce' ) );
            }
        }

        wp_send_json_success( esc_html__( 'Status has been changed!', 'revenue-booster-for-woocommerce' ), 200 );
    }

    /**
     * Based on message type prepare notice
     * 
     * @since    1.0.0
     */
    public function dsrbfw_display_action_message() {

        $message = filter_input( INPUT_GET, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $message = isset( $message ) ? sanitize_text_field( $message ) : '';
        
        if( !empty($message) ) {

            if ( 'created' === $message ) {
                $updated_message = esc_html__( "Rule has been created.", 'revenue-booster-for-woocommerce' );
            } elseif ( 'saved' === $message ) {
                $updated_message = esc_html__( "Rule has been updated.", 'revenue-booster-for-woocommerce' );
            } elseif ( 'deleted' === $message ) {
                $updated_message = esc_html__( "Rule has been deleted.", 'revenue-booster-for-woocommerce' );
            } elseif ( 'duplicated' === $message ) {
                $updated_message = esc_html__( "Rule has been duplicated.", 'revenue-booster-for-woocommerce' );
            } elseif ( 'disabled' === $message ) {
                $updated_message = esc_html__( "Rule has been disabled.", 'revenue-booster-for-woocommerce' );
            } elseif ( 'enabled' === $message ) {
                $updated_message = esc_html__( "Rule has been enabled.", 'revenue-booster-for-woocommerce' );
            }
            if ( 'failed' === $message ) {
                $failed_messsage = esc_html__( "There was an error with saving data.", 'revenue-booster-for-woocommerce' );
            } elseif ( 'nonce_check' === $message ) {
                $failed_messsage = esc_html__( "There was an error with security check.", 'revenue-booster-for-woocommerce' );
            }
            
            if ( ! empty( $updated_message ) ) {
                echo sprintf( '<div id="message" class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( $updated_message ) );
            }
            if ( ! empty( $failed_messsage ) ) {
                echo sprintf( '<div id="message" class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $failed_messsage ) );
            }
        }
    }

    /**
     * Hide meta keys from order item meta (for admin purpose)
     * 
     * @param array $meta_keys
     * 
     * @return array $meta_keys
     * 
     * @since 1.0.0
     */
    public function dsrbfw_hidden_order_itemmeta( $meta_keys ){
        $meta_keys[] = '_'.$this->ob_ac_cart_meta_key;
        $meta_keys[] = '_'.$this->ob_bo_cart_meta_key;
        return $meta_keys;
    }

    /**
     * Show badge for addon product and extra product in order item meta
     * 
     * @param int $item_id
     * @param object $item
     * 
     * @since 1.0.0
     */
    public function dsrbfw_before_order_itemmeta( $item_id, $item ){
        $item_metas = $item->get_formatted_meta_data( '', true );
        if( !empty( $item_metas ) ){
            foreach( $item_metas as $item_meta ){
                if( '_'.$this->ob_ac_cart_meta_key === $item_meta->key ) {
                    echo wp_kses_post( sprintf( '<span class="addon-product" style="background: dodgerblue; color: #fff; padding: 0px 5px 2px; border-radius: 5px;">%s</span>', esc_html__( 'Addon Product', 'revenue-booster-for-woocommerce' ) ) );
                }
                if( '_'.$this->ob_bo_cart_meta_key === $item_meta->key ) {
                    echo wp_kses_post( sprintf( '<span class="extra-product" style="background: dodgerblue; color: #fff; padding: 0px 5px 2px; border-radius: 5px;">%s</span>', esc_html__( 'Addon Product', 'revenue-booster-for-woocommerce' ) ) );
                }
            }
        }
    }

    /**
     * Get dynamic promotional bar of plugin
     *
     * @param   String  $plugin_slug  slug of the plugin added in the site option
     * 
     * @return  null
     * 
     * @since 1.0.0
     */
    public function dsrbfw_get_promotional_bar( $plugin_slug = '' ) {
        $promotional_bar_upi_url = DSRBFW_STORE_URL . 'wp-json/dpb-promotional-banner/v2/dpb-promotional-banner?' . wp_rand();
        $promotional_banner_request    = wp_remote_get( $promotional_bar_upi_url );  //phpcs:ignore
        if ( empty( $promotional_banner_request->errors ) ) {
            $promotional_banner_request_body = $promotional_banner_request['body'];	
            $promotional_banner_request_body = json_decode( $promotional_banner_request_body, true );
            echo '<div class="dynamicbar_wrapper">';
            
            if ( ! empty( $promotional_banner_request_body ) && is_array( $promotional_banner_request_body ) ) {
                foreach ( $promotional_banner_request_body as $promotional_banner_request_body_data ) {
					$promotional_banner_id        	  	= $promotional_banner_request_body_data['promotional_banner_id'];
                    $promotional_banner_cookie          = $promotional_banner_request_body_data['promotional_banner_cookie'];
                    $promotional_banner_image           = $promotional_banner_request_body_data['promotional_banner_image'];
                    $promotional_banner_description     = $promotional_banner_request_body_data['promotional_banner_description'];
                    $promotional_banner_button_group    = $promotional_banner_request_body_data['promotional_banner_button_group'];
                    $dpb_schedule_campaign_type         = $promotional_banner_request_body_data['dpb_schedule_campaign_type'];
                    $promotional_banner_target_audience = $promotional_banner_request_body_data['promotional_banner_target_audience'];

                    if ( ! empty( $promotional_banner_target_audience ) ) {
                        $plugin_keys = array();
                        if(is_array ($promotional_banner_target_audience)) {
                            foreach($promotional_banner_target_audience as $list) {
                                $plugin_keys[] = $list['value'];
                            }
                        } else {
                            $plugin_keys[] = $promotional_banner_target_audience['value'];
                        }

                        $display_banner_flag = false;
                        if ( in_array ( 'all_customers', $plugin_keys, true ) || in_array ( $plugin_slug, $plugin_keys, true ) ) {
                            $display_banner_flag = true;
                        }
                    }
                    
                    if ( true === $display_banner_flag ) {
                        if ( 'default' === $dpb_schedule_campaign_type ) {
                            $banner_cookie_show         = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $banner_cookie_visible_once = filter_input( INPUT_COOKIE, 'banner_show_once_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $flag                       = false;
                            if ( empty( $banner_cookie_show ) && empty( $banner_cookie_visible_once ) ) {
                                setcookie( 'banner_show_' . $promotional_banner_cookie, 'yes', time() + ( 86400 * 7 ) ); //phpcs:ignore
                                setcookie( 'banner_show_once_' . $promotional_banner_cookie, 'yes' ); //phpcs:ignore
                                $flag = true;
                            }

                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            if ( ! empty( $banner_cookie_show ) || true === $flag ) {
                                $banner_cookie = filter_input( INPUT_COOKIE, 'banner_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                                $banner_cookie = isset( $banner_cookie ) ? $banner_cookie : '';
                                if ( empty( $banner_cookie ) && 'yes' !== $banner_cookie ) { ?>
                            	<div class="dpb-popup <?php echo isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner'; ?>">
                                    <?php
                                    if ( ! empty( $promotional_banner_image ) ) {
                                        ?>
                                        <img src="<?php echo esc_url( $promotional_banner_image ); ?>"/>
                                        <?php
                                    }
                                    ?>
                                    <div class="dpb-popup-meta">
                                        <p>
                                            <?php
                                            echo wp_kses_post( str_replace( array( '<p>', '</p>' ), '', $promotional_banner_description ) );
                                            if ( ! empty( $promotional_banner_button_group ) ) {
                                                foreach ( $promotional_banner_button_group as $promotional_banner_button_group_data ) {
                                                    ?>
                                                    <a href="<?php echo esc_url( $promotional_banner_button_group_data['promotional_banner_button_link'] ); ?>" target="_blank"><?php echo esc_html( $promotional_banner_button_group_data['promotional_banner_button_text'] ); ?></a>
                                                    <?php
                                                }
                                            }
                                            ?>
                                    	</p>
                                    </div>
                                    <a href="javascript:void(0);" data-bar-id="<?php echo esc_attr($promotional_banner_id); ?>" data-popup-name="<?php echo isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner'; ?>" class="dpbpop-close"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><path id="Icon_material-close" data-name="Icon material-close" d="M17.5,8.507,16.493,7.5,12.5,11.493,8.507,7.5,7.5,8.507,11.493,12.5,7.5,16.493,8.507,17.5,12.5,13.507,16.493,17.5,17.5,16.493,13.507,12.5Z" transform="translate(-7.5 -7.5)" fill="#acacac"/></svg></a>
                                </div>
                                <?php
                                }
                            }
                        } else {

                            $banner_cookie_show         = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $banner_cookie_visible_once = filter_input( INPUT_COOKIE, 'banner_show_once_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            $flag                       = false;
                            if ( empty( $banner_cookie_show ) && empty( $banner_cookie_visible_once ) ) {
                                setcookie( 'banner_show_' . $promotional_banner_cookie, 'yes'); //phpcs:ignore
                                setcookie( 'banner_show_once_' . $promotional_banner_cookie, 'yes' ); //phpcs:ignore
                                $flag = true;
                            }

                            $banner_cookie_show = filter_input( INPUT_COOKIE, 'banner_show_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            if ( ! empty( $banner_cookie_show ) || true === $flag ) {

                                $banner_cookie = filter_input( INPUT_COOKIE, 'banner_' . $promotional_banner_cookie, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                                $banner_cookie = isset( $banner_cookie ) ? $banner_cookie : '';
                                if ( empty( $banner_cookie ) && 'yes' !== $banner_cookie ) { ?>
                    			<div class="dpb-popup <?php echo isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner'; ?>">
                                    <?php
                                    if ( ! empty( $promotional_banner_image ) ) {
                                        ?>
                                            <img src="<?php echo esc_url( $promotional_banner_image ); ?>"/>
                                        <?php
                                    }
                                    ?>
                                    <div class="dpb-popup-meta">
                                        <p>
                                            <?php
                                            echo wp_kses_post( str_replace( array( '<p>', '</p>' ), '', $promotional_banner_description ) );
                                            if ( ! empty( $promotional_banner_button_group ) ) {
                                                foreach ( $promotional_banner_button_group as $promotional_banner_button_group_data ) {
                                                    ?>
                                                    <a href="<?php echo esc_url( $promotional_banner_button_group_data['promotional_banner_button_link'] ); ?>" target="_blank"><?php echo esc_html( $promotional_banner_button_group_data['promotional_banner_button_text'] ); ?></a>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <a href="javascript:void(0);" data-popup-name="<?php echo isset( $promotional_banner_cookie ) ? esc_html( $promotional_banner_cookie ) : 'default-banner'; ?>" class="dpbpop-close"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><path id="Icon_material-close" data-name="Icon material-close" d="M17.5,8.507,16.493,7.5,12.5,11.493,8.507,7.5,7.5,8.507,11.493,12.5,7.5,16.493,8.507,17.5,12.5,13.507,16.493,17.5,17.5,16.493,13.507,12.5Z" transform="translate(-7.5 -7.5)" fill="#acacac"/></svg></a>
                                </div>
                                <?php
                                }
                            }
                        }
                    }
                }
            }
            echo '</div>';
        }
    }
}

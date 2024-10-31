<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://thedotstore.com/
 * @since             1.0.0
 * @package           Revenue_Booster_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Revenue Booster for WooCommerce
 * Plugin URI:        https://www.thedotstore.com/revenue-booster-for-woocommerce/
 * Description:       Lets shoppers create compelling offers on product and checkout pages, boosting revenue and enhancing user experience.
 * Version:           1.0.0
 * Author:            theDotstore
 * Author URI:        https://thedotstore.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       revenue-booster-for-woocommerce
 * Domain Path:       /languages
 * 
 * Requires PHP:            7.4
 * Requires at least:       6.0
 * WP tested up to:         6.6.2
 * WC requires at least:    9.0.0
 * WC tested up to:         9.3.3
 * Requires Plugins:        woocommerce
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
if( ! defined( 'DSRBFW_PLUGIN_VERSION' ) ) {
    define( 'DSRBFW_PLUGIN_VERSION', '1.0.0' );
}

/**
 * Minimum PHP version required
 */
if( ! defined( 'DSRBFW_MINIMUM_PHP_VERSION' ) ) {
    define( 'DSRBFW_MINIMUM_PHP_VERSION', '7.4' );
}

/**
 * Minimum WordPress version required
 */
if( ! defined( 'DSRBFW_MINIMUM_WP_VERSION' ) ) {
    define( 'DSRBFW_MINIMUM_WP_VERSION', '6.0' );
}

/**
 * Minimum WooCommerce version required
 */
if( ! defined( 'DSRBFW_MINIMUM_WC_VERSION' ) ) {
    define( 'DSRBFW_MINIMUM_WC_VERSION', '9.0.0' );
}

/**
 * Define plugin logo URL
 */
if( ! defined( 'DSRBFW_PLUGIN_LOGO_URL' ) ) {
    define( 'DSRBFW_PLUGIN_LOGO_URL', plugin_dir_url( __FILE__ ) . 'admin/images/revenue-booster-for-woocommerce.png' );
}

/**
 * Define the plugin's name if not already defined.
 */
if ( ! defined( 'DSRBFW_PLUGIN_NAME' ) ) {
    define( 'DSRBFW_PLUGIN_NAME', 'Revenue Booster for WooCommerce' );
}

/** 
 * Plugin version type lable 
 */
if ( !defined( 'DSRBFW_VERSION_LABEL' ) ) {
    define( 'DSRBFW_VERSION_LABEL', esc_html__( 'Free', 'revenue-booster-for-woocommerce' ) );
}

/**
 * Retrieve the basename of the main plugin file. 
 * This ensures that the constant always holds the accurate basename, even if the plugin file is renamed or moved.
 */
if ( !defined( 'DSRBFW_PLUGIN_BASENAME' ) ) {
    define( 'DSRBFW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

/**
 * Define the slug for the promotional feature if not already defined.
 * This code snippet establishes a standardized slug for the promotional bar feature used within the plugin.
 */
if ( ! defined( 'DSRBFW_PROMOTIONAL_SLUG' ) ) {
    define( 'DSRBFW_PROMOTIONAL_SLUG', 'basic_revenue_booster' );
}

/**
 * The function is used to dynamically generate the URL of the directory containing the main plugin file.
 */
if ( ! defined( 'DSRBFW_PLUGIN_URL' ) ) {
    define( 'DSRBFW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * The function is used to dynamically generate the base path of the directory containing the main plugin file.
 */
if ( ! defined( 'DSRBFW_PLUGIN_BASE_DIR' ) ) {
    define( 'DSRBFW_PLUGIN_BASE_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Define the URL of the plugin store if not already defined.
 */
if ( ! defined( 'DSRBFW_STORE_URL' ) ) {
    define( 'DSRBFW_STORE_URL', 'https://www.thedotstore.com/' );
}

/** 
 * Add the API URL to fetch the promotional banners 
 */
if ( !defined( 'DSRBFW_PROMOTIONAL_BANNER_API_URL' ) ) {
    define( 'DSRBFW_PROMOTIONAL_BANNER_API_URL', 'https://www.thedotstore.com/' );
}

/**
 * Define the post type name for listing rule use.
 */
if ( ! defined( 'DSRBFW_BEFORE_OB_POST_TYPE' ) ) {
    define( 'DSRBFW_BEFORE_OB_POST_TYPE', 'dsrbfw_at_checkout' );
}

/**
 * Define the post type name for listing rule use.
 */
if ( ! defined( 'DSRBFW_AFTER_OB_POST_TYPE' ) ) {
    define( 'DSRBFW_AFTER_OB_POST_TYPE', 'dsrbfw_before_order' );
}

/**
 * Define the post type name for listing rule use.
 */
if ( ! defined( 'DSRBFW_DOC_LINK' ) ) {
    define( 'DSRBFW_DOC_LINK', 'https://docs.thedotstore.com/article/922-comming-soon' );
}

/**
 * Define the header file link.
 */
if ( ! defined( 'DSRBFW_PLUGIN_HEADER_LINK' ) ) {
    define( 'DSRBFW_PLUGIN_HEADER_LINK', plugin_dir_path( __FILE__ ) . 'admin/partials/header/plugin-header.php' );
}

/**
 * Define the footer file link.
 */
if ( ! defined( 'DSRBFW_PLUGIN_FOOTER_LINK' ) ) {
    define( 'DSRBFW_PLUGIN_FOOTER_LINK', plugin_dir_path( __FILE__ ) . 'admin/partials/header/plugin-footer.php' );
}

/**
 * Define Order Bump At Checkout Title length.
 */
if ( ! defined( 'DSRBFW_OB_AC_TITLE_LENGTH' ) ) {
    define( 'DSRBFW_OB_AC_TITLE_LENGTH', apply_filters( 'dsrbfw_at_checkout_title_length', 50 ) );
}

/**
 * Define the post type name for listing rule use.
 */
if ( ! defined( 'DSRBFW__DEV_MODE' ) ) {
    define( 'DSRBFW__DEV_MODE', false );
}

// Below activate plugin need to check for the environment compatibility (like PHP, WP, WC version)
require plugin_dir_path( __FILE__ ) . 'revenue-booster-for-woocommerce-security-checks.php';

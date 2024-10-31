<?php

/**
 * This is plugin initial security and compatibility check class.
 */

defined( 'ABSPATH' ) or exit;

/**
 * The plugin loader class.
 *
 * @since 1.0.0
 */
if( ! class_exists( 'Revenue_Booster_For_Woocommerce_Security_Checks' ) ) {

    class Revenue_Booster_For_Woocommerce_Security_Checks {

        /** @var \Revenue_Booster_For_Woocommerce_Security_Checks single instance of this class */
        private static $instance;

        /** @var array the admin notices to add */
        private $notices = array();

        /**
         * Loads the plugin.
         *
         * @since 1.0.0
         */
        protected function __construct() {

            register_activation_hook( __FILE__, array( $this, 'activate_revenue_booster_for_woocommerce' ) );

            register_deactivation_hook( __FILE__, array( $this, 'deactivate_revenue_booster_for_woocommerce' ) );

            add_action( 'admin_init', array( $this, 'check_environment' ) );
            add_action( 'admin_init', array( $this, 'add_plugin_notices' ) );

            add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );

            // if the environment check fails, initialize the plugin
            if ( $this->is_environment_compatible() ) {

                add_action( 'plugins_loaded', array( $this, 'dsrbfw_run_revenue_booster_for_woocommerce' ) );
            }
        }

        /**
         * The code that runs during plugin activation.
         * This action is documented in includes/class-revenue-booster-for-woocommerce-activator.php
         */
        protected function activate_revenue_booster_for_woocommerce() {

            if ( ! $this->is_environment_compatible() ) {

                $this->deactivate_plugin();

                wp_die( DSRBFW_PLUGIN_NAME . ' could not be activated. ' . $this->get_environment_message() ); // phpcs:ignore
            } 

            require_once plugin_dir_path( __FILE__ ) . 'includes/class-revenue-booster-for-woocommerce-activator.php';
            Revenue_Booster_For_Woocommerce_Activator::activate();
        }

        /**
         * The code that runs during plugin deactivation.
         * This action is documented in includes/class-revenue-booster-for-woocommerce-deactivator.php
         */
        protected function deactivate_revenue_booster_for_woocommerce() {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-revenue-booster-for-woocommerce-deactivator.php';
            Revenue_Booster_For_Woocommerce_Deactivator::deactivate();
        }

        /**
         * Checks the environment on loading WordPress, just in case the environment changes after activation.
         * Adds notices for out-of-date WordPress and/or WooCommerce versions.
         *
         * @since 1.0.0
         */
        public function check_environment() {
            
            // PHP version check
            if ( ! $this->is_environment_compatible() && is_plugin_active( DSRBFW_PLUGIN_BASENAME ) ) {
                
                $this->deactivate_plugin();

                $this->add_admin_notice( 'bad_environment', 'error', DSRBFW_PLUGIN_NAME . ' has been deactivated. ' . $this->get_environment_message() );
            }

        }

        /**
         * Adds notices for out-of-date WordPress and/or WooCommerce versions.
         *
         * @since 1.0.0
         */
        public function add_plugin_notices() {

            if ( ! $this->is_wp_compatible() ) {

                $this->add_admin_notice( 'update_wordpress', 'error', sprintf(
                    '%s requires WordPress version %s or higher. Please %supdate WordPress &raquo;%s',
                    '<strong>' . DSRBFW_PLUGIN_NAME . '</strong>',
                    DSRBFW_MINIMUM_WP_VERSION,
                    '<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
                ) );
            }

            if ( ! $this->is_wc_compatible() ) {

                $this->add_admin_notice( 'update_woocommerce', 'error', sprintf(
                    '%1$s requires WooCommerce version %2$s or higher. Please %3$supdate WooCommerce%4$s to the latest version, or %5$sdownload the minimum required version &raquo;%6$s',
                    '<strong>' . DSRBFW_PLUGIN_NAME . '</strong>',
                    DSRBFW_MINIMUM_WC_VERSION,
                    '<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>',
                    '<a href="' . esc_url( 'https://downloads.wordpress.org/plugin/woocommerce.' . DSRBFW_MINIMUM_WC_VERSION . '.zip' ) . '">', '</a>'
                ) );
            }
        }

        /**
         * Begins execution of the plugin.
         *
         * Since everything within the plugin is registered via hooks,
         * then kicking off the plugin from this point in the file does
         * not affect the page life cycle.
         *
         * @since    1.0.0
         */
        public function dsrbfw_run_revenue_booster_for_woocommerce() {
            
            if ( ! $this->plugins_compatible() ) {
                return;
            }

            /**
             * The core plugin class that is used to define internationalization,
             * admin-specific hooks, and public-facing site hooks.
             */
            require plugin_dir_path( __FILE__ ) . 'includes/class-revenue-booster-for-woocommerce.php';

            dsrbfw();
            dsrbfw()->run();
        }

        /**
        * Determines if the server environment is compatible with this plugin.
        *
        * Override this method to add checks for more than just the PHP version.
        *
        * @since 1.0.0
        *
        * @return bool
        */
        protected function is_environment_compatible() {

            return version_compare( PHP_VERSION, DSRBFW_MINIMUM_PHP_VERSION, '>=' );
        }

        /**
         * Deactivates the plugin.
         *
         * @since 1.0.0
         */
        protected function deactivate_plugin() {

            deactivate_plugins( plugin_basename( __FILE__ ) );

            if ( isset( $_GET['activate'] ) ) { // phpcs:ignore
                unset( $_GET['activate'] ); // phpcs:ignore
            }
        }

        /**
         * Determines if the WordPress compatible.
         *
         * @since 1.0.0
         *
         * @return bool
         */
        protected function is_wp_compatible() {

            return version_compare( get_bloginfo( 'version' ), DSRBFW_MINIMUM_WP_VERSION, '>=' );
        }


        /**
         * Determines if the WooCommerce compatible.
         *
         * @since 1.0.0
         *
         * @return bool
         */
        protected function is_wc_compatible() {

            return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, DSRBFW_MINIMUM_WC_VERSION, '>=' );
        }

        /**
         * Determines if the required plugins are compatible.
         *
         * @since 1.0.0
         *
         * @return bool
         */
        protected function plugins_compatible() {

            return $this->is_wp_compatible() && $this->is_wc_compatible();
        }

        /**
         * Gets the message for display when the environment is incompatible with this plugin.
         *
         * @since 1.0.0
         *
         * @return string
         */
        protected function get_environment_message() {

            return sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', DSRBFW_MINIMUM_PHP_VERSION, PHP_VERSION );
        }

        /**
         * Adds an admin notice to be displayed.
         *
         * @since 1.0.0
         *
         * @param string $slug the slug for the notice
         * @param string $class the css class for the notice
         * @param string $message the notice message
         */
        public function add_admin_notice( $slug, $class, $message ) {

            $this->notices[ $slug ] = array(
                'class'   => $class,
                'message' => $message
            );
        }

        /**
         * Displays any admin notices
         *
         * @since 1.0.0
         */
        public function admin_notices() {
            
            foreach ( $this->notices as $notice ) : ?>
                <div class="<?php echo esc_attr( $notice['class'] ); ?>">
                    <p><?php echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) ); ?></p>
                </div>
                <?php
            endforeach;
        }

        /**
         * Gets the main loader instance.
         *
         * Ensures only one instance can be loaded.
         *
         * @since 1.0.0
         *
         * @return \Revenue_Booster_For_Woocommerce_Security_Checks
         */
        public static function instance() {

            if ( null === self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
        }
    }
}

// fire it up!
Revenue_Booster_For_Woocommerce_Security_Checks::instance();
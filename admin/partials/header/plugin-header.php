<?php
	// If this file is called directly, abort.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

    $plugin_slug = DSRBFW_PROMOTIONAL_SLUG;

    $current_page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $dsrbfw_global_settings = isset( $current_page ) && 'dsrbfw-global-settings' === $current_page ? 'active' : '';
    $dsrbfw_ob_at_checkout_list = isset( $current_page ) && 'dsrbfw-ob-at-checkout-list' === $current_page ? 'active' : '';
    $dsrbfw_ob_before_order_list = isset( $current_page ) && 'dsrbfw-ob-before-order-list' === $current_page ? 'active' : '';
    $dsrbfw_settings_menu = isset($current_page) && ( 'dsrbfw-get-started' === $current_page || 'dsrbfw-information' === $current_page ) ? 'active' : '';
   
    $dsrbfw_get_started    = isset($current_page) && 'dsrbfw-get-started' === $current_page ? 'active' : '';
    $dsrbfw_quick_info     = isset($current_page) && 'dsrbfw-information' === $current_page ? 'active' : '';

    $dsrbfw_display_submenu = !empty( $dsrbfw_settings_menu ) && 'active' === $dsrbfw_settings_menu ? 'display:inline-block' : 'display:none';

    //enable this when you want to use promotional bar (we are send parameter blank as we dont want to enqueue any script)
	$dsrbfw_admin_object = new Revenue_Booster_For_Woocommerce_Admin( '', '' );
?>
<div class="wrap">
    <div id="dotsstoremain" class="dsrbfw-section">
        <div class="all-pad">
            <?php 
            //enable this when you want to use promotional bar
            $dsrbfw_admin_object->dsrbfw_get_promotional_bar( $plugin_slug ); ?>
            <hr class="wp-header-end" />
            <header class="dots-header">
                <div class="dots-plugin-details">
                    <div class="dots-header-left">
                        <div class="dots-logo-main">
                            <img src="<?php echo esc_url(DSRBFW_PLUGIN_LOGO_URL); ?>" alt="<?php esc_attr_e( 'Plugin LOGO', 'revenue-booster-for-woocommerce' ); ?>"/>
                        </div>
                        <div class="plugin-name">
                            <div class="title"><?php echo esc_html( DSRBFW_PLUGIN_NAME ); ?></div>
                        </div>
                        <span class="version-label"><?php echo esc_html( DSRBFW_VERSION_LABEL ); ?></span>
                        <span class="version-number">v<?php echo esc_html( DSRBFW_PLUGIN_VERSION ); ?></span>
                    </div>
                    <div class="dots-header-right">
                        <div class="button-dots">
                            <a target="_blank" href="<?php echo esc_url('http://www.thedotstore.com/support/'); ?>">
                                <?php esc_html_e('Support', 'revenue-booster-for-woocommerce') ?>
                            </a>
                        </div>
                        <div class="button-dots">
                            <a target="_blank" href="<?php echo esc_url('https://www.thedotstore.com/feature-requests/'); ?>">
                                <?php esc_html_e('Suggest', 'revenue-booster-for-woocommerce') ?>
                            </a>
                        </div>
                        <div class="button-dots last-link-button">
                            <a target="_blank" href="<?php echo esc_url(DSRBFW_DOC_LINK); ?>">
                                <?php esc_html_e('Help', 'revenue-booster-for-woocommerce') ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="dots-menu-main">
                    <nav>
                        <ul>
                            <li>
                                <a class="dotstore_plugin <?php echo esc_attr($dsrbfw_global_settings); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'dsrbfw-global-settings'), admin_url('admin.php'))); ?>"><?php esc_html_e('Global Settings', 'revenue-booster-for-woocommerce'); ?></a>
                            </li>
                            <li>
                                <a class="dotstore_plugin <?php echo esc_attr($dsrbfw_ob_at_checkout_list); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'dsrbfw-ob-at-checkout-list'), admin_url('admin.php'))); ?>"><?php esc_html_e('Order Bumps', 'revenue-booster-for-woocommerce'); ?></a>
                            </li>
                            <li>
                                <a class="dotstore_plugin <?php echo esc_attr($dsrbfw_ob_before_order_list); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'dsrbfw-ob-before-order-list'), admin_url('admin.php'))); ?>"><?php esc_html_e('After Checkout', 'revenue-booster-for-woocommerce'); ?></a>
                            </li>
                            <li>
                                <a class="dotstore_plugin <?php echo esc_attr($dsrbfw_settings_menu); ?>" href="<?php  echo esc_url( add_query_arg(array('page' => 'dsrbfw-get-started'), admin_url('admin.php'))); ?>"><?php esc_html_e('Settings', 'revenue-booster-for-woocommerce'); ?></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </header>
            <div class="dots-settings-inner-main">
                <div class="dots-settings-left-side">
                    <div class="dotstore-submenu-items" style="<?php echo esc_attr($dsrbfw_display_submenu); ?>">
                    <ul>
                        <li><a class="<?php echo esc_attr($dsrbfw_get_started); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'dsrbfw-get-started'), admin_url('admin.php'))); ?>"><?php esc_html_e('About', 'revenue-booster-for-woocommerce'); ?></a></li>
                        <li><a class="<?php echo esc_attr($dsrbfw_quick_info); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'dsrbfw-information'), admin_url('admin.php'))); ?>"><?php esc_html_e('Quick info', 'revenue-booster-for-woocommerce'); ?></a></li>
                        <li><a href="<?php echo esc_url('https://www.thedotstore.com/plugins/'); ?>" target="_blank"><?php esc_html_e('Shop Plugins', 'revenue-booster-for-woocommerce'); ?></a></li>
                    </ul>
                </div>
                <!-- <hr class="wp-header-end" /> -->
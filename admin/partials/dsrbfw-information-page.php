<?php
/**
 * Handles plugin information page.
 * 
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( DSRBFW_PLUGIN_HEADER_LINK );
?>
    <div class="dsrbfw-section-left">
        <div class="dsrbfw-main-table res-cl">
            <h2><?php esc_html_e( 'Quick info', 'revenue-booster-for-woocommerce' ); ?></h2>
            <table class="table-outer">
                <tbody>
                <tr>
                    <td class="fr-1"><?php esc_html_e( 'Product Type', 'revenue-booster-for-woocommerce' ); ?></td>
                    <td class="fr-2"><?php esc_html_e( 'WooCommerce Plugin', 'revenue-booster-for-woocommerce' ); ?></td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e( 'Product Name', 'revenue-booster-for-woocommerce' ); ?></td>
                    <td class="fr-2"><?php echo esc_html( DSRBFW_PLUGIN_NAME ); ?></td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e( 'Installed Version', 'revenue-booster-for-woocommerce' ); ?></td>
                    <td class="fr-2">
                        <?php /* translators: %1$s is replaced with "string" which show License Name and %2$s is replaced with "string" which show Version number */?>
                        <?php echo sprintf( esc_html__( '%1$s v%2$s', 'revenue-booster-for-woocommerce' ), esc_html( DSRBFW_VERSION_LABEL ), esc_html(DSRBFW_PLUGIN_VERSION) );?>
                    </td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e( 'License & Terms of use', 'revenue-booster-for-woocommerce' ); ?></td>
                    <td class="fr-2">
                        <a target="_blank" href="<?php echo esc_url( 'www.thedotstore.com/terms-and-conditions' ); ?>">
							<?php esc_html_e( 'Click here', 'revenue-booster-for-woocommerce' ); ?>
                        </a>
						<?php esc_html_e( ' to view license and terms of use.', 'revenue-booster-for-woocommerce' ); ?>
                    </td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e( 'Help & Support', 'revenue-booster-for-woocommerce' ); ?></td>
                    <td class="fr-2">
                        <ul>
                            <li>
                                <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'dsrbfw-get-started' ), admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Quick Start', 'revenue-booster-for-woocommerce' ); ?></a>
                            </li>
                            <li><a target="_blank" href="<?php echo esc_url( DSRBFW_DOC_LINK ); ?>"><?php esc_html_e( 'Guide Documentation', 'revenue-booster-for-woocommerce' ); ?></a>
                            </li>
                            <li><a target="_blank" href="<?php echo esc_url( 'www.thedotstore.com/support' ); ?>"><?php esc_html_e( 'Support Forum', 'revenue-booster-for-woocommerce' ); ?></a>
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e( 'Localization', 'revenue-booster-for-woocommerce' ); ?></td>
                    <td class="fr-2"><?php esc_html_e( 'English, German', 'revenue-booster-for-woocommerce' ); ?></td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
<?php
require_once( DSRBFW_PLUGIN_FOOTER_LINK );
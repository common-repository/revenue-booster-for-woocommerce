<?php
/**
 * Handles plugin getting started page.
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
		<h2><?php esc_html_e( 'Getting Started', 'revenue-booster-for-woocommerce' ); ?></h2>
		<table class="table-outer">
			<tbody>
				<tr>
					<td class="fr-2">
						<p class="block textgetting"><?php esc_html_e( 'This will create after testing!', 'revenue-booster-for-woocommerce' ); ?></p>
						<p class="block textgetting"><?php esc_html_e( 'This is black page for details of plugin', 'revenue-booster-for-woocommerce' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php
require_once( DSRBFW_PLUGIN_FOOTER_LINK );
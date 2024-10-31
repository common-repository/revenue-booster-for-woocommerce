<?php

/**
 * Provide a Order Bump Before Order configuration form view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.thedotstore.com/
 * @since      1.0.0
 *
 * @package    Revenue_Booster_For_Woocommerce
 * @subpackage Revenue_Booster_For_Woocommerce/admin/partials
 */


if ( ! defined( 'ABSPATH' ) ) { 
    exit;
}

/**
 * DSRBFW_Order_Bump_Before_Order_Listing class.
 */
if ( ! class_exists( 'DSRBFW_Order_Bump_Before_Order_Listing' ) ) {

    class DSRBFW_Order_Bump_Before_Order_Listing {
        
        /**
         * Display output
         *
         * @since 1.0.0
         *
         * @uses dsrbfw_save_method
         * @uses dsrbfw_add_rule_form
         * @uses dsrbfw_delete_method
         * @uses dsrbfw_duplicate_method
         * @uses dsrbfw_list_methods_screen
         *
         * @access   public
         */
        public static function dsrbfw_listing_output() {

            $action             = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            $post_id_request    = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );

            if ( isset( $action ) && ! empty( $action ) ) {
                if ( 'add' === $action ) {
                    self::dsrbfw_save_method();
                    self::dsrbfw_add_rule_form();
                } elseif ( 'edit' === $action ) {
                    self::dsrbfw_save_method( $post_id_request );
                    self::dsrbfw_add_rule_form();
                } elseif ( 'delete' === $action ) {
                    self::dsrbfw_delete_method( $post_id_request );
                } elseif ( 'duplicate' === $action ) {
                    self::dsrbfw_duplicate_method( $post_id_request );
                } else {
                    self::dsrbfw_list_methods_screen();
                }
            } else {
                self::dsrbfw_list_methods_screen();
            }
        }

        /**
         * Save Order Bump Before Order data
         *
         * @param int $post_id
         * @since    1.0.0
         *
         */
        public static function dsrbfw_save_method( $post_id = 0 ) {

            $dsrbfw_ob_bo_rule_save  = filter_input( INPUT_POST, 'dsrbfw_ob_bo_rule_save', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            
            //It will only work for after add new rule save value
            if( wp_verify_nonce( sanitize_text_field( $dsrbfw_ob_bo_rule_save ), 'dsrbfw_ob_bo_rule_save_action' ) ) {

                $get_dsrbfw_ob_bo_status           = filter_input( INPUT_POST, 'dsrbfw_ob_bo_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                $get_dsrbfw_ob_bo_product          = filter_input( INPUT_POST, 'dsrbfw_ob_bo_product', FILTER_SANITIZE_NUMBER_INT );
                $get_dsrbfw_ob_bo                  = filter_input( INPUT_POST, 'dsrbfw_ob', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );

                $dsrbfw_ob_bo_status               = !empty($get_dsrbfw_ob_bo_status) ? sanitize_text_field($get_dsrbfw_ob_bo_status) : 'draft';
                $dsrbfw_ob_bo_product              = !empty($get_dsrbfw_ob_bo_product) ? sanitize_text_field($get_dsrbfw_ob_bo_product) : '';

                $dsrbfw_ob_conditions = dsrbfw()->dsrbfw_prepare_rule_filter_array($get_dsrbfw_ob_bo);
                
                if ( '' === $post_id || 0 === $post_id ) {
                    $dsrbfw_count = self::dsrbfw_count_method();
                    $post_title = 'Order Bump After Checkout - ' . $dsrbfw_count + 1;
                    $dsrbfw_args = array(
                        'post_title'  => wp_strip_all_tags( $post_title ),
                        'post_status' => $dsrbfw_ob_bo_status,
                        'post_type'   => DSRBFW_AFTER_OB_POST_TYPE,
                        'menu_order'  => $dsrbfw_count + 1,
                    );
                    $post_id  = wp_insert_post( $dsrbfw_args );
                    $message_type = 'created';
                } else {
                    $dsrbfw_args = array(
                        'ID'          => intval( $post_id ),
                        'post_status' => $dsrbfw_ob_bo_status,
                        'post_type'   => DSRBFW_AFTER_OB_POST_TYPE,
                    );
                    $post_id  = wp_update_post( $dsrbfw_args );
                    $message_type = 'saved';
                }

                if ( '' !== $post_id && $post_id > 0 ) {

                    update_post_meta( $post_id, 'dsrbfw_ob_bo_product', $dsrbfw_ob_bo_product );
                    update_post_meta( $post_id, 'dsrbfw_ob_conditions', $dsrbfw_ob_conditions );
                }

                wp_safe_redirect( add_query_arg( array(
                    'page'      => 'dsrbfw-ob-before-order-list',
                    'action'    => 'edit',
                    'id'        => $post_id,
                    'message'   => $message_type
                ), admin_url( 'admin.php' ) ) );

                wp_die( );
            }
        }

        /**
         * Count total Order Bump Before Order rules
         *
         * @return int $dsrbfw_list
         * @since    1.0.0
         *
         */
        public static function dsrbfw_count_method() {
            $dsrbfw_args = array(
                'post_type'      => DSRBFW_AFTER_OB_POST_TYPE,
                'post_status'    => array( 'publish', 'draft' ),
                'posts_per_page' => -1,
            );
            $dsrbfw_query  = new WP_Query( $dsrbfw_args );
            $dsrbfw_list   = $dsrbfw_query->posts;

            return count( $dsrbfw_list );
        }

        /**
         * Add Order Bump Before Order Rule data
         *
         * @since    1.0.0
         */
        public static function dsrbfw_add_rule_form() {
            require_once( plugin_dir_path( __FILE__ ) . 'dsrbfw-ob-before-order-add-new.php' );
        }

        /**
         * dsrbfw_list_methods_screen function.
         *
         * @since    1.0.0
         *
         * @uses DSRBFW_OB_Before_Order_List_Table class
         * @uses DSRBFW_OB_Before_Order_List_Table::process_bulk_action()
         * @uses DSRBFW_OB_Before_Order_List_Table::prepare_items()
         * @uses DSRBFW_OB_Before_Order_List_Table::search_box()
         * @uses DSRBFW_OB_Before_Order_List_Table::display()
         *
         * @access public
         *
         */
        public static function dsrbfw_list_methods_screen() {
            
            if ( ! class_exists( 'DSRBFW_OB_Before_Order_List_Table' ) ) {
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'list-tables/ob-before-order/class-ob-before-order-list-table.php';
            }
            $link = add_query_arg( array(
                'page'   => 'dsrbfw-ob-before-order-list',
                'action' => 'add'
            ), admin_url( 'admin.php' ) );

            require_once( DSRBFW_PLUGIN_HEADER_LINK );
            ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="dsrbfw-section-left">
                        <div class="dsrbfw-main-table res-cl dsrbfw-add-rule-page">
                            <h1 class="wp-heading-inline"><?php esc_html_e( 'Order Bump After Checkout', 'revenue-booster-for-woocommerce' ); ?></h1>
                            <a class="page-title-action dots-btn-with-brand-color" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'Add New', 'revenue-booster-for-woocommerce' ); ?></a>
                            <?php
                            //We have usef GET here because of 'prepare_items' funciton need search term while pagination else pagination not work properly
                            $request_s = filter_input( INPUT_GET, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                            if ( isset( $request_s ) && ! empty( $request_s ) ) {
                                /* translators: %s is replaced with "string" which show searched string */
                                echo sprintf( '<span class="subtitle">' . esc_html__( 'Search results for &#8220;%s&#8221;', 'revenue-booster-for-woocommerce' ) . '</span>', esc_html( $request_s ) );
                            }
                            wp_nonce_field('ob_at_checkout_list_action','ob_at_checkout_list');
                            $DSRBFW_OB_Before_Order_List_Table = new DSRBFW_OB_Before_Order_List_Table();
                            $DSRBFW_OB_Before_Order_List_Table->process_bulk_action();
                            $DSRBFW_OB_Before_Order_List_Table->prepare_items();
                            $DSRBFW_OB_Before_Order_List_Table->search_box( esc_html__( 'Search', 'revenue-booster-for-woocommerce' ), 'shipping-method' );
                            $DSRBFW_OB_Before_Order_List_Table->display();
                            ?>
                        </div>
                    </div>
                </form>
            <?php
            require_once( DSRBFW_PLUGIN_FOOTER_LINK );
        }

        /**
         * Delete Order Bump Before Order Rule
         *
         * @param int $ob_bo_id
         *
         * @access   public
         *
         * @since    1.0.0
         *
         */
        public static function dsrbfw_delete_method( $ob_bo_id ) {

            $_wpnonce = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

            $getnonce = wp_verify_nonce( $_wpnonce, 'del_' . $ob_bo_id );

            if ( isset( $getnonce ) && 1 === $getnonce ) {

                // Delete the rule
                wp_delete_post( $ob_bo_id );
                
                wp_safe_redirect( add_query_arg( array(
                    'page'    => 'dsrbfw-ob-before-order-list',
                    'message' => 'deleted'
                ), admin_url( 'admin.php' ) ) );
                
                exit;
            }
        }

        /**
         * Duplicate Order Bump Before Order rule data
         *
         * @param int $id
         *
         * @access   public
         *
         * @since    1.0.0
         *
         */
        public static function dsrbfw_duplicate_method( $id ) {

            $_wpnonce = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

            $getnonce = wp_verify_nonce( $_wpnonce, 'duplicate_' . $id );
            if ( isset( $getnonce ) && 1 === $getnonce ) {

                // Get all the original post data
                $post = get_post( $id );

                // Get current user and make it new post user (for duplicate post)
                $current_user    = wp_get_current_user();
                $new_post_author = $current_user->ID;

                // If post data exists, duplicate the data into new duplicate post
                if ( isset( $post ) && null !== $post ) {
                    $dsrbfw_rule_count = self::dsrbfw_count_method();
                    $args = array(
                        'comment_status' => $post->comment_status,
                        'ping_status'    => $post->ping_status,
                        'post_author'    => $new_post_author,
                        'post_content'   => $post->post_content,
                        'post_excerpt'   => $post->post_excerpt,
                        'post_name'      => $post->post_name,
                        'post_parent'    => $post->post_parent,
                        'post_password'  => $post->post_password,
                        'post_status'    => 'draft',
                        'post_title'     => $post->post_title . ' (duplicate of #' . $id . ')',
                        'post_type'      => DSRBFW_AFTER_OB_POST_TYPE,
                        'to_ping'        => $post->to_ping,
                        'menu_order'     => $dsrbfw_rule_count + 1,
                    );

                    // Duplicate the post by wp_insert_post() function
                    $duplicate_post_id = wp_insert_post( $args );

                    // Get all postmeta from original post
                    $post_meta_data = get_post_meta( $id );
                    if( 0 !== count($post_meta_data) ){
                        foreach( $post_meta_data as $meta_key => $meta_data ){
                            $meta_value = maybe_unserialize( $meta_data[0] );
                            update_post_meta( $duplicate_post_id, $meta_key, $meta_value );
                        }
                    }

                    $admin_url = admin_url( 'admin.php' );

                    // Redirect after duplicate rule
                    wp_safe_redirect( add_query_arg( array(
                        'page'      => 'dsrbfw-ob-before-order-list',
                        'id'        => $duplicate_post_id,
                        'action'    => 'edit',
                        'message'   => 'duplicated'
                    ), $admin_url ) );
                    exit();
                }
            }
        }
    }
}
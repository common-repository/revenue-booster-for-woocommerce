<?php
/**
 * Handles plugin rules listing
 * 
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * DSRBFW_OB_At_Checkout_List_Table class.
 *
 * @extends WP_List_Table
 */
if ( ! class_exists( 'DSRBFW_OB_At_Checkout_List_Table' ) ) {

	class DSRBFW_OB_At_Checkout_List_Table extends WP_List_Table {

        private static $dsrbfw_found_items = 0;
        /**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct( array(
				'singular' => 'dsrbfw_at_checkout',
				'plural'   => 'dsrbfw_at_checkouts',
				'ajax'     => false
			) );
		}

        /**
		 * get_columns function.
		 *
		 * @return  array
		 * @since 1.0.0
		 *
		 */
		public function get_columns() {
			$column_array = array(
				'cb'                => '<input type="checkbox" />',
				'title'             => esc_html__( 'Title', 'revenue-booster-for-woocommerce' ),
            );
            $column_array += array(
				'product'           => esc_html__( 'Product', 'revenue-booster-for-woocommerce' ),
                'status'            => esc_html__( 'Status', 'revenue-booster-for-woocommerce' ),
                'date'              => esc_html__( 'Date', 'revenue-booster-for-woocommerce' ),
			);
			return $column_array;
		}

        /**
		 * get_sortable_columns function.
		 *
		 * @return array
		 * @since 1.0.0
		 *
		 */
		protected function get_sortable_columns() {
			$columns = array(
				'title'  => array( 'title', true ),
				'date'   => array( 'date', false ),
			);

			return $columns;
		}

        /**
		 * Checkbox column
		 *
		 * @param string
		 *
		 * @return mixed
		 * @since 1.0.0
		 *
		 */
		public function column_cb( $item ) {
			if ( ! $item->ID ) {
				return;
			}

			return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', 'method_id_cb', esc_attr( $item->ID ) );
		}

        /**
		 * Output the shipping name column.
		 *
		 * @param object $item
		 *
		 * @return string
		 * @since 1.0.0
		 *
		 */
		public function column_title( $item ) {
			$editurl = add_query_arg( array(
				'page'   => 'dsrbfw-ob-at-checkout-list',
				'id'   => $item->ID,
				'action' => 'edit',
			), admin_url( 'admin.php' ) );
			$method_name = '<strong>
                            <a href="' . esc_url( $editurl ) . '" class="row-title">' . esc_html( $item->post_title ) . '</a>
                        </strong>';

			echo wp_kses( $method_name, dsrbfw()->dsrbfw_allowed_html_tags() );
		}

        /**
		 * Output the product name which will show as offered prosuct at checkout in order bump.
		 *
		 * @param object $item
		 *
		 * @return string
		 * @since 1.0.0
		 *
		 */
		public function column_product( $item ) {

            $product_id 		= get_post_meta( $item->ID, 'dsrbfw_ob_ac_product', true );
            $product = wc_get_product( $product_id );
            if( $product ){
                $product_name = $product->get_formatted_name();
                echo sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( get_edit_post_link( $product->get_id() ) ), esc_html( rawurldecode( wp_strip_all_tags( $product_name ) ) ) );
            } else {
                esc_html_e( 'N/A', 'revenue-booster-for-woocommerce' );
            }
		}

        /**
		 * Output the method enabled column.
		 *
		 * @param object $item
		 *
		 */
		public function column_status( $item ) {
			if ( 0 === $item->ID ) {
				return esc_html__( 'Trash', 'revenue-booster-for-woocommerce' );
			}

			$rule_status     	= get_post_status( $item->ID );
			if( !empty( $rule_status ) ) {
            ?>
            <label class="switch">
                <input type="checkbox" class="status_switch" name="dsrbfw_ob_ac_status" value="on" <?php checked( 'publish', $rule_status, true ); ?> data-obid="<?php echo esc_attr( $item->ID ); ?>">
                <div class="slider round"></div>
            </label>
            <?php
			}
		}

        /**
		 * Output the method date column.
		 *
		 * @param object $item
		 *
		 * @return mixed $item->post_date;
		 * @since 1.0.0
		 *
		 */
		public function column_date( $item ) {
			if ( 0 === $item->ID ) {
				return esc_html__( 'N/A', 'revenue-booster-for-woocommerce' );
			}
            
            $date_obj = date_create($item->post_date);
            $new_format = sprintf( '%s at %s', date_format( $date_obj, get_option('date_format')), date_format( $date_obj, get_option('time_format')));

			return $new_format;
		}

        /**
         * get_sortable_columns function.
		 *
		 * @since 1.0.0
		 */
		public function no_items() {
            esc_html_e( 'No at checkout\'s order bump found!', 'revenue-booster-for-woocommerce' );
		}

        /**
		 * Get Methods to display
		 *
		 * @since 1.0.0
		 */
		public function prepare_items() {
            $this->prepare_column_headers();
			$per_page = $this->get_items_per_page( 'dsrbfw_ob_at_checkout_per_page' );

			$get_search  = filter_input( INPUT_POST, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$get_orderby = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$get_order   = filter_input( INPUT_GET, 'order', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$get_status  = filter_input( INPUT_GET, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

			$args = array(
				'posts_per_page' => $per_page,
				'orderby'        => array(
                    'menu_order'    => 'ASC',
                    'post_date'     => 'DESC',
                ),
				'offset'         => ( $this->get_pagenum() - 1 ) * $per_page,
                'post_type'      => DSRBFW_BEFORE_OB_POST_TYPE,
			);

            //Search with pagination
			if ( isset( $get_search ) && ! empty( $get_search ) ) {
				$new_url = esc_url_raw( add_query_arg('s', $get_search) );
				wp_safe_redirect($new_url);
				exit;
			} elseif( isset( $get_search ) && empty( $get_search ) ) {
				$new_url = esc_url_raw( remove_query_arg('s') );
				wp_safe_redirect($new_url);
				exit;
			} else {
				$get_search = filter_input( INPUT_GET, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
				if ( isset( $get_search ) && ! empty( $get_search ) ) {
					$args['s'] = trim( wp_unslash( $get_search ) );
				}
			}

			if ( isset( $get_orderby ) && ! empty( $get_orderby ) ) {
				if ( 'title' === $get_orderby ) {
					$args['orderby'] = 'title';
                } elseif ( 'date' === $get_orderby ) {
					$args['orderby'] = 'date';
				}
			}

			if ( isset( $get_order ) && ! empty( $get_order ) ) {
				if ( 'asc' === strtolower( $get_order ) ) {
					$args['order'] = 'ASC';
				} elseif ( 'desc' === strtolower( $get_order ) ) {
					$args['order'] = 'DESC';
				}
			}

			if( !empty($get_status) ){
                if( 'enable' === strtolower($get_status) ){
                    $args['post_status'] = 'publish';
                } elseif( 'disable' === strtolower($get_status) ) {
                    $args['post_status'] = 'draft';
                } else {
                    $args['post_status'] = 'all';
                }
            }

			$this->items = $this->dsrbfw_find( $args );

			$total_items = $this->dsrbfw_count_method();

			$total_pages = ceil( $total_items / $per_page );

			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'total_pages' => $total_pages,
				'per_page'    => $per_page,
			) );
		}

        /**
		 * Find post data
		 *
		 * @param mixed $args
		 * @param string $get_orderby
		 *
		 * @return array $posts
		 * @since 1.0.0
		 *
		 */
		public static function dsrbfw_find( $args = '' ) {
			$defaults = array(
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'offset'         => 0,
				'orderby'        => array (
                    'ID' => 'ASC',
                )
			);

			$args = wp_parse_args( $args, $defaults );

			$args['post_type'] = DSRBFW_BEFORE_OB_POST_TYPE;

			$dsrbfw_query = new WP_Query( $args );
			$posts          = $dsrbfw_query->query( $args );

            self::$dsrbfw_found_items = $dsrbfw_query->found_posts;

			return $posts;
		}

        /**
		 * Count post data
		 *
		 * @return string
		 * @since 1.0.0
		 *
		 */
		public static function dsrbfw_count_method() {
			return self::$dsrbfw_found_items;
		}

        /**
		 * Display bulk action in filter
		 *
		 * @return array $actions
		 * @since 1.0.0
		 *
		 */
		public function get_bulk_actions() {
			$actions = array(
				'enable'  => esc_html__( 'Enable', 'revenue-booster-for-woocommerce' ),
				'disable' => esc_html__( 'Disable', 'revenue-booster-for-woocommerce' ),
				'delete'  => esc_html__( 'Delete', 'revenue-booster-for-woocommerce' )
			);

			return $actions;
		}

        /**
		 * Process bulk actions
		 *
		 * @since 1.0.0
		 */
		public function process_bulk_action() {
            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
            if( !empty( $nonce ) ){
                $action = 'bulk-' . $this->_args['plural'];

                if ( ! wp_verify_nonce( $nonce, $action ) ) {
                    dsrbfw()->dsrbfw_updated_message('nonce_check');
                }

                $action = $this->current_action();

                $get_method_id_cb   = filter_input( INPUT_POST, 'method_id_cb', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
			    $items              = ! empty( $get_method_id_cb ) ? array_map( 'absint', wp_unslash( $get_method_id_cb ) ) : array();

                switch ( $action ) {

                    case 'delete':
                        foreach ( $items as $dsrbfw_ob_ac_id ) {
                            wp_delete_post( $dsrbfw_ob_ac_id );
                        }
                        dsrbfw()->dsrbfw_updated_message('deleted');
                        break;
        
                    case 'enable':
                        foreach ( $items as $dsrbfw_ob_ac_id ) {
                            $post_args   = array(
                                'ID'          => $dsrbfw_ob_ac_id,
                                'post_status' => 'publish',
                                'post_type'   => DSRBFW_BEFORE_OB_POST_TYPE,
                            );
                            wp_update_post( $post_args );
                        }
                        dsrbfw()->dsrbfw_updated_message('enabled');
                        break;
        
                    case 'disable':
                        foreach ( $items as $dsrbfw_ob_ac_id ) {
                            $post_args   = array(
                                'ID'          => $dsrbfw_ob_ac_id,
                                'post_status' => 'draft',
                                'post_type'   => DSRBFW_BEFORE_OB_POST_TYPE,
                            );
                            wp_update_post( $post_args );
                        }
                        dsrbfw()->dsrbfw_updated_message('disabled');
                        break;

                    default:
                        // do nothing or something else
                        return;
                        break;
                }
            }
        }

        /**
		 * Generates and displays row action links.
		 *
		 * @param object $item Link being acted upon.
		 * @param string $column_name Current column name.
		 * @param string $primary Primary column name.
		 *
		 * @return string Row action output for links.
		 * @since 1.0.0
		 *
		 */
		protected function handle_row_actions( $item, $column_name, $primary ) {
			if ( $primary !== $column_name ) {
				return '';
			}

			$edit_method_url = add_query_arg( array(
				'page'   => 'dsrbfw-ob-at-checkout-list',
				'id'   => $item->ID,
				'action' => 'edit',
			), admin_url( 'admin.php' ) );
			$editurl         = esc_url($edit_method_url);

			$delete_method_url = add_query_arg( array(
				'page'   => 'dsrbfw-ob-at-checkout-list',
				'action' => 'delete',
				'id'   => $item->ID
			), admin_url( 'admin.php' ) );
			$delurl            = wp_nonce_url( $delete_method_url, 'del_' . $item->ID, '_wpnonce' );

            $duplicate_method_url = add_query_arg( array(
				'page'   => 'dsrbfw-ob-at-checkout-list',
				'action' => 'duplicate',
				'id'   => $item->ID
			), admin_url( 'admin.php' ) );
			$duplicateurl      = wp_nonce_url( $duplicate_method_url, 'duplicate_' . $item->ID, '_wpnonce' );

			$actions            = array();
            if( DSRBFW__DEV_MODE ) {
			    $actions['ID']      = esc_html__( '#', 'revenue-booster-for-woocommerce' ) . $item->ID;
            }
			$actions['edit']    = '<a href="' . esc_url($editurl) . '">' . esc_html__( 'Edit', 'revenue-booster-for-woocommerce' ) . '</a>';
			$actions['delete']  = '<a href="' . esc_url($delurl) . '">' . esc_html__( 'Delete', 'revenue-booster-for-woocommerce' ) . '</a>';
			$actions['duplicate']   = '<a href="' . esc_url($duplicateurl) . '">' . esc_html__( 'Duplicate', 'revenue-booster-for-woocommerce' ) . '</a>';

			return $this->row_actions( $actions );
		}
    }
}
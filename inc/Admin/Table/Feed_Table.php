<?php
namespace GG_Woo_Feed\Admin\Table;

use GG_Woo_Feed\Common\Provider_Attributes;

class Feed_Table extends \WP_List_Table {

	/**
	 * Get list columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return [
			'cb'           => '<input type="checkbox" />',
			'enable'       => __( 'Auto Update', 'gg-woo-feed' ),
			'title'        => __( 'Feed Name', 'gg-woo-feed' ),
			// 'count'        => __( 'Count Products', 'gg-woo-feed' ),
			'provider'     => __( 'Provider', 'gg-woo-feed' ),
			'type'         => __( 'Type', 'gg-woo-feed' ),
			'cats'         => __( 'Categories', 'gg-woo-feed' ),
			'last_updated' => __( 'Last updated', 'gg-woo-feed' ),
			'action'       => __( 'Action', 'gg-woo-feed' ),
		];
	}

	public function get_sortable_columns() {
		return [
			'title' => [ 'booktitle', false ],
		];
	}

	/**
	 * bulk actions
	 */
	public function get_bulk_actions() {
		return [
			'delete' => __( 'Delete', 'gg-woo-feed' ),
		];
	}

	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="feeds[]" value="%1$s" />', $item['option_name'] );
	}

	/**
	 * print column name
	 */
	public function column_title( $item ) {
		$edit_nonce   = wp_create_nonce( 'gg_woo_feed-nonce-edit-feed' );
		$delete_nonce = wp_create_nonce( 'gg_woo_feed-nonce-delete-feed' );
		$edit_link    = admin_url( 'admin.php?page=gg-woo-feed-feeds&action=edit&feed=' . $item['option_name'] . '&nonce=' . $edit_nonce );
		$actions      = [
			'edit'   => '<a href="' . $edit_link . '">' . __( 'Edit', 'gg-woo-feed' ) . '</a>',
			'delete' => '<a href="' . admin_url( 'admin.php?page=gg-woo-feed-feeds&action=delete&feed=' . $item['option_name'] ) . '&nonce=' . $delete_nonce .
			            '" class="submitdelete">' . __( 'Delete', 'gg-woo-feed' ) . '</a>',
		];

		$name = $item['option_name'];

		$config = maybe_unserialize( maybe_unserialize( $item['option_value'] ) );
		if ( isset( $config['feedqueries'], $config['feedqueries']['filename'] ) ) {
			$name = $config['feedqueries']['filename'];
		}

		return sprintf(
			'%1$s %3$s',
			sprintf( '<a href="%s"><b>%s</b></a>', $edit_link, $name ),
			$item['option_name'],
			$this->row_actions( $actions )
		);
	}

	public function column_count( $item ) {
		return '---';
	}

	public function column_enable( $item ) {
		$item_info = $this->get_item_info( $item );
		$checked   = ! isset( $item_info['status'] ) || ( isset( $item_info['status'] ) && 1 == $item_info['status'] ) ? 'checked' : '';
		$switch    = '<label class="gg_woo_feed-enable-switch-input">';
		$switch    .= '<input type="checkbox" id="' . esc_attr( $item['option_name'] ) . '" value="on" class="js-gg_woo_feed-change-status gg_woo_feed-enable-switch form-control" ' . $checked . ' />';
		$switch    .= '<span class="slider round"></span>';
		$switch    .= '</label>';

		return $switch;
	}

	public function column_provider( $item ) {
		$item_info = $this->get_item_info( $item );
		$provider  = $item_info['feedqueries']['provider'];
		$providers = Provider_Attributes::get_providers();

		return isset( $providers[ $provider ] ) ? esc_html( $providers[ $provider ] ) : ucwords( str_replace( '_', ' ', $provider ) );
	}

	public function column_type( $item ) {
		$item_info = $this->get_item_info( $item );
		$types     = gg_woo_feed_get_file_types();
		$type      = $item_info['feedqueries']['feed_type'];

		return isset( $types[ $type ] ) ? esc_html( $types[ $type ] ) : strtoupper( str_replace( '_', ' ', $type ) );
	}

	public function column_cats( $item ) {
		$item_info = $this->get_item_info( $item );

		$cat_all = $item_info['feedqueries']['feed_category_all'];
		$cats    = $item_info['feedqueries']['feed_category'];

		if ( 'on' === $cat_all || ! $cats ) {
			$cat_text = esc_html__( 'All', 'gg-woo-feed' );
		} else {
			foreach ( $cats as $cat_slug ) {
				$cat_obj = get_term_by( 'slug', $cat_slug, 'product_cat' );
				if ( $cat_obj ) {
					$cat_array[] = $cat_obj->name;
				}
			}

			$cat_text = implode( ', ', $cat_array );
		}

		return $cat_text;
	}

	public function column_last_updated( $item ) {
		$item_info = $this->get_item_info( $item );

		return esc_html( $item_info['last_updated'] );
	}

	public function column_action( $item ) {
		$item_info = $this->get_item_info( $item );

		$button = sprintf(
			'<a href="#" title="%2$s" class="gg_woo_feed-action-button js-copy-feed" data-clipboard-text="%1$s"><span class="dashicons dashicons-admin-page" aria-hidden="true"></span></a>',
			esc_url( $item_info['url'] ),
			esc_html__( 'Copy', 'gg-woo-feed' )
		);

		$button .= sprintf(
			'<a href="%1$s" title="%2$s" class="gg_woo_feed-action-button" target="_blank"><span class="dashicons dashicons-external" aria-hidden="true"></span></a>',
			esc_url( $item_info['url'] ),
			esc_html__( 'Open', 'gg-woo-feed' )
		);

		$button .= sprintf(
			'<a href="#" title="%1$s" class="gg_woo_feed-action-button js-regenerate-feed" id="%2$s"><span class="dashicons dashicons-update" aria-hidden="true"></span></a>',
			esc_html__( 'Regenerate', 'gg-woo-feed' ),
			$item['option_name']
		);

		$button .= sprintf(
			'<a href="%1$s" title="%2$s" class="gg_woo_feed-action-button js-download-feed" download><span class="dashicons dashicons-download" aria-hidden="true"></span></a>',
			esc_url( $item_info['url'] ),
			esc_html__( 'Download', 'gg-woo-feed' )
		);

		return $button;
	}

	protected function get_item_info( $item ) {
		return maybe_unserialize( get_option( $item['option_name'] ) );
	}

	/**
	 * No items found text.
	 */
	public function no_items() {
		esc_html_e( 'No feeds available.', 'gg-woo-feed' );
	}

	/**
	 * get items
	 */
	public function get_items() {
		$feeds = static::get_feeds();

		return [
			'items'       => $feeds,
			'total_items' => count( $feeds ),
		];
	}

	public static function get_feeds() {
		global $wpdb;
		$result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name LIKE %s ORDER BY option_id DESC;", 'gg_woo_feed_feed_%' ), 'ARRAY_A' );

		return $result;
	}

	/**
	 * hidden columns
	 */
	public function get_hidden_columns() {
		return [
			'feed_id',
		];
	}

	/**
	 * Prepare items.
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$per_page = $this->get_items_per_page( 'gg_woo_feed_item_per_page', 99 );

		// only ncessary because we have sample data
		$data              = $this->get_items();
		$this->items       = $data['items'];
		$this->total_items = $data['total_items'];

		$this->_column_headers = [ $columns, $hidden, $sortable ];
		$this->set_pagination_args( [
			'total_items' => $this->total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $this->total_items / $per_page ),
		] );
	}
}

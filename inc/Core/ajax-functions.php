<?php

use GG_Woo_Feed\Common\Dropdown;
use GG_Woo_Feed\Common\Generate_Wizard;
use GG_Woo_Feed\Common\Model\Provider;
use GG_Woo_Feed\Common\Upload;

/**
 * Get template mapping via AJAX.
 *
 * @return void
 */
function gg_woo_feed_provider_mapping_view() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_send_json_error( esc_html__( 'Unauthorized Action.', 'gg-woo-feed' ) );
		die();
	}
	global $feed_queries, $dropdown, $provider;
	$provider = isset( $_REQUEST['provider'] ) && ! empty( $_REQUEST['provider'] ) ? sanitize_text_field( $_REQUEST['provider'] ) : '';

	if ( empty( $provider ) ) {
		wp_send_json_error( esc_html__( 'Invalid Provider', 'gg-woo-feed' ) );
		wp_die();
	}

	try {
		$provider_obj = new Provider( $provider );
		$feed_queries = $provider_obj->get_template();

		$dropdown = new Dropdown();
		$is_edit  = false;
		ob_start();

		require_once( GGWOOFEED_DIR . 'inc/Admin/view/edit-mapping.php' );

		wp_send_json_success( [
			'mapping_template' => ob_get_clean(),
			'feed_type'        => strtolower( $provider_obj->get_feed_types( true ) ),
			'items_wrap'       => $feed_queries['items_wrap'],
			'item_wrap'        => $feed_queries['item_wrap'],
			'delimiter'        => $feed_queries['delimiter'],
			'enclosure'        => $feed_queries['enclosure'],
			'extra_header'     => $feed_queries['extra_header'],
		], 200 );
		wp_die();
	} catch ( \Exception $e ) {
		wp_send_json_error( [
			'message' => $e->getMessage(),
		], 400 );
	}
}

add_action( 'wp_ajax_gg_woo_feed_provider_mapping_view', 'gg_woo_feed_provider_mapping_view' );

/**
 * Get template mapping via AJAX.
 *
 * @return void
 */
function gg_woo_feed_add_new_filter_condition() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_send_json_error( esc_html__( 'Unauthorized Action.', 'gg-woo-feed' ) );
		die();
	}

	try {
		ob_start();

		?>
        <tr>
            <td>
                <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
            </td>
            <td>
                <select name="filter_atts[]" required>
					<?php print gg_woo_feed_get_product_attribute_dropdown( 'id' ); ?>
                </select>
            </td>

			<?php $condition_options = gg_woo_feed_get_filter_condition_options(); ?>
            <td>
                <select name="conditions[]" class="attr_type gg_woo_feed-not-empty">
					<?php foreach ( $condition_options as $condition => $condition_label ) : ?>
                        <option value="<?php echo esc_attr( $condition ); ?>"><?php echo esc_html( $condition_label ); ?></option>
					<?php endforeach; ?>
                </select>
            </td>
            <td>
                <input type="text" name="condition_values[]">
            </td>
            <td>
                <i class="gg_woo_feed-del-condition dashicons dashicons-no-alt"></i>
            </td>
        </tr>
		<?php

		wp_send_json_success( [
			'row' => ob_get_clean(),
		], 200 );
		wp_die();
	} catch ( \Exception $e ) {
		wp_send_json_error( [
			'message' => $e->getMessage(),
		], 400 );
	}
}

add_action( 'wp_ajax_gg_woo_feed_add_new_filter_condition', 'gg_woo_feed_add_new_filter_condition' );

/**
 * Get template mapping via AJAX.
 *
 * @return void
 */
function gg_woo_feed_add_new_filter_by_attributes_condition() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_send_json_error( esc_html__( 'Unauthorized Action.', 'gg-woo-feed' ) );
		die();
	}

	try {
		ob_start();

		?>
        <tr>
            <td>
                <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
            </td>
            <td>
                <select name="filter_by_attributes_atts[]" required>
					<?php print gg_woo_feed_get_wc_product_attribute_dropdown( '' ); ?>
                </select>
            </td>

			<?php $condition_attributes_options = gg_woo_feed_get_filter_condition_options(); ?>
            <td>
                <select name="conditions_attributes[]" class="attr_type gg_woo_feed-not-empty">
					<?php foreach ( $condition_attributes_options as $a_condition => $a_condition_label ) : ?>
                        <option value="<?php echo esc_attr( $a_condition ); ?>"><?php echo esc_html( $a_condition_label ); ?></option>
					<?php endforeach; ?>
                </select>
            </td>
            <td>
                <input type="text" name="condition_values_attributes[]">
            </td>
            <td>
                <i class="gg_woo_feed-del-condition-attributes dashicons dashicons-no-alt"></i>
            </td>
        </tr>
		<?php

		wp_send_json_success( [
			'row' => ob_get_clean(),
		], 200 );
		wp_die();
	} catch ( \Exception $e ) {
		wp_send_json_error( [
			'message' => $e->getMessage(),
		], 400 );
	}
}

add_action( 'wp_ajax_gg_woo_feed_add_new_filter_by_attributes_condition', 'gg_woo_feed_add_new_filter_by_attributes_condition' );

/**
 * Save config via AJAX.
 */
function gg_woo_feed_save_config() {
	if ( ! isset( $_POST['filename'] ) || ! $_POST['filename'] ) {
		wp_send_json_error( [
			'message' => __( 'Missing file name.', 'gg-woo-feed' ),
		], 403 );
	}

	if ( ! isset( $_POST['provider'] ) || ! $_POST['provider'] ) {
		wp_send_json_error( [
			'message' => __( 'Missing provider template.', 'gg-woo-feed' ),
		], 403 );
	}

	if ( ! gg_woo_feed_is_valid_ext( sanitize_text_field( $_POST['feed_type'] ) ) ) {
		wp_send_json_error( [
			'message' => __( 'The feed type is not supported.', 'gg-woo-feed' ),
		], 400 );
	}

	if ( ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ), 'gg_woo_feed-feed-form' ) ) {
		wp_send_json_error( [
			'message' => __( 'Security problem.', 'gg-woo-feed' ),
		], 403 );
	}

	$custom_path = '';
	if ( isset( $_POST['custom_path'] ) && $_POST['custom_path'] && ( Upload::get_folder_name() !== $_POST['custom_path'] ) ) {
		$custom_path = sanitize_file_name( $_POST['custom_path'] );
	}

	// Skip provider folder with google as default.
	$skip_provider = 'google' === sanitize_text_field( $_POST['provider'] ) ? true : false;

	$file_exist = Upload::check_feed_file( sanitize_file_name( $_POST['filename'] ), sanitize_text_field( $_POST['provider'] ), sanitize_text_field( $_POST['feed_type'] ), $custom_path,
		$skip_provider );

	$feed_option_name = ( isset( $_POST['feed_option_name'] ) && ! empty( $_POST['feed_option_name'] ) ) ? sanitize_text_field( $_POST['feed_option_name'] ) : null;

    if ( $feed_option_name ) {
    	$current_file_name = isset( $_POST['current_file_name'] ) ? sanitize_text_field( $_POST['current_file_name'] ) : '';
    	if ( sanitize_file_name( $_POST['filename'] ) !== $current_file_name ) {
		    if ( $file_exist ) {
			    wp_send_json_error( [
				    'message' => __( 'File Name is already exist. Please enter other.', 'gg-woo-feed' ),
			    ], 400 );
		    }
	    }
    } else {
	    if ( $file_exist ) {
		    wp_send_json_error( [
			    'message' => __( 'File Name is already exist. Please enter other.', 'gg-woo-feed' ),
		    ], 400 );
	    }
    }

	try {
		$file_name = Generate_Wizard::save_feed_config_data( $_POST, $feed_option_name, isset( $_POST['is_edit'] ) );

		if ( $file_name ) {
			wp_send_json_success( [
				'status'    => 1,
				'file_name' => $file_name,
				'message'   => __( 'The feed config was successfully updated.', 'gg-woo-feed' ),
			], 200 );
		} else {
			wp_send_json_success( [
				'status'  => 0,
				'message' => __( 'Update Failed.', 'gg-woo-feed' ),
			], 200 );
		}
	} catch ( \Exception $e ) {
		wp_send_json_error( [
			'message' => $e->getMessage(),
		], 400 );
	}
}

add_action( 'wp_ajax_gg_woo_feed_save_config', 'gg_woo_feed_save_config' );

/**
 * Get products via AJAX.
 */
function gg_woo_feed_get_products_for_feed() {
	$file_name = sanitize_text_field( $_REQUEST['file_name'] );

	try {
		$product_info = Generate_Wizard::get_product_information( $file_name );
		$products     = $product_info['products'];
		$total        = $product_info['total'];

		if ( $product_info['total'] > 0 ) {
			wp_send_json_success( [
				'status'   => 1,
				'products' => $products,
				'total'    => $total,
				'message'  => sprintf( __( '%s products found.', 'gg-woo-feed' ), $total ),
			], 200 );
		} else {
			wp_send_json_error( [
				'message' => __( 'Did not find any products from query filter.', 'gg-woo-feed' ),
			], 400 );
		}

	} catch ( \Exception $e ) {
		wp_send_json_error( [
			'message' => $e->getMessage(),
		], 400 );
	}
}

add_action( 'wp_ajax_gg_woo_feed_get_products_for_feed', 'gg_woo_feed_get_products_for_feed' );

/**
 * Make batch feed via AJAX.
 */
function gg_woo_feed_make_batch_feed() {
	$file_name = sanitize_text_field( $_REQUEST['file_name'] );
	$products  = isset( $_REQUEST['products'] ) ? array_map( 'absint', $_REQUEST['products'] ) : [];
	$loop      = sanitize_text_field( $_REQUEST['loop'] );

	try {
		$status = Generate_Wizard::make_batch_feed( $file_name, $products, $loop );

		if ( $status ) {
			wp_send_json_success( [
				'status' => $status,
			], 200 );
		}

	} catch ( \Exception $e ) {
		wp_send_json_error( [
			'message' => $e->getMessage(),
		], 400 );
	}
}

add_action( 'wp_ajax_gg_woo_feed_make_batch_feed', 'gg_woo_feed_make_batch_feed' );

/**
 * Save feed file via AJAX.
 */
function gg_woo_feed_save_feed_file() {
	$file_name = sanitize_text_field( $_REQUEST['file_name'] );

	try {
		$saved_data = Generate_Wizard::save_feed_file( $file_name );

		if ( $saved_data && $saved_data['success'] ) {
			$option_name = $saved_data['option_name'] ? $saved_data['option_name'] : '';
			$edit_nonce  = wp_create_nonce( 'gg_woo_feed-nonce-edit-feed' );
			$edit_link   = admin_url( 'admin.php?page=gg-woo-feed-feeds&action=edit&feed=' . $option_name . '&nonce=' . $edit_nonce );

			wp_send_json_success( [
				'status'    => $saved_data['success'],
				'url'       => $saved_data['url'],
				'edit_link' => $edit_link,
				'message'   => esc_html__( 'Done!', 'gg-woo-feed' ),
			], 200 );
		} else {
			wp_send_json_error( [
				'status'  => $saved_data['success'],
				'message' => esc_html__( 'Failed.', 'gg-woo-feed' ),
			], 400 );
		}
	} catch ( \Exception $e ) {
		wp_send_json_error( [
			'message' => $e->getMessage(),
		], 400 );
	}
}

add_action( 'wp_ajax_gg_woo_feed_save_feed_file', 'gg_woo_feed_save_feed_file' );

/**
 * Update feed status via AJAX.
 */
function gg_woo_feed_update_feed_status() {
	check_ajax_referer( 'gg_woo_feed_nonce' );
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_send_json_error( esc_html__( 'Unauthorized Action.', 'gg-woo-feed' ) );
		wp_die();
	}

	if ( ! empty( $_POST['feed_name'] ) ) {
		$feed_info           = maybe_unserialize( get_option( sanitize_text_field( $_POST['feed_name'] ) ) );
		$feed_info['status'] = isset( $_POST['status'] ) && 1 == $_POST['status'] ? 1 : 0;
		update_option( sanitize_text_field( $_POST['feed_name'] ), serialize( $feed_info ), false );
		wp_send_json_success( [ 'status' => true ] );
	} else {
		wp_send_json_error( [ 'status' => false ] );
	}
	wp_die();
}

add_action( 'wp_ajax_gg_woo_feed_update_feed_status', 'gg_woo_feed_update_feed_status' );

/**
 * Submit feed to google merchant.
 */
function gg_woo_feed_submit_google_merchant() {
	Generate_Wizard::submit_google_merchant( $_POST );
}

add_action( 'wp_ajax_gg_woo_feed_submit_google_merchant', 'gg_woo_feed_submit_google_merchant' );

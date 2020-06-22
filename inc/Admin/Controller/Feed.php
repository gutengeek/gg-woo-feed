<?php
namespace GG_Woo_Feed\Admin\Controller;

use GG_Woo_Feed\Common\Generate_Wizard;
use GG_Woo_Feed\Common\Upload;
use GG_Woo_Feed\Core\Controller;
use GG_Woo_Feed\Core\Install;

class Feed extends Controller {
	/**
	 * Register Hook Callback functions is called.
	 */
	public function register_hook_callbacks() {
		add_action( 'admin_init', [ $this, 'save_feed' ] );
		add_action( 'admin_init', [ $this, 'bulk_actions' ] );
		add_action( 'admin_init', [ $this, 'single_item_action' ] );
		add_action( 'gg_woo_feed_single_action_delete', [ $this, 'delete_item' ], 10, 2 );
		add_action( 'admin_notices', [ $this, 'print_notices' ] );
		add_action( 'gg_woo_feed_after_save_settings', [ $this, 'clean_cron_jobs' ], 10, 2 );
		add_action( 'admin_init', [ $this, 'save_google_sync_settings' ] );
	}

	/**
	 * Save feed.
	 */
	public function save_feed() {
		if ( ! isset( $_GET['page'] ) || 'gg-woo-feed-feeds' !== sanitize_text_field( $_GET['page'] ) ) {
			return;
		}

		$action  = ! empty( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';
		$nonce   = ! empty( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( $_REQUEST['_wpnonce'] ) : '';
		$is_edit = ! empty( $_REQUEST['is_edit'] ) ? sanitize_text_field( $_REQUEST['is_edit'] ) : '';

		if ( ! $action || ! $nonce || ! $is_edit ) {
			return;
		}

		if ( ! wp_verify_nonce( $nonce, 'gg_woo_feed-feed-form' ) ) {
			wp_die( __( 'Permission denied.', 'gg-woo-feed' ) );
		}

		$feed_option_name = ( isset( $_POST['feed_option_name'] ) && ! empty( $_POST['feed_option_name'] ) ) ? sanitize_text_field( $_POST['feed_option_name'] ) : null;

		$option_name = Generate_Wizard::save_feed_config_data( $_POST, $feed_option_name, isset( $_POST['edit-feed'] ) );

		$edit_nonce = wp_create_nonce( 'gg_woo_feed-nonce-edit-feed' );
		$edit_link  = admin_url( 'admin.php?page=gg-woo-feed-feeds&action=edit&feed=gg_woo_feed_feed_' . $option_name . '&nonce=' . $edit_nonce );
		wp_safe_redirect( $edit_link );
	}

	/**
	 * Bulk actions.
	 */
	public function bulk_actions() {
		if ( ! isset( $_GET['page'] ) || 'gg-woo-feed-feeds' !== sanitize_text_field( $_GET['page'] ) ) {
			return;
		}

		$action  = ! empty( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';
		$nonce   = ! empty( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( $_REQUEST['_wpnonce'] ) : '';
		$is_edit = ! empty( $_REQUEST['is_edit'] ) ? sanitize_text_field( $_REQUEST['is_edit'] ) : '';

		if ( ! $action || ! $nonce || $is_edit ) {
			return;
		}

		if ( ! wp_verify_nonce( $nonce, 'gg_woo_feed-manage-feeds' ) ) {
			wp_die( __( 'Permission denied.', 'gg-woo-feed' ) );
		}

		$feed_ids = ! empty( $_REQUEST['feeds'] ) ? gg_woo_feed_clean( $_REQUEST['feeds'] ) : [];
		switch ( $action ) {
			case 'delete':
				$count = 0;
				foreach ( $feed_ids as $feed ) {
					try {
						static::delete_feed( $feed );
					} catch ( \Exception $e ) {
						$count++;
					}
				}

				$this->add_admin_notice( sprintf( __( '%d feeds was deleted', 'gg-woo-feed' ), count( $feed_ids ) - $count ), 'success' );
				break;
			default:
				break;
		}

		wp_safe_redirect( 'admin.php?page=gg-woo-feed-feeds' );
	}

	/**
	 * Single item action.
	 */
	public function single_item_action() {
		$nonce  = ! empty( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : '';
		$feed   = ! empty( $_REQUEST['feed'] ) ? sanitize_text_field( $_REQUEST['feed'] ) : '';
		$action = ! empty( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';
		if ( ! $nonce || ! $action || ! $feed ) {
			return;
		}
		$feed_option = maybe_unserialize( get_option( $feed ) );

		// Get feed data.
		if ( ! wp_verify_nonce( $nonce, 'gg_woo_feed-nonce-' . $action . '-feed' ) || ! $feed_option ) {
			wp_die( __( 'Permission denied.', 'gg-woo-feed' ) );
		}

		// Trigger action.
		do_action( 'gg_woo_feed_single_action_' . $action, $feed_option, $feed, $action );
	}

	/**
	 * Delete item action
	 */
	public function delete_item( $feed_option, $feed ) {
		global $wpdb;

		$feed_data = $wpdb->get_row( $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name = %s", $feed ) );

		if ( $feed_data ) {
			$option_name = $feed_data->option_name;

			$name = $option_name;

			if ( isset( $feed_option['feedqueries'], $feed_option['feedqueries']['filename'] ) ) {
				$name = $feed_option['feedqueries']['filename'];
			}

			$deleted = static::delete_feed( $option_name );

			if ( $deleted ) {
				$this->add_admin_notice( sprintf( __( '%s is successfully deleted', 'gg-woo-feed' ), esc_html( $name ) ), 'success' );
			} else {
				$this->add_admin_notice( __( 'Delete failed', 'gg-woo-feed' ), 'error' );
			}
		} else {
			$this->add_admin_notice( __( 'Feed not found.', 'gg-woo-feed' ), 'error' );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=gg-woo-feed-feeds' ) );
	}

	/**
	 * Delete feed.
	 *
	 * @param $feed
	 * @return bool
	 */
	public static function delete_feed( $feed ) {
		$feed_name = gg_woo_feed_extract_feed_option_name( $feed );
		$feed_info = maybe_unserialize( get_option( 'gg_woo_feed_feed_' . $feed_name ) );

		if ( false !== $feed_info ) {
			$current_file_name = $feed_info['current_file_name'];
			$feed_info         = $feed_info['feedqueries'];
		} else {
			$feed_info         = maybe_unserialize( get_option( 'gg_woo_feed_config_' . $feed_name ) );
			$current_file_name = isset( $feed_info['current_file_name'] ) ? $feed_info['current_file_name'] : $feed_name;
		}

		$custom_path = '';
		if ( isset( $feed_info['custom_path'] ) && $feed_info['custom_path'] && ( Upload::get_folder_name() !== $feed_info['custom_path'] ) ) {
			$custom_path = sanitize_file_name( $feed_info['custom_path'] );
		}

		// Skip provider folder with google as default.
		$skip_provider = 'google' === $feed_info['provider'] ? true : false;

		$file = Upload::get_file( $current_file_name, $feed_info['provider'], $feed_info['feed_type'], $custom_path, $skip_provider );

		if ( file_exists( $file ) ) {
			unlink( $file );
		}

		$deleted_feed   = delete_option( 'gg_woo_feed_feed_' . $feed_name );
		$deleted_config = delete_option( 'gg_woo_feed_config_' . $feed_name );

		return $deleted_feed && $deleted_config;
	}

	/**
	 * Print custom icon admin notices
	 */
	public function print_notices() {
		$notices = get_transient( 'gg_woo_feed_feed_notices' );
		if ( ! $notices || ! is_array( $notices ) ) {
			return;
		}

		$types = array_keys( $notices );

		foreach ( $types as $type ): ?>
			<?php if ( ! empty( $notices[ $type ] ) ) : ?>
				<?php foreach ( $notices[ $type ] as $message ) : ?>
                    <div class="notice <?php echo esc_attr( $type ) ?>"><p><?php printf( '%s', $message ) ?></p></div>
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endforeach;

		// Clear
		set_transient( 'gg_woo_feed_feed_notices', [] );
	}

	/**
	 * Storage flash message to site transisent
	 */
	public function add_admin_notice( $message = '', $notice = 'error' ) {
		$messages = get_transient( 'gg_woo_feed_feed_notices' );
		if ( ! isset( $messages[ $notice ] ) ) {
			$messages[ $notice ] = [];
		}
		$messages[ $notice ][] = $message;
		set_transient( 'gg_woo_feed_feed_notices', $messages, 60 );
	}

	/**
	 * Clean cron jobs when saving settings.
	 *
	 * @param $update_options
	 * @param $old_options
	 */
	public function clean_cron_jobs( $update_options, $old_options ) {
		$update_schedule = isset( $update_options['schedule'] ) ? $update_options['schedule'] : 0;
		$old_schedule    = isset( $old_options['schedule'] ) ? $old_options['schedule'] : 0;
		if ( $update_schedule !== $old_schedule ) {
			wp_clear_scheduled_hook( 'gg_woo_feed_update' );
			add_filter( 'cron_schedules', [ Install::class, 'cron_schedules' ] );

			if ( ! wp_next_scheduled( 'gg_woo_feed_update' ) ) {
				wp_schedule_event( time(), 'gg_woo_feed_corn', 'gg_woo_feed_update' );
			}
			wp_unschedule_hook( 'gg_woo_feed_generate_feed' );
		}
	}

	/**
	 * Save google sync settings.
	 */
	public function save_google_sync_settings() {
		if ( ! isset( $_GET['page'] ) || 'gg-woo-feed-google-sync' !== sanitize_text_field( $_GET['page'] ) ) {
			return;
		}

		$nonce = ! empty( $_REQUEST['gg_woo_feed_meta_nonce'] ) ? sanitize_text_field( $_REQUEST['gg_woo_feed_meta_nonce'] ) : '';

		if ( ! $nonce ) {
			return;
		}

		if ( ! wp_verify_nonce( $nonce, 'gg_woo_feed_save_form_meta' ) ) {
			wp_die( __( 'Permission denied.', 'gg-woo-feed' ) );
		}

		$google_sync_settings = gg_woo_feed_clean( $_REQUEST['gg_woo_feed_google_sync'] );

		if ( isset( $_REQUEST['save_google_sync_options'] ) ) {
			update_option( 'gg_woo_feed_google_sync', [
				'client_id'     => $google_sync_settings['client_id'],
				'client_secret' => $google_sync_settings['client_secret'],
				'merchant_id'   => $google_sync_settings['merchant_id'],
			] );
		} elseif ( isset( $_REQUEST['reset_google_sync_options'] ) ) {
			delete_option( 'gg_woo_feed_google_sync' );
			delete_option( 'gg_woo_feed_google_sync_access_token' );
		}

		$redirect_link = admin_url( 'admin.php?page=gg-woo-feed-google-sync' );
		wp_safe_redirect( $redirect_link );
	}
}

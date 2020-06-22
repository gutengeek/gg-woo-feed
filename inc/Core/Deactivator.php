<?php
namespace GG_Woo_Feed\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Fired during plugin deactivation
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 **/
class Deactivator {

	/**
	 * Deactivate
	 */
	public static function deactivate() {
		$timestamp = wp_next_scheduled( 'gg_woo_feed_update' );
		wp_unschedule_event( $timestamp, 'gg_woo_feed_update' );

		wp_unschedule_hook( 'gg_woo_feed_generate_feed' );
	}
}

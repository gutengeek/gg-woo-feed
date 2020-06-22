<?php
namespace GG_Woo_Feed\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 */
class Install {
	/**
	 * Init.
	 */
	public static function init() {
		add_filter( 'cron_schedules', [ __CLASS__, 'cron_schedules' ] );
	}

	/**
	 * Install Opaljob.
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'gg_woo_feed_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'gg_woo_feed_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		static::create_cron_jobs();

		// Add the transient to redirect.
		set_transient( '_gg_woo_feed_activation_redirect', true, 30 );

		delete_transient( 'gg_woo_feed_installing' );

		// Remove rewrite rules and then recreate rewrite rules.
		flush_rewrite_rules();

		do_action( 'gg_woo_feed_installed' );
	}

	/**
	 * Add more cron schedules.
	 *
	 * @param array $schedules List of WP scheduled cron jobs.
	 *
	 * @return array
	 */
	public static function cron_schedules( $schedules ) {
		$interval                   = gg_woo_feed_get_option( 'schedule', '0' );
		$schedules['gg_woo_feed_corn'] = [
			'display'  => __( 'GG Woo Feed Update Interval', 'gg-woo-feed' ),
			'interval' => $interval,
		];

		return $schedules;
	}

	/**
	 * Create cron jobs (clear them first).
	 */
	private static function create_cron_jobs() {
		wp_clear_scheduled_hook( 'gg_woo_feed_corn' );
		wp_clear_scheduled_hook( 'gg_woo_feed_update' );

		if ( ! wp_next_scheduled ( 'gg_woo_feed_update' ) ) {
			wp_schedule_event( time(), 'gg_woo_feed_corn', 'gg_woo_feed_update' );
		}
	}
}


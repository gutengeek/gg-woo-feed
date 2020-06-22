<?php
namespace GG_Woo_Feed\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 **/
class Activator {

	/**
	 * Activate.
	 */
	public static function activate() {

		$min_php = '7.0';

		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare( PHP_VERSION, $min_php, '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'This plugin requires a minmum PHP Version of ' . $min_php );
		}

		Install::install();
	}
}

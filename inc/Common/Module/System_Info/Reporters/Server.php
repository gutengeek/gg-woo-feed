<?php
namespace GG_Woo_Feed\Common\Module\System_Info\Reporters;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Server environment report.
 *
 */
class Server extends Base {

	/**
	 * Get server environment reporter title.
	 *
	 * Retrieve server environment reporter title.
	 *
	 * @access public
	 *
	 * @return string Reporter title.
	 */
	public function get_title() {
		return 'Server Environment';
	}

	/**
	 * Get server environment report fields.
	 *
	 * Retrieve the required fields for the server environment report.
	 *
	 * @access public
	 *
	 * @return array Required report fields with field ID and field label.
	 */
	public function get_fields() {
		return [
			'os'                 => 'Operating System',
			'software'           => 'Software',
			'mysql_version'      => 'MySQL version',
			'php_version'        => 'PHP Version',
			'php_max_input_vars' => 'PHP Max Input Vars',
			'php_max_post_size'  => 'PHP Max Post Size',
			'gd_installed'       => 'GD Installed',
			'zip_installed'      => 'ZIP Installed',
			'write_permissions'  => 'Write Permissions',
		];
	}

	/**
	 * Get server operating system.
	 *
	 * Retrieve the server operating system.
	 *
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 * @type string $value Server operating system.
	 * }
	 */
	public function get_os() {
		return [
			'value' => PHP_OS,
		];
	}

	/**
	 * Get server software.
	 *
	 * Retrieve the server software.
	 *
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 * @type string $value Server software.
	 * }
	 */
	public function get_software() {
		return [
			'value' => $_SERVER['SERVER_SOFTWARE'],
		];
	}

	/**
	 * Get PHP version.
	 *
	 * Retrieve the PHP version.
	 *
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 * @type string $value          PHP version.
	 * @type string $recommendation Minimum PHP version recommendation.
	 * @type bool   $warning        Whether to display a warning.
	 * }
	 */
	public function get_php_version() {
		$result = [
			'value' => PHP_VERSION,
		];

		if ( version_compare( $result['value'], '5.4', '<' ) ) {
			$result['recommendation'] = _x( 'We recommend to use php 5.4 or higher', 'System Info', 'gg-woo-feed' );

			$result['warning'] = true;
		}

		return $result;
	}

	/**
	 * Get PHP `max_input_vars`.
	 *
	 * Retrieve the value of `max_input_vars` from `php.ini` configuration file.
	 *
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 * @type string $value PHP `max_input_vars`.
	 * }
	 */
	public function get_php_max_input_vars() {
		return [
			'value' => ini_get( 'max_input_vars' ),
		];
	}

	/**
	 * Get PHP `post_max_size`.
	 *
	 * Retrieve the value of `post_max_size` from `php.ini` configuration file.
	 *
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 * @type string $value PHP `post_max_size`.
	 * }
	 */
	public function get_php_max_post_size() {
		return [
			'value' => ini_get( 'post_max_size' ),
		];
	}

	/**
	 * Get GD installed.
	 *
	 * Whether the GD extension is installed.
	 *
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 * @type string $value   Yes if the GD extension is installed, No otherwise.
	 * @type bool   $warning Whether to display a warning. True if the GD extension is installed, False otherwise.
	 * }
	 */
	public function get_gd_installed() {
		$gd_installed = extension_loaded( 'gd' );

		return [
			'value'   => $gd_installed ? 'Yes' : 'No',
			'warning' => ! $gd_installed,
		];
	}

	/**
	 * Get ZIP installed.
	 *
	 * Whether the ZIP extension is installed.
	 *
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 * @type string $value   Yes if the ZIP extension is installed, No otherwise.
	 * @type bool   $warning Whether to display a warning. True if the ZIP extension is installed, False otherwise.
	 * }
	 */
	public function get_zip_installed() {
		$zip_installed = extension_loaded( 'zip' );

		return [
			'value'   => $zip_installed ? 'Yes' : 'No',
			'warning' => ! $zip_installed,
		];
	}

	/**
	 * Get MySQL version.
	 *
	 * Retrieve the MySQL version.
	 *
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 * @type string $value MySQL version.
	 * }
	 */
	public function get_mysql_version() {
		global $wpdb;

		$db_server_version = $wpdb->get_results( "SHOW VARIABLES WHERE `Variable_name` IN ( 'version_comment', 'innodb_version' )", OBJECT_K );

		return [
			'value' => $db_server_version['version_comment']->Value . ' v' . $db_server_version['innodb_version']->Value,
		];
	}

	/**
	 * Get write permissions.
	 *
	 * Check whether the required folders has writing permissions.
	 *
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 * @type string $value   Writing permissions status.
	 * @type bool   $warning Whether to display a warning. True if some required
	 *                          folders don't have writing permissions, False otherwise.
	 * }
	 */
	public function get_write_permissions() {
		$paths_to_check = [
			ABSPATH => 'WordPress root directory',
		];

		$write_problems = [];

		$wp_upload_dir = wp_upload_dir();

		if ( $wp_upload_dir['error'] ) {
			$write_problems[] = 'WordPress root uploads directory';
		}

		$gg_woo_feed_uploads_path = $wp_upload_dir['basedir'] . '/gg-woo-feed';

		if ( is_dir( $gg_woo_feed_uploads_path ) ) {
			$paths_to_check[ $gg_woo_feed_uploads_path ] = 'GG Woo Feed uploads directory';
		}

		$htaccess_file = ABSPATH . '/.htaccess';

		if ( file_exists( $htaccess_file ) ) {
			$paths_to_check[ $htaccess_file ] = '.htaccess file';
		}

		foreach ( $paths_to_check as $dir => $description ) {
			if ( ! is_writable( $dir ) ) {
				$write_problems[] = $description;
			}
		}

		if ( $write_problems ) {
			$value = 'There are some writing permissions issues with the following directories/files:' . "\n\t\t - ";

			$value .= implode( "\n\t\t - ", $write_problems );
		} else {
			$value = 'All right';
		}

		return [
			'value'   => $value,
			'warning' => ! ! $write_problems,
		];
	}
}

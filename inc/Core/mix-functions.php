<?php

/**
 * Generate Unique slug for feed.
 * This function only check database for existing feed for generating unique slug.
 * Use generate_unique_feed_file_name() for complete unique slug name.
 *
 * @param string $slug      slug for checking uniqueness.
 * @param string $prefix    prefix to check with. Optional.
 * @param int    $option_id option id. Optional option id to exclude specific option.
 *
 * @return string
 * @see wp_unique_post_slug()
 *
 */
function gg_woo_feed_unique_feed_slug( $slug, $prefix = '', $option_id = null ) {
	global $wpdb;

	$disallowed = [ 'siteurl', 'home', 'blogname', 'blogdescription', 'users_can_register', 'admin_email' ];
	if ( $option_id && $option_id > 0 ) {
		$checkSql  = "SELECT option_name FROM $wpdb->options WHERE option_name = %s AND option_id != %d LIMIT 1";
		$nameCheck = $wpdb->get_var( $wpdb->prepare( $checkSql, $prefix . $slug, $option_id ) );
	} else {
		$checkSql  = "SELECT option_name FROM $wpdb->options WHERE option_name = %s LIMIT 1";
		$nameCheck = $wpdb->get_var( $wpdb->prepare( $checkSql, $prefix . $slug ) );
	}

	if ( $nameCheck || in_array( $slug, $disallowed ) ) {
		$suffix = 2;
		do {
			$altName = _truncate_post_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
			if ( $option_id && $option_id > 0 ) {
				$nameCheck = $wpdb->get_var( $wpdb->prepare( $checkSql, $prefix . $altName, $option_id ) );
			} else {
				$nameCheck = $wpdb->get_var( $wpdb->prepare( $checkSql, $prefix . $altName ) );
			}
			$suffix++;
		} while ( $nameCheck );
		$slug = $altName;
	}

	return $slug;
}

/**
 * Get CSV/TXT Delimiters
 *
 * @return array
 */
function gg_woo_feed_get_csv_delimiters() {
	return apply_filters( 'gg_woo_feed_csv_delimiters', [
		','   => esc_html__( 'Comma', 'gg-woo-feed' ),
		'tab' => esc_html__( 'Tab', 'gg-woo-feed' ),
		':'   => esc_html__( 'Colon', 'gg-woo-feed' ),
		' '   => esc_html__( 'Space', 'gg-woo-feed' ),
		'|'   => esc_html__( 'Pipe', 'gg-woo-feed' ),
		';'   => esc_html__( 'Semi Colon', 'gg-woo-feed' ),
	] );
}

/**
 * Get CSV/TXT Enclosure for multiple words
 *
 * @return array
 */
function gg_woo_feed_get_csv_enclosures() {
	return apply_filters( 'gg_woo_feed_csv_enclosure', [
		'double' => '"',
		'single' => '\'',
		' '      => esc_html__( 'None', 'gg-woo-feed' ),
	] );
}

/**
 * Guess Brand name from Site URL
 *
 * @return string
 */
function gg_woo_feed_get_default_brand() {
	$brand = apply_filters( 'gg_woo_feed_pre_get_default_brand_name', null );
	if ( ! is_null( $brand ) ) {
		return $brand;
	}
	$brand = '';
	$url   = filter_var( site_url(), FILTER_SANITIZE_URL );
	if ( false !== $url ) {
		$url = wp_parse_url( $url );
		if ( array_key_exists( 'host', $url ) ) {
			$arr = explode( '.', $url['host'] );
			if ( count( $arr ) >= 2 ) {
				$brand = $arr[ count( $arr ) - 2 ];
			} else {
				$brand = $arr[0];
			}
			$brand = ucfirst( $brand );
		}
	}

	return apply_filters( 'gg_woo_feed_get_default_brand_name', $brand );
}

/**
 * Get Schedule Intervals
 *
 * @return array
 */
function gg_woo_feed_get_schedule_interval_options() {
	return apply_filters( 'gg_woo_feed_schedule_interval_options', [
			'0'                  => esc_html__( 'Never', 'gg-woo-feed' ),
			WEEK_IN_SECONDS      => esc_html__( '1 Week', 'gg-woo-feed' ),
			DAY_IN_SECONDS       => esc_html__( '24 Hours', 'gg-woo-feed' ),
			12 * HOUR_IN_SECONDS => esc_html__( '12 Hours', 'gg-woo-feed' ),
			6 * HOUR_IN_SECONDS  => esc_html__( '6 Hours', 'gg-woo-feed' ),
			HOUR_IN_SECONDS      => esc_html__( '1 Hours', 'gg-woo-feed' ),
		]
	);
}

if ( ! function_exists( 'gg_woo_feed_write_log' ) ) {

	/**
	 * Write log.
	 *
	 * @param $log
	 */
	function gg_woo_feed_write_log( $log ) {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
}

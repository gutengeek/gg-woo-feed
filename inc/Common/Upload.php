<?php
namespace GG_Woo_Feed\Common;

class Upload {
	/**
	 * Get folder name.
	 */
	public static function get_folder_name() {
		return apply_filters( 'gg_woo_feed_folder_name', 'gg-woo-feed' );
	}

	/**
	 * Get File Path for feed or the file upload path for the plugin to use.
	 *
	 * @param string $provider provider name.
	 * @param string $type     feed file type.
	 *
	 * @return string
	 */
	public static function get_file_path( $provider = '', $type = '', $custom_path = '', $skip_provider = false ) {
		$upload_dir = wp_get_upload_dir();

		return sprintf( '%1$s/%2$s/%3$s%4$s/',
			$upload_dir['basedir'],
			$custom_path ? $custom_path : static::get_folder_name(),
			! $skip_provider ? $provider . '/' : '',
			$type
		);
	}

	/**
	 * Get Feed File URL
	 *
	 * @param string $file_name
	 * @param string $provider
	 * @param string $type
	 * @param string $custom_path
	 * @param bool   $skip_provider
	 *
	 * @return string
	 */
	public static function get_file( $file_name, $provider, $type, $custom_path = '', $skip_provider = false ) {
		$file_name = gg_woo_feed_extract_feed_option_name( $file_name );
		$path      = static::get_file_path( $provider, $type, $custom_path, $skip_provider );

		return sprintf( '%s/%s.%s', untrailingslashit( $path ), $file_name, $type );
	}

	/**
	 * Get Feed File URL
	 *
	 * @param string $file_name
	 * @param string $provider
	 * @param string $type
	 *
	 * @return string
	 */
	public static function get_file_url( $file_name, $provider, $type, $custom_path = '', $skip_provider = false ) {
		$file_name  = gg_woo_feed_extract_feed_option_name( $file_name );
		$upload_dir = wp_get_upload_dir();

		return esc_url( sprintf( '%1$s/%2$s/%3$s%4$s/%5$s.%6$s',
				$upload_dir['baseurl'],
				$custom_path ? $custom_path : static::get_folder_name(),
				! $skip_provider ? $provider . '/' : '',
				$type,
				$file_name,
				$type )
		);
	}

	/**
	 * Check if feed file exists
	 *
	 * @param string $file_name
	 * @param string $provider
	 * @param string $type
	 *
	 * @return bool
	 */
	public static function check_feed_file( $file_name, $provider, $type, $custom_path = '', $skip_provider = false ) {
		$upload_dir = wp_get_upload_dir();

		return file_exists( sprintf( '%1$s/%2$s/%3$s%4$s/%5$s.%6$s',
				$upload_dir['basedir'],
				$custom_path ? $custom_path : static::get_folder_name(),
				! $skip_provider ? $provider . '/' : '',
				$type,
				$file_name,
				$type )
		);
	}

	/**
	 * Get folder directory
	 *
	 * @param string $provider
	 * @param string $type
	 *
	 * @return string
	 */
	public static function get_folder_dir( $provider, $type, $custom_path = '', $skip_provider = false ) {
		$upload_dir = wp_get_upload_dir();

		return sprintf(
			'%1$s/%2$s/%3$s%4$s',
			$upload_dir['basedir'],
			$custom_path ? $custom_path : static::get_folder_name(),
			! $skip_provider ? $provider . '/' : '',
			$type
		);
	}

	/**
	 * Get file directory
	 *
	 * @param string $provider
	 * @param string $type
	 *
	 * @return string
	 */
	public static function get_file_dir( $file_name, $provider, $type, $custom_path = '', $skip_provider = false ) {
		$ext = $type;
		if ( 'csv' === $type ) {
			$ext = 'json';
		}

		$path = Upload::get_folder_dir( $provider, $type, $custom_path, $skip_provider );

		return $path . '/' . $file_name . '.' . $ext;
	}

	/**
	 * Check if the directory for feed file exist or not and make directory
	 *
	 * @param $path
	 * @return bool
	 */
	public function check_dir( $path ) {
		if ( ! file_exists( $path ) ) {
			return wp_mkdir_p( $path );
		}

		return true;
	}

	/**
	 * @param        $file_name
	 * @param        $provider
	 * @param        $type
	 * @param string $custom_path
	 * @param bool   $skip_provider
	 * @return bool|string
	 */
	public static function get_unique_name( $file_name, $provider, $type, $custom_path = '', $skip_provider = false ) {
		$feed_dir     = static::get_folder_dir( $provider, $type, $custom_path, $skip_provider );
		$raw_filename = sanitize_title( $file_name, '', 'save' );
		$raw_filename = sanitize_file_name( $raw_filename . '.' . $type );
		$raw_filename = wp_unique_filename( $feed_dir, $raw_filename );
		$raw_filename = str_replace( '.' . $type, '', $raw_filename );

		return -1 != $raw_filename ? $raw_filename : false;
	}

	public function is_exist_dir( $path ) {
		if ( ! file_exists( $path ) ) {
			return false;
		}

		return true;
	}

	public function is_name_unique( $name ) {
		return ! is_dir( $this->get_icon_sets_dir() . '/' . $name );
	}

	/**
	 * Save CSV file.
	 *
	 * @param $path
	 * @param $file
	 * @param $content
	 * @param $info
	 *
	 * @return bool
	 */
	public function save_csv_file( $path, $file, $content, $info ) {
		if ( $this->check_dir( $path ) ) {
			if ( file_exists( $file ) ) {
				unlink( $file );
			}

			$fp = fopen( $file, 'wb' );

			if ( 'tab' === $info['delimiter'] ) {
				$delimiter = "\t";
			} else {
				$delimiter = $info['delimiter'];
			}

			$enclosure = $info['enclosure'];
			$eol       = PHP_EOL;

			if ( count( $content ) ) {
				foreach ( $content as $fields ) {
					if ( 'double' === $enclosure ) {
						fputcsv( $fp, $fields, $delimiter, chr( 34 ) );
					} elseif ( 'single' === $enclosure ) {
						fputcsv( $fp, $fields, $delimiter, chr( 39 ) );
					} else {
						fputs( $fp, implode( $delimiter, $fields ) . $eol );
					}
				}
			}

			fclose( $fp );

			return true;
		}

		return false;
	}

	/**
	 * Save File
	 *
	 * @param $path
	 * @param $file
	 * @param $content
	 *
	 * @return bool
	 */
	public function save_file( $path, $file, $content ) {
		if ( $this->check_dir( $path ) ) {
			if ( file_exists( $file ) ) {
				unlink( $file );
			}
			$fp = fopen( $file, 'w+' );
			fwrite( $fp, $content );
			fclose( $fp );

			return true;
		}

		return false;
	}

	public function prepend_save_file( $path, $file, $content ) {
		if ( $this->check_dir( $path ) ) {
			$fp = fopen( $file, 'r+' );
			fwrite( $fp, $content );
			fclose( $fp );

			return true;
		}

		return false;
	}

	public function append_save_file( $path, $file, $content ) {
		if ( $this->check_dir( $path ) ) {
			$fp = fopen( $file, 'a' );
			fwrite( $fp, $content );
			fclose( $fp );

			return true;
		}

		return false;
	}

	public function append_save_csv_file( $path, $file, $content, $info ) {
		if ( $this->check_dir( $path ) ) {
			$fp = fopen( $file, 'a' );
			if ( 'tab' === $info['delimiter'] ) {
				$delimiter = "\t";
			} else {
				$delimiter = $info['delimiter'];
			}

			$enclosure = $info['enclosure'];
			$eol       = PHP_EOL;

			if ( count( $content ) ) {
				foreach ( $content as $fields ) {
					if ( 'double' === $enclosure ) {
						fputcsv( $fp, $fields, $delimiter, chr( 34 ) );
					} elseif ( 'single' === $enclosure ) {
						fputcsv( $fp, $fields, $delimiter, chr( 39 ) );
					} else {
						fputs( $fp, implode( $delimiter, $fields ) . $eol );
					}
				}
			}

			fclose( $fp );

			return true;
		}

		return false;
	}
}

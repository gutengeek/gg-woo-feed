<?php
namespace GG_Woo_Feed\Common;

use GG_Woo_Feed\Common\Integrations\Generate;
use GG_Woo_Feed\Common\Model\Mapping;
use GG_Woo_Feed\Common\Module\Google_Merchant_Content_API;
use GG_Woo_Feed\Core\Constant;
use Google_Service_ShoppingContent;
use Google_Service_ShoppingContent_Datafeed;
use Google_Service_ShoppingContent_DatafeedFetchSchedule;
use Google_Service_ShoppingContent_DatafeedFormat;
use Google_Service_ShoppingContent_DatafeedTarget;

class Generate_Wizard {
	/**
	 * Step 1: Save config.
	 *
	 * @param      $data
	 * @param null $feed_option_name
	 * @param bool $is_edit
	 * @return bool|string
	 */
	public static function save_feed_config_data( $data, $feed_option_name = null, $is_edit = true ) {
		if ( ! is_array( $data ) ) {
			return false;
		}

		if ( ! isset( $data['filename'], $data['feed_type'], $data['provider'] ) ) {
			return false;
		}

		$removables = [ '_wpnonce', '_wp_http_referer', 'save', 'is_edit' ];
		foreach ( $removables as $removable ) {
			if ( isset( $data[ $removable ] ) ) {
				unset( $data[ $removable ] );
			}
		}

		$data = gg_woo_feed_sanitize_data_option( $data );
		$data = gg_woo_feed_parse_feed_queries( $data );
		$data = gg_woo_feed_sanitize_form_fields( $data );

		if ( empty( $feed_option_name ) ) {
			$custom_path = '';
			if ( isset( $data['custom_path'] ) && $data['custom_path'] && ( Upload::get_folder_name() !== $data['custom_path'] ) ) {
				$custom_path = $data['custom_path'];
			}

			// Skip provider folder with google as default.
			$skip_provider    = 'google' === $data['provider'] ? true : false;
			$feed_option_name = gg_woo_feed_generate_unique_feed_file_name( $data['filename'], $data['feed_type'], $data['provider'], $custom_path, $skip_provider );
		} else {
			$feed_option_name = gg_woo_feed_extract_feed_option_name( $feed_option_name );
			$old_option       = maybe_unserialize( get_option( 'gg_woo_feed_config_' . $feed_option_name ) );
			update_option( 'gg_woo_feed_temp_' . $feed_option_name, $old_option, false );
		}

		$updated = update_option( 'gg_woo_feed_config_' . $feed_option_name, $data, false );

		if ( $updated && ! $is_edit ) {
			$old_feed    = maybe_unserialize( get_option( 'gg_woo_feed_feed_' . $feed_option_name ) );
			$custom_path = '';
			if ( isset( $data['custom_path'] ) && $data['custom_path'] && ( Upload::get_folder_name() !== $data['custom_path'] ) ) {
				$custom_path = sanitize_file_name( $data['custom_path'] );
			}

			// Skip provider folder with google as default.
			$skip_provider = 'google' === $data['provider'] ? true : false;

			$file_name = Upload::get_unique_name( $data['filename'], $data['provider'], $data['feed_type'], $custom_path, $skip_provider );

			if ( ! $file_name ) {
				$file_name = $feed_option_name;
			}

			$feed_data = [
				'feedqueries'       => $data,
				'current_file_name' => $file_name,
				'url'               => Upload::get_file_url( $file_name, $data['provider'], $data['feed_type'], $custom_path, $skip_provider ),
				'last_updated'      => gmdate( 'Y-m-d H:i:s' ),
				'status'            => isset( $old_feed['status'] ) && 1 == $old_feed['status'] ? 1 : 0,
			];

			update_option( 'gg_woo_feed_feed_' . $feed_option_name, maybe_serialize( $feed_data ), false );
		}

		return $feed_option_name;
	}

	/**
	 * Step 2: Get product info
	 *
	 * @param $file_name
	 * @return array
	 */
	public static function get_product_information( $file_name ) {
		$feed  = gg_woo_feed_extract_feed_option_name( $file_name );
		$limit = gg_woo_feed_get_option( 'product_per_batch', 300 );

		$feed_config = maybe_unserialize( get_option( 'gg_woo_feed_config_' . $feed ) );

		try {
			$products = new Mapping( $feed_config );
			$ids      = $products->query_products();

			if ( is_array( $ids ) && ! empty( $ids ) ) {
				if ( count( $ids ) > $limit ) {
					$batches = array_chunk( $ids, $limit );
				} else {
					$batches = [ $ids ];
				}

				return [
					'products' => $batches,
					'total'    => count( $ids ),
				];
			}

			return [
				'products' => '',
				'total'    => count( $ids ),
			];
		} catch ( \Exception $e ) {
			wp_send_json_error( [
				'message' => $e->getMessage(),
			], 400 );
		}
	}

	/**
	 * Step 3
	 *
	 * @param     $feed
	 * @param     $products
	 * @param int $loop
	 *
	 * @return bool
	 */
	public static function make_batch_feed( $feed, $products, $loop = 0 ) {
		$feed_name   = gg_woo_feed_extract_feed_option_name( sanitize_text_field( $feed ) );
		$feed_info   = get_option( 'gg_woo_feed_config_' . $feed_name, false );
		$feed_config = maybe_unserialize( get_option( 'gg_woo_feed_feed_' . $feed_name, [] ) );

		$offset                   = isset( $loop ) ? absint( $loop ) : 0;
		$feed_info['product_ids'] = isset( $products ) ? array_map( 'absint', $products ) : [];

		$custom_path = '';
		if ( isset( $feed_info['custom_path'] ) && $feed_info['custom_path'] && ( Upload::get_folder_name() !== $feed_info['custom_path'] ) ) {
			$custom_path = sanitize_file_name( $feed_info['custom_path'] );
		}

		$type     = $feed_info['feed_type'];
		$provider = $feed_info['provider'];

		// Skip provider folder with google as default.
		$skip_provider = 'google' === $feed_info['provider'] ? true : false;

		if ( $feed_config ) {
			$current_file_name = $feed_config['current_file_name'];
			if ( $current_file_name !== static::sanitize_file_name( $feed_info['filename'], $type ) ) {
				$file_name = Upload::get_unique_name( $feed_info['filename'], $provider, $type, $custom_path, $skip_provider );

				if ( ! $file_name ) {
					$file_name = $current_file_name;
				}
			} else {
				$file_name = $current_file_name;
			}
		} else {
			$file_name = $feed_name;
		}

		if ( 0 == $offset ) {
			static::unlink_temp_files( $provider, $file_name, $type, $custom_path, $skip_provider );
		}

		return static::generate_batch_data( $feed_info, $file_name, $feed_config );
	}

	/**
	 * Step 4: Save Feed File
	 *
	 * @param $file_name
	 * @return array
	 */
	public static function save_feed_file( $file_name ) {
		$feed_name   = gg_woo_feed_extract_feed_option_name( sanitize_text_field( $file_name ) );
		$info        = get_option( 'gg_woo_feed_config_' . $feed_name, false );
		$feed_config = maybe_unserialize( get_option( 'gg_woo_feed_feed_' . $feed_name, [] ) );
		$old_option  = maybe_unserialize( get_option( 'gg_woo_feed_temp_' . $feed_name, [] ) );

		if ( $old_option ) {
			$old_custom_path = '';
			if ( isset( $old_option['custom_path'] ) && $old_option['custom_path'] && ( Upload::get_folder_name() !== $old_option['custom_path'] ) ) {
				$old_custom_path = sanitize_file_name( $old_option['custom_path'] );
			}

			// Skip provider folder with google as default.
			$old_skip_provider = 'google' === $old_option['provider'] ? true : false;
			$old_path          = Upload::get_folder_dir( $old_option['provider'], $old_option['feed_type'], $old_custom_path, $old_skip_provider );
			$old_file          = $old_path . '/' . $feed_config['current_file_name'] . '.' . $old_option['feed_type'];

			if ( file_exists( $old_file ) ) {
				unlink( $old_file );
			}
		}

		$provider = $info['provider'];
		$type     = $info['feed_type'];

		$custom_path = '';
		if ( isset( $info['custom_path'] ) && $info['custom_path'] && ( Upload::get_folder_name() !== $info['custom_path'] ) ) {
			$custom_path = sanitize_file_name( $info['custom_path'] );
		}

		// Skip provider folder with google as default.
		$skip_provider = 'google' === $provider ? true : false;
		if ( $feed_config ) {
			$current_file_name = $feed_config['current_file_name'];
			if ( $current_file_name !== static::sanitize_file_name( $info['filename'], $type ) ) {
				$the_file_name = Upload::get_unique_name( $info['filename'], $provider, $type, $custom_path, $skip_provider );
				if ( ! $file_name ) {
					$the_file_name = $current_file_name;
				}
			} else {
				$the_file_name = $current_file_name;
			}
		} else {
			$the_file_name = $feed_name;
		}

		$footer = static::get_batch_feed( $provider, $type, Constant::TEMP_FOOTER_PREFIX . $the_file_name, $custom_path, $skip_provider );

		$feed_body     = Constant::TEMP_BODY_PREFIX . $the_file_name;
		$the_feed_file = static::get_batch_feed_file_dir( $provider, $type, $feed_body, $custom_path, $skip_provider );

		$path = Upload::get_folder_dir( $provider, $type, $custom_path, $skip_provider );
		$file = $path . '/' . $the_file_name . '.' . $type;

		if ( $the_feed_file ) {
			if ( $footer ) {
				static::append_save_batch_feed( $provider, $type, $footer, $feed_body, $custom_path, $skip_provider );
			}

			if ( file_exists( $file ) ) {
				unlink( $file );
			}
			static::rename_feed_file( $provider, $type, $feed_body, $file, $custom_path, $skip_provider );
		}

		$info['current_file_name'] = $the_file_name;
		update_option( 'gg_woo_feed_config_' . $feed_name, $info );

		// Save Info into database.
		$feed_info = [
			'feedqueries'       => $info,
			'current_file_name' => $the_file_name,
			'url'               => Upload::get_file_url( $the_file_name, $provider, $type, $custom_path, $skip_provider ),
			'last_updated'      => gmdate( 'Y-m-d H:i:s' ),
		];

		$old_info = maybe_unserialize( get_option( 'gg_woo_feed_feed_' . $feed_name ) );
		if ( isset( $old_info['status'] ) ) {
			$feed_info['status'] = $old_info['status'];
		} else {
			$feed_info['status'] = 1;
		}

		static::unlink_temp_files( $provider, $the_file_name, $type, $custom_path, $skip_provider );

		$updated = update_option( 'gg_woo_feed_feed_' . $feed_name, serialize( $feed_info ), false );

		if ( $updated ) {
			$feed_info = maybe_unserialize( get_option( 'gg_woo_feed_feed_' . $feed_name ) );
			$url       = $feed_info['url'];
			$data      = [
				'success'     => true,
				'info'        => $feed_info,
				'url'         => $url,
				'option_name' => 'gg_woo_feed_feed_' . $feed_name,
			];
		} else {
			$data = [
				'success' => false,
			];
		}

		delete_option( 'gg_woo_feed_temp_' . $feed_name );

		return $data;
	}

	/**
	 * Generate batch data.
	 *
	 * @param $info
	 * @param $feed_slug
	 * @return bool
	 */
	public static function generate_batch_data( $info, $file_name, $feed_config ) {
		$info = gg_woo_feed_parse_feed_queries( isset( $info['feedqueries'] ) ? $info['feedqueries'] : $info );
		try {
			$status = false;
			if ( ! empty( $info['provider'] ) ) {
				// Get Post data.
				$provider     = sanitize_text_field( $info['provider'] );
				$type         = sanitize_text_field( $info['feed_type'] );
				$feed_queries = $info;
				// Get Feed info.
				$products = new Generate( $provider, $feed_queries );
				$feed     = $products->get_frame();

				if ( ! empty( $feed['body'] ) ) {
					$custom_path = '';
					if ( isset( $info['custom_path'] ) && $info['custom_path'] && ( Upload::get_folder_name() !== $info['custom_path'] ) ) {
						$custom_path = sanitize_file_name( $info['custom_path'] );
					}

					// Skip provider folder with google as default.
					$skip_provider = 'google' === $provider ? true : false;

					$feed_body = Constant::TEMP_BODY_PREFIX . $file_name;

					if ( 'csv' === $type ) {
						static::save_csv_batch_feed( $info, $provider, $type, [], Constant::TEMP_HEADER_PREFIX . $file_name, $custom_path, $skip_provider );
						static::save_csv_batch_feed( $info, $provider, $type, [], Constant::TEMP_FOOTER_PREFIX . $file_name, $custom_path, $skip_provider );
					} else {
						static::save_batch_feed( $provider, $type, $feed['header'], Constant::TEMP_HEADER_PREFIX . $file_name, $custom_path, $skip_provider );
						static::save_batch_feed( $provider, $type, $feed['footer'], Constant::TEMP_FOOTER_PREFIX . $file_name, $custom_path, $skip_provider );
					}

					$the_feed_file = static::get_batch_feed_file_dir( $provider, $type, $feed_body, $custom_path, $skip_provider );

					if ( $the_feed_file ) {
						if ( 'csv' === $type ) {
							static::append_save_csv_batch_feed( $info, $provider, $type, $feed['body'], $feed_body, $custom_path, $skip_provider );
						} else {
							static::append_save_batch_feed( $provider, $type, $feed['body'], $feed_body, $custom_path, $skip_provider );
						}
					} else {
						if ( 'csv' === $type ) {
							$new_array[] = $feed['header'];
							static::save_csv_batch_feed( $info, $provider, $type, array_merge( $new_array, $feed['body'] ), $feed_body, $custom_path, $skip_provider );
						} else {
							static::save_batch_feed( $provider, $type, $feed['header'] . $feed['body'], $feed_body, $custom_path, $skip_provider );
						}
					}

					$status = true;
				} else {
					$status = false;
				}
			}

			return $status;
		} catch ( \Exception $e ) {
			wp_send_json_error( [
				'message' => $e->getMessage(),
			], 400 );
		}
	}

	/**
	 * Update bulk feed.
	 *
	 * @return void
	 */
	public static function regenerate_bulk_feeds() {
		global $wpdb;

		$query  = $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s;", 'gg_woo_feed_feed_' . "%" );
		$result = $wpdb->get_results( $query, ARRAY_A );
		if ( ! $result ) {
			return;
		}

		try {
			static::go_update( $result );
		} catch ( \Exception $e ) {
			gg_woo_feed_write_log( $e->getMessage() );
		}
	}

	public static function go_update( $result ) {
		try {
			$first_option = null;
			$time         = time();

			foreach ( $result as $value ) {
				$feed_info = maybe_unserialize( get_option( $value['option_name'] ) );

				if ( ! isset( $feed_info['feedqueries'] ) || ( isset( $feed_info['status'] ) && '0' == $feed_info['status'] ) ) {
					continue;
				}

				$products_info = static::get_product_information( $value['option_name'] );
				$batches       = $products_info['products'];

				if ( $products_info['total'] == 0 || ! $batches ) {
					continue;
				}

				if ( ! $first_option ) {
					$first_option = $value;
				} else {
					if ( ! wp_next_scheduled( 'gg_woo_feed_generate_feed', [ $value['option_name'] ] ) ) {
						$time += 600; // 10 minutes.
						wp_schedule_single_event( $time, 'gg_woo_feed_generate_feed', [ $value['option_name'] ] );
					}
				}
			}

			if ( $first_option ) {
				$option = $first_option;

				$feed_info = maybe_unserialize( get_option( $option['option_name'] ) );
				if ( isset( $feed_info['feedqueries'] ) && ( isset( $feed_info['status'] ) && '0' != $feed_info['status'] ) ) {
					$products_info = static::get_product_information( $option['option_name'] );
					$batches       = $products_info['products'];

					if ( $products_info['total'] != 0 && $batches ) {
						foreach ( $batches as $key => $products ) {
							static::make_batch_feed( $option['option_name'], $products );
							gg_woo_feed_write_log( sprintf( esc_html__( 'GG Woo Feed: Make batch %1$s/%2$s from %3$s', 'gg-woo-feed' ), absint( $key + 1 ), count( $batches ),
								$option['option_name'] ) );
						}

						static::save_feed_file( gg_woo_feed_extract_feed_option_name( $option['option_name'] ) );

						gg_woo_feed_write_log( sprintf( esc_html__( 'GG Woo Feed: Auto Regenerated %s. Done!', 'gg-woo-feed' ), $option['option_name'] ) );
					}
				}
			}
		} catch ( \Exception $e ) {
			gg_woo_feed_write_log( $e->getMessage() );
		}
	}

	public static function do_this_generate( $option_name ) {
		static::go_update( [ 0 => [ 'option_name' => $option_name ] ] );
	}

	/**
	 * Send feed to Google
	 *
	 * @param $data
	 * @return array
	 */
	public static function submit_google_merchant( $data ) {
		$feed_option_name = gg_woo_feed_extract_feed_option_name( sanitize_text_field( $data['feed_option_name'] ) );
		$config           = get_option( 'gg_woo_feed_config_' . $feed_option_name, false );
		$feed_info        = maybe_unserialize( get_option( 'gg_woo_feed_feed_' . $feed_option_name, [] ) );

		if ( ! $config || ! $feed_info ) {
			wp_send_json_error( [
				'success' => false,
				'message' => esc_html__( 'Feed does not exist!', 'gg-woo-feed' ),
				'reason'  => 'not_exist',
			], 400 );
		}

		if ( ! isset( $data['google_target_country'] ) || ! $data['google_target_country'] ) {
			wp_send_json_error( [
				'success' => false,
				'message' => esc_html__( 'Missing Target Country', 'gg-woo-feed' ),
				'reason'  => 'target_country',
			], 400 );
		}

		if ( ! isset( $data['google_target_language'] ) || ! $data['google_target_language'] ) {
			wp_send_json_error( [
				'success' => false,
				'message' => esc_html__( 'Missing Target Language', 'gg-woo-feed' ),
				'reason'  => 'target_language',
			], 400 );
		}

		$target_country  = sanitize_text_field( $data['google_target_country'] );
		$target_language = sanitize_text_field( $data['google_target_language'] );

		$google_merchant = new Google_Merchant_Content_API();

		if ( $google_merchant->is_authenticate() ) {
			$feed_url      = $feed_info['url'];
			$feed_name     = $config['filename'];
			$client        = $google_merchant::get_client();
			$client_id     = $google_merchant::$client_id;
			$client_secret = $google_merchant::$client_secret;
			$merchant_id   = $google_merchant::$merchant_id;

			$access_token = $google_merchant->get_access_token();
			$client->setClientId( $client_id );
			$client->setClientSecret( $client_secret );
			$client->setScopes( 'https://www.googleapis.com/auth/content' );
			$client->setAccessToken( $access_token );

			$service  = new Google_Service_ShoppingContent( $client );
			$datafeed = new Google_Service_ShoppingContent_Datafeed();
			$target   = new Google_Service_ShoppingContent_DatafeedTarget();

			$filename = $feed_info['current_file_name'] . uniqid();

			$target->setLanguage( $target_language );
			$target->setCountry( $target_country );
			$target->setIncludedDestinations( [ 'Shopping' ] );

			$datafeed->setName( $feed_name );
			$datafeed->setContentType( 'products' );
			$datafeed->setAttributeLanguage( $target_language );
			$datafeed->setTargets( [ $target ] );

			if ( ! $google_merchant->feed_exists( $feed_option_name ) ) {
				$datafeed->setFileName( $filename );
			} else {
				$datafeed->setFileName( $feed_info['google_data_feed_file_name'] );
			}

			$fetch_schedule = new Google_Service_ShoppingContent_DatafeedFetchSchedule();
			if ( $data['google_schedule'] === 'monthly' ) {
				$fetch_schedule->setDayOfMonth( $data['google_schedule_month'] );
			}

			if ( $data['google_schedule'] === 'weekly' ) {
				$fetch_schedule->setWeekday( $data['google_schedule_week_day'] );
			}

			$fetch_schedule->setHour( $data['google_schedule_time'] );
			$fetch_schedule->setFetchUrl( $feed_url );

			$format = new Google_Service_ShoppingContent_DatafeedFormat();
			$format->setFileEncoding( 'utf-8' );
			$datafeed->setFormat( $format );
			$datafeed->setFetchSchedule( $fetch_schedule );

			try {
				if ( $google_merchant->feed_exists( $feed_option_name ) ) {
					$datafeed_id = $feed_info['google_data_feed_id'];
					$datafeed->setId( $datafeed_id );
					$service->datafeeds->update( $merchant_id, $datafeed_id, $datafeed );
				} else {
					$datafeed          = $service->datafeeds->insert( $merchant_id, $datafeed );
					$datafeed_id       = $datafeed->getId();
					$datafeed_filename = $datafeed->getFileName();
					static::update_feed_info( $feed_option_name, 'google_data_feed_id', $datafeed_id );
					static::update_feed_info( $feed_option_name, 'google_data_feed_file_name', $datafeed_filename );
				}
				$service->datafeeds->fetchnow( $merchant_id, $datafeed_id );

				// Update.
				static::update_feed_option( $feed_option_name, 'google_target_country', $target_country );
				static::update_feed_option( $feed_option_name, 'google_target_language', $target_language );
				static::update_feed_option( $feed_option_name, 'google_schedule', $data['google_schedule'] );
				static::update_feed_option( $feed_option_name, 'google_schedule_month', $data['google_schedule_month'] );
				static::update_feed_option( $feed_option_name, 'google_schedule_week_day', $data['google_schedule_week_day'] );
				static::update_feed_option( $feed_option_name, 'google_schedule_time', $data['google_schedule_time'] );

				wp_send_json_success( [
					'success' => true,
					'message' => esc_html__( 'Feed sent successfully!', 'gg-woo-feed' ),
				], 200 );
			} catch ( \Exception $e ) {
				$log = wc_get_logger();
				$log->info( $e->getMessage(), [ 'source' => 'WPFM-google' ] );
				$error  = json_decode( $e->getMessage() );
				$reason = $error->error->errors;

				wp_send_json_error( [
					'success' => false,
					'message' => $error->error->message,
					'reason'  => $reason[0]->reason,
				], 400 );
			}
		} else {
			wp_send_json_error( [
				'success' => false,
				'message' => esc_html__( 'Unauthenticated', 'gg-woo-feed' ),
				'reason'  => 'unauthenticated',
			], 400 );
		}
	}

	/**
	 * Get batch feed.
	 *
	 * @param        $provider
	 * @param        $type
	 * @param        $file_name
	 * @param string $custom_path
	 * @param bool   $skip_provider
	 * @return bool|false|mixed|string|null
	 */
	public static function get_batch_feed( $provider, $type, $file_name, $custom_path = '', $skip_provider = false ) {
		$ext = $type;
		if ( 'csv' === $type ) {
			$ext = 'json';
		}

		$path = Upload::get_folder_dir( $provider, $type, $custom_path, $skip_provider );
		$file = $path . '/' . $file_name . '.' . $ext;

		if ( 'csv' === $type && file_exists( $file ) ) {
			$file = file_get_contents( $file );

			return ( $file ) ? json_decode( $file, true ) : false;
		}

		if ( file_exists( $file ) ) {
			return file_get_contents( $file );
		}

		return false;
	}

	/**
	 * Get batch feed.
	 *
	 * @param        $provider
	 * @param        $type
	 * @param        $file_name
	 * @param string $custom_path
	 * @param bool   $skip_provider
	 * @return bool|false|mixed|string|null
	 */
	public static function get_batch_feed_file_dir( $provider, $type, $file_name, $custom_path = '', $skip_provider = false ) {
		$ext = $type;
		if ( 'csv' === $type ) {
			$ext = 'json';
		}

		$path = Upload::get_folder_dir( $provider, $type, $custom_path, $skip_provider );
		$file = $path . '/' . $file_name . '.' . $ext;

		if ( file_exists( $file ) ) {
			return $file;
		}

		return false;
	}

	/**
	 * Save batch feed.
	 *
	 * @param        $provider
	 * @param        $type
	 * @param        $string
	 * @param        $file_name
	 * @param string $custom_path
	 * @param bool   $skip_provider
	 * @return bool
	 */
	public static function save_batch_feed( $provider, $type, $string, $file_name, $custom_path = '', $skip_provider = false ) {
		$ext = $type;
		if ( 'csv' === $type ) {
			$string = wp_json_encode( $string );
			$ext    = 'json';
		}

		$path = Upload::get_folder_dir( $provider, $type, $custom_path, $skip_provider );
		$file = $path . '/' . $file_name . '.' . $ext;
		$save = new Upload();

		return $save->save_file( $path, $file, $string );
	}

	/**
	 * Save batch feed.
	 *
	 * @param        $provider
	 * @param        $type
	 * @param        $string
	 * @param        $file_name
	 * @param string $custom_path
	 * @param bool   $skip_provider
	 * @return bool
	 */
	public static function save_csv_batch_feed( $info, $provider, $type, $string, $file_name, $custom_path = '', $skip_provider = false ) {
		$ext = $type;
		if ( 'csv' === $type ) {
			// $string = wp_json_encode( $string );
			$ext = 'json';
		}

		$path = Upload::get_folder_dir( $provider, $type, $custom_path, $skip_provider );
		$file = $path . '/' . $file_name . '.' . $ext;
		$save = new Upload();

		return $save->save_csv_file( $path, $file, $string, $info );
	}

	/**
	 * Save batch feed.
	 *
	 * @param        $provider
	 * @param        $type
	 * @param        $string
	 * @param        $file_name
	 * @param string $custom_path
	 * @param bool   $skip_provider
	 * @return bool
	 */
	public static function prepend_save_batch_feed( $provider, $type, $string, $file_name, $custom_path = '', $skip_provider = false ) {
		$ext = $type;
		if ( 'csv' === $type ) {
			$string = wp_json_encode( $string );
			$ext    = 'json';
		}

		$path = Upload::get_folder_dir( $provider, $type, $custom_path, $skip_provider );
		$file = $path . '/' . $file_name . '.' . $ext;
		$save = new Upload();

		return $save->prepend_save_file( $path, $file, $string );
	}

	/**
	 * Save batch feed.
	 *
	 * @param        $provider
	 * @param        $type
	 * @param        $string
	 * @param        $file_name
	 * @param string $custom_path
	 * @param bool   $skip_provider
	 * @return bool
	 */
	public static function append_save_batch_feed( $provider, $type, $string, $file_name, $custom_path = '', $skip_provider = false ) {
		$ext = $type;
		if ( 'csv' === $type ) {
			$string = wp_json_encode( $string );
			$ext    = 'json';
		}

		$path = Upload::get_folder_dir( $provider, $type, $custom_path, $skip_provider );
		$file = $path . '/' . $file_name . '.' . $ext;
		$save = new Upload();

		return $save->append_save_file( $path, $file, $string );
	}

	/**
	 * Save batch feed.
	 *
	 * @param        $provider
	 * @param        $type
	 * @param        $string
	 * @param        $file_name
	 * @param string $custom_path
	 * @param bool   $skip_provider
	 * @return bool
	 */
	public static function append_save_csv_batch_feed( $info, $provider, $type, $string, $file_name, $custom_path = '', $skip_provider = false ) {
		$ext = $type;
		if ( 'csv' === $type ) {
			// $string = wp_json_encode( $string );
			$ext = 'json';
		}

		$path = Upload::get_folder_dir( $provider, $type, $custom_path, $skip_provider );
		$file = $path . '/' . $file_name . '.' . $ext;
		$save = new Upload();

		return $save->append_save_csv_file( $path, $file, $string, $info );
	}

	/**
	 * Get batch feed.
	 *
	 * @param        $provider
	 * @param        $type
	 * @param        $file_name
	 * @param        $new_file_name
	 * @param string $custom_path
	 * @param bool   $skip_provider
	 * @return bool|false|mixed|string|null
	 */
	public static function rename_feed_file( $provider, $type, $file_name, $new_file_name, $custom_path = '', $skip_provider = false ) {
		$file = Upload::get_file_dir( $file_name, $provider, $type, $custom_path, $skip_provider );

		if ( file_exists( $file ) ) {
			rename( $file, $new_file_name );
		}

		return false;
	}

	/**
	 * @param        $provider
	 * @param        $file_name
	 * @param        $type
	 * @param string $custom_path
	 * @param bool   $skip_provider
	 * @return bool
	 */
	public static function unlink_temp_files( $provider, $file_name, $type, $custom_path = '', $skip_provider = false ) {
		$path = Upload::get_folder_dir( $provider, $type, $custom_path, $skip_provider );
		$ext  = $type;

		if ( 'csv' === $type ) {
			$ext = 'json';
		}

		$files = [
			'headerFile' => $path . '/' . Constant::TEMP_HEADER_PREFIX . $file_name . '.' . $ext,
			'bodyFile'   => $path . '/' . Constant::TEMP_BODY_PREFIX . $file_name . '.' . $ext,
			'footerFile' => $path . '/' . Constant::TEMP_FOOTER_PREFIX . $file_name . '.' . $ext,
		];

		if ( ! empty( $files ) ) {
			foreach ( $files as $key => $file ) {
				if ( file_exists( $file ) ) {
					unlink( $file );
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * @param        $file_name
	 * @param        $provider
	 * @param        $type
	 * @param string $custom_path
	 * @param bool   $skip_provider
	 * @return bool|string
	 */
	public static function sanitize_file_name( $file_name, $type ) {
		$raw_filename = sanitize_title( $file_name, '', 'save' );
		$raw_filename = sanitize_file_name( $raw_filename . '.' . $type );
		$raw_filename = str_replace( '.' . $type, '', $raw_filename );

		return $raw_filename;
	}

	/**
	 * @param $feed_option_name
	 * @param $key
	 * @param $value
	 */
	public static function update_feed_option( $feed_option_name, $key, $value ) {
		$feed_name = gg_woo_feed_extract_feed_option_name( $feed_option_name );
		$config    = get_option( 'gg_woo_feed_config_' . $feed_name, [] );
		$feed_info = maybe_unserialize( get_option( 'gg_woo_feed_feed_' . $feed_name, [] ) );

		$config[ $key ]           = $value;
		$feed_info['feedqueries'] = $config;

		update_option( 'gg_woo_feed_config_' . $feed_name, $config );
		update_option( 'gg_woo_feed_feed_' . $feed_name, $feed_info );
	}

	/**
	 * @param $feed_option_name
	 * @param $key
	 * @param $value
	 */
	public static function update_feed_info( $feed_option_name, $key, $value ) {
		$feed_name         = gg_woo_feed_extract_feed_option_name( $feed_option_name );
		$feed_info         = maybe_unserialize( get_option( 'gg_woo_feed_feed_' . $feed_name, [] ) );
		$feed_info[ $key ] = $value;
		update_option( 'gg_woo_feed_feed_' . $feed_name, $feed_info );
	}
}

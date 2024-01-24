<?php

use GG_Woo_Feed\Common\Dropdown;
use GG_Woo_Feed\Common\Generate_Wizard;
use GG_Woo_Feed\Common\Mapping_Attributes;
use GG_Woo_Feed\Common\Upload;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
function gg_woo_feed_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'gg_woo_feed_clean', $var );
	}

	return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
}

function gg_woo_feed_is_valid_as_array( $array ) {
	return ( count( $array ) && is_array( $array ) ) ? true : false;
}

/**
 * Output msg json.
 *
 **/
function gg_woo_feed_output_msg_json( $result = false, $message = '', $args = [], $return = false ) {
	$out          = new stdClass();
	$out->status  = $result;
	$out->message = $message;
	if ( $args ) {
		foreach ( $args as $key => $arg ) {
			$out->$key = $arg;
		}
	}
	if ( $return ) {
		return json_encode( $out );
	}

	echo json_encode( $out );
	die;
}

/**
 * Get Options Value by Key
 *
 * @return mixed
 *
 */
function gg_woo_feed_get_option( $key, $default = '' ) {
	global $gg_woo_feed_options;

	$value = isset( $gg_woo_feed_options[ $key ] ) ? $gg_woo_feed_options[ $key ] : $default;
	$value = apply_filters( 'gg_woo_feed_option_', $value, $key, $default );

	return apply_filters( 'gg_woo_feed_option_' . $key, $value, $key, $default );
}

function gg_woo_feed_get_product_attributes() {
	$taxonomy_objects = get_object_taxonomies( 'product', 'objects' );
	$attributes['']   = esc_html__( 'Select', 'gg-woo-feed' );
	foreach ( $taxonomy_objects as $taxonomy_key => $taxonomy_object ) {
		if ( $taxonomy_key === 'product_type' ) {
			$attributes[ $taxonomy_key ] = esc_html__( 'Product Type', 'gg-woo-feed' ) . ' (' . $taxonomy_key . ')';
		} else {
			$attributes[ $taxonomy_key ] = $taxonomy_object->label . ' (' . $taxonomy_key . ')';
		}
	}

	return $attributes;
}

if ( ! function_exists( 'gg_woo_feed_is_valid_ext' ) ) {
	/**
	 * Is valid ext.
	 *
	 * @param string $ext
	 *
	 * @return bool
	 */
	function gg_woo_feed_is_valid_ext( $ext ) {
		return array_key_exists( $ext, gg_woo_feed_get_file_types() );
	}
}

if ( ! function_exists( 'gg_woo_feed_get_file_types' ) ) {
	function gg_woo_feed_get_file_types() {
		return apply_filters( 'gg_woo_feed_get_file_types', [
			'xml' => 'XML',
			'csv' => 'CSV',
			'txt' => 'TXT',
		] );
	}
}

if ( ! function_exists( 'gg_woo_feed_parse_feed_queries' ) ) {
	/**
	 * Parse feed rules.
	 *
	 * @param array  $rules   rules to parse.
	 * @param string $context parsing context. useful for filtering, view, save, db, create etc.
	 *
	 * @return array
	 *
	 */
	function gg_woo_feed_parse_feed_queries( $rules = [], $context = 'view' ) {
		if ( empty( $rules ) ) {
			$rules = [];
		}

		$defaults = [
			'provider'                       => '',
			'filename'                       => '',
			'feed_type'                      => '',
			'items_wrap'                     => 'products',
			'item_wrap'                      => 'product',
			'delimiter'                      => ',',
			'enclosure'                      => 'double',
			'extra_header'                   => '',
			'vendors'                        => [],
			// General
			'title_use_custom'               => 'on',
			'title_add_variation'            => 'on',
			'title_fix_uppercase'            => 'on',
			'title_remove_cap'               => 'off',
			'desc_use_custom'                => 'on',
			'desc_use_short_description'     => 'on',
			'desc_use_description'           => 'on',
			'desc_use_title'                 => 'on',
			'brand_use_custom'               => 'on',
			'brand_use_cat_custom'           => 'on',
			'condition_use_custom'           => 'on',
			'gtin_use_custom'                => 'on',
			'mpn_use_custom'                 => 'on',
			'mpn_use_cat_custom'             => 'on',
			'google_taxonomy_use_custom'     => 'on',
			'google_taxonomy_use_cat_custom' => 'on',
			'adult_use_custom'               => 'on',
			'adult_use_cat_custom'           => 'on',
			'shipping_label_use_custom'      => 'on',
			'shipping_label_use_cat_custom'  => 'on',

			'feed_variable_price'           => 'smallest',
			'feed_variable_attr'            => 'all',
			'feed_variable_title'           => 'default',
			'feed_variable_image'           => 'price',
			// 'use_default_variation'         => 'off',
			'include_additional_image_link' => 'on',
			'variable_quantity'             => 'first',
			// Filter
			'product_limit'                 => '-1',
			'feed_filter_stock'             => 'instock',
			'feed_filter_sale'              => 'all',
			'feed_category_all'             => 'on',
			'feed_category'                 => [],
			'feed_filter_product_type'      => [ 'simple', 'variable', 'grouped', 'external' ],
			'exclude_variations'            => 'on',
			'show_main_variable_product'    => 'on',
			// Filter by attributes
			'filter_by_attributes'          => 'off',
			'filter_attribute_relationship' => 'and',
			'filter_by_attributes_atts'     => [],
			'conditions_attributes'         => [],
			'condition_values_attributes'   => [],
			// Advanced Filter
			'filter_relationship'           => 'and',
			'filter_atts'                   => [],
			'conditions'                    => [],
			'condition_values'              => [],
			// Config
			'mattributes'                   => [],
			'prefix'                        => [],
			'type'                          => [],
			'attributes'                    => [],
			'default'                       => [],
			'default_value'                 => [],
			'suffix'                        => [],
			'output_type'                   => [],
			// Filters tab
			'product_ids'                   => '',
			'categories'                    => [],
			'campaign_parameters'           => [],
			'product_visibility'            => 0,

			'ptitle_show'                => '',
			'decimal_separator'          => wc_get_price_decimal_separator(),
			'thousand_separator'         => wc_get_price_thousand_separator(),
			'decimals'                   => wc_get_price_decimals(),

			// Others.
			'feed_language'              => apply_filters( 'wpml_current_language', null ),
			'feed_currency'              => get_woocommerce_currency(),

			// Google Sync.
			'google_target_country'      => 'US',
			'google_target_language'     => 'en',
			'google_schedule'            => 'hourly',
			'google_schedule_month'      => '1',
			'google_schedule_week_day'   => 'monday',
			'google_schedule_time'       => '1',
			'google_data_feed_id'        => '',
			'google_data_feed_file_name' => '',
		];

		if ( class_exists( 'WPSEO_Frontend' ) ) {
			$defaults['title_use_yoast'] = 'off';
			$defaults['desc_use_yoast']  = 'off';
		}

		$rules = wp_parse_args( $rules, $defaults );

		$rules['campaign_parameters'] = wp_parse_args(
			$rules['campaign_parameters'],
			[
				'utm_source'   => '',
				'utm_medium'   => '',
				'utm_campaign' => '',
				'utm_term'     => '',
				'utm_content'  => '',
			]
		);

		if ( ! empty( $rules['provider'] ) && is_string( $rules['provider'] ) ) {
			$rules = apply_filters( "gg_woo_feed_{$rules['provider']}_parsed_rules", $rules, $context );
		}

		return apply_filters( 'gg_woo_feed_parsed_rules', $rules, $context );
	}
}

/**
 * Sanitize data option.
 *
 * @param $data
 * @return mixed
 */
function gg_woo_feed_sanitize_data_option( $data ) {
	$checkboxes = [
		'feed_category_all',
		'title_use_custom',
		'title_add_variation',
		'title_fix_uppercase',
		'title_remove_cap',
		'desc_use_custom',
		'desc_use_short_description',
		'desc_use_description',
		'desc_use_title',
		'brand_use_custom',
		'brand_use_cat_custom',
		'condition_use_custom',
		'gtin_use_custom',
		'mpn_use_custom',
		'mpn_use_cat_custom',
		'google_taxonomy_use_custom',
		'google_taxonomy_use_cat_custom',
		'adult_use_custom',
		'adult_use_cat_custom',
		'shipping_label_use_custom',
		'shipping_label_use_cat_custom',
		'exclude_variations',
		'show_main_variable_product',
		'include_additional_image_link',
		'filter_by_attributes',
	];

	foreach ( $checkboxes as $checkbox ) {
		if ( ! isset( $data[ $checkbox ] ) ) {
			$data[ $checkbox ] = 'off';
		}
	}

	if ( isset( $data['custom_path'] ) ) {
		$data['custom_path'] = sanitize_file_name( $data['custom_path'] );
	}

	return $data;
}

if ( ! function_exists( 'gg_woo_feed_sanitize_form_fields' ) ) {
	/**
	 * Sanitize Form Fields.
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	function gg_woo_feed_sanitize_form_fields( $data ) {
		foreach ( $data as $key => $value ) {
			if ( true === apply_filters( 'gg_woo_feed_sanitize_form_fields', true, $key, $value, $data ) ) {
				if ( is_array( $value ) ) {
					$value = gg_woo_feed_sanitize_form_fields( $value );
				}
			}

			$data[ $key ] = apply_filters( 'gg_woo_feed_sanitize_form_field', $value, $key );
		}

		return $data;
	}
}

if ( ! function_exists( 'gg_woo_feed_generate_unique_feed_file_name' ) ) {
	/**
	 * Generate unique file Name.
	 *
	 * @param string $filename
	 * @param string $type
	 * @param string $provider
	 *
	 * @return string
	 */
	function gg_woo_feed_generate_unique_feed_file_name( $filename, $type, $provider, $custom_path = '', $skip_provider = false ) {
		$feed_dir     = Upload::get_folder_dir( $provider, $type, $custom_path, $skip_provider );
		$raw_filename = sanitize_title( $filename, '', 'save' );
		$raw_filename = gg_woo_feed_unique_feed_slug( $raw_filename, 'gg_woo_feed_feed_' );
		$raw_filename = sanitize_file_name( $raw_filename . '.' . $type );
		$raw_filename = wp_unique_filename( $feed_dir, $raw_filename );
		$raw_filename = str_replace( '.' . $type, '', $raw_filename );

		return -1 != $raw_filename ? $raw_filename : false;
	}
}

if ( ! function_exists( 'gg_woo_feed_extract_feed_option_name' ) ) {
	/**
	 * Remove Feed Option Name Prefix and return the slug
	 *
	 * @param string $feed_option_name
	 *
	 * @return string
	 */
	function gg_woo_feed_extract_feed_option_name( $feed_option_name ) {
		return str_replace( [ 'gg_woo_feed_feed_', 'gg_woo_feed_config_' ], '', $feed_option_name );
	}
}

if ( ! function_exists( 'gg_woo_feed_get_provider_attributes' ) ) {
	/**
	 * Parse URL parameter
	 *
	 * @param string $plugin_attr
	 * @param string $provider
	 * @param string feed_type CSV XML TXT
	 *
	 * @return string
	 */
	function gg_woo_feed_get_provider_attributes( $plugin_attr, $provider, $feed_type ) {
		$mapping       = new Mapping_Attributes();
		$provider_atts = '';
		if ( 'google' == $provider && 'xml' == $feed_type ) {
			$provider_atts = $mapping->get_google_XML_attributes();
		} elseif ( 'google' == $provider && ( 'csv' == $feed_type || 'txt' == $feed_type ) ) {
			$provider_atts = $mapping->get_google_CSV_TXT_attributes();
		} elseif ( 'facebook' == $provider && 'xml' == $feed_type ) {
			$provider_atts = $mapping->get_facebook_XML_attributes();
		} elseif ( 'facebook' == $provider && ( 'csv' == $feed_type || 'txt' == $feed_type ) ) {
			$provider_atts = $mapping->get_facebook_CSV_TXT_attributes();
		} elseif ( 'pinterest' == $provider && 'xml' == $feed_type ) {
			$provider_atts = $mapping->get_pinterest_XML_attributes();
		} elseif ( 'pinterest' == $provider && ( 'csv' == $feed_type || 'txt' == $feed_type ) ) {
			$provider_atts = $mapping->get_pinterest_CSV_TXT_attributes();
		}

		if ( ! empty( $provider_atts ) && array_key_exists( $plugin_attr, $provider_atts ) ) {
			return $provider_atts[ $plugin_attr ][0];
		}

		return $plugin_attr;
	}
}

if ( ! function_exists( 'gg_woo_feed_add_cdata' ) ) {
	/**
	 * Parse URL parameter
	 *
	 * @param string $plugin_attr
	 * @param string $attributeValue
	 * @param string $merchant
	 *
	 * @return string
	 */
	function gg_woo_feed_add_cdata( $plugin_attribute, $attribute_value, $provider ) {
		if ( strpos( $attribute_value, "<![CDATA[" ) !== false ) {
			return "$attribute_value";
		}

		$mapping       = new Mapping_Attributes();
		$provider_atts = '';
		if ( 'google' == $provider ) {
			$provider_atts = $mapping->get_google_XML_attributes();
		} elseif ( 'facebook' == $provider ) {
			$provider_atts = $mapping->get_facebook_XML_attributes();
		} elseif ( 'pinterest' == $provider ) {
			$provider_atts = $mapping->get_pinterest_XML_attributes();
		}

		if ( ! empty( $provider_atts ) && array_key_exists( $plugin_attribute, $provider_atts ) ) {
			if ( 'true' == $provider_atts[ $plugin_attribute ][1] ) {
				return "<![CDATA[$attribute_value]]>";
			} else {
				return "$attribute_value";
			}
		} elseif ( false !== strpos( $attribute_value, "&" ) || 'http' == substr( trim( $attribute_value ), 0, 4 ) ) {
			return "<![CDATA[$attribute_value]]>";
		} else {
			return "$attribute_value";
		}
	}
}

if ( ! function_exists( 'gg_woo_feed_strip_invalid_xml' ) ) {
	/**
	 * Remove non supported xml character
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	function gg_woo_feed_strip_invalid_xml( $value ) {
		$ret = '';
		if ( empty( $value ) ) {
			return $ret;
		}
		$length = strlen( $value );
		for ( $i = 0; $i < $length; $i++ ) {
			$current = ord( $value[ $i ] );
			if ( ( 0x9 == $current ) || ( 0xA == $current ) || ( 0xD == $current ) || ( ( $current >= 0x20 ) && ( $current <= 0xD7FF ) ) || ( ( $current >= 0xE000 ) && ( $current <= 0xFFFD ) ) || ( ( $current >= 0x10000 ) && ( $current <= 0x10FFFF ) ) ) {
				$ret .= chr( $current );
			} else {
				$ret .= '';
			}
		}

		return $ret;
	}
}

if ( ! function_exists( 'gg_woo_feed_make_url_with_parameter' ) ) {
	/**
	 * Make proper URL using parameters
	 *
	 * @param string $output
	 * @param string $suffix
	 *
	 * @return string
	 */
	function gg_woo_feed_make_url_with_parameter( $output = '', $suffix = '' ) {
		if ( empty( $output ) || empty( $suffix ) ) {
			return $output;
		}

		$getParam = explode( '?', $output );
		$URLParam = [];
		if ( isset( $getParam[1] ) ) {
			$URLParam = gg_woo_feed_parse_string( $getParam[1] );
		}

		$EXTRAParam = [];
		if ( ! empty( $suffix ) ) {
			$suffix     = str_replace( '?', '', $suffix );
			$EXTRAParam = gg_woo_feed_parse_string( $suffix );
		}

		$params = array_merge( $URLParam, $EXTRAParam );
		if ( ! empty( $params ) && '' != $output ) {
			$params  = http_build_query( $params );
			$baseURL = isset( $getParam ) ? $getParam[0] : $output;
			$output  = $baseURL . '?' . $params;
		}

		return $output;
	}
}

if ( ! function_exists( 'gg_woo_feed_parse_string' ) ) {
	/**
	 * Parse URL parameter
	 *
	 * @param string $str
	 *
	 * @return array
	 */
	function gg_woo_feed_parse_string( $str = '' ) {

		# result array
		$arr = [];

		if ( empty( $str ) ) {
			return $arr;
		}

		# split on outer delimiter
		$pairs = explode( '&', $str );

		if ( ! empty( $pairs ) ) {

			# loop through each pair
			foreach ( $pairs as $i ) {
				# split into name and value
				list( $name, $value ) = explode( '=', $i, 2 );

				# if name already exists
				if ( isset( $arr[ $name ] ) ) {
					# stick multiple values into an array
					if ( is_array( $arr[ $name ] ) ) {
						$arr[ $name ][] = $value;
					} else {
						$arr[ $name ] = [ $arr[ $name ], $value ];
					}
				} # otherwise, simply stick it in a scalar
				else {
					$arr[ $name ] = $value;
				}
			}
		} elseif ( ! empty( $str ) ) {
			list( $name, $value ) = explode( '=', $str, 2 );
			$arr[ $name ] = $value;
		}

		# return result array
		return $arr;
	}
}

if ( ! function_exists( 'gg_woo_feed_wc_version_check' ) ) {
	/**
	 * Check WooCommerce Version
	 *
	 * @param string $version
	 *
	 * @return bool
	 */
	function gg_woo_feed_wc_version_check( $version = '3.0' ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins = get_plugins();
		if ( array_key_exists( 'woocommerce/woocommerce.php', $plugins ) ) {
			$currentVersion = $plugins['woocommerce/woocommerce.php']['Version'];
			if ( version_compare( $currentVersion, $version, ">=" ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'gg_woo_feed_verify_nonce' ) ) {
	/**
	 * Verify nonce field
	 *
	 * @param string $version
	 *
	 * @return bool
	 */
	function gg_woo_feed_verify_nonce( $action, $name = '_wpnonce' ) {
		if ( !isset( $_REQUEST[$name] ) || !wp_verify_nonce( $_REQUEST[$name], $action ) ) {
			wp_die( __( 'Permission denied.', 'gg-woo-feed' ) );
		}
	}
}

if ( ! function_exists( 'gg_woo_feed_get_field_output_type_options' ) ) {
	function gg_woo_feed_get_field_output_type_options() {
		$dropdown = new Dropdown();

		return apply_filters( 'gg_woo_feed_field_output_options', $dropdown->output_types );
	}
}

if ( ! function_exists( 'gg_woo_feed_product_stock_statuses' ) ) {
	function gg_woo_feed_product_stock_statuses() {
		$dropdown = new Dropdown();

		return apply_filters( 'gg_woo_feed_field_output_options', $dropdown->get_product_stock_statuses() );
	}
}

if ( ! function_exists( 'gg_woo_feed_product_sale_statuses' ) ) {
	function gg_woo_feed_product_sale_statuses() {
		$dropdown = new Dropdown();

		return apply_filters( 'gg_woo_feed_field_output_options', $dropdown->get_product_sale_statuses() );
	}
}

function gg_woo_feed_get_product_attribute_dropdown( $selected = '' ) {
	return \GG_Woo_Feed\Common\Product_Attributes::get_attribute_dropdown( $selected );
}

function gg_woo_feed_get_wc_product_attribute_dropdown( $selected = '' ) {
	return \GG_Woo_Feed\Common\Product_Attributes::get_wc_product_attribute_dropdown( $selected );
}

function gg_woo_feed_get_filter_condition_options() {
	return Dropdown::get_filter_conditions();
}

function gg_woo_feed_get_meta_query_condition_options() {
	return Dropdown::get_meta_query_conditions();
}

function gg_woo_feed_regenerate_bulk_feeds() {
	Generate_Wizard::regenerate_bulk_feeds();
}

add_action( 'gg_woo_feed_update', 'gg_woo_feed_regenerate_bulk_feeds' );

function gg_woo_feed_do_this_generate( $option_name ) {
	Generate_Wizard::do_this_generate( $option_name );
}

add_action( 'gg_woo_feed_generate_feed', 'gg_woo_feed_do_this_generate', 10, 1 );

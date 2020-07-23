<?php
namespace GG_Woo_Feed\Common;

use GG_Woo_Feed\Core\Constant;

class Product_Attributes {

	/**
	 * Get all wc attributes.
	 *
	 * @return bool|array
	 */
	public static function get_all_attributes() {
		$info          = [];
		$wc_attributes = wc_get_attribute_taxonomy_labels();
		if ( count( $wc_attributes ) ) {
			foreach ( $wc_attributes as $key => $value ) {
				$info[ Constant::PRODUCT_ATTR_PREFIX . $key ] = $value;
			}
		}

		return $info;
	}

	/**
	 * Get product attribute dropdown.
	 *
	 * @param string $selected
	 *
	 * @return string
	 */
	public static function get_attribute_dropdown( $selected = '' ) {
		$attribute_dropdown = wp_cache_get( 'gg_woo_feed_dropdown_product_attributes' );

		if ( false === $attribute_dropdown ) {
			$attributes = [
				'id'                        => esc_attr__( 'Product Id', 'gg-woo-feed' ),
				'title'                     => esc_attr__( 'Product Title', 'gg-woo-feed' ),
				'description'               => esc_attr__( 'Product Description', 'gg-woo-feed' ),
				'short_description'         => esc_attr__( 'Product Short Description', 'gg-woo-feed' ),
				'product_type'              => esc_attr__( 'Product Local Category', 'gg-woo-feed' ),
				'link'                      => esc_attr__( 'Product URL', 'gg-woo-feed' ),
				'ex_link'                   => esc_attr__( 'External Product URL', 'gg-woo-feed' ),
				'condition'                 => esc_attr__( 'Condition', 'gg-woo-feed' ),
				'item_group_id'             => esc_attr__( 'Parent Id [Group Id]', 'gg-woo-feed' ),
				'sku'                       => esc_attr__( 'SKU', 'gg-woo-feed' ),
				'parent_sku'                => esc_attr__( 'Parent SKU', 'gg-woo-feed' ),
				'availability'              => esc_attr__( 'Availability', 'gg-woo-feed' ),
				'quantity'                  => esc_attr__( 'Quantity', 'gg-woo-feed' ),
				'price'                     => esc_attr__( 'Regular Price', 'gg-woo-feed' ),
				'current_price'             => esc_attr__( 'Price', 'gg-woo-feed' ),
				'sale_price'                => esc_attr__( 'Sale Price', 'gg-woo-feed' ),
				'price_with_tax'            => esc_attr__( 'Regular Price With Tax', 'gg-woo-feed' ),
				'current_price_with_tax'    => esc_attr__( 'Price With Tax', 'gg-woo-feed' ),
				'sale_price_with_tax'       => esc_attr__( 'Sale Price With Tax', 'gg-woo-feed' ),
				'sale_price_sdate'          => esc_attr__( 'Sale Start Date', 'gg-woo-feed' ),
				'sale_price_edate'          => esc_attr__( 'Sale End Date', 'gg-woo-feed' ),
				'weight'                    => esc_attr__( 'Weight', 'gg-woo-feed' ),
				'width'                     => esc_attr__( 'Width', 'gg-woo-feed' ),
				'height'                    => esc_attr__( 'Height', 'gg-woo-feed' ),
				'length'                    => esc_attr__( 'Length', 'gg-woo-feed' ),
				'shipping_class'            => esc_attr__( 'Shipping Class', 'gg-woo-feed' ),
				'type'                      => esc_attr__( 'Product Type', 'gg-woo-feed' ),
				'variation_type'            => esc_attr__( 'Variation Type', 'gg-woo-feed' ),
				'visibility'                => esc_attr__( 'Visibility', 'gg-woo-feed' ),
				'rating_total'              => esc_attr__( 'Total Rating', 'gg-woo-feed' ),
				'rating_average'            => esc_attr__( 'Average Rating', 'gg-woo-feed' ),
				'tags'                      => esc_attr__( 'Tags', 'gg-woo-feed' ),
				'sale_price_effective_date' => esc_attr__( 'Sale Price Effective Date', 'gg-woo-feed' ),
				'is_bundle'                 => esc_attr__( 'Is Bundle', 'gg-woo-feed' ),
				'author_name'               => esc_attr__( 'Author Name', 'gg-woo-feed' ),
				'author_email'              => esc_attr__( 'Author Email', 'gg-woo-feed' ),
				'date_created'              => esc_attr__( 'Date Created', 'gg-woo-feed' ),
				'date_updated'              => esc_attr__( 'Date Updated', 'gg-woo-feed' ),
			];

			$custom_metaboxes = [
				'custom_title'                => __( 'Custom title', 'gg-woo-feed' ),
				'custom_description'          => __( 'Custom description', 'gg-woo-feed' ),
				'custom_url'                  => __( 'Custom URL', 'gg-woo-feed' ),
				'condition'                   => __( 'Custom Condition', 'gg-woo-feed' ),
				'gtin'                        => __( 'GTIN', 'gg-woo-feed' ),
				'mpn'                         => __( 'MPN', 'gg-woo-feed' ),
				'google_taxonomy'             => __( 'Google Taxonomy', 'gg-woo-feed' ),
				'identifier_exists'           => __( 'Identifier exists', 'gg-woo-feed' ),
				'adult'                       => __( 'Adult', 'gg-woo-feed' ),
				'age_group'                   => __( 'Age Group', 'gg-woo-feed' ),
				'multipack'                   => __( 'Multipack', 'gg-woo-feed' ),
				'color'                       => __( 'Color', 'gg-woo-feed' ),
				'gender'                      => __( 'Gender', 'gg-woo-feed' ),
				'pattern'                     => __( 'Pattern', 'gg-woo-feed' ),
				'size'                        => __( 'Size', 'gg-woo-feed' ),
				'size_type'                   => __( 'Size type', 'gg-woo-feed' ),
				'size_system'                 => __( 'Size system', 'gg-woo-feed' ),
				'max_handling_time'           => __( 'max_handling_time', 'gg-woo-feed' ),
				'min_handling_time'           => __( 'min_handling_time', 'gg-woo-feed' ),
				'energy_efficiency_class'     => __( 'energy_efficiency_class', 'gg-woo-feed' ),
				'max_energy_efficiency_class' => __( 'max_energy_efficiency_class', 'gg-woo-feed' ),
				'min_energy_efficiency_class' => __( 'min_energy_efficiency_class', 'gg-woo-feed' ),
				'unit_pricing_measure'        => __( 'unit_pricing_measure', 'gg-woo-feed' ),
				'unit_pricing_base_measure'   => __( 'unit_pricing_base_measure', 'gg-woo-feed' ),
				'installmentmonths'           => __( 'installmentmonths', 'gg-woo-feed' ),
				'installmentamount'           => __( 'installmentamount', 'gg-woo-feed' ),
				'promotion_id'                => __( 'promotion_id', 'gg-woo-feed' ),
			];

			$images = [
				'image'         => esc_attr__( 'Main Image', 'gg-woo-feed' ),
				'feature_image' => esc_attr__( 'Featured Image', 'gg-woo-feed' ),
				'images'        => esc_attr__( 'Images [Comma Separated]', 'gg-woo-feed' ),
				'image_1'       => esc_attr__( 'Additional Image 1', 'gg-woo-feed' ),
				'image_2'       => esc_attr__( 'Additional Image 2', 'gg-woo-feed' ),
				'image_3'       => esc_attr__( 'Additional Image 3', 'gg-woo-feed' ),
				'image_4'       => esc_attr__( 'Additional Image 4', 'gg-woo-feed' ),
				'image_5'       => esc_attr__( 'Additional Image 5', 'gg-woo-feed' ),
				'image_6'       => esc_attr__( 'Additional Image 6', 'gg-woo-feed' ),
				'image_7'       => esc_attr__( 'Additional Image 7', 'gg-woo-feed' ),
				'image_8'       => esc_attr__( 'Additional Image 8', 'gg-woo-feed' ),
				'image_9'       => esc_attr__( 'Additional Image 9', 'gg-woo-feed' ),
				'image_10'      => esc_attr__( 'Additional Image 10', 'gg-woo-feed' ),
			];

			$attribute_dropdown = '<option></option>';
			if ( is_array( $attributes ) && ! empty( $attributes ) ) {
				$attribute_dropdown .= sprintf( '<optgroup label="%s">', esc_attr__( 'Primary Attributes', 'gg-woo-feed' ) );
				foreach ( $attributes as $key => $value ) {
					$attribute_dropdown .= sprintf( '<option value="%s">%s</option>', $key, $value );
				}
				$attribute_dropdown .= '</optgroup>';
			}

			if ( is_array( $custom_metaboxes ) && ! empty( $custom_metaboxes ) ) {
				$attribute_dropdown .= sprintf( '<optgroup label="%s">', esc_attr__( 'Custom Meta', 'gg-woo-feed' ) );
				foreach ( $custom_metaboxes as $meta_key => $meta_value ) {
					$meta_key           = Constant::PRODUCT_META_PREFIX . $meta_key;
					$attribute_dropdown .= sprintf( '<option value="%s">%s</option>', $meta_key, $meta_value );
				}
				$attribute_dropdown .= '</optgroup>';
			}

			if ( is_array( $images ) && ! empty( $images ) ) {
				$attribute_dropdown .= sprintf( '<optgroup label="%s">', esc_attr__( 'Image Attributes', 'gg-woo-feed' ) );
				foreach ( $images as $key => $value ) {
					$attribute_dropdown .= sprintf( '<option value="%s">%s</option>', $key, $value );
				}
				$attribute_dropdown .= '</optgroup>';
			}

			$wc_attributes = static::get_all_attributes();
			if ( is_array( $wc_attributes ) && ! empty( $wc_attributes ) ) {
				$attribute_dropdown .= sprintf( '<optgroup label="%s">', esc_attr__( 'Product Attributes', 'gg-woo-feed' ) );
				foreach ( $wc_attributes as $key => $value ) {
					$attribute_dropdown .= sprintf( '<option value="%s">%s</option>', $key, $value );
				}
				$attribute_dropdown .= '</optgroup>';
			}

			$custom_attributes = static::get_custom_fields();
			if ( is_array( $custom_attributes ) && ! empty( $custom_attributes ) ) {
				$attribute_dropdown .= sprintf( '<optgroup label="%s">', esc_attr__( 'Custom Fields', 'gg-woo-feed' ) );
				foreach ( $custom_attributes as $key => $value ) {
					$attribute_dropdown .= sprintf( '<option value="%s">%s</option>', $key, $value );
				}
				$attribute_dropdown .= '</optgroup>';
			}

			wp_cache_set( 'gg_woo_feed_dropdown_product_attributes', $attribute_dropdown );
		}

		if ( $selected && strpos( $attribute_dropdown, 'value="' . $selected . '"' ) !== false ) {
			$attribute_dropdown = str_replace( 'value="' . $selected . '"', 'value="' . $selected . '"' . ' selected', $attribute_dropdown );
		}

		return $attribute_dropdown;
	}

	/**
	 * Get all custom fields.
	 *
	 * @return array
	 */
	public static function get_custom_fields() {
		global $wpdb;
		$fields = [];

		return apply_filters( 'gg_woo_feed_get_custom_fields', $fields );

		$sql = "SELECT meta.meta_id, meta.meta_key as name, meta.meta_value as type FROM " . $wpdb->prefix . "postmeta" . " AS meta, " . $wpdb->prefix . "posts" . " AS posts WHERE meta.post_id = posts.id AND posts.post_type LIKE '%product%' AND meta.meta_key NOT LIKE 'gg_woo_feed_%' AND meta.meta_key NOT LIKE 'pyre%' AND meta.meta_key NOT LIKE 'sbg_%' AND meta.meta_key NOT LIKE 'rp_%' GROUP BY meta.meta_key ORDER BY meta.meta_key ASC;";

		$data = $wpdb->get_results( $sql );

		if ( count( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( false === stripos( $value->name, '_product_attributes' ) ) {
					$value_display                          = str_replace( '_', ' ', $value->name );
					$fields[ Constant::PRODUCT_CUSTOM_FIELD_PREFIX . $value->name ] = ucfirst( $value_display );
				}
			}
		}

		return $fields;
	}

	public static function get_wc_product_attribute_dropdown( $selected = '' ) {
		$attribute_dropdown = wp_cache_get( 'gg_woo_feed_dropdown_wc_product_attributes' );

		if ( false === $attribute_dropdown ) {
			$wc_attributes = static::get_all_attributes();
			if ( is_array( $wc_attributes ) && ! empty( $wc_attributes ) ) {
				foreach ( $wc_attributes as $key => $value ) {
					$attribute_dropdown .= sprintf( '<option value="%s">%s</option>', $key, $value );
				}
			}

			wp_cache_set( 'gg_woo_feed_dropdown_wc_product_attributes', $attribute_dropdown );
		}

		if ( $selected && strpos( $attribute_dropdown, 'value="' . $selected . '"' ) !== false ) {
			$attribute_dropdown = str_replace( 'value="' . $selected . '"', 'value="' . $selected . '"' . ' selected', $attribute_dropdown );
		}

		return $attribute_dropdown;
	}
}

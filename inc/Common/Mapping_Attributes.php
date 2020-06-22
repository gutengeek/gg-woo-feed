<?php
namespace GG_Woo_Feed\Common;

class Mapping_Attributes {
	/**
	 * @var array
	 */
	public $googleXMLAttribute = [
		'id'                         => [ 'g:id', false ],
		'title'                      => [ 'title', true ],
		'description'                => [ 'description', true ],
		'link'                       => [ 'link', true ],
		'mobile_link'                => [ 'mobile_link', true ],
		'product_type'               => [ 'g:product_type', true ],
		'google_taxonomy'            => [ 'g:google_product_category', true ],
		'image'                      => [ 'g:image_link', true ],
		'images'                     => [ 'g:additional_image_link', false ],
		'images_1'                   => [ 'g:additional_image_link_1', true ],
		'images_2'                   => [ 'g:additional_image_link_2', true ],
		'images_3'                   => [ 'g:additional_image_link_3', true ],
		'images_4'                   => [ 'g:additional_image_link_4', true ],
		'images_5'                   => [ 'g:additional_image_link_5', true ],
		'images_6'                   => [ 'g:additional_image_link_6', true ],
		'images_7'                   => [ 'g:additional_image_link_7', true ],
		'images_8'                   => [ 'g:additional_image_link_8', true ],
		'images_9'                   => [ 'g:additional_image_link_9', true ],
		'images_10'                  => [ 'g:additional_image_link_10', true ],
		'condition'                  => [ 'g:condition', false ],
		'availability'               => [ 'g:availability', false ],
		'availability_date'          => [ 'g:availability_date', false ],
		'inventory'                  => [ 'g:inventory', false ],
		'price'                      => [ 'g:price', true ],
		'sale_price'                 => [ 'g:sale_price', true ],
		'sale_price_effective_date'  => [ 'g:sale_price_effective_date', true ],
		'brand'                      => [ 'g:brand', true ],
		'sku'                        => [ 'g:mpn', true ],
		'upc'                        => [ 'g:gtin', true ],
		'identifier_exists'          => [ 'g:identifier_exists', true ],
		'item_group_id'              => [ 'g:item_group_id', false ],
		'color'                      => [ 'g:color', true ],
		'gender'                     => [ 'g:gender', true ],
		'age_group'                  => [ 'g:age_group', true ],
		'material'                   => [ 'g:material', true ],
		'pattern'                    => [ 'g:pattern', true ],
		'size'                       => [ 'g:size', true ],
		'size_type'                  => [ 'g:size_type', true ],
		'size_system'                => [ 'g:size_system', true ],
		'tax'                        => [ 'tax', true ],
		'tax_country'                => [ 'g:tax_country', true ],
		'tax_region'                 => [ 'g:tax_region', true ],
		'tax_rate'                   => [ 'g:tax_rate', true ],
		'tax_ship'                   => [ 'g:tax_ship', true ],
		'tax_category'               => [ 'g:tax_category', true ],
		'weight'                     => [ 'g:shipping_weight', false ],
		'length'                     => [ 'g:shipping_length', false ],
		'width'                      => [ 'g:shipping_width', false ],
		'height'                     => [ 'g:shipping_height', false ],
		'shipping_label'             => [ 'g:shipping_label', false ],
		'shipping_country'           => [ 'g:shipping_country', false ],
		'shipping_service'           => [ 'g:shipping_service', false ],
		'shipping_price'             => [ 'g:shipping_price', false ],
		'shipping_region'            => [ 'g:shipping_region', false ],
		'multipack'                  => [ 'g:multipack', true ],
		'is_bundle'                  => [ 'g:is_bundle', true ],
		'adult'                      => [ 'g:adult', true ],
		'adwords_redirect'           => [ 'g:adwords_redirect', true ],
		'custom_label_0'             => [ 'g:custom_label_0', true ],
		'custom_label_1'             => [ 'g:custom_label_1', true ],
		'custom_label_2'             => [ 'g:custom_label_2', true ],
		'custom_label_3'             => [ 'g:custom_label_3', true ],
		'custom_label_4'             => [ 'g:custom_label_4', true ],
		'excluded_destination'       => [ 'g:excluded_destination', true ],
		'included_destination'       => [ 'g:included_destination', true ],
		'expiration_date'            => [ 'g:expiration_date', true ],
		'unit_pricing_measure'       => [ 'g:unit_pricing_measure', true ],
		'unit_pricing_base_measure'  => [ 'g:unit_pricing_base_measure', true ],
		'installment_months'         => [ 'g:months', true ],
		'installment_amount'         => [ 'g:amount', true ],
		'subscription_period'        => [ 'g:period', true ],
		'subscription_period_length' => [ 'g:period_length', true ],
		'subscription_amount'        => [ 'g:amount', true ],
		'energy_efficiency_class'    => [ 'g:energy_efficiency_class', true ],
		'loyalty_points'             => [ 'g:loyalty_points', true ],
		'installment'                => [ 'g:installment', true ],
		'promotion_id'               => [ 'g:promotion_id', true ],
		'cost_of_goods_sold'         => [ 'g:cost_of_goods_sold', true ],
	];

	/**
	 * @var array
	 */
	public $googleCSVTXTAttribute = [
		'id'                         => [ 'id', false ],
		'title'                      => [ 'title', true ],
		'description'                => [ 'description', true ],
		'link'                       => [ 'link', true ],
		'mobile_link'                => [ 'mobile_link', true ],
		'product_type'               => [ 'product type', true ],
		'google_taxonomy'            => [ 'google product category', true ],
		'image'                      => [ 'image link', true ],
		'images'                     => [ 'additional image link', true ],
		'images_1'                   => [ 'additional image link 1', true ],
		'images_2'                   => [ 'additional image link 2', true ],
		'images_3'                   => [ 'additional image link 3', true ],
		'images_4'                   => [ 'additional image link 4', true ],
		'images_5'                   => [ 'additional image link 5', true ],
		'images_6'                   => [ 'additional image link 6', true ],
		'images_7'                   => [ 'additional image link 7', true ],
		'images_8'                   => [ 'additional image link 8', true ],
		'images_9'                   => [ 'additional image link 9', true ],
		'images_10'                  => [ 'additional image link 10', true ],
		'condition'                  => [ 'condition', false ],
		'availability'               => [ 'availability', false ],
		'availability_date'          => [ 'availability date', false ],
		'inventory'                  => [ 'inventory', false ],
		'price'                      => [ 'price', true ],
		'sale_price'                 => [ 'sale price', true ],
		'sale_price_effective_date'  => [ 'sale price effective date', true ],
		'brand'                      => [ 'brand', true ],
		'sku'                        => [ 'mpn', true ],
		'upc'                        => [ 'gtin', true ],
		'identifier_exists'          => [ 'identifier exists', true ],
		'item_group_id'              => [ 'item group id', false ],
		'color'                      => [ 'color', true ],
		'gender'                     => [ 'gender', true ],
		'age_group'                  => [ 'age group', true ],
		'material'                   => [ 'material', true ],
		'pattern'                    => [ 'pattern', true ],
		'size'                       => [ 'size', true ],
		'size_type'                  => [ 'size type', true ],
		'size_system'                => [ 'size system', true ],
		'tax'                        => [ 'tax', true ],
		'tax_country'                => [ 'tax country', true ],
		'tax_region'                 => [ 'tax region', true ],
		'tax_rate'                   => [ 'tax rate', true ],
		'tax_ship'                   => [ 'tax ship', true ],
		'tax_category'               => [ 'tax category', true ],
		'weight'                     => [ 'shipping weight', false ],
		'length'                     => [ 'shipping length', false ],
		'width'                      => [ 'shipping width', false ],
		'height'                     => [ 'shipping height', false ],
		'shipping_label'             => [ 'shipping label', false ],
		'shipping_country'           => [ 'shipping country', false ],
		'shipping_service'           => [ 'shipping service', false ],
		'shipping_price'             => [ 'shipping price', false ],
		'shipping_region'            => [ 'shipping region', false ],
		'multipack'                  => [ 'multipack', true ],
		'is_bundle'                  => [ 'is bundle', true ],
		'adult'                      => [ 'adult', true ],
		'adwords_redirect'           => [ 'adwords redirect', true ],
		'custom_label_0'             => [ 'custom label 0', true ],
		'custom_label_1'             => [ 'custom label 1', true ],
		'custom_label_2'             => [ 'custom label 2', true ],
		'custom_label_3'             => [ 'custom label 3', true ],
		'custom_label_4'             => [ 'custom label 4', true ],
		'excluded_destination'       => [ 'excluded destination', true ],
		'included_destination'       => [ 'included destination', true ],
		'expiration_date'            => [ 'expiration date', true ],
		'unit_pricing_measure'       => [ 'unit pricing measure', true ],
		'unit_pricing_base_measure'  => [ 'unit pricing base measure', true ],
		'installment_months'         => [ 'months', true ],
		'installment_amount'         => [ 'amount', true ],
		'subscription_period'        => [ 'period', true ],
		'subscription_period_length' => [ 'period_length', true ],
		'subscription_amount'        => [ 'amount', true ],
		'energy_efficiency_class'    => [ 'energy efficiency class', true ],
		'loyalty_points'             => [ 'loyalty points', true ],
		'installment'                => [ 'installment', true ],
		'promotion_id'               => [ 'promotion id', true ],
		'cost_of_goods_sold'         => [ 'cost of goods sold', true ],
	];

	/**
	 * @var array
	 */
	public $facebookXMLAttribute = [
		'id'                        => [ 'g:id', false ],
		'title'                     => [ 'g:title', true ],
		'description'               => [ 'g:description', true ],
		'link'                      => [ 'g:link', true ],
		'mobile_link'               => [ 'g:mobile_link', true ],
		'product_type'              => [ 'g:product_type', true ],
		'google_taxonomy'           => [ 'g:google_product_category', true ],
		'image'                     => [ 'g:image_link', true ],
		'images'                    => [ 'g:additional_image_link', false ],
		'images_1'                  => [ 'g:additional_image_link_1', true ],
		'images_2'                  => [ 'g:additional_image_link_2', true ],
		'images_3'                  => [ 'g:additional_image_link_3', true ],
		'images_4'                  => [ 'g:additional_image_link_4', true ],
		'images_5'                  => [ 'g:additional_image_link_5', true ],
		'images_6'                  => [ 'g:additional_image_link_6', true ],
		'images_7'                  => [ 'g:additional_image_link_7', true ],
		'images_8'                  => [ 'g:additional_image_link_8', true ],
		'images_9'                  => [ 'g:additional_image_link_9', true ],
		'images_10'                 => [ 'g:additional_image_link_10', true ],
		'condition'                 => [ 'g:condition', false ],
		'availability'              => [ 'g:availability', false ],
		'inventory'                 => [ 'g:inventory', false ],
		'override'                  => [ 'g:override', false ],
		'price'                     => [ 'g:price', true ],
		'sale_price'                => [ 'g:sale_price', true ],
		'sale_price_effective_date' => [ 'g:sale_price_effective_date', true ],
		'brand'                     => [ 'g:brand', true ],
		'sku'                       => [ 'g:mpn', true ],
		'upc'                       => [ 'g:gtin', true ],
		'identifier_exists'         => [ 'g:identifier_exists', true ],
		'item_group_id'             => [ 'g:item_group_id', false ],
		'color'                     => [ 'g:color', true ],
		'gender'                    => [ 'g:gender', true ],
		'age_group'                 => [ 'g:age_group', true ],
		'material'                  => [ 'g:material', true ],
		'pattern'                   => [ 'g:pattern', true ],
		'size'                      => [ 'g:size', true ],
		'size_type'                 => [ 'g:size_type', true ],
		'size_system'               => [ 'g:size_system', true ],
		'tax'                       => [ 'tax', true ],
		'weight'                    => [ 'g:shipping_weight', false ],
		'length'                    => [ 'g:shipping_length', false ],
		'width'                     => [ 'g:shipping_width', false ],
		'height'                    => [ 'g:shipping_height', false ],
		'shipping_label'            => [ 'g:shipping_label', false ],
		'shipping_country'          => [ 'g:shipping_country', false ],
		'shipping_service'          => [ 'g:shipping_service', false ],
		'shipping_price'            => [ 'g:shipping_price', false ],
		'shipping_region'           => [ 'g:shipping_region', false ],
		'multipack'                 => [ 'g:multipack', true ],
		'is_bundle'                 => [ 'g:is_bundle', true ],
		'adult'                     => [ 'g:adult', true ],
		'adwords_redirect'          => [ 'g:adwords_redirect', true ],
		'custom_label_0'            => [ 'g:custom_label_0', true ],
		'custom_label_1'            => [ 'g:custom_label_1', true ],
		'custom_label_2'            => [ 'g:custom_label_2', true ],
		'custom_label_3'            => [ 'g:custom_label_3', true ],
		'custom_label_4'            => [ 'g:custom_label_4', true ],
		'excluded_destination'      => [ 'g:excluded_destination', true ],
		'expiration_date'           => [ 'g:expiration_date', true ],
		'unit_pricing_measure'      => [ 'g:unit_pricing_measure', true ],
		'unit_pricing_base_measure' => [ 'g:unit_pricing_base_measure', true ],
		'energy_efficiency_class'   => [ 'g:energy_efficiency_class', true ],
		'loyalty_points'            => [ 'g:loyalty_points', true ],
		'installment'               => [ 'g:installment', true ],
		'promotion_id'              => [ 'g:promotion_id', true ],
		'cost_of_goods_sold'        => [ 'g:cost_of_goods_sold', true ],
		'availability_date'         => [ 'g:availability_date', true ],
		'tax_category'              => [ 'g:tax_category', true ],
		'included_destination'      => [ 'g:included_destination', true ],
	];

	/**
	 * @var array
	 */
	public $facebookCSVTXTAttribute = [
		'id'                        => [ 'id', false ],
		'title'                     => [ 'title', true ],
		'description'               => [ 'description', true ],
		'link'                      => [ 'link', true ],
		'mobile_link'               => [ 'mobile_link', true ],
		'product_type'              => [ 'product type', true ],
		'google_taxonomy'           => [ 'google product category', true ],
		'image'                     => [ 'image link', true ],
		'images'                    => [ 'additional image link', true ],
		'images_1'                  => [ 'additional image link 1', true ],
		'images_2'                  => [ 'additional image link 2', true ],
		'images_3'                  => [ 'additional image link 3', true ],
		'images_4'                  => [ 'additional image link 4', true ],
		'images_5'                  => [ 'additional image link 5', true ],
		'images_6'                  => [ 'additional image link 6', true ],
		'images_7'                  => [ 'additional image link 7', true ],
		'images_8'                  => [ 'additional image link 8', true ],
		'images_9'                  => [ 'additional image link 9', true ],
		'images_10'                 => [ 'additional image link 10', true ],
		'condition'                 => [ 'condition', false ],
		'availability'              => [ 'availability', false ],
		'inventory'                 => [ 'inventory', false ],
		'override'                  => [ 'override', false ],
		'price'                     => [ 'price', true ],
		'sale_price'                => [ 'sale price', true ],
		'sale_price_effective_date' => [ 'sale price effective date', true ],
		'brand'                     => [ 'brand', true ],
		'sku'                       => [ 'mpn', true ],
		'upc'                       => [ 'gtin', true ],
		'identifier_exists'         => [ 'identifier exists', true ],
		'item_group_id'             => [ 'item group id', false ],
		'color'                     => [ 'color', true ],
		'gender'                    => [ 'gender', true ],
		'age_group'                 => [ 'age group', true ],
		'material'                  => [ 'material', true ],
		'pattern'                   => [ 'pattern', true ],
		'size'                      => [ 'size', true ],
		'size_type'                 => [ 'size type', true ],
		'size_system'               => [ 'size system', true ],
		'tax'                       => [ 'tax', true ],
		'weight'                    => [ 'shipping weight', false ],
		'length'                    => [ 'shipping length', false ],
		'width'                     => [ 'shipping width', false ],
		'height'                    => [ 'shipping height', false ],
		'shipping_label'            => [ 'shipping label', false ],
		'shipping_country'          => [ 'shipping country', false ],
		'shipping_service'          => [ 'shipping service', false ],
		'shipping_price'            => [ 'shipping price', false ],
		'shipping_region'           => [ 'shipping region', false ],
		'multipack'                 => [ 'multipack', true ],
		'is_bundle'                 => [ 'is bundle', true ],
		'adult'                     => [ 'adult', true ],
		'adwords_redirect'          => [ 'adwords redirect', true ],
		'custom_label_0'            => [ 'custom label 0', true ],
		'custom_label_1'            => [ 'custom label 1', true ],
		'custom_label_2'            => [ 'custom label 2', true ],
		'custom_label_3'            => [ 'custom label 3', true ],
		'custom_label_4'            => [ 'custom label 4', true ],
		'excluded_destination'      => [ 'excluded destination', true ],
		'expiration_date'           => [ 'expiration date', true ],
		'unit_pricing_measure'      => [ 'unit pricing measure', true ],
		'unit_pricing_base_measure' => [ 'unit pricing base measure', true ],
		'energy_efficiency_class'   => [ 'energy efficiency class', true ],
		'loyalty_points'            => [ 'loyalty points', true ],
		'installment'               => [ 'installment', true ],
		'promotion_id'              => [ 'promotion id', true ],
		'cost_of_goods_sold'        => [ 'cost of goods sold', true ],
		'availability_date'         => [ 'availability date', true ],
		'tax_category'              => [ 'tax category', true ],
		'included_destination'      => [ 'included destination', true ],
	];

	/**
	 * @var array
	 */
	public $pinterestXMLAttribute = [
		'id'                        => [ 'g:id', false ],
		'title'                     => [ 'title', true ],
		'description'               => [ 'description', true ],
		'link'                      => [ 'link', true ],
		'mobile_link'               => [ 'mobile_link', true ],
		'product_type'              => [ 'g:product_type', true ],
		'google_taxonomy'           => [ 'g:google_product_category', true ],
		'image'                     => [ 'g:image_link', true ],
		'images'                    => [ 'g:additional_image_link', false ],
		'images_1'                  => [ 'g:additional_image_link_1', true ],
		'images_2'                  => [ 'g:additional_image_link_2', true ],
		'images_3'                  => [ 'g:additional_image_link_3', true ],
		'images_4'                  => [ 'g:additional_image_link_4', true ],
		'images_5'                  => [ 'g:additional_image_link_5', true ],
		'images_6'                  => [ 'g:additional_image_link_6', true ],
		'images_7'                  => [ 'g:additional_image_link_7', true ],
		'images_8'                  => [ 'g:additional_image_link_8', true ],
		'images_9'                  => [ 'g:additional_image_link_9', true ],
		'images_10'                 => [ 'g:additional_image_link_10', true ],
		'condition'                 => [ 'g:condition', false ],
		'availability'              => [ 'g:availability', false ],
		'availability_date'         => [ 'g:availability_date', false ],
		'inventory'                 => [ 'g:inventory', false ],
		'price'                     => [ 'g:price', true ],
		'sale_price'                => [ 'g:sale_price', true ],
		'sale_price_effective_date' => [ 'g:sale_price_effective_date', true ],
		'brand'                     => [ 'g:brand', true ],
		'sku'                       => [ 'g:mpn', true ],
		'upc'                       => [ 'g:gtin', true ],
		'identifier_exists'         => [ 'g:identifier_exists', true ],
		'item_group_id'             => [ 'g:item_group_id', false ],
		'color'                     => [ 'g:color', true ],
		'gender'                    => [ 'g:gender', true ],
		'age_group'                 => [ 'g:age_group', true ],
		'material'                  => [ 'g:material', true ],
		'pattern'                   => [ 'g:pattern', true ],
		'size'                      => [ 'g:size', true ],
		'size_type'                 => [ 'g:size_type', true ],
		'size_system'               => [ 'g:size_system', true ],
		'tax'                       => [ 'tax', true ],
		'tax_country'               => [ 'g:tax_country', true ],
		'tax_region'                => [ 'g:tax_region', true ],
		'tax_rate'                  => [ 'g:tax_rate', true ],
		'tax_ship'                  => [ 'g:tax_ship', true ],
		'tax_category'              => [ 'g:tax_category', true ],
		'weight'                    => [ 'g:shipping_weight', false ],
		'length'                    => [ 'g:shipping_length', false ],
		'width'                     => [ 'g:shipping_width', false ],
		'height'                    => [ 'g:shipping_height', false ],
		'shipping_label'            => [ 'g:shipping_label', false ],
		'shipping_country'          => [ 'g:shipping_country', false ],
		'shipping_service'          => [ 'g:shipping_service', false ],
		'shipping_price'            => [ 'g:shipping_price', false ],
		'shipping_region'           => [ 'g:shipping_region', false ],
		'multipack'                 => [ 'g:multipack', true ],
		'is_bundle'                 => [ 'g:is_bundle', true ],
		'adult'                     => [ 'g:adult', true ],
		'adwords_redirect'          => [ 'g:adwords_redirect', true ],
		'custom_label_0'            => [ 'g:custom_label_0', true ],
		'custom_label_1'            => [ 'g:custom_label_1', true ],
		'custom_label_2'            => [ 'g:custom_label_2', true ],
		'custom_label_3'            => [ 'g:custom_label_3', true ],
		'custom_label_4'            => [ 'g:custom_label_4', true ],
		'excluded_destination'      => [ 'g:excluded_destination', true ],
		'included_destination'      => [ 'g:included_destination', true ],
		'expiration_date'           => [ 'g:expiration_date', true ],
		'unit_pricing_measure'      => [ 'g:unit_pricing_measure', true ],
		'unit_pricing_base_measure' => [ 'g:unit_pricing_base_measure', true ],
		'energy_efficiency_class'   => [ 'g:energy_efficiency_class', true ],
		'loyalty_points'            => [ 'g:loyalty_points', true ],
		'installment'               => [ 'g:installment', true ],
		'promotion_id'              => [ 'g:promotion_id', true ],
		'cost_of_goods_sold'        => [ 'g:cost_of_goods_sold', true ],
	];

	/**
	 * @var array
	 */
	public $pinterestCSVTXTAttribute = [
		'id'                        => [ 'id', false ],
		'title'                     => [ 'title', true ],
		'description'               => [ 'description', true ],
		'link'                      => [ 'link', true ],
		'mobile_link'               => [ 'mobile_link', true ],
		'product_type'              => [ 'product_type', true ],
		'google_taxonomy'           => [ 'google_product_category', true ],
		'image'                     => [ 'image_link', true ],
		'images'                    => [ 'additional_image_link', true ],
		'images_1'                  => [ 'additional_image_link_1', true ],
		'images_2'                  => [ 'additional_image_link_2', true ],
		'images_3'                  => [ 'additional_image_link_3', true ],
		'images_4'                  => [ 'additional_image_link_4', true ],
		'images_5'                  => [ 'additional_image_link_5', true ],
		'images_6'                  => [ 'additional_image_link_6', true ],
		'images_7'                  => [ 'additional_image_link_7', true ],
		'images_8'                  => [ 'additional_image_link_8', true ],
		'images_9'                  => [ 'additional_image_link_9', true ],
		'images_10'                 => [ 'additional_image_link_10', true ],
		'condition'                 => [ 'condition', false ],
		'availability'              => [ 'availability', false ],
		'availability_date'         => [ 'availability_date', false ],
		'inventory'                 => [ 'inventory', false ],
		'price'                     => [ 'price', true ],
		'sale_price'                => [ 'sale_price', true ],
		'sale_price_effective_date' => [ 'sale_price_effective_date', true ],
		'brand'                     => [ 'brand', true ],
		'sku'                       => [ 'mpn', true ],
		'upc'                       => [ 'gtin', true ],
		'identifier_exists'         => [ 'identifier exists', true ],
		'item_group_id'             => [ 'item_group_id', false ],
		'color'                     => [ 'color', true ],
		'gender'                    => [ 'gender', true ],
		'age_group'                 => [ 'age_group', true ],
		'material'                  => [ 'material', true ],
		'pattern'                   => [ 'pattern', true ],
		'size'                      => [ 'size', true ],
		'size_type'                 => [ 'size_type', true ],
		'size_system'               => [ 'size_system', true ],
		'tax'                       => [ 'tax', true ],
		'tax_country'               => [ 'tax_country', true ],
		'tax_region'                => [ 'tax_region', true ],
		'tax_rate'                  => [ 'tax_rate', true ],
		'tax_ship'                  => [ 'tax_ship', true ],
		'tax_category'              => [ 'tax_category', true ],
		'weight'                    => [ 'shipping_weight', false ],
		'length'                    => [ 'shipping_length', false ],
		'width'                     => [ 'shipping_width', false ],
		'height'                    => [ 'shipping_height', false ],
		'shipping_label'            => [ 'shipping_label', false ],
		'shipping_country'          => [ 'shipping_country', false ],
		'shipping_service'          => [ 'shipping_service', false ],
		'shipping_price'            => [ 'shipping_price', false ],
		'shipping_region'           => [ 'shipping_region', false ],
		'multipack'                 => [ 'multipack', true ],
		'is_bundle'                 => [ 'is_bundle', true ],
		'adult'                     => [ 'adult', true ],
		'adwords_redirect'          => [ 'adwords_redirect', true ],
		'custom_label_0'            => [ 'custom_label_0', true ],
		'custom_label_1'            => [ 'custom_label_1', true ],
		'custom_label_2'            => [ 'custom_label_2', true ],
		'custom_label_3'            => [ 'custom_label_3', true ],
		'custom_label_4'            => [ 'custom_label_4', true ],
		'excluded_destination'      => [ 'excluded_destination', true ],
		'included_destination'      => [ 'included_destination', true ],
		'expiration_date'           => [ 'expiration_date', true ],
		'unit_pricing_measure'      => [ 'unit_pricing_measure', true ],
		'unit_pricing_base_measure' => [ 'unit_pricing_base_measure', true ],
		'energy_efficiency_class'   => [ 'energy_efficiency_class', true ],
		'loyalty_points'            => [ 'loyalty_points', true ],
		'installment'               => [ 'installment', true ],
		'promotion_id'              => [ 'promotion_id', true ],
		'cost_of_goods_sold'        => [ 'cost_of_goods_sold', true ],
	];

	/**
	 * Mapping_Attributes constructor.
	 */
	public function __construct() {

	}

	public function get_google_XML_attributes() {
		return $this->googleXMLAttribute;
	}

	public function get_google_CSV_TXT_attributes() {
		return $this->googleCSVTXTAttribute;
	}

	public function get_facebook_XML_attributes() {
		return $this->facebookXMLAttribute;
	}

	public function get_facebook_CSV_TXT_attributes() {
		return $this->facebookCSVTXTAttribute;
	}

	public function get_pinterest_XML_attributes() {
		return $this->pinterestXMLAttribute;
	}

	public function get_pinterest_CSV_TXT_attributes() {
		return $this->pinterestCSVTXTAttribute;
	}
}

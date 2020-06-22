<?php
namespace GG_Woo_Feed\Admin\Metabox;

use GG_Woo_Feed\Common\Dropdown;
use GG_Woo_Feed\Core\Constant;

class Product_Metabox {
	/**
	 * Prefix.
	 *
	 * @var string
	 */
	protected $prefix = Constant::PRODUCT_META_PREFIX;

	/**
	 * Product_Metabox constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'add_product_tab' ], 99, 1 );
		add_action( 'woocommerce_product_data_panels', [ $this, 'add_product_data_panel' ] );
		add_action( 'woocommerce_process_product_meta', [ $this, 'save_product_data' ] );
	}

	/**
	 * Add product tab.
	 *
	 * @param $tabs
	 * @return array
	 */
	public function add_product_tab( $tabs ) {
		$tabs['gg_woo_feed_tab'] = [
			'label'    => __( 'GG Woo Feed', 'gg-woo-feed' ),
			'target'   => 'gg_woo_feed_product_data',
			'priority' => 90,
		];

		return $tabs;
	}

	/**
	 * Add product data panel.
	 *
	 * @return string
	 */
	public function add_product_data_panel() {
		global $post, $thepostid, $product_object;
		$prefix = $this->prefix;
		?>
        <div id="gg_woo_feed_product_data" class="panel woocommerce_options_panel">
            <div class="options_group">
				<?php
				woocommerce_wp_checkbox( [
					'id'          => $prefix . 'exclude_product',
					'label'       => esc_html__( 'Exclude', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'exclude_product', true ),
					'description' => esc_html__( 'Exclude this product from feeds.', 'gg-woo-feed' ),
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'custom_title',
					'label'       => esc_html__( 'Custom Title', 'gg-woo-feed' ),
					'description' => esc_html__( 'Custom Title', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'custom_title', true ),
					'desc_tip'    => true,
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'custom_description',
					'label'       => esc_html__( 'Custom Description', 'gg-woo-feed' ),
					'description' => esc_html__( 'Custom Description', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'custom_description', true ),
					'desc_tip'    => true,
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'custom_url',
					'label'       => esc_html__( 'Custom URL', 'gg-woo-feed' ),
					'description' => esc_html__( 'Custom URL', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'custom_url', true ),
					'desc_tip'    => true,
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'brand',
					'label'       => esc_html__( 'Brand', 'gg-woo-feed' ),
					'description' => esc_html__( 'Brand', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'brand', true ),
					'desc_tip'    => true,
				] );

				woocommerce_wp_select( [
					'id'          => $prefix . 'condition',
					'label'       => esc_html__( 'Condition', 'gg-woo-feed' ),
					'description' => esc_html__( 'Condition', 'gg-woo-feed' ),
					'placeholder' => '',
					'desc_tip'    => true,
					'options'     => Dropdown::get_google_conditions(),
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'gtin',
					'label'       => esc_html__( 'GTIN', 'gg-woo-feed' ),
					'description' => esc_html__( 'GTIN', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'gtin', true ),
					'desc_tip'    => true,
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'mpn',
					'label'       => esc_html__( 'MPN', 'gg-woo-feed' ),
					'description' => esc_html__( 'MPN', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'mpn', true ),
					'desc_tip'    => true,
				] );

				woocommerce_wp_select( [
					'id'          => $prefix . 'google_taxonomy',
					'label'       => esc_html__( 'Google Taxonomy', 'gg-woo-feed' ),
					'description' => esc_html__( 'Google Taxonomy', 'gg-woo-feed' ),
					'placeholder' => '',
					'desc_tip'    => true,
					'options'     => Dropdown::get_google_taxonomy_options(),
					'class'       => 'gg_woo_feed-google-taxonomy-select',
				] );
				?>
            </div>
            <div class="options_group">
                <h4 style="padding: 1em 1.5em;"><?php esc_html_e( 'Extra fields', 'gg-woo-feed' ); ?></h4>

				<?php
				woocommerce_wp_select( [
					'id'          => $prefix . 'availability',
					'label'       => esc_html__( 'Availability', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324448">More info</a>', 'gg-woo-feed' ),
					'placeholder' => '',
					'options'     => Dropdown::get_availability_options(),
				] );

				woocommerce_wp_select( [
					'id'          => $prefix . 'identifier_exists',
					'label'       => esc_html__( 'Identifier exists', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324478">More info</a>', 'gg-woo-feed' ),
					'placeholder' => '',
					'options'     => [
						''    => esc_html__( 'Select', 'gg-woo-feed' ),
						'yes' => esc_html__( 'Yes', 'gg-woo-feed' ),
						'no'  => esc_html__( 'No', 'gg-woo-feed' ),
					],
				] );

				woocommerce_wp_select( [
					'id'          => $prefix . 'adult',
					'label'       => esc_html__( 'Adult', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324508">More info</a>', 'gg-woo-feed' ),
					'placeholder' => '',
					'options'     => [
						''    => esc_html__( 'Select', 'gg-woo-feed' ),
						'yes' => esc_html__( 'Yes', 'gg-woo-feed' ),
						'no'  => esc_html__( 'No', 'gg-woo-feed' ),
					],
				] );

				woocommerce_wp_select( [
					'id'          => $prefix . 'age_group',
					'label'       => esc_html__( 'Age group', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324463">More info</a>', 'gg-woo-feed' ),
					'placeholder' => '',
					'options'     => Dropdown::get_google_age_group(),
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'multipack',
					'label'       => esc_html__( 'Multipack', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324488">More info</a>', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'multipack', true ),
					'type'        => 'number',
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'color',
					'label'       => esc_html__( 'Color', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324487">More info</a>', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'color', true ),
				] );

				woocommerce_wp_select( [
					'id'          => $prefix . 'gender',
					'label'       => esc_html__( 'Gender', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324479">More info</a>', 'gg-woo-feed' ),
					'placeholder' => '',
					'options'     => Dropdown::get_google_gender(),
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'pattern',
					'label'       => esc_html__( 'Pattern', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324483">More info</a>', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'pattern', true ),
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'size',
					'label'       => esc_html__( 'Size', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324492">More info</a>', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'size', true ),
				] );

				woocommerce_wp_select( [
					'id'          => $prefix . 'size_type',
					'label'       => esc_html__( 'Size type', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324497">More info</a>', 'gg-woo-feed' ),
					'placeholder' => '',
					'options'     => Dropdown::get_google_size_types(),
				] );

				woocommerce_wp_select( [
					'id'          => $prefix . 'size_system',
					'label'       => esc_html__( 'Size system', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324502">More info</a>', 'gg-woo-feed' ),
					'placeholder' => '',
					'options'     => Dropdown::get_google_size_systems(),
				] );
				?>
            </div>
            <div class="options_group">
                <h4 style="padding: 1em 1.5em;"><?php esc_html_e( 'Shipping', 'gg-woo-feed' ); ?>
                    - <a target="_blank" href="https://support.google.com/merchants/answer/6069284"><i><?php esc_html_e( 'More info', 'gg-woo-feed' ); ?></i></a>
                </h4>
				<?php
				woocommerce_wp_text_input( [
					'id'          => $prefix . 'shipping_label',
					'label'       => esc_html__( 'shipping_label', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324504">More info</a>', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'shipping_label', true ),
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'max_handling_time',
					'label'       => esc_html__( 'max_handling_time', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/7388496">More info</a>', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'max_handling_time', true ),
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'min_handling_time',
					'label'       => esc_html__( 'min_handling_time', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/7388496">More info</a>', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'min_handling_time', true ),
				] );

				woocommerce_wp_select( [
					'id'          => $prefix . 'energy_efficiency_class',
					'label'       => esc_html__( 'energy_efficiency_class', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/7562785">More info</a>', 'gg-woo-feed' ),
					'placeholder' => '',
					'options'     => Dropdown::get_google_energy_efficiency_class(),
				] );

				woocommerce_wp_select( [
					'id'          => $prefix . 'max_energy_efficiency_class',
					'label'       => esc_html__( 'max_energy_efficiency_class', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/7562785">More info</a>', 'gg-woo-feed' ),
					'placeholder' => '',
					'options'     => Dropdown::get_google_energy_efficiency_class(),
				] );

				woocommerce_wp_select( [
					'id'          => $prefix . 'min_energy_efficiency_class',
					'label'       => esc_html__( 'min_energy_efficiency_class', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/7562785">More info</a>', 'gg-woo-feed' ),
					'placeholder' => '',
					'options'     => Dropdown::get_google_energy_efficiency_class(),
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'unit_pricing_measure',
					'label'       => esc_html__( 'unit_pricing_measure', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324455">More info</a>', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'unit_pricing_measure', true ),
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'unit_pricing_base_measure',
					'label'       => esc_html__( 'unit_pricing_base_measure', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324490">More info</a>', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'unit_pricing_base_measure', true ),
				] );
				?>
                <h4 style="padding: 1em 1.5em;"><?php esc_html_e( 'Installment', 'gg-woo-feed' ); ?></h4>
				<?php
				woocommerce_wp_text_input( [
					'id'          => $prefix . 'installmentmonths',
					'label'       => esc_html__( 'months', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324474">More info</a>', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'installmentmonths', true ),
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'installmentamount',
					'label'       => esc_html__( 'amount', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/6324474">More info</a>', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'installmentamount', true ),
				] );

				woocommerce_wp_text_input( [
					'id'          => $prefix . 'promotion_id',
					'label'       => esc_html__( 'promotion_id', 'gg-woo-feed' ),
					'description' => __( '<a target="_blank" href="https://support.google.com/merchants/answer/7050148">More info</a>', 'gg-woo-feed' ),
					'value'       => get_post_meta( $post->ID, $prefix . 'promotion_id', true ),
				] );
				?>
            </div>
        </div>
		<?php
	}

	/**
	 * Save product meta data.
	 *
	 * @param int $post_id
	 */
	public function save_product_data( $post_id ) {
		$prefix = $this->prefix;

		$fields = [
			'exclude_product'    => '',
			'custom_title'       => '',
			'custom_description' => '',
			'custom_url'         => '',
			'brand'              => '',
			'condition'          => '',
			'gtin'               => '',
			'mpn'                => '',
			'google_taxonomy'    => '',

			'availability'                => '',
			'identifier_exists'           => '',
			'adult'                       => '',
			'age_group'                   => '',
			'multipack'                   => '',
			'color'                       => '',
			'gender'                      => '',
			'pattern'                     => '',
			'size'                        => '',
			'size_type'                   => '',
			'size_system'                 => '',
			'shipping_label'              => '',
			'max_handling_time'           => '',
			'min_handling_time'           => '',
			'energy_efficiency_class'     => '',
			'max_energy_efficiency_class' => '',
			'min_energy_efficiency_class' => '',
			'unit_pricing_measure'        => '',
			'unit_pricing_base_measure'   => '',
			'installmentmonths'           => '',
			'installmentamount'           => '',
			'promotion_id'                => '',
		];

		$fields = apply_filters( 'gg_woo_feed_product_meta_fields_data', $fields );
		foreach ( $fields as $key => $type ) {
			$key   = $prefix . $key;
			$value = isset( $_POST[ $key ] ) ? $_POST[ $key ] : '';
			switch ( $type ) {
				case 'int' :
					$value = absint( $value );
					break;
				default :
					$value = sanitize_text_field( $value );
			}
			update_post_meta( $post_id, $key, $value );
		}

		do_action( 'gg_woo_feed_product_meta_save_data', $post_id );
	}
}

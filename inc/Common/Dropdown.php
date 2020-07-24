<?php
namespace GG_Woo_Feed\Common;

class Dropdown {
	public $output_types = [];

	public function __construct() {
		$this->output_types = static::output_types();
	}

	/**
	 * @return array
	 */
	public function get_product_stock_statuses() {
		return [
			'all'        => esc_html__( 'All Products', 'gg-woo-feed' ),
			'instock'    => esc_html__( 'Only In Stock', 'gg-woo-feed' ),
			'outofstock' => esc_html__( 'Only Out of Stock', 'gg-woo-feed' ),
		];
	}

	/**
	 * @return array
	 */
	public function get_product_sale_statuses() {
		return [
			'all'     => esc_html__( 'All Products', 'gg-woo-feed' ),
			'sale'    => esc_html__( 'Only Products on Sale', 'gg-woo-feed' ),
			'notsale' => esc_html__( 'Only Products NOT on Sale', 'gg-woo-feed' ),
		];
	}

	public function product_categories_list( $options = [] ) {
		$options = array_merge( [], $options );
		?>
        <p><b><?php esc_html_e( 'Select categories', 'gg-woo-feed' ); ?></b></p>
		<?php
		$terms = null;

		$args = [
			'taxonomy'   => [ 'product_cat' ],
			'hide_empty' => false,
			'meta_query' => [
				'relation' => 'OR',
				[
					'key'     => 'gg_woo_feed-exclude-category',
					'value'   => 'on',
					'compare' => 'NOT LIKE',
				],
				[
					'key'     => 'gg_woo_feed-exclude-category',
					'compare' => 'NOT EXISTS',
				],
			],
			'orderby'    => 'name',
			'order'      => 'ASC',
		];

		$terms = get_terms( $args );

		if ( empty( $options['feed_category'] ) ) {
			$options['feed_category']     = [];
			$options['feed_category_all'] = 'on';
			$options['feed_category'][]   = '0';

			foreach ( $terms as $key => $term ) {
				if ( get_term_meta( $term->slug, 'gg_woo_feed-exclude-category', true ) !== 'on' ) {
					$options['feed_category'][] = $term->slug;
				} else {
					unset( $terms[ $key ] );
				}
			}
		}

		?>
        <div class="gg_woo_feed-categories-list-wrap">
            <ul>
                <li>
                    <input type="checkbox" value="on" name="feed_category_all" id="feed_category_all" class="feed_category" <?php checked( 'on',
						( isset( $options['feed_category_all'] ) ? $options['feed_category_all'] : 'on' ), true ); ?>>
                    <label for="feed_category_all"><?php esc_html_e( 'All Categories', 'gg-woo-feed' ); ?></label>
                </li>
				<?php foreach ( $terms as $key => $term ) {
					$haystacks = isset( $options['feed_category'] ) ? $options['feed_category'] : [];
					$cat_key   = array_search( $term->slug, $haystacks );
					$cat_id    = isset( $haystacks[ $cat_key ] ) ? $haystacks[ $cat_key ] : -1;
					?>
                    <li>
                        <input type="checkbox" value="<?php echo sanitize_text_field( $term->slug ); ?>" name="feed_category[]" id="feed_category_<?php echo $term->slug; ?>"
                               class="feed_category" <?php checked( $term->slug, $cat_id, true ); ?>>
                        <label for="<?php echo 'feed_category_' . $term->slug; ?>"><?php echo $term->name; ?>&nbsp;(<?php echo absint( $term->count ); ?>)</label>
                    </li>
				<?php } ?>
            </ul>
        </div>
        <br>
        <div id="gg_woo_feed-popup-bottom"><a href="#done" class="gg_woo_feed-btn gg_woo_feed-btn-submit gg_woo_feed-popup-done"><?php esc_html_e( 'Done', 'gg-woo-feed' ); ?></a></div>
		<?php
	}

	public static function get_google_conditions() {
		return [
			''            => esc_html__( 'Select', 'gg-woo-feed' ),
			'new'         => esc_html__( 'new', 'gg-woo-feed' ),
			'refurbished' => esc_html__( 'refurbished', 'gg-woo-feed' ),
			'used'        => esc_html__( 'used', 'gg-woo-feed' ),
		];
	}

	public static function get_google_age_group() {
		return [
			''        => esc_html__( 'Select', 'gg-woo-feed' ),
			'newborn' => esc_html__( 'newborn', 'gg-woo-feed' ),
			'infant'  => esc_html__( 'infant', 'gg-woo-feed' ),
			'toddler' => esc_html__( 'toddler', 'gg-woo-feed' ),
			'kids'    => esc_html__( 'kids', 'gg-woo-feed' ),
			'adult'   => esc_html__( 'adult', 'gg-woo-feed' ),
		];
	}

	public static function get_google_gender() {
		return [
			''       => esc_html__( 'Select', 'gg-woo-feed' ),
			'male'   => esc_html__( 'male', 'gg-woo-feed' ),
			'female' => esc_html__( 'female', 'gg-woo-feed' ),
			'unisex' => esc_html__( 'unisex', 'gg-woo-feed' ),
		];
	}

	public static function get_google_size_types() {
		return [
			''             => esc_html__( 'Select', 'gg-woo-feed' ),
			'regular'      => esc_html__( 'regular', 'gg-woo-feed' ),
			'petite'       => esc_html__( 'petite', 'gg-woo-feed' ),
			'plus'         => esc_html__( 'plus', 'gg-woo-feed' ),
			'big and tall' => esc_html__( 'big and tall', 'gg-woo-feed' ),
			'maternity'    => esc_html__( 'maternity', 'gg-woo-feed' ),
		];
	}

	public static function get_google_size_systems() {
		return [
			''    => esc_html__( 'Select', 'gg-woo-feed' ),
			'US'  => esc_html__( 'US', 'gg-woo-feed' ),
			'UK'  => esc_html__( 'UK', 'gg-woo-feed' ),
			'EU'  => esc_html__( 'EU', 'gg-woo-feed' ),
			'DE'  => esc_html__( 'DE', 'gg-woo-feed' ),
			'FR'  => esc_html__( 'FR', 'gg-woo-feed' ),
			'JP'  => esc_html__( 'JP', 'gg-woo-feed' ),
			'CN'  => esc_html__( 'CN', 'gg-woo-feed' ),
			'IT'  => esc_html__( 'IT', 'gg-woo-feed' ),
			'BR'  => esc_html__( 'BR', 'gg-woo-feed' ),
			'MEX' => esc_html__( 'MEX', 'gg-woo-feed' ),
			'AU'  => esc_html__( 'AU', 'gg-woo-feed' ),
		];
	}

	public static function get_google_energy_efficiency_class() {
		return [
			''     => esc_html__( 'Select', 'gg-woo-feed' ),
			'A+++' => esc_html__( 'A+++', 'gg-woo-feed' ),
			'A++'  => esc_html__( 'A++', 'gg-woo-feed' ),
			'A+'   => esc_html__( 'A+', 'gg-woo-feed' ),
			'A'    => esc_html__( 'A', 'gg-woo-feed' ),
			'B'    => esc_html__( 'B', 'gg-woo-feed' ),
			'C'    => esc_html__( 'C', 'gg-woo-feed' ),
			'D'    => esc_html__( 'D', 'gg-woo-feed' ),
			'E'    => esc_html__( 'E', 'gg-woo-feed' ),
			'F'    => esc_html__( 'F', 'gg-woo-feed' ),
			'G'    => esc_html__( 'G', 'gg-woo-feed' ),
		];
	}

	/**
	 * Providers dropdown.
	 *
	 * @param string $selected
	 *
	 * @return string
	 */
	public function get_provider_dropdown( $selected = '' ) {
		$selected  = esc_attr( $selected );
		$providers = Provider_Attributes::get_providers();

		$str = '';
		foreach ( $providers as $key => $value ) {
			if ( '--' == substr( $key, 0, 2 ) ) {
				$str .= "<optgroup label='$value'>";
			} elseif ( '---' == substr( $key, 0, 2 ) ) {
				$str .= '</optgroup>';
			} else {
				$sltd = '';
				if ( $selected == $key ) {
					$sltd = 'selected="selected"';
				}
				$str .= "<option $sltd value='$key'>" . $value . '</option>';
			}
		}

		return $str;
	}

	/**
	 * Get output type dropdown.
	 *
	 * @param int $selected
	 *
	 * @return string
	 */
	public function get_output_types( $selected = 1 ) {
		$output_types = '';
		if ( ! is_array( $selected ) ) {
			$selected = (array) $selected;
		}
		foreach ( $this->output_types as $key => $value ) {
			$output_types .= "<option value=\"{$key}\"" . selected( in_array( $key, $selected ), true, false ) . ">{$value}</option>";
		}

		return $output_types;
	}

	/**
	 * Google taxonomy list
	 *
	 * @param string $selected
	 *
	 * @return string
	 */
	public function get_google_taxonomy_dropdown( $selected = '' ) {
		$file_dir = GGWOOFEED_DIR . 'inc/Helper/google-taxonomy-with-ids.txt';
		$file     = fopen( $file_dir, 'r' );
		$str      = '';
		if ( ! empty( $selected ) ) {
			$selected = trim( $selected );
			if ( ! is_numeric( $selected ) ) {
				$selected = html_entity_decode( $selected );
			} else {
				$selected = (int) $selected;
			}
		}
		if ( $file ) {
			fgets( $file );
			while ( $line = fgets( $file ) ) {
				list( $catId, $cat ) = explode( '-', $line );
				$catId = (int) trim( $catId );
				$cat   = trim( $cat );
				$str   .= sprintf(
					'<option value="%s" %s>%s</option>',
					$catId,
					selected( $selected, is_numeric( $selected ) ? $catId : $cat, false ),
					$cat
				);
			}
		}

		if ( ! empty( $str ) ) {
			$str = '<option></option>' . $str;
		}

		return $str;
	}

	/**
	 * Google taxonomy list options.
	 *
	 * @param bool $select_none
	 * @return array
	 */
	public static function get_google_taxonomy_options( $select_none = true ) {
		$file_dir = GGWOOFEED_DIR . 'inc/Helper/google-taxonomy-with-ids.txt';
		$file     = fopen( $file_dir, 'r' );
		$taxonomy = [];

		if ( $select_none ) {
			$taxonomy[''] = esc_html__( 'Select', 'gg-woo-feed' );
		}
		if ( $file ) {
			fgets( $file );
			while ( $line = fgets( $file ) ) {
				list( $catId, $cat ) = explode( '-', $line );
				$taxonomy[ absint( trim( $catId ) ) ] = trim( $cat );
			}
		}

		$taxonomy = array_filter( $taxonomy );

		return $taxonomy;
	}

	/**
	 * Get availability options.
	 *
	 * @param bool $select_none
	 * @return mixed
	 */
	public static function get_availability_options( $select_none = true ) {
		if ( $select_none ) {
			$taxonomy[''] = esc_html__( 'Select', 'gg-woo-feed' );
		}

		$taxonomy['in stock']            = esc_html__( 'in stock', 'gg-woo-feed' );
		$taxonomy['available for order'] = esc_html__( 'available for order', 'gg-woo-feed' );
		$taxonomy['preorder']            = esc_html__( 'preorder', 'gg-woo-feed' );
		$taxonomy['out of stock']        = esc_html__( 'out of stock', 'gg-woo-feed' );

		return $taxonomy;
	}

	/**
	 * Google attributes dropdown.
	 *
	 * @param string $selected
	 *
	 * @return string
	 */
	public function get_google_attributes_dropdown( $selected = '' ) {
		$g_attributes = Provider_Attributes::get_google_attributes();
		$str          = '<option></option>';
		foreach ( $g_attributes as $key => $value ) {
			if ( substr( $key, 0, 2 ) == '--' ) {
				$str .= "<optgroup label='$value'>";
			} elseif ( substr( $key, 0, 2 ) == '---' ) {
				$str .= '</optgroup>';
			} else {
				$str .= "<option value='$key'>" . $value . '</option>';
			}
		}
		$google_attributes = $str;

		$pos = strpos( $google_attributes, "value='" . $selected . "'" );

		return substr_replace( $google_attributes, "selected='selected' ", $pos, 0 );
	}

	/**
	 * Facebook Attribute list
	 *
	 * @param string $selected
	 *
	 * @return string
	 */
	public function get_facebook_attributes_dropdown( $selected = '' ) {
		return $this->get_google_attributes_dropdown( $selected );
	}

	/**
	 * Pinterest Attribute list
	 *
	 * @param string $selected
	 *
	 * @return string
	 */
	public function get_pinterest_attributes_dropdown( $selected = '' ) {
		return $this->get_google_attributes_dropdown( $selected );
	}

	public static function output_types() {
		return [
			'1'  => __( 'Default', 'gg-woo-feed' ),
			'2'  => __( 'Strip Tags', 'gg-woo-feed' ),
			'3'  => __( 'UTF-8 Encode', 'gg-woo-feed' ),
			'4'  => __( 'htmlentities', 'gg-woo-feed' ),
			'5'  => __( 'Integer', 'gg-woo-feed' ),
			'6'  => __( 'Price', 'gg-woo-feed' ),
			'7'  => __( 'Remove Space', 'gg-woo-feed' ),
			'10' => __( 'Remove ShortCodes', 'gg-woo-feed' ),
			'9'  => __( 'Remove Special Character', 'gg-woo-feed' ),
			'8'  => __( 'CDATA', 'gg-woo-feed' ),
		];
	}

	public static function custom_metaboxes() {
		return [
			'exclude_product'    => [
				'title' => __( 'exclude_product', 'gg-woo-feed' ),
				'type'  => '',
			],
			'custom_title'       => '',
			'custom_description' => '',
			'custom_url'         => '',
			'condition'          => '',
			'gtin'               => '',
			'mpn'                => '',
			'google_taxonomy'    => '',

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
	}

	public static function get_filter_conditions() {
		return [
			'contains'    => esc_html__( 'contains', 'gg-woo-feed' ),
			'containsnot' => esc_html__( 'not contain', 'gg-woo-feed' ),
			'='           => esc_html__( 'equal', 'gg-woo-feed' ),
			'!='          => esc_html__( 'not equal', 'gg-woo-feed' ),
			'>'           => esc_html__( 'greater than', 'gg-woo-feed' ),
			'>='          => esc_html__( 'greater than or equal to', 'gg-woo-feed' ),
			'<'           => esc_html__( 'less than', 'gg-woo-feed' ),
			'<='          => esc_html__( 'less or equal to', 'gg-woo-feed' ),
		];
	}

	public static function get_meta_query_conditions() {
		return [
			'='        => esc_html__( 'equal', 'gg-woo-feed' ),
			'!='       => esc_html__( 'not equal', 'gg-woo-feed' ),
			'>'        => esc_html__( 'greater than', 'gg-woo-feed' ),
			'>='       => esc_html__( 'greater than or equal to', 'gg-woo-feed' ),
			'<'        => esc_html__( 'less than', 'gg-woo-feed' ),
			'<='       => esc_html__( 'less or equal to', 'gg-woo-feed' ),
			'LIKE'     => esc_html__( 'like', 'gg-woo-feed' ),
			'NOT LIKE' => esc_html__( 'not like', 'gg-woo-feed' ),
			'IN'       => esc_html__( 'in', 'gg-woo-feed' ),
			'NOT IN'   => esc_html__( 'not in', 'gg-woo-feed' ),
		];
	}
}

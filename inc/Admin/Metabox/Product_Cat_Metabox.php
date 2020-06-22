<?php
namespace GG_Woo_Feed\Admin\Metabox;

use GG_Woo_Feed\Common\Dropdown;
use GG_Woo_Feed\Core\Constant;

class Product_Cat_Metabox {
	/**
	 * Prefix.
	 *
	 * @var string
	 */
	protected $prefix = Constant::PRODUCT_TAX_PREFIX;

	/**
	 * Product_Cat_Metabox constructor.
	 */
	public function __construct() {
		add_action( 'product_cat_add_form_fields', [ $this, 'add_product_cat_meta' ], 20 );
		add_action( 'product_cat_edit_form_fields', [ $this, 'edit_product_cat_meta' ], 20 );

		add_action( 'created_term', [ $this, 'save_category_fields' ], 10, 3 );
		add_action( 'edit_term', [ $this, 'save_category_fields' ], 10, 3 );
	}

	/**
	 * Add product data panel.
	 *
	 * @return string
	 */
	public function add_product_cat_meta() {
		$google_taxonomy_options = Dropdown::get_google_taxonomy_options();
		$adult_options           = [
			'no'  => esc_html__( 'No', 'gg-woo-feed' ),
			'yes' => esc_html__( 'Yes', 'gg-woo-feed' ),
		];
		?>
        <h2><?php esc_html_e( 'GG Woo Feed', 'gg-woo-feed' ); ?></h2>
        <div class="form-field">
            <label for="<?php echo esc_attr( $this->prefix ); ?>google_taxonomy"><?php esc_html_e( 'Google Taxonomy', 'gg-woo-feed' ); ?></label>
            <select id="<?php echo esc_attr( $this->prefix ); ?>google_taxonomy" name="<?php echo esc_attr( $this->prefix ); ?>google_taxonomy" class="postform">
				<?php foreach ( $google_taxonomy_options as $google_taxonomy_id => $google_taxonomy_name ) : ?>
                    <option value="<?php echo esc_attr( $google_taxonomy_id ); ?>"><?php echo esc_html( $google_taxonomy_name ); ?></option>
				<?php endforeach; ?>
            </select>
        </div>

        <div class="form-field">
            <label for="<?php echo esc_attr( $this->prefix ); ?>mpn"><?php esc_html_e( 'MPN', 'gg-woo-feed' ); ?></label>
            <input type="text" name="<?php echo esc_attr( $this->prefix ); ?>mpn" id="<?php echo esc_attr( $this->prefix ); ?>mpn">
        </div>

        <div class="form-field">
            <label for="<?php echo esc_attr( $this->prefix ); ?>brand"><?php esc_html_e( 'Brand', 'gg-woo-feed' ); ?></label>
            <input type="text" name="<?php echo esc_attr( $this->prefix ); ?>brand" id="<?php echo esc_attr( $this->prefix ); ?>brand">
        </div>

        <div class="form-field">
            <label for="<?php echo esc_attr( $this->prefix ); ?>adult"><?php esc_html_e( 'Adult', 'gg-woo-feed' ); ?></label>
            <select id="<?php echo esc_attr( $this->prefix ); ?>adult" name="<?php echo esc_attr( $this->prefix ); ?>adult" class="postform">
				<?php foreach ( $adult_options as $adult_id => $adult_name ) : ?>
                    <option value="<?php echo esc_attr( $adult_id ); ?>"><?php echo esc_html( $adult_name ); ?></option>
				<?php endforeach; ?>
            </select>
        </div>

        <div class="form-field">
            <label for="<?php echo esc_attr( $this->prefix ); ?>shipping_label"><?php esc_html_e( 'shipping_label', 'gg-woo-feed' ); ?></label>
            <input type="text" name="<?php echo esc_attr( $this->prefix ); ?>shipping_label" id="<?php echo esc_attr( $this->prefix ); ?>shipping_label">
        </div>
		<?php
	}

	/**
	 * Edit product data panel.
	 *
	 * @return string
	 */
	public function edit_product_cat_meta( $term ) {
		$google_tax_current      = get_term_meta( $term->term_id, $this->prefix . 'google_taxonomy', true );
		$google_taxonomy_options = Dropdown::get_google_taxonomy_options();
		$brand                   = get_term_meta( $term->term_id, $this->prefix . 'brand', true );
		$mpn                     = get_term_meta( $term->term_id, $this->prefix . 'mpn', true );
		$adult_current           = get_term_meta( $term->term_id, $this->prefix . 'adult', true );
		$adult_options           = [
			'no'  => esc_html__( 'No', 'gg-woo-feed' ),
			'yes' => esc_html__( 'Yes', 'gg-woo-feed' ),
		];
		$shipping_label          = get_term_meta( $term->term_id, $this->prefix . 'shipping_label', true );
		?>
        <tr>
            <td colspan="2"><h2><?php esc_html_e( 'GG Woo Feed', 'gg-woo-feed' ); ?></h2></td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label><?php esc_html_e( 'Google Taxonomy', 'gg-woo-feed' ); ?></label></th>
            <td>
                <select id="<?php echo esc_attr( $this->prefix ); ?>google_taxonomy" name="<?php echo esc_attr( $this->prefix ); ?>google_taxonomy" class="postform">
					<?php foreach ( $google_taxonomy_options as $google_taxonomy_id => $google_taxonomy_name ) : ?>
                        <option value="<?php echo esc_attr( $google_taxonomy_id ); ?>" <?php selected( $google_taxonomy_id, $google_tax_current ); ?>><?php echo esc_html( $google_taxonomy_name );
							?></option>
					<?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label><?php esc_html_e( 'MPN', 'gg-woo-feed' ); ?></label></th>
            <td>
                <input type="text" name="<?php echo esc_attr( $this->prefix ); ?>mpn" id="<?php echo esc_attr( $this->prefix ); ?>mpn" value="<?php echo esc_attr( $mpn ); ?>">
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label><?php esc_html_e( 'Brand', 'gg-woo-feed' ); ?></label></th>
            <td>
                <input type="text" name="<?php echo esc_attr( $this->prefix ); ?>brand" id="<?php echo esc_attr( $this->prefix ); ?>brand" value="<?php echo esc_attr( $brand ); ?>">
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label><?php esc_html_e( 'Adult', 'gg-woo-feed' ); ?></label></th>
            <td>
                <select id="<?php echo esc_attr( $this->prefix ); ?>adult" name="<?php echo esc_attr( $this->prefix ); ?>adult" class="postform">
					<?php foreach ( $adult_options as $adult_id => $adult_name ) : ?>
                        <option value="<?php echo esc_attr( $adult_id ); ?>" <?php selected( $adult_id, $adult_current ); ?>><?php echo esc_html( $adult_name );
							?></option>
					<?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label><?php esc_html_e( 'shipping_label', 'gg-woo-feed' ); ?></label></th>
            <td>
                <input type="text" name="<?php echo esc_attr( $this->prefix ); ?>shipping_label" id="<?php echo esc_attr( $this->prefix ); ?>shipping_label"
                       value="<?php echo esc_attr( $shipping_label ); ?>">
            </td>
        </tr>
		<?php
	}

	/**
	 * Save category fields
	 *
	 * @param mixed  $term_id  Term ID being saved.
	 * @param mixed  $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( 'product_cat' === $taxonomy ) {
			$terms_meta = [
				'google_taxonomy',
				'brand',
				'mpn',
				'adult',
				'shipping_label',
			];

			foreach ( $terms_meta as $term_meta ) {
				$key = $this->prefix . $term_meta;
				if ( isset( $_POST[ $key ] ) ) { // WPCS: CSRF ok, input var ok.
					update_term_meta( $term_id, $key, esc_attr( $_POST[ $key ] ) ); // WPCS: CSRF ok, sanitization ok, input var ok.
				}
			}
		}
	}
}

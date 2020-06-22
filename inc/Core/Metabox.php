<?php
namespace GG_Woo_Feed\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


use  GG_Woo_Feed\Libraries as Libraries;
use GG_Woo_Feed\Core\Sanitize;

/**
 * Metabox handler.
 *
 * @author WPOPAL
 **/
abstract class Metabox {

	/**
	 * Store the Metabox Id
	 *
	 * @var int $metabox_id
	 */
	public $metabox_id;

	/**
	 * Store Settings Of Fields in Form
	 *
	 * @var array $settings
	 */
	public $settings;

	/**
	 * Store Object Form which is instance of Core/Form
	 *
	 * @var static
	 */
	protected $form;

	/**
	 * Store Object Form which is instance of Core/Form
	 *
	 * @var static
	 */
	protected $mode;

	/**
	 * Store Object Form which is instance of Core/Form
	 *
	 * @var static
	 */
	protected $object_id;

	/**
	 * Store Object Form which is instance of Core/Form
	 *
	 * @var static
	 */
	protected $is_tab;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->metabox_id    = 'gg_woo_feed-metabox-form-data';
		$this->metabox_label = esc_html__( 'Options', 'gg-woo-feed' );

		add_action( 'save_post_gg_woo_feeds', [ $this, 'save' ], 10, 2 );
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	public function get_mode() {
		return $this->mode;
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	public function set_types( $types ) {
		$this->types = $types;
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	public function get_types() {
		return $this->types;
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	public function get_tab() {
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	public function delete() {

	}

	public function set_object_id( $id ) {
		$this->object_id = $id;
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	public function save_fields_data( $type, $post_id ) {
		$update_options = $this->get_needed_update_options();

		if ( $update_options ) {
			foreach ( $update_options as $key => $value ) {
				$this->update_meta( $post_id, $key, $value );
			}
		}
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	public function get_needed_update_options() {
		$form_meta_keys = $this->get_meta_keys_from_settings();

		$update_options = [];
		if ( ! empty( $form_meta_keys ) ) {
			foreach ( $form_meta_keys as $form_meta_key ) {

				if ( ! isset( $_POST[ $form_meta_key ] ) && in_array( $this->get_field_type( $form_meta_key ), [ 'checkbox', 'chosen', 'switch' ] ) ) {
					$_POST[ $form_meta_key ] = 'off';
				}

				$setting_field = $this->get_setting_field( $form_meta_key );

				if ( isset( $_POST[ $form_meta_key ] ) ) {
					$setting_field = $this->get_setting_field( $form_meta_key );

					if ( ! empty( $setting_field['type'] ) ) {

						switch ( $setting_field['type'] ) {
							case 'group':
								$form_meta_value = [];

								foreach ( $_POST[ $form_meta_key ] as $index => $group ) {

									// Do not save template input field values.
									if ( '{{row-count-placeholder}}' === $index ) {
										continue;
									}

									$group_meta_value = [];
									foreach ( $group as $field_id => $field_value ) {
										$field_name                    = $this->get_field_type( $field_id, $form_meta_key );
										$group_meta_value[ $field_id ] = $this->sanitizer( $field_name, $field_value );
									}

									if ( ! empty( $group_meta_value ) ) {
										$form_meta_value[ $index ] = $group_meta_value;
									}
								}

								// Arrange repeater field keys in order.
								$form_meta_value = array_values( $form_meta_value );

								break;
							default:
								$form_meta_value = $this->sanitizer( $setting_field['type'], $_POST[ $form_meta_key ] );
						}// End switch().

						/**
						 * Filter the form meta value before saving
						 */
						$form_meta_value = apply_filters(
							'gg_woo_feed_pre_save_form_meta_value',
							$this->sanitize_form_meta( $form_meta_value, $setting_field ),
							$form_meta_key,
							$this
						);

						$update_options[ $form_meta_key ] = $form_meta_value;

						if ( 'file' === $setting_field['type'] ) {
							$update_options[ $form_meta_key . '_id' ] = $this->sanitizer( 'id', $_POST[ $form_meta_key . '_id' ] );
						}

						if ( 'date' === $setting_field['type'] ) {
							$update_options[ $form_meta_key . '_id' ] = $this->sanitizer( 'date', $_POST[ $form_meta_key . '_id' ] );
						}

						if ( 'taxonomy_select' === $setting_field['type'] && isset( $_POST['post_id'] ) && $_POST['post_id'] ) {
							wp_set_object_terms( absint( $_POST['post_id'] ), (array) $_POST[ $form_meta_key ], sanitize_text_field( $setting_field['taxonomy'] ) );
						}
					}// End if().
				}// End if().
			}// End foreach().
// var_dump($update_options); die;
			return $update_options;
		}// End if().

		return $update_options;
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	public function save_settings_options( $options, $option_name ) {
		$update_options = $this->get_needed_update_options();

		if ( $update_options ) {
			$old_options    = ( $old_options = get_option( $option_name ) ) ? $old_options : [];
			$update_options = array_merge( $old_options, $update_options );
			update_option( $option_name, $update_options, false );

			do_action( 'gg_woo_feed_after_save_settings', $update_options, $old_options );
		}
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	public function save_user_options( $user_id ) {

		$update_options = $this->get_needed_update_options();


		if ( $update_options ) {
			foreach ( $update_options as $key => $value ) {
				update_user_meta( $user_id, $key, $value );
			}
		}
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	public function save_term_options( $term_id ) {

		$update_options = $this->get_needed_update_options();

		if ( $update_options ) {
			foreach ( $update_options as $key => $value ) {
				update_term_meta( $term_id, $key, $value );
			}
		}

	}

	/**
	 * Get all setting field ids.
	 *
	 * @return mixed|void
	 */
	public function sanitizer( $type, $data ) {
		if ( empty( $type ) || empty( $data ) ) {
			return;
		}

		$return    = '';
		$sanitizer = new Sanitize();

		$sanitizer->set_data( $data );
		$sanitizer->set_type( $type );

		$return = $sanitizer->clean();

		unset( $sanitizer );

		return $return;
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	private function update_meta( $id, $meta_key, $meta_value, $prev_value = '' ) {
		$status = update_post_meta( $id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	public function sanitize_form_meta( $meta_value, $setting_field ) {

		return $meta_value;
	}

	/**
	 * Gets setting field.
	 *
	 * @param string $field_id
	 * @param string $group_id
	 * @return array
	 */
	public function get_setting_field( $field_id, $group_id = '' ) {
		$setting_field = [];

		$_field_id = $field_id;
		$field_id  = empty( $group_id ) ? $field_id : $group_id;

		if ( ! empty( $this->settings ) ) {
			foreach ( $this->settings as $setting ) {
				if (
					( $this->has_sub_tab( $setting ) && ( $setting_field = $this->get_sub_field( $setting, $field_id ) ) )
					|| ( $setting_field = $this->get_field( $setting, $field_id ) )
				) {
					break;
				}
			}
		}

		// Get field from group.
		if ( ! empty( $group_id ) ) {
			foreach ( $setting_field['fields'] as $field ) {
				if ( array_key_exists( 'id', $field ) && $field['id'] === $_field_id ) {
					$setting_field = $field;
				}
			}
		}

		return $setting_field;
	}


	/**
	 * Get Field
	 *
	 * @param array  $setting  Settings array.
	 * @param string $field_id Field ID.
	 *
	 * @return array
	 */
	private function get_field( $setting, $field_id ) {
		$setting_field = [];

		if ( ! empty( $setting['fields'] ) ) {
			foreach ( $setting['fields'] as $field ) {
				if ( array_key_exists( 'id', $field ) && $field['id'] === $field_id ) {
					$setting_field = $field;
					break;
				}
			}
		}

		return $setting_field;
	}

	/**
	 * Get field type.
	 *
	 * @param string $field_id Field ID.
	 * @param string $group_id Field Group ID.
	 *
	 * @return string
	 */
	private function get_field_type( $field_id, $group_id = '' ) {
		$field = $this->get_setting_field( $field_id, $group_id );

		$type = array_key_exists( 'type', $field )
			? $field['type']
			: '';

		return $type;
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	public function save( $post_id, $post ) {

	}

	public function save_term( $term_id, $tt_id, $taxonomy = '' ) {
		return $this->save_term_options( $term_id );
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	public function setup() {
		add_meta_box(
			$this->get_metabox_ID(),
			$this->get_metabox_label(),
			[ $this, 'output' ],
			$this->types,
			'normal',
			'high'
		);
		$this->settings = $this->get_settings();
	}

	/**
	 * Get all setting field ids.
	 *
	 * @return array
	 */
	private function get_meta_keys_from_settings() {
		$meta_keys = [];

		$this->settings = $this->get_settings();

		if ( isset( $this->settings[0] ) ) {
			$this->settings = [
				'global' => [ 'fields' => $this->settings ],
			];
		}

		foreach ( $this->settings as $setting ) {
			$meta_key = $this->get_fields_id( $setting );

			if ( $this->has_sub_tab( $setting ) ) {
				$meta_key = array_merge( $meta_key, $this->get_sub_fields_id( $setting ) );
			}

			$meta_keys = array_merge( $meta_keys, $meta_key );
		}

		return $meta_keys;
	}

	/**
	 * Get field ID.
	 *
	 * @param array $field Array of Fields.
	 *
	 * @return string
	 */
	private function get_field_id( $field ) {
		$field_id = '';

		if ( array_key_exists( 'id', $field ) ) {
			$field_id = $field['id'];
		}

		return $field_id;
	}

	/**
	 * Check if setting field has sub tabs/fields
	 *
	 * @param array $field_setting Field Settings.
	 *
	 * @return bool
	 */
	private function has_sub_tab( $field_setting ) {
		$has_sub_tab = false;
		if ( array_key_exists( 'sub-fields', $field_setting ) ) {
			$has_sub_tab = true;
		}

		return $has_sub_tab;
	}

	/**
	 * Get fields ID.
	 *
	 * @param array $setting Array of settings.
	 *
	 * @return array
	 */
	private function get_fields_id( $setting ) {
		$meta_keys = [];

		if (
			! empty( $setting )
			&& array_key_exists( 'fields', $setting )
			&& ! empty( $setting['fields'] )
		) {
			foreach ( $setting['fields'] as $field ) {
				if ( $field_id = $this->get_field_id( $field ) ) {
					$meta_keys[] = $field_id;
				}
			}
		}

		return $meta_keys;
	}

	/**
	 * Get metabox id.
	 *
	 * @return array
	 */
	public function get_settings() {
	}

	/**
	 * Get metabox id.
	 *
	 * @return string
	 */
	private function render_field( $field ) {
		echo $this->form->render_field( $field );
	}

	public function get_before_render() {

	}

	/**
	 * Get metabox id.
	 */
	public function output() {

		$this->get_before_render();

		$form = Libraries\Form\Form::get_instance();

		$form->set_type( $this->mode );
		$form->set_object_id( $this->object_id );
		$args = [];
		echo $form->render( $args, $this->get_settings(), $this->is_tab );
	}

	/**
	 * Get metabox id.
	 *
	 * @return string
	 */
	public function output_tab_indexes( $args=[] ) {

		$this->get_before_render();

		$form = Libraries\Form\Form::get_instance();

		$form->set_type( $this->mode );
		$form->set_object_id( $this->object_id );
		echo $form->render( $args, $this->get_settings(), false );
	}

	/**
	 * Get metabox id.
	 *
	 * @return string
	 */
	public function get_metabox_ID() {
		return $this->metabox_id;
	}

	/**
	 * Get metabox label.
	 *
	 * @return string
	 */
	public function get_metabox_label() {
		return $this->metabox_label;
	}
}

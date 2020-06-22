<?php
/**
 * Define
 * Note: only use for internal purpose.
 *
 * @package     GG_Woo_Feed
 * @since       1.0
 */
namespace GG_Woo_Feed\Libraries\Form\Field;

use GG_Woo_Feed\Libraries\Form\Form;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HTML Form
 *
 * A helper class for outputting common HTML elements, such as product drop downs
 *
 * @package     GG_Woo_Feed
 * @subpackage  GG_Woo_Feed\Libraries
 */
class Taxonomy {
	/**
	 * @var array
	 */
	public $args;

	/**
	 * @var \GG_Woo_Feed\Libraries\Form\Form
	 */
	public $form;

	/**
	 * @var
	 */
	public $type;

	/**
	 * Init Constructor of this
	 *
	 * @return string
	 *
	 */
	public function __construct( $args, Form $form, $type = 'taxonomy_select' ) {
		$classes = [
			'gg_woo_feed-taxonomy-field',
			'gg_woo_feed-select',
			'gg_woo_feed-' . $type,
			'regular-text',
			'form-control',
		];

		$defaults = [
			'id'                => '',
			'taxonomy'          => '',
			'name'              => '',
			'value_type'        => 'slug',
			'query_args'        => [],
			'description'       => null,
			'class'             => esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) ),
			'autocomplete'      => 'off',
			'selected'          => 0,
			'chosen'            => false,
			'placeholder'       => null,
			'multiple'          => false,
			'select_atts'       => false,
			'show_option_all'   => esc_html__( 'All', 'gg-woo-feed' ),
			'show_option_none'  => esc_html__( 'None', 'gg-woo-feed' ),
			'select_all_button' => false,
			'data'              => [],
			'readonly'          => false,
			'disabled'          => false,
			'required'          => '',
			'model'             => '',
			'inline'            => false,
		];

		$args             = wp_parse_args( $args, $defaults );
		$args['selected'] = $form->get_field_value( $args );

		$this->args = $args;
		$this->form = $form;
		$this->type = $type;

		// if ( $args['model'] === 'css' ) {
		// 	$this->render_custom_style( $args['multiple'] );
		// } else {
		// 	$this->render();
		// }

		$this->render();
	}

	private function render_check_item( $args, $term ) {
		$value = $term->{$args['value_type']};

		// if ( $args['multiple'] && is_array( $args['selected'] ) ) {
		// 	$checked = checked( true, in_array( $value, $args['selected'] ), false );
		// } else {
		// 	$checked = checked( $args['selected'], $value, false );
		// }
		$checked = $args['selected'] ? checked( true, in_array( $value, $args['selected'] ), false ) : '';

		$_id = sanitize_key( $this->form->form_id . $args['id'] ) . $term->term_id;

		$output = '<span class="checkbox-item">';
		$output .= '<input type="checkbox" name="' . esc_attr( $args['id'] ) . '[]" id="' . esc_attr( $_id ) . '" value="' . $term->slug . '" class="form-control-checkbox" ' . $checked . ' ' . ' />';
		$output .= '<label class="gg_woo_feed-option-label" for="' . $_id . '">' . esc_html( $term->name ) . '</label>';
		$output .= '</span>';

		return $output;
	}

	private function render_radio_item( $args, $term ) {
		$value = $term->{$args['value_type']};


		// if ( $args['multiple'] && is_array( $args['selected'] ) ) {
		// 	$checked = checked( true, in_array( $value, $args['selected'] ), false );
		// } else {
		// 	$checked = checked( $args['selected'], $value, false );
		// }

		$checked = checked( true, in_array( $value, $args['selected'] ), false );

		$_id = sanitize_key( $this->form->form_id . $args['id'] ) . $term->term_id;

		$output = '<span class="checkbox-item">';
		$output .= '<input type="radio" name="' . esc_attr( $args['id'] ) . '[]" id="' . esc_attr( $_id ) . '" value="' . $term->slug . '" class="form-control-checkbox" ' . $checked . ' ' . ' />';
		$output .= '<label class="gg_woo_feed-option-label" for="' . $_id . '">' . esc_html( $term->name ) . '</label>';
		$output .= '</span>';

		return $output;
	}

	public function render_custom_style( $ismulti = true ) {
		$args = $this->args;

		$all_terms = $this->get_terms();

		if ( ! $all_terms || is_wp_error( $all_terms ) ) {
			echo $this->no_terms_result( $all_terms, 'strong' );

			return;
		}

		if ( $args['chosen'] ) {
			$args['class'] .= ' gg_woo_feed-select-chosen';
		}

		$data = '';
		if ( $args['multiple'] ) {
			$data .= ' multiple="multiple"';
		}

		if ( $args['readonly'] ) {
			$data .= ' readonly';
		}

		if ( 'on' === $args['autocomplete'] ) {
			$data .= ' autocomplete="' . esc_attr( $args['autocomplete'] ) . '"';
		}

		if ( $args['placeholder'] ) {
			$data .= ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '"';
		}

		if ( $args['disabled'] ) {
			$data .= ' disabled="disabled"';
		}

		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $value ) {
				$data .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
			}
		}

		if ( $args['required'] ) {
			$data .= ' required="required" ';
		}

		$output = '<div class="gg_woo_feed-field-wrap gg_woo_feed-taxonomy-select-wrap form-group" id="' . sanitize_key( $this->form->form_id . $args['id'] ) . '-wrap" >';
		if ( $args['show_label'] ) {
			$output .= '<label class="gg_woo_feed-label" for="' . esc_attr( sanitize_key( str_replace( '-', '_', $this->form->form_id . $args['id'] ) ) ) . '">' . esc_html( $args['name'] ) . '</label>';
		}

		$args    = $this->args;
		$options = '';

		if ( $ismulti ) {
			foreach ( $all_terms as $term ) {
				$output .= $this->render_check_item( $args, $term );
			}
		} else {
			foreach ( $all_terms as $term ) {
				$output .= $this->render_radio_item( $args, $term );
			}
		}

		if ( ! empty( $args['description'] ) ) {
			$output .= '<p class="gg_woo_feed-description">' . $args['description'] . '</p>';
		}

		$output .= '</div>';

		echo $output;
	}

	public function render_select_style() {
		echo 'select';
	}

	/**
	 * Render.
	 */
	public function render() {
		if ( 'taxonomy_select' === $this->type ) {
			$this->render_taxonomy_select();
		} elseif ( 'taxonomy_multicheck' === $this->type ) {
			$this->render_taxonomy_multicheck();
		} elseif ( 'taxonomy_radio' === $this->type ) {
			$this->render_taxonomy_radio();
		}
	}

	/**
	 * Render taxonomy select.
	 */
	public function render_taxonomy_select() {
		$args = $this->args;

		$all_terms = $this->get_terms();

		if ( ! $all_terms || is_wp_error( $all_terms ) ) {
			echo $this->no_terms_result( $all_terms, 'strong' );

			return;
		}

		if ( $args['chosen'] ) {
			$args['class'] .= ' gg_woo_feed-select-chosen';
		}

		$data = '';
		if ( $args['multiple'] ) {
			$data .= ' multiple="multiple"';
		}

		if ( $args['readonly'] ) {
			$data .= ' readonly';
		}

		if ( 'on' === $args['autocomplete'] ) {
			$data .= ' autocomplete="' . esc_attr( $args['autocomplete'] ) . '"';
		}

		if ( $args['placeholder'] ) {
			$data .= ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '"';
		}

		if ( $args['disabled'] ) {
			$data .= ' disabled="disabled"';
		}

		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $value ) {
				$data .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
			}
		}

		if ( $args['required'] ) {
			$data .= ' required="required" ';
		}

		$output = '<div class="gg_woo_feed-field-wrap gg_woo_feed-taxonomy-select-wrap form-group" id="' . sanitize_key( $this->form->form_id . $args['id'] ) . '-wrap" >';

		if ( $args['show_label'] ) {
			$output .= '<label class="gg_woo_feed-label" for="' . esc_attr( sanitize_key( str_replace( '-', '_', $this->form->form_id . $args['id'] ) ) ) . '">' . esc_html( $args['name'] ) . '</label>';
		}
		$output .= sprintf(
			'<select id="%1$s" class="%2$s" name="%3$s"  %4$s>',
			sanitize_key( $this->form->form_id . $args['id'] ),
			esc_attr( $args['class'] ),
			$args['multiple'] ? esc_attr( $args['id'] ) . '[]' : esc_attr( $args['id'] ),
			$data
		);

		if ( ! empty( $all_terms ) ) {
			$output .= $this->loop_option_terms( $all_terms );
		}

		$output .= '</select>';

		if ( ! empty( $args['description'] ) ) {
			$output .= '<p class="gg_woo_feed-description">' . esc_html( $args['description'] ) . '</p>';
		}

		$output .= '</div>';

		echo $output;
	}

	public function render_taxonomy_multicheck() {
		$args = $this->args;

		$all_terms = $this->get_terms();

		if ( ! $all_terms || is_wp_error( $all_terms ) ) {
			echo $this->no_terms_result( $all_terms, 'strong' );

			return;
		}

		if ( $args['chosen'] ) {
			$args['class'] .= ' gg_woo_feed-select-chosen';
		}

		$data = '';
		if ( $args['multiple'] ) {
			$data .= ' multiple="multiple"';
		}

		if ( $args['readonly'] ) {
			$data .= ' readonly';
		}

		if ( 'on' === $args['autocomplete'] ) {
			$data .= ' autocomplete="' . esc_attr( $args['autocomplete'] ) . '"';
		}

		if ( $args['placeholder'] ) {
			$data .= ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '"';
		}

		if ( $args['disabled'] ) {
			$data .= ' disabled="disabled"';
		}

		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $value ) {
				$data .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
			}
		}

		if ( $args['required'] ) {
			$data .= ' required="required" ';
		}

		$output = '<div class="gg_woo_feed-field-wrap gg_woo_feed-taxonomy-multicheck-wrap form-group" id="' . sanitize_key( $this->form->form_id . $args['id'] ) . '-wrap" >';
		if ( $args['show_label'] ) {
			$output .= '<label class="gg_woo_feed-label" for="' . esc_attr( sanitize_key( str_replace( '-', '_', $this->form->form_id . $args['id'] ) ) ) . '">' . esc_html( $args['name'] ) . '</label>';
		}

		$args    = $this->args;
		$options = '';

		$display_class = isset( $args['inline'] ) && $args['inline'] ? 'checkbox-inline' : 'checkbox-block';
		$output        .= '<div class="gg_woo_feed-field-main">';

		if ( isset( $args['select_all_button'] ) && $args['select_all_button'] ) {
			$output .= '<div class="gg_woo_feed-multicheck-action"><span class="button-secondary gg_woo_feed-multicheck-toggle">' . esc_html__( 'Select / Deselect All', 'gg-woo-feed' ) . '</span></div>';
		}

		$output .= '<div class="checkbox-list ' . $display_class . '">';

		foreach ( $all_terms as $term ) {
			$output .= $this->render_check_item( $args, $term );
		}

		$output .= '</div>';

		if ( ! empty( $args['description'] ) ) {
			$output .= '<p class="gg_woo_feed-description">' . $args['description'] . '</p>';
		}
		$output .= '</div>';
		$output .= '</div>';

		echo $output;
	}

	public function render_taxonomy_radio() {
		$args = $this->args;

		$all_terms = $this->get_terms();

		if ( ! $all_terms || is_wp_error( $all_terms ) ) {
			echo $this->no_terms_result( $all_terms, 'strong' );

			return;
		}

		if ( $args['chosen'] ) {
			$args['class'] .= ' gg_woo_feed-select-chosen';
		}

		$data = '';
		if ( $args['multiple'] ) {
			$data .= ' multiple="multiple"';
		}

		if ( $args['readonly'] ) {
			$data .= ' readonly';
		}

		if ( 'on' === $args['autocomplete'] ) {
			$data .= ' autocomplete="' . esc_attr( $args['autocomplete'] ) . '"';
		}

		if ( $args['placeholder'] ) {
			$data .= ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '"';
		}

		if ( $args['disabled'] ) {
			$data .= ' disabled="disabled"';
		}

		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $value ) {
				$data .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
			}
		}

		if ( $args['required'] ) {
			$data .= ' required="required" ';
		}

		$output = '<div class="gg_woo_feed-field-wrap gg_woo_feed-taxonomy-radio-wrap form-group" id="' . sanitize_key( $this->form->form_id . $args['id'] ) . '-wrap" >';
		if ( $args['show_label'] ) {
			$output .= '<label class="gg_woo_feed-label" for="' . esc_attr( sanitize_key( str_replace( '-', '_', $this->form->form_id . $args['id'] ) ) ) . '">' . esc_html( $args['name'] ) . '</label>';
		}

		$args    = $this->args;
		$options = '';

		$display_class = isset( $args['inline'] ) && $args['inline'] ? 'radio-inline' : 'radio-block';
		$output        .= '<div class="gg_woo_feed-field-main">';
		$output        .= '<div class="radio-list ' . $display_class . '">';

		foreach ( $all_terms as $term ) {
			$output .= $this->render_radio_item( $args, $term );
		}

		$output .= '</div>';

		if ( ! empty( $args['description'] ) ) {
			$output .= '<p class="gg_woo_feed-description">' . $args['description'] . '</p>';
		}

		$output .= '</div>';
		$output .= '</div>';

		echo $output;
	}

	/**
	 * Wrapper for `get_terms` to account for changes in WP 4.6 where taxonomy is expected
	 * as part of the arguments.
	 *
	 * @return mixed Array of terms on success
	 */
	public function get_terms() {
		$args = [
			'taxonomy'   => $this->args['taxonomy'],
			'hide_empty' => false,
		];

		$args = wp_parse_args( $this->args['query_args'], $args );

		return get_terms( $args );
	}

	protected function no_terms_result( $error, $tag = 'li' ) {
		if ( is_wp_error( $error ) ) {
			$message = $error->get_error_message();
			$data    = 'data-error="' . esc_attr( $error->get_error_code() ) . '"';
		} else {
			$message = esc_html__( 'No terms', 'gg-woo-feed' );
			$data    = '';
		}

		$this->args['select_all_button'] = false;

		return sprintf( '<%3$s><label %1$s>%2$s</label></%3$s>', $data, esc_html( $message ), $tag );
	}

	protected function loop_option_terms( $all_terms ) {
		$args    = $this->args;
		$options = '';

		foreach ( $all_terms as $term ) {
			$value = $term->{$args['value_type']};

			if ( $args['multiple'] && is_array( $args['selected'] ) ) {
				$selected = selected( true, in_array( $value, $args['selected'] ), false );
			} else {
				$selected = selected( $args['selected'], $value, false );
			}

			$options .= sprintf( '<option value="%1$s" %2$s>%3$s</option>',
				$value,
				$selected,
				esc_html( $term->name )
			);
		}

		return $options;
	}
}

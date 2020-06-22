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
class Page {
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
	public function __construct( $args, Form $form, $type = 'page_select' ) {
		$classes = [
			'gg_woo_feed-page-field',
			'gg_woo_feed-select',
			'regular-text',
			'form-control',
		];

		$defaults = [
			'id'               => '',
			'page'         => '',
			'name'             => '',
			'value_type'       => 'slug',
			'query_args'       => [],
			'description'      => null,
			'class'            => esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) ),
			'autocomplete'     => 'off',
			'selected'         => 0,
			'chosen'           => false,
			'placeholder'      => null,
			'multiple'         => false,
			'select_atts'      => false,
			'show_option_all'  => esc_html__( 'All', 'gg-woo-feed' ),
			'show_option_none' => esc_html__( 'None', 'gg-woo-feed' ),
			'data'             => [],
			'readonly'         => false,
			'disabled'         => false,
			'required'         => '',
		];

		$args             = wp_parse_args( $args, $defaults );
		$args['selected'] = $form->get_field_value( $args );

		$this->args = $args;
		$this->form = $form;
		$this->type = $type;

		$this->render();
	}

	/**
	 * Render.
	 */
	public function render() {
		if ( 'page_select' === $this->type ) {
			$this->render_page_select();
		} elseif ( 'page_multicheck' === $this->type ) {
			$this->render_page_multicheck();
		}
	}

	/**
	 * Render page select.
	 */
	public function render_page_select() {
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

		$output = '<div class="gg_woo_feed-field-wrap gg_woo_feed-page-select-wrap form-group" id="' . sanitize_key( $this->form->form_id . $args['id'] ) . '-wrap" >';

		$output .= '<label class="gg_woo_feed-label" for="' . esc_attr( sanitize_key( str_replace( '-', '_', $this->form->form_id . $args['id'] ) ) ) . '">' . esc_html( $args['name'] ) . '</label>';

		$output .= sprintf(
			'<select id="%1$s" class="%2$s" name="%3$s"  %4$s>',
			sanitize_key( $this->form->form_id . $args['id'] ),
			esc_attr( $args['class'] ),
			$args['multiple'] ? esc_attr( $args['id'] ) . '[]' : esc_attr( $args['id'] ),
			$data
		);

		if ( ! empty( $all_terms ) ) {
			$output .= $this->loop_terms( $all_terms );
		}

		$output .= '</select>';

		if ( ! empty( $args['description'] ) ) {
			$output .= '<p class="gg_woo_feed-description">' . esc_html( $args['description'] ) . '</p>';
		}

		$output .= '</div>';

		echo $output;
	}

	/**
	 * Render page multicheck.
	 */
	public function render_page_multicheck() {

	}

	/**
	 * Wrapper for `get_terms` to account for changes in WP 4.6 where page is expected
	 * as part of the arguments.
	 *
	 * @return mixed Array of terms on success
	 */
	public function get_terms() {

		$args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'hierarchical' => 1,
			'exclude' => '',
			'include' => '',
			'meta_key' => '',
			'meta_value' => '',
			'authors' => '',
			'child_of' => 0,
			'parent' => -1,
			'exclude_tree' => '',
			'number' => '',
			'offset' => 0,
			'post_type' => 'page',
			'post_status' => 'publish'
		);

		$pages = get_pages( $args );


		return $pages;
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

	protected function loop_terms( $all_terms ) {
		$args    = $this->args;
		$options = '';

		$selected = is_array($args['selected']) ? selected( true, in_array( "", $args['selected'] ), false ) : false;
		$options .= sprintf( '<option value="%1$s" %2$s>%3$s</option>',
				"",
				$selected,
				esc_html__( '-- Select Page --', 'gg-woo-feed' )
		);

		foreach ( $all_terms as $page ) {
			$value = $page->post_name;

			if ( $args['multiple'] && is_array( $args['selected'] ) ) {
				$selected = selected( true, in_array( $value, $args['selected'] ), false );
			} else {
				$selected = selected( $args['selected'], $value, false );
			}

			$options .= sprintf( '<option value="%1$s" %2$s>%3$s</option>',
				$value,
				$selected,
				esc_html( $page->post_title )
			);
		}

		return $options;
	}
}

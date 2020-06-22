<?php
$classes = [
	'gg_woo_feed-multicheckbox-field',
	'regular-text',
	'form-control',
];

$defaults = [
	'id'                => '',
	'name'              => '',
	'show_label'        => true,
	'options'           => [],
	'description'       => null,
	'class'             => esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) ),
	'select_all_button' => false,
	'inline'            => false,
];

$args  = wp_parse_args( $args, $defaults );
$value = $this->get_field_value( $args );

$output = '<div class="gg_woo_feed-field-wrap gg_woo_feed-multicheckbox-wrap form-group" id="' . sanitize_key( $this->form_id . $args['id'] ) . '-wrap" >';
if ( $args['show_label'] ) {
	$output .= '<label class="gg_woo_feed-label" for="' . esc_attr( sanitize_key( str_replace( '-', '_', $this->form_id . $args['id'] ) ) ) . '">' . esc_html( $args['name'] ) . '</label>';
}

$display_class = isset( $args['inline'] ) && $args['inline'] ? 'checkbox-inline' : 'checkbox-block';
$output        .= '<div class="gg_woo_feed-field-main">';

if ( isset( $args['select_all_button'] ) && $args['select_all_button'] ) {
	$output .= '<div class="gg_woo_feed-multicheck-action"><span class="button-secondary gg_woo_feed-multicheck-toggle">' . esc_html__( 'Select / Deselect All', 'gg-woo-feed' ) . '</span></div>';
}

$output .= '<div class="checkbox-list ' . $display_class . '">';

foreach ( $args['options'] as $key => $option_name ) {
	$checked = ( null !== $value && $value ) ? checked( true, in_array( $key, $value ), false ) : '';

	$_id = sanitize_key( $this->form_id . $args['id'] ) . '_' . $key;

	$output .= '<span class="checkbox-item">';
	$output .= '<input type="checkbox" name="' . esc_attr( $args['id'] ) . '[]" id="' . esc_attr( $_id ) . '" value="' . $key . '" class="form-control-checkbox" ' . $checked . ' ' . ' />';
	$output .= '<label class="gg_woo_feed-option-label" for="' . $_id . '">' . esc_html( $option_name ) . '</label>';
	$output .= '</span>';
}

$output .= '</div>';

if ( ! empty( $args['description'] ) ) {
	$output .= '<p class="gg_woo_feed-description">' . $args['description'] . '</p>';
}
$output .= '</div>';
$output .= '</div>';

echo $output;

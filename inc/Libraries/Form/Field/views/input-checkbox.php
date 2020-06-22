<?php
$defaults = [
	'id'          => '',
	'value'       => 'on',
	'name'        => '',
	'description' => null,
	'class'       => 'gg_woo_feed-checkbox form-control',
	'data'        => [],
	'default'	  => '',
	'disabled'    => false,
];

$args = wp_parse_args( $args, $defaults );

$valued = $this->get_field_value( $args );

if ( null == $valued ) {
	$value = $args['default'] && in_array( $args['default'], [ 'on', 'off' ] ) ? esc_attr( $args['default'] ) : 'off';
} else {
	$value = $valued ? $valued : 'off';
}

$data = '';
if ( $args['disabled'] ) {
	$data .= ' disabled="disabled"';
}

if ( ! empty( $args['data'] ) ) {
	foreach ( $args['data'] as $key => $value ) {
		$data .= ' data-' . $key . '="' . $value . '" ';
	}
}

$output = '<div class="gg_woo_feed-field-wrap gg_woo_feed-checkbox-field-wrap form-group" id="' . sanitize_key( $this->form_id . $args['id'] ) . '-wrap">';
if( $this->show_label ) {
	$output .= '<label class="gg_woo_feed-label" for="' . sanitize_key( $this->form_id . $args['id'] ) . '">' . esc_html( $args['name'] ) . '</label>';
}
$output .= '<input type="checkbox" name="' . esc_attr( $args['id'] ) . '" id="' . esc_attr( $this->form_id . $args['id'] ) . '" value="on" class="' . $args['class'] . '" ' . checked( $value,
		'on', false ) . ' ' . $data . ' />';

if ( ! empty( $args['description'] ) ) {
	$output .= '<p class="gg_woo_feed-description">' . $args['description'] . '</p>';
}

$output .= '</div>';

echo $output;

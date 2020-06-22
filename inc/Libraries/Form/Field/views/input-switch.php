<?php
$defaults = [
	'id'          => '',
	'name'        => '',
	'description' => null,
	'class'       => 'gg_woo_feed-switch form-control',
	'wrap_class'  => '',
	'data'        => [],
	'disabled'    => false,
	'default'     => '',
	'before'      => '',
	'after'       => '',
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
		$data .= 'data-' . $key . '="' . $value . '" ';
	}
}

$output = '';

if ( $args['before'] ) {
	$output .= $args['before'];
}

$wrap_class = isset( $args['wrap_class'] ) && $args['wrap_class'] ? $args['wrap_class'] : '';

$output .= '<div class="gg_woo_feed-field-wrap gg_woo_feed-switch-field-wrap form-group ' . $wrap_class . '" id="' . sanitize_key( $this->form_id . $args['id'] ) . '-wrap">';

if ( $args['name'] ) {
	$output .= '<label class="gg_woo_feed-label" for="' . sanitize_key( $this->form_id . $args['id'] ) . '">' . esc_html( $args['name'] ) . '</label>';
}
$output .= '<div class="gg_woo_feed-field-main">';
$output .= '<label class="gg_woo_feed-switch-input">';

$output .= '<input type="checkbox" name="' . esc_attr( $args['id'] ) . '" id="' . esc_attr( $this->form_id . $args['id'] ) . '" value="on" class="' . $args['class'] . '" ' . checked( $value,
		'on', false ) . ' ' . $data . ' />';
$output .= '<span class="slider round"></span>';

$output .= '</label>';

if ( ! empty( $args['description'] ) ) {
	$output .= '<p class="gg_woo_feed-description">' . $args['description'] . '</p>';
}

$output .= '</div>';
$output .= '</div>';

if ( $args['after'] ) {
	$output .= $args['after'];
}

echo $output;

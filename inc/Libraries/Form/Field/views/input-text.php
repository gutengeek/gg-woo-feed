<?php
$defaults = [
	'id'           => '',
	'name'         => '',
	'type'         => 'text',
	'description'  => null,
	'placeholder'  => '',
	'class'        => 'gg_woo_feed-text regular-text form-control',
	'wrap_class'   => '',
	'disabled'     => false,
	'autocomplete' => 'off',
	'data'         => [],
	'default'      => '',
	'required'     => false,
	'attributes'   => [],
	'before'       => '',
	'after'        => '',
];

$args   = wp_parse_args( $args, $defaults );
$valued = $this->get_field_value( $args );

if ( null == $valued ) {
	$value = $args['default'] ? esc_attr( $args['default'] ) : '';
} else {
	$value = $valued;
}

$value = ! isset( $args['attributes']['value'] ) ? 'value="' . esc_attr( $value ) . '"' : '';

$data = '';

if ( $args['placeholder'] ) {
	$data .= ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';
}

if ( 'on' === $args['autocomplete'] ) {
	$data .= ' autocomplete="' . esc_attr( $args['autocomplete'] ) . '"';
}

if ( ! empty( $args['attributes'] ) ) {
	foreach ( $args['attributes'] as $key => $_value ) {
		$data .= $key . '="' . esc_attr( $_value ) . '" ';
		if ( $key == 'required' ) {
			$args['required'] = true;
		}
	}
}

if ( ! empty( $args['data'] ) ) {
	foreach ( $args['data'] as $key => $_value ) {
		$data .= 'data-' . $key . '="' . esc_attr( $_value ) . '" ';
	}
}

if ( $args['disabled'] ) {
	$data .= ' disabled="disabled"';
}

$required_label = '';
if ( $args['required'] ) {
	$required_label = '<span class="required"> *</span>';
	$data           .= ' required="required" ';
}

$wrap_class = isset( $args['wrap_class'] ) && $args['wrap_class'] ? $args['wrap_class'] : '';
$output     = '';

if ( $args['before'] ) {
	$output .= $args['before'];
}

if ( 'hidden' !== $args['type'] ) {
	$output .= '<div class="gg_woo_feed-field-wrap gg_woo_feed-text-field-wrap form-group ' . $wrap_class . '" id="' . sanitize_key( $this->form_id . $args['id'] ) . '-wrap">';

	if ( $args['name'] && $this->show_label ) {
		$output .= '<label class="gg_woo_feed-label" for="' . sanitize_key( $this->form_id . $args['id'] ) . '">' . esc_html( $args['name'] ) . $required_label . '</label>';
	}

	$output .= '<div class="gg_woo_feed-field-main">';
}


$output .= sprintf( '<input type="%1$s" id="%2$s" class="%3$s" name="%4$s" %5$s %6$s />',
	esc_attr( $args['type'] ),
	esc_attr( $this->form_id . $args['id'] ),
	esc_attr( $args['class'] ),
	esc_attr( $args['id'] ),
	$value,
	$data
);

if ( 'hidden' !== $args['type'] ) {
	if ( ! empty( $args['description'] ) ) {
		$output .= '<p class="gg_woo_feed-description">' . $args['description'] . '</p>';
	}

	$output .= '</div>';
	$output .= '</div>';
}

if ( $args['after'] ) {
	$output .= $args['after'];
}

echo $output;

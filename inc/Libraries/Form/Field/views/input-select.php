<?php
$defaults = [
	'id'               => '',
	'name'             => null,
	'options'          => [],
	'description'      => null,
	'class'            => 'gg_woo_feed-select form-control',
	'wrap_class'       => '',
	'autocomplete'     => 'off',
	'selected'         => 0,
	'chosen'           => false,
	'placeholder'      => null,
	'multiple'         => false,
	'select_atts'      => false,
	'show_option_all'  => esc_html__( 'All', 'gg-woo-feed' ),
	'show_option_none' => esc_html__( 'None', 'gg-woo-feed' ),
	'data'             => [],
	'attributes'       => [],
	'readonly'         => false,
	'disabled'         => false,
	'required'         => '',
	'before'           => '',
	'after'            => '',
];

$args = wp_parse_args( $args, $defaults );

$valued = $this->get_field_value( $args );

if ( null == $valued ) {
	$value = $args['selected'] ? $args['selected'] : '';
} else {
	$value = $valued;
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

if ( $args['required'] ) {
	$data .= ' required="required" ';
}

if ( ! empty( $args['data'] ) ) {
	foreach ( $args['data'] as $key => $value ) {
		$data .= ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
	}
}

if ( ! empty( $args['attributes'] ) ) {
	foreach ( $args['attributes'] as $key => $value ) {
		$data .= $key . '="' . esc_attr( $value ) . '" ';
	}
}

$output = '';

if ( $args['before'] ) {
	$output .= $args['before'];
}

$wrap_class = isset( $args['wrap_class'] ) && $args['wrap_class'] ? $args['wrap_class'] : '';

$output .= '<div class="gg_woo_feed-field-wrap gg_woo_feed-select-field-wrap form-group '. esc_attr( $wrap_class ) .'" id="' . sanitize_key( $this->form_id . $args['id'] ) . '-wrap" >';
if ( $this->show_label ) {
	$output .= '<label class="gg_woo_feed-label" for="' . esc_attr( sanitize_key( str_replace( '-', '_', $this->form_id . $args['id'] ) ) ) . '">' . esc_html( $args['name'] ) . '</label>';
}
$output .= '<div class="gg_woo_feed-field-main">';
$output .= sprintf(
	'<select id="%1$s" class="%2$s" name="%3$s" data-file  %4$s>',
	sanitize_key( $this->form_id . $args['id'] ),
	esc_attr( $args['class'] ),
	$args['multiple'] ? esc_attr( $args['id'] ) . '[]' : esc_attr( $args['id'] ),
	$data
);

if ( $args['show_option_all'] ) {
	if ( $args['multiple'] ) {
		$value    = $value ? $value : [];
		$selected = selected( true, in_array( 0, $value ), false );
	} else {
		$selected = selected( $value, 0, false );
	}
}

if ( ! empty( $args['options'] ) ) {

	if ( $args['show_option_none'] ) {
		if ( $args['multiple'] ) {
			$selected = selected( true, in_array( -1, $value ), false );
		} else {
			$selected = selected( $value, -1, false );
		}
	}

	foreach ( $args['options'] as $key => $option ) {

		if ( $args['multiple'] && is_array( $value ) ) {
			$selected = selected( true, in_array( $key, $value ), false );
		} else {
			$selected = selected( $value, $key, false );
		}

		$output .= '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . esc_html( $option ) . '</option>';
	}
}

$output .= '</select>';

if ( ! empty( $args['description'] ) ) {
	$output .= '<p class="gg_woo_feed-description">' . $args['description'] . '</p>';
}

$output .= '</div>';
$output .= '</div>';

if ( $args['after'] ) {
	$output .= $args['after'];
}

echo $output;

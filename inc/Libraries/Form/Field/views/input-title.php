<?php
$defaults = [
	'id'          => '',
	'name'        => '',
	'wrap_class'  => '',
	'tag'         => 'h3',
	'description' => '',
	'before'      => '',
	'after'       => '',
	'class'       => 'gg_woo_feed-title-field regular-text form-control',
];

$args = wp_parse_args( $args, $defaults );

$output = '';

if ( $args['before'] ) {
	$output .= $args['before'];
}

$wrap_class = isset( $args['wrap_class'] ) && $args['wrap_class'] ? $args['wrap_class'] : '';

$output .= '<div class="gg_woo_feed-field-wrap gg_woo_feed-title-field-wrap form-group ' . esc_attr( $wrap_class ) . '" id="' . sanitize_key( $this->form_id . $args['id'] ) . '-wrap">';

if ( $args['name'] ) {
	$output .= sprintf( '<%2$s class="gg_woo_feed-title-text">%1$s</%2$s>', esc_html( $args['name'] ), $args['tag'] );
}

if ( ! empty( $args['description'] ) ) {
	$output .= '<p class="gg_woo_feed-description">' . $args['description'] . '</p>';
}

$output .= '</div>';

if ( $args['after'] ) {
	$output .= $args['after'];
}

echo $output;

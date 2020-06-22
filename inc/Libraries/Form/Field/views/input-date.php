<?php
$defaults = [
	'id'              => '',
	'hidden_id'       => '',
	'value'           => isset( $args['default'] ) ? $args['default'] : null,
	'id_value'        => '',
	'name'            => '',
	'description'     => null,
	'placeholder'     => '',
	'class'           => 'gg_woo_feed-datepicker regular-text form-control',
	'data'            => false,
	'disabled'        => false,
	'date_format'     => '',
	'date_storage'    => '',
	'data-datepicker' => [],
	'attributes'      => [],
];

$this->add_dependencies( [ 'jquery-ui-core', 'jquery-ui-datepicker' ] );

$args = wp_parse_args( $args, $defaults );

$valued = $this->get_field_value( $args );

if ( null == $valued ) {
	$value = '';
} else {
	$value = $valued;
}

$value = ! isset( $args['attributes']['value'] ) ?  'value="' . esc_attr( $value ) . '"' : '';

$args['hidden_id'] = $args['hidden_id'] ? $args['hidden_id'] : $args['id'] . '_id';
$id_value          = $args['id_value'] ? $args['id_value'] : $this->get_field_value( [ 'id' => $args['hidden_id'] ] );
$id_value          = $id_value ? sanitize_text_field( $id_value ) : '';
$args['id_value']  = $id_value;

$disabled = '';
if ( $args['disabled'] ) {
	$disabled = ' disabled="disabled"';
}

$data = '';
if ( ! empty( $args['data'] ) ) {
	foreach ( $args['data'] as $key => $_value ) {
		$data .= 'data-' . $key . '="' . $_value . '" ';
	}
}

if ( ! empty( $args['attributes'] ) ) {
	foreach ( $args['attributes'] as $key => $_value ) {
		$data .= $key . '="' . esc_attr( $_value ) . '" ';
		if ( $key == 'required' ) {
			$args['required'] = true;
		}
	}
}

$data_datepicker         = '';
$args['data-datepicker'] = wp_parse_args( $args['data-datepicker'], [
	'dateFormat' => $args['date_format'] ? $args['date_format'] : 'mm/dd/yy',
	'altFormat'  => $args['date_storage'] ? $args['date_storage'] : '@',
	'altField'   => '#' . sanitize_key( $args['hidden_id'] ),
] );

if ( ! empty( $args['data-datepicker'] ) ) {
	$data_datepicker = 'data-datepicker=\'' . json_encode( $args['data-datepicker'] ) . '\'';
}

$output = '<div class="gg_woo_feed-field-wrap gg_woo_feed-date-field-wrap form-group" id="' . sanitize_key( $this->form_id . $args['id'] ) . '-wrap">';
if ( $args['show_label'] ) {
	$output .= '<label class="gg_woo_feed-label" for="' . sanitize_key( $this->form_id . $args['id'] ) . '">' . esc_html( $args['name'] ) . '</label>';
}
$output .= '<input type="text" name="' . esc_attr( $args['id'] ) . '" id="' . sanitize_key( $this->form_id . $args['id'] ) . '" ' . $value . ' placeholder="' .
           esc_attr( $args['placeholder'] ) . '" class="' . $args['class'] . '" ' . $data . '' . $disabled . $data_datepicker . '/>';

$output .= '<input type="hidden" name="' . esc_attr( $args['hidden_id'] ) . '" id="' . sanitize_key( $this->form_id . $args['hidden_id'] ) . '" value="' . esc_attr( $args['id_value'] ) . '" />';

if ( ! empty( $args['description'] ) ) {
	$output .= '<p class="gg_woo_feed-description">' . $args['description'] . '</p>';
}

$output .= '</div>';

echo $output;

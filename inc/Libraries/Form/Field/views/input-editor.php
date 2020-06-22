<?php
$defaults = [
	'name'          => '',
	'value'         => isset( $args['default'] ) ? $args['default'] : null,
	'label'         => null,
	'description'   => null,
	'class'         => 'large-text',
	'disabled'      => false,
	'wrapper_class' => '',
	'attributes'    => [ 'class' => '' ],
	'style'         => '',
];

$args = wp_parse_args( $args, $defaults );

$args['value'] = $this->get_field_value( $args );

$args['wrapper_class']   = isset( $args['wrapper_class'] ) ? $args['wrapper_class'] : '';
$args['unique_field_id'] = sanitize_key( $this->form_id . $args['id'] );

$editor_attributes = [
	'textarea_name' => isset( $args['repeatable_field_id'] ) ? $args['repeatable_field_id'] : $args['id'],
	'textarea_rows' => '10',
	'editor_css'    => esc_attr( $args['style'] ),
	'editor_class'  => $args['attributes']['class'],
];
$data_wp_editor    = ' data-wp-editor="' . base64_encode( json_encode( [
		$args['value'],
		$args['unique_field_id'],
		$editor_attributes,
	] ) ) . '"';

$data_wp_editor = isset( $args['repeatable_field_id'] ) ? $data_wp_editor : '';

echo '<div class="gg_woo_feed-field-wrap gg_woo_feed-editor-field-wrap form-group ' . $args['unique_field_id'] . '_field ' . esc_attr( $args['wrapper_class'] ) . '"' . $data_wp_editor . '><label for="' .
     $args['unique_field_id'] .
     '">' . wp_kses_post( $args['name'] ) . '</label>';

wp_editor(
	$args['value'],
	$args['unique_field_id'],
	$editor_attributes
);

if ( ! empty( $args['description'] ) ) {
	echo '<p class="gg_woo_feed-description">' . $args['description'] . '</p>';
}

echo '</div>';

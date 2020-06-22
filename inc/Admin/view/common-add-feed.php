<?php

global $provider, $feed_queries;
?>
<table class="table tree widefat fixed gg_woo_feed-table-template" style="width: 100%;" id="gg_woo_feed-table-template">
    <thead>
    <tr>
        <th></th>
        <th><?php esc_html_e( 'Attributes', 'gg-woo-feed' ); ?></th>
        <th><?php esc_html_e( 'Prefix', 'gg-woo-feed' ); ?></th>
        <th><?php esc_html_e( 'Type', 'gg-woo-feed' ); ?></th>
        <th><?php esc_html_e( 'Value', 'gg-woo-feed' ); ?></th>
        <th><?php esc_html_e( 'Default Value', 'gg-woo-feed' ); ?></th>
        <th><?php esc_html_e( 'Suffix', 'gg-woo-feed' ); ?></th>
        <th><?php esc_html_e( 'Output Type', 'gg-woo-feed' ); ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
	<?php

	foreach ( $feed_queries['mattributes'] as $key => $value ) {
		$prefix           = $feed_queries['prefix'][ $key ];
		$attr_selected    = ( 'attribute' === $feed_queries['type'][ $key ] ) ? 'selected' : '';
		$pattern_selected = ( 'pattern' === $feed_queries['type'][ $key ] ) ? 'selected' : '';
		$attribute        = $feed_queries['attributes'][ $key ];
		$pattern          = $feed_queries['default'][ $key ];
		$default_value    = $feed_queries['default_value'][ $key ];
		$suffix           = $feed_queries['suffix'][ $key ];
		$output_type      = $feed_queries['output_type'][ $key ];
		?>
        <tr>
            <td><i class="gg_woo_feed-sort dashicons dashicons-move"></i></td>
            <td>
                <input type="text" name="mattributes[]" autocomplete="off" required="required" value="<?php print esc_attr( $value ); ?>" class="gg_woo_feed-validate-attr gg_woo_feed-map-attributes">
            </td>
            <td>
                <input type="text" name="prefix[]" autocomplete="off" value="<?php print esc_attr( $prefix ); ?>" class="gg_woo_feed-input">
            </td>
            <td>
                <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                    <option <?php print esc_attr( $attr_selected ); ?> value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                    <option <?php print esc_attr( $pattern_selected ); ?> value="pattern"><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
                </select>
            </td>
            <td>
                <select <?php print ( 'selected' != $attr_selected ) ? "style='display:none;'" : ''; ?> name="attributes[]" required="required"
                                                                                                        class="gg_woo_feed-validate-attr gg_woo_feed-attr-val gg_woo_feed-attributes">
					<?php print gg_woo_feed_get_product_attribute_dropdown( $attribute ); ?>
                </select>
                <input value="<?php print esc_attr( $pattern ); ?>" type="text" name="default[]" autocomplete="off"
                       class="gg_woo_feed-default-val gg_woo_feed-attributes" <?php print ( 'selected' != $pattern_selected ) ? "style='display:none;'" : ''; ?>">
            </td>
            <td class="gg_woo_feed-default-value-td">
                <input type="text" name="default_value[]" autocomplete="off" value="<?php print esc_attr( $default_value ); ?>" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
            </td>
            <td>
                <input type="text" name="suffix[]" autocomplete="off" value="<?php print esc_attr( $suffix ); ?>" class="gg_woo_feed-input">
            </td>
            <td>
                <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
					<?php print $dropdown->get_output_types( $output_type ); ?>
                </select>
            </td>
            <td>
                <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
            </td>
        </tr>
		<?php
	}
	?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3">
            <button type="button" class="gg_woo_feed-btn gg_woo_feed-btn-primary" id="gg_woo_feed-add-new-row">
                <span class="dashicons dashicons-plus"></span><?php esc_html_e( 'Add New Row', 'gg-woo-feed' ); ?>
            </button>
        </td>
        <td colspan="6"></td>
    </tr>
    </tfoot>
</table>

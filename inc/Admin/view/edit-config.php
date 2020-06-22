<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

$dropdown = new \GG_Woo_Feed\Common\Dropdown();
?>
<table class="table tree widefat fixed sorted_table gg_woo_feed-table-template" style="width: 100%" id="gg_woo_feed-table-template">
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
	if ( isset( $feed_queries['mattributes'] ) && count( $feed_queries['mattributes'] ) > 0 ) {
		$mAttributes    = $feed_queries['mattributes'];
		$opalAttributes = $feed_queries['attributes'];
		$type           = $feed_queries['type'];
		$default        = $feed_queries['default'];
		$default_value  = $feed_queries['default_value'];
		$prefix         = $feed_queries['prefix'];
		$suffix         = $feed_queries['suffix'];
		$output_type    = $feed_queries['output_type'];
		$counter        = 0;
		foreach ( $mAttributes as $merchant => $mAttribute ) {
			?>
            <tr>
                <td><i class="gg_woo_feed-sort dashicons dashicons-move"></i></td>
                <td>
					<?php if ( method_exists( $dropdown, 'get_' . $feed_queries['provider'] . '_attributes_dropdown' ) ) { ?>
                        <select name="mattributes[]" class="gg_woo_feed-map-attributes">
							<?php print $dropdown->{'get_' . $feed_queries['provider'] . '_attributes_dropdown'}( esc_attr( $mAttribute ) ); ?>
                        </select>
					<?php } else { ?>
                        <input type="text" name="mattributes[]" value="<?php echo esc_attr( $mAttribute ); ?>" required="required" class="gg_woo_feed-map-attributes">
					<?php } ?>
                </td>
                <td>
                    <input type="text" name="prefix[]" value="<?php echo esc_attr( stripslashes( $prefix[ $merchant ] ) ); ?>" autocomplete="off" class="gg_woo_feed-input"/>
                </td>
                <td>
                    <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                        <option <?php echo ( 'attribute' == $type[ $merchant ] ) ? 'selected="selected" ' : ''; ?>value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                        <option <?php echo ( 'pattern' == $type[ $merchant ] ) ? 'selected="selected" ' : ''; ?> value="pattern"><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
                    </select>
                </td>
                <td>
                    <select <?php echo ( 'attribute' == $type[ $merchant ] ) ? '' : 'style=" display: none;" '; ?>name="attributes[]" class="gg_woo_feed-attr-val gg_woo_feed-attributes">
						<?php print gg_woo_feed_get_product_attribute_dropdown( esc_attr( $opalAttributes[ $merchant ] ) ); ?>
                    </select>
					<?php if ( in_array( $feed_queries['provider'], [ 'google', 'facebook', 'pinterest' ] ) && 'google_taxonomy' == $mAttribute ) { ?>
                        <span <?php echo ( 'pattern' == $type[ $merchant ] ) ? '' : 'style=" display: none;" '; ?>class="gg_woo_feed-default-val gg_woo_feed-attributes">
							<select name="default[]" class="gg_woo_feed-google-taxonomy-select">
								<?php echo $dropdown->get_google_taxonomy_dropdown( esc_attr( $default[ $merchant ] ) ); ?>
							</select>
						</span>
					<?php } else { ?>
                        <input <?php echo ( 'pattern' == $type[ $merchant ] ) ? '' : 'style=" display: none;"'; ?>autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes " type="text"
                               name="default[]"
                               value="<?php echo esc_attr( $default[ $merchant ] ); ?>"/>
					<?php } ?>
                </td>
                <td class="gg_woo_feed-default-value-td">
                    <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes"
                           value="<?php echo esc_attr( $default_value[ $merchant ] ); ?>">
                </td>
                <td>
                    <input type="text" name="suffix[]" value="<?php echo esc_attr( stripslashes( $suffix[ $merchant ] ) ); ?>" autocomplete="off" class="gg_woo_feed-input"/>
                </td>
                <td class="gg_woo_feed-output-type-td">
                    <select name="output_type[<?php echo esc_attr( $counter ); ?>][]" class="output_type gg_woo_feed-not-empty">
						<?php
						foreach ( gg_woo_feed_get_field_output_type_options() as $key => $option ) {
							if ( isset( $output_type[ $counter ] ) ) {
								if ( is_array( $output_type[ $counter ] ) ) {
									$selected = in_array( $key, $output_type[ $counter ] );
								} else {
									$selected = $output_type[ $counter ] == $key;
								}
							} else {
								$selected = '1' == $key;
							}
							printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $selected, true, false ), esc_html( $option ) );
						}
						?>
                    </select>
                </td>
                <td>
                    <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
                </td>
            </tr>
			<?php
			$counter++;
		}
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

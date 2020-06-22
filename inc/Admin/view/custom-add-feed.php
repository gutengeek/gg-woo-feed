<?php

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
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <input type="text" name="mattributes[]" autocomplete="off" required class="gg_woo_feed-validate-attr gg_woo_feed-map-attributes">
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php esc_html_e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern"><?php esc_html_e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
            <select name="attributes[]" required="required" class="gg_woo_feed-validate-attr gg_woo_feed-attr-val gg_woo_feed-attributes">
				<?php print gg_woo_feed_get_product_attribute_dropdown(); ?>
            </select>
            <input type="text" name="default[]" autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes" style=" display: none;">
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php print $dropdown->get_output_types(); ?>
            </select>
        </td>

        <td>
            <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
        </td>
    </tr>
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

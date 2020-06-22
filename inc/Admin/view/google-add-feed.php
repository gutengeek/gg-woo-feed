<?php
/**
 * Google Template
 */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
global $provider, $dropdown;
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
        <th class="gg_woo_feed-output-type-th"><?php _e( 'Output Type', 'gg-woo-feed' ); ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <select name="mattributes[]" required class="gg_woo_feed-map-attributes">
				<?php print $dropdown->get_google_attributes_dropdown( 'id' ); ?>
            </select>
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern"><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
            <select name="attributes[]" class="gg_woo_feed-attr-val gg_woo_feed-attributes">
				<?php print gg_woo_feed_get_product_attribute_dropdown( 'id' ); ?>
            </select>
            <input type="text" name="default[]" autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes" style=" display: none;">
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td class="gg_woo_feed-output-type-td">
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php print $dropdown->get_output_types(); ?>
            </select>
        </td>

        <td>
            <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
        </td>
    </tr>
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <select name="mattributes[]" required class="gg_woo_feed-map-attributes">
				<?php print $dropdown->get_google_attributes_dropdown( 'title' ); ?>
            </select>
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern"><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
            <select name="attributes[]" class="gg_woo_feed-attr-val gg_woo_feed-attributes">
				<?php print gg_woo_feed_get_product_attribute_dropdown( 'title' ); ?>
            </select>
            <input type="text" name="default[]" autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes" style=" display: none;">
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td class="gg_woo_feed-output-type-td">
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php print $dropdown->get_output_types(); ?>
            </select>
        </td>

        <td>
            <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
        </td>
    </tr>
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <select name="mattributes[]" required class="gg_woo_feed-map-attributes">
				<?php print $dropdown->get_google_attributes_dropdown( 'description' ); ?>
            </select>
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern"><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
            <select name="attributes[]" class="gg_woo_feed-attr-val gg_woo_feed-attributes">
				<?php print gg_woo_feed_get_product_attribute_dropdown( 'description' ); ?>
            </select>
            <input type="text" name="default[]" autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes" style=" display: none;">
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td class="gg_woo_feed-output-type-td">
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php print $dropdown->get_output_types(); ?>
            </select>
        </td>

        <td>
            <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
        </td>
    </tr>
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <select name="mattributes[]" required class="gg_woo_feed-map-attributes">
				<?php print $dropdown->get_google_attributes_dropdown( 'item_group_id' ); ?>
            </select>
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern"><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
            <select name="attributes[]" class="gg_woo_feed-attr-val gg_woo_feed-attributes">
				<?php print gg_woo_feed_get_product_attribute_dropdown( 'item_group_id' ); ?>
            </select>
            <input type="text" name="default[]" autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes" style=" display: none;">
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td class="gg_woo_feed-output-type-td">
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php print $dropdown->get_output_types(); ?>
            </select>
        </td>

        <td>
            <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
        </td>
    </tr>
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <select name="mattributes[]" required class="gg_woo_feed-map-attributes">
				<?php print $dropdown->get_google_attributes_dropdown( 'link' ); ?>
            </select>
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern"><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
            <select name="attributes[]" class="gg_woo_feed-attr-val gg_woo_feed-attributes">
				<?php print gg_woo_feed_get_product_attribute_dropdown( 'link' ); ?>
            </select>
            <input type="text" name="default[]" autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes" style=" display: none;">
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td class="gg_woo_feed-output-type-td">
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php
				print $dropdown->get_output_types();
				?>
            </select>
        </td>

        <td>
            <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
        </td>
    </tr>
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <select name="mattributes[]" required class="gg_woo_feed-map-attributes">
				<?php print $dropdown->get_google_attributes_dropdown( 'product_type' ); ?>
            </select>
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern"><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
            <select name="attributes[]" class="gg_woo_feed-attr-val gg_woo_feed-attributes">
				<?php print gg_woo_feed_get_product_attribute_dropdown( 'product_type' ); ?>
            </select>
            <input type="text" name="default[]" autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes" style=" display: none;">
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td class="gg_woo_feed-output-type-td">
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php print $dropdown->get_output_types(); ?>
            </select>
        </td>

        <td>
            <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
        </td>
    </tr>
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <select name="mattributes[]" required class="gg_woo_feed-map-attributes">
				<?php print $dropdown->get_google_attributes_dropdown( 'google_taxonomy' ); ?>
            </select>
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern" selected><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
			<span class="gg_woo_feed-default-val gg_woo_feed-attributes">
				<select name="default[]" class="gg_woo_feed-google-taxonomy-select">
					<?php print $dropdown->get_google_taxonomy_dropdown(); ?>
                </select>
			</span>
            <select name="attributes[]" class="gg_woo_feed-attr-val gg_woo_feed-attributes" style="display:none;">
				<?php print gg_woo_feed_get_product_attribute_dropdown( '' ); ?>
            </select>
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td class="gg_woo_feed-output-type-td">
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php print $dropdown->get_output_types(); ?>
            </select>
        </td>

        <td>
            <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
        </td>
    </tr>
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <select name="mattributes[]" required class="gg_woo_feed-map-attributes">
				<?php print $dropdown->get_google_attributes_dropdown( 'image' ); ?>
            </select>
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern"><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
            <select name="attributes[]" class="gg_woo_feed-attr-val gg_woo_feed-attributes">
				<?php print gg_woo_feed_get_product_attribute_dropdown( 'image' ); ?>
            </select>
            <input type="text" name="default[]" autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes" style=" display: none;">
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td class="gg_woo_feed-output-type-td">
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php print $dropdown->get_output_types(); ?>
            </select>
        </td>

        <td>
            <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
        </td>
    </tr>
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <select name="mattributes[]" required class="gg_woo_feed-map-attributes">
				<?php print $dropdown->get_google_attributes_dropdown( 'condition' ); ?>
            </select>
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern"><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
            <select name="attributes[]" class="gg_woo_feed-attr-val gg_woo_feed-attributes">
				<?php print gg_woo_feed_get_product_attribute_dropdown( 'condition' ); ?>
            </select>
            <input type="text" style=" display: none;" name="default[]" autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes"
            />
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td class="gg_woo_feed-output-type-td">
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php print $dropdown->get_output_types(); ?>
            </select>
        </td>

        <td>
            <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
        </td>
    </tr>
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <select name="mattributes[]" required class="gg_woo_feed-map-attributes">
				<?php print $dropdown->get_google_attributes_dropdown( 'availability' ); ?>
            </select>
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern"><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
            <select name="attributes[]" class="gg_woo_feed-attr-val gg_woo_feed-attributes">
				<?php print gg_woo_feed_get_product_attribute_dropdown( 'availability' ); ?>
            </select>
            <input type="text" name="default[]" autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes" style=" display: none;">
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td class="gg_woo_feed-output-type-td">
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php print $dropdown->get_output_types(); ?>
            </select>
        </td>

        <td>
            <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
        </td>
    </tr>
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <select name="mattributes[]" required class="gg_woo_feed-map-attributes">
				<?php print $dropdown->get_google_attributes_dropdown( 'price' ); ?>
            </select>
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern"><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
            <select name="attributes[]" class="gg_woo_feed-attr-val gg_woo_feed-attributes">
				<?php print gg_woo_feed_get_product_attribute_dropdown( 'price' ); ?>
            </select>
            <input type="text" name="default[]" autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes" style=" display: none;">
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" value="<?php print esc_attr( get_woocommerce_currency() ); ?>" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td class="gg_woo_feed-output-type-td">
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php print $dropdown->get_output_types( 6 ); ?>
            </select>
        </td>

        <td>
            <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
        </td>
    </tr>
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <select name="mattributes[]" required class="gg_woo_feed-map-attributes">
				<?php print $dropdown->get_google_attributes_dropdown( 'sku' ); ?>
            </select>
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern"><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
            <select name="attributes[]" class="gg_woo_feed-attr-val gg_woo_feed-attributes">
				<?php print gg_woo_feed_get_product_attribute_dropdown( 'sku' ); ?>
            </select>
            <input type="text" name="default[]" autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes" style=" display: none;">
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td class="gg_woo_feed-output-type-td">
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php print $dropdown->get_output_types(); ?>
            </select>
        </td>

        <td>
            <i class="gg_woo_feed-del-row dashicons dashicons-no-alt"></i>
        </td>
    </tr>
    <tr>
        <td>
            <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
        </td>
        <td>
            <select name="mattributes[]" required class="gg_woo_feed-map-attributes">
				<?php print $dropdown->get_google_attributes_dropdown( 'brand' ); ?>
            </select>
        </td>
        <td>
            <input type="text" name="prefix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td>
            <select name="type[]" class="attr_type gg_woo_feed-not-empty">
                <option value="attribute"><?php _e( 'Attribute', 'gg-woo-feed' ); ?></option>
                <option value="pattern" selected><?php _e( 'Pattern', 'gg-woo-feed' ); ?></option>
            </select>
        </td>
        <td>
            <select name="attributes[]" style=" display: none;" class="gg_woo_feed-attr-val gg_woo_feed-attributes">
				<?php
				print gg_woo_feed_get_product_attribute_dropdown();
				?>
            </select>
            <input type="text" name="default[]" value="<?php print esc_attr( gg_woo_feed_get_default_brand() ); ?>" autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes">
        </td>
        <td class="gg_woo_feed-default-value-td">
            <input type="text" name="default_value[]" autocomplete="off" class="gg_woo_feed-default-value-val gg_woo_feed-attributes">
        </td>
        <td>
            <input type="text" name="suffix[]" autocomplete="off" class="gg_woo_feed-input">
        </td>
        <td class="gg_woo_feed-output-type-td">
            <select name="output_type[][]" class="output_type gg_woo_feed-not-empty">
				<?php
				print $dropdown->get_output_types();
				?>
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

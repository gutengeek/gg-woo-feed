<?php
/**
 * Filter by attributes.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

$has_condition_attributes = isset( $feed_queries['filter_by_attributes_atts'] ) && count( $feed_queries['filter_by_attributes_atts'] ) > 0;
?>
<div class="gg_woo_feed-field-wrap gg_woo_feed-switch-field-wrap form-group " id="gg_woo_feed-form-filter_by_attributes-wrap">
    <div class="gg_woo_feed-field-main">
        <label class="gg_woo_feed-enable-switch-input">
            <input type="checkbox" name="filter_by_attributes" id="gg_woo_feed-form-filter_by_attributes" value="on"
                   class="gg_woo_feed-switch form-control" <?php checked( 'on',
				( isset( $feed_queries['filter_by_attributes'] ) ? $feed_queries['filter_by_attributes'] : 'off' ), true ); ?>>
            <span class="slider round"></span>
        </label>
        <label class="gg_woo_feed-enable-switch-label" for="gg_woo_feed-form-filter_by_attributes"><?php esc_html_e( 'Filter by product attributes',
				'gg-woo-feed' ); ?></label>
    </div>
</div>
<p class="gg_woo_feed-description"><?php esc_html_e( 'The filter by product attributes feature will take variation products only.', 'gg-woo-feed' ); ?></p>
<div class="filter_by_attributes_section">
    <div class="gg_woo_feed-field-wrap gg_woo_feed-select-field-wrap form-group " id="gg_woo_feed-form-filter_attribute_relationship-wrap">
        <label class="gg_woo_feed-label" for="gg_woo_feed-form-filter_attribute_relationship" style="width: 15%"><?php esc_html_e( 'Relationship Conditions', 'gg-woo-feed' ); ?></label>
        <div class="gg_woo_feed-field-main">
			<?php
			$relationship_attribute_options = [
				'and' => esc_html__( 'AND', 'gg-woo-feed' ),
				'or'  => esc_html__( 'OR', 'gg-woo-feed' ),
			];
			?>
            <select name="filter_attribute_relationship" id="gg_woo_feed-form-filter_attribute_relationship" style="width: 200px">
				<?php foreach ( $relationship_attribute_options as $relationship_attribute => $relationship_attribute_label ) : ?>
                    <option <?php selected( $relationship_attribute, isset( $feed_queries['filter_attribute_relationship'] ) ? $feed_queries['filter_attribute_relationship'] : 'and', true ); ?>
                            value="<?php echo esc_attr( trim( $relationship_attribute ) ); ?>">
						<?php echo esc_html( $relationship_attribute_label ); ?>
                    </option>
				<?php endforeach; ?>
            </select>
            <p class="gg_woo_feed-description"><?php esc_html_e( 'Select a relationship for conditions.', 'gg-woo-feed' ); ?></p>
        </div>
    </div>

    <table class="table tree widefat fixed gg_woo_feed-table-filter-attributes" style="width: 80%;" id="gg_woo_feed-table-filter-attributes">
        <thead>
        <tr>
            <th></th>
            <th><?php esc_html_e( 'Product Attributes', 'gg-woo-feed' ); ?></th>
            <th><?php esc_html_e( 'Condition', 'gg-woo-feed' ); ?></th>
            <th><?php esc_html_e( 'Value', 'gg-woo-feed' ); ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
		<?php
		if ( $has_condition_attributes ) :
			$filter_by_attributes_atts = $feed_queries['filter_by_attributes_atts'];
			$conditions_attributes = $feed_queries['conditions_attributes'];
			$condition_values_attributes = $feed_queries['condition_values_attributes'];

			foreach ( $filter_by_attributes_atts as $filter_key => $filter_value ) :
				?>
                <tr>
                    <td>
                        <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
                    </td>
                    <td>
                        <select name="filter_by_attributes_atts[]" required>
							<?php print gg_woo_feed_get_wc_product_attribute_dropdown( $filter_value ); ?>
                        </select>
                    </td>

					<?php $condition_attributes_options = gg_woo_feed_get_meta_query_condition_options(); ?>
                    <td>
                        <select name="conditions_attributes[]" class="attr_type gg_woo_feed-not-empty">
							<?php foreach ( $condition_attributes_options as $a_condition => $a_condition_label ) : ?>
                                <option value="<?php echo esc_attr( $a_condition ); ?>" <?php selected( $a_condition,
									isset( $conditions_attributes[ $filter_key ] ) && $conditions_attributes[ $filter_key ] ? $conditions_attributes[ $filter_key ] : '', true );
								?>><?php
									echo esc_html( $a_condition_label ); ?></option>
							<?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="condition_values_attributes[]"
                               value="<?php echo isset( $condition_values_attributes[ $filter_key ] ) && $condition_values_attributes[ $filter_key ] ? $condition_values_attributes[ $filter_key ] : ''; ?>">
                    </td>
                    <td>
                        <i class="gg_woo_feed-del-condition-attributes dashicons dashicons-no-alt"></i>
                    </td>
                </tr>
			<?php endforeach; ?>
		<?php endif; ?>
        </tbody>
        <tfoot>
        <tr class="gg_woo_feed-no-conditions_attributes" <?php echo ! $has_condition_attributes ? 'style="display: none;"' : ''; ?>>
            <td colspan="5">
                <p><?php esc_html_e( 'No conditions', 'gg-woo-feed' ); ?></p>
            </td>
        </tr>
        <tr>
            <td colspan="5">
                <button type="button" class="gg_woo_feed-btn gg_woo_feed-btn-primary" id="gg_woo_feed-add-new-condition-attribites">
                    <span class="dashicons dashicons-plus"></span><?php esc_html_e( 'Add New Condition', 'gg-woo-feed' ); ?>
                </button>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

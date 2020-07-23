<?php
/**
 * Advanced Filter.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

$has_condition = isset( $feed_queries['filter_atts'] ) && count( $feed_queries['filter_atts'] ) > 0;
?>
<div class="gg_woo_feed-field-wrap gg_woo_feed-select-field-wrap form-group " id="gg_woo_feed-form-filter_relationship-wrap">
    <label class="gg_woo_feed-label" for="gg_woo_feed-form-filter_relationship" style="width: 15%"><?php esc_html_e( 'Relationship Conditions', 'gg-woo-feed' ); ?></label>
    <div class="gg_woo_feed-field-main">
		<?php
		$relationship_options = [
			'and' => esc_html__( 'AND', 'gg-woo-feed' ),
			'or'  => esc_html__( 'OR', 'gg-woo-feed' ),
		];
		?>
        <select name="filter_relationship" id="gg_woo_feed-form-filter_relationship" style="width: 200px">
			<?php foreach ( $relationship_options as $relationship_attr => $relationship_attr_label ) : ?>
                <option <?php selected( $relationship_attr, isset( $feed_queries['filter_relationship'] ) ? $feed_queries['filter_relationship'] : 'and', true ); ?>
                        value="<?php echo esc_attr( trim( $relationship_attr ) ); ?>">
					<?php echo esc_html( $relationship_attr_label ); ?>
                </option>
			<?php endforeach; ?>
        </select>
        <p class="gg_woo_feed-description"><?php esc_html_e( 'Select a relationship for conditions.', 'gg-woo-feed' ); ?></p>
    </div>
</div>

<table class="table tree widefat fixed gg_woo_feed-table-filter" style="width: 100%;" id="gg_woo_feed-table-filter">
    <thead>
    <tr>
        <th></th>
        <th><?php esc_html_e( 'Attributes', 'gg-woo-feed' ); ?></th>
        <th><?php esc_html_e( 'Condition', 'gg-woo-feed' ); ?></th>
        <th><?php esc_html_e( 'Value', 'gg-woo-feed' ); ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
	<?php
	if ( $has_condition ) :
		$filter_atts = $feed_queries['filter_atts'];
		$conditions = $feed_queries['conditions'];
		$condition_values = $feed_queries['condition_values'];

		foreach ( $filter_atts as $filter_key => $filter_value ) :
			?>
            <tr>
                <td>
                    <i class="gg_woo_feed-sort dashicons dashicons-move"></i>
                </td>
                <td>
                    <select name="filter_atts[]" required>
						<?php print gg_woo_feed_get_product_attribute_dropdown( $filter_value ); ?>
                    </select>
                </td>

				<?php $condition_options = gg_woo_feed_get_filter_condition_options(); ?>
                <td>
                    <select name="conditions[]" class="attr_type gg_woo_feed-not-empty">
						<?php foreach ( $condition_options as $condition => $condition_label ) : ?>
                            <option value="<?php echo esc_attr( $condition ); ?>" <?php selected( $condition,
								isset( $conditions[ $filter_key ] ) && $conditions[ $filter_key ] ? $conditions[ $filter_key ] : '', true );
							?>><?php
								echo esc_html( $condition_label ); ?></option>
						<?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="text" name="condition_values[]"
                           value="<?php echo isset( $condition_values[ $filter_key ] ) && $condition_values[ $filter_key ] ? $condition_values[ $filter_key ] : ''; ?>">
                </td>
                <td>
                    <i class="gg_woo_feed-del-condition dashicons dashicons-no-alt"></i>
                </td>
            </tr>
		<?php endforeach; ?>
	<?php endif; ?>
    </tbody>
    <tfoot>
    <tr class="gg_woo_feed-no-conditions" <?php echo ! $has_condition ? 'style="display: none;"' : ''; ?>>
        <td colspan="5">
            <p><?php esc_html_e( 'No conditions', 'gg-woo-feed' ); ?></p>
        </td>
    </tr>
    <tr>
        <td colspan="5">
            <button type="button" class="gg_woo_feed-btn gg_woo_feed-btn-primary" id="gg_woo_feed-add-new-condition">
                <span class="dashicons dashicons-plus"></span><?php esc_html_e( 'Add New Condition', 'gg-woo-feed' ); ?>
            </button>
        </td>
    </tr>
    </tfoot>
</table>

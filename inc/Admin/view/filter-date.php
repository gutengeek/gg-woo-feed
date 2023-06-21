<?php
/**
 * Filter by attributes.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

?>
<div class="gg_woo_feed-field-wrap gg_woo_feed-switch-field-wrap form-group " id="gg_woo_feed-form-filter_by_date-wrap">
    <div class="gg_woo_feed-field-main">
        <label class="gg_woo_feed-enable-switch-input">
            <input type="checkbox" name="filter_by_date" id="gg_woo_feed-form-filter_by_date" value="on"
                   class="gg_woo_feed-switch form-control" <?php checked( 'on',
				( isset( $feed_queries['filter_by_date'] ) ? $feed_queries['filter_by_date'] : 'off' ), true ); ?>>
            <span class="slider round"></span>
        </label>
        <label class="gg_woo_feed-enable-switch-label" for="gg_woo_feed-form-filter_by_date"><?php esc_html_e( 'Filter by Date',
				'gg-woo-feed' ); ?></label>
    </div>
</div>
<p class="gg_woo_feed-description"><?php esc_html_e( 'The filter by product date public', 'gg-woo-feed' ); ?></p>
<div class="filter_by_date_section" <?php echo (!isset( $feed_queries['filter_by_date']) || $feed_queries['filter_by_date'] == 'off' ) ? 'style="display: none"' : '' ?>>
    <div class="gg_woo_feed-field-wrap gg_woo_feed-select-field-wrap form-group " id="gg_woo_feed-form-filter_date_start-wrap">
        <label class="gg_woo_feed-label" for="gg_woo_feed-form-filter_date_start" style="width: 15%"><?php esc_html_e( 'Start Date', 'gg-woo-feed' ); ?></label>
        <div class="gg_woo_feed-field-main">
			<input type="date" 
                    name="filter_date_start" 
                    id="gg_woo_feed-form-filter_date_start" 
                    style="width: 200px;" 
                    <?php echo (isset( $feed_queries['filter_date_start'])) ? 'value="'.$feed_queries['filter_date_start'].'"' : '' ?>>
        </div>
    </div>

    <div class="gg_woo_feed-field-wrap gg_woo_feed-select-field-wrap form-group " id="gg_woo_feed-form-filter_date_end-wrap">
        <label class="gg_woo_feed-label" for="gg_woo_feed-form-filter_date_end" style="width: 15%"><?php esc_html_e( 'End Date', 'gg-woo-feed' ); ?></label>
        <div class="gg_woo_feed-field-main">
			<input type="date" 
                    name="filter_date_end" 
                    id="gg_woo_feed-form-filter_date_end" 
                    style="width: 200px;" 
                    <?php echo (isset( $feed_queries['filter_date_end'])) ? 'value="'.$feed_queries['filter_date_end'].'"' : '' ?>>
        </div>
    </div>
</div>

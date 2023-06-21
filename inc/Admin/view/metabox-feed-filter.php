<?php
?>
<div id="gg_woo_feed_filter_meta" class="postbox gg_woo_feed-metabox">
    <button type="button" class="handlediv" aria-expanded="true">
        <span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Filter', 'gg-woo-feed' ); ?></span><span class="toggle-indicator" aria-hidden="true"></span>
    </button>
    <h2 class="hndle ui-sortable-handle"><span><?php esc_html_e( 'Filter', 'gg-woo-feed' ); ?></span></h2>
    <div class="inside">
        <div class="gg_woo_feed-metabox-content">
            <div class="gg_woo_feed-tabs--horizontal">
                <div class="gg_woo_feed-tabs__wrap">
                    <div class="gg_woo_feed-tabs__nav">
                        <ul class="gg_woo_feed-ul gg_woo_feed-tabs__nav-list">
                            <li class="gg_woo_feed-tabs__item gg_woo_feed-active">
                                <div class="gg_woo_feed-tabs__title">
                                    <span class="gg_woo_feed-tabs__title-text"><?php esc_html_e( 'Query', 'gg-woo-feed' ); ?></span>
                                </div>
                            </li>
                            <li class="gg_woo_feed-tabs__item ">
                                <div class="gg_woo_feed-tabs__title">
                                    <span class="gg_woo_feed-tabs__title-text"><?php esc_html_e( 'Advanced', 'gg-woo-feed' ); ?></span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="gg_woo_feed-tabs__body">
                        <div class="gg_woo_feed-tabs__content gg_woo_feed-active">
                            <div class="gg_woo_feed-row">
                                <div class="gg_woo_feed-col-4">
                                    <div class="gg_woo_feed-field-wrap gg_woo_feed-text-field-wrap form-group " id="gg_woo_feed-form-limit-wrap">
                                        <label class="gg_woo_feed-label" for="gg_woo_feed-form-limit"><?php esc_html_e( 'Limit', 'gg-woo-feed' ) ?></label>
                                        <div class="gg_woo_feed-field-main">
                                            <input type="number" min="-1" step="1" id="gg_woo_feed-form-limit" class="gg_woo_feed-text gg_woo_feed-text-small gg_woo_feed-text-number form-control"
                                                   name="product_limit"
                                                   value="<?php echo isset( $feed_queries['product_limit'] ) ? $feed_queries['product_limit'] : '-1'; ?>">
                                            <p class="gg_woo_feed-description"><?php esc_html_e( 'Default `-1` will get all products.' ); ?></p>
                                        </div>
                                    </div>

                                    <div class="gg_woo_feed-field-wrap gg_woo_feed-select-field-wrap form-group " id="gg_woo_feed-form-feed_filter_stock-wrap">
                                        <label class="gg_woo_feed-label" for="gg_woo_feed-form-feed_filter_stock"><?php esc_html_e( 'Stock', 'gg-woo-feed' ); ?></label>
                                        <div class="gg_woo_feed-field-main">
                                            <select name="feed_filter_stock">
												<?php foreach ( gg_woo_feed_product_stock_statuses() as $stock_key => $stock_label ) : ?>
                                                    <option <?php selected( $stock_key, isset( $feed_queries['feed_filter_stock'] ) ? $feed_queries['feed_filter_stock'] : 'instock', true ); ?>
                                                            value="<?php echo esc_attr(
																$stock_key );
															?>">
														<?php echo esc_html( $stock_label ); ?>
                                                    </option>
												<?php endforeach; ?>
                                            </select>
                                            <p class="gg_woo_feed-description"><?php esc_html_e( 'Select a stock status.', 'gg-woo-feed' ); ?></p>
                                        </div>
                                    </div>

                                    <div class="gg_woo_feed-field-wrap gg_woo_feed-select-field-wrap form-group " id="gg_woo_feed-form-feed_filter_sale-wrap">
                                        <label class="gg_woo_feed-label" for="gg_woo_feed-form-feed_filter_sale"><?php esc_html_e( 'Sale', 'gg-woo-feed' ) ?></label>
                                        <div class="gg_woo_feed-field-main">
                                            <select name="feed_filter_sale">
												<?php foreach ( gg_woo_feed_product_sale_statuses() as $sale_key => $sale_label ) : ?>
                                                    <option <?php selected( $sale_key, isset( $feed_queries['feed_filter_sale'] ) ? $feed_queries['feed_filter_sale'] : 'all', true ); ?>
                                                            value="<?php echo esc_attr(
																$sale_key );
															?>">
														<?php echo esc_html( $sale_label ); ?>
                                                    </option>
												<?php endforeach; ?>
                                            </select>
                                            <p class="gg_woo_feed-description"><?php esc_html_e( 'Select a sale status.', 'gg-woo-feed' ); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="gg_woo_feed-col-3">
                                    <div class="gg_woo_feed-open-popup-wrap">
                                        <a href="#chose_categories" class="gg_woo_feed-btn gg_woo_feed-btn-primary gg_woo_feed-open-popup" id="gg_woo_feed-select-categories">
											<?php esc_html_e( 'Select Product Categories', 'gg-woo-feed' ); ?>
                                        </a>
                                        <div class="gg_woo_feed-popup-wrap" style="display: none;">
                                            <div class="gg_woo_feed-popup-bg"></div>
                                            <div class="gg_woo_feed-popup">
                                                <div class="gg_woo_feed-popup-close" tabindex="0" title="<?php esc_attr_e( 'Close', 'gg-woo-feed' ); ?>"><span
                                                            class="dashicons dashicons-no-alt"></span></div>
                                                <div class="gg_woo_feed-popup-form">
                                                    <div id="gg_woo_feed-popup-categories" class="gg_woo_feed-popup-body">
														<?php $dropdown->product_categories_list( $feed_queries ? $feed_queries : [] ); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="gg_woo_feed-open-popup-wrap">
                                        <a href="#chose_product_type" class="gg_woo_feed-btn gg_woo_feed-btn-primary gg_woo_feed-open-popup" id="gg_woo_feed-select-product_type">
											<?php esc_html_e( 'Select Product Types', 'gg-woo-feed' ); ?>
                                        </a>
                                        <div class="gg_woo_feed-popup-wrap" style="display: none;">
                                            <div class="gg_woo_feed-popup-bg"></div>
                                            <div class="gg_woo_feed-popup">
                                                <div class="gg_woo_feed-popup-close" tabindex="0" title="<?php esc_attr_e( 'Close', 'gg-woo-feed' ); ?>"><span
                                                            class="dashicons dashicons-no-alt"></span></div>
                                                <div class="gg_woo_feed-popup-form">
                                                    <div id="gg_woo_feed-popup-type" class="gg_woo_feed-popup-body">
                                                        <p><b><?php esc_html_e( 'Select product types', 'gg-woo-feed' ); ?></b></p>
                                                        <ul>
															<?php
															$is_empty_product_type = true;
															if ( ! empty( $feed_queries['feed_filter_product_type'] ) &&
															     is_array( $feed_queries['feed_filter_product_type'] ) &&
															     count( $feed_queries['feed_filter_product_type'] ) > 0 ) {
																$is_empty_product_type = false;
															}

															foreach ( wc_get_product_types() as $value => $label ) {
																$selected = true;
																if ( ! $is_empty_product_type ) {
																	$selected = in_array( $value, $feed_queries['feed_filter_product_type'] );
																}
																print '<li><label class="gg_woo_feed_checkboxes_top"><input type="checkbox" name="feed_filter_product_type[]" value="' . esc_attr( $value ) . '" ' .
																      ( $selected ? 'checked' : '' ) . '>' . esc_html( $label ) . '</label></li>';
															}
															?>
                                                        </ul>
                                                        <div id="gg_woo_feed-popup-bottom"><a href="javascript:void(0);"
                                                                                              class="gg_woo_feed-btn gg_woo_feed-btn-submit gg_woo_feed-popup-done"><?php esc_html_e( 'Done',
																	'gg-woo-feed' );
																?></a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="gg_woo_feed-col-5">
                                    <div class="gg_woo_feed-field-wrap gg_woo_feed-switch-field-wrap form-group " id="gg_woo_feed-form-exclude_variations-wrap">
                                        <div class="gg_woo_feed-field-main">
                                            <label class="gg_woo_feed-enable-switch-input">
                                                <input type="checkbox" name="exclude_variations" id="gg_woo_feed-form-exclude_variations" value="on"
                                                       class="gg_woo_feed-switch form-control" <?php checked( 'on',
													( isset( $feed_queries['exclude_variations'] ) ? $feed_queries['exclude_variations'] : 'on' ), true ); ?>>
                                                <span class="slider round"></span>
                                            </label>
                                            <label class="gg_woo_feed-enable-switch-label" for="gg_woo_feed-form-exclude_variations"><?php esc_html_e( 'Exclude variations for variable products',
													'gg-woo-feed' ); ?></label>
                                        </div>
                                    </div>
                                    <div class="gg_woo_feed-field-wrap gg_woo_feed-switch-field-wrap form-group " id="gg_woo_feed-form-show_main_variable_product-wrap">
                                        <div class="gg_woo_feed-field-main">
                                            <label class="gg_woo_feed-enable-switch-input">
                                                <input type="checkbox" name="show_main_variable_product" id="gg_woo_feed-form-show_main_variable_product" value="on"
                                                       class="gg_woo_feed-switch form-control" <?php checked(
													'on',
													( isset( $feed_queries['show_main_variable_product'] ) ? $feed_queries['show_main_variable_product'] : 'on' ), true ); ?>>
                                                <span class="slider round"></span>
                                            </label>
                                            <label class="gg_woo_feed-enable-switch-label" for="gg_woo_feed-form-show_main_variable_product"><?php esc_html_e( 'Show main variable product',
													'gg-woo-feed' ); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
	                        <?php require_once( GGWOOFEED_DIR . 'inc/Admin/view/filter-attributes.php' ); ?>
                            <?php require_once( GGWOOFEED_DIR . 'inc/Admin/view/filter-date.php' ); ?>
                        </div>

                        <div class="gg_woo_feed-tabs__content">
	                        <?php require_once( GGWOOFEED_DIR . 'inc/Admin/view/filter-advanced.php' ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

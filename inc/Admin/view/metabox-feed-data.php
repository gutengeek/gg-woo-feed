<?php
?>
<div id="gg_woo_feed_data_meta" class="postbox gg_woo_feed-metabox">
    <button type="button" class="handlediv" aria-expanded="true">
        <span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Data', 'gg-woo-feed' ); ?></span><span class="toggle-indicator" aria-hidden="true"></span>
    </button>
    <h2 class="hndle ui-sortable-handle"><span><?php esc_html_e( 'Data', 'gg-woo-feed' ); ?></span></h2>
    <div class="inside">
        <div class="gg_woo_feed-metabox-content">
            <div class="gg_woo_feed-tabs--horizontal">
                <div class="gg_woo_feed-tabs__wrap">
                    <div class="gg_woo_feed-tabs__nav">
                        <ul class="gg_woo_feed-ul gg_woo_feed-tabs__nav-list">
                            <li class="gg_woo_feed-tabs__item gg_woo_feed-active">
                                <div class="gg_woo_feed-tabs__title">
                                    <span class="gg_woo_feed-tabs__title-text"><?php esc_html_e( 'General', 'gg-woo-feed' ); ?></span>
                                </div>
                            </li>
                            <li class="gg_woo_feed-tabs__item ">
                                <div class="gg_woo_feed-tabs__title">
                                    <span class="gg_woo_feed-tabs__title-text"><?php esc_html_e( 'Mapping', 'gg-woo-feed' ); ?></span>
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
                            <div class="gg_woo_feed-general-data-section">
                                <h4 class="gg_woo_feed-section-heading"><?php esc_html_e( 'Product Title', 'gg-woo-feed' ); ?></h4>
								<?php if ( class_exists( 'WPSEO_Frontend' ) ) : ?>
                                    <label>
                                        <input name="title_use_yoast" type="checkbox" value="on" <?php checked( 'on',
											( isset( $feed_queries['title_use_yoast'] ) ? $feed_queries['title_use_yoast'] : 'off' ), true ); ?>>
										<?php esc_html_e( 'Prefer to use Yoast SEO title.', 'gg-woo-feed' ); ?>
                                    </label>
								<?php endif; ?>

                                <label>
                                    <input name="title_use_custom" type="checkbox" value="on" <?php checked( 'on',
										( isset( $feed_queries['title_use_custom'] ) ? $feed_queries['title_use_custom'] : 'on' ), true ); ?>>
									<?php esc_html_e( 'Prefer to use Custom title. If this value is empty, use the mapping field.', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input name="title_add_variation" type="checkbox" value="on" <?php checked( 'on',
										( isset( $feed_queries['title_add_variation'] ) ? $feed_queries['title_add_variation'] : 'on' ), true ); ?>>
									<?php esc_html_e( 'Add variation title in the product name.', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input name="title_fix_uppercase" type="checkbox" value="on" <?php checked( 'on',
										( isset( $feed_queries['title_fix_uppercase'] ) ? $feed_queries['title_fix_uppercase'] : 'on' ), true ); ?>>
									<?php esc_html_e( 'Fix uppercase letters from product titles.', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input name="title_remove_cap" type="checkbox" value="on" <?php checked( 'on',
										( isset( $feed_queries['title_remove_cap'] ) ? $feed_queries['title_remove_cap'] : 'off' ), true ); ?>>
									<?php esc_html_e( 'Remove capital letters from product titles.', 'gg-woo-feed' ); ?>
                                </label>
                            </div>

                            <div class="gg_woo_feed-general-data-section">
                                <h4 class="gg_woo_feed-section-heading"><?php esc_html_e( 'Product Description', 'gg-woo-feed' ); ?></h4>
                                <p class="gg_woo_feed-general-data-desc"><?php esc_html_e( 'Product description will be fill in the below order:', 'gg-woo-feed' ); ?></p>
								<?php if ( class_exists( 'WPSEO_Frontend' ) ) : ?>
                                    <label>
                                        <input name="desc_use_yoast" type="checkbox" value="on" <?php checked( 'on',
											( isset( $feed_queries['desc_use_yoast'] ) ? $feed_queries['desc_use_yoast'] : 'off' ), true ); ?>>
										<?php esc_html_e( 'Prefer to use Yoast SEO Meta description.', 'gg-woo-feed' ); ?>
                                    </label>
								<?php endif; ?>

                                <label>
                                    <input name="desc_use_custom" type="checkbox" value="on" <?php checked( 'on',
										( isset( $feed_queries['desc_use_custom'] ) ? $feed_queries['desc_use_custom'] : 'on' ), true ); ?>>
									<?php esc_html_e( 'Prefer to use Custom description.', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input name="desc_use_short_description" type="checkbox" value="on" <?php checked( 'on',
										( isset( $feed_queries['desc_use_short_description'] ) ? $feed_queries['desc_use_short_description'] : 'on' ), true ); ?>>
									<?php esc_html_e( 'Short description', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input name="desc_use_description" type="checkbox" value="on" <?php checked( 'on',
										( isset( $feed_queries['desc_use_description'] ) ? $feed_queries['desc_use_description'] : 'on' ), true ); ?>>
									<?php esc_html_e( 'Description', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input type="checkbox" value="on" checked disabled>
									<?php esc_html_e( 'Use the mapping field', 'gg-woo-feed' ); ?>
                                </label>
                            </div>

                            <div class="gg_woo_feed-general-data-section">
                                <h4 class="gg_woo_feed-section-heading"><?php esc_html_e( 'Main variable product', 'gg-woo-feed' ); ?></h4>
                                <p class="gg_woo_feed-general-data-desc"><?php esc_html_e( 'Main variable product settings when you turn on `Show main variable product` ', 'gg-woo-feed' ); ?></p>
                                <div class="gg_woo_feed-field-wrap gg_woo_feed-select-field-wrap form-group " id="gg_woo_feed-form-feed_variable_price-wrap">
                                    <label class="gg_woo_feed-label" for="gg_woo_feed-form-feed_variable_price"><?php esc_html_e( 'Main products price', 'gg-woo-feed' ); ?></label>
                                    <div class="gg_woo_feed-field-main">
										<?php
										$variable_price_options = [
											'smallest' => esc_html__( 'Smallest Variation Price', 'gg-woo-feed' ),
											'biggest'  => esc_html__( 'Biggest Variation Price', 'gg-woo-feed' ),
											'first'    => esc_html__( 'Default Variation', 'gg-woo-feed' ),
										];
										?>
                                        <select name="feed_variable_price" id="gg_woo_feed-form-feed_variable_price">
											<?php foreach ( $variable_price_options as $variable_price => $variable_price_label ) : ?>
                                                <option <?php selected( $variable_price, isset( $feed_queries['feed_variable_price'] ) ? $feed_queries['feed_variable_price'] : 'smallest', true ); ?>
                                                        value="<?php echo esc_attr( $variable_price ); ?>">
													<?php echo esc_html( $variable_price_label ); ?>
                                                </option>
											<?php endforeach; ?>
                                        </select>
                                        <p class="gg_woo_feed-description"><?php esc_html_e( 'Select a price for main variable product.', 'gg-woo-feed' ); ?></p>
                                    </div>
                                </div>

                                <div class="gg_woo_feed-field-wrap gg_woo_feed-select-field-wrap form-group " id="gg_woo_feed-form-feed_variable_attr-wrap">
                                    <label class="gg_woo_feed-label" for="gg_woo_feed-form-feed_variable_attr"><?php esc_html_e( 'Main products attribute', 'gg-woo-feed' ); ?></label>
                                    <div class="gg_woo_feed-field-main">
										<?php
										$variable_attr_options = [
											'all'   => esc_html__( 'All attributes', 'gg-woo-feed' ),
											'price' => esc_html__( 'Based on `Main products price`', 'gg-woo-feed' ),
										];
										?>
                                        <select name="feed_variable_attr" id="gg_woo_feed-form-feed_variable_attr">
											<?php foreach ( $variable_attr_options as $variable_attr => $variable_attr_label ) : ?>
                                                <option <?php selected( $variable_attr, isset( $feed_queries['feed_variable_attr'] ) ? $feed_queries['feed_variable_attr'] : 'all', true ); ?>
                                                        value="<?php echo esc_attr( $variable_attr ); ?>">
													<?php echo esc_html( $variable_attr_label ); ?>
                                                </option>
											<?php endforeach; ?>
                                        </select>
                                        <p class="gg_woo_feed-description"><?php esc_html_e( 'Select an attribute type for main variable product.', 'gg-woo-feed' ); ?></p>
                                    </div>
                                </div>

                                <div class="gg_woo_feed-field-wrap gg_woo_feed-select-field-wrap form-group " id="gg_woo_feed-form-feed_variable_title-wrap">
                                    <label class="gg_woo_feed-label" for="gg_woo_feed-form-feed_variable_title"><?php esc_html_e( 'Main products title', 'gg-woo-feed' ); ?></label>
                                    <div class="gg_woo_feed-field-main">
										<?php
										$variable_title_options = [
											'default' => esc_html__( 'Default product title. Keep the product title intact', 'gg-woo-feed' ),
											'price'   => esc_html__( 'Based on `Main products price`', 'gg-woo-feed' ),
											'all'     => esc_html__( 'Add all attributes after product title', 'gg-woo-feed' ),
										];
										?>
                                        <select name="feed_variable_title" id="gg_woo_feed-form-feed_variable_title">
											<?php foreach ( $variable_title_options as $variable_title => $variable_title_label ) : ?>
                                                <option <?php selected( $variable_title, isset( $feed_queries['feed_variable_title'] ) ? $feed_queries['feed_variable_title'] : 'default', true ); ?>
                                                        value="<?php echo esc_attr( $variable_title ); ?>">
													<?php echo esc_html( $variable_title_label ); ?>
                                                </option>
											<?php endforeach; ?>
                                        </select>
                                        <p class="gg_woo_feed-description"><?php esc_html_e( 'Select a product title type for main variable product.', 'gg-woo-feed' ); ?></p>
                                    </div>
                                </div>
                                <div class="gg_woo_feed-field-wrap gg_woo_feed-select-field-wrap form-group " id="gg_woo_feed-form-feed_variable_image-wrap">
                                    <label class="gg_woo_feed-label" for="gg_woo_feed-form-feed_variable_image"><?php esc_html_e( 'Main products image', 'gg-woo-feed' ); ?></label>
                                    <div class="gg_woo_feed-field-main">
			                            <?php
			                            $variable_image_options = [
				                            'default' => esc_html__( 'Default parent product image', 'gg-woo-feed' ),
				                            'price'   => esc_html__( 'Based on `Main products price`', 'gg-woo-feed' ),
			                            ];
			                            ?>
                                        <select name="feed_variable_image" id="gg_woo_feed-form-feed_variable_image">
				                            <?php foreach ( $variable_image_options as $variable_image => $variable_image_label ) : ?>
                                                <option <?php selected( $variable_image, isset( $feed_queries['feed_variable_image'] ) ? $feed_queries['feed_variable_image'] : 'price', true ); ?>
                                                        value="<?php echo esc_attr( $variable_image ); ?>">
						                            <?php echo esc_html( $variable_image_label ); ?>
                                                </option>
				                            <?php endforeach; ?>
                                        </select>
                                        <p class="gg_woo_feed-description"><?php esc_html_e( 'Select a product image type for main variable product.', 'gg-woo-feed' ); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="gg_woo_feed-general-data-section">
                                <h4 class="gg_woo_feed-section-heading"><?php esc_html_e( 'Product Images', 'gg-woo-feed' ); ?></h4>
                                <div class="gg_woo_feed-field-wrap gg_woo_feed-switch-field-wrap form-group " id="gg_woo_feed-form-include_additional_image_link-wrap">
                                    <div class="gg_woo_feed-field-main">
                                        <label class="gg_woo_feed-enable-switch-input">
                                            <input type="checkbox" name="include_additional_image_link" id="gg_woo_feed-form-include_additional_image_link" value="on"
                                                   class="gg_woo_feed-switch form-control" <?php
											checked( 'on',
												( isset( $feed_queries['include_additional_image_link'] ) ? $feed_queries['include_additional_image_link'] : 'on' ), true ); ?>>
                                            <span class="slider round"></span>
                                        </label>
                                        <label class="gg_woo_feed-enable-switch-label"><?php esc_html_e( 'Include additional_image_link', 'gg-woo-feed' ); ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="gg_woo_feed-general-data-section">
                                <h4 class="gg_woo_feed-section-heading"><?php esc_html_e( 'Brand', 'gg-woo-feed' ); ?></h4>
                                <p class="gg_woo_feed-general-data-desc"><?php esc_html_e( 'Brand will be fill in the below order:', 'gg-woo-feed' ); ?></p>
                                <label>
                                    <input name="brand_use_custom" type="checkbox" value="on" <?php checked( 'on',
				                        ( isset( $feed_queries['brand_use_custom'] ) ? $feed_queries['brand_use_custom'] : 'on' ), true ); ?>>
			                        <?php esc_html_e( 'Use Custom Product metabox (in product editor).', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input name="brand_use_cat_custom" type="checkbox" value="on" <?php checked( 'on',
				                        ( isset( $feed_queries['brand_use_cat_custom'] ) ? $feed_queries['brand_use_cat_custom'] : 'on' ), true ); ?>>
			                        <?php esc_html_e( 'Use Custom Product Category Term meta (in product category editor).', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input type="checkbox" value="on" checked disabled>
			                        <?php esc_html_e( 'If above values is empty, use the mapping field.', 'gg-woo-feed' ); ?>
                                </label>
                            </div>

                            <div class="gg_woo_feed-general-data-section">
                                <h4 class="gg_woo_feed-section-heading"><?php esc_html_e( 'Product Condition', 'gg-woo-feed' ); ?></h4>
                                <label>
                                    <input name="condition_use_custom" type="checkbox" value="on" <?php checked( 'on',
										( isset( $feed_queries['condition_use_custom'] ) ? $feed_queries['condition_use_custom'] : 'on' ), true ); ?>>
									<?php esc_html_e( 'Prefer to use Custom condition. If this value is empty, use the mapping field.', 'gg-woo-feed' ); ?>
                                </label>
                            </div>

                            <div class="gg_woo_feed-general-data-section">
                                <h4 class="gg_woo_feed-section-heading"><?php esc_html_e( 'GTIN', 'gg-woo-feed' ); ?></h4>
                                <label>
                                    <input name="gtin_use_custom" type="checkbox" value="on" <?php checked( 'on',
										( isset( $feed_queries['gtin_use_custom'] ) ? $feed_queries['gtin_use_custom'] : 'on' ), true ); ?>>
									<?php esc_html_e( 'Prefer to use Custom GTIN metabox. If this value is empty, use the mapping field.', 'gg-woo-feed' ); ?>
                                </label>
                            </div>

                            <div class="gg_woo_feed-general-data-section">
                                <h4 class="gg_woo_feed-section-heading"><?php esc_html_e( 'MPN', 'gg-woo-feed' ); ?></h4>
                                <p class="gg_woo_feed-general-data-desc"><?php esc_html_e( 'MPN will be fill in the below order:', 'gg-woo-feed' ); ?></p>
                                <label>
                                    <input name="mpn_use_custom" type="checkbox" value="on" <?php checked( 'on',
										( isset( $feed_queries['mpn_use_custom'] ) ? $feed_queries['mpn_use_custom'] : 'on' ), true ); ?>>
									<?php esc_html_e( 'Use Custom Product metabox (in product editor).', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input name="mpn_use_cat_custom" type="checkbox" value="on" <?php checked( 'on',
			                            ( isset( $feed_queries['mpn_use_cat_custom'] ) ? $feed_queries['mpn_use_cat_custom'] : 'on' ), true ); ?>>
		                            <?php esc_html_e( 'Use Custom Product Category Term meta (in product category editor).', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input type="checkbox" value="on" checked disabled>
		                            <?php esc_html_e( 'If above values is empty, use the mapping field.', 'gg-woo-feed' ); ?>
                                </label>
                            </div>

                            <div class="gg_woo_feed-general-data-section">
                                <h4 class="gg_woo_feed-section-heading"><?php esc_html_e( 'Google Taxonomy', 'gg-woo-feed' ); ?></h4>
                                <p class="gg_woo_feed-general-data-desc"><?php esc_html_e( 'Product Google Taxonomy will be fill in the below order:', 'gg-woo-feed' ); ?></p>
                                <label>
                                    <input name="google_taxonomy_use_custom" type="checkbox" value="on" <?php checked( 'on',
										( isset( $feed_queries['google_taxonomy_use_custom'] ) ? $feed_queries['google_taxonomy_use_custom'] : 'on' ), true ); ?>>
									<?php esc_html_e( 'Use Custom Product metabox (in product editor).', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input name="google_taxonomy_use_cat_custom" type="checkbox" value="on" <?php checked( 'on',
										( isset( $feed_queries['google_taxonomy_use_cat_custom'] ) ? $feed_queries['google_taxonomy_use_cat_custom'] : 'on' ), true ); ?>>
									<?php esc_html_e( 'Use Custom Product Category Term meta (in product category editor).', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input type="checkbox" value="on" checked disabled>
									<?php esc_html_e( 'If above values is empty, use the mapping field.', 'gg-woo-feed' ); ?>
                                </label>
                            </div>

                            <div class="gg_woo_feed-general-data-section">
                                <h4 class="gg_woo_feed-section-heading"><?php esc_html_e( 'Adult', 'gg-woo-feed' ); ?></h4>
                                <p class="gg_woo_feed-general-data-desc"><?php esc_html_e( 'Adult will be fill in the below order:', 'gg-woo-feed' ); ?></p>
                                <label>
                                    <input name="adult_use_custom" type="checkbox" value="on" <?php checked( 'on',
				                        ( isset( $feed_queries['adult_use_custom'] ) ? $feed_queries['adult_use_custom'] : 'on' ), true ); ?>>
			                        <?php esc_html_e( 'Use Custom Product metabox (in product editor).', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input name="adult_use_cat_custom" type="checkbox" value="on" <?php checked( 'on',
				                        ( isset( $feed_queries['adult_use_cat_custom'] ) ? $feed_queries['adult_use_cat_custom'] : 'on' ), true ); ?>>
			                        <?php esc_html_e( 'Use Custom Product Category Term meta (in product category editor).', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input type="checkbox" value="on" checked disabled>
			                        <?php esc_html_e( 'If above values is empty, use the mapping field.', 'gg-woo-feed' ); ?>
                                </label>
                            </div>

                            <div class="gg_woo_feed-general-data-section">
                                <h4 class="gg_woo_feed-section-heading"><?php esc_html_e( 'shipping_label', 'gg-woo-feed' ); ?></h4>
                                <p class="gg_woo_feed-general-data-desc"><?php esc_html_e( 'shipping_label will be fill in the below order:', 'gg-woo-feed' ); ?></p>
                                <label>
                                    <input name="shipping_label_use_custom" type="checkbox" value="on" <?php checked( 'on',
				                        ( isset( $feed_queries['shipping_label_use_custom'] ) ? $feed_queries['shipping_label_use_custom'] : 'on' ), true ); ?>>
			                        <?php esc_html_e( 'Use Custom Product metabox (in product editor).', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input name="shipping_label_use_cat_custom" type="checkbox" value="on" <?php checked( 'on',
				                        ( isset( $feed_queries['shipping_label_use_cat_custom'] ) ? $feed_queries['shipping_label_use_cat_custom'] : 'on' ), true ); ?>>
			                        <?php esc_html_e( 'Use Custom Product Category Term meta (in product category editor).', 'gg-woo-feed' ); ?>
                                </label>
                                <label>
                                    <input type="checkbox" value="on" checked disabled>
			                        <?php esc_html_e( 'If above values is empty, use the mapping field.', 'gg-woo-feed' ); ?>
                                </label>
                            </div>
                        </div>

                        <div class="gg_woo_feed-tabs__content">
							<?php if ( ! $is_edit ) : ?>
                                <div id="gg_woo_feed_core_mapping_fields"></div>
							<?php else : ?>
								<?php require_once( GGWOOFEED_DIR . 'inc/Admin/view/edit-mapping.php' ); ?>
							<?php endif; ?>
                        </div>

                        <div class="gg_woo_feed-tabs__content">
                            <h3 class="gg_woo_feed-tabs__content-title"><?php esc_html_e( 'UTM Parameters', 'gg-woo-feed' ); ?></h3>
                            <div class="gg_woo_feed-row">
                                <div class="gg_woo_feed-col-5">
                                    <div class="gg_woo_feed-field-wrap gg_woo_feed-text-field-wrap form-group " id="gg_woo_feed-form-utm_source-wrap">
                                        <label class="gg_woo_feed-label" for="gg_woo_feed-form-utm_source"><?php esc_html_e( 'Source', 'gg-woo-feed' ) ?></label>
                                        <div class="gg_woo_feed-field-main">
                                            <input type="text" id="gg_woo_feed-form-utm_source" class="gg_woo_feed-text gg_woo_feed-text-small gg_woo_feed-text-number form-control"
                                                   name="campaign_parameters[utm_source]"
                                                   value="<?php echo isset( $feed_queries['campaign_parameters']['utm_source'] ) ? $feed_queries['campaign_parameters']['utm_source'] : ''; ?>">
                                            <p class="gg_woo_feed-description"><?php esc_html_e( 'The referrer: (ex: google, newsletter)', 'gg-woo-feed' ); ?></p>
                                        </div>
                                    </div>

                                    <div class="gg_woo_feed-field-wrap gg_woo_feed-text-field-wrap form-group " id="gg_woo_feed-form-utm_campaign-wrap">
                                        <label class="gg_woo_feed-label" for="gg_woo_feed-form-utm_campaign"><?php esc_html_e( 'Campaign', 'gg-woo-feed' ) ?></label>
                                        <div class="gg_woo_feed-field-main">
                                            <input type="text" id="gg_woo_feed-form-utm_campaign" class="gg_woo_feed-text gg_woo_feed-text-small gg_woo_feed-text-number form-control"
                                                   name="campaign_parameters[utm_campaign]"
                                                   value="<?php echo isset( $feed_queries['campaign_parameters']['utm_campaign'] ) ? $feed_queries['campaign_parameters']['utm_campaign'] : ''; ?>">
                                            <p class="gg_woo_feed-description"><?php esc_html_e( 'Product, slogan (ex: new_sale)', 'gg-woo-feed' ); ?></p>
                                        </div>
                                    </div>

                                    <div class="gg_woo_feed-field-wrap gg_woo_feed-text-field-wrap form-group " id="gg_woo_feed-form-utm_content-wrap">
                                        <label class="gg_woo_feed-label" for="gg_woo_feed-form-utm_content"><?php esc_html_e( 'Content', 'gg-woo-feed' ) ?></label>
                                        <div class="gg_woo_feed-field-main">
                                            <input type="text" id="gg_woo_feed-form-utm_content" class="gg_woo_feed-text gg_woo_feed-text-small gg_woo_feed-text-number form-control"
                                                   name="campaign_parameters[utm_content]"
                                                   value="<?php echo isset( $feed_queries['campaign_parameters']['utm_content'] ) ? $feed_queries['campaign_parameters']['utm_content'] : ''; ?>">
                                            <p class="gg_woo_feed-description"><?php esc_html_e( 'Use to differentiate ads.', 'gg-woo-feed' ); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="gg_woo_feed-col-5">
                                    <div class="gg_woo_feed-field-wrap gg_woo_feed-text-field-wrap form-group " id="gg_woo_feed-form-utm_medium-wrap">
                                        <label class="gg_woo_feed-label" for="gg_woo_feed-form-utm_medium"><?php esc_html_e( 'Medium', 'gg-woo-feed' ) ?></label>
                                        <div class="gg_woo_feed-field-main">
                                            <input type="text" id="gg_woo_feed-form-utm_medium" class="gg_woo_feed-text gg_woo_feed-text-small gg_woo_feed-text-number form-control"
                                                   name="campaign_parameters[utm_medium]"
                                                   value="<?php echo isset( $feed_queries['campaign_parameters']['utm_medium'] ) ? $feed_queries['campaign_parameters']['utm_medium'] : ''; ?>">
                                            <p class="gg_woo_feed-description"><?php esc_html_e( 'Marketing medium: (ex: banner, email)', 'gg-woo-feed' ); ?></p>
                                        </div>
                                    </div>

                                    <div class="gg_woo_feed-field-wrap gg_woo_feed-text-field-wrap form-group " id="gg_woo_feed-form-utm_term-wrap">
                                        <label class="gg_woo_feed-label" for="gg_woo_feed-form-utm_term"><?php esc_html_e( 'Term', 'gg-woo-feed' ) ?></label>
                                        <div class="gg_woo_feed-field-main">
                                            <input type="text" id="gg_woo_feed-form-utm_term" class="gg_woo_feed-text gg_woo_feed-text-small gg_woo_feed-text-number form-control"
                                                   name="campaign_parameters[utm_term]"
                                                   value="<?php echo isset( $feed_queries['campaign_parameters']['utm_term'] ) ? $feed_queries['campaign_parameters']['utm_term'] : ''; ?>">
                                            <p class="gg_woo_feed-description"><?php esc_html_e( 'Identify the paid keywords.', 'gg-woo-feed' ); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h3 class="gg_woo_feed-tabs__content-title"><?php esc_html_e( 'Custom Path', 'gg-woo-feed' ); ?></h3>
                            <div class="gg_woo_feed-field-wrap gg_woo_feed-text-field-wrap form-group " id="gg_woo_feed-form-custom_path-wrap">
                                <label class="gg_woo_feed-label" for="gg_woo_feed-form-custom_path"><?php esc_html_e( 'Folder path' ) ?></label>
                                <div class="gg_woo_feed-field-main">
                                    <input type="text" id="gg_woo_feed-form-custom_path" class="gg_woo_feed-text gg_woo_feed-text-small gg_woo_feed-text-number form-control" name="custom_path"
                                           value="<?php echo isset( $feed_queries['custom_path'] ) ? $feed_queries['custom_path'] : 'gg-woo-feed'; ?>">
                                    <p class="gg_woo_feed-description"><?php esc_html_e( 'Folders within the bundle that contain feed files. Default folder is `wp-content/uploads/gg-woo-feed/`.',
											'gg-woo-feed' ); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

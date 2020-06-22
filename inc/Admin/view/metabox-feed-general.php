<?php

?>
<table class="gg_woo_feed-top-meta widefat postbox">
    <thead>
    <tr>
        <th colspan="2"><?php esc_html_e( 'General', 'gg-woo-feed' ); ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th><label for="filename"><?php esc_html_e( 'File Name', 'gg-woo-feed' ); ?> <span class="required">*</span></label></th>
        <td>
            <input name="filename" value="<?php echo isset( $feed_queries['filename'] ) ? esc_attr( $feed_queries['filename'] ) : ''; ?>" type="text" id="filename" class="gg_woo_feed-filename-input
            gg_woo_feed-input" required="required">
            <p class="gg_woo_feed-meta-desc">
			    <?php esc_html_e( 'Set a unique file name.', 'gg-woo-feed' ) ?>
            </p>
        </td>
    </tr>

    <tr>
        <th><label for="provider"><?php esc_html_e( 'Provider', 'gg-woo-feed' ); ?> <span class="required">*</span></label></th>
        <td>
            <select name="provider" id="provider" class="gg_woo_feed-provider-select gg_woo_feed-input" required="required">
				<?php echo $dropdown->get_provider_dropdown( $feed_queries['provider'] ); ?>
            </select>
            <p class="gg_woo_feed-meta-desc">
                <?php esc_html_e( 'Choose a provider or a template.', 'gg-woo-feed' ) ?>
            </p>
        </td>
    </tr>

    <tr>
        <th><label for="feed_type"><?php esc_html_e( 'Feed Type', 'gg-woo-feed' ); ?> <span class="required">*</span></label></th>
        <td>
            <select name="feed_type" id="feed_type" class="gg_woo_feed-feedtype-select gg_woo_feed-input" required="required">
                <option value=""></option>
				<?php
				foreach ( gg_woo_feed_get_file_types() as $type => $label ) {
					printf( '<option value="%1$s" %3$s>%2$s</option>', esc_attr( $type ), esc_html( $label ), selected( $feed_queries['feed_type'], $type, false ) );
				}
				?>
            </select>
            <span class="spinner" style="float: none; margin: 0;"></span>
            <p class="gg_woo_feed-meta-desc">
		        <?php esc_html_e( 'Set a feed type.', 'gg-woo-feed' ) ?>
            </p>
        </td>
    </tr>

    <tr class="item_wrap" style="display: none;">
        <th><label for="items_wrap"><?php esc_html_e( 'Items Wrapper', 'gg-woo-feed' ); ?> <span class="required">*</span></label></th>
        <td>
            <input name="items_wrap" id="items_wrap" type="text"
                   value="<?php echo ( 'xml' == $feed_queries['feed_type'] ) && isset( $feed_queries['items_wrap'] ) ? esc_attr( $feed_queries['items_wrap'] ) : 'products'; ?>" class="gg_woo_feed-input"
                   required="required">
        </td>
    </tr>

    <tr class="item_wrap" style="display: none;">
        <th><label for="item_wrap"><?php esc_html_e( 'Single Item Wrapper', 'gg-woo-feed' ); ?> <span class="required">*</span></label></th>
        <td>
            <input name="item_wrap" id="item_wrap" type="text"
                   value="<?php echo ( 'xml' == $feed_queries['feed_type'] ) && isset( $feed_queries['item_wrap'] ) ? esc_attr( $feed_queries['item_wrap'] ) : 'product'; ?>" class="gg_woo_feed-input"
                   required="required">
        </td>
    </tr>

    <tr class="gg_woo_feed-type-csvtxt" style="display: none;">
        <th><label for="delimiter"><?php esc_html_e( 'Delimiter', 'gg-woo-feed' ); ?> <span class="required">*</span></label></th>
        <td>
            <select name="delimiter" id="delimiter" class="gg_woo_feed-input">
				<?php

				foreach ( gg_woo_feed_get_csv_delimiters() as $key => $value ) {
					printf( '<option value="%1$s" %3$s>%2$s</option>', esc_attr( $key ), esc_html( $value ), selected( $feed_queries['delimiter'], $key, false ) );
				}
				?>
            </select>
        </td>
    </tr>
    <tr class="gg_woo_feed-type-csvtxt" style="display: none;">
        <th><label for="enclosure"><?php esc_html_e( 'Enclosure', 'gg-woo-feed' ); ?> <span class="required">*</span></label></th>
        <td>
            <select name="enclosure" id="enclosure" class="gg_woo_feed-input">
				<?php
				foreach ( gg_woo_feed_get_csv_enclosures() as $key => $value ) {
					printf( '<option value="%1$s" %3$s>%2$s</option>', esc_attr( $key ), esc_html( $value ), selected( $feed_queries['enclosure'], $key, false ) );
				}
				?>
            </select>
        </td>
    </tr>
    </tbody>
</table>

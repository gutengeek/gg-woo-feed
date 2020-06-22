<?php
?>

<div id="side-sortables" class="meta-box-sortables">
    <div id="submitdiv" class="postbox">
        <h2 class="hndle"><?php esc_html_e( 'Publish', 'gg-woo-feed' ); ?></h2>
        <div class="inside">
            <div class="submitbox" id="submitpost">
                <div id="misc-publishing-actions">
                    <div class="misc-pub-section">
						<?php esc_html_e( 'Last Updated:', 'gg-woo-feed' ); ?>
                        <span class="misc-pub-detail">
                            <?php echo isset( $feed_info['last_updated'] ) ? esc_html( $feed_info['last_updated'] ) : esc_html__( 'N/A', 'gg-woo-feed' ); ?>
                        </span>
                    </div>
                    <div class="misc-pub-section">
		                <?php esc_html_e( 'Actions:', 'gg-woo-feed' ); ?>
                        <span class="misc-pub-detail">
                            <?php
                            if ( isset( $feed_info['url'] ) && $feed_info['url'] ) {
	                            $button = sprintf(
		                            '<a href="#" title="%2$s" class="gg_woo_feed-action-button js-copy-feed" data-clipboard-text="%1$s"><span class="dashicons dashicons-admin-page" aria-hidden="true"></span></a>',
		                            esc_url( $feed_info['url'] ),
		                            esc_html__( 'Copy', 'gg-woo-feed' )
	                            );

	                            $button .= sprintf(
		                            '<a href="%1$s" title="%2$s" class="gg_woo_feed-action-button" target="_blank"><span class="dashicons dashicons-external" aria-hidden="true"></span></a>',
		                            esc_url( $feed_info['url'] ),
		                            esc_html__( 'Open', 'gg-woo-feed' )
	                            );

	                            $button .= sprintf(
		                            '<a href="%1$s" title="%2$s" class="gg_woo_feed-action-button js-download-feed" download><span class="dashicons dashicons-download" aria-hidden="true"></span></a>',
		                            esc_url( $feed_info['url'] ),
		                            esc_html__( 'Download', 'gg-woo-feed' )
	                            );

	                            echo $button;
                            } else {
	                            esc_html_e( 'N/A', 'gg-woo-feed' );
                            }
                            ?>
                        </span>
                    </div>
                </div>
                <div id="major-publishing-actions">
                    <div id="publishing-action">
						<?php //if ( $is_edit ) : ?>
<!--                            <button name="save" type="submit" class="gg_woo_feed-btn gg_woo_feed-btn-submit" id="publish">--><?php //esc_html_e( 'Save', 'gg-woo-feed' ); ?><!--</button>-->
						<?php //endif; ?>
                        <button name="<?php echo $is_edit ? 'edit-feed' : ''; ?>" type="button" class="gg_woo_feed-generate updatefeed gg_woo_feed-btn gg_woo_feed-btn-submit">
							<?php esc_html_e( 'Save and Generate Feed', 'gg-woo-feed' ); ?>
                        </button>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>

	<?php
	if ( $is_edit && ( 'google' === $provider ) ) {
		require_once( GGWOOFEED_DIR . 'inc/Admin/view/metabox-google-sync.php' );
	}
	?>
</div>

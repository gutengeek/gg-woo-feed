<?php
use GG_Woo_Feed\Common\Model\Provider;

if ( ! function_exists( 'opal_feed_merchant_info_metabox' ) ) {
	/**
	 * Render Provider Info Metabox
	 *
	 * @param array $feed_config
	 *
	 * @return void
	 */
	function opal_feed_merchant_info_metabox( $feed_config ) {
		$provider      = ( isset( $feed_config['provider'] ) && ! empty( $feed_config['provider'] ) ) ? $feed_config['provider'] : '';
		$provider_info = new Provider( $provider );
		?>
        <span class="spinner"></span>
        <div class="merchant-infos">
			<?php foreach ( $provider_info->get_info() as $k => $v ) { ?>
                <div class="merchant-info-section <?php echo esc_attr( $k ); ?>">
					<?php if ( 'link' == $k ) { ?>
                        <span class="dashicons dashicons-media-document" style="color: #82878c;"
                              aria-hidden="true"></span>
                        <span><?php esc_html_e( 'Feed Specification:', 'gg-woo-feed' ) ?></span>
                        <strong class="data"><?php
							( empty( $v ) ) ? esc_html_e( 'N/A',
								'gg-woo-feed' ) : printf( '<a href="%s" target="_blank">%s</a>',
								esc_url( $v ),
								esc_html__( 'Read Article', 'gg-woo-feed' ) );
							?></strong>
					<?php } elseif ( 'video' == $k ) { ?>
                        <span class="dashicons dashicons-video-alt3" style="color: #82878c;" aria-hidden="true"></span>
                        <span><?php esc_html_e( 'Video Documentation:', 'gg-woo-feed' ) ?></span>
                        <strong class="data"><?php
							/** @noinspection HtmlUnknownTarget */
							( empty( $v ) ) ? esc_html_e( 'N/A',
								'gg-woo-feed' ) : printf( '<a href="%s" target="_blank">%s</a>',
								esc_url( $v ),
								esc_html__( 'Watch now', 'gg-woo-feed' ) );
							?></strong>
					<?php } elseif ( 'feed_file_type' == $k ) { ?>
                        <span class="dashicons dashicons-media-text" style="color: #82878c;"
                              aria-hidden="true"></span> <?php esc_html_e( 'Format Supported:', 'gg-woo-feed' ) ?>
                        <strong class="data"><?php
							if ( empty( $v ) ) {
								esc_html_e( 'N/A', 'gg-woo-feed' );
							} else {
								$v = implode( ', ',
									array_map( function ( $type ) {
										return esc_html( strtoupper( $type ) );
									},
										(array) $v ) );
								echo esc_html( $v );
							} ?></strong>
						<?php
					} elseif ( 'doc' == $k ) { ?>
                        <span class="dashicons dashicons-editor-help" style="color: #82878c;" aria-hidden="true"></span>
                        <span><?php esc_html_e( 'Support Docs:', 'gg-woo-feed' ); ?></span>
                        <ul class="data">
							<?php
							if ( empty( $v ) ) {
								esc_html_e( 'N/A', 'gg-woo-feed' );
							} else {
								foreach ( $v as $label => $link ) {
									printf( '<li><a href="%s" target="_blank">%s</a></li>',
										esc_url( $link ),
										esc_html( $label ) );
								}
							}
							?>
                        </ul>
						<?php
					} ?>
                </div>
			<?php } ?>
        </div>
		<?php
	}
}

if ( ! function_exists( 'gg_woo_feed_get_mapping_template' ) ) {
	/**
	 * @param string $tabId
	 * @param array $feed_queries
	 * @param bool $id_edit
	 */
	function gg_woo_feed_get_mapping_template( $feed_queries, $id_edit ) {
		global $provider, $dropdown;
		if ( $id_edit ) {
			include GGWOOFEED_DIR . 'inc/Admin/view/edit-config.php';
		} else {
            if ( file_exists( GGWOOFEED_DIR . 'inc/Admin/view/' . $provider . '-add-feed.php' ) ) {
				include GGWOOFEED_DIR . 'inc/Admin/view/' . $provider . '-add-feed.php';
			} else {
				include GGWOOFEED_DIR . 'inc/Admin/view/common-add-feed.php';
			}
		}
	}
}

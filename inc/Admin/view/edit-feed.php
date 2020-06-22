<?php

$dropdown = new \GG_Woo_Feed\Common\Dropdown();
$is_edit  = true;
$feed_name = sanitize_text_field( $_GET['feed'] );
$feed_info = maybe_unserialize( get_option( $feed_name ) );
?>
<div class="wrap" id="gg_woo_feed-feeds-form">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Edit Feed', 'gg-woo-feed' ); ?></h1>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-add-feed' ) ); ?>" class="page-title-action">
		<?php esc_html_e( 'Add New Feed', 'gg-woo-feed' ); ?>
    </a>
    <hr class="wp-header-end"/>
    <form action="" name="feed" id="updatefeed" class="gg_woo_feed-generate-form" method="POST">
		<?php wp_nonce_field( 'gg_woo_feed-feed-form' ); ?>
        <input type="hidden" name="feed_option_name" value="<?php echo esc_attr( str_replace( array( 'gg_woo_feed_feed_', 'gg_woo_feed_config_' ), '', $feed_name ) ); ?>">
        <input type="hidden" name="feed_id" value="<?php echo esc_attr( $feed_id ); ?>">
        <input type="hidden" name="is_edit" value="1">
        <input type="hidden" name="current_file_name" value="<?php echo esc_attr( isset( $feed_info['current_file_name'] ) ? $feed_info['current_file_name'] : '' ); ?>">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
	                <?php
	                $args   = [];

	                require_once( GGWOOFEED_DIR . 'inc/Admin/view/metabox-feed-general.php' );
	                require_once( GGWOOFEED_DIR . 'inc/Admin/view/metabox-feed-filter.php' );
	                require_once( GGWOOFEED_DIR . 'inc/Admin/view/metabox-feed-data.php' );
	                ?>
                </div>
                <div id="postbox-container-1">
                    <?php require_once( GGWOOFEED_DIR . 'inc/Admin/view/sidebar.php' ); ?>
                </div>
            </div>
            <div class="clear"></div>

	        <?php require_once( GGWOOFEED_DIR . 'inc/Admin/view/processing-generate.php' ); ?>
        </div>
    </form>
</div>

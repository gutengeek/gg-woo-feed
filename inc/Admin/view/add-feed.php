<?php
use GG_Woo_Feed\Libraries\Form\Form;

$dropdown = new \GG_Woo_Feed\Common\Dropdown();
$is_edit = false;
?>
<div class="wrap" id="gg_woo_feed-feeds-form">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Add New Feed', 'gg-woo-feed' ); ?></h1>
    <hr class="wp-header-end"/>
    <form action="" name="feed" id="gg_woo_feed-generate-form" class="gg_woo_feed-generate-form add-new" method="POST">
		<?php wp_nonce_field( 'gg_woo_feed-feed-form' ); ?>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
	                <?php
	                $feed_queries = gg_woo_feed_parse_feed_queries( [] );
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

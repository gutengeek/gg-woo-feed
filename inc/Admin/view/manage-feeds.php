<?php

use GG_Woo_Feed\Admin\Table\Feed_Table;

$table = new Feed_Table();
?>
<div class="wrap" id="gg_woo_feed-feeds">
    <h1 class="wp-heading-inline">
		<?php esc_html_e( 'Manage Feeds', 'gg-woo-feed' ) ?>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->plugin_name . '-add-feed' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'gg-woo-feed' )
			?></a>
    </h1>
    <form action="" id="manage-feeds-form" method="POST" enctype="multipart/form-data">
		<?php
		$table->views();
		$table->prepare_items();
		$table->display();
		wp_nonce_field( 'gg_woo_feed-manage-feeds' );
		?>
    </form>
	<?php require_once( GGWOOFEED_DIR . 'inc/Admin/view/processing-regenerate.php' ); ?>
</div>

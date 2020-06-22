<?php
namespace GG_Woo_Feed\Admin\Page;

use GG_Woo_Feed\Common\Module\Google_Merchant_Content_API;
use Google_Service_ShoppingContent;

class Google_Status {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_text_domain The text domain of this plugin.
	 */
	private $plugin_text_domain;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name        The name of this plugin.
	 * @param string $version            The version of this plugin.
	 * @param string $plugin_text_domain The text domain of this plugin.
	 * @since       1.0.0
	 */
	public function __construct( $plugin_name, $version, $plugin_text_domain ) {
		$this->plugin_name        = $plugin_name;
		$this->version            = $version;
		$this->plugin_text_domain = $plugin_text_domain;

		$this->add_plugin_admin_menu();
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		add_submenu_page(
			$this->plugin_name . '-feeds',
			apply_filters( $this->plugin_name . '-google-status-page-title', esc_html__( 'Google Status', 'gg-woo-feed' ) ),
			apply_filters( $this->plugin_name . '-google-status-title', esc_html__( 'Google Status', 'gg-woo-feed' ) ),
			'manage_options',
			$this->plugin_name . '-google-status',
			[ $this, 'render' ]
		);
	}

	/**
	 * Render system info.
	 */
	public function render() {
		$google_merchant = new Google_Merchant_Content_API();
		?>
        <div id="gg_woo_feed-google-status">
            <h3 class="gg-woo-feed-google-status-title"><?php esc_html_e( 'Google Merchant Status', 'gg-woo-feed' ); ?></h3>
            <div class="gg_woo_feed-google-status-page">
				<?php if ( ! ( $google_merchant->is_authenticate() ) ) : ?>
                    <div class="gg-woo-feed-google-sync-notice">
                        <div class="gg-woo-feed-authen-status notice">
                            <p class="gg-woo-feed-authen-status-label">
								<?php esc_html_e( 'You are not authorized.', 'gg-woo-feed' ); ?>
                                <a class="" href="<?php echo esc_url( admin_url( 'admin.php?page=gg-woo-feed-google-sync' ) ); ?>">
									<?php esc_html_e( 'Authenticate Now', 'gg-woo-feed' ); ?>
                                    <span class="dashicons dashicons-external" aria-hidden="true"></span>
                                </a>
                            </p>
                        </div>
                    </div>
				<?php else : ?>
					<?php
					$client        = $google_merchant::get_client();
					$client_id     = $google_merchant::$client_id;
					$client_secret = $google_merchant::$client_secret;
					$merchant_id   = $google_merchant::$merchant_id;

					try {
						$access_token = $google_merchant->get_access_token();
						$client->setClientId( $client_id );
						$client->setClientSecret( $client_secret );
						$client->setScopes( 'https://www.googleapis.com/auth/content' );
						$client->setAccessToken( $access_token );

						$service         = new Google_Service_ShoppingContent( $client );
						$accountstatuses = $service->accountstatuses->get( $merchant_id, $merchant_id );
						$level_issues    = $accountstatuses->accountLevelIssues;
						$products        = $accountstatuses->products;
					} catch ( \Exception $e ) {
						$error = json_decode( $e->getMessage() );
						?>
                        <div class="gg-woo-feed-google-sync-notice">
                            <div class="gg-woo-feed-authen-status notice">
                                <p class="gg-woo-feed-authen-status-label">
									<?php echo esc_html( $error->error->message ); ?>
                                    <a class="" href="<?php echo esc_url( admin_url( 'admin.php?page=gg-woo-feed-google-sync' ) ); ?>">
										<?php esc_html_e( 'Authenticate Now', 'gg-woo-feed' ); ?>
                                        <span class="dashicons dashicons-external" aria-hidden="true"></span>
                                    </a>
                                </p>
                            </div>
                        </div>
						<?php
					}

					?>
					<?php if ( $level_issues ) : ?>
                        <div class="gg-woo-feed-status-level-issues">
							<?php foreach ( $level_issues as $level_issue ) : ?>
                                <p class="gg-woo-feed-status-level-issue <?php echo esc_attr( $level_issue->severity ); ?>">
									<?php echo esc_html( $level_issue->title ); ?>
                                    <a href="<?php echo esc_url( $level_issue->documentation ); ?>" target="_blank"><?php esc_html_e( 'Learn more' ); ?></a>
                                </p>
							<?php endforeach; ?>
                        </div>
					<?php endif; ?>

					<?php if ( $products ) : ?>
						<?php foreach ( $products as $product ) : ?>
							<?php $statistics = $product->statistics; ?>
							<?php $item_level_issues = $product->itemLevelIssues; ?>
							<?php if ( $statistics ) : ?>
                                <div class="gg-woo-feed-product-statuses">
                                    <div class="gg-woo-feed-product-status active">
                                        <p><?php esc_html_e( 'Active items', 'gg-woo-feed' ); ?></p>
                                        <span><?php echo absint( $statistics->active ); ?></span>
                                    </div>

                                    <div class="gg-woo-feed-product-status expiring">
                                        <p><?php esc_html_e( 'Expiring items', 'gg-woo-feed' ); ?></p>
                                        <span><?php echo absint( $statistics->expiring ); ?></span>
                                    </div>

                                    <div class="gg-woo-feed-product-status pending">
                                        <p><?php esc_html_e( 'Pending items', 'gg-woo-feed' ); ?></p>
                                        <span><?php echo absint( $statistics->pending ); ?></span>
                                    </div>

                                    <div class="gg-woo-feed-product-status disapproved">
                                        <p><?php esc_html_e( 'Disapproved items', 'gg-woo-feed' ); ?></p>
                                        <span><?php echo absint( $statistics->disapproved ); ?></span>
                                    </div>
                                </div>
							<?php endif; ?>

                            <table class="table tree widefat fixed gg-woo-feed-product-status-table" style="width: 100%">
                                <thead>
                                <tr>
                                    <th class="issue-column"><?php esc_html_e( 'Issue', 'gg-woo-feed' ); ?></th>
                                    <th class="number-items-column"><?php esc_html_e( 'Number items', 'gg-woo-feed' ); ?></th>
                                    <th class="solution-column"><?php esc_html_e( 'Solution', 'gg-woo-feed' ); ?></th>
                                    <th class="documentation-column"><?php esc_html_e( 'Documentation', 'gg-woo-feed' ); ?></th>
                                </tr>
                                </thead>
                                <tbody>
								<?php
								if ( $item_level_issues ) :
									foreach ( $item_level_issues as $item_level_issue ) : ?>
                                        <tr>
                                            <td>
                                                <span class="dashicons dashicons-warning <?php echo esc_attr( $item_level_issue->servability ); ?>"></span>
												<?php echo esc_html( $item_level_issue->description ); ?>
                                            </td>
                                            <td>
												<?php echo absint( $item_level_issue->numItems ); ?>
                                            </td>
                                            <td>
												<?php echo esc_html( $item_level_issue->detail ); ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo esc_url( $item_level_issue->documentation ) ?>" target="_blank"><?php echo esc_html( $item_level_issue->documentation ) ?></a>
                                            </td>
                                        </tr>
									<?php endforeach;
								endif;
								?>
                                </tbody>
                            </table>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endif; ?>
            </div>
        </div>
		<?php
	}
}

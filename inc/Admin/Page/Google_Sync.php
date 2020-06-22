<?php
namespace GG_Woo_Feed\Admin\Page;

use GG_Woo_Feed\Common\Module\Google_Merchant_Content_API;

class Google_Sync {

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
			apply_filters( $this->plugin_name . '-google-sync-page-title', esc_html__( 'Google Merchant Sync', 'gg-woo-feed' ) ),
			apply_filters( $this->plugin_name . '-google-sync-title', esc_html__( 'Google Merchant Sync', 'gg-woo-feed' ) ),
			'manage_options',
			$this->plugin_name . '-google-sync',
			[ $this, 'render' ]
		);
	}

	/**
	 * Render system info.
	 */
	public function render() {
		$google_merchant = new Google_Merchant_Content_API();
		$client_id       = $google_merchant::$client_id;
		$client_secret   = $google_merchant::$client_secret;
		$merchant_id     = $google_merchant::$merchant_id;
		$redirect_uri    = $google_merchant::get_redirect_url();

		if ( isset( $_GET['code'] ) ) {
			$google_merchant->save_access_token( $_GET['code'] );
		}

		$html = '';
		if ( ! ( $google_merchant->is_authenticate() ) ) {
			if ( $client_id && $client_secret && $merchant_id ) {
				$html = $google_merchant->get_access_token_link();
			}
		} else {
			$html = $google_merchant->get_authorization_success_html();
		}
		?>
        <div id="gg_woo_feed-google-sync">
            <h3 class="gg-woo-feed-google-sync-title"><?php esc_html_e( 'Google Merchant Sync config', 'gg-woo-feed' ); ?></h3>

            <div class="gg-woo-feed-google-sync-notice">
				<?php print $html; ?>
            </div>

            <div class="gg_woo_feed-settings-page">
            <div class="gg_woo_feed-row">
                <div class="gg_woo_feed-col-6">
                    <form action="" method="post">
                        <div class="js-gg_woo_feed-metabox-wrap">
			                <?php wp_nonce_field( 'gg_woo_feed_save_form_meta', 'gg_woo_feed_meta_nonce' ); ?>
                            <div class="gg_woo_feed-field-wrap gg_woo_feed-text-field-wrap form-group " id="gg_woo_feed-form-client-id-wrap">
                                <label class="gg_woo_feed-label" for="gg_woo_feed-form-client-id"><?php esc_html_e( 'Client ID', 'gg-woo-feed' ); ?></label>
                                <div class="gg_woo_feed-field-main">
                                    <input type="text" id="gg_woo_feed-form-client-id" class="gg_woo_feed-text gg_woo_feed-text-small gg_woo_feed-text-number regular-text form-control"
                                           name="gg_woo_feed_google_sync[client_id]" value="<?php echo esc_attr( $client_id ); ?>">
                                </div>
                            </div>
                            <div class="gg_woo_feed-field-wrap gg_woo_feed-text-field-wrap form-group " id="gg_woo_feed-form-client-secret-wrap">
                                <label class="gg_woo_feed-label" for="gg_woo_feed-form-client-secret"><?php esc_html_e( 'Client Secret', 'gg-woo-feed' ); ?></label>
                                <div class="gg_woo_feed-field-main">
                                    <input type="text" id="gg_woo_feed-form-client-secret" class="gg_woo_feed-text gg_woo_feed-text-small gg_woo_feed-text-number regular-text form-control"
                                           name="gg_woo_feed_google_sync[client_secret]" value="<?php echo esc_attr( $client_secret ); ?>">
                                </div>
                            </div>
                            <div class="gg_woo_feed-field-wrap gg_woo_feed-text-field-wrap form-group " id="gg_woo_feed-form-merchant-id-wrap">
                                <label class="gg_woo_feed-label" for="gg_woo_feed-form-merchant-id"><?php esc_html_e( 'Merchant ID', 'gg-woo-feed' ); ?></label>
                                <div class="gg_woo_feed-field-main">
                                    <input type="text" id="gg_woo_feed-form-merchant-id" class="gg_woo_feed-text gg_woo_feed-text-small gg_woo_feed-text-number regular-text form-control"
                                           name="gg_woo_feed_google_sync[merchant_id]" value="<?php echo esc_attr( $merchant_id ); ?>">
                                </div>
                            </div>
                            <div class="gg_woo_feed-field-wrap gg_woo_feed-text-field-wrap form-group " id="gg_woo_feed-form-redirect-url-wrap">
                                <label class="gg_woo_feed-label" for="gg_woo_feed-form-redirect-url"><?php esc_html_e( 'Redirect URL', 'gg-woo-feed' ); ?></label>
                                <div class="gg_woo_feed-field-main">
                                    <code><?php echo esc_url( $redirect_uri ); ?></code>
                                </div>
                            </div>

                        </div>
                        <div class="gg-woo-feed-google-sync-settings-actions">
                            <button class="gg_woo_feed-btn gg_woo_feed-btn-primary" name="reset_google_sync_options" value="resetdata" type="submit"><?php esc_html_e( 'Reset', 'gg-woo-feed' ); ?></button>
                            <button class="gg_woo_feed-btn gg_woo_feed-btn-submit" name="save_google_sync_options" value="savedata" type="submit"><?php esc_html_e( 'Save', 'gg-woo-feed' ); ?></button>
                        </div>
                    </form>
                </div>

                <div class="gg_woo_feed-col-6">
                    <a href="https://www.youtube.com/watch?v=eIzwPZHoipA" target="_blank"><?php esc_html_e( 'How to create Google Merchant config?', 'gg-woo-feed' ); ?></a>
                </div>
            </div>
            </div>
        </div>
		<?php
	}
}

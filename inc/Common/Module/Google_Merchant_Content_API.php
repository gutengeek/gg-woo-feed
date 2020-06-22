<?php
namespace GG_Woo_Feed\Common\Module;

use Google_Client;
use Google_Service_ShoppingContent;

class Google_Merchant_Content_API {

	/**
	 * Client ID.
	 *
	 * @var string
	 */
	static $client_id;

	/**
	 * Client Secret.
	 *
	 * @var string
	 */
	static $client_secret;

	/**
	 * Merchant ID.
	 *
	 * @var string
	 */
	static $merchant_id;

	/**
	 * Access token.
	 *
	 * @var string
	 */
	static $access_token;

	/**
	 * Client.
	 *
	 * @var
	 */
	protected $client;

	/**
     * Instance.
     *
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * Instance.
	 */
	public static function instance() {
		if ( is_null( static::$_instance ) ) {
			static::$_instance = new static();
		}

		return static::$_instance;
	}

	/**
	 * Google_Merchant_Content_API constructor.
	 */
	public function __construct() {
		$option                = static::get_option();
		static::$client_id     = $option['client_id'];
		static::$client_secret = $option['client_secret'];
		static::$merchant_id   = $option['merchant_id'];
	}

	/**
     * New Client.
     *
	 * @return \Google_Client
	 */
	public function init_client() {
		$redirect_uri = static::get_redirect_url();
		$this->client = new Google_Client();
		$this->client->setClientId( static::$client_id );
		$this->client->setClientSecret( static::$client_secret );
		$this->client->setRedirectUri( $redirect_uri );
		$this->client->setScopes( 'https://www.googleapis.com/auth/content' );

		return $this->client;
	}

	/**
     * Get option.
     *
	 * @return array
	 */
	public static function get_option() {
		$option = get_option( 'gg_woo_feed_google_sync', [] );

		$defaut = [
			'client_id'     => '',
			'client_secret' => '',
			'merchant_id'   => '',
		];

		return wp_parse_args( $option, $defaut );
	}

	/**
     * Get Client.
     *
	 * @return \Google_Client
	 */
	public static function get_client() {
		return new Google_Client();
	}

	/**
     * Get access token.
     *
	 * @return string
	 */
	public function get_access_token() {
		return get_option( 'gg_woo_feed_google_sync_access_token', '' );
	}

	/**
     * Get redirect URL.
     *
	 * @return string
	 */
	public static function get_redirect_url() {
		return admin_url( 'admin.php?page=gg-woo-feed-google-sync' );
	}

	/**
	 * Is authenticate?
	 *
	 * @return bool
	 */
	public function is_authenticate() {
		$access_token = get_option( 'gg_woo_feed_google_sync_access_token', '' );

		if ( ! $access_token ) {
			return false;
		}

		$client = static::get_client();

		if ( is_array( $access_token ) ) {
			$client->setAccessToken( $access_token );
		} else {
			$client->setAccessToken( json_decode( $access_token, true ) );
		}

		if ( $client->isAccessTokenExpired() ) {
			return false;
		}

		return true;
	}

	/**
     * Get access token link.
     *
	 * @return string
	 */
	public function get_access_token_link() {
		$client       = static::get_client();
		$redirect_uri = static::get_redirect_url();
		$client->setClientId( static::$client_id );
		$client->setClientSecret( static::$client_secret );
		$client->setRedirectUri( $redirect_uri );
		$client->setScopes( 'https://www.googleapis.com/auth/content' );
		$authenticate_url = $client->createAuthurl();
		ob_start();
		?>
        <div class="gg-woo-feed-authen-status notice">
            <p class="gg-woo-feed-authen-status-label"><?php esc_html_e( 'You are not authorized.', 'gg-woo-feed' ); ?></p>
            <p><?php echo __( 'Your access token has expired. This application uses <strong>OAuth 2.0</strong> to access <strong>Google APIs</strong> and <strong>Content API for Shopping</strong>
                library. Generated access token expires after 3600(s).', 'gg-woo-feed' ); ?></p>
            <a class="gg_woo_feed-btn gg_woo_feed-btn-submit" href="<?php echo esc_url( $authenticate_url ); ?>"><?php esc_html_e( 'Authenticate Now', 'gg-woo-feed' ); ?><span
                        class="dashicons dashicons-external" aria-hidden="true"></span></a>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
     * Get authorization success HTML.
     *
	 * @return string
	 */
	public function get_authorization_success_html() {
		ob_start();
		?>
        <div class="gg-woo-feed-authen-status updated">
            <p class="gg-woo-feed-authen-status-label"><?php esc_html_e( 'You are authorized.', 'gg-woo-feed' ); ?></p>
            <p><?php esc_html_e( 'Now, you can send feeds to your Google Merchant. Notice: The access token expires after 3600(s).', 'gg-woo-feed' ); ?></p>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
     * Save access token.
     *
	 * @param $code
	 */
	public function save_access_token( $code ) {
		$redirect_uri = static::get_redirect_url();
		$client       = new Google_Client();
		$client->setClientId( static::$client_id );
		$client->setClientSecret( static::$client_secret );
		$client->setRedirectUri( $redirect_uri );
		$client->setScopes( 'https://www.googleapis.com/auth/content' );

		if ( ! $this->is_authenticate() ) {
			$client->authenticate( $code );
			$access_token = $client->getAccessToken();
			if ( $access_token ) {
				update_option( 'gg_woo_feed_google_sync_access_token', json_encode( $access_token ) );
			}
		}
	}

	/**
     * Is exist?
     *
	 * @param $feed_option_name
	 * @return bool
	 */
	public function feed_exists( $feed_option_name ) {
		$feed_option_name = gg_woo_feed_extract_feed_option_name( sanitize_text_field( $feed_option_name ) );
		$feed_info        = maybe_unserialize( get_option( 'gg_woo_feed_feed_' . $feed_option_name, [] ) );
		$client           = $this->init_client();
		$service          = new Google_Service_ShoppingContent( $client );
		if ( isset( $feed_info['google_data_feed_id'] ) && $feed_info['google_data_feed_id'] ) {
			try {
				$feed = $service->datafeeds->get( static::$merchant_id, $feed_info['google_data_feed_id'] );

				return true;
			} catch ( \Exception $e ) {
				return false;
			}
		}

		return false;
	}
}

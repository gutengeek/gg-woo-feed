<?php
namespace GG_Woo_Feed\Common\Module;

class Google_Site_Verification {

	/**
	 * Conversion_Tracking constructor.
	 */
	public function __construct() {
		add_action( 'wp_head', [ $this, 'render' ] );
	}

	/**
	 * Render.
	 */
	public function render() {
		$google_site_verification = gg_woo_feed_get_option( 'google_site_verification', '' );

		if ( ! $google_site_verification ) {
			return;
		}

		?>
        <meta name="google-site-verification" content="<?php echo esc_attr( $google_site_verification ); ?>"/>
		<?php
	}
}

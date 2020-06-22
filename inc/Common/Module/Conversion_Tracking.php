<?php
namespace GG_Woo_Feed\Common\Module;

class Conversion_Tracking {

	/**
	 * Conversion ID
	 *
	 * @var string
	 */
	private $conversion_id;

	/**
	 * Conversion label
	 *
	 * @var string
	 */
	private $conversion_label;

	/**
	 * Order total logic
	 *
	 * @var string
	 */
	private $order_total_logic;

	/**
	 * Ignore admin?
	 *
	 * @var string
	 */
	private $ignore_admin;

	/**
	 * Is disable gtag?
	 *
	 * @var string
	 */
	private $disable_gtag;

	/**
	 * Conversion_Tracking constructor.
	 */
	public function __construct() {
		add_action( 'wp_head', [ $this, 'render' ] );
		add_action( 'template_redirect', [ $this, 'cdata_template_redirect' ], -1 );
	}

	/**
	 * Render.
	 */
	public function render() {
		$this->conversion_id     = $this->get_conversion_id();
		$this->conversion_label  = $this->get_conversion_label();
		$this->ignore_admin      = $this->get_ignore_admin();
		$this->disable_gtag      = $this->get_disable_gtag();
		$this->order_total_logic = $this->get_order_total_logic();

		if ( ! $this->conversion_id || ! $this->conversion_label ) {
			return;
		}

		if ( $this->disable_gtag === 'off' ) {
			?>
            <!--noptimize--><!--noptimize-->
            <!-- Global site tag (gtag.js) - Google Ads: <?php echo esc_html( $this->conversion_id ) ?> -->
            <script async
                    src="https://www.googletagmanager.com/gtag/js?id=AW-<?php echo esc_html( $this->conversion_id ) ?>"></script>
            <script>
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push( arguments );
                }

                gtag( 'js', new Date() );

                gtag( 'config', 'AW-<?php echo esc_html( $this->conversion_id ) ?>' );
            </script>
            <!--/noptimize-->
			<?php
		}

		if ( is_order_received_page() ) {
			$order_key            = sanitize_text_field( $_GET['key'] );
			$order                = new \WC_Order( wc_get_order_id_by_order_key( $order_key ) );
			$order_total          = 'order_subtotal' === $this->order_total_logic ? $order->get_subtotal() : $order->get_total();
			$order_total          = $order_total - $order->get_total_discount();
			$order_currency       = $order->get_currency();
			$order_total_filtered = apply_filters( 'gg_woo_feed_conversion_value_filter', $order_total, $order );
			$ignore               = ( 'on' === $this->ignore_admin ) ? ! current_user_can( 'edit_others_pages' ) : true;

			if ( ! $order->has_status( 'failed' ) && $ignore ) {
				?>
                <!--noptimize--><!--noptimize-->
                <!-- Global site tag (gtag.js) - Google Ads: <?php echo esc_html( $this->conversion_id ) ?> -->
                <script>
                    gtag( 'event', 'conversion', {
                        'send_to': 'AW-<?php echo esc_html( $this->conversion_id ) ?>/<?php echo esc_html( $this->conversion_label ) ?>',
                        'value': <?php echo $order_total_filtered; ?>,
                        'currency': '<?php echo $order_currency; ?>',
                        'transaction_id': '<?php echo $order->get_order_number(); ?>',
                    } );
                </script>
                <!--/noptimize-->
				<?php
			}
			?>
			<?php
		}
	}

	/**
	 * CDATA template redirect.
	 *
	 * @param $content
	 */
	public function cdata_template_redirect( $content ) {
		ob_start( [ $this, 'escape_cdata_markup' ] );
	}

	/**
	 * Escape CDATA markup.
	 *
	 * @param $content
	 * @return string|string[]
	 */
	public function escape_cdata_markup( $content ) {
		$content = str_replace( "]]&gt;", "]]>", $content );

		return $content;
	}

	public function get_conversion_id() {
		return wp_strip_all_tags( str_ireplace( [ 'AW-', '"', ], [ '', '' ], gg_woo_feed_get_option( 'ct_conversion_id', '' ) ) );
	}

	public function get_conversion_label() {
		return gg_woo_feed_get_option( 'ct_conversion_label', '' );
	}

	public function get_order_total_logic() {
		return gg_woo_feed_get_option( 'ct_order_total_logic', 'order_subtotal' );
	}

	public function get_ignore_admin() {
		return gg_woo_feed_get_option( 'gg_ignore_admin', 'on' );
	}

	public function get_disable_gtag() {
		return gg_woo_feed_get_option( 'gg_disable_gtag', 'off' );
	}
}

<?php
namespace GG_Woo_Feed\Common\Module;

class Dynamic_Remaketing {
	/**
	 * Conversion ID
	 *
	 * @var string
	 */
	private $conversion_id;

	/**
	 * Merchant Center Prefix
	 *
	 * @var string
	 */
	private $mc_prefix;

	/**
	 * Product Identifier.
	 *
	 * @var string
	 */
	private $product_identifier;

	/**
	 * Ignore admin?
	 *
	 * @var string
	 */
	private $ignore_admin;

	/**
	 * Gtag deactivation.
	 *
	 * @var string
	 */
	private $disable_gtag;

	/**
	 * Conversion ID
	 *
	 * @var bool
	 */
	private $autoptimize_active;

	public function __construct() {
		$this->conversion_id      = wp_strip_all_tags( str_ireplace( [ 'AW-', '"', ], [ '', '' ], gg_woo_feed_get_option( 'dr_conversion_id', '' ) ) );
		$this->mc_prefix          = gg_woo_feed_get_option( 'dr_mc_prefix', '' );
		$this->product_identifier = gg_woo_feed_get_option( 'dr_product_identifier', 'post_id' );
		$this->disable_gtag       = gg_woo_feed_get_option( 'gg_disable_gtag', 'off' );
		$this->ignore_admin       = gg_woo_feed_get_option( 'gg_ignore_admin', 'on' );

		add_action( 'plugins_loaded', [ $this, 'check_optimize' ] );
		add_action( 'plugins_loaded', [ $this, 'handle' ] );
	}

	public function check_optimize() {
		$ignore = ( 'on' === $this->ignore_admin ) ? ! current_user_can( 'edit_others_pages' ) : true;
		if ( $ignore ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$this->autoptimize_active = is_plugin_active( 'autoptimize/autoptimize.php' );
		}
	}

	public function handle() {
		$ignore = ( 'on' === $this->ignore_admin ) ? ! current_user_can( 'edit_others_pages' ) : true;
		if ( $ignore ) {
			if ( $this->disable_gtag === 'off' ) {
				add_action( 'wp_head', [ $this, 'enqueue_gtag' ] );
			}

			add_action( 'wp_footer', [ $this, 'render' ] );
		}
	}

	public function enqueue_gtag() {
		?>
        <!--noptimize-->
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

	public function render() {
		global $woocommerce;

		if ( $this->autoptimize_active == true ) {
			echo "<!--noptimize-->";
		}
		?>
		<?php
		if ( is_front_page() ) {
			?>
            <script type="text/javascript">
                gtag( 'event', 'page_view', {
                    'send_to': 'AW-<?php echo esc_html( $this->conversion_id ) ?>',
                    'ecomm_pagetype': 'home'
                } );
            </script>
			<?php
		} elseif ( is_product_category() ) {
			$product_id = get_the_ID();
			?>

            <script type="text/javascript">
                gtag( 'event', 'page_view', {
                    'send_to': 'AW-<?php echo esc_html( $this->conversion_id ) ?>',
                    'ecomm_pagetype': 'category',
                    'ecomm_category': <?php echo( json_encode( $this->get_product_category( $product_id ) ) ); ?>
                } );
            </script>
			<?php
		} elseif ( is_search() ) {
			?>
            <script type="text/javascript">
                gtag( 'event', 'page_view', {
                    'send_to': 'AW-<?php echo esc_html( $this->conversion_id ) ?>',
                    'ecomm_pagetype': 'searchresults'
                } );
            </script>
			<?php
		} elseif ( is_product() ) {
			$product_id = get_the_ID();
			$product    = wc_get_product( $product_id );

			if ( is_bool( $product ) ) {
				return;
			}

			$product_id_code = '
		<script type="text/javascript">
			gtag(\'event\', \'page_view\', {
			    \'send_to\': \'AW-' . esc_html( $this->conversion_id ) . '\',
			    \'ecomm_pagetype\': \'product\',
			    \'ecomm_category\': ' . json_encode( $this->get_product_category( $product_id ) ) . ',
				\'ecomm_prodid\': ' . json_encode( $this->mc_prefix . ( 'post_id' === $this->product_identifier ? get_the_ID() : $product->get_sku() ) ) . ',
				\'ecomm_totalvalue\': ' . (float) $product->get_price() . '
			});
		</script>';

			echo $product_id_code;
		} elseif ( is_cart() ) {
			$cartprods = $woocommerce->cart->get_cart();
			?>
            <script type="text/javascript">
                gtag( 'event', 'page_view', {
                    'send_to': 'AW-<?php echo esc_html( $this->conversion_id ) ?>',
                    'ecomm_pagetype': 'cart',
                    'ecomm_prodid': <?php echo( json_encode( $this->get_cart_product_ids( $cartprods ) ) );?>,
                    'ecomm_totalvalue': <?php echo WC()->cart->get_cart_contents_total(); ?>
                } );
            </script>
			<?php
		} elseif ( is_order_received_page() ) {
			$order_key      = sanitize_text_field( $_GET['key'] );
			$order          = new \WC_Order( wc_get_order_id_by_order_key( $order_key ) );
			$order_subtotal = $order->get_subtotal();
			$order_subtotal = $order_subtotal - $order->get_total_discount();

			// Only run conversion script if the payment has not failed. (has_status('completed') is too restrictive)
			// And use the order meta to check if the conversion code has already run for this order ID. If yes, don't run it again.
			if ( ! $order->has_status( 'failed' ) ) {
				?>
                <script type="text/javascript">
                    gtag( 'event', 'page_view', {
                        'send_to': 'AW-<?php echo esc_html( $this->conversion_id ) ?>',
                        'ecomm_pagetype': 'purchase',
                        'ecomm_prodid': <?php echo( json_encode( $this->get_content_ids( $order ) ) ); ?>,
                        'ecomm_totalvalue': <?php echo $order_subtotal; ?>
                    } );
                </script>
				<?php
			}
		} else {
			?>
            <script type="text/javascript">
                gtag( 'event', 'page_view', {
                    'send_to': 'AW-<?php echo esc_html( $this->conversion_id ) ?>',
                    'ecomm_pagetype': 'other'
                } );
            </script>
			<?php
		}
		?>
		<?php
		if ( $this->autoptimize_active == true ) {
			echo "<!--/noptimize-->";
		}
		?>
        <style>
            iframe[name='google_conversion_frame'] {
                height: 0 !important;
                width: 0 !important;
                line-height: 0 !important;
                font-size: 0 !important;
                margin-top: -13px;
                float: left;
            }
        </style>
		<?php
	}

	/**
	 * @param $product_id
	 * @return array
	 */
	public function get_product_category( $product_id ) {
		$prod_cats        = get_the_terms( $product_id, 'product_cat' );
		$prod_cats_output = [];

		if ( ! empty( $prod_cats ) ) {
			foreach ( (array) $prod_cats as $k1 ) {
				$prod_cats_output[] = $k1->name;
			}
		}

		return $prod_cats_output;
	}

	/**
	 * @param $cartprods
	 * @return array
	 */
	public function get_cart_product_ids( $cartprods ) {
		$cartprods_items = [];

		foreach ( (array) $cartprods as $entry ) {
			if ( 'post_id' === $this->product_identifier ) {
				$cartprods_items[] = $this->mc_prefix . $entry['product_id'];
			} else {
				$product           = wc_get_product( $entry['product_id'] );
				$cartprods_items[] = $this->mc_prefix . $product->get_sku();
			}
		}

		return $cartprods_items;
	}

	/**
	 * @param \WC_Order $order
	 * @return array
	 */
	public function get_content_ids( $order ) {
		$order_items       = $order->get_items();
		$order_items_array = [];

		foreach ( (array) $order_items as $item ) {
			if ( 'post_id' === $this->product_identifier ) {
				$order_items_array[] = $this->mc_prefix . $item['product_id'];
			} else {
				$product             = wc_get_product( $item['product_id'] );
				$order_items_array[] = $this->mc_prefix . $product->get_sku();
			}
		}

		return $order_items_array;
	}
}

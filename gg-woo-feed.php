<?php
/**
 * Plugin Name:       GTG Product Feed for Shopping
 * Plugin URI:        https://gutengeek.com
 * Description:       GG Woo Feed helps you make feeds with WooCommerce to connect to popular providers: Google, Facebook, Printerst.
 * Version:           1.2.4
 * Author:            GutenGeek
 * Author URI:        https://gutengeek.com/contact
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gg-woo-feed
 * Domain Path:       /languages
 * Tested up to: 6.2
 * WC requires at least: 4.4
 * WC tested up to: 7.8
 */

// If this file is called directly, abort.
use GG_Woo_Feed\Core\Init;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Constants
 */
define( 'GGWOOFEED', 'gg-woo-feed' );
define( 'GGWOOFEED_VERSION', '1.2.4' );
define( 'GGWOOFEED_DIR', plugin_dir_path( __FILE__ ) );
define( 'GGWOOFEED_URL', plugin_dir_url( __FILE__ ) );
define( 'GGWOOFEED_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'GGWOOFEED_PLUGIN_TEXT_DOMAIN', 'gg-woo-feed' );
define( 'GGWOOFEED_METABOX_PREFIX', '_' );

require_once( GGWOOFEED_DIR . 'vendor/autoload.php' );
require_once( GGWOOFEED_DIR . 'inc/Core/functions.php' );
require_once( GGWOOFEED_DIR . 'inc/Core/mix-functions.php' );
require_once( GGWOOFEED_DIR . 'inc/Core/template-functions.php' );
require_once( GGWOOFEED_DIR . 'inc/Core/ajax-functions.php' );

/**
 * Register Activation and Deactivation Hooks
 * This action is documented in inc/core/class-activator.php
 */
register_activation_hook( __FILE__, array( 'GG_Woo_Feed\Core\Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented inc/core/class-deactivator.php
 */
register_deactivation_hook( __FILE__, array( 'GG_Woo_Feed\Core\Deactivator', 'deactivate' ) );

/**
 * Plugin Singleton Container
 *
 * Maintains a single copy of the plugin app object
 *
 * @since    1.0.0
 */
class GG_Woo_Feed {

	/**
	 * The instance of the plugin.
	 *
	 * @var      Init $init Instance of the plugin.
	 */
	private static $init;
	/**
	 * Loads the plugin
	 *
	 * @access    public
	 */
	public static function init() {

		if ( null === self::$init ) {
			self::$init = new Init();
			self::$init->run();
		}

		return self::$init;
	}
}

/**
 * Begins execution of the plugin
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Also returns copy of the app object so 3rd party developers
 * can interact with the plugin's hooks contained within.
 **/
function gg_woo_feed_init() {
	return GG_Woo_Feed::init();
}

gg_woo_feed_init();

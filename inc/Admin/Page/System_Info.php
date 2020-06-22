<?php
namespace GG_Woo_Feed\Admin\Page;

use GG_Woo_Feed\Common\Module\System_Info\Helpers\Model_Helper;
use GG_Woo_Feed\Common\Module\System_Info\Reporters\Base;

class System_Info {

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
	 * Settings.
	 *
	 * Holds the object settings.
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * System info reports.
	 *
	 * Holds an array of available reports in System info page.
	 *
	 * @access private
	 *
	 * @var array
	 */
	private static $reports = [
		'server'          => [],
		'wordpress'       => [],
	];

	private static $report_classes = [
		'server'          => 'GG_Woo_Feed\Common\Module\System_Info\Reporters\Server',
		'wordpress'       => 'GG_Woo_Feed\Common\Module\System_Info\Reporters\WordPress',
	];

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
			apply_filters( $this->plugin_name . '-system-page-title', esc_html__( 'System Info', 'gg-woo-feed' ) ),
			apply_filters( $this->plugin_name . '-system-menu-title', esc_html__( 'System Info', 'gg-woo-feed' ) ),
			'manage_options',
			$this->plugin_name . '-system',
			[ $this, 'render' ]
		);
	}

	/**
	 * Render system info.
	 */
	public function render() {
		$reports_info = self::get_allowed_reports();

		$reports = $this->load_reports( $reports_info, 'html' );

		$raw_reports = $this->load_reports( $reports_info, 'raw' );

		?>
        <div id="gg_woo_feed-system-info">
            <h3><?php esc_html_e( 'System Info', 'gg-woo-feed' ); ?></h3>
            <div class="gg_woo_feed-system-info-wrap"><?php $this->print_report( $reports, 'html' ); ?></div>
        </div>
		<?php
	}

	/**
	 * Get allowed reports.
	 *
	 * Retrieve the available reports in system info page.
	 *
	 * @access public
	 * @static
	 *
	 * @return array Available reports in system info page.
	 */
	public static function get_allowed_reports() {
		return self::$reports;
	}

	/**
	 * Load reports.
	 *
	 * Retrieve the system info reports.
	 *
	 * @access public
	 *
	 * @param array  $reports An array of system info reports.
	 * @param string $format  - possible values: 'raw' or empty string, meaning 'html'
	 *
	 * @return array An array of system info reports.
	 */
	public function load_reports( $reports, $format = '' ) {
		$result = [];

		foreach ( $reports as $report_name => $report_info ) {
			$reporter_params = [
				'name'   => $report_name,
				'format' => $format,
			];

			$reporter_params = array_merge( $reporter_params, $report_info );

			$reporter = $this->create_reporter( $reporter_params );

			if ( ! $reporter instanceof Base ) {
				continue;
			}

			$result[ $report_name ] = [
				'report' => $reporter->get_report( $format ),
				'label'  => $reporter->get_title(),
			];

			if ( ! empty( $report_info['sub'] ) ) {
				$result[ $report_name ]['sub'] = $this->load_reports( $report_info['sub'] );
			}
		}

		return $result;
	}

	/**
	 * Print report.
	 *
	 * Output the system info page reports using an output template.
	 *
	 * @access public
	 *
	 * @param array  $reports  An array of system info reports.
	 * @param string $template Output type from the templates folder. Available
	 *                         templates are `raw` and `html`. Default is `raw`.
	 */
	public function print_report( $reports, $template = 'raw' ) {
		static $tabs_count = 0;

		static $required_plugins_properties = [
			'Name',
			'Version',
			'URL',
			'Author',
		];

		$template_path = GGWOOFEED_DIR . 'inc/Common/Module/System_Info/templates/' . $template . '.php';

		require $template_path;
	}

	/**
	 * Create a report.
	 *
	 * Register a new report that will be displayed in system info page.
	 *
	 * @param array $properties Report properties.
	 *
	 * @return \WP_Error|false|Base Base instance if the report was created,
	 *                                       False or WP_Error otherwise.
	 * @access public
	 *
	 */
	public function create_reporter( array $properties ) {
		$properties = Model_Helper::prepare_properties( $this->get_settings( 'reporter_properties' ), $properties );

		$reporter_class = $properties['class_name'] ? $properties['class_name'] : $this->get_reporter_class( $properties['name'] );

		$reporter = new $reporter_class( $properties );

		if ( ! ( $reporter instanceof Base ) ) {
			return new \WP_Error( 'Each reporter must to be an instance or sub-instance of `Base` class.' );
		}

		if ( ! $reporter->is_enabled() ) {
			return false;
		}

		return $reporter;
	}

	/**
	 * Get Settings.
	 *
	 * @access public
	 *
	 * @param string $setting Optional. The key of the requested setting. Default is null.
	 *
	 * @return mixed An array of all settings, or a single value if `$setting` was specified.
	 */
	public function get_settings( $setting = null ) {
		$this->ensure_settings();

		return self::get_items( $this->settings, $setting );
	}

	/**
	 * Get report class.
	 *
	 * Retrieve the class of the report for any given report type.
	 *
	 * @access public
	 *
	 * @param string $reporter_type The type of the report.
	 *
	 * @return string The class of the report.
	 */
	public function get_reporter_class( $reporter_type ) {
		$classes = static::$report_classes;

		return $classes[ $reporter_type ];
	}

	/**
	 * Ensure settings.
	 *
	 * Ensures that the `$settings` member is initialized
	 *
	 * @access private
	 */
	private function ensure_settings() {
		if ( null === $this->settings ) {
			$this->settings = $this->get_init_settings();
		}
	}

	/**
	 * Get default settings.
	 *
	 * Retrieve the default settings. Used to reset the report settings on
	 * initialization.
	 *
	 * @access protected
	 *
	 * @return array Default settings.
	 */
	protected function get_init_settings() {
		$settings = [];

		$reporter_properties = Base::get_properties_keys();

		array_push( $reporter_properties, 'category', 'name', 'class_name' );

		$settings['reporter_properties'] = $reporter_properties;

		$settings['reportFilePrefix'] = '';

		return $settings;
	}

	/**
	 * Get items.
	 *
	 * Utility method that receives an array with a needle and returns all the
	 * items that match the needle. If needle is not defined the entire haystack
	 * will be returned.
	 *
	 * @access protected
	 * @static
	 *
	 * @param array  $haystack An array of items.
	 * @param string $needle   Optional. Needle. Default is null.
	 *
	 * @return mixed The whole haystack or the needle from the haystack when requested.
	 */
	final protected static function get_items( array $haystack, $needle = null ) {
		if ( $needle ) {
			return isset( $haystack[ $needle ] ) ? $haystack[ $needle ] : null;
		}

		return $haystack;
	}
}

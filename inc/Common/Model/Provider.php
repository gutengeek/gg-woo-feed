<?php
namespace GG_Woo_Feed\Common\Model;

use GG_Woo_Feed\Common\Feed_Template;

class Provider {
	/**
	 * @var array
	 */
	private $templates;

	/**
	 * @var string
	 */
	private $provider;

	/**
	 * @var array
	 */
	private $template_raw;

	/**
	 * @var array
	 */
	private $template;

	/**
	 * @var array
	 */
	private $feed_types;

	/**
	 * @var bool
	 */
	private $is_default_template = false;

	/**
	 * Provider constructor.
	 *
	 * @param null $provider
	 */
	public function __construct( $provider = null ) {
		$this->set_feed_templates();
		$this->provider = $provider;
		$this->get_template_raw();

		$this->feed_types = [ 'xml', 'csv', 'txt' ];
	}

	/**
	 * Get template
	 *
	 * @return bool|array
	 */
	public function get_provider_template() {
		if ( ! is_null( $this->provider ) && array_key_exists( $this->provider, $this->templates ) ) {
			return $this->templates[ $this->provider ];
		}

		return false;
	}

	/**
	 * Get default template.
	 *
	 * @return array
	 */
	public function get_default_template() {
		return $this->templates['default'];
	}

	/**
	 * Get template raw.
	 *
	 * @return array|mixed
	 */
	public function get_template_raw() {
		if ( is_array( $this->template_raw ) ) {
			return $this->template_raw;
		}

		if ( ! is_null( $this->provider ) && array_key_exists( $this->provider, $this->templates ) ) {
			$this->template_raw = $this->templates[ $this->provider ];
		} else {
			$this->is_default_template = true;

			$this->template_raw = $this->templates['default'];
		}

		return $this->template_raw;
	}

	/**
	 *
	 * @return array
	 */
	public function get_template() {
		if ( is_array( $this->template ) ) {
			return $this->template;
		}
		$this->template = array_merge( $this->template_raw, [
			'provider' => $this->provider,
			'feed_type' => $this->get_feed_types( true ),
		] );
		$this->template = gg_woo_feed_parse_feed_queries( $this->template, 'create' );

		return $this->template;
	}

	/**
	 * Get feed types.
	 *
	 * @param bool $default get the default type
	 *
	 * @return string[]|string|false
	 */
	public function get_feed_types( $default = false ) {

		if ( false === $default ) {
			return $this->feed_types;
		}

		return 'xml';
	}

	/**
	 * Get name.
	 *
	 * @return string|null
	 */
	public function get_name() {
		return $this->provider;
	}

	/**
	 * Is default template?
	 *
	 * @return bool
	 */
	public function is_default_template() {
		return $this->is_default_template;
	}

	/**
	 * Set feed templates.
	 *
	 * @return void
	 */
	private function set_feed_templates() {
		$this->templates = Feed_Template::get_feed_templates();
	}
}

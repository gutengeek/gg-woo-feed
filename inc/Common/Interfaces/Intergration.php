<?php
namespace GG_Woo_Feed\Common\interfaces;

/**
 * Taxonomies Class
 *
 *  This Class Register all needed taxonomies for project.
 **/
interface Intergration {

	/**
	 * Category Taxonomy
	 *
	 *	Register Category Taxonomy related to Job post type.
	 *
	 * @since 1.0
	 *
	 * @return avoid
	 */
	public function register_admin_actions();

	/**
	 * Category Taxonomy
	 *
	 *	Register Category Taxonomy related to Job post type.
	 *
	 * @since 1.0
	 *
	 * @return avoid
	 */
	public function register_frontend_actions ();
}

<?php
/**
 * File containing the class Easy_Job_Portal_Admin.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles front admin page for Easy Job Portal.
 *
 * @since 1.0.0
 */
class Easy_Job_Portal_Admin {

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.26.0
	 */
	private static $instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since  1.26.0
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $wp_version;

		include_once dirname( __FILE__ ) . '/class-easy-job-portal-admin-notices.php';
		include_once dirname( __FILE__ ) . '/class-easy-job-portal-cpt.php';
		Easy_Job_Portal_CPT::instance();

		include_once dirname( __FILE__ ) . '/class-easy-job-portal-settings.php';
		include_once dirname( __FILE__ ) . '/class-easy-job-portal-writepanels.php';
		include_once dirname( __FILE__ ) . '/class-easy-job-portal-setup.php';

		$this->settings_page = Easy_Job_Portal_Settings::instance();

		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'current_screen', [ $this, 'conditional_includes' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 12 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
	}

	/**
	 * Set up actions during admin initialization.
	 */
	public function admin_init() {
		include_once dirname( __FILE__ ) . '/class-easy-job-portal-taxonomy-meta.php';
	}

	/**
	 * Include admin files conditionally.
	 */
	public function conditional_includes() {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}
		switch ( $screen->id ) {
			case 'options-permalink':
				include 'class-easy-job-portal-permalink-settings.php';
				break;
		}
	}

	/**
	 * Enqueues CSS and JS assets.
	 */
	public function admin_enqueue_scripts() {
		Easy_Job_Portal::register_select2_assets();

		$screen = get_current_screen();
		if ( in_array( $screen->id, apply_filters( 'easy_job_portal_admin_screen_ids', [ 'edit-job_listing', 'plugins', 'job_listing', 'job_listing_page_easy-job-portal-settings', 'job_listing_page_easy-job-portal-addons' ] ), true ) ) {
			wp_enqueue_style( 'jquery-ui' );
			wp_enqueue_style( 'select2' );
			wp_enqueue_style( 'easy_job_portal_admin_css', EASY_JOB_PLUGIN_URL . '/assets/css/admin.css', [], EASY_JOB_VERSION );
			wp_enqueue_script( 'easy-job-portal-datepicker' );
			wp_register_script( 'jquery-tiptip', EASY_JOB_PLUGIN_URL . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', [ 'jquery' ], EASY_JOB_VERSION, true );
			wp_enqueue_script( 'easy_job_portal_admin_js', EASY_JOB_PLUGIN_URL . '/assets/js/admin.min.js', [ 'jquery', 'jquery-tiptip', 'select2' ], EASY_JOB_VERSION, true );

			wp_localize_script(
				'easy_job_portal_admin_js',
				'easy_job_portal_admin_params',
				[
					'user_selection_strings' => [
						'no_matches'        => _x( 'No matches found', 'user selection', 'easy-job-portal' ),
						'ajax_error'        => _x( 'Loading failed', 'user selection', 'easy-job-portal' ),
						'input_too_short_1' => _x( 'Please enter 1 or more characters', 'user selection', 'easy-job-portal' ),
						'input_too_short_n' => _x( 'Please enter %qty% or more characters', 'user selection', 'easy-job-portal' ),
						'load_more'         => _x( 'Loading more results&hellip;', 'user selection', 'easy-job-portal' ),
						'searching'         => _x( 'Searching&hellip;', 'user selection', 'easy-job-portal' ),
					],
					'ajax_url'               => admin_url( 'admin-ajax.php' ),
					'search_users_nonce'     => wp_create_nonce( 'search-users' ),
				]
			);
		}

		wp_enqueue_style( 'easy_job_portal_admin_menu_css', EASY_JOB_PLUGIN_URL . '/assets/css/menu.css', [], EASY_JOB_VERSION );
	}

	/**
	 * Adds pages to admin menu.
	 */
	public function admin_menu() {
		add_submenu_page( 'edit.php?post_type=job_listing', __( 'Settings', 'easy-job-portal' ), __( 'Settings', 'easy-job-portal' ), 'manage_options', 'easy-job-portal-settings', [ $this->settings_page, 'output' ] );
	}

}

Easy_Job_Portal_Admin::instance();

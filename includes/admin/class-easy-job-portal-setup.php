<?php
/**
 * File containing the class Easy_Job_Portal_Setup.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles initial environment setup after plugin is first activated.
 *
 * @since 1.16.0
 */
class Easy_Job_Portal_Setup {

	private static $instance = null;

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
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 12 );
		add_action( 'admin_head', [ $this, 'admin_head' ] );

		if ( isset( $_GET['page'] ) && 'easy-job-portal-setup' === $_GET['page'] ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 12 );
		}
	}

	/**
	 * Adds setup link to admin dashboard menu briefly so the page callback is registered.
	 */
	public function admin_menu() {
		add_dashboard_page( __( 'Setup', 'easy-job-portal' ), __( 'Setup', 'easy-job-portal' ), 'manage_options', 'easy-job-portal-setup', [ $this, 'setup_page' ] );
	}

	/**
	 * Removes the setup link from admin dashboard menu so just the handler callback is registered.
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'easy-job-portal-setup' );
	}

	/**
	 * Enqueues scripts for setup page.
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'easy_job_portal_setup_css', EASY_JOB_PLUGIN_URL . '/assets/css/setup.css', [ 'dashicons' ], EASY_JOB_VERSION );
	}

	public function create_page( $title, $content, $option ) {
		$page_data = [
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => sanitize_title( $title ),
			'post_title'     => $title,
			'post_content'   => $content,
			'post_parent'    => 0,
			'comment_status' => 'closed',
		];
		$page_id   = wp_insert_post( $page_data );

		if ( $option ) {
			update_option( $option, $page_id );
		}
	}

	/**
	 * Handle request to the setup page.
	 */
	public function setup_page() {
		$usage_tracking = Easy_Job_Portal_Usage_Tracking::get_instance();
		$step           = ! empty( $_GET['step'] ) ? absint( $_GET['step'] ) : 1;

		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			// Handle step 1 (usage tracking).
			$enable = isset( $_POST['easy_job_portal_usage_tracking_enabled'] )
				&& '1' === $_POST['easy_job_portal_usage_tracking_enabled'];

			$nonce       = isset( $_POST['nonce'] ) ? wp_unslash( $_POST['nonce'] ) : null; // 

			$valid_nonce = wp_verify_nonce( $nonce, 'enable-usage-tracking' );

			if ( $valid_nonce ) {
				$usage_tracking->set_tracking_enabled( $enable );
				$usage_tracking->hide_tracking_opt_in();
			}

			// Handle step 2 -> step 3 (setting up pages).
			if ( 3 === $step && ! empty( $_POST ) ) {
				if (
					! isset( $_REQUEST['setup_wizard'] )
					|| false === wp_verify_nonce( wp_unslash( $_REQUEST['setup_wizard'] ), 'step_3' ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce should not be modified.
				) {
					wp_die( 'Error in nonce. Try again.', 'easy-job-portal' );
				}
				$create_pages    = isset( $_POST['easy-job-portal-create-page'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['easy-job-portal-create-page'] ) ) : [];
				$page_titles     = isset( $_POST['easy-job-portal-page-title'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['easy-job-portal-page-title'] ) ) : [];
				$pages_to_create = [
					'submit_job_form' => '[submit_job_form]',
					'job_dashboard'   => '[job_dashboard]',
					'jobs'            => '[jobs]',
					
					'register'            => '[register]',
				];

				foreach ( $pages_to_create as $page => $content ) {
					if ( ! isset( $create_pages[ $page ] ) || empty( $page_titles[ $page ] ) ) {
						continue;
					}
					$this->create_page( sanitize_text_field( $page_titles[ $page ] ), $content, 'easy_job_portal_' . $page . '_page_id' );
				}
			}
		}

		// Handle step 3 (from step 1 or 2).
		if ( 3 === $step ) {
			Easy_Job_Portal_Admin_Notices::remove_notice( Easy_Job_Portal_Admin_Notices::NOTICE_CORE_SETUP );
		}

		$this->output();
	}

	/**
	 * Usage tracking opt in text for setup page.
	 *
	 * Used in `views/html-admin-setup-opt-in-usage-tracking.php`
	 */
	private function opt_in_text() {
		return Easy_Job_Portal_Usage_Tracking::get_instance()->opt_in_checkbox_text();
	}

	/**
	 * Output opt-in checkbox if usage tracking isn't already enabled.
	 *
	 * Used in `views/html-admin-setup-step-1.php`
	 */
	private function maybe_output_opt_in_checkbox() {
		// Only show the checkbox if we aren't already opted in.
		$usage_tracking = Easy_Job_Portal_Usage_Tracking::get_instance();
		if ( ! $usage_tracking->get_tracking_enabled() ) {
			include dirname( __FILE__ ) . '/views/html-admin-setup-opt-in-usage-tracking.php';
		}
	}

	/**
	 * Displays setup page.
	 */
	public function output() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Input is used safely.
		$step = ! empty( $_GET['step'] ) ? absint( $_GET['step'] ) : 1;

		include dirname( __FILE__ ) . '/views/html-admin-setup-header.php';
		if ( 1 === $step ) {
			include dirname( __FILE__ ) . '/views/html-admin-setup-step-1.php';
		} elseif ( 2 === $step ) {
			include dirname( __FILE__ ) . '/views/html-admin-setup-step-2.php';
		} elseif ( 3 === $step ) {
			include dirname( __FILE__ ) . '/views/html-admin-setup-step-3.php';
		}
		include dirname( __FILE__ ) . '/views/html-admin-setup-footer.php';
	}
}

Easy_Job_Portal_Setup::instance();

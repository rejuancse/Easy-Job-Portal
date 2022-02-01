<?php
/**
 * File containing the class Easy_Job_Portal_Forms.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base class for all Easy Job Portal forms.
 *
 * @since 1.0.0
 */
class Easy_Job_Portal_Forms {

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
		add_action( 'init', [ $this, 'load_posted_form' ] );
	}

	/**
	 * If a form was posted, load its class so that it can be processed before display.
	 */
	public function load_posted_form() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Input is used safely.
		$easy_job_portal_form = ! empty( $_POST['easy_job_portal_form'] ) ? sanitize_title( wp_unslash( $_POST['easy_job_portal_form'] ) ) : false;

		if ( ! empty( $easy_job_portal_form ) ) {
			$this->load_form_class( $easy_job_portal_form );
		}
	}

	/**
	 * Load a form's class
	 *
	 * @param  string $form_name
	 * @return bool|Easy_Job_Portal_Form Class instance on success, false on failure.
	 */
	private function load_form_class( $form_name ) {
		if ( ! class_exists( 'Easy_Job_Portal_Form' ) ) {
			include 'abstracts/abstract-easy-job-portal-form.php';
		}

		// Now try to load the form_name.
		$form_class = 'Easy_Job_Portal_Form_' . str_replace( '-', '_', $form_name );
		$form_file  = EASY_JOB_PLUGIN_DIR . '/includes/forms/class-easy-job-portal-form-' . $form_name . '.php';

		if ( class_exists( $form_class ) ) {
			return call_user_func( [ $form_class, 'instance' ] );
		}

		if ( ! file_exists( $form_file ) ) {
			return false;
		}

		if ( ! class_exists( $form_class ) ) {
			include $form_file;
		}

		// Init the form.
		return call_user_func( [ $form_class, 'instance' ] );
	}

	/**
	 * Returns the form content.
	 *
	 * @param string $form_name
	 * @param array  $atts Optional passed attributes.
	 * @return string|null
	 */
	public function get_form( $form_name, $atts = [] ) {
		$form = $this->load_form_class( $form_name );
		if ( $form ) {
			ob_start();
			$form->output( $atts );
			return ob_get_clean();
		}
	}
}

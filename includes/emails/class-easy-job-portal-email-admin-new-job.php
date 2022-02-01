<?php
/**
 * File containing the class Easy_Job_Portal_Email_Admin_New_Job.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Email notification to administrator when a new job is submitted.
 *
 * @since 1.31.0
 * @extends Easy_Job_Portal_Email
 */
class Easy_Job_Portal_Email_Admin_New_Job extends Easy_Job_Portal_Email_Template {
	/**
	 * Get the unique email notification key.
	 *
	 * @return string
	 */
	public static function get_key() {
		return 'admin_new_job';
	}

	/**
	 * Get the friendly name for this email notification.
	 *
	 * @return string
	 */
	public static function get_name() {
		return __( 'Admin Notice of New Listing', 'easy-job-portal' );
	}

	/**
	 * Get the description for this email notification.
	 *
	 * @type abstract
	 * @return string
	 */
	public static function get_description() {
		return __( 'Send a notice to the site administrator when a new job is submitted on the frontend.', 'easy-job-portal' );
	}

	/**
	 * Get the email subject.
	 *
	 * @return string
	 */
	public function get_subject() {
		$args = $this->get_args();

		/**
		 * Job listing post object.
		 *
		 * @var WP_Post $job
		 */
		$job = $args['job_listing'];

		// translators: Placeholder %s is the job listing post title.
		return sprintf( __( 'New Job Listing Submitted: %s', 'easy-job-portal' ), $job->post_title );
	}

	/**
	 * Get `From:` address header value. Can be simple email or formatted `Firstname Lastname <email@example.com>`.
	 *
	 * @return string|bool Email from value or false to use WordPress' default.
	 */
	public function get_from() {
		return false;
	}

	/**
	 * Get array or comma-separated list of email addresses to send message.
	 *
	 * @return string|array
	 */
	public function get_to() {
		return get_option( 'admin_email', false );
	}

	/**
	 * Checks the arguments and returns whether the email notification is properly set up.
	 *
	 * @return bool
	 */
	public function is_valid() {
		$args = $this->get_args();
		return isset( $args['job_listing'] )
				&& $args['job_listing'] instanceof WP_Post
				&& $this->get_to();
	}
}

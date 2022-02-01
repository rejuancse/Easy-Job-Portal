<?php
/**
 * File containing the class Easy_Job_Portal_Email_Admin_Expiring_Job.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Email notification to the site administrator when a job is expiring.
 *
 * @since 1.31.0
 * @extends Easy_Job_Portal_Email
 */
class Easy_Job_Portal_Email_Admin_Expiring_Job extends Easy_Job_Portal_Email_Employer_Expiring_Job {
	/**
	 * Get the unique email notification key.
	 *
	 * @return string
	 */
	public static function get_key() {
		return 'admin_expiring_job';
	}

	/**
	 * Get the friendly name for this email notification.
	 *
	 * @return string
	 */
	public static function get_name() {
		return __( 'Admin Notice of Expiring Easy Job Portal', 'easy-job-portal' );
	}

	/**
	 * Get the description for this email notification.
	 *
	 * @type abstract
	 * @return string
	 */
	public static function get_description() {
		return __( 'Send notices to the site administrator before a job listing expires.', 'easy-job-portal' );
	}

	/**
	 * Get array or comma-separated list of email addresses to send message.
	 *
	 * @return string|array
	 */
	public function get_to() {
		return get_option( 'admin_email', false );
	}

}

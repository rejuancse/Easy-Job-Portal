<?php
/**
 * File containing the class Easy_Job_Portal_Addons.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the admin add-ons page.
 *
 * @since 1.1.0
 */
class Easy_Job_Portal_Addons {
	const DLJP_COM_PRODUCTS_API_BASE_URL = 'https://wpjobmanager.com/wp-json/wpjmcom-products/1.0';

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
	 * Call API to get DLJP add-ons
	 *
	 * @since  1.30.0
	 *
	 * @param  string $category
	 *
	 * @return array of add-ons.
	 */
	private function get_add_ons( $category = null ) {
		$raw_add_ons = wp_remote_get(
			add_query_arg( [ [ 'category' => $category ] ], self::DLJP_COM_PRODUCTS_API_BASE_URL . '/search' )
		);
		if ( ! is_wp_error( $raw_add_ons ) ) {
			$add_ons = json_decode( wp_remote_retrieve_body( $raw_add_ons ) )->products;
		}
		return $add_ons;
	}

	/**
	 * Get categories for the add-ons screen
	 *
	 * @since  1.30.0
	 *
	 * @return array of objects.
	 */
	private function get_categories() {
		$add_on_categories = get_transient( 'jm_wpjmcom_add_on_categories' );
		if ( false === ( $add_on_categories ) ) {
			$raw_categories = wp_safe_remote_get( self::DLJP_COM_PRODUCTS_API_BASE_URL . '/categories' );
			if ( ! is_wp_error( $raw_categories ) ) {
				$add_on_categories = json_decode( wp_remote_retrieve_body( $raw_categories ) );
				if ( $add_on_categories ) {
					set_transient( 'jm_wpjmcom_add_on_categories', $add_on_categories, WEEK_IN_SECONDS );
				}
			}
		}
		return apply_filters( 'easy_job_portal_add_on_categories', $add_on_categories );
	}

	/**
	 * Get messages for the add-ons screen
	 *
	 * @since  1.30.0
	 *
	 * @return array of objects.
	 */
	private function get_messages() {
		$add_on_messages = get_transient( 'jm_wpjmcom_add_on_messages' );
		if ( false === ( $add_on_messages ) ) {
			$raw_messages = wp_safe_remote_get(
				add_query_arg(
					[
						'version' => EASY_JOB_VERSION,
						'lang'    => get_locale(),
					],
					self::DLJP_COM_PRODUCTS_API_BASE_URL . '/messages'
				)
			);
			if ( ! is_wp_error( $raw_messages ) ) {
				$add_on_messages = json_decode( wp_remote_retrieve_body( $raw_messages ) );
				if ( $add_on_messages ) {
					set_transient( 'jm_wpjmcom_add_on_messages', $add_on_messages, WEEK_IN_SECONDS );
				}
			}
		}
		return apply_filters( 'easy_job_portal_add_on_messages', $add_on_messages );
	}

	/**
	 * Handles output of the reports page in admin.
	 */
	
}

return Easy_Job_Portal_Addons::instance();

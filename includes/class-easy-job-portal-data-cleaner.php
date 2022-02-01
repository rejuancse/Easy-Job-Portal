<?php
/**
 * File containing the class Easy_Job_Portal_Data_Cleaner.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Methods for cleaning up all plugin data.
 *
 * @author Automattic
 * @since 1.31.0
 */
class Easy_Job_Portal_Data_Cleaner {

	/**
	 * Custom post types to be deleted.
	 *
	 * @var $custom_post_types
	 */
	private static $custom_post_types = [
		'job_listing',
	];

	/**
	 * Taxonomies to be deleted.
	 *
	 * @var $taxonomies
	 */
	private static $taxonomies = [
		'job_listing_category',
		'job_listing_type',
	];

	/** Cron jobs to be unscheduled.
	 *
	 * @var $cron_jobs
	 */
	private static $cron_jobs = [
		'easy_job_portal_check_for_expired_jobs',
		'easy_job_portal_delete_old_previews',
		'easy_job_portal_email_daily_notices',
		'easy_job_portal_usage_tracking_send_usage_data',

		// Old cron jobs.
		'easy_job_portal_clear_expired_transients',
	];

	/**
	 * Options to be deleted.
	 *
	 * @var $options
	 */
	private static $options = [
		'wp_easy_job_portal_version',
		'easy_job_portal_installed_terms',
		'wpjm_permalinks',
		'easy_job_portal_permalinks',
		'easy_job_portal_helper',
		'easy_job_portal_date_format',
		'easy_job_portal_google_maps_api_key',
		'easy_job_portal_usage_tracking_enabled',
		'easy_job_portal_usage_tracking_opt_in_hide',
		'easy_job_portal_per_page',
		'easy_job_portal_hide_filled_positions',
		'easy_job_portal_hide_expired',
		'easy_job_portal_hide_expired_content',
		'easy_job_portal_enable_categories',
		'easy_job_portal_enable_default_category_multiselect',
		'easy_job_portal_category_filter_type',
		'easy_job_portal_enable_types',
		'easy_job_portal_multi_job_type',
		'easy_job_portal_user_requires_account',
		'easy_job_portal_enable_registration',
		'easy_job_portal_generate_username_from_email',
		'easy_job_portal_use_standard_password_setup_email',
		'easy_job_portal_registration_role',
		'easy_job_portal_submission_requires_approval',
		'easy_job_portal_user_can_edit_pending_submissions',
		'easy_job_portal_user_edit_published_submissions',
		'easy_job_portal_submission_duration',
		'easy_job_portal_allowed_application_method',
		'easy_job_portal_recaptcha_label',
		'easy_job_portal_recaptcha_site_key',
		'easy_job_portal_recaptcha_secret_key',
		'easy_job_portal_enable_recaptcha_job_submission',
		'easy_job_portal_submit_job_form_page_id',
		'easy_job_portal_job_dashboard_page_id',
		'easy_job_portal_jobs_page_id',
		'easy_job_portal_submit_page_slug',
		'easy_job_portal_job_dashboard_page_slug',
		'easy_job_portal_delete_data_on_uninstall',
		'easy_job_portal_email_admin_updated_job',
		'easy_job_portal_email_admin_new_job',
		'easy_job_portal_email_admin_expiring_job',
		'easy_job_portal_email_employer_expiring_job',
		'easy_job_portal_admin_notices',
		'widget_widget_featured_jobs',
		'widget_widget_recent_jobs',
	];

	/**
	 * Site options to be deleted.
	 *
	 * @var $site_options
	 */
	private static $site_options = [
		'easy_job_portal_helper',
	];

	/**
	 * Transient names (as MySQL regexes) to be deleted. The prefixes
	 * "_transient_" and "_transient_timeout_" will be prepended.
	 *
	 * @var $transients
	 */
	private static $transients = [
		'_easy_job_portal_activation_redirect', // Legacy transient that should still be removed.
		'get_job_listings-transient-version',
		'jm_.*',
	];

	/**
	 * Role to be removed.
	 *
	 * @var $role
	 */
	private static $role = 'employer';

	/**
	 * Capabilities to be deleted.
	 *
	 * @var $caps
	 */
	private static $caps = [
		'manage_job_listings',
		'edit_job_listing',
		'read_job_listing',
		'delete_job_listing',
		'edit_job_listings',
		'edit_others_job_listings',
		'publish_job_listings',
		'read_private_job_listings',
		'delete_job_listings',
		'delete_private_job_listings',
		'delete_published_job_listings',
		'delete_others_job_listings',
		'edit_private_job_listings',
		'edit_published_job_listings',
		'manage_job_listing_terms',
		'edit_job_listing_terms',
		'delete_job_listing_terms',
		'assign_job_listing_terms',
	];

	/**
	 * User meta key names to be deleted.
	 *
	 * @var array $user_meta_keys
	 */
	private static $user_meta_keys = [
		'_company_logo',
		'_company_name',
		'_company_website',
		'_company_tagline',
		'_company_twitter',
		'_company_video',
	];

	/**
	 * Cleanup all data.
	 *
	 * @access public
	 */
	public static function cleanup_all() {
		self::cleanup_custom_post_types();
		self::cleanup_taxonomies();
		self::cleanup_pages();
		self::cleanup_cron_jobs();
		self::cleanup_roles_and_caps();
		self::cleanup_transients();
		self::cleanup_user_meta();
		self::cleanup_options();
		self::cleanup_site_options();
	}

	/**
	 * Cleanup data for custom post types.
	 *
	 * @access private
	 */
	private static function cleanup_custom_post_types() {
		foreach ( self::$custom_post_types as $post_type ) {
			$items = get_posts(
				[
					'post_type'   => $post_type,
					'post_status' => 'any',
					'numberposts' => -1,
					'fields'      => 'ids',
				]
			);

			foreach ( $items as $item ) {
				wp_trash_post( $item );
			}
		}
	}

	/**
	 * Cleanup data for taxonomies.
	 *
	 * @access private
	 */
	private static function cleanup_taxonomies() {
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		foreach ( self::$taxonomies as $taxonomy ) {
			$terms = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT term_id, term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = %s",
					$taxonomy
				)
			);

			// Delete all data for each term.
			foreach ( $terms as $term ) {
				$wpdb->delete( $wpdb->term_relationships, [ 'term_taxonomy_id' => $term->term_taxonomy_id ] );
				$wpdb->delete( $wpdb->term_taxonomy, [ 'term_taxonomy_id' => $term->term_taxonomy_id ] );
				$wpdb->delete( $wpdb->terms, [ 'term_id' => $term->term_id ] );
				$wpdb->delete( $wpdb->termmeta, [ 'term_id' => $term->term_id ] );
			}

			if ( function_exists( 'clean_taxonomy_cache' ) ) {
				clean_taxonomy_cache( $taxonomy );
			}
		}

		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching
	}

	/**
	 * Cleanup data for pages.
	 *
	 * @access private
	 */
	private static function cleanup_pages() {
		// Trash the Submit Job page.
		$submit_job_form_page_id = get_option( 'easy_job_portal_submit_job_form_page_id' );
		if ( $submit_job_form_page_id ) {
			wp_trash_post( $submit_job_form_page_id );
		}

		// Trash the Job Dashboard page.
		$job_dashboard_page_id = get_option( 'easy_job_portal_job_dashboard_page_id' );
		if ( $job_dashboard_page_id ) {
			wp_trash_post( $job_dashboard_page_id );
		}

		// Trash the Jobs page.
		$jobs_page_id = get_option( 'easy_job_portal_jobs_page_id' );
		if ( $jobs_page_id ) {
			wp_trash_post( $jobs_page_id );
		}
	}

	/**
	 * Cleanup data for options.
	 *
	 * @access private
	 */
	private static function cleanup_options() {
		foreach ( self::$options as $option ) {
			delete_option( $option );
		}
	}

	/**
	 * Cleanup data for site options.
	 *
	 * @access private
	 */
	private static function cleanup_site_options() {
		foreach ( self::$site_options as $option ) {
			delete_site_option( $option );
		}
	}

	/**
	 * Cleanup transients from the database.
	 *
	 * @access private
	 */
	private static function cleanup_transients() {
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
		foreach ( [ '_transient_', '_transient_timeout_' ] as $prefix ) {
			foreach ( self::$transients as $transient ) {
				$wpdb->query(
					$wpdb->prepare(
						"DELETE FROM $wpdb->options WHERE option_name RLIKE %s",
						$prefix . $transient
					)
				);
			}
		}
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching
	}

	/**
	 * Cleanup data for roles and caps.
	 *
	 * @access private
	 */
	private static function cleanup_roles_and_caps() {
		global $wp_roles;

		// Remove caps from roles.
		$role_names = array_keys( $wp_roles->roles );
		foreach ( $role_names as $role_name ) {
			$role = get_role( $role_name );
			self::remove_all_easy_job_portal_caps( $role );
		}

		// Remove caps and role from users.
		$users = get_users( [] );
		foreach ( $users as $user ) {
			self::remove_all_easy_job_portal_caps( $user );
			$user->remove_role( self::$role );
		}

		// Remove role.
		remove_role( self::$role );
	}

	/**
	 * Helper method to remove DLJP caps from a user or role object.
	 *
	 * @param (WP_User|WP_Role) $object the user or role object.
	 */
	private static function remove_all_easy_job_portal_caps( $object ) {
		foreach ( self::$caps as $cap ) {
			$object->remove_cap( $cap );
		}
	}

	/**
	 * Cleanup user meta from the database.
	 *
	 * @access private
	 */
	private static function cleanup_user_meta() {
		global $wpdb;

		foreach ( self::$user_meta_keys as $meta_key ) {
			// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery -- Delete data across all users.
			$wpdb->delete( $wpdb->usermeta, [ 'meta_key' => $meta_key ] );
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		}
	}

	/**
	 * Cleanup cron jobs. Note that this should be done on deactivation, but
	 * doing it here as well for safety.
	 *
	 * @access private
	 */
	private static function cleanup_cron_jobs() {
		foreach ( self::$cron_jobs as $job ) {
			wp_clear_scheduled_hook( $job );
		}
	}
}

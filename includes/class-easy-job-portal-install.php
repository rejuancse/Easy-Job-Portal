<?php
/**
 * File containing the class Easy_Job_Portal_Install.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the installation of the Easy Job Portal plugin.
 *
 * @since 1.0.0
 */
class Easy_Job_Portal_Install {

	/**
	 * Installs Easy Job Portal.
	 */
	public static function install() {
		global $wpdb;

		self::init_user_roles();
		self::default_terms();

		$is_new_install = false;

		// Fresh installs should be prompted to set up their instance.
		if ( ! get_option( 'wp_easy_job_portal_version' ) ) {
			include_once EASY_JOB_PLUGIN_DIR . '/includes/admin/class-easy-job-portal-admin-notices.php';
			Easy_Job_Portal_Admin_Notices::add_notice( Easy_Job_Portal_Admin_Notices::NOTICE_CORE_SETUP );
			$is_new_install = true;
		}

		// Update featured posts ordering.
		if ( version_compare( get_option( 'wp_easy_job_portal_version', EASY_JOB_VERSION ), '1.22.0', '<' ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One time data update.
			$wpdb->query( "UPDATE {$wpdb->posts} p SET p.menu_order = 0 WHERE p.post_type='job_listing';" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One time data update.
			$wpdb->query( "UPDATE {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id SET p.menu_order = -1 WHERE pm.meta_key = '_featured' AND pm.meta_value='1' AND p.post_type='job_listing';" );
		}

		// Update default term meta with employment types.
		if ( version_compare( get_option( 'wp_easy_job_portal_version', EASY_JOB_VERSION ), '1.28.0', '<' ) ) {
			self::add_employment_types();
		}

		// Update legacy options.
		if ( false === get_option( 'easy_job_portal_submit_job_form_page_id', false ) && get_option( 'easy_job_portal_submit_page_slug' ) ) {
			$page_id = get_page_by_path( get_option( 'easy_job_portal_submit_page_slug' ) )->ID;
			update_option( 'easy_job_portal_submit_job_form_page_id', $page_id );
		}
		if ( false === get_option( 'easy_job_portal_job_dashboard_page_id', false ) && get_option( 'easy_job_portal_job_dashboard_page_slug' ) ) {
			$page_id = get_page_by_path( get_option( 'easy_job_portal_job_dashboard_page_slug' ) )->ID;
			update_option( 'easy_job_portal_job_dashboard_page_id', $page_id );
		}

		// Scheduled hook was removed in 1.33.4.
		if ( wp_next_scheduled( 'easy_job_portal_clear_expired_transients' ) ) {
			wp_clear_scheduled_hook( 'easy_job_portal_clear_expired_transients' );
		}

		if ( $is_new_install ) {
			$permalink_options                 = (array) json_decode( get_option( 'easy_job_portal_permalinks', '[]' ), true );
			$permalink_options['jobs_archive'] = '';
			update_option( 'easy_job_portal_permalinks', wp_json_encode( $permalink_options ) );
		}

		delete_transient( 'wp_easy_job_portal_addons_html' );
		update_option( 'wp_easy_job_portal_version', EASY_JOB_VERSION );
	}

	/**
	 * Initializes user roles.
	 */
	private static function init_user_roles() {
		$roles = wp_roles();

		if ( is_object( $roles ) ) {
			add_role(
				'employer',
				__( 'Employer', 'easy-job-portal' ),
				[
					'read'         => true,
					'edit_posts'   => false,
					'delete_posts' => false,
				]
			);

			$capabilities = self::get_core_capabilities();

			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$roles->add_cap( 'administrator', $cap );
				}
			}
		}
	}

	/**
	 * Returns capabilities.
	 *
	 * @return array
	 */
	private static function get_core_capabilities() {
		return [
			'core'        => [
				'manage_job_listings',
			],
			'job_listing' => [
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
			],
		];
	}

	/**
	 * Sets up the default Easy Job Portal terms.
	 */
	private static function default_terms() {
		if ( 1 === intval( get_option( 'easy_job_portal_installed_terms' ) ) ) {
			return;
		}

		$taxonomies = self::get_default_taxonomy_terms();
		foreach ( $taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $term => $meta ) {
				if ( ! get_term_by( 'slug', sanitize_title( $term ), $taxonomy ) ) {
					$tt_package = wp_insert_term( $term, $taxonomy );
					if ( is_array( $tt_package ) && isset( $tt_package['term_id'] ) && ! empty( $meta ) ) {
						foreach ( $meta as $meta_key => $meta_value ) {
							add_term_meta( $tt_package['term_id'], $meta_key, $meta_value );
						}
					}
				}
			}
		}

		update_option( 'easy_job_portal_installed_terms', 1 );
	}

	/**
	 * Default taxonomy terms to set up in Easy Job Portal.
	 *
	 * @return array Default taxonomy terms.
	 */
	private static function get_default_taxonomy_terms() {
		return [
			'job_listing_type' => [
				'Full Time'  => [
					'employment_type' => 'FULL_TIME',
				],
				'Part Time'  => [
					'employment_type' => 'PART_TIME',
				],
				'Temporary'  => [
					'employment_type' => 'TEMPORARY',
				],
				'Freelance'  => [
					'employment_type' => 'CONTRACTOR',
				],
				'Internship' => [
					'employment_type' => 'INTERN',
				],
			],
		];
	}

	/**
	 * Adds the employment type to default job types when updating from a previous Easy Job Portal version.
	 */
	private static function add_employment_types() {
		$taxonomies = self::get_default_taxonomy_terms();
		$terms      = $taxonomies['job_listing_type'];

		foreach ( $terms as $term => $meta ) {
			$term = get_term_by( 'slug', sanitize_title( $term ), 'job_listing_type' );
			if ( $term ) {
				foreach ( $meta as $meta_key => $meta_value ) {
					if ( ! get_term_meta( (int) $term->term_id, $meta_key, true ) ) {
						add_term_meta( (int) $term->term_id, $meta_key, $meta_value );
					}
				}
			}
		}
	}
}

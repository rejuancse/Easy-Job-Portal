<?php
defined( 'ABSPATH' ) || die();

require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'lib/WL_JP_Helper.php' );

class WL_JP_Setting {
	public static function add_settings_link( $links ) {
	    $settings_link = '<a href="' . menu_page_url( 'job_portal_settings', false ) . '">' . esc_html__( 'Settings', DJP_DOMAIN ) . '</a>';
	    array_push( $links, $settings_link );
	  	return $links;
	}

	public static function register_settings() {
		/* Register settings */
		$section_group = 'wljp';
		$page          = $section_group;
	    register_setting( $section_group, 'wljp_general', array( 'WL_JP_Setting', 'wljp_general_validate' ) );

		$settings_section_general = 'wljp_general';
		/* Create section of page */
		add_settings_section( $settings_section_general, esc_html__( 'General', DJP_DOMAIN ), '', $page );

		/* Add fields to section */
		add_settings_field( 'wljp_general_jobs_per_page', esc_html__( 'Number of jobs per page', DJP_DOMAIN ), array( 'WL_JP_Setting', 'wljp_general_jobs_per_page_input' ), $page, $settings_section_general );

		add_settings_field( 'wljp_general_admin_applications_per_page', esc_html__( 'Number of applications per page in admin panel', DJP_DOMAIN ), array( 'WL_JP_Setting', 'wljp_general_admin_applications_per_page_input' ), $page, $settings_section_general );

		add_settings_field( 'wljp_general_employee_jobs_applied_per_page', esc_html__( 'Number of jobs applied per page in employee panel', DJP_DOMAIN ), array( 'WL_JP_Setting', 'wljp_general_employee_jobs_applied_per_page_input' ), $page, $settings_section_general );


		add_settings_field( 'wljp_general_account_page_id', esc_html__( 'Account Page', DJP_DOMAIN ) . '<br><span id="wljp_job_portal_account_shortcode">[job_portal_account]</span> <span id="wljp_job_portal_account_shortcode_copy">' . esc_html__( 'Copy', DJP_DOMAIN ) . '</span>', array( 'WL_JP_Setting', 'wljp_general_account_page_id_input' ), $page, $settings_section_general );
	}

	public static function wljp_general_jobs_per_page_input() {
		$general_jobs_per_page = WL_JP_Helper::general_jobs_per_page();
		echo '<input type="number" min="0" step="1" id="wljp_general_jobs_per_page" name="wljp_general[jobs_per_page]" value="' . $general_jobs_per_page . '">';
	}

	public static function wljp_general_admin_applications_per_page_input() {
		$general_admin_applications_per_page = WL_JP_Helper::general_admin_applications_per_page();
		echo '<input type="number" min="0" step="1" id="wljp_general_admin_applications_per_page" name="wljp_general[admin_applications_per_page]" value="' . $general_admin_applications_per_page . '">';
	}

	public static function wljp_general_employee_jobs_applied_per_page_input() {
		$general_employee_jobs_applied_per_page = WL_JP_Helper::general_employee_jobs_applied_per_page();
		echo '<input type="number" min="0" step="1" id="wljp_general_employee_jobs_applied_per_page" name="wljp_general[employee_jobs_applied_per_page]" value="' . $general_employee_jobs_applied_per_page . '">';
	}

	public static function wljp_general_job_portal_page_id_input() {
		$general_job_portal_page_id  = WL_JP_Helper::general_job_portal_page_id();
		$general_job_portal_page_url = $general_job_portal_page_id ? get_permalink( $general_job_portal_page_id ) : home_url();
		wp_dropdown_pages( array(
			'name'     => 'wljp_general[job_portal_page_id]',
			'id'       => 'wljp_general_job_portal_page_id',
			'selected' => $general_job_portal_page_id
		) );
	}

	public static function wljp_general_account_page_id_input() {
		$general_account_page_id  = WL_JP_Helper::general_account_page_id();
		$general_account_page_url = $general_account_page_id ? get_permalink( $general_account_page_id ) : home_url();
		wp_dropdown_pages( array(
			'name'     => 'wljp_general[account_page_id]',
			'id'       => 'wljp_general_account_page_id',
			'selected' => $general_account_page_id
		) );
	}

	public static function wljp_general_validate( $input ) {
		$validated = array();
		$validated['jobs_per_page']               = isset( $input['jobs_per_page'] ) ? intval( $input['jobs_per_page'] ) : 15;
		$validated['admin_applications_per_page'] = isset( $input['admin_applications_per_page'] ) ? intval( $input['admin_applications_per_page'] ) : 15;
		$validated['employee_jobs_applied_per_page'] = isset( $input['employee_jobs_applied_per_page'] ) ? intval( $input['employee_jobs_applied_per_page'] ) : 5;
		$validated['job_portal_page_id']          = isset( $input['job_portal_page_id'] ) ? intval( $input['job_portal_page_id'] ) : 0;
		$validated['account_page_id']             = isset( $input['account_page_id'] ) ? intval( $input['account_page_id'] ) : 0;
		return $validated;
	}
} ?>
<?php
defined( 'ABSPATH' ) || die();

require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'lib/WL_JP_Helper.php' );

class WL_JP_Menu { 
	/* Add menu */
	public static function create_menu() {
		$role_array = array( 'administrator', 'editor' );
		$user = wp_get_current_user();
 		$roles = ( array ) $user->roles;
		$user_role = $roles[0];

		if ( in_array( $user_role, $role_array ) ) {
			$job_applications = add_submenu_page( 'edit.php?post_type=employee', esc_html__( 'Job Applications', DJP_DOMAIN ), esc_html__( 'Job Applications', DJP_DOMAIN ), $user_role, 'job_applications', array( 'WL_JP_Menu', 'job_applications' ) );
			add_action( 'admin_print_styles-' . $job_applications, array( 'WL_JP_Menu', 'job_applications_assets' ) );
		}

	}

	/* Dashboard submenu assets */
	public static function dashboard_assets() {
		/* Enqueue styles */
		wp_enqueue_style( 'wljp-bootstrap', EASY_JOB_PORTAL_PLUGIN_URL . '/assets/css/bootstrap.min.css' );
		wp_enqueue_style( 'font-awesome-5', EASY_JOB_PORTAL_PLUGIN_URL . '/assets/css/all.min.css' );
		wp_enqueue_style( 'wljp-admin', EASY_JOB_PORTAL_PLUGIN_URL . '/assets/css/wljp-admin.css' );

		/* Enqueue scripts */
		wp_enqueue_script( 'wljp-popper-js', EASY_JOB_PORTAL_PLUGIN_URL . '/assets/js/popper.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'wljp-bootstrap-js', EASY_JOB_PORTAL_PLUGIN_URL . '/assets/js/bootstrap.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'wljp-admin-js', EASY_JOB_PORTAL_PLUGIN_URL . '/assets/js/wljp-admin.js', array( 'jquery' ), true, true );
	}

	/* Settings submenu assets */ 
	public static function settings_assets() {
		/* Enqueue styles */
		wp_enqueue_style( 'wljp-admin', EASY_JOB_PORTAL_PLUGIN_URL . '/assets/css/wljp-admin.css' );
		wp_enqueue_style( 'toastr', EASY_JOB_PORTAL_PLUGIN_URL . '/assets/css/toastr.min.css' );

		/* Enqueue scripts */
		wp_enqueue_script( 'wljp-admin', EASY_JOB_PORTAL_PLUGIN_URL . '/assets/js/wljp-admin.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'toastr', EASY_JOB_PORTAL_PLUGIN_URL . '/assets/js/toastr.min.js', array( 'jquery' ), true, true );
	}

	/* Job applications submenu */
	public static function job_applications() {
		require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'admin/inc/views/wljp_job_applications.php' );
	}

	/* Job applications submenu assets */
	public static function job_applications_assets() {
		/* Enqueue styles */
		wp_enqueue_style( 'wljp-bootstrap', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/bootstrap.min.css' );
		wp_enqueue_style( 'font-awesome-5', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/all.min.css' );
		wp_enqueue_style( 'wljp-admin', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/wljp-admin.css' );

		/* Enqueue scripts */
		wp_enqueue_script( 'wljp-popper', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/popper.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'wljp-bootstrap', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/bootstrap.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'wljp-admin', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/wljp-admin.js', array( 'jquery' ), true, true );
	}
}
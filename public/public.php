<?php
defined( 'ABSPATH' ) || die();

require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'lib/WL_JP_Helper.php' );
require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'public/inc/WL_JP_Language.php' );
require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'public/inc/WL_JP_Job_Front.php' );
require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'public/inc/WL_JP_Employee_Front.php' );
require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'public/inc/WL_JP_Shortcode.php' );
require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'public/inc/WL_JP_User.php' );

/* Load translation */
add_action( 'plugins_loaded', array( 'WL_JP_Language', 'load_translation' ) );

/* Register post types */
// add_action( 'init', array( 'WL_JP_Job_Front', 'register_job_post_type' ) );
add_action( 'init', array( 'WL_JP_Employee_Front', 'register_employee_post_type' ) );

/* Flush rewrite rule if flag is set */
add_action( 'init', array( 'WL_JP_Helper', 'flush_rewrite_rules_maybe' ), 20 );


/* Include templates */
add_filter( 'single_template', array( 'WL_JP_Job_Front', 'single_template' ) );

/* Enqueue scripts and styles */
add_action( 'wp_enqueue_scripts', array( 'WL_JP_Job_Front', 'enqueue_scripts_styles' ) );

/* Shortcodes */
add_shortcode( 'job_portal', array( 'WL_JP_Shortcode', 'job_portal' ) );
add_shortcode( 'job_portal_account', array( 'WL_JP_Shortcode', 'job_portal_account' ) );

/* Shortcode Assets */
add_action( 'wp_enqueue_scripts', array( 'WL_JP_Shortcode', 'shortcode_assets' ) );

/* Action to signup user */
add_action( 'admin_post_nopriv_wljp-signup', array( 'WL_JP_User', 'signup' ) );

/* Action to login user */
add_action( 'admin_post_nopriv_wljp-login', array( 'WL_JP_User', 'login' ) );

/* Action to update account settings */
add_action( 'admin_post_wljp-account', array( 'WL_JP_User', 'update_account_settings' ) );

/* Action to register cv */
add_action( 'admin_post_wljp-cv', array( 'WL_JP_Employee_Front', 'register_cv' ) );

/* Action to update cv */
add_action( 'admin_post_wljp-cv-update', array( 'WL_JP_Employee_Front', 'update_cv' ) );

/* Action to delete cv */
add_action( 'admin_post_wljp-cv-delete', array( 'WL_JP_Employee_Front', 'delete_cv' ) );

/* Action to apply job */
add_action( 'admin_post_wljp-job-apply', array( 'WL_JP_Employee_Front', 'apply_to_job' ) );
?>
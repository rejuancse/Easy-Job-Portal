<?php
/**
* Plugin Name: Easy Job Portal
* Description: Manage Job listings from the WordPress admin panel and allow users to post jobs.
* Version: 1.0.0
* Author: Rejuan Ahamed
* Text Domain: easy-job-portal
* Domain Path: /languages/
*
* @package easy-job-portal
*/

if ( ! defined( 'DJP_DOMAIN' ) ) {
    define( 'DJP_DOMAIN', 'easy-job-portal' );
}

# Define
define( 'EASY_JOB_VERSION', '1.0.0' );
define( 'EASY_JOB_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'EASY_JOB_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
define( 'EASY_JOB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// EASY_JOB_PORTAL_PLUGIN_URL
if ( ! defined( 'EASY_JOB_PORTAL_PLUGIN_URL' ) ) {
    define( 'EASY_JOB_PORTAL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'EASY_JOB_PORTAL_PLUGIN_DIR_PATH' ) ) {
    define( 'EASY_JOB_PORTAL_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'EASY_JOB_PORTAL_PLUGIN_BASE_NAME' ) ) {
    define( 'EASY_JOB_PORTAL_PLUGIN_BASE_NAME', plugin_basename( __FILE__ ) );
}

require_once dirname( __FILE__ ) . '/includes/easy-job-portal-dependency-checker.php';
if ( ! Easy_Job_Portal_Dependency_Checker::check_dependencies() ) {
	return;
}
require_once dirname( __FILE__ ) . '/includes/easy-job-portal.php';

function DLJP() { 
	return Easy_Job_Portal::instance();
}
$GLOBALS['easy_job_portal'] = DLJP();

# Activation
register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( DLJP(), 'activate' ) );

# Deactivation.
register_deactivation_hook( __FILE__, array( DLJP(), 'unschedule_cron_jobs' ) );
register_deactivation_hook( __FILE__, array( DLJP(), 'usage_tracking_cleanup' ) );


# Add CSS for Frontend
add_action( 'wp_enqueue_scripts', 'easy_jobportal_style' );
if(!function_exists('easy_jobportal_style')):
    function easy_jobportal_style(){
        wp_enqueue_script('custom',plugins_url('assets/js/custom.js',__FILE__), array('jquery'));

        # For Ajax URL
        global $wp;
        wp_localize_script( 'custom', 'ajax_djp', array(
            'ajaxurl'           => admin_url( 'admin-ajax.php' ),
            'redirecturl'       => home_url($wp->request),
            'home_url'           => home_url(),
            'loadingmessage'    => __('Sending user info, please wait...','easy-job-portal')
        ));
    }
endif;



include_once( 'templates/login.php' );
# wp load login modal
function load_login_modal() {
    include_once( 'templates/login-modal.php' );
}
add_action( 'wp_footer', 'load_login_modal' );




final class WL_JP_Jobs_Portal {
    private static $instance = null;

    private function __construct() {
        $this->initialize_hooks();
        $this->setup_database();
    }

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initialize_hooks() {
        if ( is_admin() ) {
            require_once( 'admin/admin.php' );
        }
        require_once( 'public/public.php' );
    }

    private function setup_database() {
        require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'admin/inc/WL_JP_Database.php' );
        register_activation_hook( __FILE__, array( 'WL_JP_Database', 'activation' ) );
        register_deactivation_hook( __FILE__, array( 'WL_JP_Database', 'deactivation' ) );
        register_uninstall_hook( __FILE__, array( 'WL_JP_Database', 'deactivation' ) );
    }
}
WL_JP_Jobs_Portal::get_instance(); 


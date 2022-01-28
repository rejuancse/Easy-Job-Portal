<?php
/**
 * Plugin Name:       Easy Job Portal
 * Description:       Manage Job listings from the WordPress admin panel and allow users to post jobs.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rejuan Ahamed
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages/
 * 
 * @package easy-job-portal
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The main plugin class
 */
final class EasyJobPortal {

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0';

    /**
     * Class construcotr
     */
    private function __construct() {
        $this->define_constants();

        register_activation_hook( __FILE__, [ $this, 'activate' ] );

        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
    }

    /**
     * Initializes a singleton instance
     *
     * @return \EasyJobPortal
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'JOB_PORTAL_VERSION', self::version );
        define( 'JOB_PORTAL_FILE', __FILE__ );
        define( 'JOB_PORTAL_PATH', __DIR__ );
        define( 'JOB_PORTAL_URL', plugins_url( '', JOB_PORTAL_FILE ) );
        define( 'JOB_PORTAL_ASSETS', JOB_PORTAL_URL . '/assets' );
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {

        if ( is_admin() ) {

        } else {

        }

    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate() {
        $installed = get_option( 'job_portal_installed' );

        if ( ! $installed ) {
            update_option( 'job_portal_installed', time() );
        }

        update_option( 'job_portal_version', JOB_PORTAL_VERSION );
    }
}

/**
 * Initializes the main plugin
 *
 * @return \EasyJobPortal
 */
function EasyJobPortal() {
    return EasyJobPortal::init();
}

// kick-off the plugin
EasyJobPortal();
<?php
defined( 'ABSPATH' ) || die();

require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'lib/WL_JP_Helper.php' );

class WL_JP_Job_Front {
	
	public static function single_template( $template ) {
	    global $post;
		global $wp;
	    if ( $post->post_type == 'job_listing' ) {
        	return EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'public/inc/templates/single-job.php';
	    }
	    return $template;
	}

	/**
	 * Enqueue scripts and styles to job page templates
	 * @return void
	 */
	public static function enqueue_scripts_styles( ) {
		self::enqueue_scripts_single_template();
	}

	/**
	 * Enqueue scripts and styles to single job page template
	 * @return void
	 */
	private static function enqueue_scripts_single_template() {
		if ( is_single() && get_post_type() == 'job_listing' ) {
			self::enqueue_libraries();
		}
	}

	/**
	 * Enqueue scripts and styles
	 * @return void
	 */
	private static function enqueue_libraries() {
		/* Enqueue styles */
		wp_enqueue_style( 'bootstrap', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/bootstrap.min.css' );
		wp_enqueue_style( 'font-awesome', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/all.min.css' );
		wp_enqueue_style( 'toastr', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/toastr.min.css' );
		wp_enqueue_style( 'wljp-public', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/wljp-public.css' );

		/* Enqueue scripts */
		wp_enqueue_script( 'jquery-form' );
		wp_enqueue_script( 'popper', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/popper.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'bootstrap', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/bootstrap.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'toastr', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/toastr.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'wljp-moment-js', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/moment.min.js', array(), '', true );
		wp_enqueue_script( 'wljp-public-js', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/wljp-public.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'wljp-public-ajax-js', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/wljp-public-ajax.js', array( 'jquery' ), true, true );
		wp_localize_script( 'wljp-public-ajax-js', 'WLJPAjax', array( 'security' => wp_create_nonce( 'wljp' ) ) );
		wp_localize_script( 'wljp-public-ajax-js', 'wljpajaxurl', array( esc_url( admin_url( 'admin-post.php' ) ) ) );
		wp_localize_script( 'wljp-public-ajax-js', 'WLJPAdminUrl', array( admin_url() ) );

	}
}
?>
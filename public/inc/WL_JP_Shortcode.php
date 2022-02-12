<?php
defined( 'ABSPATH' ) || die();

class WL_JP_Shortcode {

	/**
	* Add job_portal_account shortcode
	* @param  array $attr
	* @return void
	*/
	public static function job_portal_account( $attr ) {
		ob_start();
		require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'public/inc/views/wljp_job_portal_account.php' );
		return ob_get_clean();
	}

	/**
	 * Enqueue shortcode assets
	 * @return void
	 */
	public static function shortcode_assets() {
		global $post;
		if ( is_a( $post, 'WP_Post' ) ) {
			if ( has_shortcode( $post->post_content, 'job_portal_account' ) ) {
				/* Enqueue styles */
				wp_enqueue_style( 'wljp-bootstrap', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/bootstrap.min.css' );
				wp_enqueue_style( 'font-awesome-5', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/all.min.css' );
				wp_enqueue_style( 'toastr', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/toastr.min.css' );
				wp_enqueue_style( 'fSelect', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/fSelect.css' );
				wp_enqueue_style( 'wljp-public', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/wljp-public.css' );

				/* Enqueue scripts */
				wp_enqueue_script( 'jquery-form' );
				wp_enqueue_script( 'wljp-popper', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/popper.min.js', array( 'jquery' ), true, true );
				wp_enqueue_script( 'wljp-bootstrap-js', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/bootstrap.min.js', array( 'jquery' ), true, true );
				wp_enqueue_script( 'toastr', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/toastr.min.js', array( 'jquery' ), true, true );
				wp_enqueue_script( 'wljp-moment-js', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/moment.min.js', array(), '', true );
				wp_enqueue_script( 'fSelect', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/fSelect.js', array( 'jquery' ), true, true );
				wp_enqueue_script( 'wljp-public-js', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/wljp-public.js', array( 'jquery' ), true, true );
				wp_enqueue_script( 'wljp-public-ajax-js', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/wljp-public-ajax.js', array( 'jquery' ), true, true );
				wp_localize_script( 'wljp-public-ajax-js', 'WLJPAjax', array( 'security' => wp_create_nonce( 'wljp' ) ) );
				wp_localize_script( 'wljp-public-ajax-js', 'wljpajaxurl', array( esc_url( admin_url( 'admin-post.php' ) ) ) );
				wp_localize_script( 'wljp-public-ajax-js', 'WLJPAdminUrl', array( admin_url() ) );
			}
		}
	}
}
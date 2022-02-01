<?php
/**
 * File containing the class Easy_Job_Portal.
 *
 * @package easy-job-portal
 * @since   1.33.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles core plugin hooks and action setup.
 *
 * @since 1.0.0
 */
class Easy_Job_Portal {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.26.0
	 */
	private static $instance = null;

	/**
	 * Main Easy Job Portal Instance.
	 *
	 * Ensures only one instance of Easy Job Portal is loaded or can be loaded.
	 *
	 * @since  1.26.0
	 * @static
	 * @see DLJP()
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Includes.
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-install.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-post-types.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-ajax.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-shortcodes.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-api.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-forms.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-geocode.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-blocks.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-cache-helper.php';
		
		include_once EASY_JOB_PLUGIN_DIR . '/includes/abstracts/abstract-easy-job-portal-email.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/abstracts/abstract-easy-job-portal-email-template.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-email-notifications.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-data-exporter.php';

		if ( is_admin() ) {
			include_once EASY_JOB_PLUGIN_DIR . '/includes/admin/class-easy-job-portal-admin.php';
		}

		

		// Init classes.
		$this->forms      = Easy_Job_Portal_Forms::instance();
		$this->post_types = Easy_Job_Portal_Post_Types::instance();

		// Schedule cron jobs.
		self::maybe_schedule_cron_jobs();

		// Switch theme.
		add_action( 'after_switch_theme', [ 'Easy_Job_Portal_Ajax', 'add_endpoint' ], 10 );
		add_action( 'after_switch_theme', [ $this->post_types, 'register_post_types' ], 11 );
		add_action( 'after_switch_theme', 'flush_rewrite_rules', 15 );

		// Actions.
		add_action( 'after_setup_theme', [ $this, 'load_plugin_textdomain' ] );
		add_action( 'after_setup_theme', [ $this, 'include_template_functions' ], 11 );
		add_action( 'widgets_init', [ $this, 'widgets_init' ] );
		add_action( 'wp_loaded', [ $this, 'register_shared_assets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_scripts' ] );
		add_action( 'wp_footer', [ $this, 'maybe_localize_jquery_ui_datepicker' ], 1 );
		add_action( 'admin_init', [ $this, 'updater' ] );
		add_action( 'admin_init', [ $this, 'add_privacy_policy_content' ] );
		add_action( 'wp_logout', [ $this, 'cleanup_job_posting_cookies' ] );
		add_action( 'init', [ 'Easy_Job_Portal_Email_Notifications', 'init' ] );
		add_action( 'rest_api_init', [ $this, 'rest_init' ] );

		// Filters.
		add_filter( 'wp_privacy_personal_data_exporters', [ 'Easy_Job_Portal_Data_Exporter', 'register_wpjm_user_data_exporter' ] );

		add_action( 'init', [ $this, 'usage_tracking_init' ] );

		// Defaults for DLJP core actions.
		add_action( 'wpjm_notify_new_user', 'wp_easy_job_portal_notify_new_user', 10, 2 );
	}

	/**
	 * Performs plugin activation steps.
	 */
	public function activate() {
		Easy_Job_Portal_Ajax::add_endpoint();
		unregister_post_type( 'job_listing' );
		add_filter( 'pre_option_easy_job_portal_enable_types', '__return_true' );
		$this->post_types->register_post_types();
		remove_filter( 'pre_option_easy_job_portal_enable_types', '__return_true' );
		Easy_Job_Portal_Install::install();
		flush_rewrite_rules();
	}

	/**
	 * Handles tasks after plugin is updated.
	 */
	public function updater() {
		if ( version_compare( EASY_JOB_VERSION, get_option( 'wp_easy_job_portal_version' ), '>' ) ) {
			Easy_Job_Portal_Install::install();

			flush_rewrite_rules();
		}
	}

	/**
	 * Adds Privacy Policy suggested content.
	 */
	public function add_privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		$content = sprintf(
			// translators: Placeholders %1$s and %2$s are the names of the two cookies used in Easy Job Portal.
			__(
				'This site adds the following cookies to help users resume job submissions that they
				have started but have not completed: %1$s and %2$s',
				'easy-job-portal'
			),
			'<code>easy-job-portal-submitting-job-id</code>',
			'<code>easy-job-portal-submitting-job-key</code>'
		);

		wp_add_privacy_policy_content(
			'Easy Job Portal',
			wp_kses_post( wpautop( $content, false ) )
		);
	}

	/**
	 * Loads textdomain for plugin.
	 */
	public function load_plugin_textdomain() {
		load_textdomain( 'easy-job-portal', WP_LANG_DIR . '/easy-job-portal/easy-job-portal-' . apply_filters( 'plugin_locale', get_locale(), 'easy-job-portal' ) . '.mo' );
		load_plugin_textdomain( 'easy-job-portal', false, EASY_JOB_PLUGIN_DIR . '/languages/' );
	}

	/**
	 * Loads plugin's core helper template functions.
	 */
	public function include_template_functions() {
		include_once EASY_JOB_PLUGIN_DIR . '/easy-job-portal-deprecated.php';
		include_once EASY_JOB_PLUGIN_DIR . '/easy-job-portal-functions.php';
		include_once EASY_JOB_PLUGIN_DIR . '/easy-job-portal-template.php';
	}

	/**
	 * Loads the REST API functionality.
	 */
	public function rest_init() {
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-rest-api.php';
		Easy_Job_Portal_REST_API::init();
	}

	/**
	 * Loads plugin's widgets.
	 */
	public function widgets_init() {
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-widget.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/widgets/class-easy-job-portal-widget-recent-jobs.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/widgets/class-easy-job-portal-widget-featured-jobs.php';
	}

	/**
	 * Initialize the Usage Tracking system.
	 */
	public function usage_tracking_init() {
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-usage-tracking.php';
		include_once EASY_JOB_PLUGIN_DIR . '/includes/class-easy-job-portal-usage-tracking-data.php';

		Easy_Job_Portal_Usage_Tracking::get_instance()->set_callback(
			[ 'Easy_Job_Portal_Usage_Tracking_Data', 'get_usage_data' ]
		);

		if ( is_admin() ) {
			Easy_Job_Portal_Usage_Tracking::get_instance()->schedule_tracking_task();
		}
	}

	/**
	 * Cleanup the Usage Tracking system for plugin deactivation.
	 */
	public function usage_tracking_cleanup() {
		Easy_Job_Portal_Usage_Tracking::get_instance()->unschedule_tracking_task();
	}

	/**
	 * Schedule cron jobs for DLJP events.
	 */
	public static function maybe_schedule_cron_jobs() {
		if ( ! wp_next_scheduled( 'easy_job_portal_check_for_expired_jobs' ) ) {
			wp_schedule_event( time(), 'hourly', 'easy_job_portal_check_for_expired_jobs' );
		}
		if ( ! wp_next_scheduled( 'easy_job_portal_delete_old_previews' ) ) {
			wp_schedule_event( time(), 'daily', 'easy_job_portal_delete_old_previews' );
		}
		if ( ! wp_next_scheduled( 'easy_job_portal_email_daily_notices' ) ) {
			wp_schedule_event( time(), 'daily', 'easy_job_portal_email_daily_notices' );
		}
	}

	/**
	 * Unschedule cron jobs. This is run on plugin deactivation.
	 */
	public static function unschedule_cron_jobs() {
		wp_clear_scheduled_hook( 'easy_job_portal_check_for_expired_jobs' );
		wp_clear_scheduled_hook( 'easy_job_portal_delete_old_previews' );
		wp_clear_scheduled_hook( 'easy_job_portal_email_daily_notices' );
	}

	/**
	 * Cleanup job posting cookies.
	 */
	public function cleanup_job_posting_cookies() {
		if ( isset( $_COOKIE['easy-job-portal-submitting-job-id'] ) ) {
			setcookie( 'easy-job-portal-submitting-job-id', '', 0, COOKIEPATH, COOKIE_DOMAIN, false );
		}
		if ( isset( $_COOKIE['easy-job-portal-submitting-job-key'] ) ) {
			setcookie( 'easy-job-portal-submitting-job-key', '', 0, COOKIEPATH, COOKIE_DOMAIN, false );
		}
	}

	/**
	 * Registers assets used in both the frontend and WP admin.
	 */
	public function register_shared_assets() {
		global $wp_scripts;

		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
		wp_register_style( 'jquery-ui', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.min.css', [], $jquery_version );

		// Register datepicker JS. It will be enqueued if needed when a date field is used.
		wp_register_script( 'easy-job-portal-datepicker', EASY_JOB_PLUGIN_URL . '/assets/js/datepicker.min.js', [ 'jquery', 'jquery-ui-datepicker' ], EASY_JOB_VERSION, true );
	}

	/**
	 * Registers select2 assets when needed.
	 */
	public static function register_select2_assets() {
		wp_register_script( 'select2', EASY_JOB_PLUGIN_URL . '/assets/js/select2/select2.full.min.js', [ 'jquery' ], '4.0.10', false );
		wp_register_style( 'select2', EASY_JOB_PLUGIN_URL . '/assets/js/select2/select2.min.css', [], '4.0.10' );
	}

	/**
	 * WordPress localizes this script late in `wp_head`. We sometimes enqueue the datepicker later on.
	 *
	 * @access private
	 * @since 1.34.1
	 */
	public function maybe_localize_jquery_ui_datepicker() {
		// Check if this data has already been added. Prevents outputting localization data multiple times.
		if ( wp_scripts()->get_data( 'jquery-ui-datepicker', 'after' ) ) {
			return;
		}

		wp_localize_jquery_ui_datepicker();
	}

	/**
	 * Registers and enqueues scripts and CSS.
	 *
	 * Note: For enhanced select, 1.32.0 moved to Select2. Chosen is currently packaged but will be removed in an
	 * upcoming release.
	 */
	public function frontend_scripts() {
		$ajax_url         = Easy_Job_Portal_Ajax::get_endpoint();
		$ajax_filter_deps = [ 'jquery', 'jquery-deserialize' ];
		$ajax_data        = [
			'ajax_url'                => $ajax_url,
			'is_rtl'                  => is_rtl() ? 1 : 0,
			'i18n_load_prev_listings' => __( 'Load previous listings', 'easy-job-portal' ),
		];

		/**
		 * Retrieves the current language for use when caching requests.
		 *
		 * @since 1.26.0
		 *
		 * @param string|null $lang
		 */
		$ajax_data['lang'] = apply_filters( 'wpjm_lang', null );

		$enhanced_select_shortcodes   = [ 'submit_job_form', 'job_dashboard', 'jobs' ];
		$enhanced_select_used_on_page = has_wpjm_shortcode( null, $enhanced_select_shortcodes );

		/**
		 * Set the constant `JOB_MANAGER_DISABLE_CHOSEN_LEGACY_COMPAT` to true to test for future behavior once
		 * this legacy code is removed and `chosen` is no longer packaged with the plugin.
		 */
		if ( ! defined( 'JOB_MANAGER_DISABLE_CHOSEN_LEGACY_COMPAT' ) || ! JOB_MANAGER_DISABLE_CHOSEN_LEGACY_COMPAT ) {
			if ( is_wpjm_taxonomy() || is_wpjm_job_listing() || is_wpjm_page() ) {
				$enhanced_select_used_on_page = true;
			}

			// Register the script for dependencies that still require it.
			if ( ! wp_script_is( 'chosen', 'registered' ) ) {
				wp_register_script( 'chosen', EASY_JOB_PLUGIN_URL . '/assets/js/jquery-chosen/chosen.jquery.min.js', [ 'jquery' ], '1.1.0', true );
				wp_register_style( 'chosen', EASY_JOB_PLUGIN_URL . '/assets/css/chosen.css', [], '1.1.0' );
			}

			// Backwards compatibility for third-party themes/plugins while they transition to Select2.
			wp_localize_script(
				'chosen',
				'easy_job_portal_chosen_multiselect_args',
				apply_filters(
					'easy_job_portal_chosen_multiselect_args',
					[
						'search_contains' => true,
					]
				)
			);

			/**
			 * Filter the use of the deprecated chosen library. Themes and plugins should migrate to Select2. This will be
			 * removed in an upcoming major release.
			 *
			 * @since 1.19.0
			 * @deprecated 1.32.0 Migrate to easy_job_portal_select2_enabled and enable only on pages that need it.
			 *
			 * @param bool $chosen_used_on_page
			 */
			if ( apply_filters( 'easy_job_portal_chosen_enabled', false ) ) {
				_deprecated_hook( 'easy_job_portal_chosen_enabled', '1.32.0', 'easy_job_portal_select2_enabled' );

				// Assume if this filter returns true that the current page should have the multi-select scripts.
				$enhanced_select_used_on_page = true;

				wp_enqueue_script( 'chosen' );
				wp_enqueue_style( 'chosen' );
			}
		}

		/**
		 * Filter the use of the enhanced select.
		 *
		 * Note: Don't depend on `select2` being registered/enqueued in customizations.
		 *
		 * @since 1.32.0
		 *
		 * @param bool $enhanced_select_used_on_page Defaults to only when there are known shortcodes on the page.
		 */
		if ( apply_filters( 'easy_job_portal_enhanced_select_enabled', $enhanced_select_used_on_page ) ) {
			self::register_select2_assets();
			wp_register_script( 'easy-job-portal-term-multiselect', EASY_JOB_PLUGIN_URL . '/assets/js/term-multiselect.min.js', [ 'jquery', 'select2' ], EASY_JOB_VERSION, true );
			wp_register_script( 'easy-job-portal-multiselect', EASY_JOB_PLUGIN_URL . '/assets/js/multiselect.min.js', [ 'jquery', 'select2' ], EASY_JOB_VERSION, true );
			wp_enqueue_style( 'select2' );

			$ajax_filter_deps[] = 'select2';

			$select2_args = [];
			if ( is_rtl() ) {
				$select2_args['dir'] = 'rtl';
			}

			$select2_args['width'] = '100%';

			wp_localize_script(
				'select2',
				'easy_job_portal_select2_args',
				apply_filters( 'easy_job_portal_select2_args', $select2_args )
			);
		}

		if ( easy_job_portal_user_can_upload_file_via_ajax() ) {
			wp_register_script( 'jquery-iframe-transport', EASY_JOB_PLUGIN_URL . '/assets/js/jquery-fileupload/jquery.iframe-transport.js', [ 'jquery' ], '10.1.0', true );
			wp_register_script( 'jquery-fileupload', EASY_JOB_PLUGIN_URL . '/assets/js/jquery-fileupload/jquery.fileupload.js', [ 'jquery', 'jquery-iframe-transport', 'jquery-ui-widget' ], '10.1.0', true );
			wp_register_script( 'easy-job-portal-ajax-file-upload', EASY_JOB_PLUGIN_URL . '/assets/js/ajax-file-upload.min.js', [ 'jquery', 'jquery-fileupload' ], EASY_JOB_VERSION, true );

			ob_start();
			get_easy_job_portal_template(
				'form-fields/uploaded-file-html.php',
				[
					'name'      => '',
					'value'     => '',
					'extension' => 'jpg',
				]
			);
			$js_field_html_img = ob_get_clean();

			ob_start();
			get_easy_job_portal_template(
				'form-fields/uploaded-file-html.php',
				[
					'name'      => '',
					'value'     => '',
					'extension' => 'zip',
				]
			);
			$js_field_html = ob_get_clean();

			wp_localize_script(
				'easy-job-portal-ajax-file-upload',
				'easy_job_portal_ajax_file_upload',
				[
					'ajax_url'               => $ajax_url,
					'js_field_html_img'      => esc_js( str_replace( "\n", '', $js_field_html_img ) ),
					'js_field_html'          => esc_js( str_replace( "\n", '', $js_field_html ) ),
					'i18n_invalid_file_type' => esc_html__( 'Invalid file type. Accepted types:', 'easy-job-portal' ),
				]
			);
		}

		wp_register_script( 'jquery-deserialize', EASY_JOB_PLUGIN_URL . '/assets/js/jquery-deserialize/jquery.deserialize.js', [ 'jquery' ], '1.2.1', true );
		wp_register_script( 'easy-job-portal-ajax-filters', EASY_JOB_PLUGIN_URL . '/assets/js/ajax-filters.min.js', $ajax_filter_deps, EASY_JOB_VERSION, true );
		wp_register_script( 'easy-job-portal-job-dashboard', EASY_JOB_PLUGIN_URL . '/assets/js/job-dashboard.min.js', [ 'jquery' ], EASY_JOB_VERSION, true );
		wp_register_script( 'easy-job-portal-job-application', EASY_JOB_PLUGIN_URL . '/assets/js/job-application.min.js', [ 'jquery' ], EASY_JOB_VERSION, true );
		wp_register_script( 'easy-job-portal-job-submission', EASY_JOB_PLUGIN_URL . '/assets/js/job-submission.min.js', [ 'jquery' ], EASY_JOB_VERSION, true );
		wp_localize_script( 'easy-job-portal-ajax-filters', 'easy_job_portal_ajax_filters', $ajax_data );

		wp_localize_script(
			'easy-job-portal-job-submission',
			'easy_job_portal_job_submission',
			[
				// translators: Placeholder %d is the number of files to that users are limited to.
				'i18n_over_upload_limit' => esc_html__( 'You are only allowed to upload a maximum of %d files.', 'easy-job-portal' ),
			]
		);

		wp_localize_script(
			'easy-job-portal-job-dashboard',
			'easy_job_portal_job_dashboard',
			[
				'i18n_confirm_delete' => esc_html__( 'Are you sure you want to delete this listing?', 'easy-job-portal' ),
			]
		);

		wp_localize_script(
			'easy-job-portal-job-submission',
			'easy_job_portal_job_submission',
			[
				'i18n_required_field' => __( 'This field is required.', 'easy-job-portal' ),
			]
		);

		/**
		 * Filter whether to enqueue DLJP core's frontend scripts. By default, they will only be enqueued on DLJP related
		 * pages.
		 *
		 * If your theme or plugin depend on `frontend.css` from DLJP core, you can use the
		 * `easy_job_portal_enqueue_frontend_style` filter.
		 *
		 * Example code for a custom shortcode that depends on the frontend style:
		 *
		 * add_filter( 'easy_job_portal_enqueue_frontend_style', function( $frontend_used_on_page ) {
		 *   global $post;
		 *   if ( is_singular()
		 *        && is_a( $post, 'WP_Post' )
		 *        && has_shortcode( $post->post_content, 'resumes' )
		 *   ) {
		 *     $frontend_used_on_page = true;
		 *   }
		 *   return $frontend_used_on_page;
		 * } );
		 *
		 * @since 1.30.0
		 *
		 * @param bool $is_frontend_style_enabled
		 */
		if ( apply_filters( 'easy_job_portal_enqueue_frontend_style', is_wpjm() ) ) {
			wp_enqueue_style( 'easy-job-portal-frontend', EASY_JOB_PLUGIN_URL . '/assets/css/frontend.css', [], EASY_JOB_VERSION );
		} else {
			wp_register_style( 'easy-job-portal-job-listings', EASY_JOB_PLUGIN_URL . '/assets/css/job-listings.css', [], EASY_JOB_VERSION );
		}
	}
}

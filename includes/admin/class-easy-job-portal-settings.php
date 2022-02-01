<?php
/**
 * File containing the class Easy_Job_Portal_Settings.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the management of plugin settings.
 *
 * @since 1.0.0
 */
class Easy_Job_Portal_Settings {

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.26.0
	 */
	private static $instance = null;

	/**
	 * Our Settings.
	 *
	 * @var array Settings.
	 */
	protected $settings = [];

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since  1.26.0
	 * @static
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
		$this->settings_group = 'easy_job_portal';
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	/**
	 * Get Job Manager Settings
	 *
	 * @return array
	 */
	public function get_settings() {
		if ( 0 === count( $this->settings ) ) {
			$this->init_settings();
		}
		return $this->settings;
	}

	/**
	 * Initializes the configuration for the plugin's setting fields.
	 *
	 * @access protected
	 */
	protected function init_settings() {
		// Prepare roles option.
		$roles         = get_editable_roles();
		$account_roles = [];

		foreach ( $roles as $key => $role ) {
			if ( 'administrator' === $key ) {
				continue;
			}
			$account_roles[ $key ] = $role['name'];
		}

		$this->settings = apply_filters(
			'easy_job_portal_settings',
			[
				'general'        => [
					__( 'General', 'easy-job-portal' ),
					[
						[
							'name'    => 'easy_job_portal_date_format',
							'std'     => 'relative',
							'label'   => __( 'Date Format', 'easy-job-portal' ),
							'desc'    => __( 'Choose how you want the published date for jobs to be displayed on the front-end.', 'easy-job-portal' ),
							'type'    => 'radio',
							'options' => [
								'relative' => __( 'Relative to the current date (e.g., 1 day, 1 week, 1 month ago)', 'easy-job-portal' ),
								'default'  => __( 'Default date format as defined in Settings', 'easy-job-portal' ),
							],
						],
						[
							'name'       => 'easy_job_portal_google_maps_api_key',
							'std'        => '',
							'label'      => __( 'Google Maps API Key', 'easy-job-portal' ),
							// translators: Placeholder %s is URL to set up a Google Maps API key.
							'desc'       => sprintf( __( 'Google requires an API key to retrieve location information for job listings. Acquire an API key from the <a href="%s">Google Maps API developer site</a>.', 'easy-job-portal' ), 'https://developers.google.com/maps/documentation/geocoding/get-api-key' ),
							'attributes' => [],
						],
						[
							'name'       => 'easy_job_portal_delete_data_on_uninstall',
							'std'        => '0',
							'label'      => __( 'Delete Data On Uninstall', 'easy-job-portal' ),
							'cb_label'   => __( 'Delete Easy Job Portal data when the plugin is deleted. Once removed, this data cannot be restored.', 'easy-job-portal' ),
							'desc'       => '',
							'type'       => 'checkbox',
							'attributes' => [],
						],
					],
				],
				'job_listings'   => [
					__( 'Easy Job Portal', 'easy-job-portal' ),
					[
						[
							'name'        => 'easy_job_portal_per_page',
							'std'         => '10',
							'placeholder' => '',
							'label'       => __( 'Listings Per Page', 'easy-job-portal' ),
							'desc'        => __( 'Number of job listings to display per page.', 'easy-job-portal' ),
							'attributes'  => [],
						],
						[
							'name'       => 'easy_job_portal_hide_filled_positions',
							'std'        => '0',
							'label'      => __( 'Filled Positions', 'easy-job-portal' ),
							'cb_label'   => __( 'Hide filled positions', 'easy-job-portal' ),
							'desc'       => __( 'Filled positions will not display in your archives.', 'easy-job-portal' ),
							'type'       => 'checkbox',
							'attributes' => [],
						],
						[
							'name'       => 'easy_job_portal_hide_expired',
							'std'        => get_option( 'easy_job_portal_hide_expired_content' ) ? '1' : '0', // back compat.
							'label'      => __( 'Hide Expired Listings', 'easy-job-portal' ),
							'cb_label'   => __( 'Hide expired listings in job archives/search', 'easy-job-portal' ),
							'desc'       => __( 'Expired job listings will not be searchable.', 'easy-job-portal' ),
							'type'       => 'checkbox',
							'attributes' => [],
						],
						[
							'name'       => 'easy_job_portal_hide_expired_content',
							'std'        => '1',
							'label'      => __( 'Hide Expired Listings Content', 'easy-job-portal' ),
							'cb_label'   => __( 'Hide content in expired single job listings', 'easy-job-portal' ),
							'desc'       => __( 'Your site will display the titles of expired listings, but not the content of the listings', 'easy-job-portal' ),
							'type'       => 'checkbox',
							'attributes' => [],
						],
						[
							'name'       => 'easy_job_portal_enable_categories',
							'std'        => '0',
							'label'      => __( 'Categories', 'easy-job-portal' ),
							'cb_label'   => __( 'Enable listing categories', 'easy-job-portal' ),
							'desc'       => __( 'This lets users select from a list of categories when submitting a job.', 'easy-job-portal' ),
							'type'       => 'checkbox',
							'attributes' => [],
						],
						[
							'name'       => 'easy_job_portal_enable_default_category_multiselect',
							'std'        => '0',
							'label'      => __( 'Multi-select Categories', 'easy-job-portal' ),
							'cb_label'   => __( 'Default to category multiselect', 'easy-job-portal' ),
							'desc'       => __( 'The category selection box will default to allowing multiple selections on the [jobs] shortcode.', 'easy-job-portal' ),
							'type'       => 'checkbox',
							'attributes' => [],
						],
						[
							'name'    => 'easy_job_portal_category_filter_type',
							'std'     => 'any',
							'label'   => __( 'Category Filter Type', 'easy-job-portal' ),
							'desc'    => __( 'Determines the logic used to display jobs when selecting multiple categories.', 'easy-job-portal' ),
							'type'    => 'radio',
							'options' => [
								'any' => __( 'Jobs will be shown if within ANY selected category', 'easy-job-portal' ),
								'all' => __( 'Jobs will be shown if within ALL selected categories', 'easy-job-portal' ),
							],
						],
						[
							'name'       => 'easy_job_portal_enable_types',
							'std'        => '1',
							'label'      => __( 'Types', 'easy-job-portal' ),
							'cb_label'   => __( 'Enable listing types', 'easy-job-portal' ),
							'desc'       => __( 'This lets users select from a list of types when submitting a job.', 'easy-job-portal' ),
							'type'       => 'checkbox',
							'attributes' => [],
						],
						[
							'name'       => 'easy_job_portal_multi_job_type',
							'std'        => '0',
							'label'      => __( 'Multi-select Listing Types', 'easy-job-portal' ),
							'cb_label'   => __( 'Allow multiple types for listings', 'easy-job-portal' ),
							'desc'       => __( 'This allows users to select more than one type when submitting a job.', 'easy-job-portal' ),
							'type'       => 'checkbox',
							'attributes' => [],
						],
					],
				],
				'job_submission' => [
					__( 'Job Submission', 'easy-job-portal' ),
					[
						[
							'name'       => 'easy_job_portal_user_requires_account',
							'std'        => '1',
							'label'      => __( 'Account Required', 'easy-job-portal' ),
							'cb_label'   => __( 'Require an account to submit listings', 'easy-job-portal' ),
							'desc'       => __( 'Limits job listing submissions to registered, logged-in users.', 'easy-job-portal' ),
							'type'       => 'checkbox',
							'attributes' => [],
						],
						[
							'name'       => 'easy_job_portal_enable_registration',
							'std'        => '1',
							'label'      => __( 'Account Creation', 'easy-job-portal' ),
							'cb_label'   => __( 'Enable account creation during submission', 'easy-job-portal' ),
							'desc'       => __( 'Includes account creation on the listing submission form, to allow non-registered users to create an account and submit a job listing simultaneously.', 'easy-job-portal' ),
							'type'       => 'checkbox',
							'attributes' => [],
						],
						[
							'name'       => 'easy_job_portal_generate_username_from_email',
							'std'        => '1',
							'label'      => __( 'Account Username', 'easy-job-portal' ),
							'cb_label'   => __( 'Generate usernames from email addresses', 'easy-job-portal' ),
							'desc'       => __( 'Automatically generates usernames for new accounts from the registrant\'s email address. If this is not enabled, a "username" field will display instead.', 'easy-job-portal' ),
							'type'       => 'checkbox',
							'attributes' => [],
						],
						[
							'name'       => 'easy_job_portal_use_standard_password_setup_email',
							'std'        => '1',
							'label'      => __( 'Account Password', 'easy-job-portal' ),
							'cb_label'   => __( 'Email new users a link to set a password', 'easy-job-portal' ),
							'desc'       => __( 'Sends an email to the user with their username and a link to set their password. If this is not enabled, a "password" field will display instead, and their email address won\'t be verified.', 'easy-job-portal' ),
							'type'       => 'checkbox',
							'attributes' => [],
						],
						[
							'name'    => 'easy_job_portal_registration_role',
							'std'     => 'employer',
							'label'   => __( 'Account Role', 'easy-job-portal' ),
							'desc'    => __( 'Any new accounts created during submission will have this role. If you haven\'t enabled account creation during submission in the options above, your own method of assigning roles will apply.', 'easy-job-portal' ),
							'type'    => 'select',
							'options' => $account_roles,
						],
						[
							'name'       => 'easy_job_portal_submission_requires_approval',
							'std'        => '1',
							'label'      => __( 'Moderate New Listings', 'easy-job-portal' ),
							'cb_label'   => __( 'Require admin approval of all new listing submissions', 'easy-job-portal' ),
							'desc'       => __( 'Sets all new submissions to "pending." They will not appear on your site until an admin approves them.', 'easy-job-portal' ),
							'type'       => 'checkbox',
							'attributes' => [],
						],
						[
							'name'       => 'easy_job_portal_user_can_edit_pending_submissions',
							'std'        => '0',
							'label'      => __( 'Allow Pending Edits', 'easy-job-portal' ),
							'cb_label'   => __( 'Allow editing of pending listings', 'easy-job-portal' ),
							'desc'       => __( 'Users can continue to edit pending listings until they are approved by an admin.', 'easy-job-portal' ),
							'type'       => 'checkbox',
							'attributes' => [],
						],
						[
							'name'       => 'easy_job_portal_user_edit_published_submissions',
							'std'        => 'yes',
							'label'      => __( 'Allow Published Edits', 'easy-job-portal' ),
							'cb_label'   => __( 'Allow editing of published listings', 'easy-job-portal' ),
							'desc'       => __( 'Choose whether published job listings can be edited and if edits require admin approval. When moderation is required, the original job listings will be unpublished while edits await admin approval.', 'easy-job-portal' ),
							'type'       => 'radio',
							'options'    => [
								'no'            => __( 'Users cannot edit', 'easy-job-portal' ),
								'yes'           => __( 'Users can edit without admin approval', 'easy-job-portal' ),
								'yes_moderated' => __( 'Users can edit, but edits require admin approval', 'easy-job-portal' ),
							],
							'attributes' => [],
						],
						[
							'name'       => 'easy_job_portal_submission_duration',
							'std'        => '30',
							'label'      => __( 'Listing Duration', 'easy-job-portal' ),
							'desc'       => __( 'Listings will display for the set number of days, then expire. Leave this field blank if you don\'t want listings to have an expiration date.', 'easy-job-portal' ),
							'attributes' => [],
						],
						[
							'name'    => 'easy_job_portal_allowed_application_method',
							'std'     => '',
							'label'   => __( 'Application Method', 'easy-job-portal' ),
							'desc'    => __( 'Choose the application method job listers will need to provide. Specify URL or email address only, or allow listers to choose which they prefer.', 'easy-job-portal' ),
							'type'    => 'radio',
							'options' => [
								''      => __( 'Email address or website URL', 'easy-job-portal' ),
								'email' => __( 'Email addresses only', 'easy-job-portal' ),
								'url'   => __( 'Website URLs only', 'easy-job-portal' ),
							],
						],
					],
				],
				
				'job_pages'      => [
					__( 'Pages', 'easy-job-portal' ),
					[
						[
							'name'  => 'easy_job_portal_submit_job_form_page_id',
							'std'   => '',
							'label' => __( 'Submit Job Form Page', 'easy-job-portal' ),
							'desc'  => __( 'Select the page where you\'ve used the [submit_job_form] shortcode. This lets the plugin know the location of the form.', 'easy-job-portal' ),
							'type'  => 'page',
						],
						[
							'name'  => 'easy_job_portal_job_dashboard_page_id',
							'std'   => '',
							'label' => __( 'Job Dashboard Page', 'easy-job-portal' ),
							'desc'  => __( 'Select the page where you\'ve used the [job_dashboard] shortcode. This lets the plugin know the location of the dashboard.', 'easy-job-portal' ),
							'type'  => 'page',
						],
						[
							'name'  => 'easy_job_portal_jobs_page_id',
							'std'   => '',
							'label' => __( 'Easy Job Portal Page', 'easy-job-portal' ),
							'desc'  => __( 'Select the page where you\'ve used the [jobs] shortcode. This lets the plugin know the location of the job listings page.', 'easy-job-portal' ),
							'type'  => 'page',
						],
					],
				],
			]
		);
	}

	/**
	 * Registers the plugin's settings with WordPress's Settings API.
	 */
	public function register_settings() {
		$this->init_settings();

		foreach ( $this->settings as $section ) {
			foreach ( $section[1] as $option ) {
				if ( isset( $option['std'] ) ) {
					add_option( $option['name'], $option['std'] );
				}
				register_setting( $this->settings_group, $option['name'] );
			}
		}
	}

	/**
	 * Shows the plugin's settings page.
	 */
	public function output() {
		$this->init_settings();
		?>
		<div class="wrap easy-job-portal-settings-wrap">
			<form class="easy-job-portal-options" method="post" action="options.php">

				<?php settings_fields( $this->settings_group ); ?>

				<h2 class="nav-tab-wrapper">
					<?php
					foreach ( $this->settings as $key => $section ) {
						echo '<a href="#settings-' . esc_attr( sanitize_title( $key ) ) . '" class="nav-tab">' . esc_html( $section[0] ) . '</a>';
					}
					?>
				</h2>

				<?php
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Used for basic flow.
				if ( ! empty( $_GET['settings-updated'] ) ) {
					flush_rewrite_rules();
					echo '<div class="updated fade easy-job-portal-updated"><p>' . esc_html__( 'Settings successfully saved', 'easy-job-portal' ) . '</p></div>';
				}

				foreach ( $this->settings as $key => $section ) {
					$section_args = isset( $section[2] ) ? (array) $section[2] : [];
					echo '<div id="settings-' . esc_attr( sanitize_title( $key ) ) . '" class="settings_panel">';
					if ( ! empty( $section_args['before'] ) ) {
						echo '<p class="before-settings">' . wp_kses_post( $section_args['before'] ) . '</p>';
					}
					echo '<table class="form-table settings parent-settings">';

					foreach ( $section[1] as $option ) {
						$value = get_option( $option['name'] );
						$this->output_field( $option, $value );
					}

					echo '</table>';
					if ( ! empty( $section_args['after'] ) ) {
						echo '<p class="after-settings">' . wp_kses_post( $section_args['after'] ) . '</p>';
					}
					echo '</div>';

				}
				?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'easy-job-portal' ); ?>" />
				</p>
			</form>
		</div>
		<script type="text/javascript">
			jQuery('.nav-internal').click(function (e) {
				e.preventDefault();
				jQuery('.nav-tab-wrapper a[href="' + jQuery(this).attr('href') + '"]').click();

				return false;
			});

			jQuery('.nav-tab-wrapper a').click(function() {
				if ( '#' !== jQuery(this).attr( 'href' ).substr( 0, 1 ) ) {
					return false;
				}
				jQuery('.settings_panel').hide();
				jQuery('.nav-tab-active').removeClass('nav-tab-active');
				jQuery( jQuery(this).attr('href') ).show();
				jQuery(this).addClass('nav-tab-active');
				window.location.hash = jQuery(this).attr('href');
				jQuery( 'form.easy-job-portal-options' ).attr( 'action', 'options.php' + jQuery(this).attr( 'href' ) );
				window.scrollTo( 0, 0 );
				return false;
			});
			var goto_hash = window.location.hash;
			if ( '#' === goto_hash.substr( 0, 1 ) ) {
				jQuery( 'form.easy-job-portal-options' ).attr( 'action', 'options.php' + jQuery(this).attr( 'href' ) );
			}
			if ( goto_hash ) {
				var the_tab = jQuery( 'a[href="' + goto_hash + '"]' );
				if ( the_tab.length > 0 ) {
					the_tab.click();
				} else {
					jQuery( '.nav-tab-wrapper a:first' ).click();
				}
			} else {
				jQuery( '.nav-tab-wrapper a:first' ).click();
			}
			var $use_standard_password_setup_email = jQuery('#setting-easy_job_portal_use_standard_password_setup_email');
			var $generate_username_from_email = jQuery('#setting-easy_job_portal_generate_username_from_email');
			var $easy_job_portal_registration_role = jQuery('#setting-easy_job_portal_registration_role');

			jQuery('#setting-easy_job_portal_enable_registration').change(function(){
				if ( jQuery( this ).is(':checked') ) {
					$easy_job_portal_registration_role.closest('tr').show();
					$use_standard_password_setup_email.closest('tr').show();
					$generate_username_from_email.closest('tr').show();
				} else {
					$easy_job_portal_registration_role.closest('tr').hide();
					$use_standard_password_setup_email.closest('tr').hide();
					$generate_username_from_email.closest('tr').hide();
				}
			}).change();

			jQuery( '.sub-settings-expander' ).on( 'change', function() {
				var $expandable = jQuery(this).parent().siblings( '.sub-settings-expandable' );
				var checked = jQuery(this).is( ':checked' );
				if ( checked ) {
					$expandable.addClass( 'expanded' );
				} else {
					$expandable.removeClass( 'expanded' );
				}
			} ).trigger( 'change' );
		</script>
		<?php
	}

	/**
	 * Checkbox input field.
	 *
	 * @param array  $option
	 * @param array  $attributes
	 * @param mixed  $value
	 * @param string $ignored_placeholder
	 */
	protected function input_checkbox( $option, $attributes, $value, $ignored_placeholder ) {
		if ( ! isset( $option['hidden_value'] ) ) {
			$option['hidden_value'] = '0';
		}
		?>
		<label>
		<input type="hidden" name="<?php echo esc_attr( $option['name'] ); ?>" value="<?php echo esc_attr( $option['hidden_value'] ); ?>" />
		<input
			id="setting-<?php echo esc_attr( $option['name'] ); ?>"
			name="<?php echo esc_attr( $option['name'] ); ?>"
			type="checkbox"
			value="1"
			<?php
			echo implode( ' ', $attributes ) . ' '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			checked( '1', $value );
			?>
		/> <?php echo wp_kses_post( $option['cb_label'] ); ?></label>
		<?php
		if ( ! empty( $option['desc'] ) ) {
			echo ' <p class="description">' . wp_kses_post( $option['desc'] ) . '</p>';
		}
	}

	/**
	 * Text area input field.
	 *
	 * @param array  $option
	 * @param array  $attributes
	 * @param mixed  $value
	 * @param string $placeholder
	 */
	protected function input_textarea( $option, $attributes, $value, $placeholder ) {
		?>
		<textarea
			id="setting-<?php echo esc_attr( $option['name'] ); ?>"
			class="large-text"
			cols="50"
			rows="3"
			name="<?php echo esc_attr( $option['name'] ); ?>"
			<?php
			echo implode( ' ', $attributes ) . ' '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $placeholder; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		>
			<?php echo esc_textarea( $value ); ?>
		</textarea>
		<?php

		if ( ! empty( $option['desc'] ) ) {
			echo ' <p class="description">' . wp_kses_post( $option['desc'] ) . '</p>';
		}
	}

	/**
	 * Select input field.
	 *
	 * @param array  $option
	 * @param array  $attributes
	 * @param mixed  $value
	 * @param string $ignored_placeholder
	 */
	protected function input_select( $option, $attributes, $value, $ignored_placeholder ) {
		?>
		<select
			id="setting-<?php echo esc_attr( $option['name'] ); ?>"
			class="regular-text"
			name="<?php echo esc_attr( $option['name'] ); ?>"
			<?php
			echo implode( ' ', $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		>
		<?php
		foreach ( $option['options'] as $key => $name ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( $value, $key, false ) . '>' . esc_html( $name ) . '</option>';
		}
		?>
		</select>
		<?php

		if ( ! empty( $option['desc'] ) ) {
			echo ' <p class="description">' . wp_kses_post( $option['desc'] ) . '</p>';
		}
	}

	/**
	 * Radio input field.
	 *
	 * @param array  $option
	 * @param array  $ignored_attributes
	 * @param mixed  $value
	 * @param string $ignored_placeholder
	 */
	protected function input_radio( $option, $ignored_attributes, $value, $ignored_placeholder ) {
		?>
		<fieldset>
		<legend class="screen-reader-text">
		<span><?php echo esc_html( $option['label'] ); ?></span>
		</legend>
		<?php
		if ( ! empty( $option['desc'] ) ) {
			echo ' <p class="description">' . wp_kses_post( $option['desc'] ) . '</p>';
		}

		foreach ( $option['options'] as $key => $name ) {
			echo '<label><input name="' . esc_attr( $option['name'] ) . '" type="radio" value="' . esc_attr( $key ) . '" ' . checked( $value, $key, false ) . ' />' . esc_html( $name ) . '</label><br>';
		}
		?>
		</fieldset>
		<?php
	}

	/**
	 * Page input field.
	 *
	 * @param array  $option
	 * @param array  $ignored_attributes
	 * @param mixed  $value
	 * @param string $ignored_placeholder
	 */
	protected function input_page( $option, $ignored_attributes, $value, $ignored_placeholder ) {
		$args = [
			'name'             => $option['name'],
			'id'               => $option['name'],
			'sort_column'      => 'menu_order',
			'sort_order'       => 'ASC',
			'show_option_none' => __( '--no page--', 'easy-job-portal' ),
			'echo'             => false,
			'selected'         => absint( $value ),
		];

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe output.
		echo str_replace( ' id=', " data-placeholder='" . esc_attr__( 'Select a page&hellip;', 'easy-job-portal' ) . "' id=", wp_dropdown_pages( $args ) );

		if ( ! empty( $option['desc'] ) ) {
			echo ' <p class="description">' . wp_kses_post( $option['desc'] ) . '</p>';
		}
	}

	/**
	 * Hidden input field.
	 *
	 * @param array  $option
	 * @param array  $attributes
	 * @param mixed  $value
	 * @param string $ignored_placeholder
	 */
	protected function input_hidden( $option, $attributes, $value, $ignored_placeholder ) {
		$human_value = $value;
		if ( $option['human_value'] ) {
			$human_value = $option['human_value'];
		}
		?>
		<input
			id="setting-<?php echo esc_attr( $option['name'] ); ?>"
			type="hidden"
			name="<?php echo esc_attr( $option['name'] ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php
			echo implode( ' ', $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		/><strong><?php echo esc_html( $human_value ); ?></strong>
		<?php

		if ( ! empty( $option['desc'] ) ) {
			echo ' <p class="description">' . wp_kses_post( $option['desc'] ) . '</p>';
		}
	}

	/**
	 * Password input field.
	 *
	 * @param array  $option
	 * @param array  $attributes
	 * @param mixed  $value
	 * @param string $placeholder
	 */
	protected function input_password( $option, $attributes, $value, $placeholder ) {
		?>
		<input
			id="setting-<?php echo esc_attr( $option['name'] ); ?>"
			class="regular-text"
			type="password"
			name="<?php echo esc_attr( $option['name'] ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php
			echo implode( ' ', $attributes ) . ' '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $placeholder; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		/>
		<?php

		if ( ! empty( $option['desc'] ) ) {
			echo ' <p class="description">' . wp_kses_post( $option['desc'] ) . '</p>';
		}
	}

	/**
	 * Number input field.
	 *
	 * @param array  $option
	 * @param array  $attributes
	 * @param mixed  $value
	 * @param string $placeholder
	 */
	protected function input_number( $option, $attributes, $value, $placeholder ) {
		echo isset( $option['before'] ) ? wp_kses_post( $option['before'] ) : '';
		?>
		<input
			id="setting-<?php echo esc_attr( $option['name'] ); ?>"
			class="small-text"
			type="number"
			name="<?php echo esc_attr( $option['name'] ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php
			echo implode( ' ', $attributes ) . ' '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $placeholder; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		/>
		<?php
		echo isset( $option['after'] ) ? wp_kses_post( $option['after'] ) : '';
		if ( ! empty( $option['desc'] ) ) {
			echo ' <p class="description">' . wp_kses_post( $option['desc'] ) . '</p>';
		}
	}

	/**
	 * Text input field.
	 *
	 * @param array  $option
	 * @param array  $attributes
	 * @param mixed  $value
	 * @param string $placeholder
	 */
	protected function input_text( $option, $attributes, $value, $placeholder ) {
		?>
		<input
			id="setting-<?php echo esc_attr( $option['name'] ); ?>"
			class="regular-text"
			type="text"
			name="<?php echo esc_attr( $option['name'] ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php
			echo implode( ' ', $attributes ) . ' '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $placeholder; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		/>
		<?php

		if ( ! empty( $option['desc'] ) ) {
			echo ' <p class="description">' . wp_kses_post( $option['desc'] ) . '</p>';
		}
	}

	/**
	 * Outputs the field row.
	 *
	 * @param array $option
	 * @param mixed $value
	 */
	protected function output_field( $option, $value ) {
		$placeholder    = ( ! empty( $option['placeholder'] ) ) ? 'placeholder="' . esc_attr( $option['placeholder'] ) . '"' : '';
		$class          = ! empty( $option['class'] ) ? $option['class'] : '';
		$option['type'] = ! empty( $option['type'] ) ? $option['type'] : 'text';
		$attributes     = [];
		if ( ! empty( $option['attributes'] ) && is_array( $option['attributes'] ) ) {
			foreach ( $option['attributes'] as $attribute_name => $attribute_value ) {
				$attributes[] = esc_attr( $attribute_name ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		echo '<tr valign="top" class="' . esc_attr( $class ) . '">';

		if ( ! empty( $option['label'] ) ) {
			echo '<th scope="row"><label for="setting-' . esc_attr( $option['name'] ) . '">' . esc_html( $option['label'] ) . '</a></th><td>';
		} else {
			echo '<td colspan="2">';
		}

		$method_name = 'input_' . $option['type'];
		if ( method_exists( $this, $method_name ) ) {
			$this->$method_name( $option, $attributes, $value, $placeholder );
		} else {
			/**
			 * Allows for custom fields in admin setting panes.
			 *
			 * @since 1.14.0
			 *
			 * @param string $option     Field name.
			 * @param array  $attributes Array of attributes.
			 * @param mixed  $value      Field value.
			 * @param string $value      Placeholder text.
			 */
			do_action( 'wp_easy_job_portal_admin_field_' . $option['type'], $option, $attributes, $value, $placeholder );
		}
		echo '</td></tr>';
	}

	/**
	 * Multiple settings stored in one setting array that are shown when the `enable` setting is checked.
	 *
	 * @param array  $option
	 * @param array  $attributes
	 * @param array  $values
	 * @param string $placeholder
	 */
	protected function input_multi_enable_expand( $option, $attributes, $values, $placeholder ) {
		echo '<div class="setting-enable-expand">';
		$enable_option               = $option['enable_field'];
		$enable_option['name']       = $option['name'] . '[' . $enable_option['name'] . ']';
		$enable_option['type']       = 'checkbox';
		$enable_option['attributes'] = [ 'class="sub-settings-expander"' ];

		if ( isset( $enable_option['force_value'] ) && is_bool( $enable_option['force_value'] ) ) {
			if ( true === $enable_option['force_value'] ) {
				$values[ $option['enable_field']['name'] ] = '1';
			} else {
				$values[ $option['enable_field']['name'] ] = '0';
			}

			$enable_option['hidden_value'] = $values[ $option['enable_field']['name'] ];
			$enable_option['attributes'][] = 'disabled="disabled"';
		}

		$this->input_checkbox( $enable_option, $enable_option['attributes'], $values[ $option['enable_field']['name'] ], null );

		echo '<div class="sub-settings-expandable">';
		$this->input_multi( $option, $attributes, $values, $placeholder );
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Multiple settings stored in one setting array.
	 *
	 * @param array  $option
	 * @param array  $ignored_attributes
	 * @param array  $values
	 * @param string $ignored_placeholder
	 */
	protected function input_multi( $option, $ignored_attributes, $values, $ignored_placeholder ) {
		echo '<table class="form-table settings child-settings">';
		foreach ( $option['settings'] as $sub_option ) {
			$value              = isset( $values[ $sub_option['name'] ] ) ? $values[ $sub_option['name'] ] : $sub_option['std'];
			$sub_option['name'] = $option['name'] . '[' . $sub_option['name'] . ']';
			$this->output_field( $sub_option, $value );
		}
		echo '</table>';
	}

	/**
	 * Proxy for text input field.
	 *
	 * @param array  $option
	 * @param array  $attributes
	 * @param mixed  $value
	 * @param string $placeholder
	 */
	protected function input_input( $option, $attributes, $value, $placeholder ) {
		$this->input_text( $option, $attributes, $value, $placeholder );
	}
}

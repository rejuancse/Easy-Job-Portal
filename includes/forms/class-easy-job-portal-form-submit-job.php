<?php
/**
 * File containing the class Easy_Job_Portal_Form_Submit_Job.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the editing of Easy Job Portal from the public facing frontend (from within `[submit_job_form]` shortcode).
 *
 * @extends Easy_Job_Portal_Form
 * @since 1.0.0n
 */
class Easy_Job_Portal_Form_Submit_Job extends Easy_Job_Portal_Form {

	/**
	 * Form name.
	 *
	 * @var string
	 */
	public $form_name = 'submit-job';

	/**
	 * Job listing ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $job_id;

	/**
	 * Preview job (unused)
	 *
	 * @access protected
	 * @var string
	 */
	protected $preview_job;

	/**
	 * Stores static instance of class.
	 *
	 * @access protected
	 * @var Easy_Job_Portal_Form_Submit_Job The single instance of the class
	 */
	protected static $instance = null;

	/**
	 * Returns static instance of class.
	 *
	 * @return self
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
		add_action( 'wp', [ $this, 'process' ] );
		add_action( 'submit_job_form_start', [ $this, 'output_submit_form_nonce_field' ] );
		add_action( 'preview_job_form_start', [ $this, 'output_preview_form_nonce_field' ] );
		add_action( 'easy_job_portal_job_submitted', [ $this, 'track_job_submission' ] );

		if ( $this->use_recaptcha_field() ) {
			add_action( 'submit_job_form_end', [ $this, 'display_recaptcha_field' ] );
			add_filter( 'submit_job_form_validate_fields', [ $this, 'validate_recaptcha_field' ] );
			add_filter( 'submit_draft_job_form_validate_fields', [ $this, 'validate_recaptcha_field' ] );
		}

		$this->steps = (array) apply_filters(
			'submit_job_steps',
			[
				'submit'  => [
					'name'     => __( 'Submit Details', 'easy-job-portal' ),
					'view'     => [ $this, 'submit' ],
					'handler'  => [ $this, 'submit_handler' ],
					'priority' => 10,
				],
				'preview' => [
					'name'     => __( 'Preview', 'easy-job-portal' ),
					'view'     => [ $this, 'preview' ],
					'handler'  => [ $this, 'preview_handler' ],
					'priority' => 20,
				],
				'done'    => [
					'name'     => __( 'Done', 'easy-job-portal' ),
					'before'   => [ $this, 'done_before' ],
					'view'     => [ $this, 'done' ],
					'priority' => 30,
				],
			]
		);

		uasort( $this->steps, [ $this, 'sort_by_priority' ] );

		// phpcs:disable WordPress.Security.NonceVerification.Missing,  WordPress.Security.NonceVerification.Recommended -- Check happens later when possible. Input is used safely.
		// Get step/job.
		if ( isset( $_POST['step'] ) ) {
			$this->step = is_numeric( $_POST['step'] ) ? max( absint( $_POST['step'] ), 0 ) : array_search( sanitize_text_field( $_POST['step'] ), array_keys( $this->steps ), true );
		} elseif ( ! empty( $_GET['step'] ) ) {
			$this->step = is_numeric( $_GET['step'] ) ? max( absint( $_GET['step'] ), 0 ) : array_search( sanitize_text_field( $_GET['step'] ), array_keys( $this->steps ), true );
		}

		$this->job_id = ! empty( $_REQUEST['job_id'] ) ? absint( $_REQUEST['job_id'] ) : 0;
		// phpcs:enable WordPress.Security.NonceVerification.Missing,  WordPress.Security.NonceVerification.Recommended

		if ( ! easy_job_portal_user_can_edit_job( $this->job_id ) ) {
			$this->job_id = 0;
		}

		// Allow resuming from cookie.
		$this->resume_edit = false;
		if (
			! isset( $_GET['new'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Input is used safely.
			&& (
				'before' === get_option( 'easy_job_portal_paid_listings_flow' )
				|| ! $this->job_id
			)
			&& ! empty( $_COOKIE['easy-job-portal-submitting-job-id'] )
			&& ! empty( $_COOKIE['easy-job-portal-submitting-job-key'] )
		) {
			$job_id     = absint( $_COOKIE['easy-job-portal-submitting-job-id'] );
			$job_status = get_post_status( $job_id );

			if (
				(
					'preview' === $job_status
					|| 'pending_payment' === $job_status
				)
				&& get_post_meta( $job_id, '_submitting_key', true ) === $_COOKIE['easy-job-portal-submitting-job-key']
			) {
				$this->job_id      = $job_id;
				$this->resume_edit = get_post_meta( $job_id, '_submitting_key', true );
			}
		}

		// Load job details.
		if ( $this->job_id ) {
			$job_status = get_post_status( $this->job_id );
			if ( 'expired' === $job_status ) {
				if ( ! easy_job_portal_user_can_edit_job( $this->job_id ) ) {
					$this->job_id = 0;
					$this->step   = 0;
				}
			} elseif ( ! in_array( $job_status, apply_filters( 'easy_job_portal_valid_submit_job_statuses', [ 'preview', 'draft' ] ), true ) ) {
				$this->job_id = 0;
				$this->step   = 0;
			}
		}
	}

	/**
	 * Gets the submitted job ID.
	 *
	 * @return int
	 */
	public function get_job_id() {
		return absint( $this->job_id );
	}

	/**
	 * Initializes the fields used in the form.
	 */
	public function init_fields() {
		if ( $this->fields ) {
			return;
		}

		$allowed_application_method = get_option( 'easy_job_portal_allowed_application_method', '' );
		switch ( $allowed_application_method ) {
			case 'email':
				$application_method_label       = __( 'Application email', 'easy-job-portal' );
				$application_method_placeholder = __( 'you@example.com', 'easy-job-portal' );
				$application_method_sanitizer   = 'email';
				break;
			case 'url':
				$application_method_label       = __( 'Application URL', 'easy-job-portal' );
				$application_method_placeholder = __( 'https://', 'easy-job-portal' );
				$application_method_sanitizer   = 'url';
				break;
			default:
				$application_method_label       = __( 'Email/URL', 'easy-job-portal' );
				$application_method_placeholder = __( 'Enter an email address or website URL', 'easy-job-portal' );
				$application_method_sanitizer   = 'url_or_email';
				break;
		}

		if ( easy_job_portal_multi_job_type() ) {
			$job_type = 'term-multiselect';
		} else {
			$job_type = 'term-select';
		}
		$this->fields = apply_filters(
			'submit_job_form_fields',
			[
				'job_listing'     => [
					'job_title'       => [
						'label'       => __( 'Job Title', 'easy-job-portal' ),
						'type'        => 'text',
						'required'    => true,
						'placeholder' => '',
						'priority'    => 1,
					],
					'job_type'        => [
						'label'       => __( 'Job type', 'easy-job-portal' ),
						'type'        => $job_type,
						'required'    => true,
						'placeholder' => __( 'Choose job type&hellip;', 'easy-job-portal' ),
						'priority'    => 2,
						'default'     => 'full-time',
						'taxonomy'    => 'job_listing_type',
					],
					'job_location'    => [
						'label'       => __( 'Location', 'easy-job-portal' ),
						'type'        => 'text',
						'required'    => false,
						'placeholder' => __( '', 'easy-job-portal' ),
						'priority'    => 3,
					],
					'job_category'    => [
						'label'       => __( 'Job category', 'easy-job-portal' ),
						'type'        => 'term-multiselect',
						'required'    => true,
						'placeholder' => '',
						'priority'    => 4,
						'default'     => '',
						'taxonomy'    => 'job_listing_category',
					],
					'application'     => [
						'label'       => $application_method_label,
						'type'        => 'text',
						'sanitizer'   => $application_method_sanitizer,
						'required'    => true,
						'placeholder' => $application_method_placeholder,
						'priority'    => 5,
					],
					'job_description' => [
						'label'    => __( 'Description', 'easy-job-portal' ),
						'type'     => 'wp-editor',
						'required' => true,
						'priority' => 6,
					],
				],
				# Company Details
				'company' => [
					'company_name'    => [
						'label'       => __( 'Company name', 'easy-job-portal' ),
						'type'        => 'text',
						'required'    => true,
						'placeholder' => __( 'Enter the name of the company', 'easy-job-portal' ),
						'priority'    => 1,
					],
					'company_website' => [
						'label'       => __( 'Website', 'easy-job-portal' ),
						'type'        => 'text',
						'sanitizer'   => 'url',
						'required'    => false,
						'placeholder' => __( 'http://', 'easy-job-portal' ),
						'priority'    => 2,
					],
					'company_tagline' => [
						'label'       => __( 'Tagline', 'easy-job-portal' ),
						'type'        => 'text',
						'required'    => false,
						'placeholder' => __( 'Briefly describe your company', 'easy-job-portal' ),
						'maxlength'   => 64,
						'priority'    => 3,
					],
					'company_twitter' => [
						'label'       => __( 'Twitter username', 'easy-job-portal' ),
						'type'        => 'text',
						'required'    => false,
						'placeholder' => __( '@yourcompany', 'easy-job-portal' ),
						'priority'    => 5,
					],
					'company_logo'    => [
						'label'              => __( 'Logo', 'easy-job-portal' ),
						'type'               => 'file',
						'required'           => false,
						'placeholder'        => '',
						'priority'           => 6,
						'ajax'               => true,
						'multiple'           => false,
						'allowed_mime_types' => [
							'jpg'  => 'image/jpeg',
							'jpeg' => 'image/jpeg',
							'gif'  => 'image/gif',
							'png'  => 'image/png',
						],
					],
				],
			]
		);

		if ( ! get_option( 'easy_job_portal_enable_categories' ) || 0 === intval( wp_count_terms( 'job_listing_category' ) ) ) {
			unset( $this->fields['job_listing']['job_category'] );
		}
		if ( ! get_option( 'easy_job_portal_enable_types' ) || 0 === intval( wp_count_terms( 'job_listing_type' ) ) ) {
			unset( $this->fields['job_listing']['job_type'] );
		}
	}

	/**
	 * Use reCAPTCHA field on the form?
	 *
	 * @return bool
	 */
	// public function use_recaptcha_field() {
	// 	if ( ! $this->is_recaptcha_available() ) {
	// 		return false;
	// 	}
	// 	return 1 === absint( get_option( 'easy_job_portal_enable_recaptcha_job_submission' ) );
	// }

	/**
	 * Validates the posted fields.
	 *
	 * @param array $values
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 * @throws Exception Uploaded file is not a valid mime-type or other validation error.
	 */
	protected function validate_fields( $values ) {
		foreach ( $this->fields as $group_key => $group_fields ) {
			foreach ( $group_fields as $key => $field ) {
				if ( $field['required'] && empty( $values[ $group_key ][ $key ] ) ) {
					// translators: Placeholder %s is the label for the required field.
					return new WP_Error( 'validation-error', sprintf( __( '%s is a required field', 'easy-job-portal' ), $field['label'] ) );
				}
				if ( ! empty( $field['taxonomy'] ) && in_array( $field['type'], [ 'term-checklist', 'term-select', 'term-multiselect' ], true ) ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						$check_value = $values[ $group_key ][ $key ];
					} else {
						$check_value = empty( $values[ $group_key ][ $key ] ) ? [] : [ $values[ $group_key ][ $key ] ];
					}
					foreach ( $check_value as $term ) {
						if ( ! term_exists( $term, $field['taxonomy'] ) ) {
							// translators: Placeholder %s is the field label that is did not validate.
							return new WP_Error( 'validation-error', sprintf( __( '%s is invalid', 'easy-job-portal' ), $field['label'] ) );
						}
					}
				}
				if ( 'file' === $field['type'] ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						$check_value = array_filter( $values[ $group_key ][ $key ] );
					} else {
						$check_value = array_filter( [ $values[ $group_key ][ $key ] ] );
					}
					if ( ! empty( $check_value ) ) {
						foreach ( $check_value as $file_url ) {
							if ( is_numeric( $file_url ) ) {
								continue;
							}
							$file_url = esc_url( $file_url, [ 'http', 'https' ] );
							if ( empty( $file_url ) ) {
								throw new Exception( __( 'Invalid attachment provided.', 'easy-job-portal' ) );
							}
						}
					}
				}
				if ( 'file' === $field['type'] && ! empty( $field['allowed_mime_types'] ) ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						$check_value = array_filter( $values[ $group_key ][ $key ] );
					} else {
						$check_value = array_filter( [ $values[ $group_key ][ $key ] ] );
					}
					if ( ! empty( $check_value ) ) {
						foreach ( $check_value as $file_url ) {
							$file_url  = current( explode( '?', $file_url ) );
							$file_info = wp_check_filetype( $file_url );

							if ( ! is_numeric( $file_url ) && $file_info && ! in_array( $file_info['type'], $field['allowed_mime_types'], true ) ) {
								// translators: Placeholder %1$s is field label; %2$s is the file mime type; %3$s is the allowed mime-types.
								throw new Exception( sprintf( __( '"%1$s" (filetype %2$s) needs to be one of the following file types: %3$s', 'easy-job-portal' ), $field['label'], $file_info['ext'], implode( ', ', array_keys( $field['allowed_mime_types'] ) ) ) );
							}
						}
					}
				}
				if ( empty( $field['file_limit'] ) && empty( $field['multiple'] ) ) {
					$field['file_limit'] = 1;
				}
				if ( 'file' === $field['type'] && ! empty( $field['file_limit'] ) ) {
					$file_limit = intval( $field['file_limit'] );
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						$check_value = array_filter( $values[ $group_key ][ $key ] );
					} else {
						$check_value = array_filter( [ $values[ $group_key ][ $key ] ] );
					}
					if ( count( $check_value ) > $file_limit ) {
						// translators: Placeholder %d is the number of files to that users are limited to.
						$message = esc_html__( 'You are only allowed to upload a maximum of %d files.', 'easy-job-portal' );
						if ( ! empty( $field['file_limit_message'] ) ) {
							$message = $field['file_limit_message'];
						}

						throw new Exception( esc_html( sprintf( $message, $file_limit ) ) );
					}
				}
			}
		}

		// Application method.
		if ( isset( $values['job_listing']['application'] ) && ! empty( $values['job_listing']['application'] ) ) {
			$allowed_application_method   = get_option( 'easy_job_portal_allowed_application_method', '' );
			$values['job_listing']['application'] = str_replace( ' ', '+', $values['job_listing']['application'] );
			switch ( $allowed_application_method ) {
				case 'email':
					if ( ! is_email( $values['job_listing']['application'] ) ) {
						throw new Exception( __( 'Please enter a valid application email address', 'easy-job-portal' ) );
					}
					break;
				case 'url':
					// Prefix http if needed.
					if ( ! strstr( $values['job_listing']['application'], 'http:' ) && ! strstr( $values['job_listing']['application'], 'https:' ) ) {
						$values['job_listing']['application'] = 'http://' . $values['job_listing']['application'];
					}
					if ( ! filter_var( $values['job_listing']['application'], FILTER_VALIDATE_URL ) ) {
						throw new Exception( __( 'Please enter a valid application URL', 'easy-job-portal' ) );
					}
					break;
				default:
					if ( ! is_email( $values['job_listing']['application'] ) ) {
						// Prefix http if needed.
						if ( ! strstr( $values['job_listing']['application'], 'http:' ) && ! strstr( $values['job_listing']['application'], 'https:' ) ) {
							$values['job_listing']['application'] = 'http://' . $values['job_listing']['application'];
						}
						if ( ! filter_var( $values['job_listing']['application'], FILTER_VALIDATE_URL ) ) {
							throw new Exception( __( 'Please enter a valid application email address or URL', 'easy-job-portal' ) );
						}
					}
					break;
			}
		}

		/**
		 * Perform additional validation on the job submission fields.
		 *
		 */
		return apply_filters( 'submit_job_form_validate_fields', true, $this->fields, $values );
	}

	/**
	 * Enqueues scripts and styles for editing and posting a job listing.
	 */
	protected function enqueue_job_form_assets() {
		wp_enqueue_script( 'easy-job-portal-job-submission' );
		wp_enqueue_style( 'easy-job-portal-job-submission', EASY_JOB_PLUGIN_URL . '/assets/css/job-submission.css', [], EASY_JOB_VERSION );
	}

	/**
	 * Localize frontend scripts that have been enqueued. This should be called
	 * after the fields are rendered, in case some of them enqueue new scripts.
	 *
	 * @deprecated 1.34.1 No longer needed.
	 */
	public function localize_job_form_scripts() {
		_deprecated_function( __METHOD__, '1.34.1' );
	}

	/**
	 * Returns an array of the job types indexed by slug. (Unused)
	 *
	 * @return array
	 */
	private function job_types() {
		$options = [];
		$terms   = get_job_listing_types();
		foreach ( $terms as $term ) {
			$options[ $term->slug ] = $term->name;
		}
		return $options;
	}

	/**
	 * Displays the form.
	 */
	public function submit() {
		$this->init_fields();

		// Load data if necessary.
		if ( $this->job_id ) {
			$job = get_post( $this->job_id );
			foreach ( $this->fields as $group_key => $group_fields ) {
				foreach ( $group_fields as $key => $field ) {
					switch ( $key ) {
						case 'job_title':
							$this->fields[ $group_key ][ $key ]['value'] = $job->post_title;
							break;
						case 'job_description':
							$this->fields[ $group_key ][ $key ]['value'] = $job->post_content;
							break;
						case 'job_type':
							$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms( $job->ID, 'job_listing_type', [ 'fields' => 'ids' ] );
							if ( ! easy_job_portal_multi_job_type() ) {
								$this->fields[ $group_key ][ $key ]['value'] = current( $this->fields[ $group_key ][ $key ]['value'] );
							}
							break;
						case 'job_category':
							$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms( $job->ID, 'job_listing_category', [ 'fields' => 'ids' ] );
							break;
						case 'company_logo':
							$this->fields[ $group_key ][ $key ]['value'] = has_post_thumbnail( $job->ID ) ? get_post_thumbnail_id( $job->ID ) : get_post_meta( $job->ID, '_' . $key, true );
							break;
						default:
							$this->fields[ $group_key ][ $key ]['value'] = get_post_meta( $job->ID, '_' . $key, true );
							break;
					}
				}
			}

			$this->fields = apply_filters( 'submit_job_form_fields_get_job_data', $this->fields, $job );

			// Get user meta.
		} elseif ( is_user_logged_in() && empty( $_POST['submit_job'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Safe input.
			if ( ! empty( $this->fields['company'] ) ) {
				foreach ( $this->fields['company'] as $key => $field ) {
					$this->fields['company'][ $key ]['value'] = get_user_meta( get_current_user_id(), '_' . $key, true );
				}
			}
			if ( ! empty( $this->fields['job_listing']['application'] ) ) {
				$allowed_application_method = get_option( 'easy_job_portal_allowed_application_method', '' );
				if ( 'url' !== $allowed_application_method ) {
					$current_user                                = wp_get_current_user();
					$this->fields['job_listing']['application']['value'] = $current_user->user_email;
				}
			}
			$this->fields = apply_filters( 'submit_job_form_fields_get_user_data', $this->fields, get_current_user_id() );
		}

		$this->enqueue_job_form_assets();
		get_easy_job_portal_template(
			'job-submit.php',
			[
				'form'               => $this->form_name,
				'job_id'             => $this->get_job_id(),
				'resume_edit'        => $this->resume_edit,
				'action'             => $this->get_action(),
				'job_fields'         => $this->get_fields( 'job_listing' ),
				'company_fields'     => $this->get_fields( 'company' ),
				'step'               => $this->get_step(),
				'can_continue_later' => $this->can_continue_later(),
				'submit_button_text' => apply_filters( 'submit_job_form_submit_button_text', __( 'Preview', 'easy-job-portal' ) ),
			]
		);
	}

	/**
	 * Handles the submission of form data.
	 *
	 * @throws Exception On validation error.
	 */
	public function submit_handler() {
		try {
			// Init fields.
			$this->init_fields();

			// Get posted values.
			$values = $this->get_posted_fields();

			$input_create_account_username        = isset( $_POST['create_account_username'] ) ? sanitize_text_field( wp_unslash( $_POST['create_account_username'] ) ) : false;

			$input_create_account_password        = isset( $_POST['create_account_password'] ) ? sanitize_text_field( wp_unslash( $_POST['create_account_password'] ) ) : false;

			$input_create_account_password_verify = isset( $_POST['create_account_password_verify'] ) ? sanitize_text_field( wp_unslash( $_POST['create_account_password_verify'] ) ) : false;
			
			$input_create_account_email           = isset( $_POST['create_account_email'] ) ? sanitize_text_field( wp_unslash( $_POST['create_account_email'] ) ) : false;

			$is_saving_draft                      = $this->can_continue_later() && ! empty( $_POST['save_draft'] );

			if ( empty( $_POST['submit_job'] ) && ! $is_saving_draft ) {
				return;
			}
			// phpcs:enable WordPress.Security.NonceVerification.Missing

			$this->check_submit_form_nonce_field();

			// Validate fields.
			if ( $is_saving_draft ) {
				$validation_status = apply_filters( 'submit_draft_job_form_validate_fields', true, $this->fields, $values );
			} else {
				$validation_status = $this->validate_fields( $values );
			}

			if ( is_wp_error( $validation_status ) ) {
				throw new Exception( $validation_status->get_error_message() );
			}

			// Account creation.
			if ( ! is_user_logged_in() ) {
				$create_account = false;

				if ( easy_job_portal_enable_registration() ) {
					if ( easy_job_portal_user_requires_account() ) {
						
						if ( empty( $input_create_account_username ) ) {
							throw new Exception( __( 'Please enter a username.', 'easy-job-portal' ) );
						}

						if ( empty( $input_create_account_password ) ) {
							throw new Exception( __( 'Please enter a password.', 'easy-job-portal' ) );
						}
						if ( empty( $input_create_account_email ) ) {
							throw new Exception( __( 'Please enter your email address.', 'easy-job-portal' ) );
						}
					}

					if ( empty( $input_create_account_password_verify ) || $input_create_account_password_verify !== $input_create_account_password ) {
						throw new Exception( __( 'Passwords must match.', 'easy-job-portal' ) );
					}
					if ( ! wpjm_validate_new_password( sanitize_text_field( wp_unslash( $input_create_account_password ) ) ) ) {
						$password_hint = wpjm_get_password_rules_hint();
						if ( $password_hint ) {
							throw new Exception( sprintf( __( 'Invalid Password: %s', 'easy-job-portal' ), $password_hint ) );
						} else {
							throw new Exception( __( 'Password is not valid.', 'easy-job-portal' ) );
						}
					}
					
					if ( ! empty( $input_create_account_email ) ) {
						$create_account = wp_easy_job_portal_create_account(
							[
								'username' => $input_create_account_username,
								'password' => $input_create_account_password,
								'email'    => sanitize_text_field( wp_unslash( $input_create_account_email ) ),
								'role'     => get_option( 'easy_job_portal_registration_role' ),
							]
						);
					}
				}

				if ( is_wp_error( $create_account ) ) {
					throw new Exception( $create_account->get_error_message() );
				}
			}

			if ( easy_job_portal_user_requires_account() && ! is_user_logged_in() ) {
				throw new Exception( __( 'You must be signed in to post a new listing.', 'easy-job-portal' ) );
			}

			$post_status = '';
			if ( $is_saving_draft ) {
				$post_status = 'draft';
			} elseif ( ! $this->job_id || 'draft' === get_post_status( $this->job_id ) ) {
				$post_status = 'preview';
			}

			// Update the job.
			$this->save_job( $values['job_listing']['job_title'], $values['job_listing']['job_description'], $post_status, $values );
			$this->update_job_data( $values );

			if ( $this->job_id ) {
				// Reset the `_filled` flag.
				update_post_meta( $this->job_id, '_filled', 0 );
			}

			if ( $is_saving_draft ) {
				$job_dashboard_page_id = get_option( 'easy_job_portal_job_dashboard_page_id', false );

				// translators: placeholder is the URL to the job dashboard page.
				$this->add_message( sprintf( __( 'Draft was saved. Job listing drafts can be resumed from the <a href="%s">job dashboard</a>.', 'easy-job-portal' ), get_permalink( $job_dashboard_page_id ) ) );
			} else {
				// Successful, show next step.
				$this->step++;
			}
		} catch ( Exception $e ) {
			$this->add_error( $e->getMessage() );
			return;
		}
	}

	/**
	 * Updates or creates a job listing from posted data.
	 *
	 * @param  string $post_title
	 * @param  string $post_content
	 * @param  string $status
	 * @param  array  $values
	 * @param  bool   $update_slug
	 */
	protected function save_job( $post_title, $post_content, $status = 'preview', $values = [], $update_slug = true ) {
		$job_data = [
			'post_title'     => $post_title,
			'post_content'   => $post_content,
			'post_type'      => 'job_listing',
			'comment_status' => 'closed',
		];

		if ( $update_slug ) {
			$job_slug = [];

			// Prepend with company name.
			if ( apply_filters( 'submit_job_form_prefix_post_name_with_company', true ) && ! empty( $values['company']['company_name'] ) ) {
				$job_slug[] = $values['company']['company_name'];
			}

			// Prepend location.
			if ( apply_filters( 'submit_job_form_prefix_post_name_with_location', true ) && ! empty( $values['job_listing']['job_location'] ) ) {
				$job_slug[] = $values['job_listing']['job_location'];
			}

			// Prepend with job type.
			if ( apply_filters( 'submit_job_form_prefix_post_name_with_job_type', true ) && ! empty( $values['job_listing']['job_type'] ) ) {
				if ( ! easy_job_portal_multi_job_type() ) {
					$job_slug[] = $values['job_listing']['job_type'];
				} else {
					$terms = $values['job_listing']['job_type'];

					foreach ( $terms as $term ) {
						$term = get_term_by( 'id', intval( $term ), 'job_listing_type' );

						if ( $term ) {
							$job_slug[] = $term->slug;
						}
					}
				}
			}

			$job_slug[]            = $post_title;
			$job_data['post_name'] = sanitize_title( implode( '-', $job_slug ) );
		}

		if ( $status ) {
			$job_data['post_status'] = $status;
		}

		$job_data = apply_filters( 'submit_job_form_save_job_data', $job_data, $post_title, $post_content, $status, $values );

		if ( $this->job_id ) {
			$job_data['ID'] = $this->job_id;
			wp_update_post( $job_data );
		} else {
			$this->job_id = wp_insert_post( $job_data );

			if ( ! headers_sent() ) {
				$submitting_key = uniqid();

				setcookie( 'easy-job-portal-submitting-job-id', $this->job_id, false, COOKIEPATH, COOKIE_DOMAIN, false );
				setcookie( 'easy-job-portal-submitting-job-key', $submitting_key, false, COOKIEPATH, COOKIE_DOMAIN, false );

				update_post_meta( $this->job_id, '_submitting_key', $submitting_key );
			}
		}
	}

	/**
	 * Creates a file attachment.
	 *
	 * @param  string $attachment_url
	 * @return int attachment id.
	 */
	protected function create_attachment( $attachment_url ) {
		include_once ABSPATH . 'wp-admin/includes/image.php';
		include_once ABSPATH . 'wp-admin/includes/media.php';

		$upload_dir     = wp_upload_dir();
		$attachment_url = esc_url( $attachment_url, [ 'http', 'https' ] );
		if ( empty( $attachment_url ) ) {
			return 0;
		}

		$attachment_url_parts = wp_parse_url( $attachment_url );

		// Relative paths aren't allowed.
		if ( false !== strpos( $attachment_url_parts['path'], '../' ) ) {
			return 0;
		}

		$attachment_url = sprintf( '%s://%s%s', $attachment_url_parts['scheme'], $attachment_url_parts['host'], $attachment_url_parts['path'] );

		$attachment_url = str_replace( [ $upload_dir['baseurl'], WP_CONTENT_URL, site_url( '/' ) ], [ $upload_dir['basedir'], WP_CONTENT_DIR, ABSPATH ], $attachment_url );
		if ( empty( $attachment_url ) || ! is_string( $attachment_url ) ) {
			return 0;
		}

		$attachment = [
			'post_title'   => wpjm_get_the_job_title( $this->job_id ),
			'post_content' => '',
			'post_status'  => 'inherit',
			'post_parent'  => $this->job_id,
			'guid'         => $attachment_url,
		];

		$info = wp_check_filetype( $attachment_url );
		if ( $info ) {
			$attachment['post_mime_type'] = $info['type'];
		}

		$attachment_id = wp_insert_attachment( $attachment, $attachment_url, $this->job_id );

		if ( ! is_wp_error( $attachment_id ) ) {
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $attachment_url ) );
			return $attachment_id;
		}

		return 0;
	}

	/**
	 * Sets job meta and terms based on posted values.
	 *
	 * @param  array $values
	 */
	protected function update_job_data( $values ) {
		// Set defaults.
		add_post_meta( $this->job_id, '_filled', 0, true );
		add_post_meta( $this->job_id, '_featured', 0, true );

		$maybe_attach = [];

		// Loop fields and save meta and term data.
		foreach ( $this->fields as $group_key => $group_fields ) {
			foreach ( $group_fields as $key => $field ) {
				// Save taxonomies.
				if ( ! empty( $field['taxonomy'] ) ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						wp_set_object_terms( $this->job_id, $values[ $group_key ][ $key ], $field['taxonomy'], false );
					} else {
						wp_set_object_terms( $this->job_id, [ $values[ $group_key ][ $key ] ], $field['taxonomy'], false );
					}

					// Company logo is a featured image.
				} elseif ( 'company_logo' === $key ) {
					$attachment_id = is_numeric( $values[ $group_key ][ $key ] ) ? absint( $values[ $group_key ][ $key ] ) : $this->create_attachment( $values[ $group_key ][ $key ] );
					if ( empty( $attachment_id ) ) {
						delete_post_thumbnail( $this->job_id );
					} else {
						set_post_thumbnail( $this->job_id, $attachment_id );
					}
					update_user_meta( get_current_user_id(), '_company_logo', $attachment_id );

					// Save meta data.
				} else {
					update_post_meta( $this->job_id, '_' . $key, $values[ $group_key ][ $key ] );

					// Handle attachments.
					if ( 'file' === $field['type'] ) {
						if ( is_array( $values[ $group_key ][ $key ] ) ) {
							foreach ( $values[ $group_key ][ $key ] as $file_url ) {
								$maybe_attach[] = $file_url;
							}
						} else {
							$maybe_attach[] = $values[ $group_key ][ $key ];
						}
					}
				}
			}
		}

		$maybe_attach = array_filter( $maybe_attach );

		// Handle attachments.
		if ( count( $maybe_attach ) && apply_filters( 'easy_job_portal_attach_uploaded_files', true ) ) {
			// Get attachments.
			$attachments     = get_posts( 'post_parent=' . $this->job_id . '&post_type=attachment&fields=ids&numberposts=-1' );
			$attachment_urls = [];

			// Loop attachments already attached to the job.
			foreach ( $attachments as $attachment_id ) {
				$attachment_urls[] = wp_get_attachment_url( $attachment_id );
			}

			foreach ( $maybe_attach as $attachment_url ) {
				if ( ! in_array( $attachment_url, $attachment_urls, true ) ) {
					$this->create_attachment( $attachment_url );
				}
			}
		}

		// And user meta to save time in future.
		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), '_company_name', isset( $values['company']['company_name'] ) ? $values['company']['company_name'] : '' );
			update_user_meta( get_current_user_id(), '_company_website', isset( $values['company']['company_website'] ) ? $values['company']['company_website'] : '' );
			update_user_meta( get_current_user_id(), '_company_tagline', isset( $values['company']['company_tagline'] ) ? $values['company']['company_tagline'] : '' );
			update_user_meta( get_current_user_id(), '_company_twitter', isset( $values['company']['company_twitter'] ) ? $values['company']['company_twitter'] : '' );
			update_user_meta( get_current_user_id(), '_company_video', isset( $values['company']['company_video'] ) ? $values['company']['company_video'] : '' );
		}

		do_action( 'easy_job_portal_update_job_data', $this->job_id, $values );
	}

	/**
	 * Displays preview of Job Listing.
	 */
	public function preview() {
		global $post, $job_preview;

		if ( $this->job_id ) {
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Job preview depends on temporary override. Reset below.
			$post              = get_post( $this->job_id );
			$job_preview       = true;
			$post->post_status = 'preview';

			setup_postdata( $post );

			get_easy_job_portal_template(
				'job-preview.php',
				[
					'form' => $this,
				]
			);

			wp_reset_postdata();
		}
	}

	/**
	 * Handles the preview step form response.
	 */
	public function preview_handler() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Input is used safely.
		if ( empty( $_POST ) ) {
			return;
		}

		$this->check_preview_form_nonce_field();

		// Edit = show submit form again.
		if ( ! empty( $_POST['edit_job'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Input is used safely.
			$this->step --;
		}

		// Continue = change job status then show next screen.
		if ( ! empty( $_POST['continue'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Input is used safely.
			$job = get_post( $this->job_id );

			if ( in_array( $job->post_status, [ 'preview', 'expired' ], true ) ) {
				// Reset expiry.
				delete_post_meta( $job->ID, '_job_expires' );

				// Update job listing.
				$update_job                  = [];
				$update_job['ID']            = $job->ID;
				$update_job['post_status']   = apply_filters( 'submit_job_post_status', get_option( 'easy_job_portal_submission_requires_approval' ) ? 'pending' : 'publish', $job );
				$update_job['post_date']     = current_time( 'mysql' );
				$update_job['post_date_gmt'] = current_time( 'mysql', 1 );
				$update_job['post_author']   = get_current_user_id();

				wp_update_post( $update_job );
			}

			$this->step ++;
		}
	}

	/**
	 * Output the nonce field on job submission form.
	 */
	public function output_submit_form_nonce_field() {
		if ( ! is_user_logged_in() ) {
			return;
		}
		wp_nonce_field( 'submit-job-' . $this->job_id, '_wpjm_nonce' );
	}

	/**
	 * Check the nonce field on the submit form.
	 */
	public function check_submit_form_nonce_field() {
		if ( ! is_user_logged_in() ) {
			return;
		}
		if (
			empty( $_REQUEST['_wpjm_nonce'] )
			|| ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpjm_nonce'] ), 'submit-job-' . $this->job_id ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce should not be modified.
		) {
			wp_nonce_ays( 'submit-job-' . $this->job_id );
			die();
		}
	}

	/**
	 * Output the nonce field on job preview form.
	 */
	public function output_preview_form_nonce_field() {
		if ( ! is_user_logged_in() ) {
			return;
		}
		wp_nonce_field( 'preview-job-' . $this->job_id, '_wpjm_nonce' );
	}

	/**
	 * Check the nonce field on the preview form.
	 */
	public function check_preview_form_nonce_field() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		if (
			empty( $_REQUEST['_wpjm_nonce'] )
			|| ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpjm_nonce'] ), 'preview-job-' . $this->job_id ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce should not be modified.
		) {
			wp_nonce_ays( 'preview-job-' . $this->job_id );
			die();
		}
	}

	/**
	 * Displays the final screen after a job listing has been submitted.
	 */
	public function done() {
		get_easy_job_portal_template( 'job-submitted.php', [ 'job_listing' => get_post( $this->job_id ) ] ); 
	}

	/**
	 * Handles the job submissions before the view is called.
	 */
	public function done_before() {
		do_action( 'easy_job_portal_job_submitted', $this->job_id );
	}

	/**
	 * Checks if we can resume submission later.
	 *
	 * @return bool
	 */
	protected function can_continue_later() {
		$can_continue_later    = false;
		$job_dashboard_page_id = get_option( 'easy_job_portal_job_dashboard_page_id', false );

		if ( ! $job_dashboard_page_id ) {
			// For now, we're going to block resuming later if no job dashboard page has been set.
			$can_continue_later = false;
		} elseif ( is_user_logged_in() ) {
			// If they're logged in, we can assume they can access the job dashboard to resume later.
			$can_continue_later = true;
		} elseif ( easy_job_portal_user_requires_account() && easy_job_portal_enable_registration() ) {
			// If these are enabled, we know an account will be created on save.
			$can_continue_later = true;
		}

		/**
		 * Override if visitor can resume job submission later.
		 *
		 * @param bool $can_continue_later True if they can resume job later.
		 */
		return apply_filters( 'submit_job_form_can_continue_later', $can_continue_later );
	}

	/**
	 * Send usage tracking event for job submission.
	 *
	 * @param int $post_id Post ID.
	 */
	public function track_job_submission( $post_id ) {
		Easy_Job_Portal_Usage_Tracking::track_job_submission(
			$post_id,
			[
				'source'     => 'frontend',
				'old_status' => 'preview',
			]
		);
	}
}

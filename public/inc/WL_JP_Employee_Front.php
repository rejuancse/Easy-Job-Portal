<?php
defined( 'ABSPATH' ) || die();

require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'lib/WL_JP_Helper.php' );

class WL_JP_Employee_Front {
	/**
	 * Register employee post type
	 * @return void
	 */
	public static function register_employee_post_type() {
		$labels = array(
			'name'                  => esc_html(_x( 'Employees', 'Post Type General Name', DJP_DOMAIN )),
			'singular_name'         => esc_html(_x( 'Employee', 'Post Type Singular Name', DJP_DOMAIN )),
			'menu_name'             => esc_html__( 'Employees', DJP_DOMAIN ),
			'name_admin_bar'        => esc_html__( 'Employee', DJP_DOMAIN ),
			'archives'              => esc_html__( 'Employee Archives', DJP_DOMAIN ),
			'attributes'            => esc_html__( 'Employee Attributes', DJP_DOMAIN ),
			'all_items'             => esc_html__( 'All Employees', DJP_DOMAIN ),
			'add_new_item'          => esc_html__( 'Add New Employee', DJP_DOMAIN ),
			'add_new'               => esc_html__( 'Add New', DJP_DOMAIN ),
			'new_item'              => esc_html__( 'New Employee', DJP_DOMAIN ),
			'edit_item'             => esc_html__( 'Edit Employee', DJP_DOMAIN ),
			'update_item'           => esc_html__( 'Update Employee', DJP_DOMAIN ),
			'view_item'             => esc_html__( 'View Employee', DJP_DOMAIN ),
			'view_items'            => esc_html__( 'View Employees', DJP_DOMAIN ),
			'search_items'          => esc_html__( 'Search Employee', DJP_DOMAIN ),
			'not_found'             => esc_html__( 'Not found', DJP_DOMAIN ),
			'not_found_in_trash'    => esc_html__( 'Not found in Trash', DJP_DOMAIN ),
			'items_list'            => esc_html__( 'Employee list', DJP_DOMAIN ),
			'items_list_navigation' => esc_html__( 'Employee list navigation', DJP_DOMAIN ),
			'filter_items_list'     => esc_html__( 'Filter Employee list', DJP_DOMAIN ),
		);
		$args = array(
			'label'                 => esc_html__( 'Employee', DJP_DOMAIN ),
			'labels'                => $labels,
			'supports'              => array( 'title' ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
	        'menu_icon'             => 'dashicons-admin-users',
			'menu_position'         => 28,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'capability_type'       => 'page',
			'capabilities'          => array( 'create_posts' => is_multisite() ? 'do_not_allow' : false ),
  			'map_meta_cap'          => true,
			'rewrite'               => array( 'slug' => 'employee' ),
		);
		register_post_type( 'employee', $args );
	}

	/**
	 * Register cv
	 * @return void
	 */
	public static function register_cv() {
		if ( ! wp_verify_nonce( $_POST['cv'], 'cv' ) ) {
			die();
		}

		$user_id                = get_current_user_id();
		$personal_name          = isset( $_POST['employee_personal_name'] ) ? sanitize_text_field( $_POST['employee_personal_name'] ) : NULL;
		$personal_email         = isset( $_POST['employee_personal_email'] ) ? sanitize_email( $_POST['employee_personal_email'] ) : NULL;
		$personal_mobile        = isset( $_POST['employee_personal_mobile'] ) ? $_POST['employee_personal_mobile'] : NULL;
		$personal_date_of_birth = isset( $_POST['employee_personal_date_of_birth'] ) ? $_POST['employee_personal_date_of_birth'] : NULL;
		$personal_location      = isset( $_POST['employee_personal_location'] ) ? $_POST['employee_personal_location'] : NULL;
		$personal_gender        = isset( $_POST['employee_personal_gender'] ) ? $_POST['employee_personal_gender'] : NULL;

		$document_cv = ( isset( $_FILES['employee_document_cv'] ) && is_array( $_FILES['employee_document_cv'] ) ) ? $_FILES['employee_document_cv'] : null;

		$work_experience_profile_title          = isset( $_POST['employee_work_experience_profile_title'] ) ? sanitize_text_field( $_POST['employee_work_experience_profile_title'] ) : NULL;
		$work_experience_profile_summary        = isset( $_POST['employee_work_experience_profile_summary'] ) ? sanitize_text_field( $_POST['employee_work_experience_profile_summary'] ) : NULL;
		$work_experience_total_experience       = ( isset( $_POST['employee_work_experience_total_experience'] ) && is_array( $_POST['employee_work_experience_total_experience'] ) ) ? $_POST['employee_work_experience_total_experience'] : array();
		$work_experience_total_experience_year  = isset( $work_experience_total_experience['year'] ) ? intval( sanitize_text_field( $work_experience_total_experience['year'] ) ) : '';
		$work_experience_total_experience_month = isset( $work_experience_total_experience['month'] ) ? intval( sanitize_text_field( $work_experience_total_experience['month'] ) ) : '';
		$work_experience_salary                 = isset( $_POST['employee_work_experience_salary'] ) ? sanitize_text_field( $_POST['employee_work_experience_salary'] ) : NULL;
		$work_experience_notice_period          = isset( $_POST['employee_work_experience_notice_period'] ) ? sanitize_text_field( $_POST['employee_work_experience_notice_period'] ) : NULL;
		$work_experience_last_working_day       = isset( $_POST['employee_work_experience_last_working_day'] ) ? sanitize_text_field( $_POST['employee_work_experience_last_working_day'] ) : NULL;

		$employment_job_title     = ( isset( $_POST['employee_employment_job_title'] ) && is_array( $_POST['employee_employment_job_title'] ) ) ? $_POST['employee_employment_job_title'] : array();
		$employment_company_name  = ( isset( $_POST['employee_employment_company_name'] ) && is_array( $_POST['employee_employment_company_name'] ) ) ? $_POST['employee_employment_company_name'] : array();
		$employment_industry      = ( isset( $_POST['employee_employment_industry'] ) && is_array( $_POST['employee_employment_industry'] ) ) ? $_POST['employee_employment_industry'] : array();
		$employment_duration_from = ( isset( $_POST['employee_employment_duration_from'] ) && is_array( $_POST['employee_employment_duration_from'] ) ) ? $_POST['employee_employment_duration_from'] : array();
		$employment_duration_to   = ( isset( $_POST['employee_employment_duration_to'] ) && is_array( $_POST['employee_employment_duration_to'] ) ) ? $_POST['employee_employment_duration_to'] : array();

		$education_specialization  = ( isset( $_POST['employee_education_specialization'] ) && is_array( $_POST['employee_education_specialization'] ) ) ? $_POST['employee_education_specialization'] : array();
		$education_institute_name  = ( isset( $_POST['employee_education_institute_name'] ) && is_array( $_POST['employee_education_institute_name'] ) ) ? $_POST['employee_education_institute_name'] : array();
		$education_course_type     = ( isset( $_POST['employee_education_course_type'] ) && is_array( $_POST['employee_education_course_type'] ) ) ? $_POST['employee_education_course_type'] : array();
		$education_year_of_passing = ( isset( $_POST['employee_education_year_of_passing'] ) && is_array( $_POST['employee_education_year_of_passing'] ) ) ? $_POST['employee_education_year_of_passing'] : array();

		$skills = ( isset( $_POST['employee_skills'] ) && is_array( $_POST['employee_skills'] ) ) ? $_POST['employee_skills'] : array();

		$certification_title = ( isset( $_POST['employee_certification_title'] ) && is_array( $_POST['employee_certification_title'] ) ) ? $_POST['employee_certification_title'] : array();
		$certification_year  = ( isset( $_POST['employee_certification_year'] ) && is_array( $_POST['employee_certification_year'] ) ) ? $_POST['employee_certification_year'] : array();

		$desired_job_locations   = isset( $_POST['employee_desired_job_locations'] ) ? sanitize_text_field( $_POST['employee_desired_job_locations'] ) : NULL;
		$desired_job_industry    = isset( $_POST['employee_desired_job_industry'] ) ? sanitize_text_field( $_POST['employee_desired_job_industry'] ) : NULL;
		$desired_job_salary      = isset( $_POST['employee_desired_job_salary'] ) ? sanitize_text_field( $_POST['employee_desired_job_salary'] ) : NULL;
		$desired_job_departments = ( isset( $_POST['employee_desired_job_departments'] ) && is_array( $_POST['employee_desired_job_departments'] ) ) ? $_POST['employee_desired_job_departments'] : array();
		$desired_job_types       = ( isset( $_POST['employee_desired_job_types'] ) && is_array( $_POST['employee_desired_job_types'] ) ) ? $_POST['employee_desired_job_types'] : array();

		$errors = array();

		if ( empty( $personal_name ) ) {
			$errors['employee_personal_name'] = esc_html__( 'Please specify your name', DJP_DOMAIN );
		}

		if ( ! empty( $document_cv ) ) {
			$file_name          = sanitize_file_name( $document_cv['name'] );
			$file_type          = $document_cv['type'];
			$allowed_file_types = WL_JP_Helper::cv_document_file_types();

			if ( ! in_array( $file_type, $allowed_file_types ) ) {
				$errors['employee_document_cv'] = esc_html__( 'Please provide CV in PDF, DOC or DOCX format.', DJP_DOMAIN );
			}
		}

		if ( count( $errors ) ) {
			wp_send_json_error( $errors );
		}

		if ( ! empty( $document_cv ) ) {
			$document_cv = media_handle_upload( 'employee_document_cv', 0 );
			if ( is_wp_error( $document_cv ) ) {
				wp_send_json_error( $document_cv->get_error_message() );
			}
		}

		$post_id = wp_insert_post(
			array(
				'post_title'     => $personal_name,
				'post_type'      => 'employee',
				'post_status'    => 'publish',
				'comment_status' => 'closed',
				'ping_status'    => 'closed'
			)
		);

		$document = array(
			'cv'             => $document_cv,
			'latest_cv_date' => date( 'Y-m-d H:i:s' )
		);

		$personal = array(
			'name'          => $personal_name,
			'email'         => $personal_email,
			'mobile'        => $personal_mobile,
			'date_of_birth' => $personal_date_of_birth,
			'location'      => $personal_location,
			'gender'        => $personal_gender
		);

		$work_experience = array(
			'profile_title'    => $work_experience_profile_title,
			'profile_summary'  => $work_experience_profile_summary,
			'total_experience' => array(
				'year'  => $work_experience_total_experience_year,
				'month' => $work_experience_total_experience_month
			),
			'salary'           => $work_experience_salary,
			'notice_period'    => $work_experience_notice_period,
			'last_working_day' => $work_experience_last_working_day
		);

		$employment = array();
		foreach( $employment_job_title as $key => $job_title ) {
			array_push( $employment, array(
				'job_title'     => $job_title,
				'company_name'  => isset( $employment_company_name[$key] ) ? $employment_company_name[$key] : NULL,
				'industry'      => isset( $employment_industry[$key] ) ? $employment_industry[$key] : NULL,
				'duration_from' => isset( $employment_duration_from[$key] ) ? $employment_duration_from[$key] : NULL,
				'duration_to'   => isset( $employment_duration_to[$key] ) ? $employment_duration_to[$key] : NULL
			) );
		}

		$education = array();
		foreach( $education_specialization as $key => $specialization ) {
			array_push( $education, array(
				'specialization'  => $specialization,
				'institute_name'  => isset( $education_institute_name[$key] ) ? $education_institute_name[$key] : NULL,
				'course_type'     => isset( $education_course_type[$key] ) ? $education_course_type[$key] : NULL,
				'year_of_passing' => isset( $education_year_of_passing[$key] ) ? $education_year_of_passing[$key] : NULL
			) );
		}

		$certification = array();
		foreach( $certification_title as $key => $title ) {
			array_push( $certification, array(
				'certification_title'   => $title,
				'year_of_certification' => isset( $certification_year[$key] ) ? $certification_year[$key] : NULL,
			) );
		}

		if ( $desired_job_locations ) {
			$desired_job_locations = explode( ',', $desired_job_locations );
			$desired_job_locations = array_map( 'trim', $desired_job_locations );
		} else {
			$desired_job_locations = array();
		}

		$desired_job = array(
			'locations'   => $desired_job_locations,
			'industry'    => $desired_job_industry,
			'departments' => $desired_job_departments,
			'salary'      => $desired_job_salary,
			'job_types'   => $desired_job_types
		);

		update_post_meta( $post_id, 'wljp_employee_user_id', $user_id );
		update_post_meta( $post_id, 'wljp_employee_personal', $personal );
		update_post_meta( $post_id, 'wljp_employee_document', $document );
		update_post_meta( $post_id, 'wljp_employee_work_experience', $work_experience );
		update_post_meta( $post_id, 'wljp_employee_employment', $employment );
		update_post_meta( $post_id, 'wljp_employee_education', $education );
		update_post_meta( $post_id, 'wljp_employee_skills', $skills );
		update_post_meta( $post_id, 'wljp_employee_certification', $certification );
		update_post_meta( $post_id, 'wljp_employee_desired_job', $desired_job );

		wp_send_json_success( array( 'message' => esc_html__( 'Thank you for CV registration.', DJP_DOMAIN ), 'reload' => true ) );
	}

	/**
	 * Update cv
	 * @return void
	 */
	public static function update_cv() {
		if ( ! wp_verify_nonce( $_POST['cv-update'], 'cv-update' ) ) {
			die();
		}

		$user_id = get_current_user_id();
		if ( $employee = WL_JP_Helper::user_has_cv( $user_id ) ) {
			$post_id = $employee->ID;
		} else {
			die();
		}

		$personal_name          = isset( $_POST['employee_personal_name'] ) ? sanitize_text_field( $_POST['employee_personal_name'] ) : NULL;
		$personal_email         = isset( $_POST['employee_personal_email'] ) ? sanitize_email( $_POST['employee_personal_email'] ) : NULL;
		$personal_mobile        = isset( $_POST['employee_personal_mobile'] ) ? $_POST['employee_personal_mobile'] : NULL;
		$personal_date_of_birth = isset( $_POST['employee_personal_date_of_birth'] ) ? $_POST['employee_personal_date_of_birth'] : NULL;
		$personal_location      = isset( $_POST['employee_personal_location'] ) ? $_POST['employee_personal_location'] : NULL;
		$personal_gender        = isset( $_POST['employee_personal_gender'] ) ? $_POST['employee_personal_gender'] : NULL;

		$saved_document       = get_post_meta( $employee->ID, 'wljp_employee_document', true );
		$saved_document_cv    = isset( $saved_document['cv'] ) ? esc_attr( $saved_document['cv'] ) : '';
		$saved_latest_cv_date = isset( $saved_document['latest_cv_date'] ) ? esc_attr( $saved_document['latest_cv_date'] ) : '';
		$document_cv          = ( isset( $_FILES['employee_document_cv'] ) && is_array( $_FILES['employee_document_cv'] ) ) ? $_FILES['employee_document_cv'] : null;

		$work_experience_profile_title          = isset( $_POST['employee_work_experience_profile_title'] ) ? sanitize_text_field( $_POST['employee_work_experience_profile_title'] ) : NULL;
		$work_experience_profile_summary        = isset( $_POST['employee_work_experience_profile_summary'] ) ? sanitize_text_field( $_POST['employee_work_experience_profile_summary'] ) : NULL;
		$work_experience_total_experience       = ( isset( $_POST['employee_work_experience_total_experience'] ) && is_array( $_POST['employee_work_experience_total_experience'] ) ) ? $_POST['employee_work_experience_total_experience'] : array();
		$work_experience_total_experience_year  = isset( $work_experience_total_experience['year'] ) ? intval( sanitize_text_field( $work_experience_total_experience['year'] ) ) : '';
		$work_experience_total_experience_month = isset( $work_experience_total_experience['month'] ) ? intval( sanitize_text_field( $work_experience_total_experience['month'] ) ) : '';
		$work_experience_salary                 = isset( $_POST['employee_work_experience_salary'] ) ? sanitize_text_field( $_POST['employee_work_experience_salary'] ) : NULL;
		$work_experience_notice_period          = isset( $_POST['employee_work_experience_notice_period'] ) ? sanitize_text_field( $_POST['employee_work_experience_notice_period'] ) : NULL;
		$work_experience_last_working_day       = isset( $_POST['employee_work_experience_last_working_day'] ) ? sanitize_text_field( $_POST['employee_work_experience_last_working_day'] ) : NULL;

		$employment_job_title     = ( isset( $_POST['employee_employment_job_title'] ) && is_array( $_POST['employee_employment_job_title'] ) ) ? $_POST['employee_employment_job_title'] : array();
		$employment_company_name  = ( isset( $_POST['employee_employment_company_name'] ) && is_array( $_POST['employee_employment_company_name'] ) ) ? $_POST['employee_employment_company_name'] : array();
		$employment_industry      = ( isset( $_POST['employee_employment_industry'] ) && is_array( $_POST['employee_employment_industry'] ) ) ? $_POST['employee_employment_industry'] : array();
		$employment_duration_from = ( isset( $_POST['employee_employment_duration_from'] ) && is_array( $_POST['employee_employment_duration_from'] ) ) ? $_POST['employee_employment_duration_from'] : array();
		$employment_duration_to   = ( isset( $_POST['employee_employment_duration_to'] ) && is_array( $_POST['employee_employment_duration_to'] ) ) ? $_POST['employee_employment_duration_to'] : array();

		$education_specialization  = ( isset( $_POST['employee_education_specialization'] ) && is_array( $_POST['employee_education_specialization'] ) ) ? $_POST['employee_education_specialization'] : array();
		$education_institute_name  = ( isset( $_POST['employee_education_institute_name'] ) && is_array( $_POST['employee_education_institute_name'] ) ) ? $_POST['employee_education_institute_name'] : array();
		$education_course_type     = ( isset( $_POST['employee_education_course_type'] ) && is_array( $_POST['employee_education_course_type'] ) ) ? $_POST['employee_education_course_type'] : array();
		$education_year_of_passing = ( isset( $_POST['employee_education_year_of_passing'] ) && is_array( $_POST['employee_education_year_of_passing'] ) ) ? $_POST['employee_education_year_of_passing'] : array();

		$skills = ( isset( $_POST['employee_skills'] ) && is_array( $_POST['employee_skills'] ) ) ? $_POST['employee_skills'] : array();

		$certification_title = ( isset( $_POST['employee_certification_title'] ) && is_array( $_POST['employee_certification_title'] ) ) ? $_POST['employee_certification_title'] : array();
		$certification_year  = ( isset( $_POST['employee_certification_year'] ) && is_array( $_POST['employee_certification_year'] ) ) ? $_POST['employee_certification_year'] : array();

		$desired_job_locations   = isset( $_POST['employee_desired_job_locations'] ) ? sanitize_text_field( $_POST['employee_desired_job_locations'] ) : NULL;
		$desired_job_industry    = isset( $_POST['employee_desired_job_industry'] ) ? sanitize_text_field( $_POST['employee_desired_job_industry'] ) : NULL;
		$desired_job_salary      = isset( $_POST['employee_desired_job_salary'] ) ? sanitize_text_field( $_POST['employee_desired_job_salary'] ) : NULL;
		$desired_job_departments = ( isset( $_POST['employee_desired_job_departments'] ) && is_array( $_POST['employee_desired_job_departments'] ) ) ? $_POST['employee_desired_job_departments'] : array();
		$desired_job_types       = ( isset( $_POST['employee_desired_job_types'] ) && is_array( $_POST['employee_desired_job_types'] ) ) ? $_POST['employee_desired_job_types'] : array();

		$errors = array();

		if ( empty( $personal_name ) ) {
			$errors['employee_personal_name'] = esc_html__( 'Please specify your name', DJP_DOMAIN );
		}

		if ( ! empty( $document_cv ) ) {
			$file_name          = sanitize_file_name( $document_cv['name'] );
			$file_type          = $document_cv['type'];
			$allowed_file_types = WL_JP_Helper::cv_document_file_types();

			if ( ! in_array( $file_type, $allowed_file_types ) ) {
				$errors['employee_document_cv'] = esc_html__( 'Please provide CV in PDF, DOC or DOCX format.', DJP_DOMAIN );
			}
		}

		if ( count( $errors ) ) {
			wp_send_json_error( $errors );
		}

		$latest_cv_date = date( 'Y-m-d H:i:s' );
		if ( ! empty( $document_cv ) ) {
			/* New document provided */
			$document_cv = media_handle_upload( 'employee_document_cv', 0 );
			if ( is_wp_error( $document_cv ) ) {
				wp_send_json_error( $document_cv->get_error_message() );
			}
			/* New document provided and there is saved document */
			if ( ! empty( $saved_document_cv ) ) {
				$delete_saved_document_cv = true;
			}
		} elseif ( ! empty( $saved_document_cv ) ) {
			/* New document not provided and there is saved document */
			$document_cv    = $saved_document_cv;
			$latest_cv_date = $saved_latest_cv_date;
		} else {
			/* New document not provided and there is no saved document */
			$document_cv    = NULL;
			$latest_cv_date = NULL;
		}

		$status = wp_update_post(
		 	array(
		 		'ID'             => $post_id,
		 		'post_title'     => $personal_name,
		 		'post_type'      => 'employee',
		 		'post_status'    => 'publish',
		 		'comment_status' => 'closed',
		 		'ping_status'    => 'closed'
		 	), true
	 	);

		if ( is_wp_error( $status ) ) {
			wp_send_json_error( $status->get_error_message() );
		}

		$document = array(
			'cv'             => $document_cv,
			'latest_cv_date' => $latest_cv_date
		);

		$personal = array(
			'name'          => $personal_name,
			'email'         => $personal_email,
			'mobile'        => $personal_mobile,
			'date_of_birth' => $personal_date_of_birth,
			'location'      => $personal_location,
			'gender'        => $personal_gender
		);

		$work_experience = array(
			'profile_title'    => $work_experience_profile_title,
			'profile_summary'  => $work_experience_profile_summary,
			'total_experience' => array(
				'year'  => $work_experience_total_experience_year,
				'month' => $work_experience_total_experience_month
			),
			'salary'           => $work_experience_salary,
			'notice_period'    => $work_experience_notice_period,
			'last_working_day' => $work_experience_last_working_day
		);

		$employment = array();
		foreach( $employment_job_title as $key => $job_title ) {
			array_push( $employment, array(
				'job_title'     => $job_title,
				'company_name'  => isset( $employment_company_name[$key] ) ? $employment_company_name[$key] : NULL,
				'industry'      => isset( $employment_industry[$key] ) ? $employment_industry[$key] : NULL,
				'duration_from' => isset( $employment_duration_from[$key] ) ? $employment_duration_from[$key] : NULL,
				'duration_to'   => isset( $employment_duration_to[$key] ) ? $employment_duration_to[$key] : NULL
			) );
		}

		$education = array();
		foreach( $education_specialization as $key => $specialization ) {
			array_push( $education, array(
				'specialization'  => $specialization,
				'institute_name'  => isset( $education_institute_name[$key] ) ? $education_institute_name[$key] : NULL,
				'course_type'     => isset( $education_course_type[$key] ) ? $education_course_type[$key] : NULL,
				'year_of_passing' => isset( $education_year_of_passing[$key] ) ? $education_year_of_passing[$key] : NULL
			) );
		}

		$certification = array();
		foreach( $certification_title as $key => $title ) {
			array_push( $certification, array(
				'certification_title'   => $title,
				'year_of_certification' => isset( $certification_year[$key] ) ? $certification_year[$key] : NULL,
			) );
		}

		if ( $desired_job_locations ) {
			$desired_job_locations = explode( ',', $desired_job_locations );
			$desired_job_locations = array_map( 'trim', $desired_job_locations );
		} else {
			$desired_job_locations = array();
		}

		$desired_job = array(
			'locations'   => $desired_job_locations,
			'industry'    => $desired_job_industry,
			'departments' => $desired_job_departments,
			'salary'      => $desired_job_salary,
			'job_types'   => $desired_job_types
		);

		update_post_meta( $post_id, 'wljp_employee_user_id', $user_id );
		update_post_meta( $post_id, 'wljp_employee_personal', $personal );
		update_post_meta( $post_id, 'wljp_employee_document', $document );
		update_post_meta( $post_id, 'wljp_employee_work_experience', $work_experience );
		update_post_meta( $post_id, 'wljp_employee_employment', $employment );
		update_post_meta( $post_id, 'wljp_employee_education', $education );
		update_post_meta( $post_id, 'wljp_employee_skills', $skills );
		update_post_meta( $post_id, 'wljp_employee_certification', $certification );
		update_post_meta( $post_id, 'wljp_employee_desired_job', $desired_job );

		if ( isset( $delete_saved_document_cv ) ) {
			wp_delete_attachment( $saved_document_cv, true );
		}

		wp_send_json_success( array( 'message' => esc_html__( 'Your CV has been updated.', DJP_DOMAIN ), 'reload' => true ) );
	}

	/**
	 * Delete cv
	 * @return void
	 */
	public static function delete_cv() {
		if ( ! wp_verify_nonce( $_POST["security"], "wljp" ) ) {
			die();
		}

		$cv = WL_JP_Helper::user_has_cv( get_current_user_id() );

		if ( ! $cv ) {
			die();
		}

		$document    = get_post_meta( $cv->ID, 'wljp_employee_document', true );
		$document_cv = isset( $document['cv'] ) ? esc_attr( $document['cv'] ) : '';

		$success = wp_delete_post( $cv->ID, true );

		if ( ! $success ) {
  			throw new Exception( esc_html__( 'An unexpected error occurred.', DJP_DOMAIN ) );
		}

		if ( ! empty( $document_cv ) ) {
			wp_delete_attachment( $document_cv, true );
		}

		wp_send_json_success( array( 'message' => esc_html__( 'Your CV has been deleted.', DJP_DOMAIN ), 'reload' => true ) );
	}

	/* Apply to job */
	public static function apply_to_job() {
		$employee = WL_JP_Helper::user_has_cv( get_current_user_id() );
		if ( ! $employee ) {
			die();
		}
		if ( ! wp_verify_nonce( $_POST["security"], "wljp" ) ) {
			die();
		}

		$job_id = intval( sanitize_text_field( $_POST['id'] ) );

		global $wpdb;

		/* Check if job exists */
		$job = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}posts WHERE ID = $job_id AND post_type = 'job_listing' AND post_status = 'publish'" );
		if ( ! $job ) {
			die();
		}

		$data = array(
			'employee_id' => $employee->ID,
			'job_id'       => $job->ID,
		);

		try {
			$wpdb->query( 'BEGIN;' );

			$success = $wpdb->insert( "{$wpdb->prefix}wljp_employee_job", $data );
			if ( ! $success ) {
				throw new Exception( esc_html__( 'An unexpected error occurred.', DJP_DOMAIN ) );
			}
			$wpdb->query( 'COMMIT;' );

			wp_send_json_success( array( 'message' => esc_html__( 'You have applied.', DJP_DOMAIN ) ) );
		} catch ( Exception $exception ) {
			$wpdb->query( 'ROLLBACK;' );
			wp_send_json_error( $exception->getMessage() );
		}
	}
}
?>
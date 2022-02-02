<?php
defined( 'ABSPATH' ) || die();

require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'lib/WL_JP_Helper.php' );

class WL_JP_Employee {
	/**
	 * Add metaboxes to employee post type
	 * @return void
	 */
	public static function add_meta_boxes() {
	    add_meta_box( 'wljp_employee_account', __( 'Account Settings', EJP_DOMAIN ), array( 'WL_JP_Employee', 'account_html' ), 'employee', 'advanced' );
	    add_meta_box( 'wljp_employee_personal', __( 'Personal', EJP_DOMAIN ), array( 'WL_JP_Employee', 'personal_html' ), 'employee', 'advanced' );
	    add_meta_box( 'wljp_employee_document', __( 'Document', EJP_DOMAIN ), array( 'WL_JP_Employee', 'document_html' ), 'employee', 'side' );
	    add_meta_box( 'wljp_employee_work_experience', __( 'Work Experience', EJP_DOMAIN ), array( 'WL_JP_Employee', 'work_experience_html' ), 'employee', 'advanced' );
	    add_meta_box( 'wljp_employee_employment', __( 'Employment', EJP_DOMAIN ), array( 'WL_JP_Employee', 'employment_html' ), 'employee', 'advanced' );
	    add_meta_box( 'wljp_employee_education', __( 'Education', EJP_DOMAIN ), array( 'WL_JP_Employee', 'education_html' ), 'employee', 'advanced' );
	    add_meta_box( 'wljp_employee_skills', __( 'Skills', EJP_DOMAIN ), array( 'WL_JP_Employee', 'skills_html' ), 'employee', 'side' );
	    add_meta_box( 'wljp_employee_certification', __( 'Certification', EJP_DOMAIN ), array( 'WL_JP_Employee', 'certification_html' ), 'employee', 'advanced' );
	    add_meta_box( 'wljp_employee_desired_job', __( 'Desired Job Details', EJP_DOMAIN ), array( 'WL_JP_Employee', 'desired_job_html' ), 'employee', 'advanced' );
	}

	/**
	 * Render html of account metabox
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function account_html( $post ) {
		$post_id       = $post->ID;
		$user_id       = get_post_meta( $post_id, 'wljp_employee_user_id', true );
		$user          = get_user_by( 'id', $user_id );
		if ( $user ) {
			$email    = $user->user_email;
			$username = $user->user_login;
		} else {
			$email = '';
			$username = '';
		}
	?>
		<?php wp_nonce_field( 'save_employee_meta', 'employee_meta' ); ?>
		<div class="wljp" id="wljp_employee_account">
			<div class="row mt-2">
				<div class="col-sm-6">
					<?php if ( $username ) : ?>
					<label><?php _e( 'Username', EJP_DOMAIN ); ?>:</label><br>
					<span><strong><?php echo esc_html($username); ?></strong></span>
					<?php else : ?>
					<label for="wljp_employee_account_username"><?php esc_html_e( 'Username', EJP_DOMAIN ); ?>:</label>
					<input type="text" name="employee_account_username" id="wljp_employee_account_username" class="widefat" value="<?php echo esc_attr($username); ?>" required>
					<?php endif; ?>
				</div>
				<div class="col-sm-6">
					<label for="wljp_employee_account_email"><?php esc_html_e( 'Email Address', EJP_DOMAIN ); ?>:</label>
					<input type="email" name="employee_account_email" id="wljp_employee_account_email" class="widefat" value="<?php echo esc_attr($email); ?>" required>
				</div>
			</div>
			<div class="row mt-2">
				<div class="col-sm-6">
					<label for="wljp_employee_account_password"><?php esc_html_e( 'Password', EJP_DOMAIN ); ?>:</label>
					<input type="password" name="employee_account_password" id="wljp_employee_account_password" class="widefat">
				</div>
				<div class="col-sm-6">
					<label for="wljp_employee_account_confirm_password"><?php esc_html_e( 'Confirm Password', EJP_DOMAIN ); ?>:</label>
					<input type="password" name="employee_account_confirm_password" id="wljp_employee_account_confirm_password" class="widefat">
				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Render html of personal metabox
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function personal_html( $post ) {
		$post_id       = $post->ID;
		$name          = $post->post_title;
		$personal      = get_post_meta( $post_id, 'wljp_employee_personal', true );
		$email         = isset( $personal['email'] ) ? esc_attr( $personal['email'] ) : '';
		$mobile        = isset( $personal['mobile'] ) ? esc_attr( $personal['mobile'] ) : '';
		$date_of_birth = isset( $personal['date_of_birth'] ) ? esc_attr( $personal['date_of_birth'] ) : '';
		$location      = isset( $personal['location'] ) ? esc_attr( $personal['location'] ) : '';
		$gender        = isset( $personal['gender'] ) ? esc_attr( $personal['gender'] ) : '';
	?>
		<div class="wljp" id="wljp_employee_personal">
			<div class="row">
				<?php if ( $name ) : ?>
				<div class="col-sm-6">
					<label><?php esc_html_e( 'Name', EJP_DOMAIN ); ?>:</label><br>
					<span><?php echo esc_html($name); ?></span>
				</div>
				<?php endif; ?>
				<div class="col-sm-6 mt-2">
					<label for="wljp_employee_personal_email"><?php esc_html_e( 'Email Address', EJP_DOMAIN ); ?>:</label>
					<input type="email" name="employee_personal_email" id="wljp_employee_personal_email" class="widefat" value="<?php echo esc_attr($email); ?>" required>
				</div>
				<div class="col-sm-6 mt-2">
					<label for="wljp_employee_personal_mobile"><?php esc_html_e( 'Mobile', EJP_DOMAIN ); ?>:</label>
					<input type="text" name="employee_personal_mobile" id="wljp_employee_personal_mobile" class="widefat" value="<?php echo esc_attr($mobile); ?>">
				</div>
				<div class="col-sm-6 mt-2">
					<label for="wljp_employee_personal_date_of_birth"><?php esc_html_e( 'Date of Birth', EJP_DOMAIN ); ?>:</label>
					<input type="date" name="employee_personal_date_of_birth" id="wljp_employee_personal_date_of_birth" class="widefat" value="<?php echo esc_attr($date_of_birth); ?>">
				</div>
				<div class="col-sm-6 mt-2">
					<label for="wljp_employee_personal_location"><?php esc_html_e( 'Location', EJP_DOMAIN ); ?>:</label>
					<input type="text" name="employee_personal_location" id="wljp_employee_personal_location" class="widefat" value="<?php echo esc_attr($location); ?>">
				</div>
				<div class="col-sm-6 mt-2">
					<label for="wljp_employee_personal_gender"><?php esc_html_e( 'Gender', EJP_DOMAIN ); ?>:</label>
					<select name="employee_personal_gender" id="wljp_employee_personal_gender" class="widefat">
					<?php foreach( WL_JP_Helper::get_gender_list() as $key => $value ) : ?>
						<option <?php selected( $key, $gender ); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
					<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Render html of document metabox
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function document_html( $post ) {
		$post_id                 = $post->ID;
		$document                = get_post_meta( $post_id, 'wljp_employee_document', true );
		$document_cv             = isset( $document['cv'] ) ? esc_attr( $document['cv'] ) : '';
		$document_latest_cv_date = isset( $document['latest_cv_date'] ) ? esc_attr( $document['latest_cv_date'] ) : '';
	?>
		<div class="wljp" id="wljp_employee_document">
			<?php if ( ! empty ( $document_cv ) ) { ?>
				<a href="<?php echo wp_get_attachment_url( $document_cv ); ?>" target="_blank" class="font-weight-bold"><?php esc_html_e( 'Latest CV', EJP_DOMAIN );
						if ( ! empty( $document_latest_cv_date ) ) {
							echo " " . esc_html__( 'uploaded on', EJP_DOMAIN ) . " " . date_format( date_create( $document_latest_cv_date ), "d-m-Y" );
						} ?>
				</a>
			<?php } ?>
            <div class="row mt-2">
				<div class="form-group col-sm-12">
                    <label for="wljp_employee_document_cv" class="col-form-label"><?php esc_html_e( 'CV in PDF, DOC or DOCX format', EJP_DOMAIN ); ?>:</label><br>
                    <input name="employee_document_cv" type="file" id="wljp_employee_document_cv" class="w-100 d-block col">
				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Render html of work_experience metabox
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function work_experience_html( $post ) {
		$post_id                = $post->ID;
		$work_experience        = get_post_meta( $post_id, 'wljp_employee_work_experience', true );
		$profile_title          = isset( $work_experience['profile_title'] ) ? esc_attr( $work_experience['profile_title'] ) : '';
		$profile_summary        = isset( $work_experience['profile_summary'] ) ? esc_attr( $work_experience['profile_summary'] ) : '';
		/* An associative array with keys: years and months */
		$total_experience       = ( isset( $work_experience['total_experience'] ) && is_array( $work_experience['total_experience'] ) ) ? $work_experience['total_experience'] : array();
		$total_experience_year  = isset( $total_experience['year'] ) ? esc_attr( $total_experience['year'] ) : '';
		$total_experience_month = isset( $total_experience['month'] ) ? esc_attr( $total_experience['month'] ) : '';
		$salary                 = isset( $work_experience['salary'] ) ? esc_attr( $work_experience['salary'] ) : '';
		$notice_period          = isset( $work_experience['notice_period'] ) ? esc_attr( $work_experience['notice_period'] ) : '';
		/* If notice period is "Currently Serving Notice Period" */
		$last_working_day       = isset( $work_experience['last_working_day'] ) ? esc_attr( $work_experience['last_working_day'] ) : '';
	?>
		<div class="wljp" id="wljp_employee_work_experience">
			<div class="row">
				<div class="col-sm-12 mt-2">
					<label for="wljp_employee_work_experience_profile_title"><?php esc_html_e( 'Profile Title', EJP_DOMAIN ); ?>:</label>
					<input type="text" name="employee_work_experience_profile_title" id="wljp_employee_work_experience_profile_title" class="widefat" value="<?php echo esc_attr($profile_title); ?>">
				</div>
				<div class="col-sm-12 mt-2">
					<label for="wljp_employee_work_experience_profile_summary"><?php esc_html_e( 'Profile Summary', EJP_DOMAIN ); ?>:</label>
					<input type="text" name="employee_work_experience_profile_summary" id="wljp_employee_work_experience_profile_summary" class="widefat" value="<?php echo esc_attr($profile_summary); ?>">
				</div>
				<div class="col-sm-12 mt-2">
					<label for="wljp_employee_work_experience_total_experience"><?php esc_html_e( 'Total Experience', EJP_DOMAIN ); ?>:</label>
				</div>
				<div class="col-sm-6">
					<select name="employee_work_experience_total_experience[year]" id="wljp_employee_work_experience_total_experience_year" class="widefat">
					<?php foreach( WL_JP_Helper::total_experience_years() as $key => $value ) : ?>
						<option <?php selected( $key, $total_experience_year ); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
					<?php endforeach; ?>
					</select>
				</div>
				<div class="col-sm-6">
					<select name="employee_work_experience_total_experience[month]" id="wljp_employee_work_experience_total_experience_month" class="widefat">
					<?php foreach( WL_JP_Helper::total_experience_months() as $key => $value ) : ?>
						<option <?php selected( $key, $total_experience_month ); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
					<?php endforeach; ?>
					</select>
				</div>
				<div class="col-sm-6 mt-2">
					<label for="wljp_employee_work_experience_salary"><?php esc_html_e( 'Current / Latest Annual Salary', EJP_DOMAIN ); ?>:</label>
					<input type="text" name="employee_work_experience_salary" id="wljp_employee_work_experience_salary" class="widefat" value="<?php echo esc_attr($salary); ?>">
				</div>
				<div class="col-sm-6 mt-2">
					<label for="wljp_employee_work_experience_notice_period"><?php esc_html_e( 'Notice Period', EJP_DOMAIN ); ?>:</label>
					<select name="employee_work_experience_notice_period" id="wljp_employee_work_experience_notice_period" class="widefat">
					<?php foreach( WL_JP_Helper::notice_period_list() as $key => $value ) : ?>
						<option <?php selected( $key, $notice_period ); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
					<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-12 mt-2 wljp_last_working_day float-right">
					<div class="row">
						<div class="col-md-6"></div>
						<div class="col-md-6">
							<label for="wljp_employee_work_experience_last_working_day"><?php esc_html_e( 'Last working day', EJP_DOMAIN ); ?>:</label>
							<input type="date" name="employee_work_experience_last_working_day" id="wljp_employee_work_experience_last_working_day" class="widefat" value="<?php echo esc_attr($last_working_day); ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Render html of employment metabox
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function employment_html( $post ) {
		$post_id    = $post->ID;
		/* Array of array having keys: job_title, company_name, industry, duration_from, duration_to */
		$employment = get_post_meta( $post_id, 'wljp_employee_employment', true );
	?>
		<div class="wljp" id="wljp_employee_employment">
			<div id="wljp_employee_employment_rows">
				<?php if ( is_array( $employment ) && count ( $employment ) > 0 ) :
						foreach( $employment as $key => $value ) : 
							$job_title     = isset( $value['job_title'] ) ? esc_attr( $value['job_title'] ) : '';
							$company_name  = isset( $value['company_name'] ) ? esc_attr( $value['company_name'] ) : '';
							$industry      = isset( $value['industry'] ) ? esc_attr( $value['industry'] ) : '';
							$duration_from = isset( $value['duration_from'] ) ? esc_attr( $value['duration_from'] ) : '';
							$duration_to   = isset( $value['duration_to'] ) ? esc_attr( $value['duration_to'] ) : '';
						?>
				<div class="row wljp_employee_employment_row mt-2">
					<div class="col-sm-12 mt-2">
						<span class="employee_employment_remove_label employee_remove_label">X</span>
						<label><?php esc_html_e( 'Job Title', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_employment_job_title[]" class="widefat" value="<?php echo esc_attr($job_title); ?>">
					</div>
					<div class="col-sm-6 mt-2">
						<label><?php esc_html_e( 'Company Name', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_employment_company_name[]" class="widefat" value="<?php echo esc_attr($company_name); ?>">
					</div>
					<div class="col-sm-6 mt-2">
						<label><?php esc_html_e( 'Industry', EJP_DOMAIN ); ?>:</label>
						<select name="employee_employment_industry[]" class="widefat">
						<?php foreach( WL_JP_Helper::industries() as $key => $value ) : ?>
							<option <?php selected( $key, $industry, true ); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
						<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-6 mt-2">
						<label><?php esc_html_e( 'Duration from', EJP_DOMAIN ); ?>:</label>
						<input type="date" name="employee_employment_duration_from[]" class="widefat" value="<?php echo esc_attr($duration_from); ?>">
					</div>
					<div class="col-md-6 mt-2">
						<label><?php esc_html_e( 'Duration to', EJP_DOMAIN ); ?>:</label>
						<input type="date" name="employee_employment_duration_to[]" class="widefat" value="<?php echo esc_attr($duration_to); ?>">
					</div>
				</div>
					<?php endforeach;
				else : ?>
				<div class="row wljp_employee_employment_row mt-2">
					<div class="col-sm-12 mt-2">
						<span class="employee_employment_remove_label employee_remove_label">X</span>
						<label><?php esc_html_e( 'Job Title', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_employment_job_title[]" class="widefat">
					</div>
					<div class="col-sm-6 mt-2">
						<label><?php esc_html_e( 'Company Name', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_employment_company_name[]" class="widefat">
					</div>
					<div class="col-sm-6 mt-2">
						<label><?php esc_html_e( 'Industry', EJP_DOMAIN ); ?>:</label>
						<select name="employee_employment_industry" class="widefat">
						<?php foreach( WL_JP_Helper::industries() as $key => $value ) : ?>
							<option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
						<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-6 mt-2">
						<label><?php esc_html_e( 'Duration from', EJP_DOMAIN ); ?>:</label>
						<input type="date" name="employee_employment_duration_from" class="widefat">
					</div>
					<div class="col-md-6 mt-2">
						<label><?php esc_html_e( 'Duration to', EJP_DOMAIN ); ?>:</label>
						<input type="date" name="employee_employment_duration_to" class="widefat">
					</div>
				</div>
				<?php endif; ?>
			</div>
			<button type="button" id="wljp_employee_employment_row_add_more" class="wljp_row_add_more"><?php esc_html_e( 'Add more', EJP_DOMAIN ); ?></button>
		</div>
	<?php
	}

	/**
	 * Render html of education metabox
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function education_html( $post ) {
		$post_id   = $post->ID;
		/* Array of array having keys: education_specialization, institute_name, course_type, year_of_passing */
		$education = get_post_meta( $post_id, 'wljp_employee_education', true );
	?>
		<div class="wljp" id="wljp_employee_education">
			<div id="wljp_employee_education_rows">
				<?php if ( is_array( $education ) && count ( $education ) > 0 ) :
						foreach( $education as $key => $value ) :
							$specialization  = isset( $value['specialization'] ) ? esc_attr( $value['specialization'] ) : '';
							$institute_name  = isset( $value['institute_name'] ) ? esc_attr( $value['institute_name'] ) : '';
							$course_type     = isset( $value['course_type'] ) ? esc_attr( $value['course_type'] ) : '';
							$year_of_passing = isset( $value['year_of_passing'] ) ? esc_attr( $value['year_of_passing'] ) : '';
						?>
				<div class="row wljp_employee_education_row">
					<div class="col-sm-6 mt-2">
						<span class="employee_education_remove_label employee_remove_label">X</span>
						<label><?php esc_html_e( 'Education Specialization', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_education_specialization[]" class="widefat" value="<?php echo esc_attr($specialization); ?>">
					</div>
					<div class="col-sm-6 mt-2">
						<label><?php esc_html_e( 'Institute Name', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_education_institute_name[]" class="widefat" value="<?php echo esc_attr($institute_name); ?>">
					</div>
					<div class="col-sm-6 mt-2">
						<label><?php esc_html_e( 'Course Type', EJP_DOMAIN ); ?>:</label>
						<select name="employee_education_course_type[]" class="widefat">
						<?php foreach( WL_JP_Helper::course_types() as $key => $value ) : ?>
							<option <?php selected( $key, $course_type, true ); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
						<?php endforeach; ?>
						</select>
					</div>
					<div class="col-sm-6 mt-2">
						<label><?php esc_html_e( 'Year of Passing', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_education_year_of_passing[]" class="widefat" placeholder="<?php _e( 'Format: XXXX', EJP_DOMAIN ); ?>" value="<?php echo esc_html($year_of_passing); ?>">
					</div>
				</div>
					<?php endforeach;
				else : ?>
				<div class="row wljp_employee_education_row">
					<div class="col-sm-6 mt-2">
						<span class="employee_education_remove_label employee_remove_label">X</span>
						<label><?php esc_html_e( 'Education Specialization', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_education_specialization[]" class="widefat">
					</div>
					<div class="col-sm-6 mt-2">
						<label><?php esc_html_e( 'Institute Name', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_education_institute_name[]" class="widefat">
					</div>
					<div class="col-sm-6 mt-2">
						<label><?php esc_html_e( 'Course Type', EJP_DOMAIN ); ?>:</label>
						<select name="employee_education_course_type" class="widefat">
						<?php foreach( WL_JP_Helper::course_types() as $key => $value ) : ?>
							<option value="<?php echo esc_html($key); ?>"><?php echo esc_html($value); ?></option>
						<?php endforeach; ?>
						</select>
					</div>
					<div class="col-sm-6 mt-2">
						<label><?php _e( 'Year of Passing', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_education_year_of_passing[]" class="widefat" placeholder="<?php esc_attr_e( 'Format: XXXX', EJP_DOMAIN ); ?>">
					</div>
				</div>
				<?php endif; ?>
			</div>
			<button type="button" id="wljp_employee_education_row_add_more" class="wljp_row_add_more"><?php esc_html_e( 'Add more', EJP_DOMAIN ); ?></button>
		</div>
	<?php
	}

	/**
	 * Render html of skills metabox
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function skills_html( $post ) {
		$post_id  = $post->ID;
		/* Array of array having keys: skill, experience */
		$skills   = get_post_meta( $post_id, 'wljp_employee_skills', true );
	?>
		<div class="wljp" id="wljp_employee_skills">
			<div id="wljp_employee_skills_rows">
				<?php if ( is_array( $skills ) && count ( $skills ) > 0 ) :
						foreach( $skills as $value ) : ?>
				<div class="row wljp_employee_skills_row">
					<div class="col-sm-12 mt-2">
						<span class="employee_skills_remove_label employee_remove_label">X</span>
						<label><?php esc_html_e( 'Skill', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_skills[]" class="widefat" value="<?php echo esc_attr( $value ); ?>">
					</div>
				</div>
					<?php endforeach;
				else : ?>
				<div class="row wljp_employee_skills_row">
					<div class="col-sm-12 mt-2">
						<span class="employee_skills_remove_label employee_remove_label">X</span>
						<label><?php esc_html_e( 'Skill', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_skills[]" class="widefat">
					</div>
				</div>
				<?php endif; ?>
			</div>
			<button type="button" id="wljp_employee_skills_row_add_more" class="wljp_row_add_more"><?php esc_html_e( 'Add more', EJP_DOMAIN ); ?></button>
		</div>
	<?php
	}

	/**
	 * Render html of certification metabox
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function certification_html( $post ) {
		$post_id       = $post->ID;
		/* Array of array having keys: certification_title, year_of_certification */
		$certification = get_post_meta( $post_id, 'wljp_employee_certification', true );
	?>
		<div class="wljp" id="wljp_employee_certification">
			<div id="wljp_employee_certification_rows">
				<?php if ( is_array( $certification ) && count ( $certification ) > 0 ) :
						foreach( $certification as $key => $value ) :
							$certification_title   = isset( $value['certification_title'] ) ? esc_attr( $value['certification_title'] ) : '';
							$year_of_certification = isset( $value['year_of_certification'] ) ? esc_attr( $value['year_of_certification'] ) : '';
						?>
				<div class="row wljp_employee_certification_row">
					<div class="col-sm-8 mt-2">
						<span class="employee_certification_remove_label employee_remove_label">X</span>
						<label><?php esc_html_e( 'Certification Title', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_certification_title[]" class="widefat" value="<?php echo esc_attr( $certification_title ); ?>">
					</div>
					<div class="col-sm-4 mt-2">
						<label><?php esc_html_e( 'Year of Certification', EJP_DOMAIN ); ?>:</label>
						<input type="number" step="1" name="employee_certification_year[]" class="widefat" placeholder="<?php esc_attr_e( 'Format: XXXX', EJP_DOMAIN ); ?>" value="<?php echo esc_attr( $year_of_certification ); ?>">
					</div>
				</div>
					<?php endforeach;
				else : ?>
				<div class="row wljp_employee_certification_row">
					<div class="col-sm-8 mt-2">
						<span class="employee_certification_remove_label employee_remove_label">X</span>
						<label><?php esc_html_e( 'Certification Title', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="employee_certification_title[]" class="widefat">
					</div>
					<div class="col-sm-4 mt-2">
						<label><?php esc_html_e( 'Year of Certification', EJP_DOMAIN ); ?>:</label>
						<input type="number" step="1" name="employee_certification_year[]" class="widefat">
					</div>
				</div>
				<?php endif; ?>
			</div>
			<button type="button" id="wljp_employee_certification_row_add_more" class="wljp_row_add_more"><?php esc_html_e( 'Add more', EJP_DOMAIN ); ?></button>
		</div>
	<?php
	}

	/**
	 * Render html of desired_job metabox
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function desired_job_html( $post ) {
		$post_id     = $post->ID;
		/* Associative array having keys: locations, industry, salary, job_type */
		$desired_job        = get_post_meta( $post_id, 'wljp_employee_desired_job', true );
		$locations          = ( isset( $desired_job['locations'] ) && is_array( $desired_job['locations'] ) ) ? $desired_job['locations'] : array();
		$locations_string   = implode( ', ', $locations );
		$industry           = isset( $desired_job['industry'] ) ? esc_attr( $desired_job['industry'] ) : '';
		$departments        = ( isset( $desired_job['departments'] ) && is_array( $desired_job['departments'] ) ) ? $desired_job['departments'] : array();
		$salary             = isset( $desired_job['salary'] ) ? esc_attr( $desired_job['salary'] ) : '';
		$job_types          = ( isset( $desired_job['job_types'] ) && is_array( $desired_job['job_types'] ) ) ? $desired_job['job_types'] : array();
	?>
		<div class="wljp" id="wljp_employee_desired_job">
			<div class="row">
				<div class="col-sm-12 mt-2">
					<label for="wljp_employee_desired_job_locations"><?php esc_html_e( 'Job Locations', EJP_DOMAIN ); ?>:</label>
					<input type="text" name="employee_desired_job_locations" id="wljp_employee_desired_job_locations" class="widefat" value="<?php echo esc_attr($locations_string); ?>" placeholder="<?php esc_attr_e( 'Separated by comma', EJP_DOMAIN ); ?>">
				</div>
				<div class="col-sm-6 mt-2">
					<label for="wljp_employee_desired_job_industry"><?php esc_attr_e( 'Industry', EJP_DOMAIN ); ?>:</label>
					<select name="employee_desired_job_industry" id="wljp_employee_desired_job_industry" class="widefat">
					<?php foreach( WL_JP_Helper::industries() as $key => $value ) : ?>
						<option <?php selected( $key, $industry ); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
					<?php endforeach; ?>
					</select>
				</div>
				<div class="col-sm-6 mt-2">
					<label for="wljp_employee_desired_job_salary"><?php esc_html_e( 'Salary', EJP_DOMAIN ); ?>:</label>
					<input type="text" name="employee_desired_job_salary" id="wljp_employee_desired_job_salary" class="widefat" value="<?php echo esc_attr($salary); ?>">
				</div>
				<div class="col-sm-6 mt-2">
					<label for="wljp_employee_desired_job_departments"><?php esc_html_e( 'Departments', EJP_DOMAIN ); ?>:</label><br>
					<select data-placeholder="<?php esc_attr_e( 'Select departments', EJP_DOMAIN ); ?>" name="employee_desired_job_departments[]" id="wljp_employee_desired_job_departments" class="widefat" multiple>
					<?php $department_array = WL_JP_Helper::departments();
						foreach( $department_array as $key => $value ) : ?>
						<option <?php selected( true, in_array( $key, $departments ), true ); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
					<?php endforeach; ?>
					</select>
				</div>
				<div class="col-sm-6 mt-2">
					<label for="wljp_employee_desired_job_types"><?php esc_html_e( 'Job Types', EJP_DOMAIN ); ?>:</label>
					<select data-placeholder="<?php esc_attr_e( 'Select job types', EJP_DOMAIN ); ?>" name="employee_desired_job_types[]" id="wljp_employee_desired_job_types" class="widefat" multiple>
					<?php $job_type_array = WL_JP_Helper::job_types();
						foreach( $job_type_array as $key => $value ) : ?>
						<option <?php selected( true, in_array( $key, $job_types ), true ); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></option>
					<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Enqueue scripts and styles to admin employee post type
	 * @param  string $hook_suffix
	 * @return void
	 */
	public static function enqueue_scripts_styles( $hook_suffix ) {
	    if ( in_array( $hook_suffix, array('post.php', 'post-new.php') ) ) {
	        $screen = get_current_screen();
	        if ( is_object( $screen ) && 'employee' == $screen->post_type ) {
	        	/* Enqueue styles */
				wp_enqueue_style( 'wljp-bootstrap', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/bootstrap.min.css' );
				wp_enqueue_style( 'wljp-fSelect', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/fSelect.css' );
				wp_enqueue_style( 'wljp-admin', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/wljp-admin.css' );

	        	/* Enqueue scripts */
				wp_enqueue_script( 'wljp-jquery-validate-js', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/jquery.validate.min.js', array( 'jquery' ), true, true );
				wp_enqueue_script( 'wljp-fSelect-js', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/fSelect.js', array( 'jquery' ), true, true );
				wp_enqueue_script( 'wljp-admin-js', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/wljp-admin.js', array( 'jquery', 'wljp-jquery-validate-js' ), true, true );
	        }
	    }
	}

	/**
	 * Change title text for employee post type
	 * @param  string $title
	 * @return string
	 */
	public static function change_title_text( $title ){
		$screen = get_current_screen();
		if  ( 'employee' == $screen->post_type ) {
		  $title = esc_html__( 'Enter employee name', EJP_DOMAIN );
		}
		return $title;
	}

	/**
	 * Set employee columns
	 * @param array $columns
	 * @return array
	 */
	public static function set_columns( $columns ) {
		$newColumns = array();
		$newColumns['cb']    = $columns['cb'];
		$newColumns['title'] = esc_html__( 'Employee Name', EJP_DOMAIN );
		$newColumns['date'] = esc_html__( 'Date', EJP_DOMAIN );
		return $newColumns;
	}

	/**
	 * Save metaboxes values
	 * @param  int $post_id
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function save_metaboxes( $post_id, $post ) {
		if ( ! isset( $_POST['employee_meta'] ) || ! wp_verify_nonce( $_POST['employee_meta'], 'save_employee_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( wp_is_post_revision( $post ) ) {
			return;
		}
		if ( $post->post_type !== 'employee' ) {
			return;
		}

		$email            = isset( $_POST['employee_account_email'] ) ? sanitize_email( $_POST['employee_account_email'] ) : NULL;
		$password         = isset( $_POST['employee_account_password'] ) ? $_POST['employee_account_password'] : NULL;
		$confirm_password = isset( $_POST['employee_account_confirm_password'] ) ? $_POST['employee_account_confirm_password'] : NULL;

		$personal_name          = isset( $_POST['employee_personal_name'] ) ? sanitize_text_field( $_POST['employee_personal_name'] ) : NULL;
		$personal_email         = isset( $_POST['employee_personal_email'] ) ? sanitize_email( $_POST['employee_personal_email'] ) : NULL;
		$personal_mobile        = isset( $_POST['employee_personal_mobile'] ) ? $_POST['employee_personal_mobile'] : NULL;
		$personal_date_of_birth = isset( $_POST['employee_personal_date_of_birth'] ) ? $_POST['employee_personal_date_of_birth'] : NULL;
		$personal_location      = isset( $_POST['employee_personal_location'] ) ? $_POST['employee_personal_location'] : NULL;
		$personal_gender        = isset( $_POST['employee_personal_gender'] ) ? $_POST['employee_personal_gender'] : NULL;

		$saved_document       = get_post_meta( $post_id, 'wljp_employee_document', true );
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

		update_post_meta( $post_id, 'wljp_employee_personal', $personal );
		update_post_meta( $post_id, 'wljp_employee_work_experience', $work_experience );
		update_post_meta( $post_id, 'wljp_employee_employment', $employment );
		update_post_meta( $post_id, 'wljp_employee_education', $education );
		update_post_meta( $post_id, 'wljp_employee_skills', $skills );
		update_post_meta( $post_id, 'wljp_employee_certification', $certification );
		update_post_meta( $post_id, 'wljp_employee_desired_job', $desired_job );

		$user_id = get_post_meta( $post_id, 'wljp_employee_user_id', true );

		$user_data = array();
		if ( empty( $email ) || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {			
			return;
		}

		$user_data['user_email'] = $email;

		if ( ! empty( $password ) && ! empty( $confirm_password ) ) {
			if ( $password !== $confirm_password ) {
				return;
			} else {
				$user_data['user_pass'] = $password;
			}
		}

		$user_data['ID'] = $user_id;

		wp_update_user( $user_data );

		/* File uploads */
		if ( ! empty( $document_cv ) ) {
			$file_name          = sanitize_file_name( $document_cv['name'] );
			$file_type          = $document_cv['type'];
			$allowed_file_types = WL_JP_Helper::cv_document_file_types();

			if ( ! in_array( $file_type, $allowed_file_types ) ) {
				return;
			}
		}

		$latest_cv_date = date( 'Y-m-d H:i:s' );
		if ( ! empty( $document_cv ) ) {
			/* New document provided */
			$document_cv = media_handle_upload( 'employee_document_cv', 0 );
			if ( is_wp_error( $document_cv ) ) {
				return;
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

		$document = array(
			'cv'             => $document_cv,
			'latest_cv_date' => $latest_cv_date
		);

		update_post_meta( $post_id, 'wljp_employee_document', $document );

		if ( isset( $delete_saved_document_cv ) ) {
			wp_delete_attachment( $saved_document_cv, true );
		}
	}

	/**
	 * Delete employee's document
	 * @param  int $post_id
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function delete_document( $post_id ) {
	    global $post_type;
	    if ( $post_type != 'employee' ) {
	    	return;
	    }

		$document    = get_post_meta( $post_id, 'wljp_employee_document', true );
		$document_cv = isset( $document['cv'] ) ? esc_attr( $document['cv'] ) : '';

		if ( ! empty ( $document_cv ) ) {
			wp_delete_attachment( $document_cv, true );
		}
	}

	/**
	 * File uploads support
	 * @return void
	 */
	public static function edit_form_tag() {
	    global $post;

	    if ( $post && 'employee' === $post->post_type ) {
	        printf( ' enctype="multipart/form-data" encoding="multipart/form-data" ' );
	    }
	}
}
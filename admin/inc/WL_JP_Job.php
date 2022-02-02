<?php
defined( 'ABSPATH' ) || die();

class WL_JP_Job {
	/**
	 * Add metaboxes to job post type
	 * @return void
	 */
	public static function add_meta_boxes() {
	    add_meta_box( 'wljp_job_work_experience', esc_html__( 'Work Experience', EJP_DOMAIN ), array( 'WL_JP_Job', 'work_experience_html' ), 'job_listing', 'side' );
	    add_meta_box( 'wljp_job_salary', esc_html__( 'Salary', EJP_DOMAIN ), array( 'WL_JP_Job', 'job_salary_html' ), 'job_listing', 'advanced' );
	}

	/**
	 * Render html of work experience metabox
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function work_experience_html( $post ) {
		$post_id         = $post->ID;
		$work_experience = get_post_meta( $post_id, 'wljp_job_work_experience', true );
		$minimum         = isset( $work_experience['minimum'] ) ? esc_attr( $work_experience['minimum'] ) : '';
		$maximum         = isset( $work_experience['maximum'] ) ? esc_attr( $work_experience['maximum'] ) : '';
	?>
		<?php wp_nonce_field( 'save_job_meta', 'job_meta' ); ?>
		<div class="wljp" id="wljp_job_work_experience">
			<div class="mb-2">
				<label for="wljp_job_work_experience_minimum"><?php esc_html_e( 'Minimum Years', EJP_DOMAIN ); ?>:</label>
				<input type="number" name="job_work_experience_minimum" id="wljp_job_work_experience_minimum" class="widefat required" value="<?php echo esc_attr( $minimum ); ?>">
			</div>

			<div class="mt-2">
				<label for="wljp_job_work_experience_maximum"><?php esc_html_e( 'Maximum Years', EJP_DOMAIN ); ?>:</label>
				<input type="number" name="job_work_experience_maximum" id="wljp_job_work_experience_maximum" class="widefat required" value="<?php echo esc_attr( $maximum ); ?>">
			</div>
		</div>
	<?php
	}

	/**
	 * Render html of job salary metabox
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function job_salary_html( $post ) {
		$post_id = $post->ID;
		$salary  = get_post_meta( $post_id, 'wljp_job_salary', true );
		$type    = isset( $salary['type'] ) ? esc_attr( $salary['type'] ) : '';
		if ( $is_range = ( $type == 'range' ) ) {
			$minimum = isset( $salary['minimum'] ) ? esc_attr( $salary['minimum'] ) : '';
			$maximum = isset( $salary['maximum'] ) ? esc_attr( $salary['maximum'] ) : '';
		} elseif ( $type == 'unspecified' ) {
			$unspecified = true;
		}
	?>
		<div class="wljp" id="wljp_job_salary">
			<div class="form-check form-check-inline">
				<input <?php echo ! isset( $unspecified ) ? 'checked' : checked( $type, 'unspecified', false ); ?> class="form-check-input" type="radio" name="wljp_job_salary" id="wljp-job-salary-unspecified" value="unspecified">
				<label class="form-check-label" for="wljp-job-salary-unspecified"><?php esc_html_e( 'Unspecified', EJP_DOMAIN ); ?></label>
			</div>
			<div class="form-check form-check-inline">
				<input <?php checked( $type, 'negotiable', true ); ?> class="form-check-input" type="radio" name="wljp_job_salary" id="wljp-job-salary-negotiable" value="negotiable">
				<label class="form-check-label" for="wljp-job-salary-negotiable"><?php esc_html_e( 'Negotiable', EJP_DOMAIN ); ?></label>
			</div>
			<div class="form-check form-check-inline">
				<input <?php checked( $type, 'fixed', true ); ?> class="form-check-input" type="radio" name="wljp_job_salary" id="wljp-job-salary-fixed" value="fixed">
				<label class="form-check-label" for="wljp-job-salary-fixed"><?php esc_html_e( 'Fixed', EJP_DOMAIN ); ?></label>
			</div>
			<div class="form-check form-check-inline">
				<input <?php checked( $type, 'range', true ); ?> class="form-check-input" type="radio" name="wljp_job_salary" id="wljp-job-salary-range" value="range">
				<label class="form-check-label" for="wljp-job-salary-range"><?php esc_html_e( 'Range', EJP_DOMAIN ); ?></label>
			</div>

			<div class="wljp_job_salary">
				<div class="row mt-2">
					<div class="col-md-4 col-sm-6">
						<label for="wljp_job_salary_minimum"><?php esc_html_e( 'Minimum Salary', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="wljp_job_salary_minimum" id="wljp_job_salary_minimum" class="widefat" <?php if ( $is_range ) { echo "value='{$minimum}'"; } ?>>
					</div>  
					<div class="col-md-4 col-sm-6">
						<label for="wljp_job_salary_maximum"><?php esc_html_e( 'Maximum Salary', EJP_DOMAIN ); ?>:</label>
						<input type="text" name="wljp_job_salary_maximum" id="wljp_job_salary_maximum" class="widefat" <?php if ( $is_range ) { echo "value='{$maximum}'"; } ?>>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Enqueue scripts and styles to admin job post type
	 * @param  string $hook_suffix
	 * @return void
	 */
	public static function enqueue_scripts_styles( $hook_suffix ) {
	    if ( in_array( $hook_suffix, array('post.php', 'post-new.php') ) ) {
	        $screen = get_current_screen();
	        if ( is_object( $screen ) && 'job_listing' == $screen->post_type ) {
	        	/* Enqueue styles */
				wp_enqueue_style( 'wljp-bootstrap', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/bootstrap.min.css' );
				wp_enqueue_style( 'wljp-bootstrap', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/mdb.min.css' );
				wp_enqueue_style( 'wljp-admin', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/css/wljp-admin.css' );

	        	/* Enqueue scripts */
				wp_enqueue_script( 'wljp-jquery-validate-js', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/jquery.validate.min.js', array( 'jquery' ), true, true );
				wp_enqueue_script( 'wljp-admin-js', EASY_JOB_PORTAL_PLUGIN_URL . 'assets/js/wljp-admin.js', array( 'jquery', 'wljp-jquery-validate-js' ), true, true );
	        }
	    }
	}

	/**
	 * Save metaboxes values
	 * @param  int $post_id
	 * @param  WP_Post $post
	 * @return void
	 */
	public static function save_metaboxes( $post_id, $post ) {
		if ( ! isset( $_POST['job_meta'] ) || ! wp_verify_nonce( $_POST['job_meta'], 'save_job_meta' ) ) {
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
		if ( $post->post_type !== 'job_listing' ) {
			return;
		}

		/* Save work experience */
		$minimum         = isset( $_POST['job_work_experience_minimum'] ) ? sanitize_text_field( $_POST['job_work_experience_minimum'] ) : '';
		$maximum         = isset( $_POST['job_work_experience_maximum'] ) ? sanitize_text_field( $_POST['job_work_experience_maximum'] ) : '';
		$work_experience = array(
			'minimum' => $minimum,
			'maximum' => $maximum
		);
		update_post_meta( $post_id, 'wljp_job_work_experience', $work_experience );

		/* Save job salary */
		$salary = isset( $_POST['wljp_job_salary'] ) ? sanitize_text_field( $_POST['wljp_job_salary'] ) : '';
		$salary = array(
			'type'  => $salary
		);
		if ( $salary['type'] == 'range' ) {
			$minimum = isset( $_POST['wljp_job_salary_minimum'] ) ? sanitize_text_field( $_POST['wljp_job_salary_minimum'] ) : '';
			$maximum = isset( $_POST['wljp_job_salary_maximum'] ) ? sanitize_text_field( $_POST['wljp_job_salary_maximum'] ) : '';

			$salary['minimum'] = $minimum;
			$salary['maximum'] = $maximum;
		}
		update_post_meta( $post_id, 'wljp_job_salary', $salary );
	}
}
?>
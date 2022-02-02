<?php
defined( 'ABSPATH' ) || die();

// print_r(WL_JP_Helper::general_job_portal_page_url());

$job_portal_page_url = WL_JP_Helper::general_job_portal_page_url();
$account_page_url    = WL_JP_Helper::general_account_page_url();
$is_user_logged_in   = is_user_logged_in();
$signup_as           = 'employee';

if ( $is_user_logged_in ) {
	$signup_as = get_user_meta( get_current_user_id(), 'wljp_signup_as', true );
}

?>

<div class="wrap wljp">
	<div class="row justify-content-md-between">
		<div class="col-sm-12">
			<div class="float-right"> 
				<div class="row">
					<div class="col-sm-12 text-right wljp-job-portal-navigation">
						<a href="<?php echo esc_url($job_portal_page_url); ?>" class="wljp-job-portal-link pr-3 mb-3 border-bottom">&#8594; <?php esc_html_e( 'Back to Job Portal', EJP_DOMAIN ); ?></a>
					</div>

					<?php if ( $is_user_logged_in ) : ?> 
						<div class="col-sm-12 text-right wljp-logout-navigation">
							<a href="<?php echo wp_logout_url( $job_portal_page_url ); ?>" class="wljp-logout-link pr-3 pb-3"><?php esc_html_e( 'Logout', EJP_DOMAIN ); ?></a>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php
				if ( $is_user_logged_in ) :
					require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'public/inc/views/account/jobs_applied.php' );
				endif;
			?>
		</div>
	</div>

	<?php 
		if ( !$is_user_logged_in ) :
			/* Login - Signup */
			require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'public/inc/views/account/login_signup.php' );
			/* End Login - Signup */
		else : 
	?>
	<div class="wljp-profile-cv-company justify-content-md-between ">
		<?php
			/* Account Settings */
			require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'public/inc/views/account/settings.php' );
			/* End Account Settings */

			/* Employee Profile */
			require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'public/inc/views/account/employee.php' );
			/* End Employee Profile */
		?>
	</div>
	<?php endif; ?>
	
</div>
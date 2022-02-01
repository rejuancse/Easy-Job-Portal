<?php
/**
 * File containing the view for displaying the admin notice when user first activates DLJP.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="updated wpjm-message">
	<p>
		<?php
		echo wp_kses_post( __( 'You are nearly ready to start listing jobs with <strong>Easy Job Portal</strong>.', 'easy-job-portal' ) );
		?>
	</p>
	<p class="submit">
		<a href="<?php echo esc_url( admin_url( 'index.php?page=easy-job-portal-setup' ) ); ?>" class="button-primary"><?php esc_html_e( 'Run Setup Wizard', 'easy-job-portal' ); ?></a>
		<a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpjm_hide_notice', Easy_Job_Portal_Admin_Notices::NOTICE_CORE_SETUP ), 'easy_job_portal_hide_notices_nonce', '_wpjm_notice_nonce' ) ); ?>"><?php esc_html_e( 'Skip Setup', 'easy-job-portal' ); ?></a>
	</p>
</div>

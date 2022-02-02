<?php
/**
 * Show job application when viewing a single job listing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wp;
$job_portal_page_url = WL_JP_Helper::general_job_portal_page_url();
$account_page_url    = WL_JP_Helper::general_account_page_url();
$is_user_logged_in   = is_user_logged_in();
$employee           = false;
if ( $is_user_logged_in ) {
	$employee = WL_JP_Helper::user_has_cv( get_current_user_id() );
}

$post_id       = get_the_ID();

?>

<div class="job_application application">
    <?php if ( $employee ) :
		if ( $application = WL_JP_Helper::is_applied( $employee->ID, $post_id ) ) : ?>
			<strong class="wljp-job-apply-date"><i class="fa fa-check-circle text-success"></i>
		<?php
			printf(
				esc_html__( 'Already applied on %s', EJP_DOMAIN ),
				date_i18n( 'd M, Y', strtotime( $application->created_at ) )
			); 
		?>
		</strong>
		<?php else: ?>
			<button data-id="<?php echo esc_attr($post_id); ?>" data-message="<?php esc_attr_e( 'Are you sure to apply to this job?', EJP_DOMAIN ); ?>" id="wljp-job-apply-button">
				<?php esc_html_e( 'Apply Now', EJP_DOMAIN ); ?>
			</button>
		<?php endif; ?>
		<?php else : ?>
			<a href="<?php echo esc_url($account_page_url); ?>" class="application_button button"><?php esc_html_e( 'Apply for job', EJP_DOMAIN ); ?></a>	
	<?php endif; ?>
</div>

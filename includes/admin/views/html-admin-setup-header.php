<?php
/**
 * File containing the view used in the header of the setup pages.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap wp_easy_job_portal wp_easy_job_portal_addons_wrap">
	<h2><?php esc_html_e( 'Welcome to the Setup Easy Job Portal!', 'easy-job-portal' ); ?></h2>

	<ul class="easy-job-portal-setup-steps">
		<?php
		$step_classes          = array_fill( 1, 3, '' );
		$step_classes[ $step ] = 'easy-job-portal-setup-active-step';
		?>
		<li class="<?php echo sanitize_html_class( $step_classes[1] ); ?>">
			<span class="dashicons dashicons-flag"></span>
			<?php esc_html_e( ' Introduction', 'easy-job-portal' ); ?>
		</li>
		<li class="<?php echo sanitize_html_class( $step_classes[2] ); ?>">
			<span class="dashicons dashicons-hammer"></span>
			<?php esc_html_e( ' Page Setup', 'easy-job-portal' ); ?>
		</li>
		<li class="<?php echo sanitize_html_class( $step_classes[3] ); ?>">
			<span class="dashicons dashicons-yes"></span>
			<?php esc_html_e( ' Done', 'easy-job-portal' ); ?>
		</li>
	</ul>

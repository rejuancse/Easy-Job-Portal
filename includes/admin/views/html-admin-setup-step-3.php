<?php
/**
 * File containing the view for step 3 of the setup wizard.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h3><?php esc_html_e( 'You\'re ready to start using Easy Job Portal!', 'easy-job-portal' ); ?></h3>

<p><?php esc_html_e( 'Wondering what to do now? Here are some of the most common next steps:', 'easy-job-portal' ); ?></p>

<div class="wp-job-manager-support-the-plugin">
	<ul class="easy-job-portal-next-steps">
		<li>
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=job_listing&page=easy-job-portal-settings' ) ); ?>">
				<span class="dashicons dashicons-yes"></span>
				<?php esc_html_e( 'Tweak your settings', 'easy-job-portal' ); ?>	
			</a>
		</li>
		<li>
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=job_listing' ) ); ?>">
				<span class="dashicons dashicons-yes"></span>
				<?php esc_html_e( 'Add a job using the admin dashboard', 'easy-job-portal' ); ?>
			</a>
		</li>

		<?php $permalink = easy_job_portal_get_permalink( 'jobs' );
		if ( $permalink ) {
			?>
			<li>
				<a href="<?php echo esc_url( $permalink ); ?>">
					<span class="dashicons dashicons-yes"></span>
					<?php esc_html_e( 'View submitted job listings', 'easy-job-portal' ); ?>
				</a>
			</li>
		<?php } ?>

		<?php $permalink = easy_job_portal_get_permalink( 'submit_job_form' );
		if ( $permalink ) {
			?>
			<li>
				<a href="<?php echo esc_url( $permalink ); ?>">
					<span class="dashicons dashicons-yes"></span>
					<?php esc_html_e( 'Add a job via the front-end', 'easy-job-portal' ); ?>
				</a>
			</li>
		<?php } ?>

		<?php $permalink = easy_job_portal_get_permalink( 'job_dashboard' );
		if ( $permalink ) { ?>
			<li>
				<a href="<?php echo esc_url( $permalink ); ?>">
					<span class="dashicons dashicons-yes"></span>
					<?php esc_html_e( 'View the job dashboard', 'easy-job-portal' ); ?>
				</a>
			</li>
		<?php } ?>

	</ul>
</div>

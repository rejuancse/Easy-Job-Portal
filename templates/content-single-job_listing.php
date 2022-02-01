<?php
/**
 * Single job listing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post; ?>

<div class="single_job_listing">
	<?php if ( get_option( 'easy_job_portal_hide_expired_content', 1 ) && 'expired' === $post->post_status ) : ?>
		<div class="easy-job-portal-info"><?php _e( 'This listing has expired.', 'easy-job-portal' ); ?></div>
	<?php else : ?>
		<?php do_action( 'single_job_listing_start' ); ?>

		<div class="job_description">
			<?php wpjm_the_job_description(); ?>
		</div>

		<?php if ( employees_can_apply() ) : ?>
			<?php get_easy_job_portal_template( 'job-application.php' ); ?>
		<?php endif; ?>

		<?php do_action( 'single_job_listing_end' ); ?>
	<?php endif; ?>
</div>

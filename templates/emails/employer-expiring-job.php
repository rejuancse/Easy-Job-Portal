<?php
/**
 * Email content when notifying employers of an expiring job listing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @var WP_Post $job
 */
$job = $args['job_listing'];

/**
 * @var bool
 */
$expiring_today = $args['expiring_today'];

echo '<p>';
if ( $expiring_today ) {
	echo wp_kses_post(
		sprintf(
			__( 'The following job listing is expiring today from <a href="%s">%s</a>.', 'easy-job-portal' ),
			home_url(),
			get_bloginfo( 'name' )
		)
	);
} else {
	echo wp_kses_post(
		sprintf(
			__( 'The following job listing is expiring soon from <a href="%s">%s</a>.', 'easy-job-portal' ),
			home_url(),
			get_bloginfo( 'name' )
		)
	);
}
echo wp_kses_post(
	sprintf(
		' ' . __( 'Visit the <a href="%s">job listing dashboard</a> to manage the listing.', 'easy-job-portal' ),
		esc_url( easy_job_portal_get_permalink( 'job_dashboard' ) )
	)
);
echo '</p>';

/**
 * Show details about the job listing.
 */
do_action( 'easy_job_portal_email_job_details', $job, $email, false, $plain_text );

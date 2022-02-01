<?php
/**
 * Email content when notifying the administrator of an expiring job listing.
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

if ( $expiring_today ) {
	printf(
		esc_html__( 'The following job listing is expiring today from %s (%s).', 'easy-job-portal' ),
		esc_html( get_bloginfo( 'name' ) ),
		esc_url( home_url() )
	);
} else {
	printf(
		esc_html__( 'The following job listing is expiring soon from %s (%s).', 'easy-job-portal' ),
		esc_html( get_bloginfo( 'name' ) ),
		esc_url( home_url() )
	);
}
$edit_post_link = admin_url( sprintf( 'post.php?post=%d&amp;action=edit', $job->ID ) );
printf(
	' ' . esc_html__( 'Visit WordPress admin (%s) to manage the listing.', 'easy-job-portal' ),
	esc_url( $edit_post_link )
);

/**
 * Show details about the job listing.
 */
do_action( 'easy_job_portal_email_job_details', $job, $email, true, $plain_text );

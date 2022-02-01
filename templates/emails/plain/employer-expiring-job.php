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

if ( $expiring_today ) {
	printf( esc_html__( 'The following job listing is expiring today from %s (%s).', 'easy-job-portal' ), esc_html( get_bloginfo( 'name' ) ), esc_url( home_url() ) );
} else {
	printf( esc_html__( 'The following job listing is expiring soon from %s (%s).', 'easy-job-portal' ), esc_html( get_bloginfo( 'name' ) ), esc_url( home_url() ) );
}
printf( ' ' . esc_html__( 'Visit the job listing dashboard (%s) to manage the listing.', 'easy-job-portal' ), esc_url( easy_job_portal_get_permalink( 'job_dashboard' ) ) );

/**
 * Show details about the job listing.
 */
do_action( 'easy_job_portal_email_job_details', $job, $email, false, $plain_text );

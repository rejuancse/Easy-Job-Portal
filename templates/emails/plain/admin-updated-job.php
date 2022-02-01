<?php
/**
 * Email content when notifying admin of an updated job listing.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @var WP_Post $job
 */
$job = $args['job_listing'];

printf( esc_html__( 'A job listing has been updated on %s (%s).', 'easy-job-portal' ), esc_html( get_bloginfo( 'name' ) ), esc_url( home_url() ) );
switch ( $job->post_status ) {
	case 'publish':
		printf( ' ' . esc_html__( 'The changes have been published and are now available to the public.', 'easy-job-portal' ) );
		break;
	case 'pending':
		printf( ' ' . esc_html__( 'The job listing is not publicly available until the changes are approved by an administrator in the site\'s WordPress admin (%s).', 'easy-job-portal' ), esc_url( admin_url( 'edit.php?post_type=job_listing' ) ) );
		break;
}

/**
 * Show details about the job listing.
 */
do_action( 'easy_job_portal_email_job_details', $job, $email, true, $plain_text );

<?php
/**
 * Email content when notifying admin of a new job listing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @var WP_Post $job
 */
$job = $args['job_listing'];
?>
	<p><?php
		echo wp_kses_post(
			sprintf(
				__( 'A new job listing has been submitted to <a href="%s">%s</a>.', 'easy-job-portal' ),
				home_url(),
				get_bloginfo( 'name' )
			)
		);
		switch ( $job->post_status ) {
			case 'publish':
				printf( ' ' . esc_html__( 'It has been published and is now available to the public.', 'easy-job-portal' ) );
				break;
			case 'pending':
				echo wp_kses_post(
					sprintf(
						' ' . __( 'It is awaiting approval by an administrator in <a href="%s">WordPress admin</a>.','easy-job-portal' ),
						esc_url( admin_url( 'edit.php?post_type=job_listing' ) )
					)
				);
				break;
		}
		?></p>
<?php

/**
 * Show details about the job listing.
 */
do_action( 'easy_job_portal_email_job_details', $job, $email, true, $plain_text );

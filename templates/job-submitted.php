<?php
/**
* Notice when job has been submitted.
*
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wp_post_types;

switch ( $job_listing->post_status ) :
	case 'publish' :
		echo '<div class="easy-job-portal-message">' . wp_kses_post(
			sprintf(
				__( '%s listed successfully. To view your listing <a href="%s">click here</a>.', 'easy-job-portal' ),
				esc_html( $wp_post_types['job_listing']->labels->singular_name ),
				get_permalink( $job_listing->ID )
			)
		) . '</div>';
	break;
	case 'pending' :
		echo '<div class="easy-job-portal-message">' . wp_kses_post(
			sprintf(
				esc_html__( '%s submitted successfully. Your listing will be visible once approved.', 'easy-job-portal' ),
				esc_html( $wp_post_types['job_listing']->labels->singular_name )
			)
		) . '</div>';
	break;
	default :
		do_action( 'easy_job_portal_job_submitted_content_' . str_replace( '-', '_', sanitize_title( $job_listing->post_status ) ), $job_listing );
	break;
endswitch;

do_action( 'easy_job_portal_job_submitted_content_after', sanitize_title( $job_listing->post_status ), $job_listing );

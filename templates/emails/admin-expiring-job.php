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
$edit_post_link = admin_url( sprintf( 'post.php?post=%d&amp;action=edit', $job->ID ) );

echo '<p>';
if ( $expiring_today ) {
	// translators: %1$s placeholder is URL to the blog. %2$s placeholder is the name of the site.
	echo wp_kses_post( sprintf( __( 'The following job listing is expiring today from <a href="%1$s">%2$s</a>.', 'easy-job-portal' ), esc_url( home_url() ), esc_html( get_bloginfo( 'name' ) ) ) );
} else {
	// translators: %1$s placeholder is URL to the blog. %2$s placeholder is the name of the site.
	echo wp_kses_post( sprintf( __( 'The following job listing is expiring soon from <a href="%1$s">%2$s</a>.', 'easy-job-portal' ), esc_url( home_url() ), esc_html( get_bloginfo( 'name' ) ) ) );
}

echo ' ';

// translators: Placeholder is URL to site's WP admin.
echo wp_kses_post( sprintf( __( 'Visit <a href="%s">WordPress admin</a> to manage the listing.', 'easy-job-portal' ), esc_url( $edit_post_link ) ) );
echo '</p>';

/**
 * Show details about the job listing.
 */
do_action( 'easy_job_portal_email_job_details', $job, $email, true, $plain_text );

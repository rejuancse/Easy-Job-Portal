<?php
/**
 * Apply by email content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<p><?php printf( wp_kses_post( __( 'To apply for this job <strong>email your details to</strong> <a class="job_application_email" href="mailto:%1$s%2$s">%1$s</a>', 'easy-job-portal' ) ), esc_html( $apply->email ), '?subject=' . rawurlencode( $apply->subject ) ); ?></p>

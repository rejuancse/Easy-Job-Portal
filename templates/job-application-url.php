<?php
/**
 * Apply using link to website.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<p><?php esc_html_e( 'To apply for this job please visit', 'easy-job-portal' ); ?> <a href="<?php echo esc_url( $apply->url ); ?>" rel="nofollow"><?php echo esc_html( wp_parse_url( $apply->url, PHP_URL_HOST ) ); ?></a>.</p>

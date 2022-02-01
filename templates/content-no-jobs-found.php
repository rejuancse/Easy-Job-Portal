<?php
/**
 * Notice when no jobs were found in `[jobs]` shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php if ( defined( 'DOING_AJAX' ) ) : ?>
	<li class="no_job_listings_found"><?php esc_html_e( 'There are no listings matching your search.', 'easy-job-portal' ); ?></li>
<?php else : ?>
	<p class="no_job_listings_found"><?php esc_html_e( 'There are currently no vacancies.', 'easy-job-portal' ); ?></p>
<?php endif; ?>

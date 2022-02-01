<?php
/**
 * Filters in `[jobs]` shortcode.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

wp_enqueue_script( 'easy-job-portal-ajax-filters' );

do_action( 'easy_job_portal_job_filters_before', $atts );
?>

<form class="job_filters">
	<?php do_action( 'easy_job_portal_job_filters_start', $atts ); ?>

	<div class="search_jobs">
		<?php do_action( 'easy_job_portal_job_filters_search_jobs_start', $atts ); ?>

		<div class="search_keywords">
			<label for="search_keywords"><?php esc_html_e( 'Keywords', 'easy-job-portal' ); ?></label>
			<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e( 'Keywords', 'easy-job-portal' ); ?>" value="<?php echo esc_attr( $keywords ); ?>" />
		</div>

		<div class="search_location">
			<label for="search_location"><?php esc_html_e( 'Location', 'easy-job-portal' ); ?></label>
			<input type="text" name="search_location" id="search_location" placeholder="<?php esc_attr_e( 'Location', 'easy-job-portal' ); ?>" value="<?php echo esc_attr( $location ); ?>" />
		</div>

		<?php if ( $categories ) : ?>
			<?php foreach ( $categories as $category ) : ?>
				<input type="hidden" name="search_categories[]" value="<?php echo esc_attr( sanitize_title( $category ) ); ?>" />
			<?php endforeach; ?>
		<?php elseif ( $show_categories && ! is_tax( 'job_listing_category' ) && get_terms( [ 'taxonomy' => 'job_listing_category' ] ) ) : ?>
			<div class="search_categories">
				<label for="search_categories"><?php esc_html_e( 'Category', 'easy-job-portal' ); ?></label>
				<?php if ( $show_category_multiselect ) : ?>
					<?php easy_job_portal_dropdown_categories( [ 'taxonomy' => 'job_listing_category', 'hierarchical' => 1, 'name' => 'search_categories', 'orderby' => 'name', 'selected' => $selected_category, 'hide_empty' => true ] ); ?>
				<?php else : ?>
					<?php easy_job_portal_dropdown_categories( [ 'taxonomy' => 'job_listing_category', 'hierarchical' => 1, 'show_option_all' => __( 'Any category', 'easy-job-portal' ), 'name' => 'search_categories', 'orderby' => 'name', 'selected' => $selected_category, 'multiple' => false, 'hide_empty' => true ] ); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php
		/**
		 * Show the submit button on the job filters form.
		 *
		 * @since 1.33.0
		 *
		 * @param bool $show_submit_button Whether to show the button. Defaults to true.
		 * @return bool
		 */
		if ( apply_filters( 'easy_job_portal_job_filters_show_submit_button', true ) ) :
		?>
			<div class="search_submit">
				<input type="submit" value="<?php esc_attr_e( 'Search Jobs', 'easy-job-portal' ); ?>">
			</div>
			<div style="clear: both"></div>
		<?php endif; ?>

		<?php do_action( 'easy_job_portal_job_filters_search_jobs_end', $atts ); ?>
	</div>

	<?php do_action( 'easy_job_portal_job_filters_end', $atts ); ?>
</form>

<?php do_action( 'easy_job_portal_job_filters_after', $atts ); ?>

<noscript><?php esc_html_e( 'Your browser does not support JavaScript, or it is disabled. JavaScript must be enabled in order to view listings.', 'easy-job-portal' ); ?></noscript>

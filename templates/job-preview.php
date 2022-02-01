<?php
/**
* Job listing preview when submitting job listings.
*
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
} ?>

<form method="post" id="job_preview" action="<?php echo esc_url( $form->get_action() ); ?>">
	<?php
	/**
	 * Fires at the top of the preview job form.
	 *
	 * @since 1.32.2
	 */
	do_action( 'preview_job_form_start' );
	?>
	<div class="job_listing_preview_title">
		<input type="submit" name="continue" id="job_preview_submit_button" class="button easy-job-portal-button-submit-listing" value="<?php echo esc_attr( apply_filters( 'submit_job_step_preview_submit_text', __( 'Submit Listing', 'easy-job-portal' ) ) ); ?>" />
		<input type="submit" name="edit_job" class="button easy-job-portal-button-edit-listing" value="<?php esc_attr_e( 'Edit listing', 'easy-job-portal' ); ?>" />
		<h2><?php esc_html_e( 'Preview', 'easy-job-portal' ); ?></h2>
	</div>
	<div class="job_listing_preview single_job_listing">
		<h1 class="job-preview"><?php wpjm_the_job_title(); ?></h1>

		<?php get_easy_job_portal_template_part( 'content-single', 'job_listing' ); ?>

		<input type="hidden" name="job_id" value="<?php echo esc_attr( $form->get_job_id() ); ?>" />
		<input type="hidden" name="step" value="<?php echo esc_attr( $form->get_step() ); ?>" />
		<input type="hidden" name="easy_job_portal_form" value="<?php echo esc_attr( $form->get_form_name() ); ?>" />
	</div>
	<?php
	/**
	 * Fires at the bottom of the preview job form.
	 *
	 * @since 1.32.2
	 */
	do_action( 'preview_job_form_end' );
	?>
</form>

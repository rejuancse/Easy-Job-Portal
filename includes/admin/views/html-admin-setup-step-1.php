<?php
/**
 * File containing the view for step 1 of the setup wizard.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p class="thanks-for-install">
	<?php echo wp_kses_post( __( 'Thanks for installing <em>EASY JOB PORTAL</em>!', 'easy-job-portal' ) ); ?>
	<br>
	<?php echo wp_kses_post( __( 'Let\'s get your site ready to accept job listings. The process of creating pages for job submissions, management, and listings.', 'easy-job-portal' ) ); ?>		
</p>

<form method="post" action="<?php echo esc_url( add_query_arg( 'step', 2 ) ); ?>">
	<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'enable-usage-tracking' ) ); ?>" />

	<?php $this->maybe_output_opt_in_checkbox(); ?>

	<p class="submit">
		<input type="submit" value="<?php esc_html_e( 'Start setup', 'easy-job-portal' ); ?>" class="button button-primary" />
		<a href="<?php echo esc_url( add_query_arg( 'skip-easy-job-portal-setup', 1, admin_url( 'index.php?page=easy-job-portal-setup&step=3' ) ) ); ?>" class="button"><?php esc_html_e( 'Skip setup. I will set up the plugin manually.', 'easy-job-portal' ); ?></a>
	</p>
</form>

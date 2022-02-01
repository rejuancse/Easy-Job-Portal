<?php
/**
 * File containing the view for step 2 of the setup wizard.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h3><?php esc_html_e( 'Page Setup', 'easy-job-portal' ); ?></h3>

<p><?php esc_html_e( 'Easy Job Portal, employers and applicants can post, manage, and browse job listings right on your website.', 'easy-job-portal' ); ?></p>


<form action="<?php echo esc_url( add_query_arg( 'step', 3 ) ); ?>" method="post">
	<?php wp_nonce_field( 'step_3', 'setup_wizard' ); ?>
	<table class="easy-job-portal-shortcodes widefat">
		<thead>
		<tr>
			<th>&nbsp;</th>
			<th><?php esc_html_e( 'Page Title', 'easy-job-portal' ); ?></th>
			<th><?php esc_html_e( 'Page Description', 'easy-job-portal' ); ?></th>
			<th><?php esc_html_e( 'Content Shortcode', 'easy-job-portal' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td><input type="checkbox" checked="checked" name="easy-job-portal-create-page[submit_job_form]" /></td>
			<td><input type="text" value="<?php echo esc_attr( _x( 'Post a Job', 'Default page title (wizard)', 'easy-job-portal' ) ); ?>" name="easy-job-portal-page-title[submit_job_form]" /></td>
			<td>
				<p><?php esc_html_e( 'Creates a page that allows employers to post new jobs directly from a page on your website.', 'easy-job-portal' ); ?></p>
			</td>
			<td><code>[submit_job_form]</code></td>
		</tr>
		<tr>
			<td><input type="checkbox" checked="checked" name="easy-job-portal-create-page[job_dashboard]" /></td>
			<td><input type="text" value="<?php echo esc_attr( _x( 'Job Dashboard', 'Default page title (wizard)', 'easy-job-portal' ) ); ?>" name="easy-job-portal-page-title[job_dashboard]" /></td>
			<td>
				<p><?php esc_html_e( 'Creates a page that allows employers to manage their job listings directly from a page on your website.', 'easy-job-portal' ); ?></p>
			</td>
			<td><code>[job_dashboard]</code></td>
		</tr>
		<tr>
			<td><input type="checkbox" checked="checked" name="easy-job-portal-create-page[jobs]" /></td>
			<td><input type="text" value="<?php echo esc_attr( _x( 'Jobs', 'Default page title (wizard)', 'easy-job-portal' ) ); ?>" name="easy-job-portal-page-title[jobs]" /></td>
			<td><?php esc_html_e( 'Creates a page where visitors can browse, search, and filter job listings.', 'easy-job-portal' ); ?></td>
			<td><code>[jobs]</code></td>
		</tr>


		</tbody>
		<tfoot>
		<tr>
			<th colspan="4">
				<input type="submit" class="button button-primary" value="Create selected pages" />
				<a href="<?php echo esc_url( add_query_arg( 'step', 3 ) ); ?>" class="button"><?php esc_html_e( 'Skip this step', 'easy-job-portal' ); ?></a>
			</th>
		</tr>
		</tfoot>
	</table>
</form>

<?php
/**
 * File containing the view asking users to opt-in to usage tracking.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p>
	<label>
		<input
			type="checkbox"
			name="easy_job_portal_usage_tracking_enabled"
			value="1" />
		<?php
		echo wp_kses(
			$this->opt_in_text(),
			$usage_tracking->opt_in_dialog_text_allowed_html()
		);
		?>
	</label>
</p>

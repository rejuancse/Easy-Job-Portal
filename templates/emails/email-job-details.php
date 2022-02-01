<?php
/**
 * Email content for showing job details.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$text_align = is_rtl() ? 'right' : 'left';

if ( ! empty( $fields ) ) : ?>
	<div class="easy-job-portal-email-job-details-container email-container">
		<table border="0" cellpadding="10" cellspacing="0" width="100%" class="easy-job-portal-email-job-details details">
			<?php foreach ( $fields as $field ) : ?>
			<tr>
				<td class="detail-label" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
					<?php echo wp_kses_post( $field['label'] ); ?>
				</td>
				<td class="detail-value" style="text-align:<?php echo esc_attr( $text_align ); ?>;">
					<?php
					if ( ! empty( $field['url'] ) ) {
						echo sprintf( '<a href="%s">%s</a>', esc_url( $field['url'] ), wp_kses_post( $field['value'] ) );
					} else {
						echo wp_kses_post( $field['value'] );
					}
					?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
<?php endif; ?>

<?php
/**
 * Shows the `file` form field on job listing forms.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$classes            = [ 'input-text' ];
$allowed_mime_types = array_keys( ! empty( $field['allowed_mime_types'] ) ? $field['allowed_mime_types'] : get_allowed_mime_types() );
$field_name         = isset( $field['name'] ) ? $field['name'] : $key;
$field_name         .= ! empty( $field['multiple'] ) ? '[]' : '';
$file_limit         = false;

if ( ! empty( $field['multiple'] ) && ! empty( $field['file_limit'] ) ) {
	$file_limit = $field['file_limit'];
}

if ( ! empty( $field['ajax'] ) && easy_job_portal_user_can_upload_file_via_ajax() ) {
	wp_enqueue_script( 'easy-job-portal-ajax-file-upload' );
	$classes[] = 'easy-job-portal-file-upload';
}
?>
<div class="easy-job-portal-uploaded-files">
	<?php if ( ! empty( $field['value'] ) ) : ?>
		<?php if ( is_array( $field['value'] ) ) : ?>
			<?php foreach ( $field['value'] as $value ) : ?>
				<?php get_easy_job_portal_template( 'form-fields/uploaded-file-html.php', [ 'key' => $key, 'name' => 'current_' . $field_name, 'value' => $value, 'field' => $field ] ); ?>
			<?php endforeach; ?>
		<?php elseif ( $value = $field['value'] ) : ?>
			<?php get_easy_job_portal_template( 'form-fields/uploaded-file-html.php', [ 'key' => $key, 'name' => 'current_' . $field_name, 'value' => $value, 'field' => $field ] ); ?>
		<?php endif; ?>
	<?php endif; ?>
</div>

<input
	type="file"
	class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
	data-file_types="<?php echo esc_attr( implode( '|', $allowed_mime_types ) ); ?>"
	<?php if ( ! empty( $field['multiple'] ) ) echo 'multiple'; ?>
	<?php if ( $file_limit ) echo ' data-file_limit="' . absint( $file_limit ) . '"';?>
	<?php if ( ! empty( $field['file_limit_message'] ) ) echo ' data-file_limit_message="' . esc_attr( $field['file_limit_message'] ) . '"';?>
	name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?><?php if ( ! empty( $field['multiple'] ) ) echo '[]'; ?>"
	id="<?php echo esc_attr( $key ); ?>"
	placeholder="<?php echo empty( $field['placeholder'] ) ? '' : esc_attr( $field['placeholder'] ); ?>"
/>
<small class="description">
	<?php if ( ! empty( $field['description'] ) ) : ?>
		<?php echo wp_kses_post( $field['description'] ); ?>
	<?php else : ?>
		<?php printf( esc_html__( 'Maximum file size: %s.', 'easy-job-portal' ), size_format( wp_max_upload_size() ) ); ?>
	<?php endif; ?>
</small>

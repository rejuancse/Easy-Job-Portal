<?php
/**
 * Shows info for an uploaded file on job listing forms.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="easy-job-portal-uploaded-file">
	<?php
	if ( is_numeric( $value ) ) {
		$image_src = wp_get_attachment_image_src( absint( $value ) );
		$image_src = $image_src ? $image_src[0] : '';
	} else {
		$image_src = $value;
	}
	$extension = ! empty( $extension ) ? $extension : substr( strrchr( $image_src, '.' ), 1 );
	if ( 'image' === wp_ext2type( $extension ) ) : ?>
		<span class="easy-job-portal-uploaded-file-preview"><img src="<?php echo esc_url( $image_src ); ?>" /> <a class="easy-job-portal-remove-uploaded-file" href="#">[<?php _e( 'remove', 'easy-job-portal' ); ?>]</a></span>
	<?php else : ?>
		<span class="easy-job-portal-uploaded-file-name"><code><?php echo esc_html( basename( $image_src ) ); ?></code> <a class="easy-job-portal-remove-uploaded-file" href="#">[<?php _e( 'remove', 'easy-job-portal' ); ?>]</a></span>
	<?php endif; ?>

	<input type="hidden" class="input-text" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
</div>

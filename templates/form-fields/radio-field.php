<?php
/**
 * Shows the `radio` form field on job listing forms.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$field['default'] = empty( $field['default'] ) ? current( array_keys( $field['options'] ) ) : $field['default'];
$default          = ! empty( $field['value'] ) ? $field['value'] : $field['default'];

foreach ( $field['options'] as $option_key => $value ) : ?>

	<label><input type="radio" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" value="<?php echo esc_attr( $option_key ); ?>" <?php checked( $default, $option_key ); ?> /> <?php echo esc_html( $value ); ?></label><br/>

<?php endforeach; ?>
<?php if ( ! empty( $field['description'] ) ) : ?><small class="description"><?php echo wp_kses_post( $field['description'] ); ?></small><?php endif; ?>

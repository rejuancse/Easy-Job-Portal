<?php
/**
 * Email content for showing job details.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo "\n\n";

if ( ! empty( $fields ) ) {
	foreach ( $fields as $field ) {
		echo esc_html( wp_strip_all_tags( $field[ 'label' ] )  .': '. wp_strip_all_tags( $field[ 'value' ] ) );
		if ( ! empty( $field['url'] ) ) {
			echo ' (' . esc_url( $field['url'] ) . ')';
		}
		echo "\n";
	}
}

<?php
/**
 * Deprecated functions. Do not use these.
 *
 * @package easy-job-portal
 */

if ( ! function_exists( 'order_featured_job_listing' ) ) :
	function order_featured_job_listing( $args ) {
		global $wpdb;
		$args['orderby'] = "$wpdb->posts.menu_order ASC, $wpdb->posts.post_date DESC";
		return $args;
	}
endif;



if ( ! function_exists( 'the_job_type' ) ) :
	function the_job_type( $post = null ) {
		_deprecated_function( __FUNCTION__, '1.27.0', 'wpjm_the_job_types' );

		if ( ! get_option( 'easy_job_portal_enable_types' ) ) {
			return '';
		}
		$job_type = get_the_job_type( $post );
		if ( $job_type ) {
			echo esc_html( $job_type->name );
		}
	}
endif;

if ( ! function_exists( 'get_the_job_type' ) ) :
	function get_the_job_type( $post = null ) {
		_deprecated_function( __FUNCTION__, '1.27.0', 'wpjm_get_the_job_types' );

		$post = get_post( $post );
		if ( 'job_listing' !== $post->post_type ) {
			return;
		}

		$types = wp_get_post_terms( $post->ID, 'job_listing_type' );

		if ( $types ) {
			$type = current( $types );
		} else {
			$type = false;
		}

		return apply_filters( 'the_job_type', $type, $post );
	}
endif;

if ( ! function_exists( 'wpjm_get_permalink_structure' ) ) :
	function wpjm_get_permalink_structure() {
		return Easy_Job_Portal_Post_Types::get_permalink_structure();
	}
endif;


if ( ! function_exists( 'easy_job_portal_add_post_types' ) ) :
	function easy_job_portal_add_post_types( $types ) {
		_deprecated_function( __FUNCTION__, '1.33.0', 'Easy_Job_Portal_Post_Types::delete_user_add_job_listings_post_type' );

		return Easy_Job_Portal_Post_Types::instance()->delete_user_add_job_listings_post_type( $types );
	}
endif;

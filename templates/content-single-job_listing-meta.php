<?php
/**
 * Single view job meta box.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post;

do_action( 'single_job_listing_meta_before' ); ?>

<ul class="job-listing-meta meta">
	<?php do_action( 'single_job_listing_meta_start' ); ?>

	<?php if ( get_option( 'easy_job_portal_enable_types' ) ) { ?>
		<?php $types = wpjm_get_the_job_types(); ?>
		<?php if ( ! empty( $types ) ) : foreach ( $types as $type ) : ?>
			<li class="job-type <?php echo esc_attr( sanitize_title( $type->slug ) ); ?>"><?php echo esc_html( $type->name ); ?></li>
		<?php endforeach; endif; ?>
	<?php } ?>

	<?php 
		$post          = get_post();
		$post_id       = $post->ID;
		
		$work_experience         = get_post_meta( $post_id, 'wljp_job_work_experience', true );
		$minimum_work_experience = isset( $work_experience['minimum'] ) ? esc_attr( $work_experience['minimum'] ) : '';
		$maximum_work_experience = isset( $work_experience['maximum'] ) ? esc_attr( $work_experience['maximum'] ) : '';

		$salary  = get_post_meta( $post_id, 'wljp_job_salary', true );
		$type    = isset( $salary['type'] ) ? esc_attr( $salary['type'] ) : '';
		if ( $is_range = ( $type == 'range' ) ) {
			$minimum_salary = isset( $salary['minimum'] ) ? esc_attr( $salary['minimum'] ) : '';
			$maximum_salary = isset( $salary['maximum'] ) ? esc_attr( $salary['maximum'] ) : '';
			$salary  = "{$minimum_salary} - $maximum_salary";
		} elseif ( $type == 'negotiable' ) {
			$salary = esc_html__( 'Negotiable', EJP_DOMAIN );
		} elseif ( $type == 'fixed' ) {
			$salary = esc_html__( 'Fixed', EJP_DOMAIN );
		} else {
			$salary = '';
		}
	?>


	<?php if ( $minimum_work_experience && $maximum_work_experience ) { ?>
    	<li class="wljp-experience">
			<i class="fa fa-briefcase"></i>&nbsp; <?php echo "{$minimum_work_experience} - {$maximum_work_experience} " . esc_html__( 'Yr', EJP_DOMAIN ); ?>
		</li>
    <?php } elseif( $minimum_work_experience ) { ?>
    	<li class="wljp-experience">
			<i class="fa fa-briefcase"></i>&nbsp; <?php echo "{$minimum_work_experience} " . esc_html__( 'Yr', EJP_DOMAIN ); ?>
		</li>
	<?php } elseif ( $maximum_work_experience ) { ?>
    	<li class="wljp-experience">
			<i class="fa fa-briefcase"></i>&nbsp; <?php echo "{$maximum_work_experience} " . esc_html__( 'Yr', EJP_DOMAIN ); ?>
		</li>
	<?php }
	
	if ( $salary ) { ?>
		<li class="sallery">
			<i class="fa fa-credit-card"></i>&nbsp; <?php echo esc_html($salary); ?>
		</li>
    <?php } ?>
	
	<li class="location"><?php the_job_location(); ?></li>

	<li class="date-posted"><?php the_job_publish_date(); ?></li>

	<?php if ( is_position_filled() ) : ?>
		<li class="position-filled"><?php _e( 'This position has been filled', 'easy-job-portal' ); ?></li>
	<?php elseif ( ! employees_can_apply() && 'preview' !== $post->post_status ) : ?>
		<li class="listing-expired"><?php _e( 'Applications have closed', 'easy-job-portal' ); ?></li>
	<?php endif; ?>

	<?php do_action( 'single_job_listing_meta_end' ); ?>
</ul>

<?php do_action( 'single_job_listing_meta_after' ); ?>

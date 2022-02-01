<?php
defined( 'ABSPATH' ) || die();
get_header();

global $wp;
$job_portal_page_url = WL_JP_Helper::general_job_portal_page_url();
$account_page_url    = WL_JP_Helper::general_account_page_url();
$is_user_logged_in   = is_user_logged_in();
$employee           = false;
if ( $is_user_logged_in ) {
	$employee = WL_JP_Helper::user_has_cv( get_current_user_id() );
}
?>

<div class="wrap wljp">
	<div class="container mt-4 mb-3">
		<section class="wljp-job">
		    <?php
	    	while ( have_posts() ) : the_post();
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
					$salary = esc_html__( 'Negotiable', DJP_DOMAIN );
				} elseif ( $type == 'fixed' ) {
					$salary = esc_html__( 'Fixed', DJP_DOMAIN );
				} else {
					$salary = '';
				}
		    ?>

			<article <?php post_class(); ?>>
			    <header class="wljp-job-heading">
			    	<div class="row">
				    	<div class="col-sm-9">
				        	<h1 class="page-header"><?php the_title(); ?></h1>
				        </div>
				    	<div class="col-sm-3">
							<div class="col-sm-12 text-right wljp-job-portal-navigation wljp-navigation">
								<a href="<?php echo esc_url($job_portal_page_url); ?>" class="wljp-navigation-link pr-3 mb-3 border-bottom">&#8594; <?php esc_html_e( 'Back to Job Portal', DJP_DOMAIN ); ?></a>
							</div>
							<?php 
								if ( $is_user_logged_in ) : ?>
								<?php if ( $employee ) : ?>
									<div class="col-sm-12 text-right wljp-cv-navigation wljp-navigation">
										<a href="<?php echo esc_url($account_page_url); ?>" class="wljp-navigation-link pr-3 mb-3 border-bottom"><?php esc_html_e( 'Your CV', DJP_DOMAIN ); ?></a>
									</div>
									<?php else: ?>
									<div class="col-sm-12 text-right wljp-cv-navigation wljp-navigation">
										<a href="<?php echo esc_url($account_page_url); ?>" class="wljp-navigation-link pr-3 mb-3 border-bottom"><?php esc_html_e( 'Register CV', DJP_DOMAIN ); ?></a>
									</div>
								<?php endif; ?>
							<div class="col-sm-12 text-right wljp-logout-navigation wljp-navigation">
								<a href="<?php echo wp_logout_url( $wp->request ); ?>" class="wljp-navigation-link pr-3 pb-3"><?php esc_html_e( 'Logout', DJP_DOMAIN ); ?></a>
							</div>
							<?php else : ?>
							<div class="col-sm-12 text-right wljp-login-signup-navigation wljp-navigation">
								<a href="<?php echo esc_url($account_page_url); ?>" class="wljp-navigation-link pr-3 mb-3 border-bottom"><?php esc_html_e( 'Login / Signup', DJP_DOMAIN ); ?></a>
							</div>
							<?php endif; ?>
			        	</div>
			        </div>
			        
			    </header>
			    <div id="wljp-job-content-<?php the_ID(); ?>" class="mb-4">
			        <?php the_content(); ?>
			    </div>
		    </article>
		    <?php endwhile;
		    wp_reset_postdata();
		    ?>
	    </section>
    </div>
</div>
<?php get_footer(); ?>
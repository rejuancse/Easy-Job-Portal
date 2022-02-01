<?php
defined( 'ABSPATH' ) || die();

global $wpdb;

$count_jobs = wp_count_posts( 'job_listing' ); 
$count_jobs = $count_jobs ? $count_jobs->publish : 0;

$job_types       = get_terms( array( 'taxonomy'   => 'job_type', 'hide_empty' => false ) );
$count_job_types = is_array( $job_types ) ? count( $job_types ) : 0;

$industries       = get_terms( array( 'taxonomy'   => 'industry', 'hide_empty' => false ) );
$count_industries = is_array( $industries ) ? count( $industries ) : 0;

$departments       = get_terms( array( 'taxonomy'   => 'department', 'hide_empty' => false ) );
$count_departments = is_array( $departments ) ? count( $departments ) : 0;

$skills       = get_terms( array( 'taxonomy'   => 'skill', 'hide_empty' => false ) );
$count_skills = is_array( $skills ) ? count( $skills ) : 0;

$job_locations       = get_terms( array( 'taxonomy'   => 'job_location', 'hide_empty' => false ) );
$count_job_locations = is_array( $job_locations ) ? count( $job_locations ) : 0;

$count_employees = wp_count_posts( 'employee' );
$count_employees = $count_employees ? $count_employees->publish : 0;

$count_applications = $wpdb->get_var( "SELECT COUNT(*) as count FROM {$wpdb->prefix}wljp_employee_job" );

$recent_jobs = wp_get_recent_posts( array(
	'post_type'   => 'job_listing',
	'numberposts' => 10,
	'post_status' => 'publish'
));

$recent_employees = wp_get_recent_posts( array(
	'post_type'   => 'employee',
	'numberposts' => 10,
	'post_status' => 'publish'
));

$recent_applications = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wljp_employee_job ORDER BY created_at DESC LIMIT 10" );
?>
<div class="wljp">
	<div class="container-fluid">
		<div class="row">
			<div class="col">
				<h1 class="display-4 text-center bg-primary text-white pt-1 pb-3 mt-2"><span class="border-bottom"><i class="fa fa-tachometer"></i> <?php esc_html_e( 'Job Portal Dashboard', DJP_DOMAIN ); ?></span></h1>
				<div class="mt-3 alert alert-secondary text-center" role="alert">
					<?php esc_html_e( "Here, you can view job portal stats.", DJP_DOMAIN ); ?>
				</div>
			</div>
		</div>

		<div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-4">
            	<div class="wljp-stats-item">
            		<div class="row d-flex h-100 ">
            			<div class="col-4 justify-content-center align-self-center"><div class="wljp-stats-icon"><i class="fa fa-bullhorn"></i></div></div>
            			<div class="col-8 justify-content-center align-self-center text-right">
            				<div class="wljp-stats-count"><?php echo esc_html($count_jobs); ?></div>
            				<div class="wljp-stats-title"><a href="<?php echo admin_url( 'edit.php?post_type=job_listing' ); ?>" class="wljp-stats-item-link"><?php esc_html_e( 'Jobs Published', DJP_DOMAIN ); ?></a></div>
            			</div>
            		</div>
            	</div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-4">
            	<div class="wljp-stats-item">
            		<div class="row d-flex h-100">
            			<div class="col-4 justify-content-center align-self-center"><div class="wljp-stats-icon"><i class="fa fa-industry"></i></div></div>
            			<div class="col-8 justify-content-center align-self-center text-right">
            				<div class="wljp-stats-count"><?php echo esc_html($count_industries); ?></div>
            				<div class="wljp-stats-title"><a href="<?php echo admin_url( 'edit-tags.php?taxonomy=industry&post_type=job_listing' ); ?>" class="wljp-stats-item-link"><?php esc_html_e( 'Industries', DJP_DOMAIN ); ?></a></div>
            			</div>
            		</div>
            	</div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-4">
            	<div class="wljp-stats-item">
            		<div class="row d-flex h-100">
            			<div class="col-4 justify-content-center align-self-center"><div class="wljp-stats-icon"><i class="fa fa-building"></i></div></div>
            			<div class="col-8 justify-content-center align-self-center text-right">
            				<div class="wljp-stats-count"><?php echo esc_html($count_departments); ?></div>
            				<div class="wljp-stats-title"><a href="<?php echo admin_url( 'edit-tags.php?taxonomy=department&post_type=job_listing' ); ?>" class="wljp-stats-item-link"><?php esc_html_e( 'Departments', DJP_DOMAIN ); ?></a></div>
            			</div>
            		</div>
            	</div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-4">
            	<div class="wljp-stats-item">
            		<div class="row d-flex h-100">
            			<div class="col-4 justify-content-center align-self-center"><div class="wljp-stats-icon"><i class="fa fa-asterisk"></i></div></div>
            			<div class="col-8 justify-content-center align-self-center text-right">
            				<div class="wljp-stats-count"><?php echo esc_html($count_skills); ?></div>
            				<div class="wljp-stats-title"><a href="<?php echo admin_url( 'edit-tags.php?taxonomy=skill&post_type=job_listing' ); ?>" class="wljp-stats-item-link"><?php esc_html_e( 'Skills', DJP_DOMAIN ); ?></a></div>
            			</div>
            		</div>
            	</div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-4">
            	<div class="wljp-stats-item">
            		<div class="row d-flex h-100">
            			<div class="col-4 justify-content-center align-self-center"><div class="wljp-stats-icon"><i class="fa fa-map"></i></div></div>
            			<div class="col-8 justify-content-center align-self-center text-right">
            				<div class="wljp-stats-count"><?php echo esc_html($count_job_locations); ?></div>
            				<div class="wljp-stats-title"><a href="<?php echo admin_url( 'edit-tags.php?taxonomy=job_location&post_type=job_listing' ); ?>" class="wljp-stats-item-link"><?php esc_html_e( 'Job Locations', DJP_DOMAIN ); ?></a></div>
            			</div>
            		</div>
            	</div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-4">
            	<div class="wljp-stats-item">
            		<div class="row d-flex h-100">
            			<div class="col-4 justify-content-center align-self-center"><div class="wljp-stats-icon"><i class="fa fa-users"></i></div></div>
            			<div class="col-8 justify-content-center align-self-center text-right">
            				<div class="wljp-stats-count"><?php echo esc_html( $count_employees ); ?></div>
            				<div class="wljp-stats-title"><a href="<?php echo admin_url( 'edit.php?post_type=employee' ); ?>" class="wljp-stats-item-link"><?php esc_html_e( 'Employees', DJP_DOMAIN ); ?></a></div>
            			</div>
            		</div>
            	</div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 mb-4">
            	<div class="wljp-stats-item">
            		<div class="row d-flex h-100">
            			<div class="col-4 justify-content-center align-self-center"><div class="wljp-stats-icon"><i class="fa fa-envelope-open"></i></div></div>
            			<div class="col-8 justify-content-center align-self-center text-right">
            				<div class="wljp-stats-count"><?php echo esc_html( $count_applications ); ?></div>
            				<div class="wljp-stats-title"><a href="<?php menu_page_url( 'job_applications', true ); ?>" class="wljp-stats-item-link"><?php esc_html_e( 'Job Applications', DJP_DOMAIN ); ?></a></div>
            			</div>
            		</div>
            	</div>
            </div>
		</div>

		<div class="row">
			<div class="col">
				<div class="text-center wljp-recent-activities-heading"><?php esc_html_e( "Recent Activities", DJP_DOMAIN ); ?></div>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12 mb-4">
						<?php if ( count( $recent_jobs ) ) { ?>
						<div class="wljp-recent-list-title"><?php esc_html_e( "Last 10 Jobs Published", DJP_DOMAIN ); ?><span class="float-right"><i class="fa fa-bullhorn"></i></span></div>
						<ul class="list-group wljp-recent-list">
							<?php foreach ( $recent_jobs as $recent_job ) {
									$job_url = get_the_permalink( $recent_job['ID'] ); ?>
							<li class="list-group-item">
								<span class="wljp-recent-item-left"><a target="_blank" href="<?php echo esc_url( $job_url ); ?>"><?php echo esc_html( $recent_job['post_title'] ); ?></a></span>
								<span class="wljp-recent-item-right float-right"><?php echo date_format( date_create( $recent_job['post_date'] ), 'd-m-Y g:i A' ); ?></span>
							</li>
							<?php } ?>
						</ul>
						<?php } else { ?>
							<div class="alert alert-secondary"><?php esc_html_e( "There is no recent job.", DJP_DOMAIN ); ?></div>
						<?php } ?>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 mb-4">
						<?php if ( count( $recent_employees ) ) { ?>
						<div class="wljp-recent-list-title"><?php esc_html_e( "Last 10 Employee Registrations", DJP_DOMAIN ); ?><span class="float-right"><i class="fa fa-users"></i></span></div>
						<ul class="list-group wljp-recent-list">
							<?php foreach ( $recent_employees as $recent_employee ) {
									$employee_url = get_edit_post_link( $recent_employee['ID'] ); ?>
							<li class="list-group-item">
								<span class="wljp-recent-item-left"><a target="_blank" href="<?php echo esc_url( $employee_url ); ?>"><?php echo esc_html($recent_employee['post_title']); ?></a></span>
								<span class="wljp-recent-item-right float-right"><?php echo date_format( date_create( $recent_employee['post_date'] ), 'd-m-Y g:i A' ); ?></span>
							</li>
							<?php } ?>
						</ul>
						<?php } else { ?>
							<div class="alert alert-secondary"><?php esc_html_e( "There is no recent employee registration.", DJP_DOMAIN ); ?></div>
						<?php } ?>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 mb-4">
						<?php if ( count( $recent_applications ) ) { ?>
						<div class="wljp-recent-list-title"><?php esc_html_e( "Last 10 Job Applications", DJP_DOMAIN ); ?><span class="float-right"><i class="fa fa-envelope-open"></i></span></div>
						<ul class="list-group wljp-recent-list">
							<?php foreach ( $recent_applications as $application ) {
									$employee_id = $application->employee_id;
									$job_id       = $application->job_id;
									$date         = date_i18n( 'd-m-Y g:i A', strtotime( $application->created_at ) );

									/* Employee link */
									$employee_title = get_the_title( $employee_id );
									$employee_url   = get_edit_post_link( $employee_id );
									$employee_link  = '<a target="_blank" href="' . $employee_url . '">' . $employee_title . '</a>';

									/* Job link */
									$job_title     = get_the_title( $job_id );
									$job_permalink = get_the_permalink( $job_id );
									$job_link      = '<a target="_blank" href="' . $job_permalink . '">' . $job_title . '</a>';
							?>
							<li class="list-group-item">
								<span class="wljp-recent-item-left"><?php echo wp_kses_post( $employee_link ); ?>&nbsp;<?php esc_html_e( "applied to", DJP_DOMAIN ); ?>&nbsp;<?php echo wp_kses_post($job_link); ?></span>
								<span class="wljp-recent-item-right float-right"><?php echo esc_html($date); ?></span>
							</li>
							<?php } ?>
						</ul>
						<?php } else { ?>
							<div class="alert alert-secondary"><?php esc_html_e( "There is no recent job application.", DJP_DOMAIN ); ?></div>
						<?php } ?>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 mb-4">
						<div class="wljp-recent-list-title bg-primary text-white">
							<?php esc_html_e( "Jobs Portal Pro Features", DJP_DOMAIN ); ?>
							<span class="float-right"><i class="fa fa-star"></i></span>
						</div>
						<ul class="list-group list-group-flush wljp-recent-list">
							<li class="list-group-item">
								<div class="alert alert-primary">
									<h6><i class="fa fa-check"></i>&nbsp;&nbsp;<?php esc_html_e( "Signup as Recruiter", DJP_DOMAIN ); ?></h6>
								</div>
								<div class="alert alert-warning">
									<h6><i class="fa fa-check"></i>&nbsp;&nbsp;<?php esc_html_e( "Separate Recruiter Dashboard", DJP_DOMAIN ); ?></h6>
								</div>
								<div class="alert alert-info">
									<h6><i class="fa fa-check"></i>&nbsp;&nbsp;<?php esc_html_e( "Unlimited Recruiters", DJP_DOMAIN ); ?></h6>
								</div>
								<div class="alert alert-success">
									<h6><i class="fa fa-check"></i>&nbsp;&nbsp;<?php esc_html_e( "Recruiter's Account Approval", DJP_DOMAIN ); ?></h6>
								</div>
								<div class="row mt-2">
									<div class="col">
										<a href="http://demo.weblizar.com/jobs-portal-pro/" class="btn btn-block btn-info" target="_blank"><?php esc_html_e( "Try Pro", DJP_DOMAIN ); ?></a>
									</div>
									<div class="col">
										<a href="https://weblizar.com/plugins/jobs-portal-pro/" class="btn btn-block btn-success" target="_blank"><?php esc_html_e( "Buy Pro", DJP_DOMAIN ); ?></a>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
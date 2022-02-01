<?php
/**
 * In job listing creation flow, this template shows above the job creation form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php if ( is_user_logged_in() ) : ?>

	<fieldset class="fieldset-logged_in">
		<label><?php esc_html_e( 'Your account', 'easy-job-portal' ); ?></label>
		<div class="field account-sign-in">
			<?php
				$user = wp_get_current_user();
				printf( wp_kses_post( __( 'You are currently signed in as <strong>%s</strong>.', 'easy-job-portal' ) ), esc_html( $user->user_login ) );
			?>

			<a class="button" href="<?php echo esc_url( apply_filters( 'submit_job_form_logout_url', wp_logout_url( get_permalink() ) ) ); ?>"><?php esc_html_e( 'Sign out', 'easy-job-portal' ); ?></a>
		</div>
	</fieldset>

<?php else :
	$account_required            = easy_job_portal_user_requires_account();
	$registration_enabled        = easy_job_portal_enable_registration();
	$registration_fields         = wpjm_get_registration_fields();
	$use_standard_password_email = wpjm_use_standard_password_setup_email(); ?>

	<fieldset class="fieldset-login_required">
		<label><?php esc_html_e( 'Have an account?', 'easy-job-portal' ); ?></label>
		<div class="field account-sign-in">

			<div class="header-login-wrap">
			    <a class="button show" aria-haspopup="true" href="#modal-login">Sign in</a>
			</div>
			
			<?php if ( $registration_enabled ) : ?>
				<?php printf( esc_html__( 'If you don\'t have an account you can %screate one below.', 'easy-job-portal' ), $account_required ? '' : esc_html__( 'optionally', 'easy-job-portal' ) . ' ' ); ?>
			<?php elseif ( $account_required ) : ?>
				<?php echo wp_kses_post( apply_filters( 'submit_job_form_login_required_message',  __( 'You must sign in to create a new listing.', 'easy-job-portal' ) ) ); ?>
			<?php endif; ?>

		</div>
	</fieldset>

	<?php if ( ! empty( $registration_fields ) ) {
		foreach ( $registration_fields as $key => $field ) { ?>
			<fieldset class="fieldset-<?php echo esc_attr( $key ); ?>">
				<label
					for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[ 'label' ] ) . wp_kses_post( apply_filters( 'submit_job_form_required_label', $field[ 'required' ] ? '' : ' <small>' . __( '(optional)', 'easy-job-portal' ) . '</small>', $field ) ); ?></label>
				<div class="field <?php echo $field[ 'required' ] ? 'required-field draft-required' : ''; ?>">
					<?php get_easy_job_portal_template( 'form-fields/' . $field[ 'type' ] . '-field.php', [ 'key'   => $key, 'field' => $field ] ); ?>
				</div>
			</fieldset>
			<?php
		}
		do_action( 'easy_job_portal_register_form' );
	}
	?>
<?php endif; ?>

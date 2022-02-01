<?php
defined( 'ABSPATH' ) || die(); ?>

<div class="col-sm-12 col-md-12 card p-3 mt-3">
	
	<div class="wljp-account-heading mt-0 mb-0">
		<span><?php esc_html_e( 'Account Settings', DJP_DOMAIN ); ?></span>
	</div>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="wljp-account-form" class="wljp-account-form">
		<?php
			$user             = wp_get_current_user();
			$account_email    = $user->user_email;
			$account_username = $user->user_login;
		?>
        <input type="hidden" name="action" value="wljp-account">

		<div class="form-group mt-3">
			<label><?php esc_html_e( 'Username', DJP_DOMAIN ); ?></label><br>
			<span><?php echo esc_html( $account_username ); ?></span>
			<a href="javascript:void(0)" id="wljp-change-password-email-button" class="ml-2"><?php esc_html_e( 'Change Password', DJP_DOMAIN ); ?></a>
		</div>

		<div class="wljp-change-password-email">
			<div class="form-group">
				<label for="wljp-account-email"><?php esc_html_e( 'Email Address', DJP_DOMAIN ); ?></label><br>
				<input type="email" name="email" id="wljp-account-email" class="w-100 d-block col" value="<?php echo esc_attr( $account_email ); ?>">
			</div>

			<div class="form-group">
				<label for="wljp-signup-password"><?php esc_html_e( 'Password', DJP_DOMAIN ); ?></label>
				<input type="password" name="password" id="wljp-signup-password" class="w-100 d-block col">
			</div>

			<div class="form-group">
				<label for="wljp-account-confirm-password"><?php esc_html_e( 'Confirm Password', DJP_DOMAIN ); ?></label>
				<input type="password" name="confirm_password" id="wljp-account-confirm-password" class="w-100 d-block col">
			</div>

			<div class="float-right wljp-account-submit-block">
				<button type="submit" class="wljp-account-submit"><?php esc_html_e( 'Update', DJP_DOMAIN ); ?></button>
			</div>
		</div>
	</form>
</div>
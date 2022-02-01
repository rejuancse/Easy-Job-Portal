<?php
/**
* Job dashboard shortcode content if user is not logged in.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; # Exit if accessed directly.
} ?> 

<div id="easy-job-portal-job-dashboard">
	<p class="account-sign-in">
		<?php esc_html_e( 'You need to be signed in to manage your listings.', 'easy-job-portal' ); ?> 
		<a class="button show" href="#modal-login">
			<?php esc_html_e( 'Sign in', 'easy-job-portal' ); ?>
		</a>
	</p>
</div>

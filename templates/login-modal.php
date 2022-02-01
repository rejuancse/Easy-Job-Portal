<?php if ( !is_user_logged_in() ): ?>
    <!-- Login -->
    <div class="mask" role="dialog"></div>
    <div class="modal fade modal-popup-wrap" id="myModal" role="alert">
        <button class="close" role="button">&times;</button>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php _e( 'Sign In','easy-job-portal' ); ?></h4>
                    <p class="modal-text"><?php _e( 'Don’t worry, we won’t spam you <br> or sell your information.','easy-job-portal' ); ?></p>
                </div>
                <div class="modal-body">
                    <form id="login" action="login" method="post">
                        <div class="login-error alert alert-danger" role="alert"></div>
                        <input type="text"  id="usernamelogin" name="username" class="form-control" placeholder="Username">
                        <input type="password" id="passwordlogin" name="password" class="form-control" placeholder="Password">
                        <input type="checkbox" id="rememberlogin" name="remember" ><label><?php _e( 'Remember me','easy-job-portal' ); ?></label>
                        <input type="submit" class="btn btn-primary submit_button"  value="Log In" name="submit">

                        <?php wp_nonce_field( 'ajax-login-nonce', 'securitylogin' ); ?>
                    </form>
                </div>
                <div class="modal-footer clearfix d-block text-left">
                    <div class="d-inline-block">
                        <a href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php echo esc_html__( 'Forgot password?','easy-job-portal' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

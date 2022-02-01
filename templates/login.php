<?php
/* ------------------------------------------ *
 *              Login Action
 * ------------------------------------------ */
add_action( 'wp_ajax_nopriv_ajaxlogin', 'djp_ajax_login' );
function djp_ajax_login(){
    check_ajax_referer( 'ajax-login-nonce', 'security' );
    $info = array();
    $info['user_login'] = sanitize_text_field( $_POST['username'] );
    $info['user_password'] = sanitize_text_field( $_POST['password'] );
    $info['remember'] = sanitize_text_field( $_POST['remember'] );

    $user_signon = wp_signon( $info, false );

    if ( is_wp_error($user_signon) ){
        echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.','easy-job-portal')));
    } else {
        echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...','easy-job-portal')));
    }
    die();
}

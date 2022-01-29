<?php

namespace EasyJob\Portal\Admin;

/**
 * The menu handler class
 */
class Menu {

    /**
     * Initalize the class
     */
    function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    /**
     * Register admin menu
     *
     * @return void
     */  
    public function admin_menu() {
        $parent_slug = 'easy-jobportal';
        $capability = 'manage_options';
        add_menu_page( __('Easy Job Portal', 'easy-jobportal'),  __('Easy Job Portal', 'easy-jobportal'), $capability, $parent_slug, [ $this, 'addressbook_settings'], 'dashicons-welcome-learn-more' );
    }

    /**
     * Render the plugin page
     * 
     * @return void
     */
    public function addressbook_settings() {
        echo 'Hello World';
    }
    
    
}

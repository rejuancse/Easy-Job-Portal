<?php
defined( 'ABSPATH' ) || die();

require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'admin/inc/WL_JP_Job.php' );
require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'admin/inc/WL_JP_Employee.php' );
require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'admin/inc/WL_JP_Menu.php' );
require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'admin/inc/WL_JP_Setting.php' );
require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'lib/WL_JP_Helper.php' );

/* Add plugin settings link */
add_filter( "plugin_action_links_" . EASY_JOB_PORTAL_PLUGIN_BASE_NAME, array( 'WL_JP_Setting', 'add_settings_link' ) );

/* Add metaboxes */
add_action( 'add_meta_boxes', array( 'WL_JP_Job', 'add_meta_boxes' ) );
add_action( 'add_meta_boxes', array( 'WL_JP_Employee', 'add_meta_boxes' ) );

/* Enqueue scripts and styles */
add_action( 'admin_enqueue_scripts', array( 'WL_JP_Job', 'enqueue_scripts_styles' ) );
add_action( 'admin_enqueue_scripts', array( 'WL_JP_Employee', 'enqueue_scripts_styles' ) );

/* Save metaboxes */
add_action( 'save_post', array( 'WL_JP_Job', 'save_metaboxes' ), 10, 2 );
add_action( 'save_post', array( 'WL_JP_Employee', 'save_metaboxes' ), 10, 2 );

/* Delete employee's document */
add_action( 'before_delete_post', array( 'WL_JP_Employee', 'delete_document' ), 10, 2 );

/* File uploads in employee post type */
add_action( 'post_edit_form_tag', array( 'WL_JP_Employee', 'edit_form_tag' ) );

/* Change title text */
add_filter( 'enter_title_here', array( 'WL_JP_Employee', 'change_title_text' ) );

/* Set employee columns */
add_filter( 'manage_employee_posts_columns', array( 'WL_JP_Employee', 'set_columns' ) );

/* Create menu */
add_action( 'admin_menu', array( 'WL_JP_Menu', 'create_menu' ) );

/* Register settings */
add_action( 'admin_init', array( 'WL_JP_Setting', 'register_settings' ) );
?>
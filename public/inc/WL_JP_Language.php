<?php
defined( 'ABSPATH' ) || die();

class WL_JP_Language {
	public static function load_translation() {
		load_plugin_textdomain( EJP_DOMAIN, false, basename( EASY_JOB_PORTAL_PLUGIN_DIR_PATH ) . '/languages' );
	}
}
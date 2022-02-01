<?php
/**
 * Handles Job Manager's Gutenberg Blocks.
 *
 * @package easy-job-portal
 * @since 1.32.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Easy_Job_Portal_Blocks.
 */
class Easy_Job_Portal_Blocks {
	/**
	 * The static instance of the Easy_Job_Portal_Blocks
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Singleton instance getter
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new Easy_Job_Portal_Blocks();
		}

		return self::$instance;
	}

	/**
	 * Instance constructor
	 */
	private function __construct() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		add_action( 'init', [ $this, 'register_blocks' ] );
	}

	/**
	 * Register all Gutenblocks
	 */
	public function register_blocks() {
		// Add script includes for gutenblocks.
	}
}

Easy_Job_Portal_Blocks::get_instance();

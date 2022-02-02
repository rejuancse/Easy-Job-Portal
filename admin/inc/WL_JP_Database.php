<?php
defined( 'ABSPATH' ) || die();

require_once( EASY_JOB_PORTAL_PLUGIN_DIR_PATH . 'lib/WL_JP_Helper.php' );

class WL_JP_Database {
	/* On plugin activation */
	public static function activation() {
		if ( ! get_option( 'wljp_flush_rewrite_rules_flag' ) ) {
			add_option( 'wljp_flush_rewrite_rules_flag', true );
		}

		/* Create employee_job table */
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wljp_employee_job (
				ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				job_id bigint(20) UNSIGNED DEFAULT NULL,
				employee_id bigint(20) UNSIGNED DEFAULT NULL,
				created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				INDEX (job_id),
				INDEX (employee_id),
				FOREIGN KEY (job_id) REFERENCES {$wpdb->prefix}posts (ID) ON DELETE CASCADE,
				FOREIGN KEY (employee_id) REFERENCES {$wpdb->prefix}posts (ID) ON DELETE CASCADE
				) $charset_collate";
		dbDelta( $sql );

		/* Add job portal default settings */
		add_option( 'wljp_general', array(
			'jobs_per_page'               => 15,
			'admin_applications_per_page' => 15
		) );

		/* Add job portal page */
		if ( ! WL_JP_Helper::general_job_portal_page_id() ) {
			$page_content = '[jobs]';
			$job_portal_page = array(
				'comment_status' => 'closed',
				'ping_status'    =>  'closed' ,
				'post_author'    => 1,
				'post_content'   => $page_content,
				'post_date'      => date('Y-m-d H:i:s'),
				'post_status'    => 'publish' ,
				'post_title'     => esc_html__( 'Jobs', EJP_DOMAIN ),
				'post_type'      => 'page',
			);  
			$job_portal_page_id = wp_insert_post( $job_portal_page, false );

			$options = get_option( 'wljp_general' );
			$options['job_portal_page_id'] = $job_portal_page_id;
			update_option( 'wljp_general', $options );
		}

		/* Add account page */
		if ( ! WL_JP_Helper::general_account_page_id() ) {
			$page_content = '[job_portal_account]';
			$account_page = array(
				'comment_status' => 'closed',
				'ping_status'    =>  'closed' ,
				'post_author'    => 1,
				'post_content'   => $page_content,
				'post_date'      => date('Y-m-d H:i:s'),
				'post_status'    => 'publish' ,
				'post_title'     => esc_html__( 'Account', EJP_DOMAIN ),
				'post_type'      => 'page',
			);
			$account_page_id = wp_insert_post( $account_page, false );

			$options = get_option( 'wljp_general' );
			$options['account_page_id'] = $account_page_id;
			update_option( 'wljp_general', $options );
		}
	}

	/* On plugin deactivation */
	public static function deactivation() {
	}
} ?>
<?php
/**
 * File containing the class Easy_Job_Portal_Data_Exporter.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the user data export.
 *
 * @package
 * @since
 */
class Easy_Job_Portal_Data_Exporter {
	/**
	 * Register the user data exporter method
	 *
	 * @param array $exporters The exporter array.
	 * @return array $exporters The exporter array.
	 */
	public static function register_wpjm_user_data_exporter( $exporters ) {
		$exporters['easy-job-portal'] = [
			'exporter_friendly_name' => __( 'Easy Job Portal', 'easy-job-portal' ),
			'callback'               => [ __CLASS__, 'user_data_exporter' ],
		];
		return $exporters;
	}

	/**
	 * Data exporter
	 *
	 * @param string $email_address User email address.
	 * @return array
	 */
	public static function user_data_exporter( $email_address ) {
		$export_items = [];
		$user         = get_user_by( 'email', $email_address );
		if ( false === $user ) {
			return [
				'data' => $export_items,
				'done' => true,
			];
		}

		$user_data_to_export = [];
		$user_meta_keys      = [
			'_company_logo'    => __( 'Company Logo', 'easy-job-portal' ),
			'_company_name'    => __( 'Company Name', 'easy-job-portal' ),
			'_company_website' => __( 'Company Website', 'easy-job-portal' ),
			'_company_tagline' => __( 'Company Tagline', 'easy-job-portal' ),
			'_company_twitter' => __( 'Company Twitter', 'easy-job-portal' ),
			// '_company_video'   => __( 'Company Video', 'easy-job-portal' ),
		];

		foreach ( $user_meta_keys as $user_meta_key => $name ) {
			$user_meta = get_user_meta( $user->ID, $user_meta_key, true );

			if ( empty( $user_meta ) ) {
				continue;
			}

			if ( '_company_logo' === $user_meta_key ) {
				$user_meta = wp_get_attachment_url( $user_meta );
				if ( false === $user_meta ) {
					continue;
				}
			}

			$user_data_to_export[] = [
				'name'  => $name,
				'value' => $user_meta,
			];
		}

		$export_items[] = [
			'group_id'    => 'wpjm-user-data',
			'group_label' => __( 'Easy Job Portal User Data', 'easy-job-portal' ),
			'item_id'     => "wpjm-user-data-{$user->ID}",
			'data'        => $user_data_to_export,
		];

		return [
			'data' => $export_items,
			'done' => true,
		];
	}
}

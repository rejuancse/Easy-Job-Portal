<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Easy_Job_Portal_Post_Types {

	const PERMALINK_OPTION_NAME = 'easy_job_portal_permalinks';

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_post_types' ], 0 );
		add_action( 'init', [ $this, 'prepare_block_editor' ] );
		add_action( 'init', [ $this, 'register_meta_fields' ] );
		add_filter( 'admin_head', [ $this, 'admin_head' ] );
		add_action( 'easy_job_portal_check_for_expired_jobs', [ $this, 'check_for_expired_jobs' ] );
		add_action( 'easy_job_portal_delete_old_previews', [ $this, 'delete_old_previews' ] );

		add_action( 'pending_to_publish', [ $this, 'set_expiry' ] );
		add_action( 'preview_to_publish', [ $this, 'set_expiry' ] );
		add_action( 'draft_to_publish', [ $this, 'set_expiry' ] );
		add_action( 'auto-draft_to_publish', [ $this, 'set_expiry' ] );
		add_action( 'expired_to_publish', [ $this, 'set_expiry' ] );
		add_action( 'wp_head', [ $this, 'noindex_expired_filled_job_listings' ] );
		add_action( 'wp_footer', [ $this, 'output_structured_data' ] );

		add_filter( 'the_job_description', 'wptexturize' );
		add_filter( 'the_job_description', 'convert_smilies' );
		add_filter( 'the_job_description', 'convert_chars' );
		add_filter( 'the_job_description', 'wpautop' );
		add_filter( 'the_job_description', 'shortcode_unautop' );
		add_filter( 'the_job_description', 'prepend_attachment' );

		if ( ! empty( $GLOBALS['wp_embed'] ) ) {
			add_filter( 'the_job_description', [ $GLOBALS['wp_embed'], 'run_shortcode' ], 8 );
			add_filter( 'the_job_description', [ $GLOBALS['wp_embed'], 'autoembed' ], 8 );
		}

		add_action( 'easy_job_portal_application_details_email', [ $this, 'application_details_email' ] );
		add_action( 'easy_job_portal_application_details_url', [ $this, 'application_details_url' ] );

		add_filter( 'wp_insert_post_data', [ $this, 'fix_post_name' ], 10, 2 );
		add_action( 'add_post_meta', [ $this, 'maybe_add_geolocation_data' ], 10, 3 );
		add_action( 'update_post_meta', [ $this, 'update_post_meta' ], 10, 4 );
		add_action( 'wp_insert_post', [ $this, 'maybe_add_default_meta_data' ], 10, 2 );
		add_filter( 'post_types_to_delete_with_user', [ $this, 'delete_user_add_job_listings_post_type' ] );

		add_action( 'transition_post_status', [ $this, 'track_job_submission' ], 10, 3 );

		add_action( 'parse_query', [ $this, 'add_feed_query_args' ] );

		// Single job content.
		$this->job_content_filter( true );
	}

	/**
	 * Prepare CPTs for special block editor situations.
	 */
	public function prepare_block_editor() {
		add_filter( 'allowed_block_types_all', [ $this, 'force_classic_block' ], 10, 2 );

		if ( false === easy_job_portal_multi_job_type() ) {
			add_filter( 'rest_prepare_taxonomy', [ $this, 'hide_job_type_block_editor_selector' ], 10, 3 );
		}
	}

	public function force_classic_block( $allowed_block_types_all, $post ) {

		if ( isset( $post->post_type ) && 'job_listing' === $post->post_type ) {
			return [ 'core/freeform' ];
		}
		return $allowed_block_types_all;
	}

	public function hide_job_type_block_editor_selector( $response, $taxonomy, $request ) {
		if (
			'job_listing_type' === $taxonomy->name
			&& 'edit' === $request->get_param( 'context' )
		) {
			$response->data['visibility']['show_ui'] = false;
		}
		return $response;
	}

	/**
	 * Registers the custom post type and taxonomies.
	 */
	public function register_post_types() {
		if ( post_type_exists( 'job_listing' ) ) {
			return;
		}

		$admin_capability = 'manage_job_listings';

		$permalink_structure = self::get_permalink_structure();

		/**
		 * Taxonomies
		 */
		if ( get_option( 'easy_job_portal_enable_categories' ) ) {
			$singular = __( 'Job category', 'easy-job-portal' );
			$plural   = __( 'Job categories', 'easy-job-portal' );

			if ( current_theme_supports( 'easy-job-portal-templates' ) ) {
				$rewrite = [
					'slug'         => $permalink_structure['category_rewrite_slug'],
					'with_front'   => false,
					'hierarchical' => false,
				];
				$public  = true;
			} else {
				$rewrite = false;
				$public  = false;
			}

			register_taxonomy(
				'job_listing_category',
				apply_filters( 'register_taxonomy_job_listing_category_object_type', [ 'job_listing' ] ),
				apply_filters(
					'register_taxonomy_job_listing_category_args',
					[
						'hierarchical'          => true,
						'update_count_callback' => '_update_post_term_count',
						'label'                 => $plural,
						'labels'                => [
							'name'              => $plural,
							'singular_name'     => $singular,
							'menu_name'         => ucwords( $plural ),
							'search_items'      => sprintf( __( 'Search %s', 'easy-job-portal' ), $plural ),
							'all_items'         => sprintf( __( 'All %s', 'easy-job-portal' ), $plural ),
							'parent_item'       => sprintf( __( 'Parent %s', 'easy-job-portal' ), $singular ),
							'parent_item_colon' => sprintf( __( 'Parent %s:', 'easy-job-portal' ), $singular ),
							'edit_item'         => sprintf( __( 'Edit %s', 'easy-job-portal' ), $singular ),
							'update_item'       => sprintf( __( 'Update %s', 'easy-job-portal' ), $singular ),
							'add_new_item'      => sprintf( __( 'Add New %s', 'easy-job-portal' ), $singular ),
							'new_item_name'     => sprintf( __( 'New %s Name', 'easy-job-portal' ), $singular ),
						],
						'show_ui'               => true,
						'show_tagcloud'         => false,
						'public'                => $public,
						'capabilities'          => [
							'manage_terms' => $admin_capability,
							'edit_terms'   => $admin_capability,
							'delete_terms' => $admin_capability,
							'assign_terms' => $admin_capability,
						],
						'rewrite'               => $rewrite,
						'show_in_rest'          => true,
						'rest_base'             => 'job-categories',

					]
				)
			);
		}

		if ( get_option( 'easy_job_portal_enable_types' ) ) {
			$singular = __( 'Job type', 'easy-job-portal' );
			$plural   = __( 'Job types', 'easy-job-portal' );

			if ( current_theme_supports( 'easy-job-portal-templates' ) ) {
				$rewrite = [
					'slug'         => $permalink_structure['type_rewrite_slug'],
					'with_front'   => false,
					'hierarchical' => false,
				];
				$public  = true;
			} else {
				$rewrite = false;
				$public  = false;
			}

			register_taxonomy(
				'job_listing_type',
				apply_filters( 'register_taxonomy_job_listing_type_object_type', [ 'job_listing' ] ),
				apply_filters(
					'register_taxonomy_job_listing_type_args',
					[
						'hierarchical'         => true,
						'label'                => $plural,
						'labels'               => [
							'name'              => $plural,
							'singular_name'     => $singular,
							'menu_name'         => ucwords( $plural ),
							'search_items'      => sprintf( __( 'Search %s', 'easy-job-portal' ), $plural ),
							'all_items'         => sprintf( __( 'All %s', 'easy-job-portal' ), $plural ),
							'parent_item'       => sprintf( __( 'Parent %s', 'easy-job-portal' ), $singular ),
							'edit_item'         => sprintf( __( 'Edit %s', 'easy-job-portal' ), $singular ),
							'update_item'       => sprintf( __( 'Update %s', 'easy-job-portal' ), $singular ),
							'add_new_item'      => sprintf( __( 'Add New %s', 'easy-job-portal' ), $singular ),
							'new_item_name'     => sprintf( __( 'New %s Name', 'easy-job-portal' ), $singular ),
						],
						'show_ui'              => true,
						'show_tagcloud'        => false,
						'public'               => $public,
						'capabilities'         => [
							'manage_terms' => $admin_capability,
							'edit_terms'   => $admin_capability,
							'delete_terms' => $admin_capability,
							'assign_terms' => $admin_capability,
						],
						'rewrite'              => $rewrite,
						'show_in_rest'         => true,
						'rest_base'            => 'job-types',
						'meta_box_sanitize_cb' => [ $this, 'sanitize_job_type_meta_box_input' ],
					]
				)
			);
			if ( function_exists( 'wpjm_job_listing_employment_type_enabled' ) && wpjm_job_listing_employment_type_enabled() ) {
				register_meta(
					'term',
					'employment_type',
					[
						'object_subtype'    => 'job_listing_type',
						'show_in_rest'      => true,
						'type'              => 'string',
						'single'            => true,
						'description'       => esc_html__( 'Employment Type', 'easy-job-portal' ),
						'sanitize_callback' => [ $this, 'sanitize_employment_type' ],
					]
				);
			}
		}

		/**
		 * Post types
		 */
		$singular = __( 'Job', 'easy-job-portal' );
		$plural   = __( 'Jobs', 'easy-job-portal' );

		/**
		 * Set whether to add archive page support when registering the job listing post type.
		 */
		if ( apply_filters( 'easy_job_portal_enable_job_archive_page', current_theme_supports( 'easy-job-portal-templates' ) ) ) {
			$has_archive = $permalink_structure['jobs_archive_rewrite_slug'];
		} else {
			$has_archive = false;
		}

		$rewrite = [
			'slug'       => $permalink_structure['job_rewrite_slug'],
			'with_front' => false,
			'feeds'      => true,
			'pages'      => false,
		];

		register_post_type(
			'job_listing',
			apply_filters(
				'register_post_type_job_listing',
				[
					'labels'                => [
						'name'                  => $plural,
						'singular_name'         => $singular,
						'menu_name'             => __( 'Easy Job Portal', 'easy-job-portal' ),
						'all_items'             => sprintf( __( 'All %s', 'easy-job-portal' ), $plural ),
						'add_new'               => __( 'Add New', 'easy-job-portal' ),
						'add_new_item'          => sprintf( __( 'Add %s', 'easy-job-portal' ), $singular ),
						'edit'                  => __( 'Edit', 'easy-job-portal' ),
						'edit_item'             => sprintf( __( 'Edit %s', 'easy-job-portal' ), $singular ),
						'new_item'              => sprintf( __( 'New %s', 'easy-job-portal' ), $singular ),
						'view'                  => sprintf( __( 'View %s', 'easy-job-portal' ), $singular ),
						'view_item'             => sprintf( __( 'View %s', 'easy-job-portal' ), $singular ),
						'search_items'          => sprintf( __( 'Search %s', 'easy-job-portal' ), $plural ),
						'not_found'             => sprintf( __( 'No %s found', 'easy-job-portal' ), $plural ),
						'not_found_in_trash'    => sprintf( __( 'No %s found in trash', 'easy-job-portal' ), $plural ),
						'parent'                => sprintf( __( 'Parent %s', 'easy-job-portal' ), $singular ),
						'featured_image'        => __( 'Company Logo', 'easy-job-portal' ),
						'set_featured_image'    => __( 'Set company logo', 'easy-job-portal' ),
						'remove_featured_image' => __( 'Remove company logo', 'easy-job-portal' ),
						'use_featured_image'    => __( 'Use as company logo', 'easy-job-portal' ),
					],
					'description'           => sprintf( __( 'This is where you can create and manage %s.', 'easy-job-portal' ), $plural ),
					'public'                => true,
					'show_ui'               => true,
					'capability_type'       => 'job_listing',
					'map_meta_cap'          => true,
					'publicly_queryable'    => true,
					'exclude_from_search'   => false,
					'hierarchical'          => false,
					'rewrite'               => $rewrite,
					'query_var'             => true,
					'supports'              => [ 'title', 'editor', 'custom-fields', 'publicize', 'thumbnail', 'author' ],
					'has_archive'           => $has_archive,
					'show_in_nav_menus'     => false,
					'delete_with_user'      => true,
					'show_in_rest'          => true,
					'rest_base'             => 'job-listings',
					'rest_controller_class' => 'WP_REST_Posts_Controller',
					'template'              => [ [ 'core/freeform' ] ],
					'template_lock'         => 'all',
					'menu_icon'				=> 'dashicons-businessman',

					
				]
			)
		);

		/**
		 * Feeds
		 */
		add_feed( self::get_job_feed_name(), [ $this, 'job_feed' ] );

		/**
		 * Post status
		 */
		register_post_status(
			'expired',
			[
				'label'                     => _x( 'Expired', 'post status', 'easy-job-portal' ),
				'public'                    => true,
				'protected'                 => true,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				// translators: Placeholder %s is the number of expired posts of this type.
				'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'easy-job-portal' ),
			]
		);
		register_post_status(
			'preview',
			[
				'label'                     => _x( 'Preview', 'post status', 'easy-job-portal' ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => true,
				// translators: Placeholder %s is the number of posts in a preview state.
				'label_count'               => _n_noop( 'Preview <span class="count">(%s)</span>', 'Preview <span class="count">(%s)</span>', 'easy-job-portal' ),
			]
		);
	}

	/**
	 * Change label for admin menu item to show number of Job Listing items pending approval.
	 */
	public function admin_head() {
		global $menu;

		$pending_jobs = Easy_Job_Portal_Cache_Helper::get_listings_count();

		// No need to go further if no pending jobs, menu is not set, or is not an array.
		if ( empty( $pending_jobs ) || empty( $menu ) || ! is_array( $menu ) ) {
			return;
		}

		// Try to pull menu_name from post type object to support themes/plugins that change the menu string.
		$post_type = get_post_type_object( 'job_listing' );
		$plural    = isset( $post_type->labels, $post_type->labels->menu_name ) ? $post_type->labels->menu_name : __( 'Easy Job Portal', 'easy-job-portal' );

		foreach ( $menu as $key => $menu_item ) {
			if ( strpos( $menu_item[0], $plural ) === 0 ) {
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Only way to add pending listing count.
				$menu[ $key ][0] .= " <span class='awaiting-mod update-plugins count-" . esc_attr( $pending_jobs ) . "'><span class='pending-count'>" . absint( number_format_i18n( $pending_jobs ) ) . '</span></span>';
				break;
			}
		}
	}

	/**
	 * Filter the post content of job listings.
	 */
	public static function output_kses_post( $post_content ) {
		echo wp_kses( $post_content, self::kses_allowed_html() );
	}

	private static function kses_allowed_html() {
		return apply_filters(
			'easy_job_portal_kses_allowed_html',
			array_replace_recursive( 
				wp_kses_allowed_html( 'post' ),
				[
					'iframe' => [
						'src'             => true,
						'width'           => true,
						'height'          => true,
						'frameborder'     => true,
						'marginwidth'     => true,
						'marginheight'    => true,
						'scrolling'       => true,
						'title'           => true,
						'allow'           => true,
						'allowfullscreen' => true,
					],
				]
			)
		);
	}

	public function sanitize_job_type_meta_box_input( $taxonomy, $input ) {
		if ( is_array( $input ) ) {
			return array_map( 'intval', $input );
		}
		return intval( $input );
	}

	private function job_content_filter( $enable ) {
		if ( ! $enable ) {
			remove_filter( 'the_content', [ $this, 'job_content' ] );
		} else {
			add_filter( 'the_content', [ $this, 'job_content' ] );
		}
	}

	public function job_content( $content ) {
		global $post;

		if ( ! is_singular( 'job_listing' ) || ! in_the_loop() || 'job_listing' !== $post->post_type ) {
			return $content;
		}

		ob_start();

		$this->job_content_filter( false );

		do_action( 'job_content_start' );

		get_easy_job_portal_template_part( 'content-single', 'job_listing' );

		do_action( 'job_content_end' );

		$this->job_content_filter( true );

		return apply_filters( 'easy_job_portal_single_job_content', ob_get_clean(), $post );
	}

	/**
	 * Generates the RSS feed for Easy Job Portal.
	 */
	public function job_feed() {
		global $easy_job_portal_keyword;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Input used to filter public data in feed.
		$input_posts_per_page  = isset( $_GET['posts_per_page'] ) ? absint( $_GET['posts_per_page'] ) : 10;
		$input_search_location = isset( $_GET['search_location'] ) ? sanitize_text_field( wp_unslash( $_GET['search_location'] ) ) : false;
		$input_job_types       = isset( $_GET['job_types'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_GET['job_types'] ) ) ) : false;
		$input_job_categories  = isset( $_GET['job_categories'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_GET['job_categories'] ) ) ) : false;
		$easy_job_portal_keyword   = isset( $_GET['search_keywords'] ) ? sanitize_text_field( wp_unslash( $_GET['search_keywords'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		$query_args = [
			'post_type'           => 'job_listing',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $input_posts_per_page,
			'paged'               => absint( get_query_var( 'paged', 1 ) ),
			'tax_query'           => [],
			'meta_query'          => [],
		];

		if ( ! empty( $input_search_location ) ) {
			$location_meta_keys = [ 'geolocation_formatted_address', '_job_location', 'geolocation_state_long' ];
			$location_search    = [ 'relation' => 'OR' ];
			foreach ( $location_meta_keys as $meta_key ) {
				$location_search[] = [
					'key'     => $meta_key,
					'value'   => $input_search_location,
					'compare' => 'like',
				];
			}
			$query_args['meta_query'][] = $location_search;
		}

		if ( ! empty( $input_job_types ) ) {
			$query_args['tax_query'][] = [
				'taxonomy' => 'job_listing_type',
				'field'    => 'slug',
				'terms'    => $input_job_types + [ 0 ],
			];
		}

		if ( ! empty( $input_job_categories ) ) {
			$cats                      = $input_job_categories + [ 0 ];
			$field                     = is_numeric( $cats ) ? 'term_id' : 'slug';
			$operator                  = 'all' === get_option( 'easy_job_portal_category_filter_type', 'all' ) && count( $cats ) > 1 ? 'AND' : 'IN';
			$query_args['tax_query'][] = [
				'taxonomy'         => 'job_listing_category',
				'field'            => $field,
				'terms'            => $cats,
				'include_children' => 'AND' !== $operator,
				'operator'         => $operator,
			];
		}

		if ( ! empty( $easy_job_portal_keyword ) ) {
			$query_args['s'] = $easy_job_portal_keyword;
			add_filter( 'posts_search', 'get_job_listings_keyword_search' );
		}

		if ( empty( $query_args['meta_query'] ) ) {
			unset( $query_args['meta_query'] );
		}

		if ( empty( $query_args['tax_query'] ) ) {
			unset( $query_args['tax_query'] );
		}

		// phpcs:ignore WordPress.WP.DiscouragedFunctions
		query_posts( apply_filters( 'job_feed_args', $query_args ) );
		add_action( 'rss2_ns', [ $this, 'job_feed_namespace' ] );
		add_action( 'rss2_item', [ $this, 'job_feed_item' ] );
		do_feed_rss2( false );
		remove_filter( 'posts_search', 'get_job_listings_keyword_search' );
	}

	/**
	 * Adds query arguments in order to make sure that the feed properly queries the 'job_listing' type.
	 *
	 * @param WP_Query $wp
	 */
	public function add_feed_query_args( $wp ) {

		// Let's leave if not the job feed.
		if ( ! isset( $wp->query_vars['feed'] ) || self::get_job_feed_name() !== $wp->query_vars['feed'] ) {
			return;
		}

		// Leave if not a feed.
		if ( false === $wp->is_feed ) {
			return;
		}

		// If the post_type was already set, let's get out of here.
		if ( isset( $wp->query_vars['post_type'] ) && ! empty( $wp->query_vars['post_type'] ) ) {
			return;
		}

		$wp->query_vars['post_type'] = 'job_listing';
	}

	/**
	 * Adds a custom namespace to the job feed.
	 */
	public function job_feed_namespace() {
		echo 'xmlns:job_listing="' . esc_url( site_url() ) . '"' . "\n";
	}

	/**
	 * Adds custom data to the job feed.
	 */
	public function job_feed_item() {
		$post_id   = get_the_ID();
		$location  = get_the_job_location( $post_id );
		$company   = get_the_company_name( $post_id );
		$job_types = wpjm_get_the_job_types( $post_id );

		if ( $location ) {
			echo '<job_listing:location><![CDATA[' . esc_html( $location ) . "]]></job_listing:location>\n";
		}
		if ( ! empty( $job_types ) ) {
			$job_types_names = implode( ', ', wp_list_pluck( $job_types, 'name' ) );
			echo '<job_listing:job_type><![CDATA[' . esc_html( $job_types_names ) . "]]></job_listing:job_type>\n";
		}
		if ( $company ) {
			echo '<job_listing:company><![CDATA[' . esc_html( $company ) . "]]></job_listing:company>\n";
		}

		/**
		 * Fires at the end of each job RSS feed item.
		 *
		 * @param int $post_id The post ID of the job.
		 */
		do_action( 'job_feed_item', $post_id );
	}

	/**
	 * Maintenance task to expire jobs.
	 */
	public function check_for_expired_jobs() {
		// Change status to expired.
		$job_ids = get_posts(
			[
				'post_type'      => 'job_listing',
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'posts_per_page' => -1,
				'meta_query'     => [
					'relation' => 'AND',
					[
						'key'     => '_job_expires',
						'value'   => 0,
						'compare' => '>',
					],
					[
						'key'     => '_job_expires',
						'value'   => date( 'Y-m-d', current_time( 'timestamp' ) ),
						'compare' => '<',
					],
				],
			]
		);

		if ( $job_ids ) {
			foreach ( $job_ids as $job_id ) {
				$job_data                = [];
				$job_data['ID']          = $job_id;
				$job_data['post_status'] = 'expired';
				wp_update_post( $job_data );
			}
		}

		// Delete old expired jobs.
		if ( apply_filters( 'easy_job_portal_delete_expired_jobs', false ) ) {

			$delete_expired_jobs_days = apply_filters( 'easy_job_portal_delete_expired_jobs_days', 30 );

			$job_ids = get_posts(
				[
					'post_type'      => 'job_listing',
					'post_status'    => 'expired',
					'fields'         => 'ids',
					'date_query'     => [
						[
							'column' => 'post_modified',
							'before' => date( 'Y-m-d', strtotime( '-' . $delete_expired_jobs_days . ' days', current_time( 'timestamp' ) ) ),
						],
					],
					'posts_per_page' => -1,
				]
			);

			if ( $job_ids ) {
				foreach ( $job_ids as $job_id ) {
					wp_trash_post( $job_id );
				}
			}
		}
	}

	/**
	 * Deletes old previewed jobs after 30 days to keep the DB clean.
	 */
	public function delete_old_previews() {
		// Delete old jobs stuck in preview.
		$job_ids = get_posts(
			[
				'post_type'      => 'job_listing',
				'post_status'    => 'preview',
				'fields'         => 'ids',
				'date_query'     => [
					[
						'column' => 'post_modified',
						'before' => date( 'Y-m-d', strtotime( '-30 days', current_time( 'timestamp' ) ) ),
					],
				],
				'posts_per_page' => -1,
			]
		);

		if ( $job_ids ) {
			foreach ( $job_ids as $job_id ) {
				wp_delete_post( $job_id, true );
			}
		}
	}

	public function set_expirey( $post ) {
		_deprecated_function( __METHOD__, '1.0.1', 'Easy_Job_Portal_Post_Types::set_expiry' );
		$this->set_expiry( $post );
	}

	public function set_expiry( $post ) {
		if ( 'job_listing' !== $post->post_type ) {
			return;
		}

		// See if it is already set.
		if ( metadata_exists( 'post', $post->ID, '_job_expires' ) ) {
			$expires = get_post_meta( $post->ID, '_job_expires', true );
			if ( $expires && strtotime( $expires ) < current_time( 'timestamp' ) ) {
				update_post_meta( $post->ID, '_job_expires', '' );
			}
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce check handled by WP core.
		$input_job_expires = isset( $_POST['_job_expires'] ) ? sanitize_text_field( wp_unslash( $_POST['_job_expires'] ) ) : null;

		// See if the user has set the expiry manually.
		if ( ! empty( $input_job_expires ) ) {
			update_post_meta( $post->ID, '_job_expires', date( 'Y-m-d', strtotime( $input_job_expires ) ) );
		} elseif ( ! isset( $expires ) ) {
			// No manual setting? Lets generate a date if there isn't already one.
			$expires = calculate_job_expiry( $post->ID );
			update_post_meta( $post->ID, '_job_expires', $expires );

			// In case we are saving a post, ensure post data is updated so the field is not overridden.
			if ( null !== $input_job_expires ) {
				$_POST['_job_expires'] = $expires;
			}
		}
	}

	public function application_details_email( $apply ) {
		get_easy_job_portal_template( 'job-application-email.php', [ 'apply' => $apply ] );
	}

	public function application_details_url( $apply ) {
		get_easy_job_portal_template( 'job-application-url.php', [ 'apply' => $apply ] );
	}

	public function fix_post_name( $data, $postarr ) {
		if ( 'job_listing' === $data['post_type']
			&& 'pending' === $data['post_status']
			&& ! current_user_can( 'publish_posts' )
			&& isset( $postarr['post_name'] )
		) {
			$data['post_name'] = $postarr['post_name'];
		}
		return $data;
	}

	public static function job_is_editable( $job_id ) {
		$job_is_editable = true;
		$post_status     = get_post_status( $job_id );

		if (
			( 'publish' === $post_status && ! wpjm_user_can_edit_published_submissions() )
			|| ( 'publish' !== $post_status && ! easy_job_portal_user_can_edit_pending_submissions() )
		) {
			$job_is_editable = false;
		}

		return apply_filters( 'easy_job_portal_job_is_editable', $job_is_editable, $job_id );
	}

	public static function get_job_feed_name() {
		return apply_filters( 'easy_job_portal_job_feed_name', 'job_feed' );
	}

	public static function get_raw_permalink_settings() {

		$legacy_permalink_settings = '[]';
		if ( false !== get_option( 'wpjm_permalinks', false ) ) {
			$legacy_permalink_settings = wp_json_encode( get_option( 'wpjm_permalinks', [] ) );
			delete_option( 'wpjm_permalinks' );
		}

		return (array) json_decode( get_option( self::PERMALINK_OPTION_NAME, $legacy_permalink_settings ), true );
	}

	public static function get_permalink_structure() {
		// Switch to the site's default locale, bypassing the active user's locale.
		if ( function_exists( 'switch_to_locale' ) && did_action( 'admin_init' ) ) {
			switch_to_locale( get_locale() );
		}

		$permalink_settings = self::get_raw_permalink_settings();

		// First-time activations will get this cleared on activation.
		if ( ! array_key_exists( 'jobs_archive', $permalink_settings ) ) {
			// Create entry to prevent future checks.
			$permalink_settings['jobs_archive'] = '';
			if ( current_theme_supports( 'easy-job-portal-templates' ) ) {
				// This isn't the first activation and the theme supports it. Set the default to legacy value.
				$permalink_settings['jobs_archive'] = _x( 'jobs', 'Post type archive slug - resave permalinks after changing this', 'easy-job-portal' );
			}
			update_option( self::PERMALINK_OPTION_NAME, wp_json_encode( $permalink_settings ) );
		}

		$permalinks = wp_parse_args(
			$permalink_settings,
			[
				'job_base'      => '',
				'category_base' => '',
				'type_base'     => '',
				'jobs_archive'  => '',
			]
		);

		// Ensure rewrite slugs are set. Use legacy translation options if not.
		$permalinks['job_rewrite_slug']          = untrailingslashit( empty( $permalinks['job_base'] ) ? _x( 'job_listing', 'Job permalink - resave permalinks after changing this', 'easy-job-portal' ) : $permalinks['job_base'] );
		$permalinks['category_rewrite_slug']     = untrailingslashit( empty( $permalinks['category_base'] ) ? _x( 'job-category', 'Job category slug - resave permalinks after changing this', 'easy-job-portal' ) : $permalinks['category_base'] );
		$permalinks['type_rewrite_slug']         = untrailingslashit( empty( $permalinks['type_base'] ) ? _x( 'job-type', 'Job type slug - resave permalinks after changing this', 'easy-job-portal' ) : $permalinks['type_base'] );
		$permalinks['jobs_archive_rewrite_slug'] = untrailingslashit( empty( $permalinks['jobs_archive'] ) ? 'job-listings' : $permalinks['jobs_archive'] );

		// Restore the original locale.
		if ( function_exists( 'restore_current_locale' ) && did_action( 'admin_init' ) ) {
			restore_current_locale();
		}
		return $permalinks;
	}

	public function maybe_add_geolocation_data( $object_id, $meta_key, $meta_value ) {
		if ( '_job_location' !== $meta_key || 'job_listing' !== get_post_type( $object_id ) ) {
			return;
		}
		do_action( 'easy_job_portal_job_location_edited', $object_id, $meta_value );
	}

	public function update_post_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( 'job_listing' !== get_post_type( $object_id ) ) {
			return;
		}

		switch ( $meta_key ) {
			case '_job_location':
				$this->maybe_update_geolocation_data( $meta_id, $object_id, $meta_key, $meta_value );
				break;
			case '_featured':
				$this->maybe_update_menu_order( $meta_id, $object_id, $meta_key, $meta_value );
				break;
		}
	}

	public function maybe_update_geolocation_data( $meta_id, $object_id, $meta_key, $meta_value ) {
		do_action( 'easy_job_portal_job_location_edited', $object_id, $meta_value );
	}

	public function maybe_update_menu_order( $meta_id, $object_id, $meta_key, $meta_value ) {
		global $wpdb;

		if ( 1 === intval( $meta_value ) ) {
			$wpdb->update(
				$wpdb->posts,
				[ 'menu_order' => -1 ],
				[ 'ID' => $object_id ]
			);
		} else {
			$wpdb->update(
				$wpdb->posts,
				[ 'menu_order' => 0 ],
				[
					'ID'         => $object_id,
					'menu_order' => -1,
				]
			);
		}

		clean_post_cache( $object_id );
	}

	public function maybe_generate_geolocation_data( $meta_id, $object_id, $meta_key, $meta_value ) {
		_deprecated_function( __METHOD__, '1.19.1', 'Easy_Job_Portal_Post_Types::maybe_update_geolocation_data' );
		$this->maybe_update_geolocation_data( $meta_id, $object_id, $meta_key, $meta_value );
	}

	public function maybe_add_default_meta_data( $post_id, $post ) {
		if ( empty( $post ) || 'job_listing' === $post->post_type ) {
			add_post_meta( $post_id, '_filled', 0, true );
			add_post_meta( $post_id, '_featured', 0, true );
		}
	}

	public function track_job_submission( $new_status, $old_status, $post ) {
		if ( empty( $post ) || 'job_listing' !== get_post_type( $post ) ) {
			return;
		}

		if ( $new_status === $old_status || 'publish' !== $new_status ) {
			return;
		}

		// For the purpose of this event, we only care about admin requests and REST API requests.
		if ( ! is_admin() && ! Easy_Job_Portal_Usage_Tracking::is_rest_request() ) {
			return;
		}

		$source = Easy_Job_Portal_Usage_Tracking::is_rest_request() ? 'rest_api' : 'admin';

		if ( 'pending' === $old_status ) {
			// Track approving a new job listing.
			Easy_Job_Portal_Usage_Tracking::track_job_approval(
				$post->ID,
				[
					'source' => $source,
				]
			);

			return;
		}

		Easy_Job_Portal_Usage_Tracking::track_job_submission(
			$post->ID,
			[
				'source'     => $source,
				'old_status' => $old_status,
			]
		);
	}

	/**
	 * Add noindex for expired and filled job listings.
	 */
	public function noindex_expired_filled_job_listings() {
		if ( ! is_single() ) {
			return;
		}

		$post = get_post();
		if ( ! $post || 'job_listing' !== $post->post_type ) {
			return;
		}

		if ( wpjm_allow_indexing_job_listing() ) {
			return;
		}

		wp_no_robots();
		// wp_robots_no_robots();
	}

	/**
	 * Add structured data to the footer of job listing pages.
	 */
	public function output_structured_data() {
		if ( ! is_single() ) {
			return;
		}

		if ( ! wpjm_output_job_listing_structured_data() ) {
			return;
		}

		$structured_data = wpjm_get_job_listing_structured_data();
		if ( ! empty( $structured_data ) ) {
			echo '<!-- Easy Job Portal Structured Data -->' . "\r\n";
			echo '<script type="application/ld+json">' . wpjm_esc_json( wp_json_encode( $structured_data ), true ) . '</script>';
		}
	}

	/**
	 * Sanitize and verify employment type.
	 */
	public function sanitize_employment_type( $employment_type ) {
		$employment_types = wpjm_job_listing_employment_type_options();
		if ( ! isset( $employment_types[ $employment_type ] ) ) {
			return null;
		}
		return $employment_type;
	}

	/**
	 * Registers job listing meta fields.
	 */
	public function register_meta_fields() {
		$fields = self::get_job_listing_fields();

		foreach ( $fields as $meta_key => $field ) {
			register_meta(
				'post',
				$meta_key,
				[
					'type'              => $field['data_type'],
					'show_in_rest'      => $field['show_in_rest'],
					'description'       => $field['label'],
					'sanitize_callback' => $field['sanitize_callback'],
					'auth_callback'     => $field['auth_edit_callback'],
					'single'            => true,
					'object_subtype'    => 'job_listing',
				]
			);
		}
	}

	/**
	 * Returns configuration for custom fields on Job Listing posts.
	 */
	public static function get_job_listing_fields() {
		$default_field = [
			'label'              => null,
			'placeholder'        => null,
			'description'        => null,
			'priority'           => 10,
			'value'              => null,
			'default'            => null,
			'classes'            => [],
			'type'               => 'text',
			'data_type'          => 'string',
			'show_in_admin'      => true,
			'show_in_rest'       => false,
			'auth_edit_callback' => [ __CLASS__, 'auth_check_can_edit_job_listings' ],
			'auth_view_callback' => null,
			'sanitize_callback'  => [ __CLASS__, 'sanitize_meta_field_based_on_input_type' ],
		];

		$allowed_application_method     = get_option( 'easy_job_portal_allowed_application_method', '' );
		$application_method_label       = __( 'Email/URL', 'easy-job-portal' );
		$application_method_placeholder = __( 'Enter an email address or website URL', 'easy-job-portal' );

		if ( 'email' === $allowed_application_method ) {
			$application_method_label       = __( 'Application email', 'easy-job-portal' );
			$application_method_placeholder = __( 'you@example.com', 'easy-job-portal' );
		} elseif ( 'url' === $allowed_application_method ) {
			$application_method_label       = __( 'Application URL', 'easy-job-portal' );
			$application_method_placeholder = __( 'https://', 'easy-job-portal' );
		}

		$fields = [
			'_company_name'    => [
				'label'         => __( 'Company Name', 'easy-job-portal' ),
				'placeholder'   => '',
				'priority'      => 1,
				'data_type'     => 'string',
				'show_in_admin' => true,
				'show_in_rest'  => true,
			],
			'_job_location'    => [
				'label'         => __( 'Location', 'easy-job-portal' ),
				'placeholder'   => __( 'e.g. "Dhaka"', 'easy-job-portal' ),
				'description'   => __( 'Leave this blank if the location is not important.', 'easy-job-portal' ),
				'priority'      => 3,
				'data_type'     => 'string',
				'show_in_admin' => true,
				'show_in_rest'  => true,
			],
			'_application'     => [
				'label'             => $application_method_label,
				'placeholder'       => $application_method_placeholder,
				'description'       => __( 'This field is required for the "application" area to appear beneath the listing.', 'easy-job-portal' ),
				'priority'          => 2,
				'data_type'         => 'string',
				'show_in_admin'     => true,
				'show_in_rest'      => true,
				'sanitize_callback' => [ __CLASS__, 'sanitize_meta_field_application' ],
			],
			'_company_website' => [
				'label'             => __( 'Company Website', 'easy-job-portal' ),
				'placeholder'       => '',
				'priority'          => 4,
				'data_type'         => 'string',
				'show_in_admin'     => true,
				'show_in_rest'      => true,
				'sanitize_callback' => [ __CLASS__, 'sanitize_meta_field_url' ],
			],
			'_company_tagline' => [
				'label'         => __( 'Company Tagline', 'easy-job-portal' ),
				'placeholder'   => __( 'Brief description about the company', 'easy-job-portal' ),
				'priority'      => 5,
				'data_type'     => 'string',
				'show_in_admin' => true,
				'show_in_rest'  => true,
			],
			'_company_twitter' => [
				'label'         => __( 'Company Twitter', 'easy-job-portal' ),
				'placeholder'   => '@yourcompany',
				'priority'      => 6,
				'data_type'     => 'string',
				'show_in_admin' => true,
				'show_in_rest'  => true,
			],
			'_filled'          => [
				'label'         => __( 'Position Filled', 'easy-job-portal' ),
				'type'          => 'checkbox',
				'priority'      => 9,
				'data_type'     => 'integer',
				'show_in_admin' => true,
				'show_in_rest'  => true,
				'description'   => __( 'Filled listings will no longer accept applications.', 'easy-job-portal' ),
			],
			'_featured'        => [
				'label'              => __( 'Featured Listing', 'easy-job-portal' ),
				'type'               => 'checkbox',
				'description'        => __( 'Featured listings will be sticky during searches, and can be styled differently.', 'easy-job-portal' ),
				'priority'           => 11,
				'data_type'          => 'integer',
				'show_in_admin'      => true,
				'show_in_rest'       => true,
				'auth_edit_callback' => [ __CLASS__, 'auth_check_can_manage_job_listings' ],
			],
			'_job_expires'     => [
				'label'              => __( 'Expiry Date', 'easy-job-portal' ),
				'priority'           => 10,
				'show_in_admin'      => true,
				'show_in_rest'       => true,
				'data_type'          => 'string',
				'classes'            => [ 'easy-job-portal-datepicker' ],
				'auth_edit_callback' => [ __CLASS__, 'auth_check_can_manage_job_listings' ],
				'auth_view_callback' => [ __CLASS__, 'auth_check_can_edit_job_listings' ],
				'sanitize_callback'  => [ __CLASS__, 'sanitize_meta_field_date' ],
			],
		];

		$fields = apply_filters( 'easy_job_portal_job_listing_data_fields', $fields );

		// Ensure default fields are set.
		foreach ( $fields as $key => $field ) {
			$fields[ $key ] = array_merge( $default_field, $field );
		}

		return $fields;
	}

	/**
	 * Sanitize meta fields based on input type.
	 */
	public static function sanitize_meta_field_based_on_input_type( $meta_value, $meta_key ) {
		$fields = self::get_job_listing_fields();

		if ( is_string( $meta_value ) ) {
			$meta_value = trim( $meta_value );
		}

		$type = 'text';
		if ( isset( $fields[ $meta_key ] ) ) {
			$type = $fields[ $meta_key ]['type'];
		}

		if ( 'textarea' === $type || 'wp_editor' === $type ) {
			return wp_kses_post( wp_unslash( $meta_value ) );
		}

		if ( 'checkbox' === $type ) {
			if ( $meta_value && '0' !== $meta_value ) {
				return 1;
			}
			return 0;
		}

		if ( is_array( $meta_value ) ) {
			return array_filter( array_map( 'sanitize_text_field', $meta_value ) );
		}

		return sanitize_text_field( $meta_value );
	}

	/**
	 * Sanitize `_application` meta field.
	 */
	public static function sanitize_meta_field_application( $meta_value ) {
		if ( is_email( $meta_value ) ) {
			return sanitize_email( $meta_value );
		}

		return self::sanitize_meta_field_url( $meta_value );
	}

	/**
	 * Sanitize URL meta fields.
	 */
	public static function sanitize_meta_field_url( $meta_value ) {
		$meta_value = trim( $meta_value );
		if ( '' === $meta_value ) {
			return $meta_value;
		}

		return esc_url_raw( $meta_value );
	}

	/**
	 * Sanitize date meta fields.
	 */
	public static function sanitize_meta_field_date( $meta_value ) {
		$meta_value = trim( $meta_value );

		// Matches yyyy-mm-dd.
		if ( ! preg_match( '/[\d]{4}\-[\d]{2}\-[\d]{2}/', $meta_value ) ) {
			return '';
		}

		// Checks for valid date.
		if ( date( 'Y-m-d', strtotime( $meta_value ) ) !== $meta_value ) {
			return '';
		}

		return $meta_value;
	}

	public static function auth_check_can_manage_job_listings( $allowed, $meta_key, $post_id, $user_id ) {
		$user = get_user_by( 'ID', $user_id );

		if ( ! $user ) {
			return false;
		}

		return $user->has_cap( 'manage_job_listings' );
	}

	public static function auth_check_can_edit_job_listings( $allowed, $meta_key, $post_id, $user_id ) {
		$user = get_user_by( 'ID', $user_id );

		if ( ! $user ) {
			return false;
		}

		if ( empty( $post_id ) ) {
			return current_user_can( 'edit_job_listings' );
		}

		return easy_job_portal_user_can_edit_job( $post_id );
	}

	public static function auth_check_can_edit_others_job_listings( $allowed, $meta_key, $post_id, $user_id ) {
		$user = get_user_by( 'ID', $user_id );

		if ( ! $user ) {
			return false;
		}

		return $user->has_cap( 'edit_others_job_listings' );
	}

	/**
	 * Add post type for Job Manager to list of post types deleted with user.
	 */
	public function delete_user_add_job_listings_post_type( $types ) {
		$types[] = 'job_listing';

		return $types;
	}
}

<?php
/**
 * File containing the class Easy_Job_Portal_Widget_Featured_Jobs.
 *
 * @package easy-job-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Featured Jobs widget.
 *
 * @package easy-job-portal
 * @since 1.21.0
 */
class Easy_Job_Portal_Widget_Featured_Jobs extends Easy_Job_Portal_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $wp_post_types;

		// translators: Placeholder %s is the plural label for the job listing post type.
		$this->widget_name        = sprintf( __( 'Featured %s', 'easy-job-portal' ), $wp_post_types['job_listing']->labels->name );
		$this->widget_cssclass    = 'easy_job_portal widget_featured_jobs';
		$this->widget_description = __( 'Display a list of featured listings on your site.', 'easy-job-portal' );
		$this->widget_id          = 'widget_featured_jobs';
		$this->settings           = [
			'title'     => [
				'type'  => 'text',
				// translators: Placeholder %s is the plural label for the job listing post type.
				'std'   => sprintf( __( 'Featured %s', 'easy-job-portal' ), $wp_post_types['job_listing']->labels->name ),
				'label' => __( 'Title', 'easy-job-portal' ),
			],
			'number'    => [
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 10,
				'label' => __( 'Number of listings to show', 'easy-job-portal' ),
			],
			'orderby'   => [
				'type'    => 'select',
				'std'     => 'date',
				'label'   => __( 'Sort By', 'easy-job-portal' ),
				'options' => [
					'date'          => __( 'Date', 'easy-job-portal' ),
					'title'         => __( 'Title', 'easy-job-portal' ),
					'author'        => __( 'Author', 'easy-job-portal' ),
					'rand_featured' => __( 'Random', 'easy-job-portal' ),
				],
			],
			'order'     => [
				'type'    => 'select',
				'std'     => 'DESC',
				'label'   => __( 'Sort Direction', 'easy-job-portal' ),
				'options' => [
					'ASC'  => __( 'Ascending', 'easy-job-portal' ),
					'DESC' => __( 'Descending', 'easy-job-portal' ),
				],
			],
			'show_logo' => [
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => esc_html__( 'Show Company Logo', 'easy-job-portal' ),
			],
		];
		parent::__construct();
	}

	/**
	 * Echoes the widget content.
	 *
	 * @see WP_Widget
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		wp_enqueue_style( 'easy-job-portal-job-listings' );

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		$instance = array_merge( $this->get_default_instance(), $instance );

		ob_start();

		$title_instance = esc_attr( $instance['title'] );
		$number         = absint( $instance['number'] );
		$orderby        = esc_attr( $instance['orderby'] );
		$order          = esc_attr( $instance['order'] );
		$title          = apply_filters( 'widget_title', $title_instance, $instance, $this->id_base );
		$show_logo      = absint( $instance['show_logo'] );
		$jobs           = get_job_listings(
			[
				'posts_per_page' => $number,
				'orderby'        => $orderby,
				'order'          => $order,
				'featured'       => true,
			]
		);

		if ( $jobs->have_posts() ) : ?>

			<?php echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<?php
			if ( $title ) {
				echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>

			<ul class="job_listings">

				<?php
				while ( $jobs->have_posts() ) :
					$jobs->the_post();
					?>

					<?php get_easy_job_portal_template( 'content-widget-job_listing.php', [ 'show_logo' => $show_logo ] ); ?>

				<?php endwhile; ?>

			</ul>

			<?php echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<?php else : ?>

			<?php get_easy_job_portal_template_part( 'content-widget', 'no-jobs-found' ); ?>

			<?php
		endif;

		wp_reset_postdata();

		$content = ob_get_clean();

		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$this->cache_widget( $args, $content );
	}
}

register_widget( 'Easy_Job_Portal_Widget_Featured_Jobs' );

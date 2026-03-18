<?php
/**
 * Show posts related to a person.
 *
 * @package sunflower-persons
 */

?>
<div class="container container-narrow ">
	<div class="container related-posts">
			<?php
				// Fetch all posts connected to this person.
				$sunflower_persons_person_id    = get_the_ID();
				$sunflower_persons_per_page     = 5;
				$sunflower_persons_paged        = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				$sunflower_persons_person_args  = array(
					'post_type'      => 'post',
					'posts_per_page' => $sunflower_persons_per_page,
					'paged'          => $sunflower_persons_paged,
					'meta_query'     => array(
						array(
							'key'     => 'sunflower_connected_persons',
							'value'   => $sunflower_persons_person_id,
							'compare' => 'LIKE',
						),
					),
					'orderby'        => 'date',
					'order'          => 'DESC',
				);
				$sunflower_persons_person_query = new WP_Query( $sunflower_persons_person_args );

				if ( $sunflower_persons_person_query->have_posts() ) {
					?>
					<div class="col-12 p-5">
						<h2>
							<?php
								/* translators: %s: Author's display name. */
									esc_attr_e( 'Latest posts', 'sunflower-persons' );
							?>
						</h2>
					</div>
					<?php
					while ( $sunflower_persons_person_query->have_posts() ) {
						$sunflower_persons_person_query->the_post();
						require SUNFLOWER_PERSONS_PATH . 'template-parts/content-archive.php';
					}
					wp_reset_postdata();
				}

				$sunflower_persons_total_pages = $sunflower_persons_person_query->max_num_pages;

				if ( $sunflower_persons_total_pages > 1 ) {
					echo '<div class="d-flex justify-content-around mt-3 mb-5"><nav class="sunflower-pagination">';
					echo wp_kses_post(
						paginate_links(
							array(
								'total'   => $sunflower_persons_total_pages,
								'current' => $sunflower_persons_paged,
								'type'    => 'list',
							)
						)
					);
					echo '</nav></div>';
				}

				?>
		</div>
	</div>
</div>

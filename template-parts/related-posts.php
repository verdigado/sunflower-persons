<?php
/**
 * Show posts related to a person.
 *
 * @package sunflower-persons
 */

?>
<div class="container container-narrow ">
	<div class="container related-posts">
		<div class="col-12 p-5">
			<h2>
				<?php
					/* translators: %s: Author's display name. */
						esc_attr_e( 'Latest posts', 'sunflower-persons' );
				?>
			</h2>
		</div>


			<?php
				// Fetch all posts connected to this person.
				$sunflower_persons_person_id    = get_the_ID();
				$sunflower_persons_person_args  = array(
					'post_type'  => 'post',
					'meta_query' => array(
						array(
							'key'     => 'sunflower_connected_persons',
							'value'   => $sunflower_persons_person_id,
							'compare' => 'LIKE',
						),
					),
				);
				$sunflower_persons_person_query = new WP_Query( $sunflower_persons_person_args );

				if ( $sunflower_persons_person_query->have_posts() ) {
					while ( $sunflower_persons_person_query->have_posts() ) {
						$sunflower_persons_person_query->the_post();
						require SUNFLOWER_PERSONS_PATH . 'template-parts/content-archive.php';
					}
					wp_reset_postdata();
				}

				?>
		</div>
	</div>
</div>

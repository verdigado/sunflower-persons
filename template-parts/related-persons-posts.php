<?php
/**
 * Show related persons on a single post.
 *
 * @package sunflower-persons
 */

?>
<?php
	// Get ids of all related persons records.
	$sunflower_persons_person_ids = get_post_meta( get_the_ID(), 'sunflower_connected_persons', true );
if ( ! empty( $sunflower_persons_person_ids ) ) {

	$sunflower_persons_person_ids = maybe_unserialize( $sunflower_persons_person_ids );
	$sunflower_persons_person_ids = array_map( 'absint', (array) $sunflower_persons_person_ids );

	$sunflower_persons_person_args = array(
		'post_type'      => 'sunflower_person',
		'post__in'       => $sunflower_persons_person_ids,
		'posts_per_page' => -1,
		'orderby'        => 'post__in',
		'post_status'    => 'publish',
	);

	$sunflower_persons_person_query = new WP_Query( $sunflower_persons_person_args );

	if ( $sunflower_persons_person_query->have_posts() ) :
		?>
			<div class="row ">
			<section class="related-persons">
				<div class="wp-block-group is-layout-grid wp-block-group-is-layout-grid">
					<?php
					while ( $sunflower_persons_person_query->have_posts() ) :
						$sunflower_persons_person_query->the_post();
						?>
						<?php
								$sunflower_persons_context = array(
									'post_id'   => get_the_ID(),
									'title'     => get_the_title(),
									'permalink' => get_permalink(),

									'photo_id'  => get_post_meta( get_the_ID(), 'person_photo_id', true ),

									'govoffice' => get_post_meta( get_the_ID(), 'person_govoffice', true ),
									'mandate'   => get_post_meta( get_the_ID(), 'person_mandate', true ),

								);

								sunflower_persons_get_template_part(
									'persons/related/item',
									$sunflower_persons_context
								);

						?>
					<?php endwhile; ?>
				</div>
			</section>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php endif;
}
?>

<?php
/**
 * Render callback for the block sunflower-persons/person
 *
 * @param array $attributes Block attributes.
 * @return string HTML output.
 *
 * @package Sunflower Persons
 */

$sunflower_persons_person_id = isset( $attributes['personId'] ) ? intval( $attributes['personId'] ) : 0;

// Single person view.
if ( $sunflower_persons_person_id > 0 ) {
	$sunflower_persons_post = get_post( $sunflower_persons_person_id );
	if ( ! $sunflower_persons_post || 'person' !== $sunflower_persons_post->post_type ) {
		return '<div class="sunflower-person">⚠️ ' . esc_html__( 'Person not found.', 'sunflower-persons' ) . '</div>';
	}

	setup_postdata( $sunflower_persons_post );
	?>
	<article class="sunflower-person sunflower-person--single" id="person-<?php echo esc_attr( $sunflower_persons_post->ID ); ?>">
		<div class="sunflower-person__media">
			<?php echo get_the_post_thumbnail( $sunflower_persons_post->ID, 'medium', array( 'class' => 'sunflower-person-thumb' ) ); ?>
		</div>
		<div class="sunflower-person__body">
			<h3 class="sunflower-person__title"><?php echo esc_html( get_the_title( $sunflower_persons_post ) ); ?></h3>
			<?php
				echo wp_kses_post( sunflower_persons_get_all_person_groups( $sunflower_persons_post ) );
			?>
			<div class="sunflower-person__content">
			<?php
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			echo wp_kses_post( apply_filters( 'the_content', $sunflower_persons_post->post_content ) );
			?>
			</div>
		</div>
	</article>
	<?php
	wp_reset_postdata();
} else {
	$sunflower_persons_persons = sunflower_persons_get_all_persons();
	if ( ! $sunflower_persons_persons->have_posts() ) {
		return '<div class="sunflower-person-list">' . esc_html__( 'Keine Personen gefunden.', 'sunflower-persons' ) . '</div>';
	}

	?>
<section class="sunflower-person-list" aria-label="<?php echo esc_attr__( 'Personen', 'sunflower-persons' ); ?>">
	<?php
	while ( $sunflower_persons_persons->have_posts() ) :
		$sunflower_persons_persons->the_post();
		?>
		<article class="sunflower-person">
			<a href="<?php the_permalink(); ?>" class="sunflower-person__link">
				<div class="sunflower-person__media">
					<?php echo get_the_post_thumbnail( get_the_ID(), 'thumbnail', array( 'class' => 'sunflower-person-thumb' ) ); ?>
				</div>
				<h4 class="sunflower-person__title"><?php the_title(); ?></h4>
			</a>
		</article>
	<?php endwhile; ?>
</section>
	<?php
}

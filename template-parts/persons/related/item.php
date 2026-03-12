<?php
/**
 * Block template: List layout
 *
 * @var array $args
 *
 * @package Sunflower Persons
 */

?>
<article
	class="sunflower-person sunflower-person--single has-sand-background-color"
	id="person-<?php echo esc_attr( $args['post_id'] ); ?>">

	<a class="sunflower-person__header"
		href="<?php echo esc_url( $args['permalink'] ); ?>"
		title="<?php echo esc_attr( $args['title'] ); ?>"
		rel="bookmark">

		<?php
			$sunflower_persons_photo_id = get_post_meta( $args['post_id'], 'person_photo_id', true );
		if ( $sunflower_persons_photo_id ) {
			$sunflower_persons_thumbnail = wp_get_attachment_image(
				$sunflower_persons_photo_id,
				'medium',
				false,
				array(
					'class' => 'sunflower-person__image',
					'alt'   => get_post_meta( $sunflower_persons_photo_id, '_wp_attachment_image_alt', true )
								? get_post_meta( $sunflower_persons_photo_id, '_wp_attachment_image_alt', true ) : sprintf(
									/* translators: %s = Personen‑Name */
									__( 'Portrait of %s', 'sunflower-persons' ),
									$args['title']
								),
				)
			);
		}

			// Use default placeholder if no image is set or image is missing in media library.
		if ( empty( $sunflower_persons_thumbnail ) ) {
			$sunflower_persons_thumbnail = sprintf(
				'<img src="%s" class="sunflower-person__image" alt="%s" loading="lazy" decoding="async" />',
				esc_url( SUNFLOWER_PERSONS_URL . 'assets/img/exampleuser_eloise.png' ),
				esc_attr__( 'Default person illustration', 'sunflower-persons' )
			);
		}

			echo wp_kses_post( $sunflower_persons_thumbnail );

		?>


		<div class="sunflower-person__info">

			<h3 class="sunflower-person__title">
				<?php echo esc_html( $args['title'] ); ?>
			</h3>

			<?php if ( $args['govoffice'] ) : ?>
				<p class="sunflower-person__govoffice"><?php echo esc_html( $args['govoffice'] ); ?></p>
			<?php endif; ?>

			<?php if ( $args['mandate'] ) : ?>
				<p class="sunflower-person__mandate"><?php echo esc_html( $args['mandate'] ); ?></p>
			<?php endif; ?>

		</div>

			</a>
</article>

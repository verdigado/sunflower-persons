<?php
/**
 * Block template: List layout
 *
 * @var array $args
 *
 * @package Sunflower Persons
 */

?>
<article class="sunflower-person" data-group="<?php echo esc_attr( implode( ' ', wp_list_pluck( $args['groups'], 'slug' ) ) ); ?>">

	<h4><?php echo esc_html( $args['title'] ); ?></h4>
		<a href="<?php the_permalink(); ?>" class="sunflower-person__link">
			<div class="sunflower-person__media">
			<?php
			$sunflower_persons_thumbnail = '';
			$sunflower_persons_photo_id  = get_post_meta( $args['post_id'], 'person_photo_id', true );
			if ( $sunflower_persons_photo_id ) {
				$sunflower_persons_thumbnail = wp_get_attachment_image( $sunflower_persons_photo_id, 'thumbnail', false, array( 'class' => 'sunflower-person-thumb' ) );
			}

			// If still empty, take the default image.
			if ( ! $sunflower_persons_thumbnail ) {
				$sunflower_persons_thumbnail = '<img src="' . esc_url( SUNFLOWER_PERSONS_URL . 'assets/img/exampleuser_eloise.png' ) . '" class="sunflower-person-thumb" . alt="Drawing of a person head." />';
			}
			echo wp_kses_post( $sunflower_persons_thumbnail );
			?>
			</div>
			<div class="sunflower-person__body">
				<h4 class="sunflower-person__title"><?php the_title(); ?></h4>
			</div>

			<ul class="sunflower-person__meta">
				<?php if ( $args['website'] ) : ?>
					<li class="sunflower-person__website">
						<a href="<?php echo esc_url( $args['website'] ); ?>" target="_blank" rel="noopener">
							<i class="fa-solid fa-globe"></i>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( $args['email'] ) : ?>
					<li class="sunflower-person__email">
						<a href="mailto:<?php echo esc_attr( $args['email'] ); ?>">
							<i class="fa-solid fa-envelope"></i>
						</a>
					</li>
				<?php endif; ?>
				<?php
				if ( $args['socialmedia'] && is_array( $args['socialmedia'] ) ) {
					foreach ( $args['socialmedia'] as $sunflower_persons_person_profile ) {
						echo '<li class="sunflower-person__socialmedia">' . wp_kses_post( $sunflower_persons_person_profile ) . '</li>';
					}
				}
				?>
			</ul>
		</a>

</article>

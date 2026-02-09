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
	<div class="wp-block-media-text is-stacked-on-mobile mb-4" style="grid-template-columns:auto 30%">
		<div class="wp-block-media-text__content">
			<h4><?php echo esc_html( $args['title'] ); ?></h4>
			<ul class="sunflower-person__meta">
			<?php if ( $args['phone'] ) : ?>
				<li class="sunflower-person__phone">
					<i class="fab fa-phone"></i>
					<?php echo esc_html( $args['phone'] ); ?>
				</li>
			<?php endif; ?>
			<?php if ( $args['email'] ) : ?>
				<li class="sunflower-person__email">
					<a href="mailto:<?php echo esc_attr( $args['email'] ); ?>">
						<i class="fab fa-envelope"></i> <?php echo esc_html( $args['email'] ); ?>
					</a>
				</li>
			<?php endif; ?>
			</ul>
			<ul class="sunflower-person__metasocial">
				<?php
				if ( $args['socialmedia'] && is_array( $args['socialmedia'] ) ) {
					foreach ( $args['socialmedia'] as $sunflower_persons_person_profile ) {
						echo '<li class="sunflower-person__socialmedia">' . wp_kses_post( $sunflower_persons_person_profile ) . '</li>';
					}
				}
				?>
			</ul>
		</div>
		<div class="wp-block-media-text__media">
				<?php
					$sunflower_persons_thumbnail = '';
					$sunflower_persons_photo_id  = get_post_meta( $args['post_id'], 'person_photo_id', true );
				if ( $sunflower_persons_photo_id ) {
					$sunflower_persons_thumbnail = wp_get_attachment_image( $sunflower_persons_photo_id, 'thumbnail', false, array( 'class' => 'sunflower-person-thumb' ) );
				}
							echo wp_kses_post( $sunflower_persons_thumbnail );
				?>
		</div>
	</div>
</article>

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
	if ( ! $sunflower_persons_post || 'sunflower_person' !== $sunflower_persons_post->post_type ) {
		return '<div class="sunflower-person">⚠️ ' . esc_html__( 'Person not found.', 'sunflower-persons' ) . '</div>';
	}

	setup_postdata( $sunflower_persons_post );

	$sunflower_persons_person_phone       = get_post_meta( $sunflower_persons_post->ID, 'person_phone', true );
	$sunflower_persons_person_email       = get_post_meta( $sunflower_persons_post->ID, 'person_email', true );
	$sunflower_persons_person_website     = get_post_meta( $sunflower_persons_post->ID, 'person_website', true );
	$sunflower_persons_person_socialmedia = sunflower_persons_get_social_media_profiles( $sunflower_persons_post->ID );
	?>
	<article class="sunflower-person sunflower-person--single" id="person-<?php echo esc_attr( $sunflower_persons_post->ID ); ?>">
		<div class="sunflower-person__media">
			<?php
				$sunflower_persons_thumbnail = '';
				$sunflower_persons_photo_id  = get_post_meta( $sunflower_persons_post->ID, 'person_photo_id', true );
			if ( $sunflower_persons_photo_id ) {
				$sunflower_persons_thumbnail = wp_get_attachment_image( $sunflower_persons_photo_id, 'medium', false, array( 'class' => 'sunflower-person-medium' ) );
			}

			// If still empty, take the default image.
			if ( ! $sunflower_persons_thumbnail ) {
				$sunflower_persons_thumbnail = '<img src="' . esc_url( SUNFLOWER_PERSONS_URL . 'assets/img/exampleuser_eloise.png' ) . '" class="sunflower-person-medium" . alt="Drawing of a person head." />';

			}
				echo wp_kses_post( $sunflower_persons_thumbnail );
			?>
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
		<ul class="sunflower-person__meta">
				<?php if ( $sunflower_persons_person_phone ) : ?>
					<li class="sunflower-person__phone">
						<i class="fa-solid fa-phone"></i>
						<?php echo esc_html( $sunflower_persons_person_phone ); ?>
					</li>
				<?php endif; ?>

				<?php if ( $sunflower_persons_person_email ) : ?>
					<li class="sunflower-person__email">
						<a href="mailto:<?php echo esc_attr( $sunflower_persons_person_email ); ?>">
							<i class="fa-solid fa-envelope"></i>
							<?php echo antispambot( esc_html( $sunflower_persons_person_email ) ); ?>
						</a>
					</li>
				<?php endif; ?>

				<?php if ( $sunflower_persons_person_website ) : ?>
					<li class="sunflower-person__website">
						<a href="<?php echo esc_url( $sunflower_persons_person_website ); ?>" target="_blank" rel="noopener">
							<i class="fa-solid fa-globe"></i>
							<?php echo esc_html( $sunflower_persons_person_website ); ?>
						</a>
					</li>
				<?php endif; ?>

				<?php
				if ( $sunflower_persons_person_socialmedia && is_array( $sunflower_persons_person_socialmedia ) ) {
					foreach ( $sunflower_persons_person_socialmedia as $sunflower_persons_person_profile ) {
						echo '<li class="sunflower-person__socialmedia">' . wp_kses_post( $sunflower_persons_person_profile ) . '</li>';
					}
				}
				?>
			</ul>
	</article>
	<?php
	wp_reset_postdata();
} else {
	// Person list view.
	$sunflower_persons_groups = array();
	if ( isset( $attributes['groups'] ) && ! empty( $attributes['groups'] ) ) {
		$sunflower_persons_groups = $attributes['groups'];
	}
	$sunflower_persons_tags = array();
	if ( isset( $attributes['tags'] ) && ! empty( $attributes['tags'] ) ) {
		$sunflower_persons_tags = $attributes['tags'];
	}
	$sunflower_persons_filter            = array();
	$sunflower_persons_person_filmstrip  = false;
	$sunflower_persons_person_navbuttons = false;
	if ( isset( $attributes['blockLayout'] ) && 'filmstrip' === $attributes['blockLayout'] ) {
		$sunflower_persons_person_filmstrip = true;
		if ( isset( $attributes['showNavButtons'] ) && true === $attributes['showNavButtons'] ) {
			$sunflower_persons_filter['limit']   = -1;
			$sunflower_persons_person_navbuttons = true;
		} elseif ( isset( $attributes['limit'] ) && ! empty( $attributes['limit'] ) ) {
				$sunflower_persons_filter['limit'] = $attributes['limit'];
		}
	}
	if ( isset( $attributes['order'] ) && ! empty( $attributes['order'] ) ) {
		$sunflower_persons_filter['order'] = $attributes['order'];
	}

	$sunflower_persons_persons = sunflower_persons_get_all_persons( $sunflower_persons_groups, $sunflower_persons_tags, $sunflower_persons_filter );
	if ( ! $sunflower_persons_persons->have_posts() ) {
		return '<div class="sunflower-person-list">' . esc_html__( 'No persons found.', 'sunflower-persons' ) . '</div>';
	}
	$sunflower_persons_person_filters = $attributes['showFilterButtons'] ?? false;
	if ( $sunflower_persons_person_filters ) {
		?>
	<div class="persons-filter">
		<?php
		$sunflower_persons_groups_filter = '';
		foreach ( $sunflower_persons_groups as $sunflower_persons_group_slug ) {
			$sunflower_persons_group_term = get_term_by( 'slug', $sunflower_persons_group_slug, 'sunflower_group' );
			if ( $sunflower_persons_group_term ) {
				$sunflower_persons_groups_filter .= sprintf(
					'<button class="filter-btn" data-filter="%s">%s</button>',
					esc_attr( $sunflower_persons_group_term->slug ),
					esc_html( $sunflower_persons_group_term->name )
				);
			}
		}
		echo wp_kses_post( $sunflower_persons_groups_filter );
		?>
	</div>
		<?php
	}
	?>

<section class="sunflower-person-list <?php echo ( $attributes['blockLayout'] ) ? 'sunflower-person-list--' . esc_attr( $attributes['blockLayout'] ) : 'sunflower-person-list--grid'; ?>"
	aria-label="<?php echo esc_attr__( 'Persons', 'sunflower-persons' ); ?>"
	data-visible="<?php echo esc_attr( isset( $attributes['limit'] ) ? intval( $attributes['limit'] ) : 5 ); ?>"
	data-autoplay-timer="<?php echo esc_attr( isset( $attributes['autoplayTimer'] ) ? intval( $attributes['autoplayTimer'] ) : 5 ); ?>"
	data-slide-start="<?php echo esc_attr( isset( $attributes['slideStart'] ) ? $attributes['slideStart'] : 'start' ); ?>"
	data-autoplay="<?php echo esc_attr( isset( $attributes['slideAutoplay'] ) ? intval( $attributes['slideAutoplay'] ) : 0 ); ?>">
	<?php
	if ( true === $sunflower_persons_person_navbuttons ) {
		printf(
			'<button class="sunflower-person-nav prev" aria-label="%s"><i class="fa-solid fa-chevron-left"></i></button>
		',
			esc_attr__( 'Back', 'sunflower-persons' )
		);
	}
	if ( true === $sunflower_persons_person_filmstrip ) {
		printf(
			'<div class="sunflower-person-track-wrapper">
			<div class="sunflower-person-track">
		',
			esc_attr__( 'Back', 'sunflower-persons' )
		);
	}
	?>
		<?php
		while ( $sunflower_persons_persons->have_posts() ) :
			$sunflower_persons_persons->the_post();
			$sunflower_persons_person_phone       = get_post_meta( get_the_ID(), 'person_phone', true );
			$sunflower_persons_person_email       = get_post_meta( get_the_ID(), 'person_email', true );
			$sunflower_persons_person_website     = get_post_meta( get_the_ID(), 'person_website', true );
			$sunflower_persons_person_socialmedia = sunflower_persons_get_social_media_profiles( get_the_ID() );
			$sunflower_persons_person_groups      = wp_get_post_terms( get_the_ID(), 'sunflower_group', array( 'fields' => 'slugs' ) );
			?>
			<article class="sunflower-person" data-group="<?php echo esc_attr( implode( ' ', $sunflower_persons_person_groups ) ); ?>">
				<a href="<?php the_permalink(); ?>" class="sunflower-person__link">
					<div class="sunflower-person__media">
					<?php
					$sunflower_persons_thumbnail = '';
					$sunflower_persons_photo_id  = get_post_meta( get_the_ID(), 'person_photo_id', true );
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
						<?php if ( $sunflower_persons_person_website ) : ?>
							<li class="sunflower-person__website">
								<a href="<?php echo esc_url( $sunflower_persons_person_website ); ?>" target="_blank" rel="noopener">
									<i class="fa-solid fa-globe"></i>
								</a>
							</li>
						<?php endif; ?>
						<?php if ( $sunflower_persons_person_email ) : ?>
							<li class="sunflower-person__email">
								<a href="mailto:<?php echo esc_attr( $sunflower_persons_person_email ); ?>">
									<i class="fa-solid fa-envelope"></i>
								</a>
							</li>
						<?php endif; ?>
						<?php
						if ( $sunflower_persons_person_socialmedia && is_array( $sunflower_persons_person_socialmedia ) ) {
							foreach ( $sunflower_persons_person_socialmedia as $sunflower_persons_person_profile ) {
								echo '<li class="sunflower-person__socialmedia">' . wp_kses_post( $sunflower_persons_person_profile ) . '</li>';
							}
						}
						?>
					</ul>
				</a>
			</article>
		<?php endwhile; ?>
	<?php
	if ( true === $sunflower_persons_person_filmstrip ) {
		printf(
			'</div>
			</div>
		'
		);
	}
	if ( true === $sunflower_persons_person_navbuttons ) {
		printf(
			'
			<button class="sunflower-person-nav next" aria-label="%s"><i class="fa-solid fa-chevron-right"></i></button>',
			esc_attr__( 'Next', 'sunflower-persons' )
		);
	}
	?>
	<?php
}

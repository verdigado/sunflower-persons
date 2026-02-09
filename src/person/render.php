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
	<?php
	/**
	 * Template‑Teil: Einzelansicht einer Person
	 *
	 * Erwartete Variablen (vorher im Loop gesetzt):
	 * - $sunflower_persons_post            (WP_Post)
	 * - $sunflower_persons_person_phone   (string|false)
	 * - $sunflower_persons_person_email   (string|false)
	 * - $sunflower_persons_person_website (string|false)
	 * - $sunflower_persons_person_socialmedia (array|false)
	 */
	?>
<article
	class="sunflower-person sunflower-person--single"
	id="person-<?php echo esc_attr( $sunflower_persons_post->ID ); ?>">

	<header class="sunflower-person__header">

		<?php
		$sunflower_persons_photo_id = (int) get_post_meta( $sunflower_persons_post->ID, 'person_photo_id', true );

		// 1️⃣ Bild aus Medienbibliothek, falls hinterlegt
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
									get_the_title( $sunflower_persons_post )
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

			<h2 class="sunflower-person__title">
				<?php echo esc_html( get_the_title( $sunflower_persons_post ) ); ?>
			</h2>

			<?php
			if ( $sunflower_persons_person_phone
				|| $sunflower_persons_person_email
				|| $sunflower_persons_person_website
				|| ( $sunflower_persons_person_socialmedia && is_array( $sunflower_persons_person_socialmedia ) )
			) :
				?>
				<ul class="sunflower-person__meta">

					<?php if ( $sunflower_persons_person_phone ) : ?>
						<li class="sunflower-person__phone">
							<i class="fab fa-phone" aria-hidden="true"></i>
							<span class="sr-only"><?php esc_html_e( 'Phone', 'sunflower-persons' ); ?>:</span>
							<?php echo esc_html( $sunflower_persons_person_phone ); ?>
						</li>
					<?php endif; ?>

					<?php if ( $sunflower_persons_person_email ) : ?>
						<li class="sunflower-person__email">
							<i class="fab fa-envelope" aria-hidden="true"></i>
							<span class="sr-only"><?php esc_html_e( 'E‑mail', 'sunflower-persons' ); ?>:</span>
							<a href="mailto:<?php echo esc_attr( $sunflower_persons_person_email ); ?>">
								<?php echo antispambot( esc_html( $sunflower_persons_person_email ) ); ?>
							</a>
						</li>
					<?php endif; ?>

					<?php if ( $sunflower_persons_person_website ) : ?>
						<li class="sunflower-person__website">
							<i class="fa-solid fa-globe" aria-hidden="true"></i>
							<span class="sr-only"><?php esc_html_e( 'Website', 'sunflower-persons' ); ?>:</span>
							<a href="<?php echo esc_url( $sunflower_persons_person_website ); ?>"
								target="_blank"
								rel="noopener noreferrer">
								<?php echo esc_html( $sunflower_persons_person_website ); ?>
							</a>
						</li>
					<?php endif; ?>

					<?php
					// Add Social‑Media Icons if available.
					if ( $sunflower_persons_person_socialmedia && is_array( $sunflower_persons_person_socialmedia ) ) :
						echo '<li class="sunflower-person__socialmedia"><ul>';
						foreach ( $sunflower_persons_person_socialmedia as $sunflower_persons_person_profile ) :
							echo '<li class="sunflower-person__socialmedia">'
								. wp_kses_post( $sunflower_persons_person_profile )
								. '</li>';
						endforeach;
						echo '</ul></li>';
					endif;
					?>

				</ul>
			<?php endif; ?>
		</div><!-- .sunflower-person__info -->
	</header>

	<section class="sunflower-person__body">
		<div class="sunflower-person__content">
			<?php
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			echo wp_kses_post( apply_filters( 'the_content', $sunflower_persons_post->post_content ) );
			?>
		</div>
	</section>

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
	if ( isset( $attributes['blockLayout'] ) && 'carousel' === $attributes['blockLayout'] ) {
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

	$sunflower_persons_classes = array( 'sunflower-person-list' );
	$sunflower_persons_classes = get_block_wrapper_attributes(
		array(
			'class' => implode( ' ', $sunflower_persons_classes ),
		)
	);

	?>

	<div <?php echo $sunflower_persons_classes;  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<?php

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

<section class="<?php echo ( $attributes['blockLayout'] ) ? 'sunflower-person-list--' . esc_attr( $attributes['blockLayout'] ) : 'sunflower-person-list--grid'; ?>"
	aria-label="<?php echo esc_attr__( 'Persons', 'sunflower-persons' ); ?>"
	data-visible="<?php echo esc_attr( isset( $attributes['limit'] ) ? intval( $attributes['limit'] ) : 5 ); ?>"
	data-autoplay-timer="<?php echo esc_attr( isset( $attributes['autoplayTimer'] ) ? intval( $attributes['autoplayTimer'] ) : 5 ); ?>"
	data-slide-start="<?php echo esc_attr( isset( $attributes['slideStart'] ) ? $attributes['slideStart'] : 'start' ); ?>"
	data-autoplay="<?php echo esc_attr( isset( $attributes['slideAutoplay'] ) ? intval( $attributes['slideAutoplay'] ) : 0 ); ?>">
	<?php
	$sunflower_persons_layout = $attributes['blockLayout'] ?? 'grid';

	if ( true === $sunflower_persons_person_navbuttons ) {
		printf(
			'<button class="sunflower-person-nav prev" aria-label="%s"><i class="fa-chevron-left"></i></button>
		',
			esc_attr__( 'Back', 'sunflower-persons' )
		);
	}

	sunflower_persons_get_template_part(
		'persons/' . $sunflower_persons_layout . '/wrapper-open',
		array()
	);

	?>
		<?php
		while ( $sunflower_persons_persons->have_posts() ) :
			$sunflower_persons_persons->the_post();

			$sunflower_persons_context = array(
				'post_id'     => get_the_ID(),
				'title'       => get_the_title(),
				'permalink'   => get_permalink(),

				'phone'       => get_post_meta( get_the_ID(), 'person_phone', true ),
				'email'       => get_post_meta( get_the_ID(), 'person_email', true ),
				'website'     => get_post_meta( get_the_ID(), 'person_website', true ),
				'position'    => get_post_meta( get_the_ID(), 'person_position', true ),

				'photo_id'    => get_post_meta( get_the_ID(), 'person_photo_id', true ),

				'groups'      => array_map(
					function ( $term ) {
						return array(
							'slug' => $term->slug,
							'name' => $term->name,
						);
					},
					wp_get_post_terms(
						get_the_ID(),
						'sunflower_group',
						array( 'fields' => 'all' )
					)
				),

				'socialmedia' => sunflower_persons_get_social_media_profiles( get_the_ID() ),
			);


			sunflower_persons_get_template_part(
				'persons/' . $sunflower_persons_layout . '/item',
				$sunflower_persons_context
			);

			endwhile;

		sunflower_persons_get_template_part(
			'persons/' . $sunflower_persons_layout . '/wrapper-close',
			array()
		);

		if ( true === $sunflower_persons_person_navbuttons ) {
			printf(
				'
			<button class="sunflower-person-nav next" aria-label="%s"><i class="fa-chevron-right"></i></button>',
				esc_attr__( 'Next', 'sunflower-persons' )
			);
		}
		?>
</div>
	<?php
}

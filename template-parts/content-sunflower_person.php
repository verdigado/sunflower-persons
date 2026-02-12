<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

$sunflower_styled_layout = (bool) get_post_meta( $post->ID, '_sunflower_styled_layout', true ) ?? false;

$sunflower_show_post_thumbnail = has_post_thumbnail() && ! get_post_meta( $post->ID, '_sunflower_hide_feature_image', true );

$sunflower_persons_person_phone       = get_post_meta( $post->ID, 'person_phone', true );
$sunflower_persons_person_mobilephone = get_post_meta( $post->ID, 'person_mobilephone', true );
$sunflower_persons_person_email       = get_post_meta( $post->ID, 'person_email', true );
$sunflower_persons_person_website     = get_post_meta( $post->ID, 'person_website', true );
$sunflower_persons_person_socialmedia = sunflower_persons_get_social_media_profiles( $post->ID );

$sunflower_persons_person_offices   = get_post_meta( $post->ID, 'person_offices', true );
$sunflower_persons_person_govoffice = get_post_meta( $post->ID, 'person_govoffice', true );
$sunflower_persons_person_mandate   = get_post_meta( $post->ID, 'person_mandate', true );

$sunflower_persons_person_constituency = get_post_meta( $post->ID, 'person_constituency', true );
$sunflower_persons_person_occupation   = get_post_meta( $post->ID, 'person_occupation', true );
$sunflower_persons_person_yearofbirth  = get_post_meta( $post->ID, 'person_yearofbirth', true );
$sunflower_persons_person_statement    = get_post_meta( $post->ID, 'person_statement', true );

$sunflower_class = 'display-single';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $sunflower_class ); ?>>
	<header class="entry-header <?php echo ( $sunflower_show_post_thumbnail ) ? 'has-post-thumbnail' : 'has-no-post-thumbnail'; ?>">
		<div class="row position-relative">
			<div class="col-12">
		<?php
		$sunflower_roofline = get_post_meta( $post->ID, '_sunflower_roofline', true );
		if ( $sunflower_roofline ) {
			printf( ' <div class="roofline roofline-single">%s</div>', esc_attr( $sunflower_roofline ) );
		}
		?>
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
		?>
			</div>
		</div>
	</header><!-- .entry-header -->
	<div class="entry-content accordion">
	<?php
	if ( 'sunflower_person' === get_post_type() ) :
		?>
			<div class="row">
				<div class="col-md-8 order-0 order-md-0">
				<?php
				if ( $sunflower_show_post_thumbnail ) {
					sunflower_post_thumbnail( $sunflower_styled_layout, true );
				}
				if ( $sunflower_persons_person_govoffice ) {
					printf( '<h3 class="wp-block-heading">%s</h3>', esc_html( $sunflower_persons_person_govoffice ) );
				}
				if ( $sunflower_persons_person_mandate ) {
					printf( '<h3 class="wp-block-heading">%s</h3>', esc_html( $sunflower_persons_person_mandate ) );
				}

				$sunflower_persons_person_labels = array();

				if ( $sunflower_persons_person_constituency ) {
					$sunflower_persons_person_labels[] = esc_html__( 'Constituency', 'sunflower-persons' ) . ': ' . esc_html( $sunflower_persons_person_constituency );
				}

				if ( $sunflower_persons_person_occupation ) {
					$sunflower_persons_person_labels[] = esc_html__( 'Occupation', 'sunflower-persons' ) . ': ' . esc_html( $sunflower_persons_person_occupation );
				}

				if ( $sunflower_persons_person_yearofbirth ) {
					$sunflower_persons_person_labels[] = esc_html__( 'Year of birth', 'sunflower-persons' ) . ': ' . esc_html( $sunflower_persons_person_yearofbirth );
				}


				if ( ! empty( $sunflower_persons_person_labels ) ) {
					printf(
						'<p class="sunflower-person__meta-inline-labels">%s</p>',
						wp_kses_post( implode( ' <br/> ', $sunflower_persons_person_labels ) )
					);
				}

				if ( ! empty( $sunflower_persons_person_statement ) ) {
					printf(
						'<blockquote class="wp-block-quote is-layout-flow wp-block-quote-is-layout-flow">%s</blockquote>',
						esc_html( $sunflower_persons_person_statement )
					);
				}

				?>
				</div><!-- .col-md-8 -->

				<div class="col-md-4 order-1 order-md-1 has-sand-background-color px-4 py-4">
					<h3 class="wp-block-heading"><?php esc_html_e( 'Contact', 'sunflower-persons' ); ?></h3>
					<ul class="sunflower-person__meta">
						<?php if ( $sunflower_persons_person_phone ) : ?>
							<li class="sunflower-person__phone">
								<i class="fa-solid fa-phone"></i>
								<?php echo esc_html( $sunflower_persons_person_phone ); ?>
							</li>
						<?php endif; ?>
						<?php if ( $sunflower_persons_person_mobilephone ) : ?>
							<li class="sunflower-person__mobilephone">
								<i class="fa-solid fa-phone"></i>
								<?php echo esc_html( $sunflower_persons_person_mobilephone ); ?>
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
							echo '<li class="sunflower-person__socialmedia"><ul>';
							foreach ( $sunflower_persons_person_socialmedia as $sunflower_persons_person_profile ) {
								echo '<li>' . wp_kses_post( $sunflower_persons_person_profile ) . '</li>';
							}
							echo '</ul></li>';
						}
						?>
					</ul>

					<?php if ( $sunflower_persons_person_offices && is_array( $sunflower_persons_person_offices ) ) : ?>
						<h3 class="wp-block-heading mt-4"><?php esc_html_e( 'Offices', 'sunflower-persons' ); ?></h3>
						<ul class="sunflower-person__offices">
							<?php
							foreach ( $sunflower_persons_person_offices as $sunflower_persons_person_office ) {
								echo '<li class="sunflower-person__office">';
								echo '<strong>' . esc_html( $sunflower_persons_person_office['label'] ) . '</strong><br />';
								echo esc_html( $sunflower_persons_person_office['street'] ) . '<br />';
								echo esc_html( $sunflower_persons_person_office['city'] ) . '<br />';
								if ( ! empty( $sunflower_persons_person_office['phone'] ) ) {
									echo '<i class="fa-solid fa-phone"></i> ' . esc_html( $sunflower_persons_person_office['phone'] ) . '<br />';
								}
								if ( ! empty( $sunflower_persons_person_office['email'] ) ) {
									echo '<i class="fa-solid fa-envelope"></i> <a href="mailto:' . esc_attr( $sunflower_persons_person_office['email'] ) . '">' . antispambot( esc_html( $sunflower_persons_person_office['email'] ) ) . '</a><br />';
								}
								echo '</li>';

								if ( ! empty( $sunflower_persons_person_office['employees'] ) && is_array( $sunflower_persons_person_office['employees'] ) ) {
									echo esc_html_e( 'Employees', 'sunflower-persons' ) . '<ul>';
									foreach ( $sunflower_persons_person_office['employees'] as $sunflower_persons_person_office_employee ) {
										$sunflower_persons_person_hidesinglepage = get_post_meta( $sunflower_persons_person_office_employee, 'person_hide_single', true );
										if ( empty( $sunflower_persons_person_hidesinglepage ) ) {
											echo '<li><a href="' . esc_url( get_permalink( $sunflower_persons_person_office_employee ) ) . '">'
												. esc_html( get_the_title( $sunflower_persons_person_office_employee ) )
												. '</a></li>';
										} else {
											echo '<li>' . esc_html( get_the_title( $sunflower_persons_person_office_employee ) ) . '</li>';
										}
									}
									echo '</ul>';
								}
							}
							?>
						</ul>
					<?php endif; ?>
				</div>
				<div class="col-md-8 order-2 order-md-2">
					<?php
					the_content();
					?>
				</div><!-- .col-md-8 -->
			</div><!-- .row -->
			<?php
		endif;
	?>
	</div>

</article><!-- #post-<?php the_ID(); ?> -->

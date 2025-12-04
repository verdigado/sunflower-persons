<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-white mb-4 has-shadow' ); ?>>
	<div class="wp-block-columns gap-0 gap-md-4 is-layout-flex wp-block-columns-is-layout-flex">
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="wp-block-column" style="flex-basis:50%">
				<a href="<?php echo esc_url( get_permalink() ); ?>" aria-label="Post Thumbnail" rel="bookmark">
					<?php sunflower_post_thumbnail( false, false, true ); ?>
				</a>
			</div>
			<?php
		}
		?>
		<div class="wp-block-column sunflower-person-post-excerpt" style="flex-basis:50%">
			<header class="entry-header mb-2">
				<?php
				if ( 'post' === get_post_type() ) {
					/* translators: used between list items, there is a space after the comma */
					$sunflower_persons_categories_list = get_the_category_list( esc_html__( ', ', 'sunflower' ) );
					if ( $sunflower_persons_categories_list ) {
						/* translators: 1: list of categories. */
						printf( '<span class="cat-links small">%s</span>', wp_kses_post( $sunflower_persons_categories_list ) );
					}
				}

				the_title( '<h2 class="card-title h4 mb-3"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
				?>
			</header><!-- .entry-header -->

			<div class="entry-content">
				<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
				<?php
				the_excerpt();
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sunflower' ),
						'after'  => '</div>',
					)
				);
				?>
				</a>
			</div><!-- .entry-content -->

			<footer class="entry-footer">

				<div class="d-flex flex-row-reverse">
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" class="continue-reading">
					<?php
					esc_attr_e( 'Continue reading', 'sunflower' );
					?>
				</a>
				</div>
			</footer><!-- .entry-footer -->
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->

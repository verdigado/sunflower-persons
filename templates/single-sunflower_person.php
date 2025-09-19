<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package sunflower
 */

get_header();

$sunflower_layout_width  = get_post_meta( $post->ID, '_sunflower_styled_layout', true ) ? '' : 'container-narrow';
$sunflower_styled_layout = get_post_meta( $post->ID, '_sunflower_styled_layout', true ) ? 'styled-layout' : '';
$sunflower_metadata      = get_post_meta( $post->ID, '_sunflower_metadata', true ) ?? false;

?>
	<div id="content" class="container <?php printf( '%s %s', esc_attr( $sunflower_layout_width ), esc_attr( $sunflower_styled_layout ) ); ?>">
		<div class="row">
			<div class="col-12">
				<main id="primary" class="site-main">

					<?php
					while ( have_posts() ) :
						the_post();

						// Fetch the template from the plugin.
						require plugin_dir_path( __DIR__ ) . 'template-parts/content-sunflower_person.php';

					endwhile;

					// End of the loop.
					?>

				</main><!-- #main -->
			</div>
		</div>

	<?php
	if ( sunflower_get_setting( 'sunflower_show_related_posts' ) ) {
		get_template_part( 'template-parts/related-posts', '' );
	}
	?>

</div>
<?php
get_sidebar();
get_footer();

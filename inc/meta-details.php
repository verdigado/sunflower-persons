<?php
/**
 * Meta box for offices and employees in sunflower_person post type.
 *
 * @package sunflower-persons
 */

/**
 * Render a wide React-based meta box for sunflower_person post type.
 */
function sunflower_persons_add_metabox_details() {
	add_meta_box(
		'person_details',
		__( 'Details of person', 'sunflower-persons' ),
		'sunflower_persons_render_metabox_details',
		'sunflower_person',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'sunflower_persons_add_metabox_details', 30 );

/**
 * Output container div for the React app.
 *
 * @param WP_Post $post The current post object.
 */
function sunflower_persons_render_metabox_details( $post ) {
	?>
	<div id="sunflower-persons-metabox-details"
		data-post-id="<?php echo esc_attr( $post->ID ); ?>">
		<!-- React app will render here -->
	</div>
	<?php
}

/**
 * Enqueue block editor assets.
 */
function sunflower_persons_enqueue_details_editor_assets() {
	if ( 'sunflower_person' !== get_post_type() ) {
		return;
	}

	wp_enqueue_script(
		'sunflower-persons-metabox-details',
		SUNFLOWER_PERSONS_URL . 'build/editor-details/plugin.js',
		array( 'wp-element', 'wp-components', 'wp-data', 'wp-core-data', 'wp-i18n', 'wp-api-fetch' ),
		SUNFLOWER_PERSONS_VERSION,
		true
	);
	wp_localize_script(
		'sunflower-persons-metabox-details',
		'sunflowerPersonDetails',
		array(
			'text' => array(
				'sortname'    => esc_html__( 'Sortname', 'sunflower-persons' ),
				'phone'       => esc_html__( 'Phone', 'sunflower-persons' ),
				'mobilephone' => esc_html__( 'Mobile phone', 'sunflower-persons' ),
				'email'       => esc_html__( 'E-Mail', 'sunflower-persons' ),
				'website'     => esc_html__( 'Website', 'sunflower-persons' ),
				'socialmedia' => esc_html__( 'Social Media', 'sunflower-persons' ),
				'photoid'     => esc_html__( 'Profile picture', 'sunflower-persons' ),
				'nophoto'     => esc_html__( 'No picture selected', 'sunflower-persons' ),
				'photochange' => esc_html__( 'Change image', 'sunflower-persons' ),
				'photoadd'    => esc_html__( 'Select or upload image', 'sunflower-persons' ),
				'photoremove' => esc_html__( 'Remove image', 'sunflower-persons' ),
			),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'sunflower_persons_enqueue_details_editor_assets' );


/**
 * Register the person_offices meta field for sunflower_person post type.
 */
function sunflower_persons_register_details_meta() {
	register_post_meta(
		'sunflower_person',
		'person_sortname',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	register_post_meta(
		'sunflower_person',
		'person_phone',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	register_post_meta(
		'sunflower_person',
		'person_mobilephone',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	register_post_meta(
		'sunflower_person',
		'person_email',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	register_post_meta(
		'sunflower_person',
		'person_website',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	register_post_meta(
		'sunflower_person',
		'person_socialmedia',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	register_post_meta(
		'sunflower_person',
		'person_photo_id',
		array(
			'type'              => 'number',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
}
add_action( 'rest_api_init', 'sunflower_persons_register_details_meta' );

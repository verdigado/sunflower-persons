<?php
/**
 * Meta box for offices and employees in sunflower_person post type.
 *
 * @package sunflower-persons
 */

/**
 * Render a wide React-based meta box for sunflower_person post type.
 */
function sunflower_persons_add_metabox_offices() {
	add_meta_box(
		'person_offices',
		__( 'Offices and employees', 'sunflower-persons' ),
		'sunflower_persons_render_metabox_offices',
		'sunflower_person',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'sunflower_persons_add_metabox_offices', 30 );

/**
 * Output container div for the React app.
 *
 * @param WP_Post $post The current post object.
 */
function sunflower_persons_render_metabox_offices( $post ) {
	?>
	<div id="sunflower-persons-metabox-offices"
		data-post-id="<?php echo esc_attr( $post->ID ); ?>">
		<!-- React app will render here -->
	</div>
	<?php
}

/**
 * Enqueue block editor assets.
 */
function sunflower_persons_enqueue_office_editor_assets() {
	if ( 'sunflower_person' !== get_post_type() ) {
		return;
	}

	wp_enqueue_script(
		'sunflower-persons-metabox-offices',
		SUNFLOWER_PERSONS_URL . 'build/editor-offices/plugin.js',
		array( 'wp-element', 'wp-components', 'wp-data', 'wp-core-data', 'wp-i18n', 'wp-api-fetch' ),
		SUNFLOWER_PERSONS_VERSION,
		true
	);
	wp_localize_script(
		'sunflower-persons-metabox-offices',
		'sunflowerPersonOffices',
		array(
			'text' => array(
				'office' => array(
					'label'             => esc_html__( 'Label', 'sunflower-persons' ),
					'streethousenumber' => esc_html__( 'Street and housenumber', 'sunflower-persons' ),
					'ziplocation'       => esc_html__( 'ZIP and location', 'sunflower-persons' ),
					'phone'             => esc_html__( 'Phone', 'sunflower-persons' ),
					'email'             => esc_html__( 'E-Mail', 'sunflower-persons' ),
					'employees'         => esc_html__( 'Employees', 'sunflower-persons' ),
					'removeoffice'      => esc_html__( 'Remove office', 'sunflower-persons' ),
					'addoffice'         => esc_html__( 'Add office', 'sunflower-persons' ),
				),
			),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'sunflower_persons_enqueue_office_editor_assets' );


/**
 * Register the person_offices meta field for sunflower_person post type.
 */
function sunflower_persons_register_office_meta() {
	register_post_meta(
		'sunflower_person',
		'person_offices',
		array(
			'type'              => 'object',
			'single'            => true,
			'show_in_rest'      => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'label'     => array(
								'type'   => 'string',
								'format' => 'text-field',
							),
							'street'    => array(
								'type'   => 'string',
								'format' => 'text-field',
							),
							'city'      => array(
								'type'   => 'string',
								'format' => 'text-field',
							),
							'phone'     => array(
								'type'   => 'string',
								'format' => 'text-field',
							),
							'email'     => array(
								'type'   => 'string',
								'format' => 'text-field',
							),
							'employees' => array(
								'type'  => 'array',
								'items' => array( 'type' => 'integer' ),
							),
						),
					),
				),
			),
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
			'sanitize_callback' => function ( $value ) {
				// Sorgt dafür, dass WP das Array immer als Array akzeptiert.
				return is_array( $value ) ? $value : array();
			},
		)
	);
}
add_action( 'rest_api_init', 'sunflower_persons_register_office_meta' );

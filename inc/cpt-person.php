<?php
/**
 * Sunflower Persons Custom Post Type
 *
 * @package sunflower-persons
 */

/**
 * Meta box for offices and employees in sunflower_person post type.
 */
require_once SUNFLOWER_PERSONS_PATH . 'inc/meta-offices.php';
require_once SUNFLOWER_PERSONS_PATH . 'inc/meta-details.php';

/**
 * Create custom post type person
 */
function sunflower_register_cpt_person() {
	$labels = array(
		'name'               => __( 'Persons', 'sunflower-persons' ),
		'singular_name'      => __( 'Person', 'sunflower-persons' ),
		'add_new_item'       => __( 'Add new person', 'sunflower-persons' ),
		'edit_item'          => __( 'Edit person', 'sunflower-persons' ),
		'new_item'           => __( 'New person', 'sunflower-persons' ),
		'all_items'          => __( 'All persons', 'sunflower-persons' ),
		'view_item'          => __( 'Show person', 'sunflower-persons' ),
		'search_items'       => __( 'Search person', 'sunflower-persons' ),
		'not_found'          => __( 'No person found', 'sunflower-persons' ),
		'not_found_in_trash' => __( 'No person found in trash', 'sunflower-persons' ),
		'menu_name'          => __( 'Persons', 'sunflower-persons' ),
	);

	register_post_type(
		'sunflower_person',
		array(
			'labels'       => $labels,
			'public'       => true,
			'has_archive'  => true,
			'show_in_rest' => true,
			'rest_base'    => 'sunflower_person',
			'hierarchical' => false,
			'menu_icon'    => 'dashicons-groups',
			'supports'     => array( 'title', 'editor', 'revisions', 'custom-fields', 'thumbnail', 'excerpt' ),
			'taxonomies'   => array( 'post_tag' ),
			'rewrite'      => array( 'slug' => 'person' ),
		)
	);
}
add_action( 'init', 'sunflower_register_cpt_person' );

/**
 * Register custom taxonomy "group" for persons
 */
function sunflower_persons_register_group_taxonomy() {

	$labels = array(
		'name'               => __( 'Groups', 'sunflower-persons' ),
		'singular_name'      => __( 'Group', 'sunflower-persons' ),
		'add_new_item'       => __( 'Add new group', 'sunflower-persons' ),
		'edit_item'          => __( 'Edit group', 'sunflower-persons' ),
		'new_item'           => __( 'New group', 'sunflower-persons' ),
		'all_items'          => __( 'All groups', 'sunflower-persons' ),
		'view_item'          => __( 'Show group', 'sunflower-persons' ),
		'search_items'       => __( 'Search group', 'sunflower-persons' ),
		'parent_item'        => __( 'Parent group', 'sunflower-persons' ),
		'parent_item_colon'  => __( 'Parent group:', 'sunflower-persons' ),
		'not_found'          => __( 'No group found', 'sunflower-persons' ),
		'not_found_in_trash' => __( 'No group found in trash', 'sunflower-persons' ),
		'menu_name'          => __( 'Groups', 'sunflower-persons' ),
	);

	register_taxonomy(
		'sunflower_group',
		'sunflower_person',
		array(
			'hierarchical' => true,
			'labels'       => $labels,
			'rewrite'      => array( 'slug' => 'group' ),
			'show_in_rest' => true,
		)
	);
}
add_action( 'init', 'sunflower_persons_register_group_taxonomy' );

/**
 * Register meta field to connect persons to posts.
 */
function sunflower_persons_register_post_persons_meta() {
	register_post_meta(
		'post',
		'sunflower_connected_persons',
		array(
			'type'          => 'array',
			'single'        => true,
			'show_in_rest'  => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'integer',
					),
				),
			),
			'default'       => array(),
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'sunflower_persons_register_post_persons_meta' );

/**
 * Enqueue block editor assets.
 */
function sunflower_persons_enqueue_editor_assets() {
	if ( 'post' === get_post_type() ) {
		$asset_data = include SUNFLOWER_PERSONS_PATH . 'build/editor-plugin/plugin.asset.php';
		wp_enqueue_script(
			'sunflower-persons-block-editor',
			SUNFLOWER_PERSONS_URL . 'build/editor-plugin/plugin.js',
			$asset_data['dependencies'],
			$asset_data['version'],
			true
		);
	}
}
add_action( 'enqueue_block_editor_assets', 'sunflower_persons_enqueue_editor_assets' );

// REST API: Hide person single view when "hide single page" meta is set.
add_filter(
	'rest_request_before_callbacks',
	function ( $response, $handler, $request ) {

		$route = $request->get_route();

		// Return early if not a sunflower_person request.
		if ( ! preg_match( '#^/wp/v2/sunflower_person/(\d+)$#', $route, $m ) ) {
			return $response;
		}

		$post_id = intval( $m[1] );

		// Only proceed for GET requests.
		if ( $request->get_method() !== 'GET' ) {
			return $response;
		}

		// Gutenberg Editor Preview must always have access.
		if ( 'edit' === $request['context'] ) {
			return $response;
		}

		// Check if the person is marked as hidden.
		$hidden = get_post_meta( $post_id, 'person_hide_single', true );

		if ( $hidden ) {
			return new WP_Error(
				'rest_post_hidden',
				__( 'Dieser Eintrag ist nicht öffentlich verfügbar.', 'sunflower' ),
				array( 'status' => 404 )
			);
		}

		return $response;
	},
	10,
	3
);

// Disable single view for persons with "hide single page" meta set.
add_action(
	'template_redirect',
	function () {
		if ( is_singular( 'sunflower_person' ) ) {
			$id   = get_queried_object_id();
			$hide = get_post_meta( $id, 'person_hide_single', true );

			if ( $hide ) {
				// 404 statt Einzelansicht anzeigen
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				return;
			}
		}
	}
);

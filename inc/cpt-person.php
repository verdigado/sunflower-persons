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
	$da = get_post_type();
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

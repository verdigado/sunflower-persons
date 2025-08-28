<?php
/**
 * Registers the Custom Post Type: person
 *
 * @package sunflower-persons
 */

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
		'person',
		array(
			'labels'       => $labels,
			'public'       => true,
			'has_archive'  => true,
			'show_in_rest' => true,
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
		'group',
		'person',
		array(
			'hierarchical' => true,
			'labels'       => $labels,
			'rewrite'      => array( 'slug' => 'group' ),
			'show_in_rest' => true,
		)
	);
}
add_action( 'init', 'sunflower_persons_register_group_taxonomy' );

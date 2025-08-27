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
		'name'               => __( 'Personen', 'sunflower-persons' ),
		'singular_name'      => __( 'Person', 'sunflower-persons' ),
		'add_new'            => __( 'Neu hinzufügen', 'sunflower-persons' ),
		'add_new_item'       => __( 'Neue Person hinzufügen', 'sunflower-persons' ),
		'edit_item'          => __( 'Person bearbeiten', 'sunflower-persons' ),
		'new_item'           => __( 'Neue Person', 'sunflower-persons' ),
		'all_items'          => __( 'Alle Personen', 'sunflower-persons' ),
		'view_item'          => __( 'Person anzeigen', 'sunflower-persons' ),
		'search_items'       => __( 'Person suchen', 'sunflower-persons' ),
		'not_found'          => __( 'Keine Personen gefunden', 'sunflower-persons' ),
		'not_found_in_trash' => __( 'Keine Personen im Papierkorb', 'sunflower-persons' ),
		'menu_name'          => __( 'Personen', 'sunflower-persons' ),
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

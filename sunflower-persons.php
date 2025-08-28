<?php
/**
 * Plugin Name: Sunflower Persons
 * Description: Custom Post Type "Person" + Gutenberg Block zur Ausgabe einer einzelnen Person oder einer Liste von Personen.
 * Version: 1.0.0
 * Author: Dein Name
 * Text Domain: sunflower-persons
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package Sunflower Persons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SUNFLOWER_PERSONS_PATH', plugin_dir_path( __FILE__ ) );
define( 'SUNFLOWER_PERSONS_URL', plugin_dir_url( __FILE__ ) );

require_once SUNFLOWER_PERSONS_PATH . 'inc/cpt-person.php';


add_action( 'init', 'sunflower_persons_blocks_init' );

/**
 * Register map block and all required assets.
 */
function sunflower_persons_blocks_init() {
	register_block_type( __DIR__ . '/build/person' );

	// Load translation file.
	wp_set_script_translations(
		'sunflower-persons-person-editor-script',
		'sunflower-persons-person',
		plugin_dir_path( __FILE__ ) . 'languages'
	);
}

/**
 * Add the block language files.
 */
function sunflower_map_points_blocks_load_textdomain() {
	load_plugin_textdomain( 'sunflower-persons', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	load_plugin_textdomain( 'sunflower-persons-person', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'after_setup_theme', 'sunflower_map_points_blocks_load_textdomain' );

/**
 * Flush rewrite rules on activation/deactivation (for CPT permalinks).
 */
register_activation_hook(
	__FILE__,
	function () {
		// Ensure CPT is registered before flushing.
		sunflower_register_cpt_person();
		flush_rewrite_rules();
	}
);

register_deactivation_hook(
	__FILE__,
	function () {
		flush_rewrite_rules();
	}
);

/**
 * Query all persons and return as list.
 *
 * @return WP_Query List of persons.
 */
function sunflower_persons_get_all_persons() {
	return new WP_Query(
		array(
			'post_type'      => 'person',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
}

/**
 * Query all groups of a person.
 *
 * @param WP_Post $sunflower_persons_post Person post object.
 * @return string HTML markup for all groups of a person.
 */
function sunflower_persons_get_all_person_groups( $sunflower_persons_post ) {

	$groups                        = get_the_terms( $sunflower_persons_post->ID, 'group' );
	$sunflower_person_group_string = '';
	if ( $groups && ! is_wp_error( $groups ) ) {
		$sunflower_person_group_string = '<div class="sunflower-person__groups">';
		foreach ( $groups as $group ) {
			$sunflower_person_group_string .= '<span class="sunflower-person__group">' . esc_html( $group->name ) . '</span> ';
		}
		$sunflower_person_group_string .= '</div>';
	}
	return $sunflower_person_group_string;
}

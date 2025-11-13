<?php
/**
 * Plugin Name: Sunflower Persons
 * Description: Custom Post Type 'Person' + Gutenberg Block zur Ausgabe einer einzelnen Person oder einer Liste von Personen.
 * Version: 1.2.0
 * Author: verdigado eG
 * Author URI: https://verdigado.com
 * Text Domain: sunflower-persons
 * Requires at least: 6.8
 * Requires PHP: 8.2
 * Plugin URI: https://github.com/verdigado/sunflower-persons
 * Update URI: https://sunflower-theme.de/updateserver/sunflower-persons/
 *
 * @package Sunflower Persons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SUNFLOWER_PERSONS_PATH', plugin_dir_path( __FILE__ ) );
define( 'SUNFLOWER_PERSONS_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'SUNFLOWER_PERSONS_VERSION' ) ) {
	$sunflower_persons_plugin_data    = get_plugin_data( __FILE__, false, false );
	$sunflower_persons_plugin_version = $sunflower_persons_plugin_data['Version'];
	define( 'SUNFLOWER_PERSONS_VERSION', $sunflower_persons_plugin_version );
}

require_once SUNFLOWER_PERSONS_PATH . 'inc/cpt-person.php';

add_action( 'init', 'sunflower_persons_blocks_init' );

/**
 * Register person block and all required assets.
 */
function sunflower_persons_blocks_init() {
	register_block_type( __DIR__ . '/build/person' );

	// Load translation file.
	wp_set_script_translations(
		'sunflower-persons-person-editor-script',
		'sunflower-persons-person',
		SUNFLOWER_PERSONS_PATH . 'languages'
	);
}

/**
 * Add the block language files.
 */
function sunflower_persons_points_blocks_load_textdomain() {
	load_plugin_textdomain( 'sunflower-persons', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	load_plugin_textdomain( 'sunflower-persons-person', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'after_setup_theme', 'sunflower_persons_points_blocks_load_textdomain' );

/**
* Flush rewrite rules on activation/deactivation ( for CPT permalinks ).
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
 * @param array $group_ids Optional array of group IDs or slugs to filter persons by group.
 * @param array $tag_ids Optional array of tag IDs or slugs to filter persons by tags.
 * @param array $display_options Optional filter array with 'limit' and 'order' keys.
 * @return WP_Query List of persons.
 */
function sunflower_persons_get_all_persons( $group_ids = array(), $tag_ids = array(), $display_options = array(
	'limit' => -1,
	'order' => 'asc',
) ) {
	$tax_query = array();

	if ( $group_ids ) {
		array_push(
			$tax_query,
			array(
				'taxonomy' => 'sunflower_group',
				'field'    => 'slug',
				'terms'    => $group_ids,
			)
		);
	}

	if ( ! empty( $tag_ids ) ) {
		array_push(
			$tax_query,
			array(
				'taxonomy' => 'post_tag',
				'field'    => 'slug',
				'terms'    => $tag_ids,
			)
		);
	}

	$args = array(
		'post_type'      => 'sunflower_person',
		'posts_per_page' => $display_options['limit'] ?? -1,
		'tax_query'      => $tax_query,
		'meta_key'       => 'person_sortname',
		'order'          => strtoupper( $display_options['order'] ?? 'asc' ),
		'orderby'        => ( ( ( $display_options['order'] ?? 'none' ) === 'random' ) ? 'rand' : 'meta_value title' ),
		'no_found_rows'  => true,
	);

	return new WP_Query( $args );
}

/**
 * Query all groups of a person.
 *
 * @param WP_Post $sunflower_persons_post Person post object.
 * @return string HTML markup for all groups of a person.
 */
function sunflower_persons_get_all_person_groups( $sunflower_persons_post ) {

	$groups                        = get_the_terms( $sunflower_persons_post->ID, 'sunflower_group' );
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

/**
 * Get the linked social media icons.
 *
 * @param int $post_id The post ID.
 * @return array Array of social media profile links.
 */
function sunflower_persons_get_social_media_profiles( $post_id ) {

	$return = array();
	$lines  = explode( "\n", (string) get_post_meta( $post_id, 'person_socialmedia', true ) );

	foreach ( $lines as $line ) {
		$line         = trim( $line );
		$some_profile = explode( ';', $line );
		$class        = $some_profile[0] ?? false;
		$title        = $some_profile[1] ?? false;
		$url          = $some_profile[2] ?? false;

		if ( false === $url || empty( $url ) ) {
			continue;
		}

		$return[] = sprintf(
			'<a href="%1$s" target="_blank" title="%3$s" class="social-media-profiles" rel="me"><i class="%2$s"></i></a>',
			$url,
			$class,
			$title
		);
	}

	return $return;
}

/**
 * Send update request to update server specified in $plugin_data[ 'UpdateURI' ].
 *
 * @param Array  $update The plugin update data with the latest details. Default false.
 * @param Array  $plugin_data Theme data array.
 * @param string $plugin_file The plugin slug - 'sunflower-persons' our case.
 */
function sunflower_persons_update_plugin( $update, $plugin_data, $plugin_file ) {
	if ( plugin_basename( __FILE__ ) !== $plugin_file ) {
		return $update;
	}
	// Include an unmodified $wp_version.
	require ABSPATH . WPINC . '/version.php';
	$php_version = PHP_VERSION;

	$request = array(
		'version' => $plugin_data['Version'],
		'php'     => $php_version,
		'url'     => get_bloginfo( 'url' ),
	);

	// Start checking for an update.
	$send_for_check = array(
		'body' => array(
            'request' => serialize( $request ), // phpcs:ignore
		),
	);
	$raw_response   = wp_remote_post( $plugin_data['UpdateURI'], $send_for_check );

	$data = false;
	if ( ! is_wp_error( $raw_response ) && ( 200 === $raw_response['response']['code'] ) ) {
		$data = json_decode( wp_remote_retrieve_body( $raw_response ) );
	} else {
		return $update;
	}

	if ( ! $data || version_compare( $plugin_data['Version'], $data->new_version, '>=' ) ) {
		return $update;
	}

	// Update object in the right WordPress format.
	$update = (object) array(
		'slug'    => $data->slug,
		'plugin'  => $plugin_file,
		'version' => $data->new_version,
		'url'     => $plugin_data['PluginURI'],
		'package' => $data->package,
	);

	return $update;
}

add_filter( 'update_plugins_sunflower-theme.de', 'sunflower_persons_update_plugin', 10, 3 );

/**
 * Use custom single template for sunflower_person post type.
 *
 * @param string $template The path of the template to include.
 * @return string The path of the template to include.
 */
function sunflower_person_single_template( $template ) {
	if ( is_singular( 'sunflower_person' ) ) {
		// First check if theme has a template part for sunflower_person.
		$theme_part = locate_template( 'template-parts/content-sunflower_person.php' );
		if ( $theme_part ) {
			return $template;
		}
		// If not, check if theme has a single-sunflower_person.php template.
		$plugin_template = plugin_dir_path( __FILE__ ) . 'templates/single-sunflower_person.php';
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}
	}
	return $template;
}
add_filter( 'single_template', 'sunflower_person_single_template' );


/**
 * Enqueue frontend assets.
 */
function sunflower_persons_enqueue_frontend_assets() {
	wp_enqueue_style(
		'sunflower-persons-frontend-style',
		SUNFLOWER_PERSONS_URL . 'assets/css/sunflower-persons.css',
		array(),
		SUNFLOWER_PERSONS_VERSION
	);
}

add_action( 'wp_enqueue_scripts', 'sunflower_persons_enqueue_frontend_assets' );

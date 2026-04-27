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
 * Exclude the "group" taxonomy from the default WordPress XML sitemaps.
 *
 * @param array $taxonomies The existing taxonomies included in the sitemap.
 * @return array The modified taxonomies with "group" excluded.
 */
function sunflower_persons_sitemap_taxonomies( $taxonomies ) {
	unset( $taxonomies['sunflower_group'] );
	return $taxonomies;
}

add_filter( 'wp_sitemaps_taxonomies', 'sunflower_persons_sitemap_taxonomies' );

/**
 * Exclude the "group" taxonomy from the Yoast SEO XML sitemaps.
 *
 * @param boolean $excluded Whether the taxonomy is excluded by default.
 * @param string  $taxonomy The taxonomy to exclude.
 *
 * @return bool Whether a given taxonomy should be excluded.
 */
function sunflower_persons_wpseo_sitemap_exclude_taxonomy( $excluded, $taxonomy ) {
	return 'sunflower_group' === $taxonomy;
}

add_filter( 'wpseo_sitemap_exclude_taxonomy', 'sunflower_persons_wpseo_sitemap_exclude_taxonomy', 10, 2 );

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

// Show related persons in quick edit. See: https://developer.wordpress.org/reference/hooks/quick_edit_custom_box/.
add_filter( 'manage_post_posts_columns', 'sunflower_add_persons_column' );

/**
 * Add a column to the post list table to show related persons and enable quick edit for it.
 *
 * @param array $columns The existing columns.
 * @return array The modified columns.
 */
function sunflower_add_persons_column( $columns ) {
	$columns['sunflower-persons-related-persons'] = __( 'Related Persons', 'sunflower-persons' );
	return $columns;
}

add_action( 'manage_post_posts_custom_column', 'sunflower_persons_column_content', 10, 2 );

/**
 * Show related persons in the custom column and add a data attribute with the person ids for quick edit.
 * The JavaScript reads the data-person-ids attribute to check the checkboxes in quick edit.
 *
 * @param string $column The column name.
 * @param int    $post_id The current post ID.
 */
function sunflower_persons_column_content( $column, $post_id ) {
	if ( 'sunflower-persons-related-persons' !== $column ) {
		return;
	}

	$person_ids = get_post_meta( $post_id, 'sunflower_connected_persons', true );
	$ids_string = '';
	$names      = array();

	if ( ! empty( $person_ids ) && is_array( $person_ids ) ) {
		$ids_string = implode( ',', (array) $person_ids );
		$names      = array_map(
			function ( $id ) {
				return get_the_title( $id );
			},
			(array) $person_ids
		);
	}

	// data-person-ids es read by JavaScript.
	printf(
		'<span data-person-ids="%s">%s</span>',
		esc_attr( $ids_string ),
		esc_html( implode( ', ', $names ) )
	);
}

/**
 * Render person checkboxes – used by Quick Edit AND Bulk Edit.
 */
function sunflower_render_persons_field() {
	$persons = get_posts(
		array(
			'post_type'      => 'sunflower_person',
			'posts_per_page' => -1,
			'orderby'        => 'meta_value',
			'meta_key'       => 'person_sortname',
			'order'          => 'ASC',
		)
	);

	wp_nonce_field( 'sunflower_quick_edit', 'sunflower_quick_edit_nonce' );
	?>
	<fieldset class="inline-edit-col-right">
		<div class="inline-edit-col">
			<span class="title inline-edit-categories-label">
				<?php echo esc_html__( 'Related Persons', 'sunflower-persons' ); ?>
			</span>
			<ul class="cat-checklist category-checklist sunflower-persons-checklist">
				<?php foreach ( $persons as $person ) : ?>
					<li>
						<label class="selectit">
							<input value="<?php echo esc_attr( $person->ID ); ?>"
									type="checkbox"
									name="sunflower_connected_persons[]">
							<?php echo esc_html( $person->post_title ); ?>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</fieldset>
	<?php
}

// Register for quick edit.
add_action( 'quick_edit_custom_box', 'sunflower_quick_edit_persons', 10, 2 );

/**
 * Render person checkboxes for quick edit.
 *
 * @param string $column_name The column name.
 * @param string $post_type The post type.
 */
function sunflower_quick_edit_persons( $column_name, $post_type ) {
	if ( 'sunflower-persons-related-persons' !== $column_name || 'post' !== $post_type ) {
		return;
	}

	$persons = get_posts(
		array(
			'post_type'      => 'sunflower_person',
			'posts_per_page' => -1,
			'orderby'        => 'meta_value',
			'meta_key'       => 'person_sortname',
			'order'          => 'ASC',
		)
	);

	wp_nonce_field( 'sunflower_quick_edit', 'sunflower_quick_edit_nonce' );
	?>
	<fieldset class="inline-edit-col-right">
		<div class="inline-edit-col">
			<span class="title inline-edit-categories-label"><?php echo esc_attr__( 'Related Persons', 'sunflower-persons' ); ?></span>
			<ul class="cat-checklist category-checklist">
				<?php foreach ( $persons as $person ) : ?>
					<li>
					<label class="selectit">
						<input type="checkbox"
								name="sunflower_connected_persons[]"
								value="<?php echo esc_attr( $person->ID ); ?>">
						<?php echo esc_html( $person->post_title ); ?>
					</label>
				</li>
				<?php endforeach; ?>
				</ul>
		</div>
	</fieldset>
	<?php
}

// Register for bulk edit (actually the same as for quick edit).
add_action( 'bulk_edit_custom_box', 'sunflower_bulk_edit_persons', 10, 2 );

/**
 * Render person checkboxes for bulk edit.
 *
 * @param string $column_name The column name.
 * @param string $post_type The post type.
 */
function sunflower_bulk_edit_persons( $column_name, $post_type ) {
	if ( 'sunflower-persons-related-persons' !== $column_name || 'post' !== $post_type ) {
		return;
	}
	sunflower_render_persons_field();
}

/**
 * Load style for indeterminate state in tri-state checkboxes.
 */
function sunflower_bulk_edit_styles() {
	wp_register_style(
		'sunflower-persons-admin',
		SUNFLOWER_PERSONS_URL . 'assets/css/admin.css',
		array(),
		SUNFLOWER_PERSONS_VERSION
	);
	wp_enqueue_style( 'sunflower-persons-admin' );
	wp_enqueue_script(
		'sunflower-quick-edit',
		plugin_dir_url( __FILE__ ) . '../assets/js/quick-edit.js',
		array( 'jquery', 'inline-edit-post' ),
		SUNFLOWER_PERSONS_VERSION,
		true
	);
}

add_action( 'admin_enqueue_scripts', 'sunflower_bulk_edit_styles' );

/**
 * Enqueue JavaScript for quick edit functionality.
 *
 * @param string $hook The current admin page hook.
 */
function sunflower_quick_edit_script( $hook ) {
	if ( 'edit.php' !== $hook ) {
		return;
	}

	wp_enqueue_script(
		'sunflower-quick-edit',
		plugin_dir_url( __FILE__ ) . '../assets/js/quick-edit.js',
		array( 'jquery', 'inline-edit-post' ),
		'1.1.1',
		true
	);
}

add_action( 'save_post', 'sunflower_save_quick_edit_persons', 10, 2 );

/**
 * Save the related persons when a post is saved via quick edit.
 *
 * @param int $post_id The ID of the post being saved.
 */
function sunflower_save_quick_edit_persons( $post_id ) {

	if ( ! isset( $_POST['sunflower_quick_edit_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['sunflower_quick_edit_nonce'], 'sunflower_quick_edit' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['sunflower_connected_persons'] ) ) {
		$person_ids = array_map( 'intval', $_POST['sunflower_connected_persons'] );
		update_post_meta( $post_id, 'sunflower_connected_persons', $person_ids );
	} else {
		delete_post_meta( $post_id, 'sunflower_connected_persons' );
	}
}

add_action( 'wp_ajax_sunflower_bulk_edit_persons', 'sunflower_process_bulk_edit' );

/**
 * Process the bulk edit AJAX request to update related persons for multiple posts.
 */
function sunflower_process_bulk_edit() {
	check_ajax_referer( 'sunflower_quick_edit', 'sunflower_quick_edit_nonce' );

	$post_ids       = array_map( 'intval', $_POST['post_ids'] ?? array() );
	$add_persons    = array_map( 'intval', $_POST['add_persons'] ?? array() );
	$remove_persons = array_map( 'intval', $_POST['remove_persons'] ?? array() );

	if ( empty( $post_ids ) || ( empty( $add_persons ) && empty( $remove_persons ) ) ) {
		wp_send_json_success();
		return;
	}

	foreach ( $post_ids as $post_id ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			continue;
		}

		$existing = get_post_meta( $post_id, 'sunflower_connected_persons', true );
		$existing = is_array( $existing ) ? $existing : array();

		$updated = array_unique( array_merge( $existing, $add_persons ) );
		$updated = array_diff( $updated, $remove_persons );

		if ( ! empty( $updated ) ) {
			update_post_meta( $post_id, 'sunflower_connected_persons', array_values( $updated ) );
		} else {
			delete_post_meta( $post_id, 'sunflower_connected_persons' );
		}
	}

	wp_send_json_success( array( 'updated' => count( $post_ids ) ) );
}

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


/**
 * Show related persons on a single post.
 *
 * @param WP_Post $post The current post object.
 */
function sunflower_persons_post_related_persons_hook( $post ) {

	if ( 'post' !== $post->post_type ) {
		return;
	}

	require SUNFLOWER_PERSONS_PATH . 'template-parts/related-persons-posts.php';
}

/**
 * Show related persons in single post view. Using the "sunflower/content/before-footer" hook provided by the Sunflower theme.
 */
add_action( 'sunflower_content_before_footer', 'sunflower_persons_post_related_persons_hook' );



add_action( 'init', 'sunflower_person_pagination_rewrite' );

/**
 * Add rewrite rules to handle pagination for person single view, e.g. /person/john-doe/page/2.
 */
function sunflower_person_pagination_rewrite() {
	add_rewrite_rule(
		'person/([^/]+)/page/?([0-9]{1,})/?$',
		'index.php?sunflower_person=$matches[1]&paged=$matches[2]',
		'top'
	);
}

add_filter( 'redirect_canonical', 'sunflower_person_disable_redirect', 10, 2 );

/**
 * Disable canonical redirect for paginated person single view, e.g. /person/john-doe/page/2, to prevent redirecting to the first page.
 *
 * @param string $redirect_url The URL to redirect to.
 * @return string|false The URL to redirect to, or false to disable the redirect.
 */
function sunflower_person_disable_redirect( $redirect_url ) {
	if ( is_singular( 'sunflower_person' ) && get_query_var( 'paged' ) > 0 ) {
		return false; // Disable canonical redirect for paginated person pages.
	}
	return $redirect_url;
}

add_filter( 'query_vars', 'sunflower_person_query_vars' );

/**
 * Add 'paged' to the list of recognized query variables so that it can be used in the rewrite rules and template redirection for paginated person single views.
 *
 * @param array $vars The existing query variables.
 * @return array The modified query variables.
 */
function sunflower_person_query_vars( $vars ) {
	$vars[] = 'paged';
	return $vars;
}

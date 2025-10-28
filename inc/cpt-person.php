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
		'sunflower_person',
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

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'sunflower_persons_contact',
			__( 'Contact details', 'sunflower-persons' ),
			'sunflower_persons_render_contact_meta',
			'sunflower_person',
			'normal',
			'default'
		);
	}
);

/**
 * Render the contact meta box.
 *
 * @param WP_Post $post The post object.
 */
function sunflower_persons_render_contact_meta( $post ) {
	$sortname    = get_post_meta( $post->ID, 'person_sortname', true );
	$phone       = get_post_meta( $post->ID, 'person_phone', true );
	$email       = get_post_meta( $post->ID, 'person_email', true );
	$website     = get_post_meta( $post->ID, 'person_website', true );
	$socialmedia = get_post_meta( $post->ID, 'person_socialmedia', true );
	?>
	<table class="form-table"><tbody>
	<tr>
		<th scope="row">
			<label for="person_sortname"><?php esc_html_e( 'Sortname', 'sunflower-persons' ); ?></label>
		</th>
		<td><input type="text" name="person_sortname" id="person_sortname" value="<?php echo esc_attr( $sortname ); ?>" />
			<br><span class="description"><?php esc_html_e( 'Sorting name. e.g. last-name', 'sunflower-persons' ); ?></span>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="person_phone"><?php esc_html_e( 'Phone', 'sunflower-persons' ); ?></label>
		</th>
		<td><input type="tel" name="person_phone" id="person_phone" value="<?php echo esc_attr( $phone ); ?>" />
			<br><span class="description">Format: +49 123 321 31 2</span>
		</td>
		<th scope="row">
			<label for="person_email"><?php esc_html_e( 'Email', 'sunflower-persons' ); ?></label>
		</th>
		<td><input type="email" name="person_email" id="person_email" value="<?php echo esc_attr( $email ); ?>" />
			<br><span class="description"></span>
		</td>
	</tr>
		<th scope="row">
			<label for="person_website"><?php esc_html_e( 'Website', 'sunflower-persons' ); ?></label>
		</th>
		<td><input type="url" name="person_website" id="person_website" value="<?php echo esc_attr( $website ); ?>" />
			<br><span class="description"></span>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="person_socialmedia"><?php esc_html_e( 'Social Media', 'sunflower-persons' ); ?></label>
		</th>
		<td colspan="3">
		<?php
		$default   = array();
		$default[] = 'fab fa-x-twitter;X (Twitter);';
		$default[] = 'fab fa-mastodon;Mastodon;';
		$default[] = 'fab fa-facebook-f;Facebook;';
		$default[] = 'fab fa-instagram;Instagram;';
		$default[] = 'fab fa-whatsapp;WhatsApp;';
		$default[] = 'fab fa-bluesky;Bluesky;';
		$default[] = 'fab fa-threads;Threads;';
		$default[] = 'fab fa-tiktok;TikTok;';
		$default[] = 'fab fa-linkedin;LinkedIn;';
		$default[] = 'fab fa-youtube;YouTube;';
		$default[] = 'fas fa-globe;Webseite;';
		$default[] = 'forkawesome fa-peertube;PeerTube;';
		$default[] = 'forkawesome fa-pixelfed;Pixelfed;';

		printf(
			'<textarea rows="10" style="white-space:pre-wrap;width:100%%" id="person_socialmedia" name="person_socialmedia">%s</textarea>',
			empty( $socialmedia ) ? esc_attr( implode( "\n", $default ) ) : esc_attr( $socialmedia )
		);
		?>
		<br><span class="description">Format: Fontawesome-Klasse; Title-Attribut; URL</span>
		</td>
	<tr>

	</table>
	<?php
}

add_action(
	'save_post_sunflower_person',
	'sunflower_persons_save_post_form'
);

/**
 * Save the form data to custom post type.
 *
 * @param int $post_id The post ID.
 */
function sunflower_persons_save_post_form( $post_id ) {

	// Do not save, if nonce is invalid.
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-post_' . $post_id ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// check user permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['person_sortname'] ) && ! empty( $_POST['person_sortname'] ) ) {
		update_post_meta( $post_id, 'person_sortname', sanitize_text_field( $_POST['person_sortname'] ) );
	} else {
		update_post_meta( $post_id, 'person_sortname', explode( ' ', get_the_title( $post_id ) )[1] ?? get_the_title( $post_id ) );
	}
	if ( isset( $_POST['person_phone'] ) ) {
		update_post_meta( $post_id, 'person_phone', sanitize_text_field( $_POST['person_phone'] ) );
	}
	if ( isset( $_POST['person_website'] ) ) {
		update_post_meta( $post_id, 'person_website', esc_url_raw( $_POST['person_website'] ) );
	}
	if ( isset( $_POST['person_email'] ) ) {
		update_post_meta( $post_id, 'person_email', sanitize_text_field( $_POST['person_email'] ) );
	}
	if ( isset( $_POST['person_socialmedia'] ) ) {
		update_post_meta( $post_id, 'person_socialmedia', sanitize_textarea_field( $_POST['person_socialmedia'] ) );
	}
}

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
	$asset_data = include SUNFLOWER_PERSONS_PATH . 'build/editor-plugin/plugin.asset.php';
	wp_enqueue_script(
		'sunflower-persons-block-editor',
		SUNFLOWER_PERSONS_URL . '/build/editor-plugin/plugin.js',
		$asset_data['dependencies'],
		$asset_data['version'],
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'sunflower_persons_enqueue_editor_assets' );

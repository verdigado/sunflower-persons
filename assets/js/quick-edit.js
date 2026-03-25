/* global jQuery */
/* global inlineEditPost */
/* global MutationObserver */
/* global ajaxurl */
/* global location */
( function ( $ ) {
	/**
	 * Quick Edit: Checkboxen aktivieren
	 */
	const originalInlineEdit = inlineEditPost.edit;

	inlineEditPost.edit = function ( id ) {
		originalInlineEdit.apply( this, arguments );

		if ( typeof id === 'object' ) {
			id = this.getId( id );
		}

		const editRow = $( '#edit-' + id );
		const postRow = $( '#post-' + id );

		editRow
			.find( 'input[name="sunflower_connected_persons[]"]' )
			.prop( 'checked', false );

		const personIds = postRow
			.find( '.column-sunflower-persons-related-persons span' )
			.data( 'person-ids' );

		if ( personIds ) {
			String( personIds )
				.split( ',' )
				.forEach( function ( pid ) {
					editRow
						.find(
							'input[name="sunflower_connected_persons[]"][value="' +
								pid.trim() +
								'"]'
						)
						.prop( 'checked', true );
				} );
		}
	};

	/**
	 * Bulk Edit: Indeterminate setzen für gemischte Zustände
	 */
	function initBulkEditPersons() {
		const checkedPosts = [];
		$( 'tbody th.check-column input[type="checkbox"]:checked' ).each(
			function () {
				checkedPosts.push( $( this ).val() );
			}
		);

		if ( ! checkedPosts.length ) {
			return;
		}

		// Pro Person zählen
		const personCounts = {};
		checkedPosts.forEach( function ( postId ) {
			const personIds = $( '#post-' + postId )
				.find( '.column-sunflower-persons-related-persons span' )
				.data( 'person-ids' );

			if ( personIds ) {
				String( personIds )
					.split( ',' )
					.forEach( function ( pid ) {
						pid = pid.trim();
						personCounts[ pid ] = ( personCounts[ pid ] || 0 ) + 1;
					} );
			}
		} );

		const totalPosts = checkedPosts.length;

		const $bulkRow = $( '#bulk-edit' );

		$bulkRow
			.find( 'input[name="sunflower_connected_persons[]"]' )
			.each( function () {
				const $cb = $( this );
				const count = personCounts[ $cb.val() ] || 0;

				if ( count === totalPosts ) {
					$cb.prop( 'checked', true ).prop( 'indeterminate', false );
				} else if ( count === 0 ) {
					$cb.prop( 'checked', false ).prop( 'indeterminate', false );
				} else {
					// ▣ Gemischt: Indeterminate + als "nicht angefasst" markieren
					$cb.prop( 'checked', false ).prop( 'indeterminate', true );
					$cb.data( 'was-indeterminate', true );
				}

				// Merken: wurde diese Checkbox vom User angeklickt?
				$cb.data( 'changed', false );
			} );

		// Ein Klick auf indeterminate → wird normale Checkbox
		$bulkRow
			.find( 'input[name="sunflower_connected_persons[]"]' )
			.off( 'change.bulk' )
			.on( 'change.bulk', function () {
				$( this )
					.prop( 'indeterminate', false )
					.data( 'changed', true );
			} );
	}

	/**
	 * Bulk Edit öffnen erkennen
	 */
	const observer = new MutationObserver( function () {
		if ( $( '#bulk-edit' ).is( ':visible' ) ) {
			initBulkEditPersons();
		}
	} );

	const bulkRow = document.getElementById( 'bulk-edit' );
	if ( bulkRow ) {
		observer.observe( bulkRow, {
			attributes: true,
			attributeFilter: [ 'class', 'style' ],
		} );
	}

	$( document ).on( 'click', '#doaction, #doaction2', function () {
		setTimeout( initBulkEditPersons, 200 );
	} );

	/**
	 * Bulk Edit: Speichern – nur geänderte Checkboxen
	 */
	$( document ).on( 'click', '#bulk_edit', function () {
		const postIds = [];
		$( 'tbody th.check-column input[type="checkbox"]:checked' ).each(
			function () {
				postIds.push( $( this ).val() );
			}
		);

		if ( ! postIds.length ) {
			return;
		}

		// Nur Checkboxen sammeln die angeklickt wurden
		const addPersons = [];
		const removePersons = [];
		let hasChanges = false;

		$( '#bulk-edit input[name="sunflower_connected_persons[]"]' ).each(
			function () {
				const $cb = $( this );

				// Nicht angefasst → ignorieren
				if ( ! $cb.data( 'changed' ) ) {
					return;
				}

				hasChanges = true;

				if ( $cb.prop( 'checked' ) ) {
					addPersons.push( $cb.val() );
				} else {
					removePersons.push( $cb.val() );
				}
			}
		);

		if ( ! hasChanges ) {
			return;
		}

		$.ajax( {
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'sunflower_bulk_edit_persons',
				sunflower_quick_edit_nonce: $(
					'#sunflower_quick_edit_nonce'
				).val(),
				post_ids: postIds,
				add_persons: addPersons,
				remove_persons: removePersons,
			},
			success( response ) {
				if ( response.success ) {
					location.reload();
				}
			},
		} );
	} );
} )( jQuery );

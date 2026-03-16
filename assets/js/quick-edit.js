/* global jQuery */
/* global inlineEditPost */
( function ( $ ) {
	const originalInlineEdit = inlineEditPost.edit;

	inlineEditPost.edit = function ( id ) {
		// Original-Funktion aufrufen
		originalInlineEdit.apply( this, arguments );

		// Post-ID ermitteln
		if ( typeof id === 'object' ) {
			id = this.getId( id );
		}

		const postRow = $( '#post-' + id );

		// Bestehende Person-IDs aus der Spalte auslesen
		const personIds = postRow
			.find( '.column-sunflower-persons-related-persons span' )
			.data( 'person-ids' );

		if ( personIds === undefined ) {
			return;
		}

		const editRow = $( '#edit-' + id );
		// Alle Checkboxen zurücksetzen
		editRow
			.find( 'input[name="sunflower_connected_persons[]"]' )
			.prop( 'checked', false );

		// Gespeicherte Werte aktivieren
		if ( personIds ) {
			const ids = String( personIds ).split( ',' );
			ids.forEach( function ( personId ) {
				editRow
					.find(
						'input[name="sunflower_connected_persons[]"][value="' +
							personId.trim() +
							'"]'
					)
					.prop( 'checked', true );
			} );
		}
	};
} )( jQuery );

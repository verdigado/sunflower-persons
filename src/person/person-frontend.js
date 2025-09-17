document.addEventListener( 'DOMContentLoaded', () => {
	const buttons = document.querySelectorAll( '.filter-btn' );
	const persons = document.querySelectorAll( '.sunflower-person' );

	// Hilfsfunktion: filtert nach Slug
	const applyFilter = ( filter ) => {
		persons.forEach( ( p ) => {
			const groups = p.dataset.group.split( ' ' );
			if ( groups.includes( filter ) ) {
				p.style.display = '';
			} else {
				p.style.display = 'none';
			}
		} );
	};

	// Klick-Event für alle Buttons
	buttons.forEach( ( btn ) => {
		btn.addEventListener( 'click', () => {
			const filter = btn.dataset.filter;

			// aktive Klasse setzen
			buttons.forEach( ( b ) => b.classList.remove( 'is-active' ) );
			btn.classList.add( 'is-active' );

			// Personen filtern
			applyFilter( filter );
		} );
	} );

	// ✅ Standard: ersten Button aktiv machen
	const firstBtn = buttons[ 0 ];
	if ( firstBtn ) {
		firstBtn.classList.add( 'is-active' );
		applyFilter( firstBtn.dataset.filter );
	}
} );

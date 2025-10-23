/* global MutationObserver */

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

document.addEventListener( 'DOMContentLoaded', () => {
	const observer = new MutationObserver( () => {
		const lists = document.querySelectorAll( '.sunflower-person-list' );

		lists.forEach( ( list ) => {
			const trackWrapper = list.querySelector(
				'.sunflower-person-track-wrapper'
			);
			const track = trackWrapper.querySelector(
				'.sunflower-person-track'
			);
			const persons = track.querySelectorAll( '.sunflower-person' );

			if ( ! track || persons.length === 0 ) {
				return;
			}

			const visibleCount = parseInt( list.dataset.visible, 10 ) || 5;
			let index = 0;

			// Kachelbreite fixieren
			const wrapperWidth = trackWrapper.clientWidth;
			const personWidth = wrapperWidth / visibleCount;

			persons.forEach( ( p ) => {
				p.style.flex = `0 0 ${ personWidth }px`;
			} );

			const prevBtn = list.querySelector( '.sunflower-person-nav.prev' );
			const nextBtn = list.querySelector( '.sunflower-person-nav.next' );

			// Helper: Scroll aktualisieren
			const updatePosition = () => {
				const personWidthLocal = persons[ 0 ].offsetWidth + 8; // 8px = gap
				const offset = -index * personWidthLocal;
				track.style.transform = `translateX(${ offset }px)`;

				prevBtn.disabled = index === 0;
				nextBtn.disabled = index >= persons.length - visibleCount;
			};

			// Klick-Handler
			prevBtn?.addEventListener( 'click', () => {
				index = Math.max( 0, index - 1 );
				updatePosition();
			} );

			nextBtn?.addEventListener( 'click', () => {
				index = Math.min( persons.length - visibleCount, index + 1 );
				updatePosition();
			} );

			// Startposition setzen
			updatePosition();

			// Optional: Window resize berücksichtigen
			window.addEventListener( 'resize', () => {
				persons.forEach(
					( p ) => ( p.style.flex = `0 0 ${ personWidth }px` )
				);
				updatePosition();
			} );
		} );
	} );

	observer.observe( document.body, { childList: true, subtree: true } );
} );

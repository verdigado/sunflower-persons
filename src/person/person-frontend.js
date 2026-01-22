/* global getComputedStyle */
/* global requestAnimationFrame */

document.addEventListener( 'DOMContentLoaded', () => {
	const buttons = document.querySelectorAll( '.filter-btn' );
	const persons = document.querySelectorAll( '.sunflower-person' );

	// Hilfsfunktion: filtert nach Slug
	const applyFilter = ( filter ) => {
		persons.forEach( ( p ) => {
			// Show all
			if ( filter === 'all' ) {
				p.style.display = '';
				return;
			}
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

/**
 * Carousel functionality
 */
document.addEventListener( 'DOMContentLoaded', () => {
	const lists = document.querySelectorAll(
		'.sunflower-person-list--carousel'
	);

	lists.forEach( ( list ) => {
		const trackWrapper = list.querySelector(
			'.sunflower-person-track-wrapper'
		);
		const track = trackWrapper.querySelector( '.sunflower-person-track' );
		const persons = Array.from(
			track.querySelectorAll( '.sunflower-person' )
		);

		if ( ! track || persons.length === 0 ) {
			return;
		}

		const visibleCount = parseInt( list.dataset.visible, 10 ) || 5;

		const autoplay = list.dataset.autoplay === '1';

		const wrapperWidth = trackWrapper.offsetWidth;
		const gap = parseInt( getComputedStyle( track ).gap ) || 8; // px

		const maxTileWidth = Math.floor(
			( wrapperWidth - gap * ( visibleCount - 1 ) ) / visibleCount
		);

		persons.forEach( ( person ) => {
			person.style.width = `${ Math.max( maxTileWidth, 180 ) }px`;
		} );

		const startMode = list.dataset.slideStart || 'start';
		let index = 0;

		switch ( startMode ) {
			case 'center':
				index = Math.floor( Math.max( persons.length / 2, 0 ) );
				break;

			case 'random':
				index = Math.floor(
					Math.random() *
						Math.max( persons.length - visibleCount + 1, 1 )
				);
				break;

			case 'start':
			default:
				index = 0;
				break;
		}

		let personWidth = persons[ 0 ].offsetWidth + gap;

		const autoplayDelay =
			parseInt( list.dataset.autoplayTimer, 10 ) * 1000 || 5000;

		let autoplayInterval = null;

		// add clone of persons for infinite scroll
		const clone = persons.map( ( p ) => p.cloneNode( true ) );
		clone.forEach( ( p ) => track.appendChild( p ) );

		const updatePosition = ( animate = true ) => {
			const totalWidth = personWidth + gap / 2;
			const offset = -index * totalWidth;
			track.style.transition = animate ? 'transform 0.5s ease' : 'none';
			track.style.transform = `translateX(${ offset }px)`;
		};

		const nextSlide = () => {
			index++;
			updatePosition( true );

			// Wenn wir ans Ende der Original-Liste kommen → Sprung zurück
			if ( index >= persons.length ) {
				setTimeout( () => {
					index = 0;
					updatePosition( false );
				}, 500 );
			}
		};

		const prevSlide = () => {
			index--;
			if ( index < 0 ) {
				index = persons.length - 1;
				updatePosition( false );
				requestAnimationFrame( () => {
					updatePosition( true );
				} );
			} else {
				updatePosition( true );
			}
		};

		const startAutoplay = () => {
			if ( autoplayInterval ) {
				return;
			}
			autoplayInterval = setInterval( nextSlide, autoplayDelay );
		};

		const stopAutoplay = () => {
			clearInterval( autoplayInterval );
			autoplayInterval = null;
		};

		// pause autoplay on hover/focus
		if ( autoplay ) {
			list.addEventListener( 'mouseenter', stopAutoplay );
			list.addEventListener( 'mouseleave', startAutoplay );
			list.addEventListener( 'focusin', stopAutoplay );
			list.addEventListener( 'focusout', startAutoplay );
		}

		const prevBtn = list.querySelector( '.sunflower-person-nav.prev' );
		const nextBtn = list.querySelector( '.sunflower-person-nav.next' );

		nextBtn?.addEventListener( 'click', () => {
			nextSlide();
			if ( autoplay ) {
				stopAutoplay();
			}
		} );
		prevBtn?.addEventListener( 'click', () => {
			prevSlide();
			if ( autoplay ) {
				stopAutoplay();
			}
		} );

		window.addEventListener( 'resize', () => updatePosition( false ) );

		const adjustWrapperWidth = () => {
			personWidth = persons[ 0 ].offsetWidth + gap / 2;
			trackWrapper.style.width = `${ visibleCount * personWidth }px`;
		};

		adjustWrapperWidth();
		updatePosition( false );

		if ( autoplay ) {
			startAutoplay();
		}

		window.addEventListener( 'resize', adjustWrapperWidth );
	} );
} );

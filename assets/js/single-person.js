( function () {
	let moved = false;
	let originalParent;
	let originalNext;

	function moveColumn() {
		const main = document.getElementById( 'person-main' );
		const below = document.getElementById( 'person-content' );

		if ( ! main || ! below ) {
			return;
		}

		if ( window.innerWidth >= 768 && ! moved ) {
			// Merken wo es war
			originalParent = below.parentNode;
			originalNext = below.nextSibling;

			// In Spalte 1 verschieben
			main.appendChild( below );

			// Bootstrap-Klassen anpassen
			below.classList.remove( 'col-md-9', 'order-2' );
			moved = true;
		} else if ( window.innerWidth < 768 && moved ) {
			// Zurück an Original-Position
			originalParent.insertBefore( below, originalNext );

			// Bootstrap-Klassen wiederherstellen
			below.classList.add( 'col-md-9', 'order-2' );
			moved = false;
		}
	}

	window.addEventListener( 'DOMContentLoaded', moveColumn );
	window.addEventListener( 'resize', moveColumn );
} )();

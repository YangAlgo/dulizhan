/**
 * Bufan theme front-end behaviour: mobile menu, sticky header shadow, product gallery.
 */
( function () {
	'use strict';

	var header = document.querySelector( '[data-header]' );
	var nav = document.querySelector( '[data-nav]' );
	var toggle = document.querySelector( '[data-nav-toggle]' );
	var label = document.querySelector( '[data-nav-toggle-label]' );
	var i18n = window.bufanI18n || { menu: 'Menu', close: 'Close' };

	// Mobile menu.
	function setNavTop() {
		if ( header ) {
			var rect = header.getBoundingClientRect();
			document.documentElement.style.setProperty( '--nav-top', Math.max( 0, rect.bottom ) + 'px' );
		}
	}

	function closeNav() {
		if ( ! nav || ! toggle ) {
			return;
		}
		nav.classList.remove( 'is-open' );
		toggle.setAttribute( 'aria-expanded', 'false' );
		document.body.classList.remove( 'nav-open' );
		if ( label ) {
			label.textContent = i18n.menu;
		}
	}

	if ( nav && toggle ) {
		toggle.addEventListener( 'click', function () {
			var open = ! nav.classList.contains( 'is-open' );
			if ( ! open ) {
				closeNav();
				return;
			}
			setNavTop();
			nav.classList.add( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'true' );
			document.body.classList.add( 'nav-open' );
			if ( label ) {
				label.textContent = i18n.close;
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && nav.classList.contains( 'is-open' ) ) {
				closeNav();
				toggle.focus();
			}
		} );

		nav.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( 'a' ) ) {
				closeNav();
			}
		} );

		window.addEventListener( 'resize', function () {
			if ( window.innerWidth > 1080 ) {
				closeNav();
			}
		} );
	}

	// Header shadow once the page scrolls.
	if ( header ) {
		var onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 8 );
		};
		onScroll();
		window.addEventListener( 'scroll', onScroll, { passive: true } );
	}

	// Product gallery: thumbnails swap the main photo; the main photo opens a lightbox.
	var gallery = document.querySelector( '[data-gallery]' );
	if ( gallery ) {
		var main = gallery.querySelector( '[data-gallery-main]' );
		var zoom = gallery.querySelector( '[data-gallery-zoom]' );
		var thumbs = gallery.querySelectorAll( '[data-gallery-thumb]' );

		thumbs.forEach( function ( thumb ) {
			thumb.addEventListener( 'click', function () {
				if ( ! main ) {
					return;
				}
				main.removeAttribute( 'srcset' );
				main.src = thumb.getAttribute( 'data-src' );
				if ( thumb.getAttribute( 'data-srcset' ) ) {
					main.setAttribute( 'srcset', thumb.getAttribute( 'data-srcset' ) );
				}
				if ( zoom ) {
					zoom.setAttribute( 'data-full', thumb.getAttribute( 'data-full' ) );
				}
				thumbs.forEach( function ( other ) {
					other.classList.toggle( 'is-active', other === thumb );
					if ( other === thumb ) {
						other.setAttribute( 'aria-current', 'true' );
					} else {
						other.removeAttribute( 'aria-current' );
					}
				} );
			} );
		} );

		var lightbox = document.querySelector( '[data-lightbox]' );
		if ( zoom && lightbox && 'function' === typeof lightbox.showModal ) {
			var lightboxImg = lightbox.querySelector( '[data-lightbox-img]' );
			zoom.addEventListener( 'click', function () {
				lightboxImg.src = zoom.getAttribute( 'data-full' ) || ( main && main.currentSrc ) || '';
				lightbox.showModal();
			} );
			lightbox.addEventListener( 'click', function ( event ) {
				if ( event.target === lightbox || event.target.closest( '[data-lightbox-close]' ) ) {
					lightbox.close();
				}
			} );
		}
	}
}() );

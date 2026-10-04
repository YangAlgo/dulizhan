/**
 * Bufan admin: product photo gallery and single image pickers.
 */
( function ( $ ) {
	'use strict';

	var l10n = window.bufanAdmin || {};

	// Product gallery (multiple images, sortable).
	$( '[data-bufan-gallery]' ).each( function () {
		var $box = $( this );
		var $list = $box.find( '.bufan-gallery__list' );
		var $input = $box.find( '[data-bufan-gallery-input]' );
		var frame;

		function sync() {
			var ids = $list.children().map( function () {
				return $( this ).data( 'id' );
			} ).get();
			$input.val( ids.join( ',' ) );
		}

		function addItem( attachment ) {
			if ( $list.children( '[data-id="' + attachment.id + '"]' ).length ) {
				return;
			}
			var sizes = attachment.sizes || {};
			var src = ( sizes.thumbnail || sizes.medium || sizes.full || { url: attachment.url } ).url;
			var $item = $( '<li class="bufan-gallery__item"></li>' ).attr( 'data-id', attachment.id );
			$item.append( $( '<img alt="">' ).attr( 'src', src ) );
			$item.append( $( '<button type="button" class="bufan-gallery__remove">×</button>' ).attr( 'aria-label', l10n.remove || 'Remove' ) );
			$list.append( $item );
		}

		$box.on( 'click', '[data-bufan-gallery-add]', function ( event ) {
			event.preventDefault();
			if ( ! frame ) {
				frame = wp.media( {
					title: l10n.galleryTitle,
					button: { text: l10n.galleryButton },
					library: { type: 'image' },
					multiple: 'add'
				} );
				frame.on( 'select', function () {
					frame.state().get( 'selection' ).each( function ( model ) {
						addItem( model.toJSON() );
					} );
					sync();
				} );
			}
			frame.open();
		} );

		$box.on( 'click', '.bufan-gallery__remove', function ( event ) {
			event.preventDefault();
			$( this ).closest( 'li' ).remove();
			sync();
		} );

		if ( $.fn.sortable ) {
			$list.sortable( {
				items: '> li',
				tolerance: 'pointer',
				update: sync
			} );
		}
	} );

	// Single image fields (logo, homepage photos).
	$( document ).on( 'click', '[data-bufan-image-select]', function ( event ) {
		event.preventDefault();
		var $field = $( this ).closest( '[data-bufan-image]' );
		var frame = wp.media( {
			title: l10n.imageTitle,
			button: { text: l10n.imageButton },
			library: { type: 'image' },
			multiple: false
		} );
		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			var sizes = attachment.sizes || {};
			var src = ( sizes.medium || sizes.full || { url: attachment.url } ).url;
			$field.find( 'input[type="hidden"]' ).val( attachment.id );
			$field.find( '.bufan-image-field__preview' ).attr( 'src', src ).prop( 'hidden', false );
			$field.find( '[data-bufan-image-remove]' ).prop( 'hidden', false );
		} );
		frame.open();
	} );

	$( document ).on( 'click', '[data-bufan-image-remove]', function ( event ) {
		event.preventDefault();
		var $field = $( this ).closest( '[data-bufan-image]' );
		$field.find( 'input[type="hidden"]' ).val( '' );
		$field.find( '.bufan-image-field__preview' ).attr( 'src', '' ).prop( 'hidden', true );
		$( this ).prop( 'hidden', true );
	} );
}( jQuery ) );

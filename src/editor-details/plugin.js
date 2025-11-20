/* global sunflowerPersonDetails */

/**
 * sunflower-persons/src/editor-contact/plugin.js
 */

import {
	createRoot,
	useState,
	useEffect,
	useMemo,
	useCallback,
} from '@wordpress/element';
import { TextControl, TextareaControl, Button } from '@wordpress/components';
import { useEntityProp, store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Details Meta Box Component
 *
 * @param {Object} props          Component props.
 * @param {string} props.postType The post type.
 * @param {number} props.postId   The post ID.
 * @return {Element} The Details Meta Box element.
 */
const DetailsMetaBox = ( { postType, postId } ) => {
	//
	// Load all meta fields of the post
	//
	const [ meta, setMeta ] = useEntityProp(
		'postType',
		postType,
		'meta',
		postId
	);

	//
	// Short helper setter
	//
	const updateField = useCallback(
		( key, value ) => {
			setMeta( {
				...meta,
				[ key ]: value,
			} );
		},
		[ meta, setMeta ] // dependencies
	);

	function useDebounce( value, delay = 300 ) {
		const [ debounced, setDebounced ] = useState( value );

		useEffect( () => {
			const t = setTimeout( () => setDebounced( value ), delay );
			return () => clearTimeout( t );
		}, [ value, delay ] );

		return debounced;
	}

	//
	// Meta fields (adjust to your real field names)
	//
	const sortname = meta?.person_sortname || '';
	const phone = meta?.person_phone || '';
	const mobilephone = meta?.person_mobilephone || '';
	const email = meta?.person_email || '';
	const website = meta?.person_website || '';
	const socialmedia = meta?.person_socialmedia || '';
	const photoId = meta?.person_photo_id || '';
	const govoffice = meta?.person_govoffice || '';
	const mandate = meta?.person_mandate || '';
	const constituency = meta?.person_constituency || '';
	const occupation = meta?.person_occupation || '';
	const yearofbirth = meta?.person_yearofbirth || '';

	const [ title ] = useEntityProp( 'postType', postType, 'title', postId );
	const debouncedTitle = useDebounce( title, 1000 );
	useEffect( () => {
		// Set sortname if not set yet from title.
		if ( debouncedTitle && ! meta?.person_sortname ) {
			const parts = debouncedTitle.split( ' ' );
			const newSortname =
				parts.length > 1
					? parts.slice( -1 ) +
					  ', ' +
					  parts.slice( 0, -1 ).join( ' ' )
					: debouncedTitle;
			updateField( 'person_sortname', newSortname );
		}
	}, [ debouncedTitle, meta?.person_sortname, updateField ] );

	const image = useSelect(
		( select ) => {
			if ( ! photoId ) {
				return null;
			}
			return select( coreStore ).getMedia( photoId );
		},
		[ photoId ]
	);

	const thumbnail = useMemo( () => {
		if ( ! image ) {
			return null;
		}
		return {
			id: image.id,
			src:
				image.media_details?.sizes?.thumbnail?.source_url ||
				image.source_url,
			full: image.source_url,
		};
	}, [ image ] );

	return (
		<div className="sunflower-contact-metabox">
			<TextControl
				label={ sunflowerPersonDetails.text.sortname }
				value={ sortname }
				onChange={ ( v ) => updateField( 'person_sortname', v ) }
			/>

			<TextControl
				label={ sunflowerPersonDetails.text.phone }
				value={ phone }
				onChange={ ( v ) => updateField( 'person_phone', v ) }
			/>

			<TextControl
				label={ sunflowerPersonDetails.text.mobilephone }
				value={ mobilephone }
				onChange={ ( v ) => updateField( 'person_mobilephone', v ) }
			/>

			<TextControl
				label={ sunflowerPersonDetails.text.email }
				value={ email }
				onChange={ ( v ) => updateField( 'person_email', v ) }
			/>

			<TextControl
				label={ sunflowerPersonDetails.text.website }
				value={ website }
				onChange={ ( v ) => updateField( 'person_website', v ) }
			/>

			<TextareaControl
				label={ __(
					'Social Media (eine Zeile pro Plattform)',
					'sunflower-persons'
				) }
				help={ __( 'Format: icon;label;URL', 'sunflower-persons' ) }
				value={ socialmedia }
				onChange={ ( v ) => updateField( 'person_socialmedia', v ) }
			/>

			<div
				className="sunflower-person-list"
				style={ { display: 'block', 'padding-bottom': '10px' } }
			>
				{ thumbnail ? (
					<img
						src={ thumbnail.full || thumbnail.src }
						alt="Foto"
						className="sunflower-person-thumb"
						style={ { marginBottom: '10px' } }
					/>
				) : (
					<div
						className="sunflower-person-thumb"
						style={ {
							background: '#eee',
							display: 'flex',
							justifyContent: 'center',
							alignItems: 'center',
							marginBottom: '10px',
						} }
					>
						{ sunflowerPersonDetails.text.nophoto }
					</div>
				) }
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ ( media ) =>
							updateField( 'person_photo_id', media.id )
						}
						allowedTypes={ [ 'image' ] }
						multiple={ false }
						value={ photoId }
						render={ ( { open } ) => (
							<>
								{ ! photoId && (
									<Button variant="primary" onClick={ open }>
										{ sunflowerPersonDetails.text.photoadd }
									</Button>
								) }

								{ photoId && (
									<>
										<Button
											variant="secondary"
											onClick={ open }
											style={ { marginRight: '10px' } }
										>
											{
												sunflowerPersonDetails.text
													.photochange
											}
										</Button>

										<Button
											variant="tertiary"
											isDestructive
											onClick={ () =>
												updateField(
													'person_photo_id',
													null
												)
											}
										>
											{
												sunflowerPersonDetails.text
													.photoremove
											}
										</Button>
									</>
								) }
							</>
						) }
					/>
				</MediaUploadCheck>
			</div>
			<TextControl
				label={ sunflowerPersonDetails.text.govoffice }
				value={ govoffice }
				onChange={ ( v ) => updateField( 'person_govoffice', v ) }
			/>
			<TextControl
				label={ sunflowerPersonDetails.text.mandate }
				value={ mandate }
				onChange={ ( v ) => updateField( 'person_mandate', v ) }
			/>
			<TextControl
				label={ sunflowerPersonDetails.text.constituency }
				value={ constituency }
				onChange={ ( v ) => updateField( 'person_constituency', v ) }
			/>
			<TextControl
				label={ sunflowerPersonDetails.text.occupation }
				value={ occupation }
				onChange={ ( v ) => updateField( 'person_occupation', v ) }
			/>
			<TextControl
				label={ sunflowerPersonDetails.text.yearofbirth }
				value={ yearofbirth }
				onChange={ ( v ) => updateField( 'person_yearofbirth', v ) }
			/>
		</div>
	);
};

/**
 * Bootstrapping: mount into Classic Meta Box wrapper div
 */
document.addEventListener( 'DOMContentLoaded', () => {
	const container = document.getElementById(
		'sunflower-persons-metabox-details'
	);
	if ( container ) {
		const root = createRoot( container );
		const postId = parseInt( container.dataset.postId, 10 );
		root.render(
			<DetailsMetaBox postType="sunflower_person" postId={ postId } />
		);
	}
} );

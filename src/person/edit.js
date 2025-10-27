/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	ToggleControl,
	Disabled,
	FormTokenField,
	PanelBody,
	RangeControl,
	SelectControl,
	Spinner,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useSelect } from '@wordpress/data';
import { useEntityRecords } from '@wordpress/core-data';
import { useState, useEffect } from '@wordpress/element';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

const EMPTY_ARRAY = [];

export default function Edit( { attributes, setAttributes } ) {
	const {
		personId,
		groups,
		tags,
		showFilterButtons,
		showNavButtons,
		showAsFilmstrip,
		slideAutoplay,
		slideStart,
		autoplayTimer,
		limit,
		order,
	} = attributes;

	const blockProps = useBlockProps( { className: 'sunflower-person-block' } );

	const persons = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords(
			'postType',
			'sunflower_person',
			{ per_page: -1 }
		);
	}, [] );

	const personOptions = [
		{
			label: __( 'Show all persons', 'sunflower-persons-person' ),
			value: 0,
		},
	];
	if ( persons ) {
		persons.forEach( ( p ) =>
			personOptions.push( {
				label: p.title.rendered || `#${ p.id }`,
				value: p.id,
			} )
		);
	}

	const [ groupsFormSuggestions, setGroupsFormSuggestions ] =
		useState( EMPTY_ARRAY );
	const [ groupsFormValue, setGroupsFormValue ] = useState( EMPTY_ARRAY );

	const [ tagsFormSuggestions, setTagsFormSuggestions ] =
		useState( EMPTY_ARRAY );
	const [ tagsFormValue, setTagsFormValue ] = useState( EMPTY_ARRAY );

	const query = { per_page: -1, context: 'view' };

	const { records: allGroups, hasResolved } = useEntityRecords(
		'taxonomy',
		'sunflower_group',
		query
	);

	const { records: allTags = [], hasResolved: hasResolvedTags } =
		useEntityRecords( 'taxonomy', 'post_tag', query );

	useEffect( () => {
		if ( ! hasResolved ) {
			return;
		}

		setGroupsFormSuggestions( allGroups.map( ( g ) => g.name ) );
		setGroupsFormValue(
			allGroups
				.filter( ( g ) => groups?.includes( g.slug ) )
				.map( ( g ) => g.name )
		);
	}, [ allGroups, hasResolved, groups ] );

	useEffect( () => {
		if ( ! hasResolvedTags ) {
			return;
		}

		setTagsFormSuggestions( allTags.map( ( t ) => t.name ) );
		setTagsFormValue(
			allTags
				.filter( ( t ) => tags?.includes( t.slug ) )
				.map( ( t ) => t.name )
		);
	}, [ allTags, hasResolvedTags, tags ] );

	const onChangeGroups = ( formGroups ) => {
		setAttributes( {
			groups: formGroups.map(
				( groupName ) =>
					allGroups
						.filter( ( g ) => g.name === groupName )
						.map( ( g ) => g.slug )[ 0 ]
			),
		} );
	};

	const onChangeTags = ( formTags ) => {
		setAttributes( {
			tags: formTags.map(
				( tagName ) =>
					allTags
						.filter( ( t ) => t.name === tagName )
						.map( ( t ) => t.slug )[ 0 ]
			),
		} );
	};

	function toggleAttribute( propName ) {
		return () => {
			const value = attributes[ propName ];

			setAttributes( { [ propName ]: ! value } );
		};
	}

	return (
		<>
			<div { ...blockProps }>
				<InspectorControls>
					<PanelBody
						title={ __( 'Settings', 'sunflower-persons-person' ) }
					>
						<SelectControl
							label={ __(
								'Choose person',
								'sunflower-persons-person'
							) }
							value={ personId }
							options={ personOptions }
							onChange={ ( val ) =>
								setAttributes( {
									personId: parseInt( val, 10 ),
								} )
							}
						/>
						<FormTokenField
							hasResolved={ hasResolved }
							label={ __( 'Groups', 'sunflower-persons-person' ) }
							value={ groupsFormValue }
							onChange={ onChangeGroups }
							suggestions={ groupsFormSuggestions }
						/>
						<ToggleControl
							label={ __(
								'Show group filter buttons on top',
								'sunflower-persons-person'
							) }
							checked={ showFilterButtons }
							onChange={ toggleAttribute( 'showFilterButtons' ) }
						/>
						<FormTokenField
							hasResolved={ hasResolvedTags }
							label={ __( 'Tag' ) }
							value={ tagsFormValue }
							onChange={ onChangeTags }
							suggestions={ tagsFormSuggestions }
						/>
					</PanelBody>
					<PanelBody
						title={ __(
							'Display Options',
							'sunflower-persons-person'
						) }
					>
						<ToggleControl
							label={ __(
								'Show as filmstrip',
								'sunflower-persons-person'
							) }
							checked={ showAsFilmstrip }
							onChange={ toggleAttribute( 'showAsFilmstrip' ) }
						/>
						{ showAsFilmstrip && (
							<ToggleControl
								label={ __(
									'Show navigation buttons',
									'sunflower-persons-person'
								) }
								checked={ showNavButtons }
								onChange={ toggleAttribute( 'showNavButtons' ) }
							/>
						) }
						{ showAsFilmstrip && (
							<ToggleControl
								label={ __(
									'Autoplay sliding',
									'sunflower-persons-person'
								) }
								checked={ slideAutoplay }
								onChange={ toggleAttribute( 'slideAutoplay' ) }
							/>
						) }
						{ showAsFilmstrip && slideAutoplay && (
							<RangeControl
								label={ __(
									'Seconds between slides (autoplay)',
									'sunflower-persons-person'
								) }
								value={ autoplayTimer }
								onChange={ ( value ) =>
									setAttributes( { autoplayTimer: value } )
								}
								min={ 1 }
								max={ 10 }
							/>
						) }
						{ showAsFilmstrip && (
							<RangeControl
								label={ __(
									'Number of Persons to show',
									'sunflower-persons-person'
								) }
								value={ limit }
								onChange={ ( value ) =>
									setAttributes( { limit: value } )
								}
								min={ 1 }
								max={ 10 }
							/>
						) }
						<SelectControl
							label={ __( 'Order', 'sunflower-persons-person' ) }
							value={ order }
							options={ [
								{
									label: __(
										'Random',
										'sunflower-persons-person'
									),
									value: 'random',
								},
								{
									label: __(
										'alphabetic order (A–Z)',
										'sunflower-persons-person'
									),
									value: 'asc',
								},
								{
									label: __(
										'alphabetic order (Z–A)',
										'sunflower-persons-person'
									),
									value: 'desc',
								},
							] }
							onChange={ ( value ) =>
								setAttributes( { order: value } )
							}
						/>
						{ showAsFilmstrip && (
							<SelectControl
								label={ __(
									'Start with position',
									'sunflower-persons-person'
								) }
								value={ slideStart }
								options={ [
									{
										label: __(
											'Start',
											'sunflower-persons-person'
										),
										value: 'start',
									},
									{
										label: __(
											'Center',
											'sunflower-persons-person'
										),
										value: 'center',
									},
									{
										label: __(
											'Random',
											'sunflower-persons-person'
										),
										value: 'random',
									},
								] }
								onChange={ ( value ) =>
									setAttributes( { slideStart: value } )
								}
							/>
						) }
					</PanelBody>
				</InspectorControls>
				{ ! persons && <Spinner /> }
				{ persons &&
					( personId === 0
						? 'Liste aller Personen'
						: `Einzelne Person #${ personId }` ) }
			</div>

			<div { ...blockProps }>
				<Disabled>
					<ServerSideRender
						block="sunflower-persons/person"
						attributes={ attributes }
					/>
				</Disabled>
			</div>
		</>
	);
}

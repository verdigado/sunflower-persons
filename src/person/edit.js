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
	CheckboxControl,
	Disabled,
	FormTokenField,
	PanelBody,
	SelectControl,
	Spinner,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useSelect } from '@wordpress/data';
import { useEntityRecords } from '@wordpress/core-data';
import { useState, useEffect } from '@wordpress/element';

const EMPTY_ARRAY = [];

export default function Edit( { attributes, setAttributes } ) {
	const { personId, groups, tags, filters } = attributes;

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

	const allFilters = [
		{
			label: __( 'Show group filter', 'sunflower-persons-person' ),
			slug: 'groups',
		},
	];

	const toggleFilter = ( slug ) => {
		const next = filters.includes( slug )
			? filters.filter( ( s ) => s !== slug )
			: [ ...filters, slug ];

		setAttributes( { filters: next } );
	};

	return (
		<>
			<div { ...blockProps }>
				<InspectorControls>
					<PanelBody title="Einstellungen">
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
							'Filter Settings',
							'sunflower-persons-person'
						) }
					>
						{ allFilters.map( ( t ) => (
							<CheckboxControl
								key={ t.slug }
								label={ t.label }
								checked={ filters.includes( t.slug ) }
								onChange={ () => toggleFilter( t.slug ) }
							/>
						) ) }
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

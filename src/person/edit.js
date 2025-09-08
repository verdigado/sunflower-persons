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
	const { personId, groups } = attributes;

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

	const query = { per_page: -1, context: 'view' };

	const { records: allGroups, hasResolved } = useEntityRecords(
		'taxonomy',
		'sunflower_group',
		query
	);

	useEffect( () => {
		if ( ! hasResolved ) {
			return;
		}

		setGroupsFormSuggestions( allGroups.map( ( group ) => group.name ) );
		setGroupsFormValue(
			allGroups
				.filter( ( agroup ) =>
					attributes.groups?.includes( agroup.slug )
				)
				.map( ( agroup ) => agroup.name )
		);
	}, [ allGroups, hasResolved, groups, attributes.groups ] );

	const onChangeGroups = ( formGroups ) => {
		setAttributes( {
			groups: formGroups.map(
				( groupName ) =>
					allGroups
						.filter( ( group ) => group.name === groupName )
						.map( ( group ) => group.slug )[ 0 ]
			),
		} );
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

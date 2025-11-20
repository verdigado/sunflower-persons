/* global sunflowerPersonOffices */

import { createRoot, useState, useEffect } from '@wordpress/element';
import { FormTokenField, TextControl, Button } from '@wordpress/components';
import { useEntityProp, useEntityRecords } from '@wordpress/core-data';

const OfficeMetaBox = ( { postType, postId } ) => {
	const [ meta, setMeta ] = useEntityProp(
		'postType',
		postType,
		'meta',
		postId
	);
	const personOffices = meta?.person_offices || [];

	const [ suggestions, setSuggestions ] = useState( [] );

	const { records: personRecords = [], hasResolved } = useEntityRecords(
		'postType',
		'sunflower_person',
		{ per_page: -1, context: 'edit' }
	);

	const allPersons = personRecords ?? [];

	useEffect( () => {
		if ( ! hasResolved ) {
			return;
		}

		setSuggestions( allPersons.map( ( p ) => p.title.rendered ) );
	}, [ hasResolved, allPersons ] );

	const updateOffice = ( index, key, value ) => {
		const updated = [ ...personOffices ];
		updated[ index ] = { ...updated[ index ], [ key ]: value };

		setMeta( { ...meta, person_offices: updated } );
	};

	const addOffice = () => {
		const updated = [
			...personOffices,
			{
				label: '',
				street: '',
				city: '',
				phone: '',
				email: '',
				employees: [],
			},
		];
		setMeta( { ...meta, person_offices: updated } );
	};
	const removeOffice = ( index ) => {
		const updated = personOffices.filter( ( _, i ) => i !== index );
		setMeta( { ...meta, person_offices: updated } );
	};

	return (
		<div className="sunflower-office-metabox">
			{ ( personOffices || [] ).map( ( office, index ) => (
				<div
					key={ index }
					style={ {
						border: '1px solid #ccc',
						padding: '12px',
						marginBottom: '12px',
					} }
				>
					<TextControl
						label={ sunflowerPersonOffices.text.office.label }
						value={ office.label }
						onChange={ ( val ) =>
							updateOffice( index, 'label', val )
						}
					/>
					<TextControl
						label={
							sunflowerPersonOffices.text.office.streethousenumber
						}
						value={ office.street }
						onChange={ ( val ) =>
							updateOffice( index, 'street', val )
						}
					/>
					<TextControl
						label={ sunflowerPersonOffices.text.office.ziplocation }
						value={ office.city }
						onChange={ ( val ) =>
							updateOffice( index, 'city', val )
						}
					/>
					<TextControl
						label={ sunflowerPersonOffices.text.office.phone }
						value={ office.phone }
						onChange={ ( val ) =>
							updateOffice( index, 'phone', val )
						}
					/>
					<TextControl
						label={ sunflowerPersonOffices.text.office.email }
						value={ office.email }
						onChange={ ( val ) =>
							updateOffice( index, 'email', val )
						}
					/>
					<FormTokenField
						label={ sunflowerPersonOffices.text.office.employees }
						suggestions={ suggestions }
						value={ office.employees.map( ( id ) => {
							const found = allPersons.find(
								( p ) => p.id === id
							);
							return found ? found.title.rendered : '';
						} ) }
						onChange={ ( newTokens ) => {
							const newIds = allPersons
								.filter( ( p ) =>
									newTokens.includes( p.title.rendered )
								)
								.map( ( p ) => p.id );
							updateOffice( index, 'employees', newIds );
						} }
					/>
					<Button
						isDestructive
						onClick={ () => removeOffice( index ) }
						style={ { marginTop: '8px' } }
					>
						{ sunflowerPersonOffices.text.office.removeoffice }
					</Button>
				</div>
			) ) }

			<Button variant="primary" onClick={ addOffice }>
				{ sunflowerPersonOffices.text.office.addoffice }
			</Button>
		</div>
	);
};

document.addEventListener( 'DOMContentLoaded', () => {
	const container = document.getElementById(
		'sunflower-persons-metabox-offices'
	);
	if ( container ) {
		const root = createRoot( container );
		const postId = parseInt( container.dataset.postId, 10 );
		root.render(
			<OfficeMetaBox postType="sunflower_person" postId={ postId } />,
			container
		);
	}
} );

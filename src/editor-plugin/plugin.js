/**
 * global React;
 */
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { registerPlugin } from '@wordpress/plugins';
import { FormTokenField, Icon } from '@wordpress/components';
import { people } from '@wordpress/icons';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEntityRecords } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';

/**
 * Editor plugin for Sunflower Persons settings in the block editor.
 *
 */

const PersonsPanel = () => {
	const meta = useSelect( ( select ) =>
		select( 'core/editor' ).getEditedPostAttribute( 'meta' )
	);
	const { editPost } = useDispatch( 'core/editor' );

	const [ suggestions, setSuggestions ] = useState( [] );
	const [ tokens, setTokens ] = useState( [] );

	const { records: allPersons = [], hasResolved } = useEntityRecords(
		'postType',
		'sunflower_person',
		{ per_page: -1, context: 'view' }
	);

	const labelStyling = {
		verticalAlign: 'middle',
		gap: '4px',
		justifyContent: 'start',
		display: 'inline-flex',
		alignItems: 'center',
	};

	/**
	 * Enhances a label with an icon.
	 *
	 * @param {string} icon The icon to display.
	 * @param {string} text The label text.
	 *
	 * @return {string} The enhanced label component.
	 */
	const enhancedLabel = ( icon = people, text ) => (
		<span style={ labelStyling }>
			<Icon icon={ icon } size={ 24 } />
			<span>{ text }</span>
		</span>
	);

	useEffect( () => {
		if ( ! hasResolved ) {
			return;
		}

		const selected = meta?.sunflower_connected_persons || [];

		setSuggestions( allPersons.map( ( p ) => p.title.rendered ) );
		setTokens(
			allPersons
				.filter( ( p ) => selected.includes( p.id ) )
				.map( ( p ) => p.title.rendered )
		);
	}, [ allPersons, hasResolved, meta?.sunflower_connected_persons ] );

	const onChangePersons = ( newTokens ) => {
		const ids = allPersons
			.filter( ( p ) => newTokens.includes( p.title.rendered ) )
			.map( ( p ) => p.id );
		editPost( { meta: { sunflower_connected_persons: ids } } );
	};

	return (
		<PluginDocumentSettingPanel
			name="sunflower-persons-panel"
			title={ enhancedLabel(
				people,
				__( 'Related persons', 'sunflower-persons-person' )
			) }
		>
			<FormTokenField
				value={ tokens }
				suggestions={ suggestions }
				onChange={ onChangePersons }
			/>
		</PluginDocumentSettingPanel>
	);
};
registerPlugin( 'sunflower-persons-plugin', { render: PersonsPanel } );

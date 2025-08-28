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
	PanelBody,
    SelectControl,
    Spinner
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useSelect } from '@wordpress/data';

export default function Edit( { attributes, setAttributes } ) {
	const {
		personId,
	} = attributes;

	const blockProps = useBlockProps({ className: 'sunflower-person-block' });

    const persons = useSelect( (select) => {
        return select('core').getEntityRecords('postType', 'person', { per_page: -1 });
    }, []);

    const personOptions = [{ label: 'Alle Personen anzeigen', value: 0 }];
    if (persons) {
        persons.forEach((p) => personOptions.push({ label: p.title.rendered || `#${p.id}`, value: p.id }));
    }

	return (
        <>
            <div {...blockProps}>
                <InspectorControls>
                    <PanelBody title="Einstellungen">
                        <SelectControl
                            label="Person auswählen"
                            value={personId}
                            options={personOptions}
                            onChange={(val) => setAttributes({ personId: parseInt(val, 10) })}
                        />
                    </PanelBody>
                </InspectorControls>
                {!persons && <Spinner />}
                {persons && (personId === 0 ? 'Liste aller Personen' : `Einzelne Person #${personId}`)}
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
};

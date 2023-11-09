/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { btsUpdate } from './block';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';
import { CustomSelectControl, PanelBody, __experimentalUnitControl as UnitControl } from '@wordpress/components';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	let { ratingCols } = attributes;

	const onChangeRatings = ( properties ) => {
		btsUpdate();
		setAttributes( { ratingCols: properties.selectedItem.key } );
	}

	let blockClass = 'bts-rating';
	let numberOptions = [
		{
        	key: '1',
        	name: '1',
    	},
		{
        	key: '2',
        	name: '2',
    	},
		{
        	key: '3',
        	name: '3',
    	},
		{
        	key: '4',
        	name: '4',
    	},
		{
        	key: '5',
        	name: '5',
    	}
	];

	const blockProps = useBlockProps({ className: blockClass, 'data-cols': ratingCols });

	return (
		<>
			<InspectorControls>
				<PanelBody title={'Ratings'}>
					<CustomSelectControl value={ ratingCols } label="Anzahl" options={ numberOptions } onChange={ onChangeRatings } />
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}></div>
		</>
	)
}

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';
import { CustomSelectControl, PanelBody, __experimentalUnitControl as UnitControl } from '@wordpress/components';

import waves from './waves';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	let { waveId, waveHeight } = attributes;

	const onChangeWaveId = ( properties ) => {
		setAttributes( { waveId: properties.selectedItem.id } );
		wave = properties.selectedItem;
	}

	const onChangeWaveHeight = ( height ) => {
		setAttributes( { waveHeight: height} );
	}

	if( undefined === waveId ) waveId = 'wave-1';

	let wave = waves.filter( wave => wave.id === waveId ).shift();
	let waveClass = waveId + '-admin';

	const blockProps = useBlockProps({ className: waveClass, style: { 'height': waveHeight } });

	return (
		<>
			<InspectorControls>
				<PanelBody title={'Welle'}>
					<CustomSelectControl label="Wellenform" options={ waves } onChange={ onChangeWaveId } />
					<UnitControl label="Höhe" value={ waveHeight } onChange={ onChangeWaveHeight } />
				</PanelBody>
			</InspectorControls>
			<div {...blockProps } />
		</>
	)
}

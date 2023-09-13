/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

import waves from './waves';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save ( { attributes } ) {
	const { waveId, waveHeight } = attributes;
	let wave = waves.filter( wave => wave.id === waveId ).shift();
	let waveClass = waveId;
	let blockProps = useBlockProps.save( { className: waveClass, style: { 'height': waveHeight, 'margin-top': '-' + waveHeight } });

	return (
		<>
			<div {...blockProps } />
		</>
	)
}

/**
 * Flip Card Back - Save Component
 *
 * @since 1.0.0
 */

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function FlipCardBackSave() {
	const blockProps = useBlockProps.save({
		className: 'airo-wp-flip-card__face airo-wp-flip-card__back',
	});

	const innerBlocksProps = useInnerBlocksProps.save(blockProps);

	return <div {...innerBlocksProps} />;
}

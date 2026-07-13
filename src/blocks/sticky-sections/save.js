/**
 * WordPress dependencies
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function Save({ attributes }) {
	const { stickyOffset } = attributes;

	const blockProps = useBlockProps.save({
		className: 'airo-wp-sticky-sections',
		style: {
			'--airo-wp-sticky-offset': stickyOffset || '0px',
		},
	});

	const innerBlocksProps = useInnerBlocksProps.save(blockProps);

	return <div {...innerBlocksProps} />;
}

/**
 * WordPress dependencies
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function Save({ attributes }) {
	const { navHeading } = attributes;

	const blockProps = useBlockProps.save({
		className: 'airo-wp-scroll-slide',
		'data-airo-wp-nav-heading': navHeading || '',
	});

	const innerBlocksProps = useInnerBlocksProps.save(blockProps);

	return <div {...innerBlocksProps} />;
}

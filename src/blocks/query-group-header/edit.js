import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const TEMPLATE = [
	[
		'core/heading',
		{ level: 3, placeholder: __('Group header', 'airo-wp') },
	],
];

export default function Edit() {
	const blockProps = useBlockProps({ className: 'airo-wp-query-group-header' });
	const innerBlocksProps = useInnerBlocksProps(blockProps, {
		template: TEMPLATE,
		templateLock: false,
	});
	return <div {...innerBlocksProps} />;
}

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classnames from 'classnames';

export default function ImageAccordionItemSave({ attributes, context }) {
	const { uniqueId, verticalAlignment, horizontalAlignment } = attributes;

	// Get context from parent accordion (same as edit.js). Note: block context
	// is not available during save serialization, so this resolves to the
	// default (overlay enabled) for serialized markup — matching prior behavior.
	const enableOverlay =
		context?.['airo-wp/imageAccordion/enableOverlay'] !== undefined
			? context['airo-wp/imageAccordion/enableOverlay']
			: true;

	// Same classes as edit.js - MUST MATCH
	const itemClasses = classnames('airo-wp-image-accordion-item', {
		'airo-wp-image-accordion-item--has-overlay': enableOverlay,
	});

	// Overlay color/opacity are no longer baked into the item markup. They
	// cascade from the parent accordion's `--airo-wp-image-accordion-overlay-*`
	// custom properties (see style.scss), so the frontend honours the parent's
	// overlay settings and no hex literal is serialized here.
	const blockProps = useBlockProps.save({
		className: itemClasses,
		style: {
			'--airo-wp-vertical-alignment': verticalAlignment || 'center',
			'--airo-wp-horizontal-alignment': horizontalAlignment || 'center',
		},
		'data-unique-id': uniqueId,
		role: 'button',
		tabIndex: 0,
	});

	const innerBlocksProps = useInnerBlocksProps.save({
		className: 'airo-wp-image-accordion-item__content',
	});

	return (
		<div {...blockProps}>
			<div {...innerBlocksProps} />
		</div>
	);
}

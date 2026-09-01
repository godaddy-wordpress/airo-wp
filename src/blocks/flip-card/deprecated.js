/**
 * Flip Card Block - Deprecated Versions
 *
 * @since 1.0.0
 */

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { getDeprecatedBlockHTML } from '../../utils/deprecated-block-html';

/**
 * Inline `width: 100%` — the version before that constant moved to style.scss.
 *
 * The root serialized `width:100%` into every saved card. It never varied by
 * attribute and style.scss already declared it on `.airo-wp-flip-card`, so the
 * inline copy was pure duplication that no Style Kit could override. Only the
 * author-controlled `--airo-wp-flip-duration` is still written inline.
 *
 * Markup-only change: same attributes, same classes — so migrate() is a
 * passthrough.
 *
 * NOTE: WordPress calls `isEligible(attributes, innerBlocks, { blockNode, block })`
 * — there is no `innerHTML` key on that third argument. It only matters for a
 * block that is otherwise VALID; for an invalid one WordPress skips isEligible
 * and picks the deprecation whose save() reproduces the stored HTML.
 */
const v1 = {
	// Must mirror block.json exactly — a deprecation whose `supports` omits a
	// group makes WordPress strip those attributes before migrate() runs.
	attributes: {
		flipTrigger: {
			type: 'string',
			default: 'hover',
			enum: ['hover', 'click'],
		},
		flipEffect: {
			type: 'string',
			default: 'flip',
			enum: ['flip', 'fade', 'slide', 'zoom'],
		},
		flipDirection: {
			type: 'string',
			default: 'horizontal',
			enum: ['horizontal', 'vertical'],
		},
		flipDuration: { type: 'string', default: '0.6s' },
	},
	supports: {
		anchor: true,
		align: false,
		html: false,
		inserter: true,
		spacing: {
			margin: true,
			padding: false,
			__experimentalDefaultControls: { margin: true },
		},
	},
	isEligible(attributes, innerBlocks, extra) {
		const html = getDeprecatedBlockHTML(extra);
		return (
			html.includes('airo-wp-flip-card') && html.includes('width:100%')
		);
	},
	migrate(attributes) {
		// Markup-only change.
		return attributes;
	},
	save({ attributes }) {
		const { flipTrigger, flipEffect, flipDirection, flipDuration } =
			attributes;

		const blockProps = useBlockProps.save({
			className: `airo-wp-flip-card airo-wp-flip-card--${flipTrigger} airo-wp-flip-card--effect-${flipEffect} airo-wp-flip-card--${flipDirection}`,
			style: {
				'--airo-wp-flip-duration': flipDuration,
				width: '100%',
			},
			'data-flip-trigger': flipTrigger,
			'data-flip-effect': flipEffect,
			'data-flip-direction': flipDirection,
		});

		const innerBlocksProps = useInnerBlocksProps.save({
			className: 'airo-wp-flip-card__container',
		});

		return (
			<div {...blockProps}>
				<div {...innerBlocksProps} />
			</div>
		);
	},
};

export { v1 };

export default [v1];

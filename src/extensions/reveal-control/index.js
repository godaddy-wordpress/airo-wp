/**
 * Reveal Control Extension
 * Adds "Reveal on Hover" functionality to container blocks
 * - Container blocks can enable reveal mode
 * - Child blocks can be marked to reveal on parent hover
 */

import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { shouldExtendBlock } from '../../utils/should-extend-block';

const CONTAINER_BLOCKS = [
	'airo-wp/section', // Section block (vertical stack)
	'airo-wp/row', // Row block (horizontal flex)
	'airo-wp/grid',
];

// NOTE: The `airo-wp/reveal/*` strings below are context-key names, not
// references to the retired `airo-wp/reveal` block. Renaming them would
// require block deprecations for every container using this extension, so the
// prefix is intentionally preserved.

/**
 * Add reveal control attributes to all blocks
 * @param {Object} settings - Block settings
 * @param {string} name     - Block name
 */
function addRevealAttributes(settings, name) {
	// Check user exclusion list first
	if (!shouldExtendBlock(name)) {
		return settings;
	}

	// Add reveal toggle to container blocks
	if (CONTAINER_BLOCKS.includes(name)) {
		settings.attributes = {
			...settings.attributes,
			enableRevealOnHover: {
				type: 'boolean',
				default: false,
			},
			revealAnimationType: {
				type: 'string',
				default: 'fade',
			},
		};

		// Add context provider for container blocks
		settings.providesContext = {
			...settings.providesContext,
			'airo-wp/reveal/isRevealContainer': 'enableRevealOnHover',
			'airo-wp/reveal/animationType': 'revealAnimationType',
		};
	}

	// Add reveal attribute to all blocks (for child blocks)
	settings.attributes = {
		...settings.attributes,
		dsgoRevealOnHover: {
			type: 'boolean',
			default: false,
		},
	};

	// Add usesContext to all blocks so they can receive reveal container context
	if (!CONTAINER_BLOCKS.includes(name)) {
		settings.usesContext = [
			...(settings.usesContext || []),
			'airo-wp/reveal/isRevealContainer',
			'airo-wp/reveal/animationType',
		];
	}

	return settings;
}

addFilter(
	'blocks.registerBlockType',
	'airo-wp/reveal-control-attributes',
	addRevealAttributes
);

/**
 * Add reveal control UI to blocks
 */
const withRevealControl = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const { attributes, setAttributes, context = {}, name } = props;
		const { dsgoRevealOnHover, enableRevealOnHover, revealAnimationType } =
			attributes;
		const isInRevealContainer = context['airo-wp/reveal/isRevealContainer'];
		const isContainerBlock = CONTAINER_BLOCKS.includes(name);

		return (
			<>
				<BlockEdit {...props} />
				<InspectorControls>
					{isContainerBlock && (
						<PanelBody
							title={__('Reveal on Hover', 'airo-wp')}
							initialOpen={false}
						>
							<ToggleControl
								label={__('Enable Reveal Mode', 'airo-wp')}
								help={
									enableRevealOnHover
										? __(
												'Child blocks can be set to reveal when hovering over this container.',
												'airo-wp'
											)
										: __(
												'Enable to allow child blocks to reveal on hover.',
												'airo-wp'
											)
								}
								checked={enableRevealOnHover}
								onChange={(value) =>
									setAttributes({
										enableRevealOnHover: value,
									})
								}
								__nextHasNoMarginBottom
							/>
							{enableRevealOnHover && (
								<ToggleControl
									label={__('Collapse Animation', 'airo-wp')}
									help={
										revealAnimationType === 'collapse'
											? __(
													'Items collapse from height 0 with fade.',
													'airo-wp'
												)
											: __(
													'Items fade in without size change.',
													'airo-wp'
												)
									}
									checked={revealAnimationType === 'collapse'}
									onChange={(value) =>
										setAttributes({
											revealAnimationType: value
												? 'collapse'
												: 'fade',
										})
									}
									__nextHasNoMarginBottom
								/>
							)}
						</PanelBody>
					)}
					{isInRevealContainer && !isContainerBlock && (
						<PanelBody
							title={__('Reveal Settings', 'airo-wp')}
							initialOpen={false}
						>
							<ToggleControl
								label={__('Reveal on Hover', 'airo-wp')}
								help={
									dsgoRevealOnHover
										? __(
												'This block will be hidden until you hover over the parent container.',
												'airo-wp'
											)
										: __(
												'This block is always visible.',
												'airo-wp'
											)
								}
								checked={dsgoRevealOnHover}
								onChange={(value) =>
									setAttributes({ dsgoRevealOnHover: value })
								}
								__nextHasNoMarginBottom
							/>
						</PanelBody>
					)}
				</InspectorControls>
			</>
		);
	};
}, 'withRevealControl');

addFilter('editor.BlockEdit', 'airo-wp/reveal-control-edit', withRevealControl);

/**
 * Add reveal classes and data attributes to blocks
 * @param {Object} props      - Block props
 * @param {Object} blockType  - Block type
 * @param {Object} attributes - Block attributes
 */
function addRevealClasses(props, blockType, attributes) {
	const { dsgoRevealOnHover, enableRevealOnHover, revealAnimationType } =
		attributes;
	const isContainerBlock = CONTAINER_BLOCKS.includes(blockType.name);

	// Add class to container blocks with reveal enabled
	if (isContainerBlock && enableRevealOnHover) {
		return {
			...props,
			className: `${props.className || ''} airo-wp-has-reveal`.trim(),
			'data-reveal-animation': revealAnimationType || 'fade',
		};
	}

	// Add class to child blocks marked for reveal
	if (dsgoRevealOnHover) {
		return {
			...props,
			className: `${props.className || ''} airo-wp-reveal-item`.trim(),
		};
	}

	return props;
}

addFilter(
	'blocks.getSaveContent.extraProps',
	'airo-wp/reveal-control-save',
	addRevealClasses
);

/**
 * Add reveal classes in editor
 */
const withRevealEditorClasses = createHigherOrderComponent((BlockListBlock) => {
	return (props) => {
		const { attributes, context = {}, name } = props;
		const { dsgoRevealOnHover, enableRevealOnHover, revealAnimationType } =
			attributes;
		const isInRevealContainer = context['airo-wp/reveal/isRevealContainer'];
		const isContainerBlock = CONTAINER_BLOCKS.includes(name);

		// Add class to container blocks with reveal enabled
		if (isContainerBlock && enableRevealOnHover) {
			return (
				<BlockListBlock
					{...props}
					className={`${props.className || ''} airo-wp-has-reveal`.trim()}
					data-reveal-animation={revealAnimationType || 'fade'}
				/>
			);
		}

		// Add class to child blocks marked for reveal
		if (isInRevealContainer && dsgoRevealOnHover && !isContainerBlock) {
			return (
				<BlockListBlock
					{...props}
					className={`${props.className || ''} airo-wp-reveal-item`.trim()}
				/>
			);
		}

		return <BlockListBlock {...props} />;
	};
}, 'withRevealEditorClasses');

addFilter(
	'editor.BlockListBlock',
	'airo-wp/reveal-control-editor-classes',
	withRevealEditorClasses
);

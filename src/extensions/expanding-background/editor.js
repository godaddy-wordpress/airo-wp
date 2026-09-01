/**
 * Expanding Background Extension - Editor Integration
 *
 * @package
 */

import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment } from '@wordpress/element';
import ExpandingBackgroundPanel from './components/ExpandingBackgroundPanel';
import { SUPPORTED_BLOCKS } from './constants';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';

/**
 * Add expanding background controls to the block editor
 */
const withExpandingBackgroundControls = createHigherOrderComponent(
	(BlockEdit) => {
		return (props) => {
			const { name } = props;

			// Only add controls to supported blocks
			if (!SUPPORTED_BLOCKS.includes(name)) {
				return <BlockEdit {...props} />;
			}

			return (
				<Fragment>
					<BlockEdit {...props} />
					<ExpandingBackgroundPanel {...props} />
				</Fragment>
			);
		};
	},
	'withExpandingBackgroundControls'
);

addFilter(
	'editor.BlockEdit',
	'airo-wp/expanding-background-controls',
	withExpandingBackgroundControls
);

/**
 * Add expanding background classes and styles to block wrapper in editor
 */
const addExpandingBackgroundEditorClasses = createHigherOrderComponent(
	(BlockListBlock) => {
		return (props) => {
			const { attributes, name } = props;

			// Only add classes to supported blocks
			if (!SUPPORTED_BLOCKS.includes(name)) {
				return <BlockListBlock {...props} />;
			}

			const { dsgoExpandingBgEnabled, dsgoExpandingBgColor } = attributes;

			// Add class and inline styles if enabled
			if (dsgoExpandingBgEnabled) {
				const className = [
					props.className,
					'has-airo-wp-expanding-background',
				]
					.filter(Boolean)
					.join(' ');

				// Add inline style with CSS variable for the color
				const style = {
					...props.style,
					'--airo-wp-expanding-bg-color':
						convertColorToCSSVar(dsgoExpandingBgColor) || '#e8e8e8',
				};

				return (
					<BlockListBlock
						{...props}
						className={className}
						style={style}
					/>
				);
			}

			return <BlockListBlock {...props} />;
		};
	},
	'addExpandingBackgroundEditorClasses'
);

addFilter(
	'editor.BlockListBlock',
	'airo-wp/expanding-background-editor-classes',
	addExpandingBackgroundEditorClasses
);

/**
 * Add expanding background attributes to save props
 *
 * @param {Object} extraProps Block save props
 * @param {Object} blockType  Block type
 * @param {Object} attributes Block attributes
 * @return {Object} Modified props
 */
function addExpandingBackgroundSaveProps(extraProps, blockType, attributes) {
	const {
		dsgoExpandingBgEnabled,
		dsgoExpandingBgColor,
		dsgoExpandingBgInitialSize,
		dsgoExpandingBgBlur,
		dsgoExpandingBgSpeed,
		dsgoExpandingBgTriggerOffset,
		dsgoExpandingBgCompletionPoint,
	} = attributes;

	// Only add props to supported blocks with the effect enabled
	if (!SUPPORTED_BLOCKS.includes(blockType.name) || !dsgoExpandingBgEnabled) {
		return extraProps;
	}

	return {
		...extraProps,
		className: [extraProps.className, 'has-airo-wp-expanding-background']
			.filter(Boolean)
			.join(' '),
		style: {
			...(extraProps.style || {}),
			'--airo-wp-expanding-bg-color':
				convertColorToCSSVar(dsgoExpandingBgColor) || '#e8e8e8',
		},
		'data-airo-wp-expanding-bg-enabled': 'true',
		'data-airo-wp-expanding-bg-color':
			convertColorToCSSVar(dsgoExpandingBgColor) || '',
		'data-airo-wp-expanding-bg-initial-size':
			dsgoExpandingBgInitialSize || '',
		'data-airo-wp-expanding-bg-blur': dsgoExpandingBgBlur || '',
		'data-airo-wp-expanding-bg-speed': dsgoExpandingBgSpeed || '',
		'data-airo-wp-expanding-bg-trigger-offset':
			dsgoExpandingBgTriggerOffset || '',
		'data-airo-wp-expanding-bg-completion-point':
			dsgoExpandingBgCompletionPoint || '',
	};
}

addFilter(
	'blocks.getSaveContent.extraProps',
	'airo-wp/expanding-background-save-props',
	addExpandingBackgroundSaveProps
);

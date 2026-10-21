/**
 * Star Rating — edit component.
 *
 * The block is server-rendered so that a bound rating (post meta, ACF, a
 * WooCommerce average) resolves at render time. Everything here is preview:
 * it reproduces render.php's markup and CSS variables so the canvas matches
 * the page.
 *
 * Colour, border and padding are skip-serialized in block.json and re-applied
 * to the inner element, the same routing render.php performs with
 * `airowp_route_visual_supports()`. Without it a background would paint
 * across the whole content column instead of hugging the stars.
 *
 * @since 2.8.0
 */

import classnames from 'classnames';
import {
	useBlockProps,
	__experimentalUseColorProps as useColorProps,
	__experimentalUseBorderProps as useBorderProps,
	__experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles,
} from '@wordpress/block-editor';
import DsgoJustificationToolbar from '../../components/shared/DsgoJustificationToolbar';
import { getJustificationClass } from '../../utils/justification';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';
import { useIconDefaults } from '../../hooks';
import Inspector from './components/Inspector';
import StarRatingPreview from './components/StarRatingPreview';
import { getFillPercent } from './utils/rating';

/**
 * @param {Object}   props               Component props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @param {string}   props.clientId      Block client id.
 * @return {JSX.Element} Edit component.
 */
export default function StarRatingEdit({
	attributes,
	setAttributes,
	clientId,
}) {
	const {
		rating,
		maxRating,
		precision,
		iconStyle,
		iconSize,
		iconGap,
		ratingColor,
		trackColor,
		justification,
		metadata,
		style,
	} = attributes;

	const isRatingBound = !!metadata?.bindings?.rating;

	// iconStyle / iconSize are unset unless the author overrides them. The
	// style is resolved here so the preview draws the inherited shape; the
	// size needs no resolving — leaving the custom property unset lets
	// style.scss fall through to the theme token, exactly as on the page.
	const iconDefaults = useIconDefaults({
		sizeKey: 'starRating',
		sizeFallback: 24,
	});

	const blockProps = useBlockProps({
		className: classnames(
			'airo-wp-star-rating',
			'airo-wp-justify',
			getJustificationClass(justification)
		),
	});

	// The custom properties live on the inner element, not the wrapper —
	// render.php has no choice about that (airowp_route_visual_supports()
	// rewrites the wrapper's style attribute wholesale), and the canvas has to
	// match it.
	const ratingVars = {
		'--airo-wp-star-rating-fill': `${getFillPercent(
			rating,
			maxRating,
			precision
		)}%`,
		...(typeof iconSize === 'number' && {
			'--airo-wp-star-rating-size': `${iconSize}px`,
		}),
		'--airo-wp-star-rating-gap': `${iconGap}px`,
		'--airo-wp-star-rating-color': convertColorToCSSVar(ratingColor),
		'--airo-wp-star-rating-track-color': convertColorToCSSVar(trackColor),
	};

	const colorProps = useColorProps(attributes);
	const borderProps = useBorderProps(attributes);
	const paddingProps = getSpacingClassesAndStyles({
		style: { spacing: { padding: style?.spacing?.padding } },
	});

	return (
		<>
			<DsgoJustificationToolbar
				value={justification}
				onChange={(value) => setAttributes({ justification: value })}
			/>
			<Inspector
				attributes={attributes}
				setAttributes={setAttributes}
				clientId={clientId}
				isRatingBound={isRatingBound}
			/>
			<div {...blockProps}>
				<StarRatingPreview
					attributes={{
						...attributes,
						iconStyle: iconStyle || iconDefaults.style,
					}}
					className={classnames(
						colorProps.className,
						borderProps.className
					)}
					style={{
						...ratingVars,
						...borderProps.style,
						...colorProps.style,
						...paddingProps.style,
					}}
				/>
			</div>
		</>
	);
}

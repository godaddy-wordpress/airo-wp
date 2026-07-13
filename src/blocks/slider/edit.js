import { __, sprintf } from '@wordpress/i18n';
import { useEffect, useMemo, useRef } from '@wordpress/element';
import {
	useBlockProps,
	useInnerBlocksProps,
	BlockControls,
	InspectorControls,
	BlockContextProvider,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	SelectControl,
	RangeControl,
	TextControl,
	Notice,
	Button,
	Tooltip,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { createBlock, cloneBlock } from '@wordpress/blocks';
import { copy, trash, plus } from '@wordpress/icons';
import classnames from 'classnames';
import {
	encodeColorValue,
	decodeColorValue,
} from '../../utils/encode-color-value';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';
import { useBlockColors } from '../../hooks';
import SliderPlaceholder from './components/SliderPlaceholder';
import DsgoChildToolbar from '../../components/shared/DsgoChildToolbar';
import useQueryHostPreview, {
	buildItemContext,
} from '../query/hooks/useQueryHostPreview';
import useParentQueryAttrs from '../query/hooks/useParentQueryAttrs';
import QueryHostReadOnlyItem from '../query/components/QueryHostReadOnlyItem';

const SINGLE_SLIDE_EFFECTS = ['fade', 'zoom'];

export default function SliderEdit({
	attributes,
	setAttributes,
	clientId,
	context,
}) {
	// When the slider sits inside a Dynamic Query, the parent provides a
	// queryId via context. In that mode the slider becomes an item host: the
	// first slide acts as the per-item template, extras are ignored
	// server-side, and the toolbar's Add/Remove controls are locked out.
	const queryId =
		typeof context === 'object' && context
			? context['airo-wp/queryId'] || ''
			: '';
	const inQueryMode = !!queryId;
	const {
		slidesPerView,
		slidesPerViewTablet,
		slidesPerViewMobile,
		height,
		aspectRatio,
		useAspectRatio,
		gap,
		showArrows,
		showDots,
		arrowStyle,
		arrowPosition,
		arrowVerticalPosition,
		arrowColor,
		arrowBackgroundColor,
		arrowSize,
		arrowPadding,
		dotStyle,
		dotPosition,
		dotColor,
		effect,
		transitionDuration,
		transitionEasing,
		autoplay,
		autoplayInterval,
		pauseOnHover,
		pauseOnInteraction,
		loop,
		draggable,
		swipeable,
		freeMode,
		centeredSlides,
		mobileBreakpoint,
		tabletBreakpoint,
		styleVariation,
		ariaLabel,
		scrollDriven,
		scrollDrivenSpeed,
	} = attributes;

	// Arrow Colors panel — migrated to useBlockColors hook.
	// colorGradientSettings is returned by the hook (same shape as
	// useMultipleOriginColorsAndGradients) and used by the inline Dot Color panel.
	const { settings: arrowColorSettings, colorGradientSettings } =
		useBlockColors({
			attributes,
			setAttributes,
			entries: [
				{
					label: __('Arrow Icon Color', 'airo-wp'),
					attribute: 'arrowColor',
				},
				{
					label: __('Arrow Background', 'airo-wp'),
					attribute: 'arrowBackgroundColor',
				},
			],
		});
	const requiresSingleSlideEffect = SINGLE_SLIDE_EFFECTS.includes(effect);

	const blockRef = useRef(null);

	// Slide list powers both the dot navigation and the editor-only navigator.
	const { slides, selectedSlideId } = useSelect(
		(select) => {
			const editor = select('core/block-editor');
			const children = editor.getBlocks(clientId) || [];
			const selectedBlock = editor.getSelectedBlockClientId();
			let selectedSlide = null;
			if (selectedBlock) {
				if (
					children.some((child) => child.clientId === selectedBlock)
				) {
					selectedSlide = selectedBlock;
				} else {
					const parents = editor.getBlockParents(selectedBlock);
					const match = parents.find((parent) =>
						children.some((child) => child.clientId === parent)
					);
					if (match) {
						selectedSlide = match;
					}
				}
			}
			return { slides: children, selectedSlideId: selectedSlide };
		},
		[clientId]
	);
	const slideCount = slides.length;
	const selectedSlideIndex = selectedSlideId
		? slides.findIndex((slide) => slide.clientId === selectedSlideId)
		: -1;
	const activeSlideIndex =
		selectedSlideIndex >= 0 ? selectedSlideIndex : undefined;

	// When bound to a parent Dynamic Query, walk up to the parent's attributes
	// so the editor preview uses the same query config (postType, perPage,
	// filters, orderBy) the frontend will. Runs only when inQueryMode.
	const parentQueryAttrs = useParentQueryAttrs(clientId, inQueryMode);

	// The per-item template is the first slide block. Memoize to avoid
	// allocating a new array on every render — useQueryHostPreview /
	// useRenderedItems depend on stable references to avoid re-serializing.
	const templateSlideBlocks = useMemo(
		() => (slides.length > 0 ? [slides[0]] : []),
		[slides]
	);

	const preview = useQueryHostPreview({
		attributes: parentQueryAttrs,
		queryId,
		innerBlocks: templateSlideBlocks,
		enabled: inQueryMode && !!parentQueryAttrs,
	});

	const { insertBlock, removeBlock, selectBlock } =
		useDispatch('core/block-editor');

	// Extract a readable label for a slide: first heading text if present, else "Slide N".
	// DOMParser handles malformed/unterminated tags safely (unlike a naive regex).
	const getSlideLabel = (slide, index) => {
		const heading = slide.innerBlocks?.find(
			(inner) => inner.name === 'core/heading'
		);
		const raw = heading?.attributes?.content ?? '';
		const parsed = new window.DOMParser().parseFromString(
			String(raw),
			'text/html'
		);
		const text = (parsed.body.textContent || '').trim();
		if (text) {
			return text.slice(0, 30);
		}
		return sprintf(
			/* translators: %d: slide number */
			__('Slide %d', 'airo-wp'),
			index + 1
		);
	};

	const handleAddSlide = () => {
		const newSlide = createBlock('airo-wp/slide');
		insertBlock(newSlide, slideCount, clientId, true);
	};

	const handleDuplicateSlide = (slide, index) => {
		// cloneBlock does a deep clone with fresh clientIds at every level.
		const clone = cloneBlock(slide);
		insertBlock(clone, index + 1, clientId, true);
	};

	const handleRemoveSlide = (slide) => {
		if (slides.length <= 1) {
			return;
		}
		removeBlock(slide.clientId, false);
	};

	useEffect(() => {
		if (
			!requiresSingleSlideEffect ||
			(slidesPerView === 1 &&
				slidesPerViewTablet === 1 &&
				slidesPerViewMobile === 1)
		) {
			return;
		}

		setAttributes({
			slidesPerView: 1,
			slidesPerViewTablet: 1,
			slidesPerViewMobile: 1,
		});
	}, [
		requiresSingleSlideEffect,
		slidesPerView,
		slidesPerViewTablet,
		slidesPerViewMobile,
		setAttributes,
	]);

	// Editor navigation: scroll the track using a ref (works inside iframed editor)
	const scrollToSlide = (direction) => {
		const track = blockRef.current?.querySelector('.airo-wp-slider__track');
		if (!track) {
			return;
		}

		const slide = track.querySelector('.airo-wp-slide');
		if (!slide) {
			return;
		}

		const slideWidth = slide.offsetWidth;
		const gapValue =
			parseFloat(
				track.ownerDocument.defaultView.getComputedStyle(track).gap
			) || 0;
		const scrollAmount = slideWidth + gapValue;

		track.scrollBy({
			left: direction === 'next' ? scrollAmount : -scrollAmount,
			behavior: 'smooth',
		});
	};

	const scrollToSlideIndex = (index) => {
		const track = blockRef.current?.querySelector('.airo-wp-slider__track');
		if (!track) {
			return;
		}

		const slide = track.querySelector('.airo-wp-slide');
		if (!slide) {
			return;
		}

		const slideWidth = slide.offsetWidth;
		const gapValue =
			parseFloat(
				track.ownerDocument.defaultView.getComputedStyle(track).gap
			) || 0;
		const scrollPosition = index * (slideWidth + gapValue);

		track.scrollTo({
			left: scrollPosition,
			behavior: 'smooth',
		});
	};

	// Declaratively calculate classes based on attributes
	const sliderClasses = classnames('airo-wp-slider', {
		[`airo-wp-slider--${styleVariation}`]: styleVariation,
		[`airo-wp-slider--effect-${effect}`]: effect,
		'airo-wp-slider--has-arrows': showArrows,
		'airo-wp-slider--has-dots': showDots,
		'airo-wp-slider--centered': centeredSlides,
		'airo-wp-slider--free-mode': freeMode,
		'airo-wp-slider--scroll-driven': scrollDriven,
	});

	const effectiveSlidesPerView = requiresSingleSlideEffect
		? 1
		: slidesPerView;
	const effectiveSlidesPerViewTablet = requiresSingleSlideEffect
		? 1
		: slidesPerViewTablet;
	const effectiveSlidesPerViewMobile = requiresSingleSlideEffect
		? 1
		: slidesPerViewMobile;
	const singleSlideNotice = __(
		'Fade and Zoom transitions show one slide per view. Switch back to the Slide effect to display multiple slides at once.',
		'airo-wp'
	);

	const handleEffectChange = (value) => {
		const updates = { effect: value };
		if (SINGLE_SLIDE_EFFECTS.includes(value)) {
			updates.slidesPerView = 1;
			updates.slidesPerViewTablet = 1;
			updates.slidesPerViewMobile = 1;
		}
		setAttributes(updates);
	};

	// Apply settings as CSS custom properties
	const customStyles = {
		...(height && { '--airo-wp-slider-height': height }),
		'--airo-wp-slider-aspect-ratio': aspectRatio,
		'--airo-wp-slider-gap': gap,
		'--airo-wp-slider-transition': transitionDuration,
		'--airo-wp-slider-slides-per-view': String(effectiveSlidesPerView),
		'--airo-wp-slider-slides-per-view-tablet': String(
			effectiveSlidesPerViewTablet
		),
		'--airo-wp-slider-slides-per-view-mobile': String(
			effectiveSlidesPerViewMobile
		),
		...(arrowColor && {
			'--airo-wp-slider-arrow-color': convertColorToCSSVar(arrowColor),
		}),
		...(arrowBackgroundColor && {
			'--airo-wp-slider-arrow-bg-color':
				convertColorToCSSVar(arrowBackgroundColor),
		}),
		...(arrowSize && { '--airo-wp-slider-arrow-size': arrowSize }),
		...(arrowPadding && { '--airo-wp-slider-arrow-padding': arrowPadding }),
		...(dotColor && {
			'--airo-wp-slider-dot-color': convertColorToCSSVar(dotColor),
		}),
	};

	// Block wrapper props
	// Data attributes for JavaScript configuration and CSS selectors (match save.js)
	const blockProps = useBlockProps({
		ref: blockRef,
		className: sliderClasses,
		style: customStyles,
		'data-slides-per-view': effectiveSlidesPerView,
		'data-slides-per-view-tablet': effectiveSlidesPerViewTablet,
		'data-slides-per-view-mobile': effectiveSlidesPerViewMobile,
		'data-use-aspect-ratio': useAspectRatio,
		'data-show-arrows': showArrows,
		'data-show-dots': showDots,
		'data-arrow-style': arrowStyle,
		'data-arrow-position': arrowPosition,
		'data-arrow-vertical-position': arrowVerticalPosition,
		'data-dot-style': dotStyle,
		'data-dot-position': dotPosition,
		'data-effect': effect,
	});

	// Inner blocks configuration - ONLY allow slide children. Initial seeding
	// is handled by SliderPlaceholder so authors pick a starter layout instead
	// of landing on a generic three-slide template. In query mode we also lock
	// add/remove at the slider level — slides beyond the first are ignored at
	// render, so allowing authors to add more would be misleading. The class
	// name also switches: in authored mode this IS the track; in query mode
	// it becomes a "display: contents" slot that sits inside a manually-built
	// track alongside read-only preview items.
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: inQueryMode
				? 'airo-wp-slider__editor-template-slot'
				: 'airo-wp-slider__track',
		},
		{
			allowedBlocks: ['airo-wp/slide'],
			orientation: 'horizontal',
			templateLock: inQueryMode ? 'insert' : false,
		}
	);

	if (slideCount === 0) {
		return (
			<div {...blockProps}>
				<SliderPlaceholder
					clientId={clientId}
					setAttributes={setAttributes}
				/>
			</div>
		);
	}

	return (
		<>
			<BlockControls>
				<DsgoChildToolbar
					parentClientId={clientId}
					childBlockName="airo-wp/slide"
					activeIndex={activeSlideIndex}
					onActiveIndexChange={(index, newClientId) => {
						// Prefer the clientId handed to us — the `slides`
						// closure here comes from the parent's useSelect and
						// doesn't include freshly-inserted children until
						// the next render. On Remove (null clientId) fall
						// through to the clamped neighbor in the stale list.
						if (newClientId) {
							selectBlock(newClientId);
							return;
						}
						const target = slides[index];
						if (target) {
							selectBlock(target.clientId);
						}
					}}
					addLabel={__('Add slide', 'airo-wp')}
					duplicateLabel={__('Duplicate slide', 'airo-wp')}
					removeLabel={__('Remove slide', 'airo-wp')}
					movePrevLabel={__('Move slide left', 'airo-wp')}
					moveNextLabel={__('Move slide right', 'airo-wp')}
					disableAdd={inQueryMode}
					disableDuplicate={inQueryMode}
					disableRemove={inQueryMode}
					disableMove={inQueryMode}
				/>
			</BlockControls>

			<InspectorControls>
				{inQueryMode && (
					<PanelBody
						title={__('Dynamic mode', 'airo-wp')}
						initialOpen={true}
					>
						<Notice status="info" isDismissible={false}>
							{__(
								'This slider is bound to a parent Dynamic Query. The first slide is the per-item template — extra slides are ignored at render. Slide management controls are disabled while bound.',
								'airo-wp'
							)}
						</Notice>
						{slideCount > 1 && (
							<Notice status="warning" isDismissible={false}>
								{sprintf(
									/* translators: %d: number of slides that will not render */
									__(
										'%d extra slide(s) will be ignored at render. Only the first slide is used as the template.',
										'airo-wp'
									),
									slideCount - 1
								)}
							</Notice>
						)}
					</PanelBody>
				)}
				<PanelBody
					title={__('Layout Settings', 'airo-wp')}
					initialOpen={true}
				>
					<RangeControl
						label={__('Slides Per View (Desktop)', 'airo-wp')}
						value={slidesPerView}
						onChange={(value) =>
							setAttributes({ slidesPerView: value })
						}
						min={1}
						max={6}
						help={
							requiresSingleSlideEffect
								? singleSlideNotice
								: __(
										'Number of slides visible at once',
										'airo-wp'
									)
						}
						disabled={requiresSingleSlideEffect}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<RangeControl
						label={__('Slides Per View (Tablet)', 'airo-wp')}
						value={slidesPerViewTablet}
						onChange={(value) =>
							setAttributes({ slidesPerViewTablet: value })
						}
						min={1}
						max={4}
						help={
							requiresSingleSlideEffect ? singleSlideNotice : ''
						}
						disabled={requiresSingleSlideEffect}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<RangeControl
						label={__('Slides Per View (Mobile)', 'airo-wp')}
						value={slidesPerViewMobile}
						onChange={(value) =>
							setAttributes({ slidesPerViewMobile: value })
						}
						min={1}
						max={2}
						help={
							requiresSingleSlideEffect ? singleSlideNotice : ''
						}
						disabled={requiresSingleSlideEffect}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					{requiresSingleSlideEffect && (
						<Notice status="info" isDismissible={false}>
							{singleSlideNotice}
						</Notice>
					)}

					<ToggleControl
						label={__('Use Aspect Ratio', 'airo-wp')}
						checked={useAspectRatio}
						onChange={(value) =>
							setAttributes({ useAspectRatio: value })
						}
						help={
							useAspectRatio
								? __('Slider uses aspect ratio', 'airo-wp')
								: __(
										'Slider uses minimum height — content can grow taller',
										'airo-wp'
									)
						}
						__nextHasNoMarginBottom
					/>

					{useAspectRatio ? (
						<SelectControl
							label={__('Aspect Ratio', 'airo-wp')}
							value={aspectRatio}
							options={[
								{ label: '16:9', value: '16/9' },
								{ label: '4:3', value: '4/3' },
								{ label: '21:9', value: '21/9' },
								{ label: '1:1', value: '1/1' },
								{ label: '3:2', value: '3/2' },
							]}
							onChange={(value) =>
								setAttributes({ aspectRatio: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					) : (
						<UnitControl
							label={__('Min Height', 'airo-wp')}
							value={height}
							onChange={(value) =>
								setAttributes({ height: value || '' })
							}
							units={[
								{ value: 'px', label: 'px', default: 500 },
								{ value: 'vh', label: 'vh', default: 50 },
								{ value: 'rem', label: 'rem', default: 30 },
							]}
							min={100}
							max={1000}
							help={
								!height
									? __(
											'No height set — slider fits its content',
											'airo-wp'
										)
									: __(
											'Slider will be at least this tall',
											'airo-wp'
										)
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}

					<UnitControl
						label={__('Gap Between Slides', 'airo-wp')}
						value={gap}
						onChange={(value) =>
							setAttributes({ gap: value || '20px' })
						}
						units={[
							{ value: 'px', label: 'px', default: 20 },
							{ value: 'rem', label: 'rem', default: 1.25 },
						]}
						min={0}
						max={64}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				<PanelBody
					title={__('Navigation Settings', 'airo-wp')}
					initialOpen={false}
				>
					<ToggleControl
						label={__('Show Arrows', 'airo-wp')}
						checked={showArrows}
						onChange={(value) =>
							setAttributes({ showArrows: value })
						}
						__nextHasNoMarginBottom
					/>

					{showArrows && (
						<>
							<SelectControl
								label={__('Arrow Style', 'airo-wp')}
								value={arrowStyle}
								options={[
									{
										label: __('Default', 'airo-wp'),
										value: 'default',
									},
									{
										label: __('Circle', 'airo-wp'),
										value: 'circle',
									},
									{
										label: __('Square', 'airo-wp'),
										value: 'square',
									},
									{
										label: __('Minimal', 'airo-wp'),
										value: 'minimal',
									},
								]}
								onChange={(value) =>
									setAttributes({ arrowStyle: value })
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>

							<SelectControl
								label={__(
									'Arrow Position (Horizontal)',
									'airo-wp'
								)}
								value={arrowPosition}
								options={[
									{
										label: __('Sides', 'airo-wp'),
										value: 'sides',
									},
									{
										label: __('Inside', 'airo-wp'),
										value: 'inside',
									},
									{
										label: __('Outside', 'airo-wp'),
										value: 'outside',
									},
								]}
								onChange={(value) =>
									setAttributes({ arrowPosition: value })
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>

							<SelectControl
								label={__(
									'Arrow Position (Vertical)',
									'airo-wp'
								)}
								value={arrowVerticalPosition}
								options={[
									{
										label: __('Top', 'airo-wp'),
										value: 'top',
									},
									{
										label: __('Center', 'airo-wp'),
										value: 'center',
									},
									{
										label: __('Bottom', 'airo-wp'),
										value: 'bottom',
									},
								]}
								onChange={(value) =>
									setAttributes({
										arrowVerticalPosition: value,
									})
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</>
					)}

					{showArrows && (
						<>
							<UnitControl
								label={__('Arrow Size', 'airo-wp')}
								value={arrowSize}
								onChange={(value) =>
									setAttributes({ arrowSize: value })
								}
								units={[
									{ value: 'px', label: 'px' },
									{ value: 'rem', label: 'rem' },
									{ value: 'em', label: 'em' },
								]}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>

							<UnitControl
								label={__('Arrow Padding', 'airo-wp')}
								value={arrowPadding}
								onChange={(value) =>
									setAttributes({ arrowPadding: value })
								}
								units={[
									{ value: 'px', label: 'px' },
									{ value: 'rem', label: 'rem' },
									{ value: 'em', label: 'em' },
								]}
								help={__(
									'Inner spacing of the arrow button',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</>
					)}

					<ToggleControl
						label={__('Show Dots', 'airo-wp')}
						checked={showDots}
						onChange={(value) => setAttributes({ showDots: value })}
						__nextHasNoMarginBottom
					/>

					{showDots && (
						<>
							<SelectControl
								label={__('Dot Style', 'airo-wp')}
								value={dotStyle}
								options={[
									{
										label: __('Default', 'airo-wp'),
										value: 'default',
									},
									{
										label: __('Lines', 'airo-wp'),
										value: 'lines',
									},
									{
										label: __('Squares', 'airo-wp'),
										value: 'squares',
									},
								]}
								onChange={(value) =>
									setAttributes({ dotStyle: value })
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>

							<SelectControl
								label={__('Dot Position', 'airo-wp')}
								value={dotPosition}
								options={[
									{
										label: __('Inside', 'airo-wp'),
										value: 'inside',
									},
									{
										label: __('Outside', 'airo-wp'),
										value: 'outside',
									},
								]}
								onChange={(value) =>
									setAttributes({ dotPosition: value })
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</>
					)}
				</PanelBody>

				<PanelBody
					title={__('Transition Settings', 'airo-wp')}
					initialOpen={false}
				>
					<SelectControl
						label={__('Transition Effect', 'airo-wp')}
						value={effect}
						options={[
							{
								label: __('Slide', 'airo-wp'),
								value: 'slide',
							},
							{ label: __('Fade', 'airo-wp'), value: 'fade' },
							{ label: __('Zoom', 'airo-wp'), value: 'zoom' },
						]}
						onChange={handleEffectChange}
						help={__(
							'Animation style between slides',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<UnitControl
						label={__('Transition Duration', 'airo-wp')}
						value={transitionDuration}
						onChange={(value) =>
							setAttributes({
								transitionDuration: value || '0.5s',
							})
						}
						units={[
							{ value: 's', label: 's', default: 0.5 },
							{ value: 'ms', label: 'ms', default: 500 },
						]}
						min={0.1}
						max={2}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<SelectControl
						label={__('Transition Easing', 'airo-wp')}
						value={transitionEasing}
						options={[
							{ label: __('Ease', 'airo-wp'), value: 'ease' },
							{
								label: __('Ease In Out', 'airo-wp'),
								value: 'ease-in-out',
							},
							{
								label: __('Ease In', 'airo-wp'),
								value: 'ease-in',
							},
							{
								label: __('Ease Out', 'airo-wp'),
								value: 'ease-out',
							},
							{
								label: __('Linear', 'airo-wp'),
								value: 'linear',
							},
						]}
						onChange={(value) =>
							setAttributes({ transitionEasing: value })
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				<PanelBody
					title={__('Auto-play Settings', 'airo-wp')}
					initialOpen={false}
				>
					<ToggleControl
						label={__('Enable Auto-play', 'airo-wp')}
						checked={autoplay}
						onChange={(value) => setAttributes({ autoplay: value })}
						help={
							autoplay
								? __(
										'Slides advance automatically',
										'airo-wp'
									)
								: __('Manual navigation only', 'airo-wp')
						}
						__nextHasNoMarginBottom
					/>

					{autoplay && (
						<>
							<RangeControl
								label={__(
									'Auto-play Interval (ms)',
									'airo-wp'
								)}
								value={autoplayInterval}
								onChange={(value) =>
									setAttributes({ autoplayInterval: value })
								}
								min={1000}
								max={10000}
								step={500}
								help={__('Time between slides', 'airo-wp')}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>

							<ToggleControl
								label={__('Pause on Hover', 'airo-wp')}
								checked={pauseOnHover}
								onChange={(value) =>
									setAttributes({ pauseOnHover: value })
								}
								__nextHasNoMarginBottom
							/>

							<ToggleControl
								label={__(
									'Pause on Interaction',
									'airo-wp'
								)}
								checked={pauseOnInteraction}
								onChange={(value) =>
									setAttributes({ pauseOnInteraction: value })
								}
								help={__(
									'Pause after user clicks, swipes, or drags',
									'airo-wp'
								)}
								__nextHasNoMarginBottom
							/>
						</>
					)}
				</PanelBody>

				<PanelBody
					title={__('Behavior Settings', 'airo-wp')}
					initialOpen={false}
				>
					<ToggleControl
						label={__('Scroll-Driven Horizontal', 'airo-wp')}
						checked={scrollDriven}
						onChange={(value) => {
							const updates = { scrollDriven: value };
							if (value) {
								updates.autoplay = false;
								updates.loop = false;
								updates.effect = 'slide';
							}
							setAttributes(updates);
						}}
						help={
							scrollDriven
								? __(
										'Vertical scrolling drives horizontal slide navigation. Autoplay and loop are disabled.',
										'airo-wp'
									)
								: __(
										'Enable to scroll through slides horizontally as the user scrolls down the page',
										'airo-wp'
									)
						}
						__nextHasNoMarginBottom
					/>

					{scrollDriven && (
						<RangeControl
							label={__('Scroll Speed', 'airo-wp')}
							value={scrollDrivenSpeed}
							onChange={(value) =>
								setAttributes({ scrollDrivenSpeed: value })
							}
							min={0.5}
							max={3}
							step={0.5}
							help={__(
								'Controls how much scrolling is needed to traverse all slides. Higher = more scroll distance.',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}

					{scrollDriven && (
						<Notice status="info" isDismissible={false}>
							{__(
								'Scroll-driven mode pins the slider in the viewport and uses vertical scroll to navigate horizontally through slides. Arrows, dots, autoplay, and loop are disabled.',
								'airo-wp'
							)}
						</Notice>
					)}

					<ToggleControl
						label={__('Loop', 'airo-wp')}
						checked={loop}
						onChange={(value) => setAttributes({ loop: value })}
						help={
							loop
								? __('Infinite loop navigation', 'airo-wp')
								: __('Stop at first/last slide', 'airo-wp')
						}
						disabled={scrollDriven}
						__nextHasNoMarginBottom
					/>

					<ToggleControl
						label={__('Swipeable (Touch)', 'airo-wp')}
						checked={swipeable}
						onChange={(value) =>
							setAttributes({ swipeable: value })
						}
						disabled={scrollDriven}
						__nextHasNoMarginBottom
					/>

					<ToggleControl
						label={__('Draggable (Mouse)', 'airo-wp')}
						checked={draggable}
						onChange={(value) =>
							setAttributes({ draggable: value })
						}
						disabled={scrollDriven}
						__nextHasNoMarginBottom
					/>

					<ToggleControl
						label={__('Free Mode', 'airo-wp')}
						checked={freeMode}
						onChange={(value) => setAttributes({ freeMode: value })}
						help={__(
							'Smooth scrolling without snap points',
							'airo-wp'
						)}
						__nextHasNoMarginBottom
					/>

					<ToggleControl
						label={__('Centered Slides', 'airo-wp')}
						checked={centeredSlides}
						onChange={(value) =>
							setAttributes({ centeredSlides: value })
						}
						help={__(
							'Active slide centered in view',
							'airo-wp'
						)}
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				<PanelBody
					title={__('Advanced Settings', 'airo-wp')}
					initialOpen={false}
				>
					<RangeControl
						label={__('Mobile Breakpoint (px)', 'airo-wp')}
						value={mobileBreakpoint}
						onChange={(value) =>
							setAttributes({ mobileBreakpoint: value })
						}
						min={320}
						max={900}
						help={__(
							'Below this width, uses mobile slides per view',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<RangeControl
						label={__('Tablet Breakpoint (px)', 'airo-wp')}
						value={tabletBreakpoint}
						onChange={(value) =>
							setAttributes({ tabletBreakpoint: value })
						}
						min={768}
						max={1280}
						help={__(
							'Below this width, uses tablet slides per view',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<TextControl
						label={__('ARIA Label', 'airo-wp')}
						value={ariaLabel}
						onChange={(value) =>
							setAttributes({ ariaLabel: value })
						}
						help={__(
							'Accessible label for screen readers',
							'airo-wp'
						)}
						placeholder={__('Image slider', 'airo-wp')}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			{showArrows && (
				<InspectorControls group="color">
					<ColorGradientSettingsDropdown
						panelId={clientId}
						title={__('Arrow Colors', 'airo-wp')}
						settings={arrowColorSettings}
						{...colorGradientSettings}
					/>
				</InspectorControls>
			)}

			{showDots && (
				<InspectorControls group="color">
					<ColorGradientSettingsDropdown
						panelId={clientId}
						title={__('Dot Color', 'airo-wp')}
						settings={[
							{
								label: __('Dot Color', 'airo-wp'),
								colorValue: decodeColorValue(
									dotColor,
									colorGradientSettings
								),
								onColorChange: (color) =>
									setAttributes({
										dotColor:
											encodeColorValue(
												color,
												colorGradientSettings
											) || '',
									}),
								enableAlpha: true,
								clearable: true,
							},
						]}
						{...colorGradientSettings}
					/>
				</InspectorControls>
			)}

			<div
				{...blockProps}
				onClickCapture={(event) => {
					// Kill link navigation inside the editor — real anchors come
					// from authored post-title/featured-image blocks and from
					// server-rendered readonly slides in query mode.
					const anchor = event.target.closest?.('a[href]');
					if (anchor) {
						event.preventDefault();
					}
				}}
			>
				<div className="airo-wp-slider__viewport">
					{inQueryMode ? (
						<QueryModeTrack
							innerBlocksProps={innerBlocksProps}
							preview={preview}
							parentQueryAttrs={parentQueryAttrs}
							outerContext={context}
						/>
					) : (
						<div {...innerBlocksProps} />
					)}
				</div>

				{/* Editor-only slide navigator */}
				{slides.length > 0 && (
					<div
						className="airo-wp-slider__nav airo-wp-slider__nav--editor-only"
						role="toolbar"
						aria-label={__('Slides', 'airo-wp')}
					>
						{slides.map((slide, index) => (
							<div
								key={slide.clientId}
								className={classnames('airo-wp-slider__nav-chip', {
									'is-active':
										selectedSlideId === slide.clientId,
								})}
							>
								<button
									type="button"
									className="airo-wp-slider__nav-chip-label"
									onClick={() => selectBlock(slide.clientId)}
								>
									<span className="airo-wp-slider__nav-chip-index">
										{index + 1}
									</span>
									<span className="airo-wp-slider__nav-chip-title">
										{getSlideLabel(slide, index)}
									</span>
								</button>
								<div className="airo-wp-slider__nav-chip-actions">
									<Tooltip
										text={__(
											'Duplicate slide',
											'airo-wp'
										)}
									>
										<Button
											size="small"
											icon={copy}
											label={__(
												'Duplicate slide',
												'airo-wp'
											)}
											disabled={inQueryMode}
											onClick={() =>
												handleDuplicateSlide(
													slide,
													index
												)
											}
										/>
									</Tooltip>
									<Tooltip
										text={__('Remove slide', 'airo-wp')}
									>
										<Button
											size="small"
											icon={trash}
											isDestructive
											label={__(
												'Remove slide',
												'airo-wp'
											)}
											disabled={inQueryMode}
											onClick={() =>
												handleRemoveSlide(slide)
											}
										/>
									</Tooltip>
								</div>
							</div>
						))}
						<Button
							size="small"
							icon={plus}
							className="airo-wp-slider__nav-add"
							disabled={inQueryMode}
							onClick={handleAddSlide}
						>
							{__('Add slide', 'airo-wp')}
						</Button>
					</div>
				)}

				{/* Editor-only navigation - functional scroll controls */}
				{showArrows && (
					<div className="airo-wp-slider__arrows airo-wp-slider__arrows--editor-only">
						<button
							type="button"
							className="airo-wp-slider__arrow airo-wp-slider__arrow--prev"
							aria-label={__('Previous slide', 'airo-wp')}
							onClick={() => scrollToSlide('prev')}
						>
							<span>‹</span>
						</button>
						<button
							type="button"
							className="airo-wp-slider__arrow airo-wp-slider__arrow--next"
							aria-label={__('Next slide', 'airo-wp')}
							onClick={() => scrollToSlide('next')}
						>
							<span>›</span>
						</button>
					</div>
				)}

				{showDots && (
					<div className="airo-wp-slider__dots airo-wp-slider__dots--editor-only">
						{Array.from({ length: slideCount }, (_, i) => (
							<button
								key={i}
								type="button"
								className="airo-wp-slider__dot"
								onClick={() => scrollToSlideIndex(i)}
							>
								<span className="screen-reader-text">
									{sprintf(
										/* translators: %d: slide number */
										__('Slide %d', 'airo-wp'),
										i + 1
									)}
								</span>
							</button>
						))}
					</div>
				)}
			</div>
		</>
	);
}

/**
 * Render the slider track in query-bound mode: item 0 wraps the editable
 * InnerBlocks slot (the template slide), items 1..N are read-only server-
 * rendered slides. Each item is wrapped in a BlockContextProvider so any
 * Block Bindings inside the template resolve against the iterated post.
 * @param root0
 * @param root0.innerBlocksProps
 * @param root0.preview
 * @param root0.parentQueryAttrs
 * @param root0.outerContext
 */
function QueryModeTrack({
	innerBlocksProps,
	preview,
	parentQueryAttrs,
	outerContext,
}) {
	const source = parentQueryAttrs?.source || 'posts';
	const { records, hasResolved, serverHtml, loading } = preview;

	if (!hasResolved) {
		return (
			<div className="airo-wp-slider__track airo-wp-slider__track--query-loading">
				<div {...innerBlocksProps} />
			</div>
		);
	}

	const items = Array.isArray(records) ? records : [];

	if (items.length === 0) {
		return (
			<div className="airo-wp-slider__track airo-wp-slider__track--query-empty">
				<div {...innerBlocksProps} />
				<div
					className="airo-wp-slider__editor-empty-hint"
					contentEditable={false}
					aria-hidden="true"
				>
					{__(
						'No posts match the parent query. Design the template slide above \u2014 it will render once per result at publish time.',
						'airo-wp'
					)}
				</div>
			</div>
		);
	}

	return (
		<div className="airo-wp-slider__track">
			{items.map((item, idx) => {
				const itemContext = buildItemContext(
					item,
					source,
					idx,
					outerContext
				);
				return (
					<BlockContextProvider
						key={item.id ?? idx}
						value={itemContext}
					>
						{idx === 0 ? (
							<div {...innerBlocksProps} />
						) : (
							<QueryHostReadOnlyItem
								className="airo-wp-slider__editor-readonly-item"
								html={serverHtml?.[idx] ?? null}
								loading={loading}
							/>
						)}
					</BlockContextProvider>
				);
			})}
		</div>
	);
}

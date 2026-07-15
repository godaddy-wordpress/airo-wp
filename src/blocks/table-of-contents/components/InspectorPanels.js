/**
 * Inspector Control Panels for Table of Contents Block
 *
 * Each exported component renders a React Fragment of
 * DsgoInspectorPanel.Item entries. Meant to be composed inside the
 * Settings DsgoInspectorPanel in table-of-contents/edit.js.
 */
import { __ } from '@wordpress/i18n';
import {
	ToggleControl,
	TextControl,
	RangeControl,
	CheckboxControl,
	RadioControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared';

export function HeadingLevelsPanel({ attributes, setAttributes }) {
	const { includeH2, includeH3, includeH4, includeH5, includeH6 } =
		attributes;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Include H2', 'airo-wp')}
				hasValue={() => includeH2 !== true}
				onDeselect={() => setAttributes({ includeH2: true })}
				isShownByDefault
			>
				<CheckboxControl
					label={__('Include H2', 'airo-wp')}
					checked={includeH2}
					onChange={(value) => setAttributes({ includeH2: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Include H3', 'airo-wp')}
				hasValue={() => includeH3 !== true}
				onDeselect={() => setAttributes({ includeH3: true })}
				isShownByDefault
			>
				<CheckboxControl
					label={__('Include H3', 'airo-wp')}
					checked={includeH3}
					onChange={(value) => setAttributes({ includeH3: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Include H4', 'airo-wp')}
				hasValue={() => includeH4 !== false}
				onDeselect={() => setAttributes({ includeH4: false })}
				isShownByDefault
			>
				<CheckboxControl
					label={__('Include H4', 'airo-wp')}
					checked={includeH4}
					onChange={(value) => setAttributes({ includeH4: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Include H5', 'airo-wp')}
				hasValue={() => includeH5 !== false}
				onDeselect={() => setAttributes({ includeH5: false })}
				isShownByDefault
			>
				<CheckboxControl
					label={__('Include H5', 'airo-wp')}
					checked={includeH5}
					onChange={(value) => setAttributes({ includeH5: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Include H6', 'airo-wp')}
				hasValue={() => includeH6 !== false}
				onDeselect={() => setAttributes({ includeH6: false })}
				isShownByDefault
			>
				<CheckboxControl
					label={__('Include H6', 'airo-wp')}
					checked={includeH6}
					onChange={(value) => setAttributes({ includeH6: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>
		</>
	);
}

export function DisplaySettingsPanel({ attributes, setAttributes }) {
	const { displayMode, listStyle } = attributes;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Display Mode', 'airo-wp')}
				hasValue={() => displayMode !== 'hierarchical'}
				onDeselect={() =>
					setAttributes({ displayMode: 'hierarchical' })
				}
				isShownByDefault
			>
				<RadioControl
					label={__('Display Mode', 'airo-wp')}
					selected={displayMode}
					options={[
						{
							label: __('Hierarchical (Nested)', 'airo-wp'),
							value: 'hierarchical',
						},
						{
							label: __('Flat List', 'airo-wp'),
							value: 'flat',
						},
					]}
					onChange={(value) => setAttributes({ displayMode: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('List Style', 'airo-wp')}
				hasValue={() => listStyle !== 'unordered'}
				onDeselect={() => setAttributes({ listStyle: 'unordered' })}
				isShownByDefault
			>
				<RadioControl
					label={__('List Style', 'airo-wp')}
					selected={listStyle}
					options={[
						{
							label: __('Unordered (Bullets)', 'airo-wp'),
							value: 'unordered',
						},
						{
							label: __('Ordered (Numbers)', 'airo-wp'),
							value: 'ordered',
						},
					]}
					onChange={(value) => setAttributes({ listStyle: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>
		</>
	);
}

export function TitleSettingsPanel({ attributes, setAttributes }) {
	const { showTitle, titleText } = attributes;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Show Title', 'airo-wp')}
				hasValue={() => showTitle !== true}
				onDeselect={() => setAttributes({ showTitle: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show Title', 'airo-wp')}
					checked={showTitle}
					onChange={(value) => setAttributes({ showTitle: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{showTitle && (
				<DsgoInspectorPanel.Item
					label={__('Title Text', 'airo-wp')}
					hasValue={() => titleText !== 'Table of Contents'}
					onDeselect={() =>
						setAttributes({ titleText: 'Table of Contents' })
					}
					isShownByDefault
				>
					<TextControl
						label={__('Title Text', 'airo-wp')}
						value={titleText}
						onChange={(value) =>
							setAttributes({ titleText: value })
						}
						placeholder={__('Table of Contents', 'airo-wp')}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}
		</>
	);
}

export function ScrollSettingsPanel({ attributes, setAttributes }) {
	const { scrollSmooth, scrollOffset, stickyOffset } = attributes;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Smooth Scroll', 'airo-wp')}
				hasValue={() => scrollSmooth !== true}
				onDeselect={() => setAttributes({ scrollSmooth: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Smooth Scroll', 'airo-wp')}
					help={__(
						'Enable smooth scrolling when clicking links',
						'airo-wp'
					)}
					checked={scrollSmooth}
					onChange={(value) => setAttributes({ scrollSmooth: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Scroll Offset', 'airo-wp')}
				hasValue={() => scrollOffset !== 0}
				onDeselect={() => setAttributes({ scrollOffset: 0 })}
				isShownByDefault
			>
				<RangeControl
					label={__('Scroll Offset', 'airo-wp')}
					help={__(
						'Offset from top when scrolling to headings (useful for sticky headers)',
						'airo-wp'
					)}
					value={scrollOffset}
					onChange={(value) => setAttributes({ scrollOffset: value })}
					min={0}
					max={200}
					step={10}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Sticky Position Offset', 'airo-wp')}
				hasValue={() => stickyOffset !== 0}
				onDeselect={() => setAttributes({ stickyOffset: 0 })}
				isShownByDefault
			>
				<RangeControl
					label={__('Sticky Position Offset', 'airo-wp')}
					help={__(
						'Offset from top when this block is sticky (prevents overlap with sticky headers)',
						'airo-wp'
					)}
					value={stickyOffset}
					onChange={(value) => setAttributes({ stickyOffset: value })}
					min={0}
					max={200}
					step={10}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>
		</>
	);
}

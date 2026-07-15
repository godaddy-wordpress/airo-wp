/**
 * Inspector Control Panels for Breadcrumbs Block
 *
 * Renders DsgoInspectorPanel.Item entries for the breadcrumbs display
 * attributes. Meant to be composed inside the Settings DsgoInspectorPanel
 * in breadcrumbs/edit.js.
 */
import { __ } from '@wordpress/i18n';
import {
	ToggleControl,
	TextControl,
	SelectControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared';

export function DisplaySettingsPanel({ attributes, setAttributes }) {
	const {
		showHome,
		homeText,
		separator,
		showCurrent,
		linkCurrent,
		prefixText,
		hideOnHome,
	} = attributes;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Show home link', 'airo-wp')}
				hasValue={() => showHome !== true}
				onDeselect={() => setAttributes({ showHome: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show home link', 'airo-wp')}
					checked={showHome}
					onChange={(value) => setAttributes({ showHome: value })}
					help={__(
						'Display a link to the homepage at the start of the breadcrumb trail',
						'airo-wp'
					)}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{showHome && (
				<DsgoInspectorPanel.Item
					label={__('Home text', 'airo-wp')}
					hasValue={() => homeText !== 'Home'}
					onDeselect={() => setAttributes({ homeText: 'Home' })}
					isShownByDefault
				>
					<TextControl
						label={__('Home text', 'airo-wp')}
						value={homeText}
						onChange={(value) => setAttributes({ homeText: value })}
						help={__(
							'Text to display for the home link',
							'airo-wp'
						)}
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			<DsgoInspectorPanel.Item
				label={__('Separator', 'airo-wp')}
				hasValue={() => separator !== 'slash'}
				onDeselect={() => setAttributes({ separator: 'slash' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Separator', 'airo-wp')}
					value={separator}
					options={[
						{ label: '/', value: 'slash' },
						{ label: '›', value: 'chevron' },
						{ label: '>', value: 'greater' },
						{ label: '•', value: 'bullet' },
						{ label: '→', value: 'arrow-right' },
					]}
					onChange={(value) => setAttributes({ separator: value })}
					help={__(
						'Character used to separate breadcrumb items',
						'airo-wp'
					)}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Show current page', 'airo-wp')}
				hasValue={() => showCurrent !== true}
				onDeselect={() => setAttributes({ showCurrent: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show current page', 'airo-wp')}
					checked={showCurrent}
					onChange={(value) => setAttributes({ showCurrent: value })}
					help={__(
						'Display the current page in the breadcrumb trail',
						'airo-wp'
					)}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{showCurrent && (
				<DsgoInspectorPanel.Item
					label={__('Link current page', 'airo-wp')}
					hasValue={() => linkCurrent !== false}
					onDeselect={() => setAttributes({ linkCurrent: false })}
					isShownByDefault
				>
					<ToggleControl
						label={__('Link current page', 'airo-wp')}
						checked={linkCurrent}
						onChange={(value) =>
							setAttributes({ linkCurrent: value })
						}
						help={__(
							'Make the current page a clickable link',
							'airo-wp'
						)}
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			<DsgoInspectorPanel.Item
				label={__('Prefix text', 'airo-wp')}
				hasValue={() => prefixText !== ''}
				onDeselect={() => setAttributes({ prefixText: '' })}
				isShownByDefault
			>
				<TextControl
					label={__('Prefix text', 'airo-wp')}
					value={prefixText}
					onChange={(value) => setAttributes({ prefixText: value })}
					help={__(
						'Optional text to display before the breadcrumb trail (e.g., "You are here:")',
						'airo-wp'
					)}
					placeholder={__('You are here:', 'airo-wp')}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Hide on homepage', 'airo-wp')}
				hasValue={() => hideOnHome !== true}
				onDeselect={() => setAttributes({ hideOnHome: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Hide on homepage', 'airo-wp')}
					checked={hideOnHome}
					onChange={(value) => setAttributes({ hideOnHome: value })}
					help={__(
						'Hide breadcrumbs when viewing the homepage',
						'airo-wp'
					)}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>
		</>
	);
}

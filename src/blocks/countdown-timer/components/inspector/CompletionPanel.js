/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { RadioControl, TextControl } from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../../components/shared';

const DEFAULT_COMPLETION_MESSAGE = 'The countdown has ended!';

/**
 * Completion Panel component
 *
 * Renders DsgoInspectorPanel.Item entries for the end-of-countdown
 * behaviour. Meant to be composed inside the Settings DsgoInspectorPanel
 * in countdown-timer/edit.js.
 *
 * @param {Object}   props               - Component properties
 * @param {Object}   props.attributes    - Block attributes
 * @param {Function} props.setAttributes - Function to update attributes
 * @return {JSX.Element} Item fragment
 */
export default function CompletionPanel({ attributes, setAttributes }) {
	const { completionAction, completionMessage } = attributes;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('When Countdown Ends', 'airo-wp')}
				hasValue={() => completionAction !== 'message'}
				onDeselect={() =>
					setAttributes({ completionAction: 'message' })
				}
				isShownByDefault
			>
				<RadioControl
					label={__('When Countdown Ends', 'airo-wp')}
					selected={completionAction}
					options={[
						{
							label: __('Show Custom Message', 'airo-wp'),
							value: 'message',
						},
						{
							label: __('Hide Timer Completely', 'airo-wp'),
							value: 'hide',
						},
					]}
					onChange={(value) =>
						setAttributes({ completionAction: value })
					}
				/>
			</DsgoInspectorPanel.Item>

			{completionAction === 'message' && (
				<DsgoInspectorPanel.Item
					label={__('Completion Message', 'airo-wp')}
					hasValue={() =>
						completionMessage !== DEFAULT_COMPLETION_MESSAGE
					}
					onDeselect={() =>
						setAttributes({
							completionMessage: DEFAULT_COMPLETION_MESSAGE,
						})
					}
					isShownByDefault
				>
					<TextControl
						label={__('Completion Message', 'airo-wp')}
						value={completionMessage}
						onChange={(value) =>
							setAttributes({ completionMessage: value })
						}
						placeholder={__('The countdown has ended!', 'airo-wp')}
						help={__(
							'This message will replace the countdown timer when it reaches zero.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}
		</>
	);
}

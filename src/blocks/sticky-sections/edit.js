/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import './editor.scss';
import StickySectionsPlaceholder from './components/StickySectionsPlaceholder';

const ALLOWED_BLOCKS = ['airo-wp/section'];

export default function Edit({ attributes, setAttributes, clientId }) {
	const { stickyOffset } = attributes;

	const hasInnerBlocks = useSelect(
		(select) => {
			const { getBlock } = select(blockEditorStore);
			const block = getBlock(clientId);
			return block?.innerBlocks?.length > 0;
		},
		[clientId]
	);

	const blockProps = useBlockProps({
		className: 'airo-wp-sticky-sections',
	});

	const innerBlocksProps = useInnerBlocksProps(blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		orientation: 'vertical',
	});

	// Show template chooser when block is first inserted
	if (!hasInnerBlocks) {
		return (
			<div {...blockProps}>
				<StickySectionsPlaceholder
					clientId={clientId}
					setAttributes={setAttributes}
				/>
			</div>
		);
	}

	return (
		<>
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() => setAttributes({ stickyOffset: '0px' })}
				>
					<DsgoInspectorPanel.Item
						label={__('Sticky Offset', 'airo-wp')}
						hasValue={() => stickyOffset !== '0px'}
						onDeselect={() =>
							setAttributes({ stickyOffset: '0px' })
						}
						isShownByDefault
					>
						<UnitControl
							label={__('Sticky Offset', 'airo-wp')}
							value={stickyOffset}
							onChange={(value) =>
								setAttributes({ stickyOffset: value })
							}
							help={__(
								'Offset from the top of the viewport. Useful when your site has a fixed header.',
								'airo-wp'
							)}
							units={[
								{ value: 'px', label: 'px' },
								{ value: 'rem', label: 'rem' },
								{ value: 'vh', label: 'vh' },
							]}
							__next40pxDefaultSize
						/>
					</DsgoInspectorPanel.Item>
				</DsgoInspectorPanel>
			</InspectorControls>

			<div {...innerBlocksProps} />
		</>
	);
}

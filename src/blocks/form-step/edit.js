/**
 * Form Step — editor.
 *
 * Steps stack in the canvas: a header with the step number and an inline
 * title, then the step's fields. The title is edited only on the canvas: a
 * second, plain-text editor would store `&` differently from the RichText.
 *
 * @since 2.10.0
 */

import classnames from 'classnames';
import { __, sprintf } from '@wordpress/i18n';
import {
	useBlockProps,
	__experimentalUseColorProps as useColorProps,
	getTypographyClassesAndStyles,
	useInnerBlocksProps,
	InnerBlocks,
	RichText,
	BlockControls,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { DsgoChildToolbar } from '../../components/shared';
import { FORM_FIELD_BLOCKS } from '../form-builder/utils/field-blocks';

export default function FormStepEdit({ attributes, setAttributes, clientId }) {
	const { title } = attributes;
	const { rootClientId, index, siblingCount } = useSelect(
		(select) => {
			const store = select('core/block-editor');
			const root = store.getBlockRootClientId(clientId);
			return {
				rootClientId: root,
				index: store.getBlockIndex(clientId),
				siblingCount: store.getBlockCount(root),
			};
		},
		[clientId]
	);

	const blockProps = useBlockProps({ className: 'airo-wp-form-step' });
	// Colour and typography skip serialization on the wrapper and style the
	// title instead, as render.php does with airowp_route_visual_supports().
	const colorProps = useColorProps(attributes);
	const typographyProps = getTypographyClassesAndStyles(attributes);
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'airo-wp-form-step__fields' },
		{
			allowedBlocks: FORM_FIELD_BLOCKS,
			orientation: 'vertical',
			renderAppender: InnerBlocks.ButtonBlockAppender,
		}
	);

	return (
		<>
			<BlockControls>
				<DsgoChildToolbar
					parentClientId={rootClientId}
					childBlockName="airo-wp/form-step"
					activeIndex={index}
					orientation="vertical"
					addLabel={__('Add step', 'airo-wp')}
					duplicateLabel={__('Duplicate step', 'airo-wp')}
					removeLabel={__('Remove step', 'airo-wp')}
					movePrevLabel={__('Move step up', 'airo-wp')}
					moveNextLabel={__('Move step down', 'airo-wp')}
					disableRemove={siblingCount <= 1}
				/>
			</BlockControls>
			<div {...blockProps}>
				<div className="airo-wp-form-step__header">
					<span className="airo-wp-form-step__number">
						{sprintf(
							/* translators: %d: step number */
							__('Step %d', 'airo-wp'),
							index + 1
						)}
					</span>
					<RichText
						tagName="h3"
						className={classnames(
							'airo-wp-form-step__title',
							colorProps.className,
							typographyProps.className
						)}
						style={{
							...colorProps.style,
							...typographyProps.style,
						}}
						value={title}
						onChange={(value) => setAttributes({ title: value })}
						placeholder={__('Step title', 'airo-wp')}
						allowedFormats={[]}
						withoutInteractiveFormatting
					/>
				</div>
				<div {...innerBlocksProps} />
			</div>
		</>
	);
}

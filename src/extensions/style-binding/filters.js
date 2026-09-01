import { addFilter } from '@wordpress/hooks';
import { __, sprintf } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	Button,
	SelectControl,
	TextControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis -- no stable export in @wordpress/components
	__experimentalHStack as HStack,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis -- no stable export in @wordpress/components
	__experimentalVStack as VStack,
} from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment } from '@wordpress/element';

const BLOCKED = new Set([
	'core/freeform',
	'core/missing',
	'core/template-part',
]);

const SOURCE_OPTIONS = [
	{ label: __('Post meta', 'airo-wp'), value: 'airo-wp/post-meta' },
	{ label: __('ACF', 'airo-wp'), value: 'airo-wp/acf' },
	{ label: __('Meta Box', 'airo-wp'), value: 'airo-wp/metabox' },
	{ label: __('Pods', 'airo-wp'), value: 'airo-wp/pods' },
	{ label: __('JetEngine', 'airo-wp'), value: 'airo-wp/jetengine' },
];

addFilter(
	'blocks.registerBlockType',
	'airo-wp/style-binding-attribute',
	(settings, name) => {
		if (BLOCKED.has(name)) {
			return settings;
		}
		if (!settings.attributes) {
			settings.attributes = {};
		}
		settings.attributes.dsgoStyleBinding = {
			type: 'object',
			default: {},
		};
		return settings;
	}
);

const withStyleBindingInspector = createHigherOrderComponent((BlockEdit) => {
	return function WithStyleBindingInspector(props) {
		if (BLOCKED.has(props.name)) {
			return <BlockEdit {...props} />;
		}
		const { attributes, setAttributes } = props;
		const binding = attributes.dsgoStyleBinding ?? {};
		const entries = Object.entries(binding);

		const updateEntry = (oldProp, newProp, config) => {
			const next = { ...binding };
			if (oldProp !== newProp) {
				delete next[oldProp];
			}
			next[newProp] = config;
			setAttributes({ dsgoStyleBinding: next });
		};

		const removeEntry = (prop) => {
			const next = { ...binding };
			delete next[prop];
			setAttributes({ dsgoStyleBinding: next });
		};

		const addEntry = () => {
			const key = `--airo-wp-binding-${Date.now()}`;
			setAttributes({
				dsgoStyleBinding: {
					...binding,
					[key]: {
						source: 'airo-wp/post-meta',
						args: { key: '' },
					},
				},
			});
		};

		return (
			<Fragment>
				<BlockEdit {...props} />
				<InspectorControls group="advanced">
					<PanelBody
						title={__('Style Bindings', 'airo-wp')}
						initialOpen={entries.length > 0}
					>
						{entries.map(([prop, config]) => (
							<VStack
								key={prop}
								spacing={1}
								style={{ marginBottom: '12px' }}
							>
								<HStack>
									<TextControl
										label={__('CSS property', 'airo-wp')}
										value={prop}
										placeholder="--brand-color"
										onChange={(val) =>
											updateEntry(prop, val, config)
										}
										__nextHasNoMarginBottom
									/>
									<Button
										variant="tertiary"
										isDestructive
										size="small"
										onClick={() => removeEntry(prop)}
										aria-label={sprintf(
											/* translators: %s: CSS property name being unbound. */
											__(
												'Remove style binding for "%s"',
												'airo-wp'
											),
											prop
										)}
										style={{ alignSelf: 'flex-end' }}
									>
										{__('Remove', 'airo-wp')}
									</Button>
								</HStack>
								<SelectControl
									label={__('Source', 'airo-wp')}
									value={config.source}
									options={SOURCE_OPTIONS}
									onChange={(val) =>
										updateEntry(prop, prop, {
											...config,
											source: val,
										})
									}
									__nextHasNoMarginBottom
								/>
								<TextControl
									label={__('Field key / name', 'airo-wp')}
									value={config.args?.key ?? ''}
									onChange={(val) =>
										updateEntry(prop, prop, {
											...config,
											args: { key: val },
										})
									}
									__nextHasNoMarginBottom
								/>
							</VStack>
						))}
						<Button
							variant="secondary"
							size="small"
							onClick={addEntry}
							__next40pxDefaultSize
						>
							{__('+ Add style binding', 'airo-wp')}
						</Button>
					</PanelBody>
				</InspectorControls>
			</Fragment>
		);
	};
}, 'withStyleBindingInspector');

addFilter(
	'editor.BlockEdit',
	'airo-wp/style-binding-inspector',
	withStyleBindingInspector
);

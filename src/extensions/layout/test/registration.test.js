// Use the registry that useBlockProps.save resolves in this checkout.

import {
	createBlock,
	getBlockType,
	parse,
	serialize,
	unregisterBlockType,
} from '@wordpress/block-editor/node_modules/@wordpress/blocks';
import { registerAiroWpBlock } from '../../../../tools/regenerate-patterns';
import { addLayoutAttribute, applyLayoutSaveProps } from '../index';

describe('optional layout registration', () => {
	afterEach(() =>
		['section', 'row', 'grid'].forEach((slug) => {
			if (getBlockType(`airo-wp/${slug}`)) {
				unregisterBlockType(`airo-wp/${slug}`);
			}
		})
	);
	it('extends only the explicit allowlist with no eager default', () => {
		expect(addLayoutAttribute({ attributes: {} }, 'core/html')).toEqual({
			attributes: {},
		});
		expect(
			addLayoutAttribute({ attributes: {} }, 'airo-wp/grid').attributes
				.dsgoLayout
		).toEqual({ type: 'object' });
	});
	it('adds no save props when there are no valid declarations', () => {
		const props = { className: 'existing' };
		expect(applyLayoutSaveProps(props, { name: 'airo-wp/grid' }, {})).toBe(
			props
		);
		expect(
			applyLayoutSaveProps(
				props,
				{ name: 'airo-wp/grid' },
				{ dsgoLayout: {} }
			)
		).toBe(props);
	});
	it.each(['section', 'row', 'grid'])(
		'keeps previous %s markup valid when optional layout is absent or empty',
		(slug) => {
			registerAiroWpBlock(`airo-wp/${slug}`);
			const name = `airo-wp/${slug}`;
			const original = serialize(createBlock(name));
			expect(parse(original)[0].isValid).toBe(true);
			const empty = serialize(createBlock(name, { dsgoLayout: {} }));
			expect(
				empty
					.replace('"dsgoLayout":{},', '')
					.replace(',"dsgoLayout":{}', '')
					.replace('{"dsgoLayout":{}}', '{}')
			).toContain(original.slice(original.indexOf('-->') + 3));
			expect(parse(empty)[0].isValid).toBe(true);
		}
	);
});

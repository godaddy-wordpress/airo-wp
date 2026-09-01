/**
 * Grid Block - save.js Tests
 *
 * Verifies overlay support (new: attribute + style-kit `is-style-overlay-*`
 * variation) and hover style-kit variations
 * (`is-style-hover-{text,icon,button}-*`) emit the matching activation
 * class, mirroring Section/Row's behavior.
 */

import {
	createBlock,
	serialize,
	registerBlockType,
	setCategories,
	// eslint-disable-next-line import/no-unresolved
} from '@wordpress/block-editor/node_modules/@wordpress/blocks';
import metadata from '../block.json';
import save from '../save';

setCategories([{ slug: 'airo-wp', title: 'airo-wp' }]);

registerBlockType(metadata.name, { ...metadata, save });

describe('grid save - overlay class', () => {
	test('no overlay by default', () => {
		const html = serialize(createBlock(metadata.name));
		expect(html).not.toContain('airo-wp-grid--has-overlay');
	});

	test('overlayColor emits overlay class + inline color var', () => {
		const html = serialize(
			createBlock(metadata.name, { overlayColor: '#000000' })
		);
		expect(html).toContain('airo-wp-grid--has-overlay');
		expect(html).toContain('--airo-wp-overlay-color');
	});

	test('is-style-overlay-dark className emits overlay class without inline color var', () => {
		const html = serialize(
			createBlock(metadata.name, { className: 'is-style-overlay-dark' })
		);
		expect(html).toContain('airo-wp-grid--has-overlay');
		expect(html).not.toContain('--airo-wp-overlay-color');
	});

	test('unrelated is-style-* variation does not enable the overlay', () => {
		const html = serialize(
			createBlock(metadata.name, { className: 'is-style-rounded' })
		);
		expect(html).not.toContain('airo-wp-grid--has-overlay');
	});
});

describe('grid save - hover variation activation classes', () => {
	test('no hover activation classes by default', () => {
		const html = serialize(createBlock(metadata.name));
		expect(html).not.toContain('airo-wp-grid--has-hover-text');
		expect(html).not.toContain('airo-wp-grid--has-hover-icon');
		expect(html).not.toContain('airo-wp-grid--has-hover-button');
	});

	test('is-style-hover-text-* emits only the hover-text activation class', () => {
		const html = serialize(
			createBlock(metadata.name, {
				className: 'is-style-hover-text-light',
			})
		);
		expect(html).toContain('airo-wp-grid--has-hover-text');
		expect(html).not.toContain('airo-wp-grid--has-hover-icon');
		expect(html).not.toContain('airo-wp-grid--has-hover-button');
	});

	test('is-style-hover-icon-* emits only the hover-icon activation class', () => {
		const html = serialize(
			createBlock(metadata.name, {
				className: 'is-style-hover-icon-blue',
			})
		);
		expect(html).toContain('airo-wp-grid--has-hover-icon');
		expect(html).not.toContain('airo-wp-grid--has-hover-text');
	});

	test('is-style-hover-button-* emits only the hover-button activation class', () => {
		const html = serialize(
			createBlock(metadata.name, {
				className: 'is-style-hover-button-accent',
			})
		);
		expect(html).toContain('airo-wp-grid--has-hover-button');
		expect(html).not.toContain('airo-wp-grid--has-hover-text');
	});

	test('setting a hover attribute alone does NOT add an activation class (inline gate handles it)', () => {
		const html = serialize(
			createBlock(metadata.name, { hoverTextColor: '#ffffff' })
		);
		expect(html).not.toContain('airo-wp-grid--has-hover-text');
		expect(html).toContain('--airo-wp-hover-text-color');
	});
});

describe('grid save - align rows (match row heights)', () => {
	test('no match-rows class by default', () => {
		const html = serialize(createBlock(metadata.name));
		expect(html).not.toContain('airo-wp-grid--match-rows');
	});

	test('matchRowHeights emits the match-rows class', () => {
		const html = serialize(
			createBlock(metadata.name, { matchRowHeights: true })
		);
		expect(html).toContain('airo-wp-grid--match-rows');
	});

	test('the runtime-only activation class is not serialized', () => {
		// `--airo-wp-row-count` / `--rows-matched` are applied by view.js at
		// runtime, never persisted in save output.
		const html = serialize(
			createBlock(metadata.name, { matchRowHeights: true })
		);
		expect(html).not.toContain('airo-wp-grid__inner--rows-matched');
		expect(html).not.toContain('--airo-wp-row-count');
	});
});

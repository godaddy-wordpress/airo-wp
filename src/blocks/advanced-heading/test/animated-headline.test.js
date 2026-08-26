import {
	createBlock,
	getBlockType,
	registerBlockType,
	serialize,
	setCategories,
} from '@wordpress/block-editor/node_modules/@wordpress/blocks';
import { readFileSync } from 'fs';
import { resolve } from 'path';
import headingMetadata from '../block.json';
import headingSave from '../save';
import segmentMetadata from '../../heading-segment/block.json';
import segmentSave from '../../heading-segment/save';

const editorSource = readFileSync(resolve(__dirname, '../editor.scss'), 'utf8');
const editSource = readFileSync(resolve(__dirname, '../edit.js'), 'utf8');

setCategories([{ slug: 'airo-wp', title: 'airo-wp' }]);

if (!getBlockType(segmentMetadata.name)) {
	registerBlockType(segmentMetadata.name, {
		...segmentMetadata,
		save: segmentSave,
	});
}

if (!getBlockType(headingMetadata.name)) {
	registerBlockType(headingMetadata.name, {
		...headingMetadata,
		save: headingSave,
	});
}

function createHeading(attributes = {}, segmentAttributes = {}) {
	return createBlock(headingMetadata.name, attributes, [
		createBlock(segmentMetadata.name, {
			content: 'Creative',
			...segmentAttributes,
		}),
	]);
}

describe('animated headline save', () => {
	test('keeps a default heading byte-identical to its existing saved markup', () => {
		const html = serialize(createHeading());

		expect(html).toBe(
			'<!-- wp:airo-wp/advanced-heading -->\n<div class="wp-block-airo-wp-advanced-heading airo-wp-advanced-heading"><h2 class="airo-wp-advanced-heading__inner"><!-- wp:airo-wp/heading-segment -->\n<span class="wp-block-airo-wp-heading-segment airo-wp-heading-segment"><span class="airo-wp-heading-segment__text">Creative</span></span>\n<!-- /wp:airo-wp/heading-segment --></h2></div>\n<!-- /wp:airo-wp/advanced-heading -->'
		);
	});

	test('adds an editor-only fallback gap before a non-leading animated segment', () => {
		expect(editorSource).toMatch(
			/\.airo-wp-heading-segment:not\(:first-child\)\s+\.airo-wp-heading-segment__animated\s*\{\s*margin-inline-start:\s*var\(--airo-wp-animated-segment-gap\);/
		);
		expect(editSource).toContain(
			"'--airo-wp-animated-segment-gap': blockGap ? '0' : '.2em'"
		);
	});

	test('saves a bounded rotating headline without encoding link data attributes', () => {
		const html = serialize(
			createHeading(
				{
					animatedHeadline: {
						mode: 'rotating',
						effect: 'untrusted',
						duration: 999999,
						delay: -4,
						loop: true,
						url: 'https://example.com/work',
						target: '_blank',
						rel: 'nofollow',
					},
				},
				{
					headlineRole: 'animated',
					animatedWords: ['Creative', 'Effective'],
				}
			)
		);

		expect(html).toContain('data-airo-wp-animated-headline="true"');
		expect(html).toContain(
			'data-airo-wp-animated-headline-mode="rotating"'
		);
		expect(html).toContain(
			'data-airo-wp-animated-headline-effect="typing"'
		);
		expect(html).toContain(
			'data-airo-wp-animated-headline-duration="10000"'
		);
		expect(html).toContain('data-airo-wp-animated-headline-delay="0"');
		expect(html).toContain('data-airo-wp-animated-headline-loop="true"');
		expect(html).not.toContain('data-airo-wp-animated-headline-url');
		expect(html).not.toContain('data-airo-wp-animated-headline-target');
		expect(html).not.toContain('data-airo-wp-animated-headline-rel');
		expect(html.match(/airo-wp-heading-segment__animated/g)).toHaveLength(
			1
		);
	});

	test('saves a reverse rotation direction only when an author selects it', () => {
		const reverseHtml = serialize(
			createHeading(
				{
					animatedHeadline: {
						mode: 'rotating',
						effect: 'slide',
						direction: 'reverse',
					},
				},
				{
					headlineRole: 'animated',
					animatedWords: ['First', 'Last'],
				}
			)
		);
		const defaultHtml = serialize(
			createHeading(
				{ animatedHeadline: { mode: 'rotating' } },
				{
					headlineRole: 'animated',
					animatedWords: ['First', 'Last'],
				}
			)
		);

		expect(reverseHtml).toContain(
			'data-airo-wp-animated-headline-direction="reverse"'
		);
		expect(defaultHtml).not.toContain(
			'data-airo-wp-animated-headline-direction'
		);
	});

	test('wraps a valid headline URL in a real sanitized anchor', () => {
		const html = serialize(
			createHeading(
				{
					animatedHeadline: {
						mode: 'rotating',
						url: 'https://example.com/work',
						target: '_blank',
						rel: 'nofollow',
					},
				},
				{
					headlineRole: 'animated',
					animatedWords: ['Creative', 'Effective'],
				}
			)
		);

		expect(html).toContain(
			'<a class="airo-wp-advanced-heading__link" href="https://example.com/work" target="_blank" rel="nofollow noopener noreferrer"><h2'
		);
		expect(html).not.toContain('data-airo-wp-animated-headline-url');
	});

	test('does not turn an unsafe headline URL into an anchor', () => {
		const html = serialize(
			createHeading(
				{
					animatedHeadline: {
						mode: 'rotating',
						url: 'javascript:alert(1)',
					},
				},
				{
					headlineRole: 'animated',
					animatedWords: ['Creative', 'Effective'],
				}
			)
		);

		expect(html).not.toContain('airo-wp-advanced-heading__link');
		expect(html).not.toContain('href="javascript:alert(1)"');
	});

	test('nests a decorative highlight SVG with the selected animated segment', () => {
		const html = serialize(
			createBlock(
				headingMetadata.name,
				{
					animatedHeadline: {
						mode: 'highlighted',
						shape: 'circle',
					},
				},
				[
					createBlock(segmentMetadata.name, { content: 'Before ' }),
					createBlock(segmentMetadata.name, {
						headlineRole: 'animated',
						animatedWords: ['Creative'],
						animatedHeadlineShape: 'circle',
					}),
					createBlock(segmentMetadata.name, { content: ' After' }),
				]
			)
		);

		expect(html).toContain('data-airo-wp-animated-headline="true"');
		expect(html).toContain(
			'data-airo-wp-animated-headline-mode="highlighted"'
		);
		expect(html).toContain('data-airo-wp-animated-headline-shape="circle"');
		expect(html).toMatch(
			/<span class="wp-block-airo-wp-heading-segment airo-wp-heading-segment airo-wp-heading-segment--highlighted"><span class="airo-wp-heading-segment__animated"[^>]*>Creative<\/span><svg class="airo-wp-heading-segment__highlight" aria-hidden="true"/
		);
		expect(html).not.toContain('airo-wp-advanced-heading__highlight');
	});
});

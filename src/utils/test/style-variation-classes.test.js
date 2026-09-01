/**
 * Shared style-variation → activation-class detection tests.
 */
import {
	hasOverlayStyleClass,
	hoverVariationClasses,
} from '../style-variation-classes';

describe('hasOverlayStyleClass', () => {
	test('returns false for no className', () => {
		expect(hasOverlayStyleClass(undefined)).toBe(false);
		expect(hasOverlayStyleClass('')).toBe(false);
	});

	test('detects is-style-overlay-* variations', () => {
		expect(hasOverlayStyleClass('is-style-overlay-dark')).toBe(true);
		expect(hasOverlayStyleClass('is-style-overlay-light')).toBe(true);
	});

	test('ignores unrelated classNames', () => {
		expect(hasOverlayStyleClass('is-style-rounded')).toBe(false);
	});
});

describe('hoverVariationClasses', () => {
	test('returns empty array for no className', () => {
		expect(hoverVariationClasses(undefined, 'airo-wp-flex')).toEqual([]);
	});

	test('maps hover-text variation to the given block prefix', () => {
		expect(
			hoverVariationClasses('is-style-hover-text-light', 'airo-wp-flex')
		).toEqual(['airo-wp-flex--has-hover-text']);
	});

	test('maps hover-icon variation to the given block prefix', () => {
		expect(
			hoverVariationClasses('is-style-hover-icon-blue', 'airo-wp-grid')
		).toEqual(['airo-wp-grid--has-hover-icon']);
	});

	test('maps hover-button variation to the given block prefix', () => {
		expect(
			hoverVariationClasses(
				'is-style-hover-button-accent',
				'airo-wp-stack'
			)
		).toEqual(['airo-wp-stack--has-hover-button']);
	});

	test('returns multiple activation classes when multiple families are present', () => {
		expect(
			hoverVariationClasses(
				'is-style-hover-text-light is-style-hover-icon-blue',
				'airo-wp-flex'
			)
		).toEqual([
			'airo-wp-flex--has-hover-text',
			'airo-wp-flex--has-hover-icon',
		]);
	});

	test('ignores unrelated classNames', () => {
		expect(
			hoverVariationClasses('is-style-rounded', 'airo-wp-flex')
		).toEqual([]);
	});
});

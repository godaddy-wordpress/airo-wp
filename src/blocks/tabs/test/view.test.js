/**
 * Tabs deep-linking must survive a hostile URL fragment.
 *
 * The fragment is whatever is in the address bar, and it was passed straight to
 * `querySelector('#' + hash)`. A fragment is NOT a valid CSS selector in the
 * general case: `#2024`, `#a b` and `#!` all make querySelector throw
 * SyntaxError. That throw happened inside init(), so a single malformed link
 * — or an ordinary year-numbered anchor from elsewhere on the page — took out
 * EVERY tabs block on the page, not just the deep link.
 */

const TABS_HTML = `
	<div class="wp-block-airo-wp-tabs airo-wp-tabs" data-active-tab="0" data-deep-linking="true">
		<div class="airo-wp-tabs__nav"></div>
		<div class="airo-wp-tab" id="panel-one" aria-label="One"></div>
		<div class="airo-wp-tab" id="panel-two" aria-label="Two"></div>
	</div>
`;

const mount = (hash) => {
	window.location.hash = hash;
	document.body.innerHTML = TABS_HTML;
	return document.querySelector('.wp-block-airo-wp-tabs');
};

describe('tabs deep linking', () => {
	beforeAll(() => {
		require('../view.js');
	});

	afterEach(() => {
		document.body.innerHTML = '';
		window.location.hash = '';
	});

	it.each([
		['a numeric fragment', '#2024'],
		['a fragment with a space', '#a b'],
		['a bare bang', '#!'],
		['a selector-ish fragment', '#a>b'],
		['a quote', '#a"b'],
	])('does not throw on %s', (_label, hash) => {
		const el = mount(hash);

		expect(() => new window.DSGTabs(el)).not.toThrow();

		// And the block is still alive: navigation got built.
		expect(el.querySelectorAll('.airo-wp-tabs__tab')).toHaveLength(2);
	});

	it('still resolves a legitimate deep link to the right panel', () => {
		const el = mount('#panel-two');

		new window.DSGTabs(el);

		const second = el.querySelector('#panel-two');
		expect(second.classList.contains('is-active')).toBe(true);
	});

	it('falls back to the first tab when the fragment matches nothing', () => {
		const el = mount('#panel-nope');

		new window.DSGTabs(el);

		const first = el.querySelector('#panel-one');
		expect(first.classList.contains('is-active')).toBe(true);
	});

	it('keeps nested tab panels out of the outer navigation', () => {
		const el = mount('');
		el.classList.add('airo-wp-tabs');
		el.innerHTML =
			'<div class="airo-wp-tabs__nav"></div>' +
			'<div class="airo-wp-tab" id="panel-outer-one" aria-label="Outer one"><div class="airo-wp-tabs"><div class="airo-wp-tab" id="panel-inner-one" aria-label="Inner one"></div><div class="airo-wp-tab" id="panel-inner-two" aria-label="Inner two"></div></div></div>' +
			'<div class="airo-wp-tab" id="panel-outer-two" aria-label="Outer two"></div>';

		const tabs = new window.DSGTabs(el);

		expect(
			el.querySelectorAll(
				':scope > .airo-wp-tabs__nav .airo-wp-tabs__tab'
			)
		).toHaveLength(2);
		tabs.setActiveTab(1);
		expect(el.querySelector('#panel-outer-one').hidden).toBe(true);
		expect(el.querySelector('#panel-outer-two').hidden).toBe(false);
	});
});

<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Services;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Services\FontDownloader;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class FontDownloaderTest extends TestCase {

	private FontDownloader $downloader;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		// Define download_url so that function_exists('download_url') returns true
		// and the require_once ABSPATH path is never hit.
		Functions\when( 'download_url' )->justReturn( '/tmp/fake-font.woff2' );

		$this->downloader = new FontDownloader();
	}

	public function test_process_font_families_returns_unchanged_when_no_theme_fonts(): void {
		$input = array(
			'custom' => array(
				array(
					'slug' => 'my-font',
					'name' => 'My Font',
				),
			),
		);

		$result = $this->downloader->process_font_families( $input );

		$this->assertSame( $input, $result );
	}

	public function test_process_font_families_returns_unchanged_when_no_font_face(): void {
		$input = array(
			'theme' => array(
				array(
					'slug' => 'roboto',
					'name' => 'Roboto',
				),
			),
		);

		$result = $this->downloader->process_font_families( $input );

		$this->assertSame( $input, $result );
	}

	public function test_process_font_families_skips_non_google_fonts_urls(): void {
		$input = array(
			'theme' => array(
				array(
					'slug'     => 'custom-font',
					'name'     => 'Custom Font',
					'fontFace' => array(
						array(
							'fontWeight' => '400',
							'fontStyle'  => 'normal',
							'src'        => array( 'https://example.com/fonts/custom.woff2' ),
						),
					),
				),
			),
		);

		$result = $this->downloader->process_font_families( $input );

		// The src should remain unchanged since it's not a Google Fonts URL.
		$this->assertSame(
			'https://example.com/fonts/custom.woff2',
			$result['theme'][0]['fontFace'][0]['src'][0]
		);
	}
}

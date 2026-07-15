<?php
/**
 * UpdateSiteOptions tool tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Site;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Site\UpdateSiteOptions;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for UpdateSiteOptions MCP tool.
 */
final class UpdateSiteOptionsTest extends TestCase {

	/**
	 * Tool under test.
	 *
	 * @var UpdateSiteOptions
	 */
	private UpdateSiteOptions $tool;

	/**
	 * Set up.
	 */
	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();

		$this->tool = new UpdateSiteOptions();
	}

	/**
	 * TOOL_ID uses the airo-wp prefix.
	 */
	public function test_tool_id_uses_airo_wp_prefix(): void {
		$this->assertSame( 'airo-wp/update-site-options', UpdateSiteOptions::TOOL_ID );
	}

	/**
	 * check_permissions returns false without manage_options capability.
	 */
	public function test_check_permissions_returns_false_without_manage_options(): void {
		Functions\expect( 'current_user_can' )->once()->with( 'manage_options' )->andReturn( false );

		$this->assertFalse( $this->tool->check_permissions() );
	}

	/**
	 * Execute rejects a protected option.
	 */
	public function test_execute_rejects_protected_option(): void {
		Functions\expect( 'current_user_can' )->with( 'manage_options' )->andReturn( true );

		$result = $this->tool->execute( array(
			'option_name'  => 'auth_key',
			'option_value' => 'hacked',
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'protected', $result['message'] );
	}

	/**
	 * Execute updates a single option successfully.
	 */
	public function test_execute_updates_single_option(): void {
		Functions\expect( 'current_user_can' )->with( 'manage_options' )->andReturn( true );
		Functions\expect( 'update_option' )->with( 'blogname', 'My Site' )->andReturn( true );

		$result = $this->tool->execute( array(
			'option_name'  => 'blogname',
			'option_value' => 'My Site',
		) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 1, $result['updated_count'] );
		$this->assertSame( 0, $result['failed_count'] );
		$this->assertCount( 1, $result['results'] );
		$this->assertTrue( $result['results'][0]['success'] );
		$this->assertSame( 'blogname', $result['results'][0]['option_name'] );
	}

	/**
	 * Execute rejects when both single and bulk options are provided.
	 */
	public function test_execute_rejects_both_single_and_bulk(): void {
		Functions\expect( 'current_user_can' )->with( 'manage_options' )->andReturn( true );

		$result = $this->tool->execute( array(
			'option_name'  => 'blogname',
			'option_value' => 'My Site',
			'options'      => array(
				array(
					'option_name'  => 'blogdescription',
					'option_value' => 'A tagline',
				),
			),
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not both', $result['message'] );
	}

	/**
	 * Execute handles bulk option updates.
	 */
	public function test_execute_handles_bulk_updates(): void {
		Functions\expect( 'current_user_can' )->with( 'manage_options' )->andReturn( true );
		Functions\expect( 'update_option' )->with( 'blogname', 'My Site' )->andReturn( true );
		Functions\expect( 'update_option' )->with( 'blogdescription', 'A tagline' )->andReturn( true );

		$result = $this->tool->execute( array(
			'options' => array(
				array(
					'option_name'  => 'blogname',
					'option_value' => 'My Site',
				),
				array(
					'option_name'  => 'blogdescription',
					'option_value' => 'A tagline',
				),
			),
		) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 2, $result['updated_count'] );
		$this->assertSame( 0, $result['failed_count'] );
		$this->assertCount( 2, $result['results'] );
	}

	/**
	 * Execute returns error when no valid options are provided.
	 */
	public function test_execute_returns_error_when_no_options(): void {
		Functions\expect( 'current_user_can' )->with( 'manage_options' )->andReturn( true );

		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'No valid options provided', $result['message'] );
	}

	/**
	 * Execute rejects empty option name.
	 */
	public function test_execute_rejects_empty_option_name(): void {
		Functions\expect( 'current_user_can' )->with( 'manage_options' )->andReturn( true );

		$result = $this->tool->execute( array(
			'option_name'  => '',
			'option_value' => 'test',
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'empty', strtolower( $result['message'] ) );
	}

	/**
	 * Execute treats same-value update_option(false) as success.
	 */
	public function test_execute_treats_same_value_as_success(): void {
		Functions\expect( 'current_user_can' )->with( 'manage_options' )->andReturn( true );
		Functions\expect( 'update_option' )->with( 'blogname', 'Same Value' )->andReturn( false );
		Functions\expect( 'get_option' )->with( 'blogname' )->andReturn( 'Same Value' );

		$result = $this->tool->execute( array(
			'option_name'  => 'blogname',
			'option_value' => 'Same Value',
		) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 1, $result['updated_count'] );
	}

	/**
	 * Execute canonicalizes WPLANG option name.
	 */
	public function test_execute_canonicalizes_wplang(): void {
		Functions\expect( 'current_user_can' )->with( 'manage_options' )->andReturn( true );
		Functions\expect( 'update_option' )->with( 'WPLANG', 'fr_FR' )->andReturn( true );
		Functions\expect( 'wp_download_language_pack' )->with( 'fr_FR' )->andReturn( 'fr_FR' );

		$result = $this->tool->execute( array(
			'option_name'  => 'wplang',
			'option_value' => 'fr-FR',
		) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 1, $result['updated_count'] );
	}

	/**
	 * Protected option names are case-insensitive.
	 */
	public function test_protected_option_case_insensitive(): void {
		Functions\expect( 'current_user_can' )->with( 'manage_options' )->andReturn( true );

		$result = $this->tool->execute( array(
			'option_name'  => 'AUTH_KEY',
			'option_value' => 'hacked',
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'protected', $result['message'] );
	}

	/**
	 * AI service API keys are protected.
	 *
	 * @dataProvider provider_ai_api_keys
	 */
	public function test_ai_api_keys_are_protected( string $key ): void {
		Functions\expect( 'current_user_can' )->with( 'manage_options' )->andReturn( true );

		$result = $this->tool->execute( array(
			'option_name'  => $key,
			'option_value' => 'sk-leaked',
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'protected', $result['message'] );
	}

	/**
	 * @return array<string, array{string}>
	 */
	public static function provider_ai_api_keys(): array {
		return array(
			'openai'     => array( 'openai_api_key' ),
			'anthropic'  => array( 'anthropic_api_key' ),
			'openai_uc'  => array( 'OPENAI_API_KEY' ),
		);
	}

	/**
	 * Options with the gd_mwcs_ prefix are protected.
	 */
	public function test_gd_mwcs_prefix_is_protected(): void {
		Functions\expect( 'current_user_can' )->with( 'manage_options' )->andReturn( true );

		$result = $this->tool->execute( array(
			'option_name'  => 'gd_mwcs_api_token',
			'option_value' => 'leaked',
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'protected', $result['message'] );
	}
}

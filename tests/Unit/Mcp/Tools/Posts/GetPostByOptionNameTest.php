<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Posts;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\GetPost;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\GetPostByOptionName;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class GetPostByOptionNameTest extends TestCase {

	private GetPostByOptionName $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_key' )->returnArg();

		$get_post   = new GetPost();
		$this->tool = new GetPostByOptionName( $get_post );
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/get-post-by-option-name', GetPostByOptionName::TOOL_ID );
	}

	public function test_check_permissions_returns_false_without_manage_options(): void {
		Functions\expect( 'current_user_can' )->once()->with( 'manage_options' )->andReturn( false );

		$this->assertFalse( $this->tool->check_permissions() );
	}

	public function test_empty_option_name_returns_not_found(): void {
		Functions\expect( 'current_user_can' )->andReturn( true );
		Functions\when( 'sanitize_key' )->justReturn( '' );
		Functions\expect( 'get_post' )->andReturn( null );

		$result = $this->tool->execute( array( 'option_name' => '' ) );
		$this->assertSame( 'not_found', $result['status'] );
	}

	public function test_valid_option_resolves_post(): void {
		Functions\expect( 'current_user_can' )->andReturn( true );
		Functions\expect( 'get_option' )->with( 'page_on_front', null )->andReturn( '42' );

		$post                = (object) array(
			'ID'            => 42,
			'post_title'    => 'Home',
			'post_content'  => '',
			'post_excerpt'  => '',
			'post_status'   => 'publish',
			'post_type'     => 'page',
			'post_author'   => '1',
			'post_date'     => '2026-01-01 00:00:00',
			'post_modified' => '2026-01-01 00:00:00',
			'post_name'     => 'home',
		);
		Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
		Functions\expect( 'get_post_thumbnail_id' )->with( 42 )->andReturn( 0 );

		$result = $this->tool->execute( array( 'option_name' => 'page_on_front' ) );
		$this->assertSame( 42, $result['id'] );
		$this->assertSame( 'Home', $result['title'] );
		$this->assertSame( 0, $result['featured_media_id'] );
	}

	public function test_option_with_non_numeric_value_returns_not_found(): void {
		Functions\expect( 'current_user_can' )->andReturn( true );
		Functions\expect( 'get_option' )->andReturn( 'not-a-number' );
		Functions\expect( 'get_post' )->with( 0 )->andReturn( null );

		$result = $this->tool->execute( array( 'option_name' => 'bad_option' ) );
		$this->assertSame( 'not_found', $result['status'] );
	}
}
